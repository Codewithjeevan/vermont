<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Item;
use App\Category;
use App\Unit;
use App\Supplier;
use App\Purchase;
use App\PurchasedItem;
use App\Salesman;
use App\PurchaseOrder;
use PDF;
use Excel;
use Button;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use DB;
use Common;

use Exception;
use Log;

class SalesmanReportController extends Controller
{
	private $optioncurr;
	private $rDateFormat;
	private $rDateFormatSQL;
	public function __construct()
	{ 
		$this->optioncurr= Common::curr(); 
		$this->rDateFormat = Common::rDateFormat();
		$this->rDateFormatSQL = Common::rDateFormatSQL($this->rDateFormat);
	}

	/**
	 * Display a listing of the resource.
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function index(Request $request){
		try{
			$salesmans = array();
			$salesmans1 = Salesman::all();	
			$salesmans[] = (object) array('id'=>'0','name' => 'All');
			foreach ($salesmans1 as $row) {
				$salesmans[]=$row;
			}
			$date = Carbon::now()->format('m/d/Y');
			$date1 = Carbon::now()->format('Y-m-d');
			$mallDetails = $this->mallDetails();
			$res = array(
				"salesmans" =>$salesmans,
				"date" => $date,
				"date1" => $date1,
				"mallDetails" => $mallDetails,
				);
			if($request->ajax()){
				return Response($res,200);
			}else{
				return View('pages.reports.salesman_report.salesman_report', $res);
			}
		}catch(Exception $e){
			Log::error("SalesmanReportController@index ".$e);
			return View("errors.exception");
		}
	}

	/**
	 * Select data which is displayed on report view table
	 * @param  REquest $request [description]
	 * @return [type]           [description]
	 */
	public function getLedger(REquest $request){
		try{		
			$dateArray = $request->all()["data"]; 
			$fromDate = $dateArray['fromDate'];
			$toDate = $dateArray['toDate'];		
			$salesman_id = $dateArray['salesman_id'];
			$total=0;
			$totalQty=0;
			$totalRate=0;
			$data = $this->getSalsmanDataQuery($fromDate,$toDate,$salesman_id);
			$salesmans = array();
			$salesmans1 = Salesman::all();	
			$salesmans[] = (object) array('id'=>'0','name' => 'All');
			foreach ($salesmans1 as $row) {
				$salesmans[]=$row;
			}	
			$labels = 			$this->getLabels();
			$total = 			$data['total'];
			$totalQty = 		$data['totalQty'];
			$totalRate = 		$data['totalRate'];
			$purchases = 		$data['purchases'];
			$res = array(
				"salesmans"  => 	$salesmans,			
				"purchases" => 		$purchases,	
				"total" =>			$total,
				"totalQty" => 		$totalQty,
				"labels" => 		$labels,	
				"totalRate" => 		$totalRate,
				"toDatePrint" => 	(new Carbon($toDate))->format($this->rDateFormat),
				"fromDatePrint" => 	(new Carbon($fromDate))->format($this->rDateFormat),				
				);
			if($request->ajax()){
				return Response($res,200);
			}else{
				return View("pages.reports.salesman_report.salesman_report",$res);
			}
		}catch(Exception $e){
			Log::error("SalesmanReportController@getLedger ".$e);
			return View("errors.exception");
		}
	}


