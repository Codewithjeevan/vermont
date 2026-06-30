<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Item;
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

class PurchaseReturnReportController extends Controller
{
	private $optioncurr;
	private $rDateFormat;
	private $rDateFormatSQL;
	private $intVatType;
	public function __construct()
	{ 
		$this->optioncurr= Common::curr(); 
		$this->rDateFormat = Common::rDateFormat();
		$this->rDateFormatSQL = Common::rDateFormatSQL($this->rDateFormat);
		$this->intVatType = common::vatType();
	}
	/**
	 * Display a listing of the resource.
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function index(Request $request){
		try{
			$suppliers = array();
			$suppliers1 = Supplier::all();	
			$suppliers[] = (object) array('id'=>'0','name' => 'All');
			foreach ($suppliers1 as $row) {
				$suppliers[]=$row;
			}
			$items = array();
			$items[] = (object) array('id'=>'0','itemName' => 'All', 'barcode' =>'All');
			$date = Carbon::now()->format('m/d/Y');
			$date1 = Carbon::now()->format('Y-m-d');
			$mallDetails = $this->mallDetails();
			$res = array(
				"suppliers" =>$suppliers,
				"items" => $items,
				"date" => $date,
				"date1"	=> $date1,
				"mallDetails" => $mallDetails,
				);
			if($request->ajax()){
				return Response($res,200);
			}else{
				return View('pages.reports.purchase_return_report.purchase_return_report', $res);
			}

		}catch(Exception $e){
			Log::error("PurchaseReturnReportController@index ".$e);
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
			$item_id = $dateArray['item_id'];
			$supplier_id = $dateArray['supplier_id'];
			$mrn_no = $dateArray['mrn_no'];
			$total=0;
			$totalCostPrise=0;
			$totalQty=0;
			$totalDisAmt=0;
			$totalSubTotal=0;
			$itemList = array();
			$data = $this->getPurchasereturnQuery($mrn_no,$supplier_id,$item_id,$fromDate,$toDate);
			$suppliers = array();
			$suppliers1 = Supplier::all();	
			$suppliers[] = (object) array('id'=>'0','name' => 'All');
			foreach ($suppliers1 as $row) {
				$suppliers[]=$row;
			}
			$items = 			array();
			$items[] = 			(object) array('id'=>'0','itemName' => 'All', 'barcode' =>'All');
			$labels = 			$this->getLabels();
			$total= 			$data['total'];
			$totalCostPrise= 	$data['totalCostPrise'];
			$totalQty= 			$data['totalQty'];
			$totalDisAmt= 		$data['totalDisAmt'];
			$totalSubTotal= 	$data['totalSubTotal'];
			$purchases = 		$data['purchases'];
			$res = array(
				"items"  => 			$items,			
				"purchases" => 			$purchases,
				"suppliers" =>			$suppliers,
				"total"	=> 				Common::fixIt($total),
				"labels" => 			$labels,
				"totalCostPrise" => 	Common::fixIt($totalCostPrise),
				"totalQty" => 			Common::fixIt($totalQty),			
				"totalDisAmt" => 		Common::fixIt($totalDisAmt),	
				"totalSubTotal" => 		Common::fixIt($totalSubTotal),
				"toDatePrint" => 		(new Carbon($toDate))->format($this->rDateFormat),
				"fromDatePrint" => 		(new Carbon($fromDate))->format($this->rDateFormat),			
				);
			if($request->ajax()){
				return Response($res,200);
			}else{
				return View("pages.reports.purchase_return_report.purchase_return_report",$res);
			}

		}catch(Exception $e){
			Log::error("PurchaseReturnReportController@getLedger ".$e);
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
			$supplier_id = $dateArray['Supplier'];
			$item_id = $dateArray['Item'];
			$mrn_no = $dateArray['mrn_no'];
			$total=0;
			$totalCostPrise=0;
			$totalQty=0;
			$totalDisAmt=0;
			$totalSubTotal=0;
			$itemList = array();
			$currency=$this->optioncurr;
			$data = $this->getPurchasereturnQuery($mrn_no,$supplier_id,$item_id,$fromDate,$toDate);
			$fromDate = date($this->rDateFormat, strtotime($fromDate));
			$toDate = date($this->rDateFormat, strtotime($toDate));
			$total= $data['total'];
			$totalCostPrise= $data['totalCostPrise'];
			$totalQty= $data['totalQty'];
			$totalDisAmt= $data['totalDisAmt'];
			$totalSubTotal= $data['totalSubTotal'];
			$purchases = $data['purchases'];
			if(Input::get('pdf')){	
				$pdf = PDF::loadView('pages.reports.purchase_return_report.purchase_return_report_pdf', [
					"purchases" => 			$purchases ,
					"total"	=> 				Common::fixIt($total),
					"totalCostPrise" => 	Common::fixIt($totalCostPrise),
					"totalQty" =>			Common::fixIt($totalQty),
					"totalDisAmt" => 		Common::fixIt($totalDisAmt),	
					"totalSubTotal" => 		Common::fixIt($totalSubTotal),
					"fromDate" => 			$fromDate,
					"toDate" => 			$toDate,
					"i" =>					'1',
					"curr"=>				$currency,
					'intVatType' 	=> $this->intVatType,
					]); 
				$pdf->setPaper('a4');
				$carbon = Carbon::today();
				$timestamp = $carbon->timestamp;
				$format = $carbon->format($this->rDateFormat);
				$exportName = 'PurchaseReport-'.$format.'.pdf';     
				return $pdf->download($exportName);
			}elseif (Input::get('xls')) {
				$carbon = Carbon::today();
				$timestamp = $carbon->timestamp;
				$format = $carbon->format($this->rDateFormat);
				$exportName = 'PurchaseReport-'.$format; 
				\App\Legacy\LegacyExcel::create($exportName, function ($excel) 
					use($purchases,
						$total,
						$totalCostPrise,
						$totalQty,
						$fromDate,
						$toDate,
						$totalDisAmt,
						$totalSubTotal,
						$currency) {
						$excel->sheet('PurchaseReport', function ($sheet) 
							use($purchases,
								$total,
								$totalCostPrise,
								$totalQty,
								$fromDate,
								$toDate,
								$totalDisAmt,
								$totalSubTotal,
								$currency) {	
								$sheet->appendRow(array('', ''));
								$sheet->appendRow(array(trans('glob.from'), $fromDate ,trans('glob.to'),$toDate ));
								$sheet->row($sheet->getHighestRow(), function ($row) {
									$row->setFontWeight('bold');
								});	
								$sheet->appendRow(array('', ''));
								/* setting column names for data - you can of course set it manually */

