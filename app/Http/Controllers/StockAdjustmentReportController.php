<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Item;
use App\Category;
use App\Unit;
use App\Supplier;
use App\Purchase;
use App\PurchasedItem;
use App\User;
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

class StockAdjustmentReportController extends Controller
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
			$items = array();
			$items[] = (object) array('id'=>'-1','itemName' => 'All', 'barcode' =>'All');

			$users = array();
			$users1 = User::all();	
			$users[] = (object) array('id'=>'-1','name' => 'All');
			foreach ($users1 as $row) {
				$users[]=$row;
			}
			$date = Carbon::now()->format('m/d/Y');
			$date1 = Carbon::now()->format('Y-m-d');
			$mallDetails = $this->mallDetails();
			$res = array(
				"items" => $items,
				"users" =>$users,
				"date" => $date,
				"date1" => $date1,
				"mallDetails" => $mallDetails,
				);
			if($request->ajax()){
				return Response($res,200);
			}else{
				return View('pages.reports.stock_adjustment_report.stock_adjustment_report', $res);
			}
		}catch(Exception $e){
			Log::error("StockAdjustmentReportController@index ".$e);
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
			$fromDate = $dateArray['fromDate'] ?? Carbon::now()->format('Y-m-d');
			$toDate = $dateArray['toDate'] ?? Carbon::now()->format('Y-m-d');	
			$user_id = $dateArray['user_id'];
			$item_id = $dateArray['item_id'];
			$total=0;
			$totalQty=0;
			$totalRate=0;
			$data = $this->getStockAdjustmentDataQuery($fromDate,$toDate,$user_id,$item_id);
			$users = array();
			$users1 = User::all();	
			$users[] = (object) array('id'=>'-1','name' => 'All');
			$items = array();
			$items[] = (object) array('id'=>'-1','itemName' => 'All', 'barcode' =>'All');

			foreach ($users1 as $row) {
				$users[]=$row;
			}	
			$labels = 			$this->getLabels();
			$total = 			$data['total'];
			$totalQty = 		$data['totalQty'];			
			$purchases = 		$data['purchases'];
			$res = array(
				"users"  => 		$users,
				"items" => 			$items,			
				"purchases" => 		$purchases,	
				"total" =>			$total,
				"totalQty" => 		$totalQty,
				"labels" => 		$labels,					
				"toDatePrint" => 	(new Carbon($toDate))->format($this->rDateFormat),
				"fromDatePrint" => 	(new Carbon($fromDate))->format($this->rDateFormat),				
				);
			if($request->ajax()){
				return Response($res,200);
			}else{
				return View("pages.reports.stock_adjustment_report.stock_adjustment_report",$res);
			}
		}catch(Exception $e){
			Log::error("StockAdjustmentReportController@getLedger ".$e);
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
	 			$fromDate =$dateArray['from'] ?? Carbon::now()->format('Y-m-d');
	 			$toDate = $dateArray['to'] ?? Carbon::now()->format('Y-m-d');
	 			$item_id = $dateArray['itemName'];
	 			$user_id = $dateArray['user'];
	 			$total=0;
	 			$totalQty=0;
	 			$totalRate=0;
	 			$currency=$this->optioncurr;
	 			$data = $this->getStockAdjustmentDataQuery($fromDate,$toDate,$user_id,$item_id);
	 			$fromDate = date($this->rDateFormat, strtotime($fromDate));
	 			$toDate = date($this->rDateFormat, strtotime($toDate));
	 			$total = 			$data['total'];
	 			$totalQty = 		$data['totalQty'];	 			
	 			$purchases = 		$data['purchases'];
	 			if(Input::get('pdf')){		 				
	 				$pdf = PDF::loadView('pages.reports.stock_adjustment_report.stock_adjustment_report_pdf', [
	 					"purchases" => 			$purchases ,
	 					"total"=> 				$total,
	 					"totalQty" => 			$totalQty,	 					
	 					"fromDate" => 			$fromDate,
	 					"toDate" => 			$toDate,
	 					"i" =>					'1',
	 					"curr"=>				$currency
	 					]); 
	 				$pdf->setPaper('a4');
	 				$carbon = Carbon::today();
	 				$timestamp = $carbon->timestamp;
	 				$format = $carbon->format($this->rDateFormat);
	 				$exportName = 'StockAdjustmentReport-'.$format.'.pdf';     
	 				return $pdf->download($exportName);
	 			}elseif (Input::get('xls')) {	 				
	 				$carbon = Carbon::today();
	 				$timestamp = $carbon->timestamp;
	 				$format = $carbon->format($this->rDateFormat);
	 				$exportName = 'StockAdjustmentReport-'.$format; 
	 				\App\Legacy\LegacyExcel::create($exportName, function ($excel) 
	 					use($purchases,
	 						$total,
	 						$totalQty,	 						
	 						$fromDate,
	 						$toDate,
	 						$currency) {
	 						$excel->sheet('StockAdjustmentReport', function ($sheet) 
	 							use($purchases,
	 								$total,
	 								$totalQty,	 								
	 								$fromDate,
	 								$toDate,
	 								$currency) {	
	 								$sheet->appendRow(array('', ''));
	 								$sheet->appendRow(array(trans('glob.from'), $fromDate,trans('glob.to'),$toDate));
	 								$sheet->row($sheet->getHighestRow(), function ($row) {
	 									$row->setFontWeight('bold');
	 								});	
	 								$sheet->appendRow(array('', ''));
	 								/* setting column names for data - you can of course set it manually */
	 								$sheet->appendRow(array(
	 									"#"  ,	
	 									trans('item.item_name') ,
	 									trans('glob.barcode') ,
	 									trans('item.qty') ,
	 									trans('item.refrence_number') ,
	 									trans('glob.unit') ,
	 									trans('glob.cost') ,
	 									trans('stock.amt')."(".$currency.")" ,
	 									trans('glob.user') ,
	 									trans('glob.date') ,
	 									)); /* column names */
	 								/* getting last row number (the one we already filled and setting it to bold */
	 								$sheet->row($sheet->getHighestRow(), function ($row) {
	 									$row->setFontWeight('bold');
	 								});
	 								foreach ($purchases as $p) {
	 									$i=0;						
	 									$sheet->appendRow( array(
	 										"#"  => $p->name,	
	 										trans('item.item_name') => '',
	 										trans('glob.barcode') => '',
	 										trans('item.qty') => '',
	 										trans('item.refrence_number') => '',
	 										trans('glob.unit') => '',
	 										trans('glob.cost') => '',
	 										trans('stock.amt')."(".$currency.")" => '',
	 										trans('glob.user') => '',
	 										trans('glob.date') => '',		
	 										)
	 									);
	 									foreach ($p->child as $o) {						
	 										$i++;		
	 													
	 										$sheet->appendRow(array(
	 											"#"  => $i,
	 											trans('item.item_name') => $o->itemName,
	 											trans('glob.barcode') => $o->barcode,
	 											trans('item.qty') => " ".Common::fixQty($o->quantity),
	 											trans('item.refrence_number') => $o->serial_number,
	 											trans('glob.unit') => $o->unit,
	 											trans('glob.cost') => " ".Common::fixIt($o->rate),
	 											trans('stock.amt')."(".$currency.")"  => " ".Common::fixIt($o->amt),
	 											trans('glob.user') => $o->name,
	 											trans('glob.date') => $o->stock_adjustment_date,	
	 											)
	 										);
	 									}		
	 									$sheet->appendRow( array(
	 										"#"  => '',	
	 										trans('item.item_name') => '',
	 										trans('glob.barcode') =>'',
	 										trans('item.qty') => trans('item.qty')." = ".Common::fixQty($p->quantity),
	 										trans('item.refrence_number') =>'',
	 										trans('glob.unit') =>'',
	 										trans('glob.cost') =>'',
	 										trans('stock.amt') => trans('stock.amt') ." = ".Common::fixIt($p->amt),
	 										trans('glob.user') =>'',
	 										trans('glob.date') =>'',	 												
	 										)
	 									);
	 								}
	 								$sheet->appendRow( array(
	 									"#"  => '',	
	 									trans('item.item_name') => '',
	 									trans('glob.barcode') => '',
	 									trans('item.qty') =>'',
	 									trans('item.refrence_number') =>'',
	 									trans('glob.unit') =>'',
	 									trans('glob.cost') => trans('glob.net_loss_value_adjusted'),
	 									trans('stock.amt') => " ".Common::fixIt($total),
	 									trans('glob.user') =>'',
	 									trans('glob.date') =>'',
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
	Log::error("StockAdjustmentReportController@exportTo ".$e);
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
 	 			"unit"	=> trans('glob.unit'),
 	 			"user"	=> trans('glob.user'),
 	 			"cost"	=> trans('glob.cost'),
 	 			"net_loss_value_adjusted"	=> trans('glob.net_loss_value_adjusted'),
 	 			"refrence_number"=>trans('item.refrence_number'),
 	 			)
 	 		);
 	 		return $labels;
 	 	}catch(Exception $e){
 	 		Log::error("StockAdjustmentReportController@getLabels ".$e);
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
 	 		Log::error("StockAdjustmentReportController@mallDetails ".$e);
 	 		return View("errors.exception");
 	 	}
 	 }

 	 public function getStockAdjustmentDataQuery($fromDate,$toDate,$user_id,$item_id){
 	 	try{
 	 		$total = 0; 	 		
 	 		$totalQty = 0;
 	 		$user_id = $user_id ?? -1; 	 

 	 		$purchases = DB::select("SELECT adjust_through, SUM(stock_adjustment_items.quantity) as quantity,(CASE WHEN adjust_through = 1 THEN -(SUM(stock_adjustment_items.amt)) ELSE SUM(stock_adjustment_items.amt) END) as amt FROM stock_adjustments INNER JOIN `stock_adjustment_items` ON stock_adjustments.id = stock_adjustment_items.stock_adjustment_id 
 	 				INNER JOIN items ON stock_adjustment_items.item_id = items.id 	 				
 	 				INNER JOIN users ON stock_adjustments.user_id = users.id 
 	 				where
 	 				(CASE WHEN $item_id = -1 THEN stock_adjustment_items.item_id = items.id ELSE $item_id = items.id END)
 	 				AND (CASE WHEN $user_id = -1 THEN stock_adjustments.user_id = users.id ELSE $user_id = users.id END)
 	 				AND CAST(stock_adjustments.stock_adjustment_date AS DATE) BETWEEN '".$fromDate."' and '".$toDate."' GROUP BY adjust_through");
 	 		foreach ($purchases as $row) {
 	 			$p=DB::select("SELECT stock_adjustments.id, DATE_FORMAT(stock_adjustments.stock_adjustment_date,'".$this->rDateFormatSQL."') as stock_adjustment_date,stock_adjustment_items.quantity,stock_adjustment_items.rate,(CASE WHEN $row->adjust_through = 1 THEN -(stock_adjustment_items.amt) ELSE stock_adjustment_items.amt END) as amt,items.itemName,items.barcode, items.serial_number,users.name,units.unit
 	 			    from stock_adjustments 
 	 				INNER JOIN stock_adjustment_items ON stock_adjustments.id = stock_adjustment_items.stock_adjustment_id
 	 				INNER JOIN items ON stock_adjustment_items.item_id = items.id 
 	 				INNER JOIN units ON items.unit = units.id 
 	 				INNER JOIN users ON stock_adjustments.user_id = users.id 
 	 				WHERE adjust_through = ".$row->adjust_through." 
 	 				AND (CASE WHEN $item_id = -1 THEN stock_adjustment_items.item_id = items.id ELSE $item_id = items.id END)
 	 				AND (CASE WHEN $user_id = -1 THEN stock_adjustments.user_id = users.id ELSE $user_id = users.id END)
 	 				AND (CAST(stock_adjustments.stock_adjustment_date AS DATE) BETWEEN '".$fromDate."' and '".$toDate."')
 	 				ORDER BY stock_adjustments.stock_adjustment_date ASC");

 	 			$row->child = $p;
 	 			$total = $row->amt + $total; 	 			
 	 			$totalQty = $row->quantity + $totalQty;
 	 			if($row->adjust_through == 1){
 	 				$row->name = "Excess";
 	 			}else if($row->adjust_through == 2){
 	 				$row->name = "Shortage";
 	 			}else if($row->adjust_through == 3){
 	 				$row->name = "Wastage";
 	 			}
 	 			
 	 		}
 	 		return array(
 	 			'purchases' => 		$purchases,
 	 			'total' => 			$total,
 	 			'totalQty' =>		$totalQty
 	 			);
 	 	}catch(Exception $e){
 	 		Log::error("StockAdjustmentReportController@getStockAdjustmentDataQuery ".$e);
 	 		return View("errors.exception");
 	 	}
 	 }
 	}