		/**
	 	* Function created for create and export exel and pdf file
	 	* @param  Request $request [description]
	 	* @return [type]           [description]
	 	*/
	 	public function exportTo(Request $request){
	 		try{				
	 			$dateArray = $request->all(); 
	 			$fromDate = $dateArray['from'];
	 			$toDate = $dateArray['to'];
	 			$salesman_id = $dateArray['salesman'];
	 			$total=0;
	 			$totalQty=0;
	 			$totalRate=0;
	 			$currency=$this->optioncurr;
	 			$data = $this->getSalsmanDataQuery($fromDate,$toDate,$salesman_id);
	 			$fromDate = date($this->rDateFormat, strtotime($fromDate));
	 			$toDate = date($this->rDateFormat, strtotime($toDate));
	 			$total = 			$data['total'];
	 			$totalQty = 		$data['totalQty'];
	 			$totalRate = 		$data['totalRate'];
	 			$purchases = 		$data['purchases'];
	 			if(Input::get('pdf')){	
	 				
	 				$pdf = PDF::loadView('pages.reports.salesman_report.salesman_report_pdf', [
	 					"purchases" => 			$purchases ,
	 					"total"=> 				$total,
	 					"totalQty" => 			$totalQty,
	 					"totalRate" => 			$totalRate,
	 					"fromDate" => 			$fromDate,
	 					"toDate" => 			$toDate,
	 					"i" =>					'1',
	 					"curr"=>				$currency
	 					]); 
	 				$pdf->setPaper('a4');
	 				$carbon = Carbon::today();
	 				$timestamp = $carbon->timestamp;
	 				$format = $carbon->format($this->rDateFormat);
	 				$exportName = 'SalesmanReport-'.$format.'.pdf';     
	 				return $pdf->download($exportName);
	 			}elseif (Input::get('xls')) {
	 				
	 				$carbon = Carbon::today();
	 				$timestamp = $carbon->timestamp;
	 				$format = $carbon->format($this->rDateFormat);
	 				$exportName = 'SalesmanReport-'.$format; 
	 				\App\Legacy\LegacyExcel::create($exportName, function ($excel) 
	 					use($purchases,
	 						$total,
	 						$totalQty,
	 						$totalRate,
	 						$fromDate,
	 						$toDate,
	 						$currency) {
	 						$excel->sheet('SalesmanReport', function ($sheet) 
	 							use($purchases,
	 								$total,
	 								$totalQty,
	 								$totalRate,
	 								$fromDate,
	 								$toDate,
	 								$currency) {	
	 								$sheet->appendRow(array('', ''));
	 								$sheet->appendRow(array(trans('glob.from'), $fromDate ,trans('glob.to'),$toDate ));
	 								$sheet->row($sheet->getHighestRow(), function ($row) {
	 									$row->setFontWeight('bold');
	 								});	
	 								$sheet->appendRow(array('', ''));
	 								/* setting column names for data - you can of course set it manually */
	 								$sheet->appendRow(array(
	 									"#"  ,	
	 									trans('billing.bill_no') ,							
	 									trans('billing.bill_date') ,						
	 									trans('glob.customer') ,
	 									trans('item.item_name') ,
	 									trans('glob.barcode') ,
	 									trans('item.refrence_number') ,
	 									trans('item.serial_number') ,
	 									trans('glob.rate') ,	
	 									trans('item.qty') ,						
	 									trans('stock.amt')."(".$currency.")" ,		
	 									)); /* column names */
	 								/* getting last row number (the one we already filled and setting it to bold */
	 								$sheet->row($sheet->getHighestRow(), function ($row) {
	 									$row->setFontWeight('bold');
	 								});
	 								foreach ($purchases as $p) {
	 									$i=0;						
	 									$sheet->appendRow( array(
	 										"#"  => $p->name,	
	 										trans('billing.bill_no') =>'',							
	 										trans('billing.bill_date') => '',						
	 										trans('glob.customer') => '',
	 										trans('item.item_name') => '',
	 										trans('glob.barcode') => '',
	 										trans('item.refrence_number') => '',
	 										trans('item.serial_number') => '',
	 										trans('glob.rate') => '',	
	 										trans('item.qty') => '',						
	 										trans('stock.amt')."(".$currency.")" => '',		
	 										)
	 									);
	 									foreach ($p->child as $o) {						
	 										$i++;			
	 										if($o->itemCode == null){
	 											$barcode="NA";
	 										}else{
	 											$barcode= $o->itemCode;
	 										}
	 										if($o->serial_number == null){
	 											$serial_number="NA";
	 										}else{
	 											$serial_number=$o->serial_number;
	 										}			
	 										if($o->refrence_number == null){
	 											$refrence_number="NA";
	 										}else{
	 											$refrence_number=$o->refrence_number;
	 										}	
	 										$sheet->appendRow(array(
	 											"#"  => $i,	
	 											trans('billing.bill_no') => "".Common::fixBill($o->id),							
	 											trans('billing.bill_date') => $o->billingDate,			
	 											trans('glob.customer') => $o->customerName ?? 'NA' ,
	 											trans('item.item_name') => $o->itemName,
	 											trans('glob.barcode') => $barcode,
	 											trans('item.refrence_number') => " ".$refrence_number,
	 											trans('item.serial_number') => $serial_number,
	 											trans('glob.rate') => " ".Common::fixIt($o->rate),	
	 											trans('item.qty') => " ".Common::fixQty($o->qty),							
	 											trans('stock.amt')."(".$currency.")" => " ".Common::fixIt($o->rate * $o->qty),		
	 											)
	 										);
	 									}		
	 									$sheet->appendRow( array(
	 										"#"  => '',	
	 										trans('billing.bill_no') => '',							
	 										trans('billing.bill_date') => '',			
	 										trans('glob.customer') => '',
	 										trans('item.item_name') => '',
	 										trans('glob.barcode') => '',
	 										trans('item.refrence_number') => '',
	 										trans('item.serial_number') => '',
	 										trans('glob.rate') => '',	
	 										trans('item.qty') =>trans('item.qty')." = ".Common::fixQty($p->qty),						
	 										trans('stock.amt') => trans('stock.amt') ." = ".Common::fixIt($p->amt),			
	 										)
	 									);
	 								}
	 								$sheet->appendRow( array(
	 									"#"  => '',	
	 									trans('billing.bill_no') => '',							
	 									trans('billing.bill_date') => '',							
	 									trans('glob.customer') => '',
	 									trans('item.item_name') => '',
	 									trans('glob.barcode') => '',
	 									trans('item.refrence_number') => '',
	 									trans('item.serial_number') => '',
	 									trans('glob.rate') => trans('glob.total'),
	 									trans('item.qty') =>" ".Common::fixQty($totalQty),						
	 									trans('stock.amt')."(".$currency.")" => " ".Common::fixIt($total),			
	 									)
	 								);
	 								$sheet->row($sheet->getHighestRow(), function ($row) {
	 									$row->setFontWeight('bold');
	 								});
	 							});
})->export('xlsx');

return back();

}
}catch(Exception $e){
	Log::error("SalesmanReportController@exportTo ".$e);
	return View("errors.exception");
}
}

