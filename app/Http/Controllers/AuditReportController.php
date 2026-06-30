<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Audit;
use PDF;
use Excel;
use Button;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;
use Common;

use Exception;
use Log;

class AuditReportController extends Controller
{



  private $rDateFormat;
  private $rDateFormatSQL;

  public function __construct(){

    $this->rDateFormat = Common::rDateFormat();
    $this->rDateFormatSQL = Common::rDateFormatSQL($this->rDateFormat);
  }

  public function getAudits(Request $request){
    try{
      $strSearchKey = $request->input('searchkey');

      $arrAudits = Audit::with("_user") 
      ->orWhere("record_id", "=", "$strSearchKey")
      ->orWhere("type", "=", "$strSearchKey")
      ->orWhere("remark", "like", "%$strSearchKey%")
      ->paginate(25);

      return View('pages.reports.audit_report.audit_report',[
        "arrAudits" => $arrAudits, 
        'searchkey' => $request->input('searchkey'),
        'date_format'   => $this->rDateFormat .' h:i',
        ]);
    }catch(Exception $e){
      Log::error("AuditReportController@getAudits ".$e);
      return View("errors.exception");
    }
  }

  public function exportTo(Request $request){
    try{
      $strSearchKey = $request->input('searchkey');

      $arrAudits = Audit::with("_user") 
      ->orWhere("record_id", "=", "$strSearchKey")
      ->orWhere("type", "=", "$strSearchKey")
      ->orWhere("remark", "like", "%$strSearchKey%")
      ->get();

      if($request->input("pdf")){
       $pdf = PDF::loadView('pages.reports.audit_report.audit_report_pdf', [
        'arrAudits' => $arrAudits,
        'i' => 1,
        'date_format'   => $this->rDateFormat .' h:i',
        ]); 

       $pdf->setPaper('a4');
       $carbon = Carbon::today();
       $timestamp = $carbon->timestamp;
       $format = $carbon->format($this->rDateFormat);
       $exportName = 'AuditReport-'.$format.'.pdf';
       return $pdf->download($exportName);

     }else if(Input::get('xls')){
       if(sizeof($arrAudits) >0){
         $carbon = Carbon::today();
         $timestamp = $carbon->timestamp;
         $format = $carbon->format($this->rDateFormat);
         $exportName = 'AuditReport-'.$format; 
         \App\Legacy\LegacyExcel::create($exportName, function ($excel) use($arrAudits){

          $excel->sheet('AuditReport', function ($sheet) use($arrAudits){

            $dbList = array();
            $i=1;
            foreach ($arrAudits as $o) {              
             if($o->type == 1)
               $type='Item';
             elseif($o->type == 2)
               $type='Purchase Order';
             elseif($o->type == 3)
               $type='Purchase';
             elseif($o->type == 4)
               $type='Purchase Return';
             elseif($o->type == 5)
               $type='Expiry';
             elseif($o->type == 6)
               $type='Damage';

             array_push($dbList, array(
              '#'  => $o->id,
              trans('glob.record_type')  => $type,
              trans('glob.record_id')  => $o->record_id,
              trans('glob.user')  => (isset($o->_user->email) ? $o->_user->email : '' ),                                   
              trans('glob.remark')  => $o->remark,                      
              trans('glob.datetime')  => $o->created_at->format($this->rDateFormat .' h:i'),                      

              )
             );
           }                           

           /* setting column names for data - you can of course set it manually */
           $sheet->appendRow(array_keys($dbList[0])); /* column names */

           /* getting last row number (the one we already filled and setting it to bold */
           $sheet->row($sheet->getHighestRow(), function ($row) {
            $row->setFontWeight('bold');
          });

           /* putting users data as next rows */
           foreach ($dbList as $l) {
            $sheet->appendRow($l);
          }
        });

        })->export('xlsx');


         return back();

       }else{
        toastr::warning('Warning', trans("validation.export_error"));
        return back();
      }
    }
  }catch(Exception $e){
    Log::error("BillingsController@exportTo ".$e);
    return View("errors.exception");
  }
}
}
