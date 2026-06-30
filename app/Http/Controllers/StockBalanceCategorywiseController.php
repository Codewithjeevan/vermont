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

class StockBalanceCategorywiseController extends Controller
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
				return View('pages.reports.stock_balance_categorywise.stock_balance_categorywise', $res);
			}
		}
		catch(Exception $e){
			Log::error("StockBalanceCategorywiseController@index ".$e);
			return View("errors.exception");
		}
	}

	public function getLedger(REquest $request){		
		try{
			$dateArray = $request->all()["data"]; 
			$fromDate = $dateArray['fromDate'];
			$toDate = $dateArray['toDate'];		
			$date = Carbon::now()->format('Y-m-d');
			$category_id = $dateArray['category_id'];
			$sub_category_id = $dateArray['sub_category_id'];		
			

			$data = $this->getStockBalanceQuery($fromDate,$toDate,$category_id,$sub_category_id);

			
			$labels = $this->getLabels();
			$res = array(						
				"categoryResponse" => 	$data['categoryResponse'],	
				"totalCP"=>				$data['totalCP'],
				"totalSP"=>				$data['totalSP'],
				"totalStock"=>			$data['totalStock'],
				"total"=>				$data['total'],
				"labels" =>				$labels,
				"toDatePrint" => 		(new Carbon($toDate))->format($this->rDateFormat),
				"fromDatePrint" => 		(new Carbon($fromDate))->format($this->rDateFormat),	

			);
			if($request->ajax()){
				return Response($res,200);
			}else{
				return View("pages.reports.stock_balance_categorywise.stock_balance_categorywise",$res);
			}
		}
		catch(Exception $e){
			Log::error("StockBalanceCategorywiseController@getLedger ".$e);
			return View("errors.exception");
		}
	}

	public function exportTo(Request $request){
		try{
			$dateArray = $request->all(); 
			$fromDate = $dateArray['from'];
			$toDate = $dateArray['to'];			
			$date = Carbon::now()->format('Y-m-d');

			$category_id = $dateArray['category'];
			$sub_category_id = $dateArray['sub_category'];		
			$totalCP = 0;
			$totalSP = 0 ;
			$totalStock = 0 ;
			$total =0;
			$currency=$this->optioncurr;

			$data = $this->getStockBalanceQuery($fromDate,$toDate,$category_id,$sub_category_id);
			
			$fromDate = date($this->rDateFormat, strtotime($fromDate));
			$toDate = date($this->rDateFormat, strtotime($toDate));
			$totalCP =  $data['totalCP'];
			$totalSP =  $data['totalSP'] ;
			$totalStock =  $data['totalStock'] ;
			$total = $data['total'];
			$categoryResponse = $data['categoryResponse'];



			if(Input::get('pdf')){	
				ini_set('max_execution_time', 600);
				

				$pdf = PDF::loadView('pages.reports.stock_balance_categorywise.stock_balance_categorywise_pdf', [
					"categoryResponse" => 	$categoryResponse ,
					"totalCP"=>				$totalCP,
					"totalSP"=>				$totalSP,
					"totalStock"=>			$totalStock,
					"total"=>				$total,
					"fromDate" => 			$fromDate,
					"toDate" => 			$toDate,
					"i" =>					'1',
					"curr"=>				$currency
				]); 

				$pdf->setPaper('a4');
				$carbon = Carbon::today();
				$timestamp = $carbon->timestamp;
				$format = $carbon->format($this->rDateFormat);
				$exportName = 'StockBalanceReport-'.$format.'.pdf';     
				return $pdf->download($exportName);

			}elseif (Input::get('xls')) {
				$carbon = Carbon::today();
				$timestamp = $carbon->timestamp;
				$format = $carbon->format($this->rDateFormat);
				$exportName = 'StockBalanceReport-'.$format; 


				\App\Legacy\LegacyExcel::create($exportName, function ($excel) 
					use($categoryResponse,
						$totalCP,
						$totalSP,
						$totalStock,
						$total,
						$fromDate,
						$toDate,
						$currency) {

						$excel->sheet('StockBalance', function ($sheet)
							use($categoryResponse,
								$totalCP,
								$totalSP,
								$totalStock,
								$total,
								$fromDate,
								$toDate,
								$currency) {	


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
									trans('glob.stock') ,								
									trans('item.c_p') ,
									trans('item.s_p') ,
									trans('billing.amt')."(".$currency.")" ,							
								)); 


								/* getting last row number (the one we already filled and setting it to bold*/
									$sheet->row($sheet->getHighestRow(), function ($row) {
										$row->setFontWeight('bold');
									});


									$itemList = array();
									$totalStock = 0;
									$total = 0;

									foreach ($categoryResponse as $p) {

										$sheet->appendRow( array(
											"#"  => $p->name,	
											trans('item.item_name') => '',							
											trans('glob.barcode') => '',
											trans('item.refrence_number') => '',			
											trans('glob.stock') => '',								
											trans('item.c_p') => '',
											trans('item.s_p') => '',
											trans('billing.amt')."(".$currency.")" => '',							
										)
									);

										$i=0;
										$stockM = 0;
										$amtM = 0;
										foreach ($p->child as $o) {
											$i++;
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
											
											$stock = ($o->balanceStock + $o->openingStock + $o->purchase - $o->purchaseReturn - $o->sale - $o->displayStock - $o->damageStock - $o->package - $o->offer + $o->saleReturn - $o->expiryStock + $o->stockAdjustmentExcess + $o->stockAdjustmentShortageWastage);
											$stockM = $stockM + $stock;
                    						$amtM = (($amtM) + ($stock * $o->costPrise));					
											$sheet->appendRow( array(
												"#"  => $i,	
												trans('item.item_name') => $o->itemName,							
												trans('glob.barcode') => $barcode,
												trans('item.refrence_number') => " ".$serial_number,			
												trans('glob.stock') => " ".Common::fixQty($stock),								
												trans('item.c_p')."(".$currency.")" => " ".Common::fixIt($o->costPrise),
												trans('item.s_p')."(".$currency.")" => " ".Common::fixIt($o->salePrise),
												trans('billing.amt')."(".$currency.")" => " ".Common::fixIt($stock * $o->costPrise),							
											)
										);
										}										

										$totalStock = $totalStock + $stockM;
                  						$total = $total + $amtM;

										$sheet->appendRow( array(
											"#"  => '',											
											trans('item.item_name') => '',	
											trans('glob.barcode') => '',			
											trans('item.refrence_number') => '',							
											trans('glob.stock') => trans('glob.stock')." = ".Common::fixQty($stockM),		
											trans('item.c_p')."(".$currency.")" => trans('item.c_p')."=".Common::fixIt($p->costPrise),
											trans('item.s_p')."(".$currency.")" => trans('item.s_p')." = ".Common::fixIt($p->salePrise),			
											trans('billing.amt')."(".$currency.")" => trans('billing.amt')." = ".Common::fixIt($amtM),				
										)
									);
									}

									$sheet->appendRow( array(
										"#"  => '',											
										trans('item.item_name') => '',	
										trans('glob.barcode') => '',			
										trans('item.refrence_number') => trans('glob.total'),							
										trans('glob.stock') => " ".Common::fixQty($totalStock),		
										trans('item.c_p')."(".$currency.")" => " ".Common::fixIt($totalCP),
										trans('item.s_p')."(".$currency.")" => " ".Common::fixIt($totalSP),			
										trans('billing.amt')."(".$currency.")" => " ".Common::fixIt($total),				
									)
								);

									
									$sheet->row($sheet->getHighestRow(), function ($row) {
										$row->setFontWeight('bold');
									});
								});

})->export('xlsx');

return back();

}
}
catch(Exception $e){
	Log::error("StockBalanceCategorywiseController@exportTo ".$e);
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
				"barcode" => trans('glob.barcode'),
				"website" => trans('glob.website'), 
				"phone" => trans('glob.phone'),
				"refrence_number"=>trans('item.refrence_number'),
			)
		);
			return $labels;
		}
		catch(Exception $e){
			Log::error("StockBalanceCategorywiseController@getLabels ".$e);
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
		}
		catch(Exception $e){
			Log::error("StockBalanceCategorywiseController@mallDetails ".$e);
			return View("errors.exception");
		}
	}

	public function getStockBalanceQuery($fromDate,$toDate,$category_id,$sub_category_id){
		try{
			$totalCP = 0;
			$totalSP = 0 ;
			$totalStock = 0 ;
			$total = 0;
			

			/*$categoryResponse=DB::select("SELECT categories.id,
				categories.categorie as name,
				sum(items.costPrise) as costPrise ,
				sum(items.salePrise) as salePrise,
				sum(items.costPrise * ((SELECT 
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
				billing_items.item_id=items.id and 
				billing_items.id = stocks.item_id
				) WHEN stocks.type = 9 THEN  stocks.item_id IN(
				SELECT
				bill_return_items.id
				FROM
				bill_return_items
				WHERE
				bill_return_items.item_id= items.id  and
				bill_return_items.id = stocks.item_id
				) ELSE  stocks.item_id = items.id 
				END
				)
				AND stocks.date <= '".$toDate."' ORDER BY stocks.id DESC LIMIT 1))) as amt,
				sum((SELECT 
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
							billing_items.item_id=items.id and 
							billing_items.id = stocks.item_id
						) WHEN stocks.type = 9 THEN  stocks.item_id IN(
							SELECT
							bill_return_items.id
							FROM
							bill_return_items
							WHERE
							bill_return_items.item_id= items.id  and
							bill_return_items.id = stocks.item_id
						) ELSE  stocks.item_id = items.id 
						END
					)
					AND stocks.date <= '".$toDate."' ORDER BY stocks.id DESC LIMIT 1)) as stock 
					FROM items INNER JOIN categories on items.category=categories.id 
					WHERE CASE WHEN  $category_id > 0 THEN categories.parent_category = $category_id ELSE true END and 
					CASE WHEN $sub_category_id > 0 THEN items.category = $sub_category_id ELSE true END GROUP BY items.category");

			foreach ($categoryResponse as $row) {		 	
				$p=DB::select("SELECT items.serial_number,
					items.barcode , 
					items.itemName,
					items.costPrise ,
					items.salePrise, 
					(items.costPrise * ((SELECT 
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
					billing_items.item_id=items.id and 
					billing_items.id = stocks.item_id
					) WHEN stocks.type = 9 THEN  stocks.item_id IN(
					SELECT
					bill_return_items.id
					FROM
					bill_return_items
					WHERE
					bill_return_items.item_id= items.id  and
					bill_return_items.id = stocks.item_id
					) ELSE  stocks.item_id = items.id 
					END
					)
					AND stocks.date <= '".$toDate."' ORDER BY stocks.id DESC LIMIT 1))) as amt,
					((SELECT 
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
								billing_items.item_id=items.id and 
								billing_items.id = stocks.item_id
							) WHEN stocks.type = 9 THEN  stocks.item_id IN(
								SELECT
								bill_return_items.id
								FROM
								bill_return_items
								WHERE
								bill_return_items.item_id= items.id  and
								bill_return_items.id = stocks.item_id
							) ELSE  stocks.item_id = items.id 
							END
						)
						AND stocks.date <= '".$toDate."' ORDER BY stocks.id DESC LIMIT 1)) as stock 
						FROM items where items.category=".$row->id);
				$row->child = $p;



				$totalCP = $row->costPrise + $totalCP;
				$totalSP = $row->salePrise +$totalSP ;
				$totalStock = $row->stock +$totalStock ;
				$total = $row->amt + $total;
			}*/

			$categoryResponse=DB::select("SELECT categories.id,
				categories.categorie as name , sum(items.costPrise) as costPrise ,
				sum(items.salePrise) as salePrise
				FROM items INNER JOIN categories on items.category=categories.id 
				WHERE CASE WHEN $category_id > 0 THEN categories.parent_category = $category_id ELSE true END and 
				CASE WHEN $sub_category_id > 0 THEN items.category = $sub_category_id ELSE true END GROUP BY items.category");
			foreach ($categoryResponse as $row) {	
				$p=DB::select("SELECT i.serial_number,i.barcode , i.itemName,	
					i.costPrise,
					i.salePrise, 		
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
								(SELECT IFNULL(sum(stock_adjustment_items.quantity), 0) 
							FROM stock_adjustment_items inner JOIN stocks on stock_adjustment_items.stock_adjustment_id = stocks.type_id 
							where stocks.item_id = i.id and stock_adjustment_items.item_id = i.id and stock_adjustment_items.adjust_through = 1 and 
							stocks.type =18 and stocks.date BETWEEN '".$fromDate."' and '".$toDate."')  as stockAdjustmentExcess,
							(SELECT -(IFNULL(sum(stock_adjustment_items.quantity), 0)) 
							FROM stock_adjustment_items inner JOIN stocks on stock_adjustment_items.stock_adjustment_id = stocks.type_id 
							where stocks.item_id = i.id and stock_adjustment_items.item_id = i.id and stock_adjustment_items.adjust_through != 1 and 
							stocks.type =18 and stocks.date BETWEEN '".$fromDate."' and '".$toDate."')  as stockAdjustmentShortageWastage
					
								FROM items i where i.category=".$row->id);
				$row->child = $p;

				$totalCP = $row->costPrise + $totalCP;
				$totalSP = $row->salePrise +$totalSP ;
			}

			return array(
				"categoryResponse"=>$categoryResponse,
				"totalCP" => $totalCP,
				"totalSP" => $totalSP ,
				"totalStock" => $totalStock ,
				"total" => $total,
			);
		}
		catch(Exception $e){
			Log::error("StockBalanceCategorywiseController@getStockBalanceQuery ".$e);
			return View("errors.exception");
		}	
	}


}