 	 /**
     * This will return resource values of labels for printing purpose
     * @return Array of labels
     */
 	 public function getLabels(){
 	 	try{
 	 		$labels = array();
 	 		array_push($labels, array(
 	 			"id" => trans('glob.id'),
 	 			"billId"  => trans('billing.bill_no'),
 	 			"itemName"  => trans('billing.item_name'),   
 	 			"qty"  => trans('billing.qty'),   
 	 			"rate"  => trans('billing.rate'),   
 	 			"amt"  => trans('billing.amt'),   
 	 			"item"  => trans('glob.item'),   
 	 			"total"  => trans('glob.total'),   
 	 			"received"  => trans('glob.received'),   
 	 			"date"  => trans('glob.date'),
 	 			"time"  => trans('glob.time'),
 	 			"customer" => trans('glob.customer'),
 	 			"itemCode" => trans('item.item_code'),
 	 			"serial_number" => trans('item.serial_number'),
 	 			"customer" => trans('glob.customer'),
 	 			"bill_date" => trans('billing.bill_date'),
 	 			"barcode" => trans('glob.barcode'),
 	 			"website" => trans('glob.website'), 
 	 			"phone" => trans('glob.phone'),
 	 			"refrence_number" => trans('item.refrence_number'),
 	 			)
 	 		);
 	 		return $labels;
 	 	}catch(Exception $e){
 	 		Log::error("SalesmanReportController@getLabels ".$e);
 	 		return View("errors.exception");
 	 	}
 	 }