								if($this->intVatType !=0){
									$sheet->appendRow(array(
										"#" ,	
										trans('glob.sale_return_no') ,	
										trans('stock.stock_transfer_no') ,
										trans('stock.return_date') ,
										trans('glob.supervisor') ,
										trans('item.item_name') ,		
										trans('glob.cost_price') ,	
										trans('item.cp_vat_percent') ,
										trans('item.qty') ,		
										)); /* column names */
								}else{
									$sheet->appendRow(array(
										"#" ,	
										trans('glob.sale_return_no') ,	
										trans('stock.stock_transfer_no') ,
										trans('stock.return_date') ,
										trans('glob.supervisor') ,
										trans('item.item_name') ,		
										trans('glob.cost_price') ,	
										trans('item.qty') ,		
										)); /* column names */
								}

								/* getting last row number (the one we already filled and setting it to bold */
								$sheet->row($sheet->getHighestRow(), function ($row) {
									$row->setFontWeight('bold');
								});

								if($this->intVatType !=0){

									foreach ($purchases as $p) {						
										$sheet->appendRow( array(
											"#"  => $p->name,	
											trans('glob.sale_return_no') => '',	
											trans('stock.stock_transfer_no') => '',
											trans('stock.return_date') => '',
											trans('glob.supervisor') => '',
											trans('item.item_name') => '',		
											trans('glob.cost_price') => '',	
											trans('item.cp_vat_percent') => '',
											trans('item.qty') =>'',		
											)
										);
										$i=0;
										$amt =0;					
										foreach ($p->child as $o) {						
											$i++;
											$amt = (($o->qty * $o->costPrise) - $o->dis_amt) + $amt;				
											$sheet->appendRow(array(
												"#"  => $i,	
												trans('glob.sale_return_no') => $o->id,	
												trans('stock.stock_transfer_no') => $o->supplierBillNo,
												trans('glob.purchase_date') => $o->supplier_billdate,
												trans('glob.supervisor') => $o->return_through,
												trans('item.item_name') => $o->itemName,
												trans('glob.cost_price')."(".$currency.")" => " ".Common::fixIt($o->costPrise),
												trans('item.cp_vat_percent')=> " ".Common::fixIt($o->cp_vat),
												trans('item.qty') => " ".Common::fixQty($o->qty),	
												)
											);
										}
										$sheet->appendRow( array(
											"#"  => '',	
											trans('glob.sale_return_no') => '',	
											trans('stock.stock_transfer_no') => '',
											trans('glob.purchase_date') => '',
											trans('glob.supervisor') => '',
											trans('item.item_name') => '',	
											trans('glob.rate')."(".$currency.")" =>'',
											trans('item.cp_vat_percent')=>'',
											trans('item.qty') =>trans('item.qty')."=".Common::fixQty($p->qty),							
											)
										);
									}

								}else{

									foreach ($purchases as $p) {						
										$sheet->appendRow( array(
											"#"  => $p->name,	
											trans('glob.sale_return_no') => '',	
											trans('stock.stock_transfer_no') => '',
											trans('stock.return_date') => '',
											trans('glob.supervisor') => '',
											trans('item.item_name') => '',		
											trans('glob.cost_price') => '',	
											trans('item.qty') =>'',		
											)
										);
										$i=0;
										$amt =0;					
										foreach ($p->child as $o) {						
											$i++;
											$amt = (($o->qty * $o->costPrise) - $o->dis_amt) + $amt;				
											$sheet->appendRow(array(
												"#"  => $i,	
												trans('glob.sale_return_no') => $o->id,	
												trans('stock.stock_transfer_no') => $o->supplierBillNo,
												trans('glob.purchase_date') => $o->supplier_billdate,
												trans('glob.supervisor') => $o->return_through,
												trans('item.item_name') => $o->itemName,
												trans('glob.cost_price')."(".$currency.")" => " ".Common::fixIt($o->costPrise),
												trans('item.qty') => " ".Common::fixQty($o->qty),	
												)
											);
										}
										$sheet->appendRow( array(
											"#"  => '',	
											trans('glob.sale_return_no') => '',	
											trans('stock.stock_transfer_no') => '',
											trans('glob.purchase_date') => '',
											trans('glob.supervisor') => '',
											trans('item.item_name') => '',	
											trans('glob.rate')."(".$currency.")" =>'',
											trans('item.qty') =>trans('item.qty')."=".Common::fixQty($p->qty),							
											)
										);
									}

								}


