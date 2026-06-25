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


class StockLedgerController extends Controller
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

			$items = array();
			$items[] = (object) array('id'=>'0','itemName' => 'All', 'barcode' =>'All');

			$categories=Category::all();
			$date = Carbon::now()->format('m/d/Y');
			$date1 = Carbon::now()->format('Y-m-d');
			$mallDetails = $this->mallDetails();
			$res = array(
				"items" => $items,
				"categories" => $categories,
				"date" => $date,
				"date1" => $date1,
				"mallDetails" => $mallDetails,
			);

			if($request->ajax()){
				return Response($res,200);
			}else{
				return View('pages.reports.stock_ledger.stock_ledger', $res);
			}
		}
		catch(Exception $e){
			Log::error("StockLedgerController@index ".$e);
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
			ini_set('max_execution_time', 300); /*300 seconds = 5 minutes */
			$dateArray = $request->all()["data"]; 
			$fromDate = $dateArray['fromDate'];
			$toDate = $dateArray['toDate'];
			$item_id = $dateArray['item_id'];
			$initial_stock=0;		
			$purchases = array();

			$purchases = $this->getLedgerQuery($item_id,$fromDate,$toDate);

			$labels = $this->getLabels();

			$items[] = (object) array('id'=>'0','itemName' => 'All', 'barcode' =>'All');	

			$res = array(
				"items"  => $items,			
				"purchases" => $purchases,
				"initial_stock" => $initial_stock,	
				"labels" => $labels,	
				"toDatePrint" => (new Carbon($toDate))->format($this->rDateFormat),
				"fromDatePrint" => (new Carbon($fromDate))->format($this->rDateFormat),		
			);
			if($request->ajax()){
				return Response($res,200);
			}else{
				return View("pages.reports.stock_ledger.stock_ledger",$res);
			}
		}
		catch(Exception $e){
			Log::error("StockLedgerController@getLedger ".$e);
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
	 			ini_set('max_execution_time', 1200);				
	 			$dateArray = $request->all(); 
	 			$fromDate = $dateArray['from'];
	 			$toDate = $dateArray['to'];
	 			$item_id = $dateArray['Supplier'];

	 			$purchases = $this->getLedgerQuery($item_id,$fromDate,$toDate);

	 			$fromDate = date($this->rDateFormat, strtotime($fromDate));
	 			$toDate = date($this->rDateFormat, strtotime($toDate));
	 			if(Input::get('pdf')){		


	 				$pdf = PDF::loadView('pages.reports.stock_ledger.stock_ledger_pdf',[
	 					"purchases" => $purchases ,
	 					"fromDate" => $fromDate,
	 					"toDate" => $toDate,
	 					"i" =>'1'
	 				]); 

	 				$pdf->setPaper('a4');
	 				$carbon = Carbon::today();
	 				$timestamp = $carbon->timestamp;
	 				$format = $carbon->format($this->rDateFormat);
	 				$exportName = 'StockLedgerReport'.$format.'.pdf'; 
	 				return $pdf->download($exportName);

	 			}elseif (Input::get('xls')) {
	 				$carbon = Carbon::today();
	 				$timestamp = $carbon->timestamp;
	 				$format = $carbon->format($this->rDateFormat);
	 				$exportName = 'StockLedgerReport'.$format; 


	 				Excel::create($exportName, function ($excel) use(
	 					$purchases,
	 					$fromDate,
	 					$toDate) {

	 					$excel->sheet('PurchaseOrder', function ($sheet) use(
	 						$purchases,
	 						$fromDate,
	 						$toDate) {		

	 						$sheet->appendRow(array('', ''));
	 						$sheet->appendRow(array(trans('glob.from'), $fromDate ,trans('glob.to'),$toDate ));
	 						$sheet->row($sheet->getHighestRow(), function ($row) {
	 							$row->setFontWeight('bold');
	 						});	
	 						$sheet->appendRow(array('', ''));


	 						$itemList = array();

	 						/* setting column names for data - you can of course set it manually*/
	 						$sheet->appendRow(array(
	 							"#" ,											
	 							trans('billing.bill_date') ,
	 							trans('glob.particulars') ,
	 							trans('glob.purchase') ,
	 							trans('glob.sale') ,	
	 							trans('glob.adjustment') ,							
	 							trans('glob.balance_stock'),
	 						)); 


	 						/* getting last row number (the one we already filled and setting it to bold*/
	 							$sheet->row($sheet->getHighestRow(), function ($row) {
	 								$row->setFontWeight('bold');
	 							});

	 							if(sizeof($purchases)>0){				

	 								foreach ($purchases as $p) {
	 									if($p->balanceStock == null){
	 										$initial_stock=0;

	 										$balance_stock = 0;
	 									}else{
	 										$initial_stock=$p->balanceStock;

	 										$balance_stock = $p->balanceStock;
	 									}

	 									$sheet->appendRow( array(
	 										"#"  => $p->itemName,											
	 										trans('billing.bill_date') => '',
	 										trans('glob.particulars') => '',
	 										trans('glob.purchase') => '',
	 										trans('glob.sale') => '',	
	 										trans('glob.adjustment') => '',								
	 										trans('glob.balance_stock') => trans('glob.balance_stock')." = ".Common::fixQty($initial_stock),
	 									)
	 								);

	 									$i=0;
	 									foreach ($p->child as $o) {

	 										$perticulars = '';
	 										$i++;
	 										$purchase = $o->openingStock+$o->purchasedQty+$o->bilReturnQty;
	 										$sale = $o->purchaseReturnQty+$o->issueStockQty+$o->damageStockQty+$o->billingQty+$o->packegeQty+$o->offerQty+$o->expiryQty+$o->billDeletedQty;	
	 										$adjustment = $o->stockAdjustmentQty;

	 										if(($o->type)==1){
	 											$balance_stock = $balance_stock + ($o->openingStock);
	 											$perticulars = "Opening Stock No: ".$o->type_id." Date ".$o->date;
	 										}elseif (($o->type)==2) {
	 											$balance_stock = $balance_stock + ($o->purchasedQty);
	 											$perticulars = "Purchase MRN No: ".$o->type_id." Bill Date ".$o->date;
	 										}
	 										elseif (($o->type)==3) {
	 											$balance_stock = $balance_stock - ($o->purchaseReturnQty);
	 											$perticulars = "Purchase Return MRN No: ".$o->type_id." Supplier BillNo: ".$o->billno." Bill Date ".$o->date;
	 										}
	 										elseif (($o->type)==4) {
	 											$balance_stock = $balance_stock - ($o->issueStockQty);
	 											$perticulars = "Display Stock MRN No: ".$o->type_id." Bill Date ".$o->date;
	 										}
	 										elseif (($o->type)==5) {
	 											$balance_stock = $balance_stock - ($o->damageStockQty);
	 											$perticulars = "Damage MRN No: ".$o->type_id." Bill Date ".$o->date;
	 										}				
	 										elseif (($o->type)==6) {
	 											$balance_stock = $balance_stock - ($o->billingQty);
	 											$perticulars = "Billing No: ".Common::fixBill($o->type_id)." Bill Date ".$o->date;
	 										}
	 										elseif (($o->type)==7) {
	 											$balance_stock = $balance_stock - ($o->packegeQty);
	 											$perticulars = "Packege MRN No: ".Common::fixBill($o->type_id)." Bill Date ".$o->date;
	 										}
	 										elseif (($o->type)==8) {
	 											$balance_stock = $balance_stock - ($o->offerQty);
	 											$perticulars = "Offer MRN No: ".Common::fixBill($o->type_id)." Bill Date ".$o->date;
	 										}
	 										elseif (($o->type)==9) {
	 											$balance_stock = $balance_stock + ($o->bilReturnQty);
	 											$perticulars = "Sale Return No: ".Common::fixBill($o->type_id)." Bill Date ".$o->date;
	 										}
	 										elseif (($o->type)==10) {
	 											$balance_stock = $balance_stock - ($o->expiryQty);
	 											$perticulars = "Stock Expiry Billing No: ".$o->type_id." Stock Date ".$o->date;
	 										}elseif (($o->type)==11) {
	 											$balance_stock = $balance_stock - ($o->billDeletedQty);
	 											$perticulars = "BillDeleted !  Billing No: ".Common::fixBill($o->type_id)." Bill Date ".$o->date;
	 										}
	 										elseif (($o->type)==18) {
	 											$balance_stock = $balance_stock + ($o->stockAdjustmentQty);
	 											$perticulars = "Stock Adjustment Id : ".$o->type_id." Date ".$o->date;
	 										}


	 										$sheet->appendRow(array(
	 											"#"  => $i,											
	 											trans('billing.bill_date') => $o->date,
	 											trans('glob.particulars') => $perticulars,
	 											trans('glob.purchase') => " ".Common::fixQty($purchase ),
	 											trans('glob.sale') => " ".Common::fixQty($sale),
	 											trans('glob.adjustment') => " ".Common::fixQty($adjustment),trans('glob.balance_stock') => " ".Common::fixQty($balance_stock),
	 										)
	 									);
	 									}
	 								}
	 							}

	 						});

})->export('xlsx');

return back();

}
}
catch(Exception $e){
	Log::error("StockLedgerController@exportTo ".$e);
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
			"perticulars" => trans('glob.perticulars'),
			"purchase"	=> trans('glob.purchase'),
			"sale"	=> trans('glob.sale'),
			"balance_stock" => trans('glob.balance_stock'),
			"barcode" => trans('glob.barcode'),
			"website" => trans('glob.website'), 
			"phone" => trans('glob.phone'),
			"adjustment"=>trans('glob.adjustment')

		)
	);
		return $labels;
	}
	catch(Exception $e){
		Log::error("StockLedgerController@getLabels ".$e);
		return View("errors.exception");
	}
}

