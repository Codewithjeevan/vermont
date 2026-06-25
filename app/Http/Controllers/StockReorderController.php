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

class StockReorderController extends Controller
{
	private $rDateFormat;
	private $rDateFormatSQL;
	public function __construct()
	{ 
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
			$reorders[] = (object) array('id'=>'1','name' => 'Under Min Level');
			$categories= array();
			$categories1 = Category::where("parent_category","=", 0)->get();
			$categories[] = (object) array('id'=>'0','categorie' => 'All' ,'parent_category' => '0');
			foreach ($categories1 as $row) {
				$categories[]=$row;
			}
			$sub_categories = array();
			$sub_categories1 = Category::where("parent_category","<>", 0)->get();	
			$sub_categories[] = (object) array('id'=>'0','categorie' => 'All' ,'parent_category' => '0');
			foreach ($sub_categories1 as $row) {
				$sub_categories[]=$row;
			}
			$date = Carbon::now()->format('m/d/Y');
			$date1 = Carbon::now()->format('Y-m-d');
			$mallDetails = $this->mallDetails();
			$res = array(
				"categories" => 		$categories,
				"sub_categories" => 	$sub_categories,
				"date" => 				$date,
				"date1" =>				$date1,
				"reorders" => 			$reorders,
				"mallDetails" => 		$mallDetails,
				);

			if($request->ajax()){
				return Response($res,200);
			}else{
				return View('pages.reports.stock_reorder.stock_reorder', $res);
			}
		}catch(Exception $e){
			Log::error("StockReorderController@index ".$e);
			return View("errors.exception");
		}
	}

	/**
	 * Select data which is displayed on report view table
	 * @param  REquest $request [description]
	 * @return [type]           [description]
	 */
	public function getLedger(Request $request){	
		try{	
			$dateArray = $request->all()["data"]; 
			$fromDate = $dateArray['fromDate'];
			$toDate = $dateArray['toDate'];
			$category_id = $dateArray['category_id'];
			$sub_category_id = $dateArray['sub_category_id'];
			$reorder = $dateArray['reorder'];
			$date = Carbon::now()->format('Y-m-d');
			$categoryResponse = $this->getStockReorderQuery($fromDate,$toDate,$category_id,$sub_category_id,$reorder);
			$labels = $this->getLabels();
			$res = array(						
				"categoryResponse" => 		$categoryResponse,	
				"labels" =>					$labels,
				"toDatePrint" => 			(new Carbon($toDate))->format($this->rDateFormat),
				"fromDatePrint" => 			(new Carbon($fromDate))->format($this->rDateFormat),	
				);
			if($request->ajax()){
				return Response($res,200);
			}else{
				return View("pages.reports.stock_reorder.stock_reorder",$res);
			}
		}catch(Exception $e){
			Log::error("StockReorderController@getLedger ".$e);
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
	 			$category_id = $dateArray['category'];
	 			$sub_category_id = $dateArray['sub_category'];
	 			$reorder = $dateArray['reorder'];
	 			$date = Carbon::now()->format('Y-m-d');
	 			$categoryResponse = $this->getStockReorderQuery($fromDate,$toDate,$category_id,$sub_category_id,$reorder);
	 			$fromDate = date($this->rDateFormat, strtotime($fromDate));
	 			$toDate = date($this->rDateFormat, strtotime($toDate));
	 			if(Input::get('pdf')){	
	 				$pdf = PDF::loadView('pages.reports.stock_reorder.stock_reorder_pdf', [
	 					"categoryResponse" => 		$categoryResponse ,
	 					"fromDate" => 				$fromDate,
	 					"toDate" => 				$toDate,
	 					"i" =>						'1'
	 					]); 
	 				$pdf->setPaper('a4');
	 				$carbon = Carbon::today();
	 				$timestamp = $carbon->timestamp;
	 				$format = $carbon->format($this->rDateFormat);
	 				$exportName = 'StockReorderReport-'.$format.'.pdf';     
	 				return $pdf->download($exportName);
	 			}elseif (Input::get('xls')) {
	 				$carbon = Carbon::today();
	 				$timestamp = $carbon->timestamp;
	 				$format = $carbon->format($this->rDateFormat);
	 				$exportName = 'StockReorderReport-'.$format; 
	 				Excel::create($exportName, function ($excel) 
	 					use($categoryResponse,
	 						$fromDate,
	 						$toDate) {
	 						$excel->sheet('StockReorder', function ($sheet) 
	 							use($categoryResponse,
	 								$fromDate,
	 								$toDate) {
	 								$sheet->appendRow(array('', ''));
	 								$sheet->appendRow(array(trans('glob.from'), $fromDate ,trans('glob.to'),$toDate ));
	 								$sheet->row($sheet->getHighestRow(), function ($row) {
	 									$row->setFontWeight('bold');
	 								});	
	 								$sheet->appendRow(array('', ''));	
	 								/* setting column names for data - you can of course set it manually*/
	 								$sheet->appendRow(array(
	 									"#" ,	
	 									trans('item.item_name') ,	
	 									trans('glob.barcode'),
	 									trans('item.refrence_number') ,
	 									trans('glob.stock') ,
	 									trans('glob.reorder_level') ,
	 									trans('glob.purchase_qty') ,
	 									trans('glob.unit') ,					
	 									)); 
	 								/* getting last row number (the one we already filled and setting it to bold*/
	 								$sheet->row($sheet->getHighestRow(), function ($row) {
	 									$row->setFontWeight('bold');
	 								});
	 								foreach ($categoryResponse as $p) {
	 									$sheet->appendRow( array(
	 										"#"  => $p->name,	
	 										trans('item.item_name') => '',	
	 										trans('glob.barcode') => '',
	 										trans('item.refrence_number') => '',
	 										trans('glob.stock') => '',
	 										trans('glob.reorder_level') => '',
	 										trans('glob.purchase_qty') => '',
	 										trans('glob.unit') => '',					
	 										)
	 									);
	 									$i=0;
	 									foreach ($p->child as $o) {
	 										$tempStock = 0;
	 										if($o->stock != null){
	 											$tempStock = $o->stock;
	 										}else{
	 											$tempStock = 0;
	 										}
	 										if($o->barcode == null){
	 											$barcode="NA";
	 										}else{
	 											$barcode= $o->barcode;
	 										}
	 										if($o->serial_number == null){
	 											$serial_number="NA";
	 										}else{
	 											$serial_number=$o->serial_number;
	 										}
	 										$i++;						
	 										$sheet->appendRow( array(
	 											"#"  => $i,	
	 											trans('item.item_name') => $o->itemName,							
	 											trans('glob.barcode') => $barcode,
	 											trans('item.refrence_number') => $serial_number,

	 											trans('glob.stock') => " ".Common::fixQty($tempStock),
	 											trans('glob.reorder_level') => " ".Common::fixQty($o->min),
	 											trans('glob.purchase_qty') => " ".Common::fixQty(($o->min - $o->stock)),
	 											trans('glob.unit') => $o->unitName,					
	 											)
	 										);
	 									}
	 								}
	 							});
	 					})->export('xlsx');
	 				return back();
	 			}
	 		}catch(Exception $e){
	 			Log::error("StockReorderController@exportTo ".$e);
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
				"dis_amt" => trans('billing.dis_amt'),
				"offer_item" => trans('glob.offer_item'),
				"offer_qty" => trans('glob.offer_qty'),
				"promo_rate" => trans('glob.promo_rate'),
				"c_p" => trans('item.c_p'),
				"s_p" => trans('item.s_p'),
				"stock" => trans('glob.stock'),
				"initial_stock" => trans('glob.initial_stock'),	
				"open_stock"	=> trans('glob.open_stock'),
				"purchase"	=> trans('glob.purchase'),
				"purchase_return" => trans('glob.purchase_return'),
				"issued_sale" => trans('glob.issued_sale'),	
				"sale_return" => trans('glob.sale_return'),
				"balance_stock"	=> trans('glob.balance_stock'),
				"reorder_level"	=> trans('glob.reorder_level'),
				"purchase_qty"	=> trans('glob.purchase_qty'),
				"unit"	=> trans('glob.unit'),	
				"barcode" => trans('glob.barcode'),	
				"website" => trans('glob.website'), 
				"phone" => trans('glob.phone'),		
				"refrence_number"		=>trans('item.refrence_number'),
				)
			);
			return $labels;
		}catch(Exception $e){
			Log::error("StockReorderController@getLabels ".$e);
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
			Log::error("StockReorderController@mallDetails ".$e);
			return View("errors.exception");
		}
	}


	public function getStockReorderQuery($fromDate,$toDate,$category_id,$sub_category_id,$reorder){
		try{
			$category_id = $category_id ?? 0;
			$sub_category_id = $sub_category_id ?? 0;
			$reorder = $reorder ?? 0;
			$categoryResponse = DB::select("SELECT categories.id,
				categories.categorie as name 
				FROM items INNER JOIN categories on items.category=categories.id 
				where CASE WHEN $category_id > 0 THEN categories.parent_category = $category_id ELSE true END and 
				CASE WHEN $reorder = 1 THEN items.stock < items.min  ELSE true END and 
				CASE WHEN $sub_category_id > 0 THEN categories.id=$sub_category_id ELSE true END GROUP by categories.id");
			foreach ($categoryResponse as $row) {		 	
				$p=DB::select("SELECT items.id,items.serial_number,
					items.barcode , items.itemName,
					items.min ,
					(SELECT IFNULL(stocks.stock, 0) from stocks 
					where stocks.item_id = items.id and 
					stocks.date <= '".$toDate."' 
					ORDER BY STR_TO_DATE(stocks.date, '%y-%m-%d'),stocks.id DESC LIMIT 1) as stock,
					(SELECT categories.categorie 
					from categories 
					where categories.id = ".$row->id." and 
					categories.parent_category!=0)  as catName ,
					(SELECT units.unit 
					from units 
					where units.id = items.unit ) as unitName 
					FROM items 
					where CASE WHEN $reorder = 1 THEN items.stock < items.min ELSE true END and
					items.category=".$row->id);
				if(sizeof($p)>0){
					foreach($p as $prow){
						$balanceStock=DB::select("SELECT 
							IFNULL(stocks.stock, 0) as balanceStock
							FROM
							stocks
							WHERE    
							(
							CASE WHEN stocks.type IN(6, 7, 8, 11) THEN  stocks.item_id IN(
							SELECT
							billing_items.id
							FROM
							billing_items
							WHERE
							billing_items.item_id=".$prow->id." and 
							billing_items.id = stocks.item_id
							) WHEN stocks.type = 9 THEN  stocks.item_id IN(
							SELECT
							bill_return_items.id
							FROM
							bill_return_items
							WHERE
							bill_return_items.item_id= ".$prow->id."  and
							bill_return_items.id = stocks.item_id
							) ELSE  stocks.item_id = ".$prow->id." 
							END
							)
							AND stocks.date <= '".$toDate."' ORDER BY stocks.id DESC LIMIT 1");
						if(sizeof($balanceStock)>0){
							$prow->stock = $balanceStock[0]->balanceStock;
						}else{
							$prow->stock = 0;
						}
					}					
				}
				$row->child = $p;				
			}
			return $categoryResponse;
		}catch(Exception $e){
			Log::error("StockReorderController@getStockReorderQuery ".$e);
			return View("errors.exception");
		}
	}
}
