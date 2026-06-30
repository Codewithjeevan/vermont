<?php

namespace App\Legacy;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

/**
 * Compatibility shim that re-implements the Maatwebsite/Excel 2.x facade API
 * (Excel::create()->sheet()->appendRow()/row()/getHighestRow()->export() and
 * Excel::load()->get()) on top of PhpSpreadsheet, which ships with
 * maatwebsite/excel 3.x.
 *
 * Goal: keep the project's ~46 legacy exports and 4 imports producing byte-for-byte
 * the same output during the Laravel upgrade, with zero changes to their internal
 * row-building logic — only the facade call name changes (Excel:: -> LegacyExcel::).
 *
 * This is a transitional layer; exports can be migrated to native v3 Export
 * classes later (tracked in the separate frontend/cleanup plan).
 */
class LegacyExcel
{
    /** Mirrors Excel::create($name, function ($excel) { ... }). */
    public static function create($title, callable $callback)
    {
        $book = new LegacyBook($title);
        $callback($book);

        return $book;
    }

    /** Mirrors \Excel::load($path, function ($reader) {})->get(). */
    public static function load($path, callable $callback = null)
    {
        return new LegacyReader($path);
    }
}

/** Represents the workbook returned by LegacyExcel::create(). */
class LegacyBook
{
    public $title;
    /** @var LegacySheet[] */
    public $sheets = [];

    public function __construct($title)
    {
        $this->title = $title;
    }

    /** Mirrors $excel->sheet($name, function ($sheet) { ... }). */
    public function sheet($name, callable $callback)
    {
        $sheet = new LegacySheet($name);
        $callback($sheet);
        $this->sheets[] = $sheet;

        return $this;
    }

    /** Build a PhpSpreadsheet document from the accumulated sheets/rows. */
    public function build()
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        $index = 0;
        foreach ($this->sheets as $legacySheet) {
            $ws = new Worksheet($spreadsheet, $this->safeSheetName($legacySheet->name, $index));
            $spreadsheet->addSheet($ws, $index);

            if (! empty($legacySheet->rows)) {
                $ws->fromArray($legacySheet->rows, null, 'A1', true);
            }

            foreach ($legacySheet->boldRows as $rowNumber) {
                $highestColumn = $ws->getHighestColumn();
                $ws->getStyle("A{$rowNumber}:{$highestColumn}{$rowNumber}")
                    ->getFont()->setBold(true);
            }

            $index++;
        }

        if ($index === 0) {
            $spreadsheet->createSheet();
        }

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    /** Return the raw binary contents (used for tests). */
    public function raw($format = 'xlsx')
    {
        $writer = $this->makeWriter($this->build(), $format);

        ob_start();
        $writer->save('php://output');

        return ob_get_clean();
    }

    /**
     * Stream the document to the browser as a download and terminate the
     * request — matches Maatwebsite 2.x ->export()/->download() behaviour.
     */
    public function export($format = 'xlsx')
    {
        $this->stream($format);
    }

    public function download($format = 'xlsx')
    {
        $this->stream($format);
    }

    /** Save the document to disk (storage/exports by default). */
    public function store($format = 'xlsx', $path = null)
    {
        $path = $path ?: storage_path('exports');
        if (! is_dir($path)) {
            mkdir($path, 0775, true);
        }
        $file = rtrim($path, '/\\').DIRECTORY_SEPARATOR.$this->title.'.'.$format;
        $this->makeWriter($this->build(), $format)->save($file);

        return $file;
    }

    private function stream($format)
    {
        $spreadsheet = $this->build();
        $writer = $this->makeWriter($spreadsheet, $format);

        $mime = $format === 'xls'
            ? 'application/vnd.ms-excel'
            : ($format === 'csv'
                ? 'text/csv'
                : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        if (! headers_sent()) {
            header('Content-Type: '.$mime);
            header('Content-Disposition: attachment; filename="'.$this->title.'.'.$format.'"');
            header('Cache-Control: max-age=0');
        }

        $writer->save('php://output');
        exit;
    }

    private function makeWriter(Spreadsheet $spreadsheet, $format)
    {
        switch ($format) {
            case 'xls':
                return new Xls($spreadsheet);
            case 'csv':
                return new Csv($spreadsheet);
            default:
                return new Xlsx($spreadsheet);
        }
    }

    private function safeSheetName($name, $index)
    {
        $name = preg_replace('/[\\\\\\/\\?\\*\\[\\]:]/', ' ', (string) $name);
        $name = trim($name);
        if ($name === '') {
            $name = 'Sheet'.($index + 1);
        }

        return mb_substr($name, 0, 31);
    }
}

/** Represents a single worksheet inside the legacy callback. */
class LegacySheet
{
    public $name;
    /** @var array[] */
    public $rows = [];
    /** @var int[] */
    public $boldRows = [];

    public function __construct($name)
    {
        $this->name = $name;
    }

    /** Mirrors $sheet->appendRow($array) — assoc arrays append their values. */
    public function appendRow($row)
    {
        $this->rows[] = array_values((array) $row);

        return $this;
    }

    /** Mirrors $sheet->prependRow($array). */
    public function prependRow($row)
    {
        array_unshift($this->rows, array_values((array) $row));

        return $this;
    }

    /** Mirrors $sheet->getHighestRow() — 1-based index of the last row. */
    public function getHighestRow()
    {
        return count($this->rows);
    }

    /** Mirrors $sheet->row($n, function ($row) { $row->setFontWeight('bold'); }). */
    public function row($number, callable $callback)
    {
        $callback(new LegacyRow($this, (int) $number));

        return $this;
    }
}

/** Represents a row passed to the $sheet->row() styling callback. */
class LegacyRow
{
    private $sheet;
    private $number;

    public function __construct(LegacySheet $sheet, $number)
    {
        $this->sheet = $sheet;
        $this->number = $number;
    }

    /** Mirrors $row->setFontWeight('bold'). */
    public function setFontWeight($weight = 'bold')
    {
        if ($weight === 'bold' || $weight === true) {
            $this->sheet->boldRows[] = $this->number;
        }

        return $this;
    }
}

/** Mirrors the reader returned by Excel::load(); ->get() yields row objects. */
class LegacyReader
{
    private $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * Return a Collection of row objects keyed by the (snake_cased) heading row,
     * matching Maatwebsite 2.x ->get() behaviour (e.g. $row->item_code).
     */
    public function get()
    {
        $reader = IOFactory::createReaderForFile($this->path);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($this->path);
        $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);

        if (empty($rows)) {
            return new Collection();
        }

        $headings = array_map([$this, 'slugHeading'], array_shift($rows));

        $result = [];
        foreach ($rows as $row) {
            if ($this->isEmptyRow($row)) {
                continue;
            }
            $assoc = [];
            foreach ($headings as $i => $key) {
                if ($key === '') {
                    continue;
                }
                $assoc[$key] = isset($row[$i]) ? $row[$i] : null;
            }
            $result[] = (object) $assoc;
        }

        return new Collection($result);
    }

    private function slugHeading($heading)
    {
        $heading = strtolower(trim((string) $heading));
        $heading = preg_replace('/[^a-z0-9]+/', '_', $heading);

        return trim($heading, '_');
    }

    private function isEmptyRow($row)
    {
        foreach ($row as $cell) {
            if ($cell !== null && $cell !== '') {
                return false;
            }
        }

        return true;
    }
}