public function getItemBySearchAll(Request $request){
	try{
		$searchKey = $request->all()['data'];
		$item = Item::with(["units"])
		->where('barcode', 'LIKE', '%'.$searchKey.'%')
		->orWhere('itemName', 'LIKE',  '%'.$searchKey.'%')
		->take(10)
		->get();

		$item = $this->applyOffers($item);

		return Response(array(
			'items' => $item,
		),200);
	}
	catch(Exception $e){
		Log::error("StockLedgerController@getItemBySearchAll ".$e);
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
		Log::error("StockLedgerController@mallDetails ".$e);
		return View("errors.exception");
	}
}


public function getLedgerQuery($item_id,$fromDate,$toDate){

	try{
		$purchases=DB::select("SELECT i.id,
			i.itemName FROM stocks s INNER JOIN items i ON (CASE WHEN s.type in (6,7,8,11)  THEN  i.id in 
			(select billing_items.item_id from billing_items where billing_items.id=s.item_id)	
			WHEN s.type =9  THEN  i.id in 
			(select bill_return_items.item_id from bill_return_items where bill_return_items.id=s.item_id)				
			ELSE CASE WHEN $item_id = 0 THEN s.item_id = i.id ELSE $item_id = i.id END END) 
			where CASE WHEN $item_id > 0 THEN i.id=$item_id ELSE true END AND s.date BETWEEN '".$fromDate."' and '".$toDate."'  GROUP BY i.id");

		foreach ($purchases as $row) {	

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
				billing_items.item_id=".$row->id." and 
				billing_items.id = stocks.item_id
				) WHEN stocks.type = 9 THEN  stocks.item_id IN(
				SELECT
				bill_return_items.id
				FROM
				bill_return_items
				WHERE
				bill_return_items.item_id= ".$row->id."  and
				bill_return_items.id = stocks.item_id
				) ELSE  stocks.item_id = ".$row->id." 
				END
				)
				AND stocks.date < '".$fromDate."' ORDER BY stocks.id DESC LIMIT 1");

			if(sizeof($balanceStock)>0){
				$row->balanceStock = $balanceStock[0]->balanceStock;
			}else{
				$row->balanceStock = 0;
			}



			$p=DB::select("					
				SELECT s.id,s.item_id,s.type,s.type_id,s.stock,DATE_FORMAT(date,'".$this->rDateFormatSQL."') date,					
				(CASE WHEN type = 2 THEN (SELECT purchases.supplierBillNo from purchases where purchases.id = s.type_id) WHEN type = 3 THEN (SELECT purchase_returns.supplier_billno from purchase_returns where purchase_returns.id = s.type_id) ELSE 0 END) as billno ,
				o.openingStock,p.purchasedQty,pr.purchaseReturnQty,i_s.issueStockQty,d_s.damageStockQty,b.billingQty,p_k.packegeQty,xy.offerQty,b_r.bilReturnQty,e_s.expiryQty,b_d.billDeletedQty,s_a.stockAdjustmentQty
				FROM stocks s    
				LEFT OUTER JOIN
				(   SELECT o.id,IFNULL(o.openingStock,0) as openingStock
				FROM opening_stock o       
				) o ON s.type_id = o.id and s.type=1 
				LEFT OUTER JOIN
				(   SELECT purchased_items.purchase_id,IFNULL(purchased_items.qty,0) as purchasedQty , purchased_items.item_id 
				FROM purchased_items       
				) p ON s.type_id = p.purchase_id and s.type=2 and s.item_id=p.item_id
				LEFT OUTER JOIN
				(   SELECT purchase_return_items.purchase_return_id,IFNULL(purchase_return_items.qty,0) as purchaseReturnQty , purchase_return_items.item_id
				FROM purchase_return_items       
				) pr ON s.type_id = pr.purchase_return_id and s.type=3 and s.item_id=pr.item_id
				LEFT OUTER JOIN
				(   SELECT issue_stock_items.issue_stocks_id,IFNULL(issue_stock_items.quantity,0) as issueStockQty,issue_stock_items.item_id
				FROM issue_stock_items       
				) i_s ON s.type_id = i_s.issue_stocks_id and s.type=4 and s.item_id=i_s.item_id
				LEFT OUTER JOIN
				(   SELECT damage_stock_items.damage_order_id,IFNULL(damage_stock_items.quantity,0) as damageStockQty,damage_stock_items.item_id
				FROM damage_stock_items       
				) d_s ON s.type_id = d_s.damage_order_id and s.type=5 and s.item_id=d_s.item_id
				LEFT OUTER JOIN
				(   SELECT billing_items.id,billing_items.billing_id,IFNULL(billing_items.qty,0) as billingQty
				FROM billing_items where billing_items.offer_type=0      
				) b ON s.type_id = b.billing_id and s.item_id=b.id and s.type=6
				LEFT OUTER JOIN
				(   SELECT billing_items.id,billing_items.billing_id,IFNULL(billing_items.qty,0) as packegeQty
				FROM billing_items  where billing_items.offer_type=1     
				) p_k ON s.type_id = p_k.billing_id and s.item_id=p_k.id and s.type=7 
				LEFT OUTER JOIN
				(   SELECT billing_items.id,billing_items.billing_id,IFNULL(billing_items.qty,0) as offerQty
				FROM billing_items WHERE billing_items.offer_type=2      
				) xy ON s.type_id = xy.billing_id and s.item_id=xy.id and s.type=8 
				LEFT OUTER JOIN
				(   SELECT bill_return_items.id,bill_return_items.return_id,IFNULL(bill_return_items.qty,0) as bilReturnQty
				FROM bill_return_items       
				) b_r ON s.type_id = b_r.return_id and s.item_id=b_r.id and s.type=9 
				LEFT OUTER JOIN
				(   SELECT expiry_items.expiry_order_id,IFNULL(expiry_items.quantity,0) as expiryQty
				FROM expiry_items       
				) e_s ON s.type_id = e_s.expiry_order_id and s.type=10  
				LEFT OUTER JOIN
				(SELECT billing_items.id,billing_items.billing_id,IFNULL(billing_items.qty,0) as billDeletedQty
				FROM billing_items where billing_items.offer_type=0      
				) b_d ON s.type_id = b_d.billing_id and s.item_id=b_d.id and s.type=11
				LEFT OUTER JOIN
				( SELECT stock_adjustment_items.stock_adjustment_id, stock_adjustment_items.adjust_through, 
				(CASE WHEN adjust_through = 1 THEN IFNULL(stock_adjustment_items.quantity,0) ELSE -(IFNULL(stock_adjustment_items.quantity,0)) END)
				as stockAdjustmentQty,
				stock_adjustment_items.item_id
				FROM stock_adjustment_items       
				) s_a ON s.type_id = s_a.stock_adjustment_id and s.type=18 and s.item_id=s_a.item_id
				where (CASE WHEN s.type in (6,7,8,11)  THEN  ".$row->id." in (select billing_items.item_id from billing_items where billing_items.id=s.item_id)	
				WHEN s.type =9  THEN  ".$row->id." in (select bill_return_items.item_id from bill_return_items where bill_return_items.id=s.item_id)				
				ELSE s.item_id = ".$row->id." END) and s.date BETWEEN '".$fromDate."' and '".$toDate."'  GROUP BY s.id,s.type_id,s.type ORDER BY s.id asc");

if(sizeof($p)>0){
	foreach($p as $prow){

		$balanceStock=DB::select("SELECT 
			IFNULL(stocks.stock, 0) as balanceStock
			FROM
			stocks
			WHERE    
			(
			CASE WHEN stocks.type IN(6, 7, 8,11) THEN  stocks.item_id IN(
			SELECT
			billing_items.id
			FROM
			billing_items
			WHERE
			billing_items.item_id=".$row->id." and 
			billing_items.id = stocks.item_id
			) WHEN stocks.type = 9 THEN  stocks.item_id IN(
			SELECT
			bill_return_items.id
			FROM
			bill_return_items
			WHERE
			bill_return_items.item_id= ".$row->id."  and
			bill_return_items.id = stocks.item_id
			) ELSE  stocks.item_id = ".$row->id." 
			END
			)
			AND stocks.date <= '".$toDate."' ORDER BY stocks.id DESC LIMIT 1");

		if(sizeof($balanceStock)>0){
			$prow->balanceStock = $balanceStock[0]->balanceStock;
		}else{
			$prow->balanceStock = 0;
		}

	}					
}

$row->child = $p;				

}

return $purchases;
}
catch(Exception $e){
	Log::error("StockLedgerController@getLedgerQuery ".$e);
	return View("errors.exception");
}

}

}
