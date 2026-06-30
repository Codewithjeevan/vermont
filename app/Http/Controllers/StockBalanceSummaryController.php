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

class StockBalanceSummaryController extends Controller
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
				"categories" => $categories,
				"sub_categories" => $sub_categories,
				"date" => $date,
				"date1" => $date1,
				"mallDetails" => $mallDetails,
			);
			if($request->ajax()){
				return Response($res,200);
			}else{
				return View('pages.reports.stock_balance_summary.stock_balance_summary', $res);
			}
		}catch(Exception $e){
			Log::error("StockBalanceSummaryReport@index ".$e);
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
			$category_id = $dateArray['category_id'];
			$sub_category_id = $dateArray['sub_category_id'];
			$date = Carbon::now()->format('Y-m-d');	
			$categoryResponse = $this->getStockBalanceSummaryQuery($fromDate,$toDate,$category_id,$sub_category_id);	
			$labels = $this->getLabels();
			$res = array(						
				"categoryResponse" => 	$categoryResponse,	
				"labels" => 			$labels,
				"toDatePrint" => 		(new Carbon($toDate))->format($this->rDateFormat),
				"fromDatePrint" => 		(new Carbon($fromDate))->format($this->rDateFormat),	
			);
			if($request->ajax()){
				return Response($res,200);
			}else{
				return View("pages.reports.stock_balance_summary.stock_balance_summary",$res);
			}
		}catch(Exception $e){
			Log::error("StockBalanceSummaryReport@getLedger ".$e);
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
			$date = Carbon::now()->format('Y-m-d');
			$categoryResponse = $this->getStockBalanceSummaryQuery($fromDate,$toDate,$category_id,$sub_category_id);
			$fromDate = date($this->rDateFormat , strtotime($fromDate));
			$toDate = date($this->rDateFormat , strtotime($toDate));
			if(Input::get('pdf')){
				ini_set('max_execution_time', 300); /*300 seconds = 5 minutes*/
				ini_set('memory_limit', '3000M'); /*This might be too large, but depends on the data set*/
				$pdf = PDF::loadView('pages.reports.stock_balance_summary.stock_balance_summary_pdf', [
					"categoryResponse" => 	$categoryResponse ,
					"fromDate" => 			$fromDate,
					"toDate" => 			$toDate,
					"i" =>					'1'
				]); 
				$pdf->setPaper('a4');
				$carbon = Carbon::today();
				$timestamp = $carbon->timestamp;
				$format = $carbon->format($this->rDateFormat );
				$exportName = 'StockBalanceSummaryReport-'.$format.'.pdf';     
				return $pdf->download($exportName);
			}elseif (Input::get('xls')) {
				ini_set('max_execution_time', 300); /*300 seconds = 5 minutes*/
				ini_set('memory_limit', '3000M'); /*This might be too large, but depends on the data set*/
				$carbon = Carbon::today();
				$timestamp = $carbon->timestamp;
				$format = $carbon->format($this->rDateFormat );
				$exportName = 'StockBalanceSummaryReport-'.$format; 
				\App\Legacy\LegacyExcel::create($exportName, function ($excel) 
					use($categoryResponse,
						$fromDate,
						$toDate) {
						$excel->sheet('StockBalanceSummary', function ($sheet) 
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
									trans('glob.barcode') ,
									trans('item.refrence_number') ,
									trans('glob.initial_stock') ,	
									trans('glob.open_stock') ,				
									trans('glob.purchase') ,
									trans('glob.purchase_return') ,
									trans('glob.issued_sale') ,
									trans('glob.sale_return') ,
									trans('glob.adjustment') ,
									trans('glob.balance_stock') ,		
								)); 
								/* getting last row number (the one we already filled and setting it to bold*/
									$sheet->row($sheet->getHighestRow(), function ($row) {
										$row->setFontWeight('bold');
									});
									$i=1;
									if(sizeof($categoryResponse) >0){
										foreach ($categoryResponse as $p) {					
											$sheet->appendRow( array(
												"#"  => $p->name,	
												trans('item.item_name') => '',										
												trans('glob.barcode') => '',
												trans('item.refrence_number') => '',
												trans('glob.initial_stock') => '',	
												trans('glob.open_stock') => '',				
												trans('glob.purchase') => '',
												trans('glob.purchase_return') => '',
												trans('glob.issued_sale') => '',
												trans('glob.sale_return') => '',
												trans('glob.adjustment') => '',
												trans('glob.balance_stock') =>  '',		
											)
										);
											$i=1;
											foreach ($p->child as $o) {
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
												if($o->balanceStock == null){
													$balanceStock = 0;
												}else{
													$balanceStock = $o->balanceStock;
												}					
												$sheet->appendRow( array(
													"#"  => $i++,		
													trans('item.item_name') => $o->itemName,									
													trans('glob.barcode') => $barcode,
													trans('item.refrence_number') => $serial_number,
													trans('glob.initial_stock') => " ".Common::fixQty($balanceStock),	
													trans('glob.open_stock') => " ".Common::fixQty($o->openingStock),				
													trans('glob.purchase') => " ".Common::fixQty($o->purchase),
													trans('glob.purchase_return') => " ".Common::fixQty($o->purchaseReturn),
													trans('glob.issued_sale') => " ".Common::fixQty(($o->sale + $o->displayStock + $o->damageStock + $o->package +$o->offer + $o->expiryStock)),
													trans('glob.sale_return') => " ".Common::fixQty($o->saleReturn),
													trans('glob.adjustment') => " ".Common::fixQty($o->stockAdjustmentExcess + $o->stockAdjustmentShortageWastage),
													trans('glob.balance_stock') => " ".Common::fixQty(($balanceStock + $o->openingStock + $o->purchase - $o->purchaseReturn - $o->sale - $o->displayStock - $o->damageStock - $o->package - $o->offer + $o->saleReturn - $o->expiryStock + $o->stockAdjustmentExcess + $o->stockAdjustmentShortageWastage)),		
												)
											);
											}
										}
									}
								});
					})->export('xlsx');
				return back();

			}
		}catch(Exception $e){
			Log::error("StockBalanceSummaryReport@exportTo ".$e);
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
				"barcode" => trans('glob.barcode'),	
				"website" => trans('glob.website'), 
				"phone" => trans('glob.phone'),	
				"refrence_number"=>trans('item.refrence_number'),
				"adjustment"	=> trans('glob.adjustment'),
			)
		);
			return $labels;
		}catch(Exception $e){
			Log::error("StockBalanceSummaryReport@getLabels ".$e);
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
			Log::error("StockBalanceSummaryReport@mallDetails ".$e);
			return View("errors.exception");
		}
	}

	public function getStockBalanceSummaryQuery($fromDate,$toDate,$category_id,$sub_category_id){
		try{
			$categoryResponse=DB::select("SELECT categories.id,
				categories.categorie as name 
				FROM items INNER JOIN categories on items.category=categories.id 
				WHERE CASE WHEN $category_id > 0 THEN categories.parent_category = $category_id ELSE true END and 
				CASE WHEN $sub_category_id > 0 THEN items.category = $sub_category_id ELSE true END GROUP BY items.category");
			foreach ($categoryResponse as $row) {	
				$p=DB::select("SELECT i.serial_number,i.barcode , i.itemName,			
					(SELECT 
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
					billing_items.item_id=i.id and 
					billing_items.id = stocks.item_id
					) WHEN stocks.type = 9 THEN  stocks.item_id IN(
					SELECT
					bill_return_items.id
					FROM
					bill_return_items
					WHERE
					bill_return_items.item_id= i.id  and
					bill_return_items.id = stocks.item_id
					) ELSE  stocks.item_id = i.id 
					END
					)
					AND stocks.date < '".$fromDate."' ORDER BY stocks.id DESC LIMIT 1) as balanceStock,
					(SELECT IFNULL(sum(opening_stock.openingStock), 0) 
					FROM opening_stock inner JOIN stocks on opening_stock.id = stocks.type_id 
					where stocks.item_id = i.id and opening_stock.itemId = i.id and 
					stocks.type = 1 and stocks.date BETWEEN '".$fromDate."' and '".$toDate."') as openingStock,
					(SELECT IFNULL(sum(purchased_items.qty), 0) 
					FROM purchased_items inner JOIN stocks on purchased_items.purchase_id = stocks.type_id 
					where stocks.item_id = i.id and purchased_items.item_id = i.id and 
					stocks.type = 2 and stocks.date BETWEEN '".$fromDate."' and '".$toDate."') as purchase,
					(SELECT IFNULL(sum(purchase_return_items.qty), 0)  
					FROM purchase_return_items inner JOIN stocks on purchase_return_items.purchase_return_id = stocks.type_id
					where stocks.item_id = i.id and purchase_return_items.item_id = i.id and 
					stocks.type =3 and stocks.date BETWEEN '".$fromDate."' and '".$toDate."') as purchaseReturn,
					(SELECT
						IFNULL(sum(billing_items.qty), 0)
						FROM
						billing_items inner JOIN stocks
						WHERE
						stocks.type in(6,11) and
						billing_items.item_id=i.id and 
						billing_items.id = stocks.item_id and CAST(stocks.date AS DATE) BETWEEN '".$fromDate."' and '".$toDate."'
					)  as sale ,
					(SELECT IFNULL(sum(issue_stock_items.quantity), 0) 
					FROM issue_stock_items inner JOIN stocks on issue_stock_items.issue_stocks_id = stocks.type_id 
					where stocks.item_id = i.id and issue_stock_items.item_id = i.id and 
					stocks.type =4 and stocks.date BETWEEN '".$fromDate."' and '".$toDate."')  as displayStock ,
					(SELECT IFNULL(sum(damage_stock_items.quantity), 0) 
					FROM damage_stock_items inner JOIN stocks on damage_stock_items.damage_order_id = stocks.type_id 
					where stocks.item_id = i.id and damage_stock_items.item_id = i.id and 
					stocks.type =5 and stocks.date BETWEEN '".$fromDate."' and '".$toDate."') as damageStock ,
					(SELECT
						IFNULL(sum(billing_items.qty), 0)
						FROM
						billing_items inner JOIN stocks
						WHERE
						stocks.type=7 and
						billing_items.item_id=i.id and 
						billing_items.id = stocks.item_id and CAST(stocks.date AS DATE) BETWEEN '".$fromDate."' and '".$toDate."') as package,
						(SELECT
							IFNULL(sum(billing_items.qty), 0)
							FROM
							billing_items inner JOIN stocks
							WHERE
							stocks.type=8 and
							billing_items.item_id=i.id and 
							billing_items.id = stocks.item_id and CAST(stocks.date AS DATE) BETWEEN '".$fromDate."' and '".$toDate."')  as offer,
							(SELECT
								IFNULL(sum(bill_return_items.qty), 0)
								FROM
								bill_return_items inner JOIN stocks
								WHERE
								stocks.type=9 and
								bill_return_items.item_id=i.id and 
								bill_return_items.id = stocks.item_id and CAST(stocks.date AS DATE) BETWEEN '".$fromDate."' and '".$toDate."')  as saleReturn,
								(SELECT IFNULL(sum(expiry_items.quantity),0) 
								FROM expiry_items inner JOIN stocks on expiry_items.expiry_order_id = stocks.type_id 
								where stocks.item_id = i.id and expiry_items.item_id = i.id and stocks.type =10 and 
								stocks.date BETWEEN '".$fromDate."' and '".$toDate."')  as expiryStock,
								(SELECT (IFNULL(SUM(stock_adjustment_items.quantity),0)) 
								FROM stock_adjustment_items inner JOIN stocks on stock_adjustment_items.stock_adjustment_id = stocks.type_id 
								where stocks.item_id = i.id and stock_adjustment_items.item_id = i.id and stock_adjustment_items.adjust_through = 1 and 
								stocks.type = 18 and stocks.date BETWEEN '".$fromDate."' and '".$toDate."')  as stockAdjustmentExcess,
								(SELECT -(IFNULL(SUM(stock_adjustment_items.quantity),0))
								FROM stock_adjustment_items inner JOIN stocks on stock_adjustment_items.stock_adjustment_id = stocks.type_id 
								where stocks.item_id = i.id and stock_adjustment_items.item_id = i.id and stock_adjustment_items.adjust_through != 1 and 
								stocks.type = 18 and stocks.date BETWEEN '".$fromDate."' and '".$toDate."')  as stockAdjustmentShortageWastage
								
								FROM items i where i.category=".$row->id);
				$row->child = $p;
			}
			return $categoryResponse;
		}catch(Exception $e){
			Log::error("StockBalanceSummaryReport@getStockBalanceSummaryQuery ".$e);
			return View("errors.exception");
		}
	}
}
