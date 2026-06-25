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

class PurchaseReportController extends Controller
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
				return View('pages.reports.purchase_report.purchase_report', $res);
			}

		}catch(Exception $e){
			Log::error("PurchaseReportController@index ".$e);
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
			$item_id = $dateArray['item_id'];
			$supplier_id = $dateArray['supplier_id'];
			$total=0;
			$totalCostPrise=0;
			$totalQty=0;
			$totalDisAmt=0;
			$totalSubTotal=0;
			$itemList = array();
			$data = $this->getPurchaseReportQuery($fromDate,$toDate,$item_id,$supplier_id);
			$suppliers = array();
			$suppliers1 = Supplier::all();	
			$suppliers[] = (object) array('id'=>'0','name' => 'All');
			foreach ($suppliers1 as $row) {
				$suppliers[]=$row;
			}
			$items = array();
			$items[] = (object) array('id'=>'0','itemName' => 'All', 'barcode' =>'All');
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
				"totalDisAmt" =>		Common::fixIt($totalDisAmt),	
				"totalSubTotal" => 		Common::fixIt($totalSubTotal),
				"toDatePrint" => 		(new Carbon($toDate))->format($this->rDateFormat),
				"fromDatePrint" => 		(new Carbon($fromDate))->format($this->rDateFormat),			
			);
			if($request->ajax()){
				return Response($res,200);
			}else{
				return View("pages.reports.purchase_report.purchase_report",$res);
			}

		}catch(Exception $e){
			Log::error("PurchaseReportController@getLedger ".$e);
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
			$total=0;
			$totalCostPrise=0;
			$totalQty=0;
			$totalDisAmt=0;
			$totalSubTotal=0;
			$itemList = array();
			$currency = $this->optioncurr;
			$data = $this->getPurchaseReportQuery($fromDate,$toDate,$item_id,$supplier_id);
			$fromDate = date($this->rDateFormat, strtotime($fromDate));
			$toDate = date($this->rDateFormat, strtotime($toDate));
			$total = 			$data['total'];
			$totalCostPrise = 	$data['totalCostPrise'];
			$totalQty = 		$data['totalQty'];
			$totalDisAmt = 		$data['totalDisAmt'];
			$totalSubTotal = 	$data['totalSubTotal'];
			$purchases = 		$data['purchases'];
			if(Input::get('pdf')){	
				$pdf = PDF::loadView('pages.reports.purchase_report.purchase_report_pdf', [
					"purchases" => 			$purchases ,
					"total"	=> 				Common::fixIt($total),
					"totalCostPrise" => 	Common::fixIt($totalCostPrise),
					"totalQty" => 			Common::fixIt($totalQty),
					"totalDisAmt" => 		Common::fixIt($totalDisAmt),	
					"totalSubTotal" => 		Common::fixIt($totalSubTotal),
					"fromDate" => 			$fromDate,
					"toDate" => 			$toDate,
					"i" =>					'1',
					"curr"=>				$currency,
					"intVatType" 	=> $this->intVatType,
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
				Excel::create($exportName, function ($excel) 
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

								if($this->intVatType != 0){

									$sheet->appendRow(array(
										"#" ,	
										trans('stock.mrn_no') ,							
										trans('glob.barcode') ,
										trans('item.refrence_number') ,
										trans('item.item_name') ,							
										trans('stock.stock_transfer_no') ,
										trans('stock.stock_transfer_date') ,
										trans('glob.rate') ,	
										trans('item.cp_vat_percent'),
										trans('item.qty') ,		
										trans('billing.sub_total')."(".$currency.")" ,		
										trans('billing.dis_amt')."(".$currency.")" ,							
										trans('stock.amt')."(".$currency.")" ,			
									)); /* column names */

								}else{

									$sheet->appendRow(array(
										"#" ,	
										trans('stock.mrn_no') ,							
										trans('glob.barcode') ,
										trans('item.refrence_number') ,
										trans('item.item_name') ,							
										trans('stock.stock_transfer_no') ,
										trans('stock.stock_transfer_date') ,
										trans('glob.rate') ,
										trans('item.qty') ,		
										trans('billing.sub_total')."(".$currency.")" ,		
										trans('billing.dis_amt')."(".$currency.")" ,							
										trans('stock.amt')."(".$currency.")" ,			
									)); /* column names */

								}



								/* getting last row number (the one we already filled and setting it to bold */
									$sheet->row($sheet->getHighestRow(), function ($row) {
										$row->setFontWeight('bold');
									});
									foreach ($purchases as $p) {						

										$i=0;
										$amt =0;	

										if($this->intVatType ==0){

											$sheet->appendRow( array(
												"#"  => $p->name,	
												trans('stock.mrn_no') => '',							
												trans('glob.barcode') => '',
												trans('item.refrence_number') => '',
												trans('item.item_name') => '',							
												trans('stock.stock_transfer_no') => '',
												trans('stock.stock_transfer_date') => '',
												trans('glob.rate') => '',
												trans('item.qty') =>'',		
												trans('billing.sub_total')."(".$currency.")" =>'',		
												trans('billing.dis_amt')."(".$currency.")" =>'',							
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
												$amt = (($o->qty * $o->costPrise) - $o->dis_amt) + $amt;				
												$sheet->appendRow( array(
													"#"  => $i,	
													trans('stock.mrn_no') => $o->id,
													trans('glob.barcode') => $barcode,
													trans('item.refrence_number') => $serial_number,
													trans('item.item_name') => $o->itemName,
													trans('stock.stock_transfer_no') => $o->supplierBillNo,
													trans('stock.stock_transfer_date') => $o->supplier_billdate,
													trans('glob.rate') => " ".Common::fixIt($o->costPrise),
													trans('item.qty') => " ".Common::fixQty($o->qty),	
													trans('billing.sub_total')."(".$currency.")" => " ".Common::fixIt($o->costPrise * $o->qty),		
													trans('billing.dis_amt')."(".$currency.")" => " ".Common::fixIt($o->dis_amt),								
													trans('stock.amt')."(".$currency.")" => " ".Common::fixIt((($o->costPrise * $o->qty) - ($o->dis_amt))),			
												)
											);
											}

										}else if($this->intVatType ==1){

											$sheet->appendRow( array(
												"#"  => $p->name,	
												trans('stock.mrn_no') => '',							
												trans('glob.barcode') => '',
												trans('item.refrence_number') => '',
												trans('item.item_name') => '',							
												trans('stock.stock_transfer_no') => '',
												trans('stock.stock_transfer_date') => '',
												trans('glob.rate') => '',	
												trans('item.cp_vat_percent')=>'',
												trans('item.qty') =>'',		
												trans('billing.sub_total')."(".$currency.")" =>'',		
												trans('billing.dis_amt')."(".$currency.")" =>'',							
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
												$amt = (($o->qty * $o->costPrise) - $o->dis_amt) + $amt;				
												$sheet->appendRow( array(
													"#"  => $i,	
													trans('stock.mrn_no') => $o->id,
													trans('glob.barcode') => $barcode,
													trans('item.refrence_number') => $serial_number,
													trans('item.item_name') => $o->itemName,
													trans('stock.stock_transfer_no') => $o->supplierBillNo,
													trans('stock.stock_transfer_date') => $o->supplier_billdate,
													trans('glob.rate') => " ".Common::fixIt($o->costPrise),
													trans('item.cp_vat_percent')=>" ".Common::fixIt($o->cp_vat),
													trans('item.qty') => " ".Common::fixQty($o->qty),	
													trans('billing.sub_total')."(".$currency.")" => " ".Common::fixIt($o->costPrise * $o->qty),		
													trans('billing.dis_amt')."(".$currency.")" => " ".Common::fixIt($o->dis_amt),								
													trans('stock.amt')."(".$currency.")" => " ".Common::fixIt((($o->costPrise * $o->qty) - ($o->dis_amt))),			
												)
											);
											}

										}else if($this->intVatType ==2){

											$sheet->appendRow( array(
												"#"  => $p->name,	
												trans('stock.mrn_no') => '',							
												trans('glob.barcode') => '',
												trans('item.refrence_number') => '',
												trans('item.item_name') => '',							
												trans('stock.stock_transfer_no') => '',
												trans('stock.stock_transfer_date') => '',
												trans('glob.rate') => '',	
												trans('item.cp_vat_percent')=>'',
												trans('item.qty') =>'',		
												trans('billing.sub_total')."(".$currency.")" =>'',		
												trans('billing.dis_amt')."(".$currency.")" =>'',							
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
												$amt = (($o->qty * ($o->costPrise + ( ($o->costPrise * $o->cp_vat)/100) )) - $o->dis_amt) + $amt;				
												$sheet->appendRow( array(
													"#"  => $i,	
													trans('stock.mrn_no') => $o->id,
													trans('glob.barcode') => $barcode,
													trans('item.refrence_number') => $serial_number,
													trans('item.item_name') => $o->itemName,
													trans('stock.stock_transfer_no') => $o->supplierBillNo,
													trans('stock.stock_transfer_date') => $o->supplier_billdate,
													trans('glob.rate') => " ".Common::fixIt($o->costPrise),
													trans('item.cp_vat_percent')=>" ".Common::fixIt($o->cp_vat),
													trans('item.qty') => " ".Common::fixQty($o->qty),	
													trans('billing.sub_total')."(".$currency.")" => " ".Common::fixIt(($o->costPrise + ( ($o->costPrise * $o->cp_vat)/100) ) * $o->qty),		
													trans('billing.dis_amt')."(".$currency.")" => " ".Common::fixIt($o->dis_amt),								
													trans('stock.amt')."(".$currency.")" => " ".Common::fixIt(((($o->costPrise + ( ($o->costPrise * $o->cp_vat)/100) ) * $o->qty) - ($o->dis_amt))),			
												)
											);
											}

										}


										if($this->intVatType !=0){

											$sheet->appendRow(array(
												"#"  => '',	
												trans('stock.mrn_no') => '',							
												trans('glob.barcode') => '',
												trans('item.refrence_number') => '',
												trans('item.item_name') => '',	
												trans('stock.stock_transfer_no') => '',						
												trans('stock.stock_transfer_date') => '',
												trans('glob.rate') =>'',
												trans('item.cp_vat_percent')=>'',
												trans('item.qty') =>trans('item.qty')."=".Common::fixQty($p->qty),							
												trans('billing.sub_total')."(".$currency.")" =>trans('billing.sub_total')."=".Common::fixIt($p->amt),		
												trans('billing.dis_amt')."(".$currency.")" =>trans('billing.dis_amt')."=".Common::fixIt($p->dis_amt),								
												trans('stock.amt')."(".$currency.")" =>trans('stock.amt')."=". Common::fixIt($amt),			
											)
										);
										}else{

											$sheet->appendRow(array(
												"#"  => '',	
												trans('stock.mrn_no') => '',							
												trans('glob.barcode') => '',
												trans('item.refrence_number') => '',
												trans('item.item_name') => '',	
												trans('stock.stock_transfer_no') => '',						
												trans('stock.stock_transfer_date') => '',
												trans('glob.rate') =>'',
												trans('item.qty') =>trans('item.qty')."=".Common::fixQty($p->qty),							
												trans('billing.sub_total')."(".$currency.")" =>trans('billing.sub_total')."=".Common::fixIt($p->amt),		
												trans('billing.dis_amt')."(".$currency.")" =>trans('billing.dis_amt')."=".Common::fixIt($p->dis_amt),								
												trans('stock.amt')."(".$currency.")" =>trans('stock.amt')."=". Common::fixIt($amt),			
											)
										);

										}
									}

									if($this->intVatType !=0){
										$sheet->appendRow( array(
											"#"  => '',	
											trans('stock.mrn_no') => '',							
											trans('glob.barcode') => '',
											trans('item.refrence_number') => '',
											trans('item.item_name') => '',
											trans('stock.stock_transfer_no') => '',							
											trans('stock.stock_transfer_date') => '',
											trans('glob.rate') =>'',
											trans('glob.cp_vat') => trans('glob.total'),	
											trans('item.qty') =>" ".Common::fixQty($totalQty),		
											trans('billing.sub_total')."(".$currency.")" => " ".Common::fixIt($totalSubTotal),		
											trans('billing.dis_amt')."(".$currency.")" => " ".Common::fixIt($totalDisAmt),				
											trans('stock.amt')."(".$currency.")" =>  " ".Common::fixIt(($totalSubTotal - $totalDisAmt)),			
										)
									);
									}else{

										$sheet->appendRow( array(
											"#"  => '',	
											trans('stock.mrn_no') => '',							
											trans('glob.barcode') => '',
											trans('item.refrence_number') => '',
											trans('item.item_name') => '',
											trans('stock.stock_transfer_no') => '',							
											trans('stock.stock_transfer_date') => '',
											trans('glob.rate') =>'',	
											trans('item.qty') =>" ".Common::fixQty($totalQty),		
											trans('billing.sub_total')."(".$currency.")" => " ".Common::fixIt($totalSubTotal),		
											trans('billing.dis_amt')."(".$currency.")" => " ".Common::fixIt($totalDisAmt),				
											trans('stock.amt')."(".$currency.")" =>  " ".Common::fixIt(($totalSubTotal - $totalDisAmt)),			
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
	Log::error("PurchaseReportController@getLedger ".$e);
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
				"cp_vat" => trans('item.cp_vat_percent'),
				"refrence_number"=>trans('item.refrence_number'),
				'stock_transfer_no'=>  trans('stock.stock_transfer_no'),
				'stock_transfer_date'=>trans('stock.stock_transfer_date'),
			)
		);
			return $labels;

		}catch(Exception $e){
			Log::error("PurchaseReportController@getLabels ".$e);
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
			Log::error("PurchaseReportController@mallDetails ".$e);
			return View("errors.exception");
		}
	}

	public function getPurchaseReportQuery($fromDate,$toDate,$item_id,$supplier_id){
		try{
			$item_id = $item_id ?? 0;
			$supplier_id = $supplier_id ?? 0;
			$totalCostPrise = 0;
			$totalQty= 0;
			$totalDisAmt = 0;
			$totalSubTotal = 0;
			$total = 0;
			$purchases=DB::select("SELECT suppliers.id,suppliers.name ,
				sum(purchased_items.qty) as qty,avg(purchased_items.costPrise) as costPrise,

				CASE
				WHEN $this->intVatType = 2 THEN
				sum(purchased_items.qty * ( purchased_items.costPrise + ( (purchased_items.costPrise * purchased_items.cp_vat )/100 ) ))
				WHEN $this->intVatType <> 2 THEN 
				sum(purchased_items.qty * purchased_items.costPrise)
				END  as amt ,

				sum((purchased_items.discount * purchased_items.amount ) / 100) as dis_amt,
				purchased_items.cp_vat 
				FROM purchases INNER JOIN purchased_items ON purchases.id = purchased_items.purchase_id 
				INNER JOIN suppliers on purchases.supplier_id = suppliers.id 
				WHERE CASE WHEN $supplier_id > 0 THEN purchases.supplier_id = $supplier_id ELSE true END and 
				CASE WHEN  $item_id > 0 THEN purchased_items.item_id = $item_id ELSE true END and 
				purchases.supplier_billdate BETWEEN '".$fromDate."' and '".$toDate."' GROUP BY purchases.supplier_id");

			foreach ($purchases as $row) {		 	
				$p=DB::select("SELECT purchases.id,
					DATE_FORMAT(purchases.supplier_billdate,'".$this->rDateFormatSQL."') supplier_billdate,
					purchases.supplierBillNo,
					purchased_items.qty,
					purchased_items.costPrise,
					items.barcode as itemCode ,
					items.serial_number as serial_number ,
					items.itemName as itemName , 
					((purchased_items.discount * purchased_items.amount ) / 100) as dis_amt,
					purchased_items.cp_vat 
					FROM purchases INNER JOIN purchased_items ON purchases.id = purchased_items.purchase_id
					INNER JOIN items on purchased_items.item_id=items.id
					WHERE CASE WHEN $item_id > 0 THEN purchased_items.item_id = $item_id ELSE true END and
					purchases.supplier_id = ".$row->id." and
					purchases.supplier_billdate BETWEEN '".$fromDate."' and '".$toDate."' ORDER BY purchases.id, STR_TO_DATE(purchases.supplier_billdate, '%y-%m-%d') ASC");
				$row->child = $p;
				$totalCostPrise = $row->costPrise + $totalCostPrise;
				$totalQty= $row->qty + $totalQty;
				$totalDisAmt = ($row->dis_amt) + $totalDisAmt;
				$totalSubTotal = $row->amt + $totalSubTotal;
				$total = ($totalSubTotal - $totalDisAmt) + $total;
			}

			return  array(
				'purchases' => 			$purchases,
				'totalCostPrise' => 	$totalCostPrise,
				'totalQty'=> 			$totalQty,
				'totalDisAmt'=> 		$totalDisAmt,
				'totalSubTotal'=>		$totalSubTotal,
				'total'=>				$total
			);

		}catch(Exception $e){
			Log::error("PurchaseReportController@getPurchaseReportQuery ".$e);
			return View("errors.exception");
		}
	}
}