 	 public function mallDetails(){
 	 	try{
 	 		$address = DB::select("SELECT options.value as address FROM options where options.type = 'general' and options.key = 'register_address'");
 	 		$mallName = DB::select("SELECT options.value as mallName FROM options where options.type = 'general' and options.key = 'mall_name'");
 	 		$logo = DB::select("SELECT options.value as logo FROM options where options.type = 'general' and options.key = 'logo'");
 	 		$telephone = DB::select("SELECT options.value as telephone FROM options where options.type = 'general' and options.key = 'mall_telephone'");
 	 		$mall_website = DB::select("SELECT options.value as mall_website FROM options where options.type = 'general' and options.key = 'mall_website'");
 	 		return $res = array(      
 	 			"address"  => (isset($address[0]->address)) ? $address[0]->address : '',   
 	 			"mallName"  => (isset($mallName[0]->mallName)) ? $mallName[0]->mallName : '', 
 	 			"logo"  => (isset($logo[0]->logo)) ? $logo[0]->logo : '', 
 	 			"telephone"  => (isset($telephone[0]->telephone)) ? $telephone[0]->telephone : '',    
 	 			"mall_website"  => (isset($mall_website[0]->mall_website)) ? $mall_website[0]->mall_website : '',          
 	 			);
 	 	}catch(Exception $e){
 	 		Log::error("SalesmanReportController@mallDetails ".$e);
 	 		return View("errors.exception");
 	 	}
 	 }

 	 public function getSalsmanDataQuery($fromDate,$toDate,$salesman_id){
 	 	try{
 	 		$total = 0;
 	 		$totalRate = 0;
 	 		$totalQty = 0;
 	 		$salesman_id = $salesman_id ?? 0;
 	 		$purchases = DB::select("SELECT s.id,s.name,
 	 			sum(bi.qty) as qty,
 	 			sum(bi.rate) as rate,
 	 			sum(bi.qty * bi.rate) as amt 
 	 			FROM billings b INNER JOIN billing_items bi ON b.id =bi.billing_id 
 	 			INNER JOIN salesman s on b.salesman_id=s.id  
 	 			where b.billing_status <> 'hold' AND  
 	 			CASE WHEN  $salesman_id > 0 THEN b.salesman_id = $salesman_id  ELSE b.salesman_id != 'null' END and 
 	 			CAST(b.created_at AS DATE) BETWEEN '".$fromDate."' and '".$toDate."' GROUP BY s.id");
 	 		foreach ($purchases as $row) {
 	 			$p=DB::select("SELECT b.id,bi.item_id,bi.qty,bi.rate,
 	 				DATE_FORMAT(b.created_at,'".$this->rDateFormatSQL."') billingDate,
 	 				salesman.name as salesmanName, customers.name as customerName ,items.itemName  as itemName,
 	 				items.barcode as itemCode, items.serial_number as refrence_number,
 	 				bi.serial_no as serial_number 
 	 				FROM billings b INNER JOIN billing_items bi ON b.id =bi.billing_id left outer join 
 	 				salesman on salesman.id = b.salesman_id left outer join customers on customers.id =b.customer_id 
 	 				left outer join items on items.id=bi.item_id 
 	 				where b.billing_status <> 'hold' AND 
 	 				b.salesman_id = ".$row->id." and 
 	 				CAST(b.created_at AS DATE) BETWEEN '".$fromDate."' and '".$toDate."'  ORDER BY b.created_at ASC");
 	 			$row->child = $p;
 	 			$total = $row->amt + $total;
 	 			$totalRate =$row->rate + $totalRate;
 	 			$totalQty = $row->qty + $totalQty;
 	 		}
 	 		return array(
 	 			'purchases' => 		$purchases,
 	 			'total' => 			$total,
 	 			'totalRate' =>		$totalRate,
 	 			'totalQty' =>		$totalQty
 	 			);
 	 	}catch(Exception $e){
 	 		Log::error("SalesmanReportController@getSalsmanDataQuery ".$e);
 	 		return View("errors.exception");
 	 	}
 	 }
 	}
