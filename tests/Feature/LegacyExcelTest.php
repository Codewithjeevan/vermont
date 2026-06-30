<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Legacy\LegacyExcel;

/**
 * Verifies the LegacyExcel compatibility shim that replaces the Maatwebsite/Excel
 * 2.x API during the upgrade. Guards both export (create/sheet/appendRow/row) and
 * import (load/get) behaviour used by the legacy controllers.
 */
class LegacyExcelTest extends TestCase
{
    /** create()->sheet()->appendRow()/row() produces a valid non-empty xlsx. */
    public function testExportProducesValidXlsx()
    {
        $book = LegacyExcel::create('Sample', function ($excel) {
            $excel->sheet('Data', function ($sheet) {
                $sheet->appendRow(['Id', 'Name']);
                $sheet->row($sheet->getHighestRow(), function ($row) {
                    $row->setFontWeight('bold');
                });
                $sheet->appendRow([1, 'Alpha']);
                $sheet->appendRow(['Id' => 2, 'Name' => 'Beta']);
            });
        });

        $raw = $book->raw('xlsx');

        $this->assertNotEmpty($raw);
        // xlsx is a zip archive — must start with the PK signature.
        $this->assertSame('PK', substr($raw, 0, 2));
    }

    /** load()->get() round-trips rows keyed by snake_cased headings. */
    public function testImportReadsRowsByHeading()
    {
        $tmp = storage_path('exports');
        $book = LegacyExcel::create('roundtrip', function ($excel) {
            $excel->sheet('Items', function ($sheet) {
                $sheet->appendRow(['Item Code', 'Item Name', 'Cost Price']);
                $sheet->appendRow(['ABC123', 'Widget', 10.5]);
                $sheet->appendRow(['XYZ999', 'Gadget', 7]);
            });
        });
        $file = $book->store('xlsx', $tmp);

        try {
            $rows = LegacyExcel::load($file)->get();

            $this->assertCount(2, $rows);
            $first = $rows->first();
            $this->assertSame('ABC123', $first->item_code);
            $this->assertSame('Widget', $first->item_name);
            $this->assertEquals(10.5, $first->cost_price);
        } finally {
            @unlink($file);
        }
    }
}