								if($this->intVatType !=0){

									$sheet->appendRow( array(
										"#"  => '',	
										trans('glob.sale_return_no') => '',	
										trans('stock.stock_transfer_no') => '',
										trans('glob.purchase_date') => '',
										trans('glob.supervisor') => '',
										trans('item.item_name') => '',		
										trans('glob.rate')."(".$currency.")" => trans('glob.total'),	
										trans('item.cp_vat_percent')=>'',
										trans('item.qty') =>" ".Common::fixQty($totalQty),		
										)
									);

								}else{

									$sheet->appendRow( array(
										"#"  => '',	
										trans('glob.sale_return_no') => '',	
										trans('stock.stock_transfer_no') => '',
										trans('glob.purchase_date') => '',
										trans('glob.supervisor') => '',
										trans('item.item_name') => '',		
										trans('glob.rate')."(".$currency.")" => trans('glob.total'),
										trans('item.qty') =>" ".Common::fixQty($totalQty),		
										)
									);

								}

								$sheet->row($sheet->getHighestRow(), function ($row) {
									$row->setFontWeight('bold');
								});
							});
})->export('xlsx');
return back();
}

}catch(Exception $e){
	Log::error("PurchaseReturnReportController@exportTo ".$e);
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
				"supplier" => trans('supplier.supplier'),
				"mrn_no" => trans('stock.mrn_no'),
				"s_bill_date" => trans('stock.s_bill_date'),
				"s_bill_no" => trans('stock.s_bill_no'),
				"barcode" => trans('glob.barcode'),
				"website" => trans('glob.website'), 
				"phone" => trans('glob.phone'),
				"dis_amt" => trans('billing.dis_amt'),
				"sub_total" => trans('billing.sub_total'),
				"sale_return_no" => trans('glob.sale_return_no'),
				"purchase_bill_no" => trans('glob.purchase_bill_no'),
				"purchase_date" => trans('glob.purchase_date'),
				"supervisor" => trans('glob.supervisor'),
				"cost_price" => trans('glob.cost_price'),
				"cp_vat"=>trans('item.cp_vat_percent'),
				"return_date"=>trans('stock.return_date'),
				"stock_transfer_no" => trans('stock.stock_transfer_no'),
				)
			);
			return $labels;

		}catch(Exception $e){
			Log::error("PurchaseReturnReportController@getLabels ".$e);
			return View("errors.exception");
		}
	}

	/**
	 * created for get Shap Details for print and Export functinality
	 * @return [type] [description]
	 */
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
			Log::error("PurchaseReturnReportController@mallDetails ".$e);
			return View("errors.exception");
		}
	}
	/**
	 * Created for search item by barcode and item name
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function getItemBySearchAll(Request $request){
		try{
			$data = $request->all()['data'];
			$searchKey = $data['search_item'];
			$mrn_no = $data['mrn_no']; 
			$supplier_id = [];
			if($mrn_no == null || $mrn_no == ''){
				$items = Item::with(["units",'categories'])
				->join('categories', 'items.category', '=', 'categories.id')
				->select('items.*', 'categories.categorie')    
				->orWhere('barcode', 'LIKE', '%'.$searchKey.'%')
				->orWhere('itemName', 'LIKE',  '%'.$searchKey.'%')
				->orderBy('id', 'asc')    
				->get();
			}else{
				$items = DB::select(DB::raw("select i.* from purchase_return_items as pri
					inner join items i on i.id = pri.item_id
					inner join purchase_returns pr on pr.id = pri.purchase_return_id
					inner join purchases p on p.supplierBillNo = pr.supplier_billno and p.supplier_id = pr.supplier_id
					WHERE
					(case when $mrn_no = null or $mrn_no ='' then 1 =1 else p.id= $mrn_no end)
					and 
					((case when '".$searchKey."' ='' then 1=1 else  i.itemName like '%".$searchKey."%' end) || (case when '".$searchKey."' ='' then 1=1 else  i.barcode like '%".$searchKey."%' end))
					"));
				if($mrn_no != null){
					$supplier_id = DB::select("select p.supplier_id from purchases p where p.id= $mrn_no");
				}
			}
			return Response(array(
				'items' => $items,
				'supplier_id' => (isset($supplier_id[0]->supplier_id)) ? $supplier_id[0]->supplier_id : ''
				),200);

		}catch(Exception $e){
			Log::error("PurchaseReturnReportController@getItemBySearchAll ".$e);
			return View("errors.exception");
		}
	}


	public function getPurchasereturnQuery($mrn_no,$supplier_id,$item_id,$fromDate,$toDate){
		try{
			$mrn_no = $mrn_no ?? 0;
			$item_id = $item_id ?? 0;
			$supplier_id = $supplier_id ?? 0;
			$total=0;
			$totalCostPrise=0;
			$totalQty=0;
			$totalDisAmt=0;
			$totalSubTotal=0;
			$purchases=DB::select("SELECT suppliers.id,
				suppliers.name ,
				sum(pri.qty) as qty,
				avg(pri.costPrise) as costPrise,
				pri.cp_vat,
				sum(pri.qty * pri.costPrise) as amt , 
				sum(pri.totalDiscount * pri.qty) as dis_amt 
				FROM purchase_returns pr INNER JOIN purchase_return_items pri ON pr.id = pri.purchase_return_id
				INNER JOIN purchases p on p.supplierBillNo = pr.supplier_billno 
				INNER JOIN suppliers on pr.supplier_id = suppliers.id 
				WHERE CASE WHEN $supplier_id > 0 THEN pr.supplier_id = $supplier_id ELSE true END and
				CASE WHEN  $mrn_no > 0 THEN p.id = ".$mrn_no." ELSE true END and
				CASE WHEN $item_id > 0 THEN pri.item_id = $item_id ELSE true END and 
				pr.return_date BETWEEN '".$fromDate."' and '".$toDate."' GROUP BY suppliers.id");
			foreach ($purchases as $i => $row) {	
				$p=DB::select("SELECT pr.id,
					DATE_FORMAT(pr.return_date,'".$this->rDateFormatSQL."') supplier_billdate,
					pr.supplier_billno as supplierBillNo,
					pri.qty,pri.costPrise,
					pri.cp_vat,
					items.barcode as itemCode ,
					items.serial_number as serial_number ,
					items.itemName as itemName , 
					(pri.totalDiscount * pri.qty) as dis_amt ,
					pr.return_through 
					FROM purchase_return_items pri 
					INNER JOIN purchase_returns pr ON pri.purchase_return_id=pr.id 
					INNER JOIN purchases p ON pr.supplier_billno = p.supplierBillNo AND 
					pr.supplier_id = p.supplier_id INNER JOIN items ON pri.item_id = items.id
					WHERE CASE WHEN $item_id > 0 THEN  pri.item_id = $item_id ELSE true END and
					CASE WHEN $mrn_no > 0 THEN p.id = ".$mrn_no." ELSE true END and
					pr.supplier_id = ".$row->id." and 
					pr.return_date BETWEEN '".$fromDate."' and '".$toDate."' ORDER BY pr.id, 
					STR_TO_DATE(pr.return_date, '%y-%m-%d') ASC");
				if(sizeof($p) > 0){
					$row->child = $p;
					$totalCostPrise = $row->costPrise + $totalCostPrise;
					$totalQty= $row->qty + $totalQty;
					$totalDisAmt = ($row->dis_amt) + $totalDisAmt;
					$totalSubTotal = $row->amt + $totalSubTotal;
					$total = ($totalSubTotal - $totalDisAmt) + $total;
				}else{
					unset($purchases[$i]);
				}
			}
			return array(
				'purchases' => 			$purchases,
				'total' => 				$total,
				'totalCostPrise' => 	$totalCostPrise,
				'totalQty' => 			$totalQty,
				'totalDisAmt' => 		$totalDisAmt,
				'totalSubTotal' => 		$totalSubTotal,
				);

		}catch(Exception $e){
			Log::error("PurchaseReturnReportController@getPurchasereturnQuery ".$e);
			return View("errors.exception");
		}
	}
}
