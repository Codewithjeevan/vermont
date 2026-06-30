<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Item;
use App\Category;
use App\Unit;
use App\Supplier;
use App\Purchase;
use App\Salesman;
use App\Customer;
use PDF;
use Excel;
use Button;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use DB;
use Common;

use Exception;
use Log;


class SalesReportController extends Controller
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
		$this->intVatType = Common::vatType();

	}
	/**
	 * Display a listing of the resource. used for Customer_Sales_Report
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function customer(Request $request){

		
		$customers = array();
		$customers1 = Customer::all();	
		$customers[] = (object) array('id'=>'0','name' => 'All');
		foreach ($customers1 as $row) {
			$customers[]=$row;
		}		
		$date = Carbon::now()->format('m/d/Y');
		$date1 = Carbon::now()->format('m/d/Y');
		$mallDetails = $this->mallDetails();
		$res = array(
			"customers" =>$customers,
			"date" => $date,
			"date1" => $date1,
			"mallDetails" => $mallDetails,
		);

		if($request->ajax()){
			return Response($res,200);
		}else{
			return View('pages.sales_reports.customer_sales_report.customer_sales_report', $res);
		}
	}

	/**
	 * Select data which is displayed on report view table . used for Customer_Sales_Report
	 * @param  REquest $request [description]
	 * @return [type]           [description]
	 */
	public function getLedger(Request $request){
		try{		
			$dateArray = $request->all()["data"]; 
			$fromDate = new Carbon($dateArray['fromDate']);
			$toDate = new Carbon($dateArray['toDate']);		
			$customer_id = $dateArray['customer_id'];
			$total=0;
			$totalDis=0;
			$totalRate=0;
			$totalQty=0;
			$itemList = [];
			$discountPercent=0;
			$totalLoyDis=0;
			$data = $this->getCustomerSalesQuery($fromDate,$toDate,$customer_id);
			$customers = array();
			$customers1 = Customer::all();	
			$customers[] = (object) array('id'=>'0','name' => 'All');
			foreach ($customers1 as $row) {
				$customers[]=$row;
			}	
			$labels = 			$this->getLabels();
			$total =  			$data['total'];
			$totalDis =  		$data['totalDis'];
			$totalRate =  		$data['totalRate'];
			$totalQty =  		$data['totalQty'];			
			$purchases = 		$data['purchases'];
			$res = array(
				"customers"  => 		$customers,			
				"purchases" => 			$purchases,	
				"total" =>				$total,
				"itemList"	=> 			$itemList,
				"totalDis" => 			$totalDis,
				"totalLoyDis"=>			$totalLoyDis,
				"totalQty" => 			$totalQty,
				"totalRate" => 			$totalRate,
				"labels" => 			$labels,
				"toDatePrint" => 		(new Carbon($toDate))->format($this->rDateFormat),
				"fromDatePrint" => 		(new Carbon($fromDate))->format($this->rDateFormat),			
			);
			if($request->ajax()){
				return Response($res,200);
			}else{
				return View("pages.sales_reports.customer_sales_report.customer_sales_report",$res);
			}
		}
		catch(Exception $e){
			Log::error("SalesReportController@getLedger ".$e);
			return View("errors.exception");

		}
	}

	/**
	 * Function created for create and export exel and pdf file . used for Customer_Sales_Report
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function exportTo(Request $request){
		try{				
			$dateArray = $request->all(); 
			$fromDate = new Carbon($dateArray['from']);
			$toDate = new Carbon($dateArray['to']);
			$customer_id = $dateArray['customer'];
			$total=0;
			$totalDis=0;
			$totalRate=0;
			$totalQty=0;
			$itemList = [];
			$discountPercent=0;
			$totalLoyDis=0;
			$currency=$this->optioncurr;
			$data = $this->getCustomerSalesQuery($fromDate,$toDate,$customer_id);
			$fromDate = date($this->rDateFormat , strtotime($fromDate));
			$toDate = date($this->rDateFormat, strtotime($toDate));
			$total =  			$data['total'];
			$totalDis =  		$data['totalDis'];
			$totalRate =  		$data['totalRate'];
			$totalQty =  		$data['totalQty'];			
			$purchases = 		$data['purchases'];
			if(Input::get('pdf')){	
				$pdf = PDF::loadView('pages.sales_reports.customer_sales_report.customer_sales_report_pdf', 
					["purchases" => 		$purchases ,
					"itemList"	=> 			$itemList,
					"totalDis" => 			$totalDis,
					"totalLoyDis"=>			$totalLoyDis,
					"total"=> 				$total,
					"totalRate" => 			$totalRate,
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
				$exportName = 'CustomerSales-'.$format.'.pdf';     
				return $pdf->download($exportName);
			}elseif (Input::get('xls')) {
				$carbon = Carbon::today();
				$timestamp = $carbon->timestamp;
				$format = $carbon->format($this->rDateFormat);
				$exportName = 'CustomerSales-'.$format; 
				Excel::create($exportName, function ($excel) 
					use($purchases,
						$total,
						$totalDis,
						$totalLoyDis,
						$totalQty,
						$totalRate,
						$fromDate,
						$toDate,
						$currency) {
						$excel->sheet('CustomerSales', function ($sheet) 
							use($purchases,
								$total,
								$totalDis,
								$totalLoyDis,
								$totalQty,
								$totalRate,
								$fromDate,
								$toDate,
								$currency) {
								$sheet->appendRow(array('', ''));						
								$sheet->appendRow(array(trans('glob.from'), $fromDate ,trans('glob.to'),$toDate));
								$sheet->row($sheet->getHighestRow(), function ($row) {
									$row->setFontWeight('bold');
								});	
								$sheet->appendRow(array('', ''));
								/* setting column names for data - you can of course set it manually */
								if($this->intVatType!=0){
									$sheet->appendRow(array(
										"#" ,
										trans('billing.bill_no') ,
										trans('billing.bill_date') ,
										trans('item.item_name') ,
										trans('glob.barcode') ,
										trans('item.refrence_number') ,
										trans('item.serial_number') ,
										trans('glob.rate') ,
										trans('item.qty') ,
										trans('glob.vat')."(%)" ,								
										trans('billing.dis_amt')."(".$currency.")" ,
										trans('glob.loyalty_discount')."(".$currency.")" ,
										trans('billing.amt')."(".$currency.")" ,
									)); /* column names */
								}
								else{
									$sheet->appendRow(array(
										"#" ,
										trans('billing.bill_no') ,
										trans('billing.bill_date') ,
										trans('item.item_name') ,
										trans('glob.barcode') ,
										trans('item.refrence_number') ,
										trans('item.serial_number') ,
										trans('glob.rate') ,
										trans('item.qty') ,								
										trans('billing.dis_amt')."(".$currency.")" ,
										trans('glob.loyalty_discount')."(".$currency.")" ,
										trans('billing.amt')."(".$currency.")" ,
									)); /* column names */
								}

								/* getting last row number (the one we already filled and setting it to bold */
									$sheet->row($sheet->getHighestRow(), function ($row) {
										$row->setFontWeight('bold');
									});
									if(sizeof($purchases)>0){
										if($this->intVatType!=0)
										{
											foreach ($purchases as $p) {
												$sheet->appendRow( array(
													"#"  => $p->name,
													trans('billing.bill_no')  => '',
													trans('billing.bill_date') => '',
													trans('item.item_name') => '',
													trans('glob.barcode') => '',
													trans('item.refrence_number') => '',
													trans('item.serial_number') => '',
													trans('glob.rate') => '',
													trans('item.qty') => '',
													trans('glob.vat')."(%)" => '',								
													trans('billing.dis_amt')."(".$currency.")" => '',
													trans('glob.loyalty_discount')."(".$currency.")" => '',
													trans('billing.amt')."(".$currency.")" => '',
												)
											);									
												$i=0;
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
													$sheet->appendRow( array(
														"#"  => $i,
														trans('glob.mo_cmo_no')  => " ".Common::fixBill($o->id),
														trans('glob.cash_memo_date') => $o->billingDate,
														trans('item.item_name') => $o->itemName,
														trans('glob.barcode') => $barcode,
														trans('item.refrence_number') => " ".$refrence_number,
														trans('item.serial_number') => $serial_number,
														trans('glob.rate') => " ".Common::fixIt($o->rate),
														trans('item.qty') => " ".Common::fixQty($o->qty),
														trans('glob.vat') => " ".Common::fixIt($o->vat),									
														trans('billing.dis_amt')."(".$currency.")" => " ".Common::fixIt($o->dis_amt),
														trans('glob.loyalty_discount')."(".$currency.")" => " ".Common::fixIt($o->loydis_amt),
														trans('billing.amt')."(".$currency.")" => " ".Common::fixIt($o->amt - $o->loydis_amt),
													)
												);
												}
												$sheet->appendRow( array(
													"#"  => '',
													trans('glob.cash_memo_no')  => '',
													trans('glob.cash_memo_date') => '',
													trans('item.item_name') => '',
													trans('glob.barcode') => '',
													trans('item.refrence_number') => '',
													trans('item.serial_number') => '',
													trans('glob.rate') => '',
													trans('item.qty') => trans('item.qty') ."=".Common::fixQty($p->qty),
													trans('glob.vat') => '',
													trans('billing.dis_amt') => trans('billing.dis_amt')." = ".Common::fixIt($p->dis_amt),
													trans('glob.loyalty_discount') => trans('glob.loyalty_discount')." = ".Common::fixIt($p->loydis_amt),
													trans('billing.amt') =>  trans('billing.amt')." = ".Common::fixIt($p->amt - $p->loydis_amt),
												)
											);
											}
										}
										else{
											foreach ($purchases as $p) {
												$sheet->appendRow( array(
													"#"  => $p->name,
													trans('billing.bill_no')  => '',
													trans('billing.bill_date') => '',
													trans('item.item_name') => '',
													trans('glob.barcode') => '',
													trans('item.refrence_number') => '',
													trans('item.serial_number') => '',
													trans('glob.rate') => '',
													trans('item.qty') => '',						
													trans('billing.dis_amt')."(".$currency.")" => '',
													trans('glob.loyalty_discount')."(".$currency.")" => '',
													trans('billing.amt')."(".$currency.")" => '',
												)
											);									
												$i=0;
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
													$sheet->appendRow( array(
														"#"  => $i,
														trans('glob.cash_memo_no')  => " ".Common::fixBill($o->id),
														trans('glob.cash_memo_date') => $o->billingDate,
														trans('item.item_name') => $o->itemName,
														trans('glob.barcode') => $barcode,
														trans('item.refrence_number') => $refrence_number,
														trans('item.serial_number') => $serial_number,
														trans('glob.rate') => " ".Common::fixIt($o->rate),
														trans('item.qty') => " ".Common::fixQty($o->qty),				
														trans('billing.dis_amt')."(".$currency.")" => " ".Common::fixIt($o->dis_amt),
														trans('glob.loyalty_discount')."(".$currency.")" => " ".Common::fixIt($o->loydis_amt),
														trans('billing.amt')."(".$currency.")" => " ".Common::fixIt($o->amt - $o->loydis_amt),
													)
												);
												}
												$sheet->appendRow( array(
													"#"  => '',
													trans('glob.cash_memo_no')  => '',
													trans('glob.cash_memo_date') => '',
													trans('item.item_name') => '',
													trans('glob.barcode') => '',
													trans('item.refrence_number') => '',
													trans('item.serial_number') => '',
													trans('glob.rate') => '',
													trans('item.qty') => trans('item.qty') ."=".Common::fixQty($p->qty),
													trans('billing.dis_amt') => trans('billing.dis_amt')." = ".Common::fixIt($p->dis_amt),
													trans('glob.loyalty_discount') => trans('glob.loyalty_discount')." = ".Common::fixIt($p->loydis_amt),
													trans('billing.amt') =>  trans('billing.amt')." = ".Common::fixIt($p->amt - $p->loydis_amt),
												)
											);
											}
										}
									}
									if($this->intVatType!=0){
										$sheet->appendRow( array(
											"#"  => '',
											trans('glob.cash_memo_no')  => '',
											trans('glob.cash_memo_date') => '',
											trans('item.item_name') => '',
											trans('glob.barcode') => '',
											trans('item.refrence_number') => '',
											trans('item.serial_number') => '',
											trans('glob.rate') => trans('glob.total'),
											trans('item.qty') => " ".Common::fixQty($totalQty),
											trans('glob.vat') => '',						
											trans('billing.dis_amt')."(".$currency.")" => " ".Common::fixIt($totalDis),
											trans('glob.loyalty_discount')."(".$currency.")" => " ".Common::fixIt($totalLoyDis),
											trans('billing.amt')."(".$currency.")" => " ".Common::fixIt($total),			
										)
									);
									}
									else{
										$sheet->appendRow( array(
											"#"  => '',
											trans('glob.cash_memo_no')  => '',
											trans('glob.cash_memo_date') => '',
											trans('item.item_name') => '',
											trans('glob.barcode') => '',
											trans('item.refrence_number') => '',
											trans('item.serial_number') => '',
											trans('glob.rate') => trans('glob.total'),
											trans('item.qty') => " ".Common::fixQty($totalQty),					
											trans('billing.dis_amt')."(".$currency.")" => " ".Common::fixIt($totalDis),
											trans('glob.loyalty_discount')."(".$currency.")" => " ".Common::fixIt($totalLoyDis),
											trans('billing.amt')."(".$currency.")" => " ".Common::fixIt($total),			
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
}
catch(Exception $e){
	Log::error("SalesReportController@exportTo ".$e);
	return View("errors.exception");

}
}

public function getCustomerSalesQuery($fromDate,$toDate,$customer_id){
	try{
		$total = 0;
		$totalRate =0;
		$totalQty = 0;
		$totalDis = 0;
		$totalLoyDis = 0;
		$customer_id = $customer_id ?? 0;
		
		$purchases =DB::select("SELECT id1 as id ,customer_name as name,qty1 as qty, rate1 as rate,vat_percent1 as vat_percent,amt1 as amt,bil_amt1 as bil_amt,dis_amt1 as dis_amt,loydis_amt1 as loydis_amt,cust as customer_id from
			(SELECT c.id as id1,c.name as customer_name,sum(bi.qty) as qty1,sum(bi.rate) as rate1, avg(bi.vat) as vat_percent1, 

			CASE WHEN $this->intVatType !=2 THEN sum((bi.rate - ((((b.dis_amt/(b.subtotal + b.v_amt))*100)*bi.rate)/100)) * bi.qty) 

			WHEN $this->intVatType=2 THEN sum(((bi.rate + (bi.rate * bi.vat /100) - ((((b.dis_amt/(b.subtotal + b.v_amt))*100)*(bi.rate + (bi.rate * bi.vat /100)) )/100)) * bi.qty)) End as amt1, sum(b.bil_amt) as bil_amt1, 

			CASE WHEN $this->intVatType!=2 THEN sum(((((b.dis_amt/(b.subtotal + b.v_amt))*100)*bi.rate)/100) * bi.qty) 

			WHEN $this->intVatType=2 THEN SUM((((((b.dis_amt/(b.subtotal + b.v_amt))*100)*(bi.rate + ( (bi.rate * bi.vat)/100 ) ) )/100 ) * bi.qty)) END as dis_amt1,b.customer_id as cust, 

			sum(((((b.loyalty_discount/(b.subtotal + b.v_amt))*100)*bi.rate)/100) * bi.qty) as loydis_amt1 FROM billings b INNER JOIN billing_items bi ON b.id =bi.billing_id INNER JOIN customers c on b.customer_id=c.id

			where b.billing_status <> 'hold' AND CASE WHEN  $customer_id > 0 THEN b.customer_id = $customer_id ELSE b.customer_id != 'null' END and CAST(b.created_at AS DATE) BETWEEN '".$fromDate."' and '".$toDate."' GROUP BY b.customer_id)as result ORDER By amt DESC");


		foreach ($purchases as $row) {	
			 	
	
			$p=DB::select("SELECT id1 as id,billingDate1 as billingDate,bil_amt1 as bil_amt,dis_amt1 as dis_amt,qty1 as qty,rate1 as rate,vat1 as vat,itemName1 as itemName,itemCode1 as itemCode,refrence_number1 as refrence_number,serial_number1 as serial_number,dis_amt1 as dis_amt,loydis_amt1 as loydis_amt,amt1 as amt from
				(SELECT b.id as id1,DATE_FORMAT(b.created_at,'".$this->rDateFormatSQL."') as billingDate1 ,
				b.bil_amt as bil_amt1,bi.qty as qty1,bi.rate as rate1,bi.vat as vat1,
				(SELECT items.itemName FROM items WHERE items.id=bi.item_id) as itemName1,
				(SELECT items.barcode FROM items WHERE items.id=bi.item_id) as itemCode1,
				(SELECT items.serial_number FROM items WHERE items.id=bi.item_id) as refrence_number1,
				bi.serial_no as serial_number1,

				CASE WHEN $this->intVatType !=2 THEN
				(((((b.dis_amt/(b.subtotal + b.v_amt))*100)*bi.rate)/100 ) * bi.qty)
				WHEN $this->intVatType = 2 THEN
				(((((b.dis_amt/(b.subtotal + b.v_amt))*100)*(bi.rate + ( (bi.rate * bi.vat)/100 ) ) )/100 ) * bi.qty)
				END
				as dis_amt1,
				(((((b.loyalty_discount/(b.subtotal + b.v_amt))*100)*bi.rate)/100 ) * bi.qty) as loydis_amt1,
				CASE when $this->intVatType!=2 THEN
				((bi.rate - ((((b.dis_amt/(b.subtotal + b.v_amt))*100)*bi.rate)/100)) * bi.qty) 
				when $this->intVatType=2 THEN

				((bi.rate + (bi.rate * bi.vat /100) - ((((b.dis_amt/(b.subtotal + b.v_amt))*100)*(bi.rate + (bi.rate * bi.vat /100)) )/100)) * bi.qty)
				End as amt1

				FROM billings b INNER JOIN billing_items bi ON b.id = bi.billing_id 
				where b.billing_status <> 'hold' AND b.customer_id =$row->id and
				CAST(b.created_at AS DATE) BETWEEN '".$fromDate."' and '".$toDate."') as result ORDER BY amt DESC");
			$row->child = $p;
			$total = ($row->amt-$row->loydis_amt) + $total;
			$totalRate =$row->rate + $totalRate;
			$totalQty = $row->qty + $totalQty;
			$totalDis = $row->dis_amt + $totalDis;
			$totalLoyDis = $row->loydis_amt + $totalLoyDis;
		}

		return array(
			'purchases' =>$purchases,
			'total' => $total,
			'totalRate' => $totalRate,	
			'totalQty' => $totalQty,
			'totalDis' => $totalDis,
			'totalLoyDis' => $totalLoyDis
		);
	}
	catch(Exception $e){
		Log::error("SalesReportController@getCustomerSalesQuery ".$e);
		return View("errors.exception");

	}
}
/**
	 * Display a listing of the resource. Used for Customer_Sales_Summary_Report
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
public function customerCSummary(Request $request){

	$customers = array();
	$customers1 = Customer::all();	
	$customers[] = (object) array('id'=>'0','name' => 'All');
	foreach ($customers1 as $row) {
		$customers[]=$row;
	}	
	$date = Carbon::now()->format('m/d/Y');
	$date1 = Carbon::now()->format('m/d/Y');
	$mallDetails = $this->mallDetails();
	$res = array(
		"customers" =>$customers,
		"date" => $date,
		"date1" => $date1,
		"mallDetails" => $mallDetails,
	);

	if($request->ajax()){
		return Response($res,200);
	}else{
		return View('pages.sales_reports.customer_sales_summary.customer_sales_summary', $res);
	}
}

/**
	 * Select data which is displayed on report view table . used for Customer_Sales_Summary_Report
	 * @param  REquest $request [description]
	 * @return [type]           [description]
	 */
public function getLedgerCSummary(Request $request){		
	$dateArray 		= 	$request->all()["data"]; 
	$fromDate 		= 	(new Carbon($dateArray['fromDate'])) ;
	$toDate 		= 	(new Carbon($dateArray['toDate'])) ;		
	$customer_id 	= 	$dateArray['customer_id'];
	$total 			= 	0;
	$data 			= 	$this->getCustomerSalesSummaryQuery($fromDate,$toDate,$customer_id);
	$customers 		= 	array();
	$customers1 	= 	Customer::all();	
	$customers[] 	= 	(object) array('id'=>'0','name' => 'All');
	foreach ($customers1 as $row) {
		$customers[]=$row;
	}	
	$labels 		= 	$this->getLabels();		
	$purchases 		= 	$data['purchases'];
	$total 			= 	$data['total'];
	$res = array(
		"customers"  => 	$customers,			
		"purchases" => 		$purchases,	
		"total" =>			$total,
		"labels" => 		$labels,
		"toDatePrint" => 	(new Carbon($toDate))->format($this->rDateFormat),
		"fromDatePrint" => 	(new Carbon($fromDate))->format($this->rDateFormat),			
	);
	if($request->ajax()){
		return Response($res,200);
	}else{
		return View("pages.sales_reports.customer_sales_summary.customer_sales_summary",$res);
	}
}

	/**
	 * Function created for create and export exel and pdf file . used for Customer_Sales_Summary_Report
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function exportToCSummary(Request $request){	

		$dateArray 	 = 	$request->all(); 
		$fromDate    =  (new Carbon($dateArray['from']));
		$toDate      = 	(new Carbon($dateArray['to']));
		$customer_id = 	$dateArray['customer'];
		$total       =	0;
		$currency    =	$this->optioncurr;
		$data 		 = 	$this->getCustomerSalesSummaryQuery($fromDate,$toDate,$customer_id);
		$fromDate    = 	date($this->rDateFormat, strtotime($fromDate));
		$toDate      = 	date($this->rDateFormat, strtotime($toDate));
		$purchases   = 	$data['purchases'];
		$total       = 	$data['total'];
		if(Input::get('pdf')){	
			$pdf = PDF::loadView('pages.sales_reports.customer_sales_summary.customer_sales_summary_pdf', [
				"purchases" => 	$purchases ,
				"total"=> 		$total,
				"fromDate" => 	$fromDate,
				"toDate" => 	$toDate,
				"i" =>			'1',
				"curr"=>		$currency
			]); 
			$pdf->setPaper('a4');
			$carbon = Carbon::today();
			$timestamp = $carbon->timestamp;
			$format = $carbon->format($this->rDateFormat);
			$exportName = 'CustomerSalesSummary-'.$format.'.pdf';     
			return $pdf->download($exportName);
		}elseif (Input::get('xls')) {
			$carbon = Carbon::today();
			$timestamp = $carbon->timestamp;
			$format = $carbon->format($this->rDateFormat);
			$exportName = 'CustomerSalesSummary-'.$format; 
			Excel::create($exportName, function ($excel) 
				use($purchases,
					$total,
					$fromDate,
					$toDate,
					$currency) {
					$excel->sheet('CustomerSalesSummary', function ($sheet) 
						use($purchases,
							$total,
							$fromDate,
							$toDate,
							$currency) {
							$sheet->appendRow(array('', ''));
							$sheet->appendRow(array(trans('glob.from'), $fromDate ,trans('glob.to'),$toDate));
							$sheet->row($sheet->getHighestRow(), function ($row) {
								$row->setFontWeight('bold');
							});	
							$sheet->appendRow(array('', ''));
							/* setting column names for data - you can of course set it manually */
							$sheet->appendRow(array(
								"#",	
								trans('billing.bill_no') ,						
								trans('billing.bill_date') ,		
								trans('stock.amt')."(".$currency.")" ,		
							)); /* column names */
							/* getting last row number (the one we already filled and setting it to bold */
								$sheet->row($sheet->getHighestRow(), function ($row) {
									$row->setFontWeight('bold');
								});
								if(sizeof($purchases) >0 ){
									$itemList = array();
									foreach ($purchases as $p) {						
										$sheet->appendRow( array(
											"#"  => $p->name,	
											trans('billing.bill_no') => '',						
											trans('billing.bill_date') => '',		
											trans('stock.amt')."(".$currency.")" => '',		
										)
									);
										$i=0;
										foreach ($p->child as $o) {						
											$i++;						
											$sheet->appendRow(array(
												"#"  => $i,	
												trans('billing.bill_no') => " ".Common::fixBill($o->id),					
												trans('billing.bill_date') => $o->billingDate,		
												trans('stock.amt')."(".$currency.")" => " ".Common::fixIt($o->bil_amt),		
											)
										);
										}
										$sheet->appendRow( array(
											"#"  => '',	
											trans('billing.bill_no') => '',						
											trans('billing.bill_date') => '',		
											trans('stock.amt') => trans('stock.amt')." = ".Common::fixIt($p->bil_amt),		
										)
									);
									}
								}
								$sheet->appendRow( array(
									trans('glob.id')  => '',	
									trans('billing.bill_no') => '',							
									trans('billing.bill_date') => trans('glob.total'),	
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
	}

	public function getCustomerSalesSummaryQuery($fromDate,$toDate,$customer_id){
		$total = 0;
		$customer_id = $customer_id ?? 0;
		
		$purchases =DB::select("SELECT id1 as id ,customer as name,bil_amt1 as bil_amt from
		(SELECT c.id as id1,c.name as customer,sum(b.bil_amt) as bil_amt1
			FROM billings b INNER JOIN customers c on b.customer_id=c.id  
			where b.billing_status <> 'hold' AND 
			CASE WHEN $customer_id > 0 THEN  b.customer_id = $customer_id ELSE b.customer_id != 'null' END and
			CAST(b.created_at AS DATE) BETWEEN '".$fromDate."' and '".$toDate."' GROUP BY b.customer_id)as result ORDER BY bil_amt DESC");
		foreach ($purchases as $row) {	
			 	
			$p=DB::select("SELECT b.id,DATE_FORMAT(b.created_at,'".$this->rDateFormatSQL."') billingDate, b.bil_amt ,
				(SELECT customers.name FROM customers WHERE customers.id =b.customer_id) as customerName 
				FROM billings b 
				where b.billing_status <> 'hold' AND 
				b.customer_id =$row->id and
				CAST(b.created_at AS DATE) BETWEEN '".$fromDate."' and '".$toDate."'  ORDER BY bil_amt DESC");
			$row->child = $p;
			$total = $row->bil_amt + $total;
		}
		return array(
			'purchases' => $purchases,
			'total' => $total
		);
	}
	/**
	 * Display a listing of the resource. Used for Sales_Profit_Report
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function customerSProfit(Request $request){
		$date = Carbon::now()->format('m/d/Y');
		$date1 = Carbon::now()->format('m/d/Y');
		$mallDetails = $this->mallDetails();
		$res = array(
			"date" => $date,
			"date1" => $date1,
			"mallDetails" => $mallDetails,
		);
		if($request->ajax()){
			return Response($res,200);
		}else{
			return View('pages.sales_reports.sales_profit.sales_profit', $res);
		}
	}

	/**
	 * Select data which is displayed on report view table . used for Sales_Profit_Report
	 * @param  REquest $request [description]
	 * @return [type]           [description]
	 */
	public function getLedgerSProfit(REquest $request){		
		$dateArray 	= 			$request->all()["data"]; 
		$fromDate 	= 			new Carbon($dateArray['fromDate']);
		$toDate 	= 			new Carbon($dateArray['toDate']);		
		$total 		=			0;
		$totalSP 	=			0;
		$totalCP 	=			0;
		$SP 		=			0;
		$CP 		=			0;
		$itemList 	= 			[];	
		$percentage =			0;
		$data 		= 			$this->getSalesProfitQuery($fromDate,$toDate);
		$labels 	= 			$this->getLabels();	
		$total		= 			$data['total'];
		$totalSP	= 			$data['totalSP'];
		$totalCP	= 			$data['totalCP'];
		$SP			= 			$data['SP'];
		$CP			= 			$data['CP'];
		$itemList	= 			$data['itemList'];
		$percentage	= 			$data['percentage'];
		$purchases	= 			$data['purchases'];		
		$res = array(
			"total" =>			Common::fixIt($total),
			"itemList"	=> 		$itemList,
			"totalCP" => 		Common::fixIt($totalCP),
			"totalSP" => 		Common::fixIt($totalSP),	
			"labels"  => 		$labels,
			"toDatePrint" => 	(new Carbon($toDate))->format($this->rDateFormat),
			"fromDatePrint" => 	(new Carbon($fromDate))->format($this->rDateFormat),			
		);
		if($request->ajax()){
			return Response($res,200);
		}else{
			return View("pages.sales_reports.sales_profit.sales_profit",$res);
		}
	}

	/**
	 * Function created for create and export exel and pdf file . used for Sales_Profit_Report
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function exportToSProfit(Request $request){				
		$dateArray 	= 			$request->all(); 
		$fromDate 	= 			new Carbon($dateArray['from']);
		$toDate 	= 			new Carbon($dateArray['to']);
		$total 		=			0;
		$totalSP 	=			0;
		$totalCP 	=			0;
		$SP 		=			0;
		$CP 		=			0;
		$itemList  	= 			[];	
		$percentage = 			0;
		$currency 	=			$this->optioncurr;
		$data  		= 			$this->getSalesProfitQuery($fromDate,$toDate);
		$total		= 			$data['total'];
		$totalSP	= 			$data['totalSP'];
		$totalCP	= 			$data['totalCP'];
		$SP			= 			$data['SP'];
		$CP			= 			$data['CP'];
		$itemList	= 			$data['itemList'];
		$percentage	= 			$data['percentage'];
		$purchases	= 			$data['purchases'];	
		if(Input::get('pdf')){	
			$pdf = PDF::loadView('pages.sales_reports.sales_profit.sales_profit_pdf', [
				"total" 	=>	Common::fixIt($total),
				"itemList"	=> 	$itemList,
				"totalCP" 	=> 	Common::fixIt($totalCP),
				"totalSP" 	=> 	Common::fixIt($totalSP),
				"fromDate" 	=> 	(new Carbon($fromDate))->format($this->rDateFormat),
				"toDate" 	=>  (new Carbon($toDate))->format($this->rDateFormat),
				"i" 		=>	'1',
				"curr"		=>	$currency
			]); 
			$pdf->setPaper('a4');
			$carbon = Carbon::today();
			$timestamp = $carbon->timestamp;
			$format = $carbon->format($this->rDateFormat);
			$exportName = 'SalesProfit-'.$format.'.pdf';     
			return $pdf->download($exportName);
		}elseif (Input::get('xls')) {
			$carbon = Carbon::today();
			$timestamp = $carbon->timestamp;
			$format = $carbon->format($this->rDateFormat);
			$exportName = 'SalesProfit-'.$format; 
			Excel::create($exportName, function ($excel) 
				use($purchases,
					$fromDate,
					$toDate,
					$currency) {
					$excel->sheet('SalesProfit', function ($sheet) 
						use($purchases,
							$fromDate,
							$toDate,
							$currency) {
							$i=0;
							$itemList = array();
							$total=0;
							$totalSP=0;
							$totalCP=0;
							$SP=0;
							$CP=0;		
							$sheet->appendRow(array('', ''));
							$sheet->appendRow(array(trans('glob.from'), (new Carbon($fromDate))->format($this->rDateFormat) ,trans('glob.to'),(new Carbon($toDate))->format($this->rDateFormat)));
							$sheet->row($sheet->getHighestRow(), function ($row) {
								$row->setFontWeight('bold');
							});			
							$sheet->appendRow(array('', ''));
							/* setting column names for data - you can of course set it manually */
							$sheet->appendRow(array(
								'#' ,
								trans('billing.bill_no')  ,
								trans('billing.bill_date') ,
								trans('item.item_name') ,
								trans('glob.barcode') ,
								trans('item.refrence_number') ,
								trans('item.serial_number') ,
								trans('item.qty') ,
								trans('glob.rate'),
								trans('item.c_p'),
								trans('glob.total_s_p'),
								trans('glob.total_c_p'),
								trans('glob.total_profit'),
								trans('glob.percentage')."(%)" ,
							)); /* column names */
							/* getting last row number (the one we already filled and setting it to bold */
								$sheet->row($sheet->getHighestRow(), function ($row) {
									$row->setFontWeight('bold');
								});
								if(sizeof($purchases)>0){
									foreach ($purchases as $o) {
										$i++;
										$qty=0;
										if(is_null($o->return_qty)){
											if($o->offer_type ==0){
												if($o->costPrise == 0){
													$SP = $o->qty * $o->rate;
													$CP = $o->qty * $o->costPrise;
													$percentage = 100;
												}else{
													$SP = $o->qty * $o->rate;
													$CP = $o->qty * $o->costPrise;
													$percentage = (($SP - $CP)/$CP)*100;
												}
											}else{
												$SP = 0;
												$CP = 0;
												$percentage =0;
											}
											$qty=$o->qty;
										}else{
											$qty=$o->return_qty;
											if($o->offer_type ==0){
												if($qty==0){
													$SP = 0;
													$CP = 0;
													$percentage =0;
												}else{
													if($o->costPrise == 0){
														$SP = $qty * $o->rate;
														$CP = $qty * $o->costPrise;
														$percentage = 100;
													}else{
														$SP = $qty * $o->rate;
														$CP = $qty * $o->costPrise;
														$percentage = (($SP - $CP)/$CP)*100;
													}
												}
											}else{
												$SP = 0;
												$CP = 0;
												$percentage =0;
											}
										}
										if($o->serial_number == null){
											$serial_number = "NA";
										}else{
											$serial_number = $o->serial_number;
										}
										if($o->refrence_number == null){
											$refrence_number = "NA";
										}else{
											$refrence_number = $o->refrence_number;
										}
										$sheet->appendRow(array(
											'#'  => $i,
											trans('billing.bill_no')  => " ".Common::fixBill($o->id),
											trans('billing.bill_date') => $o->billingDate,
											trans('item.item_name') => $o->itemName,
											trans('glob.barcode') => $o->itemCode,
											trans('item.refrence_number') => $refrence_number,
											trans('item.serial_number') => $serial_number,
											trans('item.qty') =>Common::fixQty($qty),
											trans('glob.rate')=>Common::fixIt($o->rate),
											trans('item.c_p')=>Common::fixIt($o->costPrise),
											trans('glob.total_s_p')=>Common::fixIt($SP),
											trans('glob.total_c_p')=>Common::fixIt($CP),
											trans('glob.total_profit')=>Common::fixIt(($SP - $CP)),
											trans('glob.percentage')."(%)" => " ".Common::fixIt($percentage),
										)
									);
										$totalSP = $SP +$totalSP ;
										$totalCP = $CP +$totalCP ;
										$total = ($SP - $CP) + $total;
									}
								}
								$sheet->appendRow( array(
									'#' => '',
									trans('billing.bill_no')  => '',
									trans('billing.bill_date') => '',
									trans('item.item_name') => '',
									trans('glob.barcode') => '',
									trans('item.refrence_number') => '',
									trans('item.serial_number') => '',
									trans('item.qty') => '',
									trans('glob.rate')=> '',
									trans('item.c_p') => '',
									trans('glob.total_s_p')=> trans('glob.total_s_p')." = ".Common::fixIt($totalSP) ,
									trans('glob.total_c_p')=> trans('glob.total_c_p')." = ".Common::fixIt($totalCP) ,
									trans('glob.total_profit')=>trans('glob.total_profit')." = ". Common::fixIt($total),
									trans('glob.percentage')."(%)" =>'',
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

public function getSalesProfitQuery($fromDate,$toDate){
	$total=0;
	$totalSP=0;
	$totalCP=0;
	$SP=0;
	$CP=0;
	$itemList = [];	
	$percentage=0;
	$purchases = DB::select("SELECT b.id,DATE_FORMAT(b.created_at,'".$this->rDateFormatSQL."') billingDate, 
		b.bil_amt,b.dis_amt,bi.qty,bi.rate,bi.offer_type,items.itemName as itemName ,
		items.barcode as itemCode ,
		items.serial_number as refrence_number ,
		bi.serial_no as serial_number ,
		items.costPrise as costPrise ,
		(bi.qty - (SELECT sum(bill_return_items.qty) 
		FROM bill_return_items INNER JOIN bill_returns on bill_return_items.return_id=bill_returns.id 
		where bill_returns.billing_id=b.id and bill_return_items.item_id=bi.item_id)) as return_qty
		FROM billings b INNER JOIN billing_items bi ON b.id = bi.billing_id INNER JOIN items on items.id = bi.item_id 
		where b.billing_status <> 'hold' AND  CAST(b.created_at AS DATE) BETWEEN '".$fromDate."' and '".$toDate."'  ORDER BY b.created_at ASC");
	if(sizeof($purchases)>0){
		foreach ($purchases as $o) {				
			$qty=0;
			if(is_null($o->return_qty)){
				if($o->offer_type ==0){
					if($o->costPrise == 0){
						$SP = $o->qty * $o->rate;
						$CP = $o->qty * $o->costPrise;
						$percentage = 100;
					}else{
						$SP = $o->qty * $o->rate;
						$CP = $o->qty * $o->costPrise;
						$percentage = (($SP - $CP)/$CP)*100;
					}
				}else{
					$SP = 0;
					$CP = 0;
					$percentage =0;
				}
				$qty=$o->qty;
			}else{
				$qty=$o->return_qty;
				if($o->offer_type ==0){
					if($qty==0){
						$SP = 0;
						$CP = 0;
						$percentage =0;
					}else{
						if($o->costPrise == 0){
							$SP = $qty * $o->rate;
							$CP = $qty * $o->costPrise;
							$percentage = 100;
						}else{
							$SP = $qty * $o->rate;
							$CP = $qty * $o->costPrise;
							$percentage = (($SP - $CP)/$CP)*100;
						}
					}
				}else{
					$SP = 0;
					$CP = 0;
					$percentage =0;
				}
			}
			if($o->serial_number == null){
				$serial_number = "NA";
			}else{
				$serial_number = $o->serial_number;
			}

			array_push($itemList, array(
				"id"  => Common::fixBill($o->id),
				"billingDate" => $o->billingDate,
				"itemName" => $o->itemName,
				"itemCode" => $o->itemCode,
				"serial_number" => $serial_number,
				"qty" => Common::fixQty($qty),
				"rate" => Common::fixIt($o->rate),
				"costPrise" => Common::fixIt($o->costPrise),
				"totalSP" => Common::fixIt($SP),
				"totalCP" => Common::fixIt($CP),
				"totalAmt" => Common::fixIt(($SP - $CP)),
				"percentage"=> Common::fixIt($percentage),
				"refrence_number" => $o->refrence_number ?? 'NA',
			)
		);

			$totalSP = $SP +$totalSP ;
			$totalCP = $CP +$totalCP ;
			$total 	 = ($SP - $CP) + $total;
		}
	}

	return array(
		'total'			=> $total,
		'totalSP'		=> $totalSP,
		'totalCP'		=> $totalCP,
		'SP'			=> $SP,
		'CP'			=> $CP,
		'itemList'		=> $itemList,
		'percentage'	=> $percentage,
		'purchases'		=> $purchases,
	);
}

/**
	 * Display a listing of the resource. Used for Cash_Memo_Categorywise_Report
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
public function categoryCatCashMemo(Request $request){

	$categoriesList = Category::all();		
	
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
	$date1 = Carbon::now()->format('m/d/Y');
	$mallDetails = $this->mallDetails();
	$res = array(
		"categoriesList" => $categoriesList,
		"categories" =>$categories,
		"sub_categories" => $sub_categories,
		"date" => $date,
		"date1" => $date1,
		"mallDetails" => $mallDetails,
	);

	if($request->ajax()){
		return Response($res,200);
	}else{
		return View('pages.sales_reports.cash_memo_categorywise.cash_memo_categorywise', $res);
	}
}

/**
	 * Select data which is displayed on report view table . used for Cash_Memo_Catagorywise_Report
	 * @param  REquest $request [description]
	 * @return [type]           [description]
	 */
public function getLedgerCatCashMemo(Request $request){		
	$dateArray 			= 	$request->all()["data"]; 
	$fromDate 			= 	new Carbon($dateArray['fromDate']);
	$toDate 			= 	new Carbon($dateArray['toDate']);		
	$category_id 		= 	$dateArray['category_id'];
	$sub_category_id 	= 	$dateArray['sub_category_id'];
	$total 				=	0;
	$totalDis 			=	0;
	$itemList 			= 	[];
	$discountPercent 	=	0;
	$totalQty 			=	0;
	$totalRate 			=	0;
	$totalLoyDis 		=	0;
	$data 				= 	$this->getCahMemoCatWiseQuery($fromDate,$toDate,$category_id,$sub_category_id);
	$labels 			= 	$this->getLabels();
	$total 				=	$data['total'];
	$totalDis 			=	$data['totalDis'];
	$totalQty 			=	$data['totalQty'];
	$totalRate 			=	$data['totalRate'];
	$totalLoyDis 		=	$data['totalLoyDis'];
	$purchases 			=	$data['purchases'];
	$res = array(
		"total" 		=>	$total,
		"itemList"		=> 	$itemList,
		"totalDis" 		=> 	$totalDis,
		"totalLoyDis" 	=> 	$totalLoyDis,
		"totalQty" 		=>	$totalQty,	
		"totalRate" 	=>	$totalRate,	
		"purchases" 	=> 	$purchases,
		"labels" 		=> 	$labels,	
		"toDatePrint" 	=> 	(new Carbon($toDate))->format($this->rDateFormat),
		"fromDatePrint" => 	(new Carbon($fromDate))->format($this->rDateFormat),		
	);

	if($request->ajax()){
		return Response($res,200);
	}else{
		return View("pages.sales_reports.cash_memo_categorywise.cash_memo_categorywise",$res);
	}
}

	/**
	 * Function created for create and export exel and pdf file . used for Cash_Memo_Categorywise_Report
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function exportToCatCashMemo(Request $request){				
		$dateArray 			= 	$request->all(); 
		$fromDate 			= 	new Carbon($dateArray['from']);
		$toDate 			= 	new Carbon($dateArray['to']);
		$category_id 		= 	$dateArray['category'];
		$sub_category_id 	= 	$dateArray['sub_category'];
		$total 				=	0;
		$totalDis 			=	0;
		$itemList 			= 	[];
		$discountPercent 	=	0;
		$totalQty 			=	0;
		$totalRate 			=	0;
		$totalLoyDis 		=	0;
		$currency 			=	$this->optioncurr;
		$data 				= 	$this->getCahMemoCatWiseQuery($fromDate,$toDate,$category_id,$sub_category_id);
		$fromDate 			= 	date($this->rDateFormat, strtotime($fromDate));
		$toDate 			= 	date($this->rDateFormat, strtotime($toDate));
		$total 				=	$data['total'];
		$totalDis 			=	$data['totalDis'];
		$totalQty 			=	$data['totalQty'];
		$totalRate 			=	$data['totalRate'];
		$totalLoyDis 		=	$data['totalLoyDis'];
		$purchases 			=	$data['purchases'];
		if(Input::get('pdf')){	
			$pdf = PDF::loadView('pages.sales_reports.cash_memo_categorywise.cash_memo_categorywise_pdf', [			
				"total" 		=> 	$total,			
				"totalDis" 		=> 	$totalDis,
				"totalLoyDis" 	=> 	$totalLoyDis,
				"totalQty" 		=>	$totalQty,	
				"totalRate" 	=>	$totalRate,	
				"purchases" 	=> 	$purchases,
				"fromDate" 		=> 	$fromDate,
				"toDate" 		=> 	$toDate,
				"i"  			=>	'1',
				"curr" 			=> 	$currency
			]); 
			$pdf->setPaper('a4');
			$carbon = Carbon::today();
			$timestamp = $carbon->timestamp;
			$format = $carbon->format($this->rDateFormat);
			$exportName = 'CategoryCashMemo-'.$format.'.pdf';     
			return $pdf->download($exportName);
		}elseif (Input::get('xls')) {
			$carbon = Carbon::today();
			$timestamp = $carbon->timestamp;
			$format = $carbon->format($this->rDateFormat);
			$exportName = 'CategoryCashMemo-'.$format; 
			Excel::create($exportName, function ($excel) 
				use($purchases,
					$totalDis,
					$totalLoyDis,
					$totalQty,
					$totalRate,
					$total,
					$fromDate,
					$toDate,
					$currency) {
					$excel->sheet('CategoryCashMemo', function ($sheet) 
						use($purchases,
							$totalDis,
							$totalLoyDis,
							$totalQty,
							$totalRate,
							$total,
							$fromDate,
							$toDate,
							$currency) {
							$i=0;
							$itemList = array();
							$sheet->appendRow(array('', ''));
							$sheet->appendRow(array(trans('glob.from'), $fromDate ,trans('glob.to'),$toDate));
							$sheet->row($sheet->getHighestRow(), function ($row) {
								$row->setFontWeight('bold');
							});	
							$sheet->appendRow(array('', ''));
							/* setting column names for data - you can of course set it manually*/
							$sheet->appendRow(array(
								"#" ,
								trans('billing.bill_no')  ,
								trans('billing.bill_date') ,
								trans('glob.customer') ,
								trans('item.item_name') ,
								trans('glob.barcode') ,
								trans('item.refrence_number') ,
								trans('item.serial_number') ,
								trans('glob.rate') ,
								trans('item.qty') ,								
								trans('billing.dis_amt')."(".$currency.")" ,
								trans('glob.loyalty_discount')."(".$currency.")"  ,
								trans('glob.grossSale') ,
								trans('glob.vatValue') ,
								trans('glob.netSale')."(".$currency.")" ,
							)); /* column names*/
							/* getting last row number (the one we already filled and setting it to bold*/
								$sheet->row($sheet->getHighestRow(), function ($row) {
									$row->setFontWeight('bold');
								});
								if(sizeof($purchases)>0){
									foreach ($purchases as $p) {
										$sheet->appendRow( array(
											"#" => $p->name,
											trans('billing.bill_no')  => '',
											trans('billing.bill_date') => '',
											trans('glob.customer') => '',
											trans('item.item_name') => '',
											trans('glob.barcode') => '',
											trans('item.refrence_number') => '',
											trans('item.serial_number') => '',
											trans('glob.rate') => '',
											trans('item.qty') =>'',								
											trans('billing.dis_amt')."(".$currency.")"  => '',
											trans('glob.loyalty_discount')."(".$currency.")"  => '',
											trans('glob.grossSale')=>'',
								            trans('glob.vatValue')=>'' ,
											trans('glob.netSale')."(".$currency.")" => '',
										)
									);
										$i=0;
										foreach ($p->child as $o) {
											$i++;
											$sheet->appendRow( array(
												"#" => $i,
												trans('glob.cash_memo_no')  => " ".Common::fixBill($o->id),
												trans('glob.cash_memo_date') => $o->billingDate,
												trans('glob.customer') => $o->customerName ?? 'NA',
												trans('item.item_name') => $o->itemName,
												trans('glob.barcode') => $o->barcode,
												trans('item.refrence_number') => $o->refrence_number ?? 'NA',
												trans('item.serial_number') => $o->serial_number ?? 'NA',
												trans('glob.rate') => " ".Common::fixIt($o->rate),
												trans('item.qty') => " ".Common::fixQty($o->qty),									
												trans('billing.dis_amt')."(".$currency.")" => " ".Common::fixIt($o->dis_amt),
												trans('glob.loyalty_discount')."(".$currency.")" => " ".Common::fixIt($o->loydis_amt),
												trans('glob.grossSale') => " ".Common::fixIt($o->grossSale),	
												trans('glob.vatValue') => " ".Common::fixIt($o->vat_value),	
												trans('glob.netSale')."(".$currency.")" => " ".Common::fixIt($o->amt - $o->loydis_amt),
											)
										);
										}
										$sheet->appendRow( array(
											"#"  => '',
											trans('glob.cash_memo_no')  => '',
											trans('glob.cash_memo_date') => '',
											trans('glob.customer') => '',
											trans('item.item_name') => '',
											trans('glob.barcode') => '',
											trans('item.refrence_number') => '',
											trans('item.serial_number') => '',
											trans('glob.rate') => '',
											trans('item.qty') => trans('item.qty')." = ".Common::fixIt($p->qty),								
											trans('billing.dis_amt')."(".$currency.")" => trans('billing.dis_amt')." = ". Common::fixIt($p->dis_amt),
											trans('glob.loyalty_discount')."(".$currency.")" => trans('glob.loyalty_discount')." = ". Common::fixIt($p->loydis_amt),	
											trans('glob.grossSale') => trans('glob.grossSale')." = ".Common::fixIt($p->totalgrossSale),					
											trans('glob.vatValue') => trans('glob.vatValue')." = ".Common::fixIt($p->totalvatValue),	
											trans('glob.netSale')."(".$currency.")" => trans('glob.netSale')." = ".Common::fixIt($p->amt - $p->loydis_amt),			
										)
									);
									}
								}	
								$sheet->appendRow( array(
									"#"  => '',
									trans('glob.cash_memo_no')  => '',
									trans('glob.cash_memo_date') => '',
									trans('glob.customer_name') => '',
									trans('item.item_name') => '',
									trans('glob.barcode') => '',
									trans('item.refrence_number') => '',
									trans('item.serial_number') => '',
									trans('glob.rate') => trans('glob.total'),
									trans('item.qty') => " ".Common::fixQty($totalQty),						
									trans('billing.dis_amt')."(".$currency.")" => " ".Common::fixIt($totalDis),
									trans('glob.loyalty_discount')."(".$currency.")" => " ".Common::fixIt($totalLoyDis),	
									trans('glob.grossSale')=>'',
								    trans('glob.vatValue')=>'' ,
									trans('glob.netSale')."(".$currency.")" => " ".Common::fixIt($total - $totalLoyDis),			
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

public function getCahMemoCatWiseQuery($fromDate,$toDate,$category_id,$sub_category_id){
	$totalQty 			=	0;
	$totalDis 			=	0; 
	$totalLoyDis 		=	0; 
	$totalRate 			=	0;
	$total 				=	0; 
	$category_id 		= 	$category_id ?? 0;
	$sub_category_id	= 	$sub_category_id ?? 0;
	$purchases 			= 	DB::select("SELECT c.id,c.categorie as name,sum(bi.qty) as qty,sum(bi.rate) as rate  ,
		sum((bi.rate - ((((b.dis_amt/b.subtotal)*100)*bi.rate)/100)) * bi.qty) as amt,sum(b.bil_amt) as bil_amt,
		sum(((((b.dis_amt/b.subtotal)*100)*bi.rate)/100) * bi.qty) as dis_amt,
		sum(((((b.loyalty_discount/b.subtotal)*100)*bi.rate)/100) * bi.qty) as loydis_amt,
		sum((bi.rate - ((b.dis_amt * bi.rate)/(b.subtotal + b.v_amt)) - (((bi.rate) - ((b.dis_amt * bi.rate)/(b.subtotal + b.v_amt))) - ((((bi.rate) - ((b.dis_amt * bi.rate)/(b.subtotal + b.v_amt)))*100)/(100+(bi.vat)))) ) * bi.qty) as totalgrossSale,
		SUM((((bi.rate) - ((b.dis_amt * bi.rate)/(b.subtotal + b.v_amt))) - ((((bi.rate) - ((b.dis_amt * bi.rate)/(b.subtotal + b.v_amt)))*100)/(100+(bi.vat)))) * (bi.qty)) as totalvatValue
		FROM billing_items bi  INNER JOIN billings b ON b.id = bi.billing_id 
		INNER JOIN items i ON i.id = bi.item_id INNER JOIN categories c ON i.category = c.id
		where  b.billing_status <> 'hold' AND 
		CASE WHEN $category_id > 0 THEN  c.parent_category = $category_id ELSE true END and
		CASE WHEN $sub_category_id > 0 THEN c.id = $sub_category_id ELSE true END and 
		CAST(b.created_at AS DATE) BETWEEN '".$fromDate."' and '".$toDate."' GROUP BY c.id");
	foreach ($purchases as $row) {		 	
		$p=DB::select("SELECT b.id,DATE_FORMAT(b.created_at,'".$this->rDateFormatSQL."') billingDate, 
			b.bil_amt,bi.qty,bi.rate, i.itemName,i.barcode,bi.serial_no as serial_number,i.serial_number as refrence_number,
			(((((b.dis_amt/b.subtotal)*100)*bi.rate)/100 ) * bi.qty) as dis_amt ,
			(((((b.loyalty_discount/b.subtotal)*100)*bi.rate)/100 ) * bi.qty) as loydis_amt ,
			((bi.rate - ((((b.dis_amt/b.subtotal)*100)*bi.rate)/100)) * bi.qty) as amt ,
			((bi.rate - ((b.dis_amt * bi.rate)/(b.subtotal + b.v_amt)) - (((bi.rate) - ((b.dis_amt * bi.rate)/(b.subtotal + b.v_amt))) - ((((bi.rate) - ((b.dis_amt * bi.rate)/(b.subtotal + b.v_amt)))*100)/(100+(bi.vat)))) ) * bi.qty)as grossSale,
			(((bi.rate) - ((b.dis_amt * bi.rate)/(b.subtotal + b.v_amt))) - ((((bi.rate) - ((b.dis_amt * bi.rate)/(b.subtotal + b.v_amt)))*100)/(100+(bi.vat)))) * (bi.qty) as vat_value,
			(SELECT customers.name FROM customers WHERE customers.id = b.customer_id) as customerName
			FROM billing_items bi  INNER JOIN billings b ON b.id = bi.billing_id INNER JOIN items i ON i.id = bi.item_id 
			where  b.billing_status <> 'hold' AND 
			i.category = ".$row->id." and 
			CAST(b.created_at AS DATE) BETWEEN '".$fromDate."' and '".$toDate."'  ORDER BY b.created_at ASC");
		$row->child 	= 	$p;
		$totalQty 		= 	$row->qty + $totalQty;
		$totalDis 		= 	$row->dis_amt +$totalDis ;
		$totalLoyDis 	= 	$row->loydis_amt +$totalLoyDis;
		$totalRate 		= 	$row->rate +$totalRate ;
		$total 			= 	$row->amt + $total;
	}
	return array(
		'purchases' 	=>  $purchases,
		'totalQty' 		=>	$totalQty,
		'totalDis' 		=>	$totalDis, 
		'totalLoyDis' 	=>	$totalLoyDis, 
		'totalRate' 	=>	$totalRate,
		'total' 		=>	$total, 
	);
}

/**
	 * Display a listing of the resource. Used for Sales_Summary_Report
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
public function indexSalesSummary(Request $request){
	try{

		$date = Carbon::now()->format('m/d/Y');
		$date1 = Carbon::now()->format('m/d/Y');
		$mallDetails = $this->mallDetails();
		$res = array(
			"date" => $date,
			"date1" => $date1,
			"mallDetails" => $mallDetails,
		);

		if($request->ajax()){
			return Response($res,200);
		}else{
			return View('pages.sales_reports.sales_summary.sales_summary', $res);
		}

	}catch(Exception $e){
		Log::error("SalesReportController@indexSalesSummary ".$e);
		return View("errors.exception");
	}
}

/**
	 * Select data which is displayed on report view table . used for Sales_Summary_Report
	 * @param  REquest $request [description]
	 * @return [type]           [description]
	 */
public function getLedgerSalesSummary(REquest $request){
	try{		
		$dateArray 			=	$request->all()["data"]; 
		$fromDate 			=	new Carbon($dateArray['fromDate']);
		$toDate 			=	new Carbon($dateArray['toDate']);		
		$total 				=	0;
		$totalCash 			=	0;
		$totalCard 			=	0;
		$itemList 			=	[];	
		$pay_mode 			=	'';
		$totalDiscount 		=	0;
		$totalLoyDis 		=	0;
		$totalVat 			=	0;
		$data 				= 	$this->getSalesSummaryQuery($fromDate,$toDate);
		$total 				= 	$data['total'];	
		$totalCash 			= 	$data['totalCash'];	
		$totalCard 			= 	$data['totalCard'];	
		$itemList 			= 	$data['itemList'];	
		$totalDiscount 		= 	$data['totalDiscount'];	
		$totalLoyDis 		= 	$data['totalLoyDis'];	
		$totalVat 			= 	$data['totalVat'];	
		$purchases			= 	$data['purchases'];
		$labels 			= 	$this->getLabels();				
		$res = array(
			"total" 		=> 	Common::fixIt($total),
			"itemList"		=> 	$itemList,
			"purchases" 	=> 	$purchases,
			"totalCash" 	=> 	Common::fixIt($totalCash),
			"totalCard" 	=> 	Common::fixIt($totalCard),	
			"totalDiscount" => 	Common::fixIt($totalDiscount),
			"totalLoyDis" 	=> 	Common::fixIt($totalLoyDis),	
			"totalVat"		=> 	Common::fixIt($totalVat),
			"labels"		=> 	$labels,	
			"toDatePrint" 	=> 	(new Carbon($toDate))->format($this->rDateFormat),
			"fromDatePrint" => 	(new Carbon($fromDate))->format($this->rDateFormat),	
		);
		if($request->ajax()){
			return Response($res,200);
		}else{
			return View("pages.sales_reports.sales_summary.sales_summary",$res);
		}

	}catch(Exception $e){
		Log::error("SalesReportController@getLedgerSalesSummary ".$e);
		return View("errors.exception");
	}
}

/**
	 * Function created for create and export exel and pdf file . used for Sales_Summary_Report
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
public function exportToSalesSummary(Request $request){	
	try{			
		$dateArray 		= 	$request->all(); 
		$fromDate 		= 	new Carbon($dateArray['from']);
		$toDate 		= 	new Carbon($dateArray['to']);
		$total 			=	0;
		$totalCash 		=	0;
		$totalCard 		=	0;
		$itemList  		= 	[];	
		$pay_mode  		= 	'';
		$totalDiscount 	=	0;
		$totalLoyDis 	=	0;
		$totalVat 		=	0;
		$currency 		=	$this->optioncurr;
		$data  			= 	$this->getSalesSummaryQuery($fromDate,$toDate);
		$total 			= 	$data['total'];	
		$totalCash 		= 	$data['totalCash'];	
		$totalCard 		= 	$data['totalCard'];	
		$itemList 		= 	$data['itemList'];	
		$totalDiscount 	= 	$data['totalDiscount'];	
		$totalLoyDis 	= 	$data['totalLoyDis'];	
		$totalVat 		= 	$data['totalVat'];	
		$purchases		= 	$data['purchases'];
		if(Input::get('pdf')){	
			$pdf = PDF::loadView('pages.sales_reports.sales_summary.sales_summary_pdf', [
				"total" 		=>	Common::fixIt($total),
				"itemList"		=> 	$itemList,
				"purchases" 	=> 	$purchases,
				"totalCash" 	=> 	Common::fixIt($totalCash),
				"totalCard" 	=> 	Common::fixIt($totalCard),
				"totalDiscount" => 	Common::fixIt($totalDiscount),
				"totalLoyDis" 	=> 	Common::fixIt($totalLoyDis),	
				"totalVat" 		=> 	Common::fixIt($totalVat),
				"fromDate" 		=> 	(new Carbon($fromDate))->format($this->rDateFormat)  ,
				"toDate" 		=> 	(new Carbon($toDate))->format($this->rDateFormat) ,
				"i" 			=>	'1',
				"curr"			=>	$currency,
				'intVatType'	=> 	$this->intVatType,
			]); 
			$pdf->setPaper('a4');
			$carbon = Carbon::today();
			$timestamp = $carbon->timestamp;
			$format = $carbon->format($this->rDateFormat);
			$exportName = 'SalesSummary-'.$format.'.pdf';     
			return $pdf->download($exportName);
		}elseif (Input::get('xls')) {
			$carbon = Carbon::today();
			$timestamp = $carbon->timestamp;
			$format = $carbon->format($this->rDateFormat);
			$exportName = 'SalesSummary-'.$format; 
			Excel::create($exportName, function ($excel) 
				use($purchases,
					$fromDate,
					$toDate,
					$currency) {
					$excel->sheet('SalesSummary', function ($sheet) 
						use($purchases,
							$fromDate,
							$toDate,
							$currency) {
							$i=0;
							$itemList = array();
							$total=0;
							$totalCash=0;
							$totalCard=0;
							$itemList = [];	
							$pay_mode = '';
							$totalDiscount=0;
							$totalLoyDis =0;
							$totalVat =0;
							$sheet->appendRow(array('', ''));
							$sheet->appendRow(array(trans('glob.from'), (new Carbon($fromDate))->format($this->rDateFormat),trans('glob.to'),(new Carbon($toDate))->format($this->rDateFormat)));
							$sheet->row($sheet->getHighestRow(), function ($row) {
								$row->setFontWeight('bold');
							});			
							$sheet->appendRow(array('', ''));
							/* setting column names for data - you can of course set it manually */
							if($this->intVatType != 0){
								$sheet->appendRow(array(
									'#',
									trans('billing.bill_date') ,
									trans('billing.bill_no') ,
									trans('glob.customer') ,
									trans('billing.sub_total')."(".$currency.")" ,
									trans('glob.vat')."(".$currency.")" ,
									trans('glob.discount')."(".$currency.")" ,
									trans('glob.loyalty_discount')."(".$currency.")" ,
									trans('billing.mode_of_pay')  ,
									trans('billing.cash_amt')."(".$currency.")" ,
									trans('billing.card_amt')."(".$currency.")" ,
								)); /* column names */
							}else{
								$sheet->appendRow(array(
									'#',
									trans('billing.bill_date') ,
									trans('billing.bill_no') ,
									trans('glob.customer') ,
									trans('billing.sub_total')."(".$currency.")" ,
									trans('glob.discount')."(".$currency.")" ,
									trans('glob.loyalty_discount')."(".$currency.")" ,
									trans('billing.mode_of_pay')  ,
									trans('billing.cash_amt')."(".$currency.")" ,
									trans('billing.card_amt')."(".$currency.")" ,
								)); /* column names */
							}
							/* getting last row number (the one we already filled and setting it to bold*/
								$sheet->row($sheet->getHighestRow(), function ($row) {
									$row->setFontWeight('bold');
								});
								if(sizeof($purchases)>0){
									if($this->intVatType != 0){
										foreach ($purchases as $o) {
											if($o->payment_mode == 1){
												$pay_mode = 'Cash';
											}else if($o->payment_mode == 2){
												$pay_mode = 'Credit & Cash';
											}else if($o->payment_mode == 3){
												$pay_mode = 'Credit';
											}else if($o->payment_mode == 4){
												$pay_mode = 'Credit Card';
											}else if($o->payment_mode == 5){
												$pay_mode = 'Dubai Mall Online';
											}
											if($o->customerName == null){
												$custName="NA";
											}else{
												$custName=$o->customerName;
											}
											$i++;
											$sheet->appendRow(array(
												'#' => $i,
												trans('billing.bill_date') => $o->dateTime,
												trans('billing.bill_no') => " ".Common::fixBill($o->id),
												trans('glob.customer') => $custName,
												trans('billing.sub_total')."(".$currency.")" => " ".Common::fixIt($o->subtotal),
												trans('glob.vat')."(".$currency.")" => " ".Common::fixIt($o->v_amt),
												trans('glob.discount')."(".$currency.")" => " ".Common::fixIt($o->dis_amt),
												trans('glob.loyalty_discount')."(".$currency.")" => " ".Common::fixIt($o->loyalty_discount),
												trans('billing.mode_of_pay') => $pay_mode,
												trans('billing.cash_amt')."(".$currency.")" => " ".Common::fixIt($o->cash_amt),
												trans('billing.card_amt')."(".$currency.")" => " ".Common::fixIt($o->card_amt),
											)
										);
											$totalCash = $o->cash_amt +$totalCash ;
											$totalCard = $o->card_amt +$totalCard ;
											$totalDiscount = $o->dis_amt + $totalDiscount;
											$totalLoyDis = $o->loyalty_discount + $totalLoyDis;
											$total = $o->subtotal + $total;
											$totalVat = $o->v_amt + $totalVat;
										}
									}else{
										foreach ($purchases as $o) {
											if($o->payment_mode == 1){
												$pay_mode = 'Cash';
											}else if($o->payment_mode == 2){
												$pay_mode = 'Credit & Cash';
											}else if($o->payment_mode == 3){
												$pay_mode = 'Credit';
											}else if($o->payment_mode == 4){
												$pay_mode = 'Credit Card';
											}else if($o->payment_mode == 5){
												$pay_mode = 'Dubai Mall Online';
											}
											if($o->customerName == null){
												$custName="NA";
											}else{
												$custName=$o->customerName;
											}
											$i++;
											$sheet->appendRow(array(
												'#' => $i,
												trans('billing.bill_date') => $o->dateTime,
												trans('billing.bill_no') => " ".Common::fixBill($o->id),
												trans('glob.customer') => $custName,
												trans('billing.sub_total')."(".$currency.")" => " ".Common::fixIt($o->subtotal),
												trans('glob.discount')."(".$currency.")" => " ".Common::fixIt($o->dis_amt),
												trans('glob.loyalty_discount')."(".$currency.")" => " ".Common::fixIt($o->loyalty_discount),
												trans('billing.mode_of_pay') => $pay_mode,
												trans('billing.cash_amt')."(".$currency.")" => " ".Common::fixIt($o->cash_amt),
												trans('billing.card_amt')."(".$currency.")" => " ".Common::fixIt($o->card_amt),
											)
										);
											$totalCash = $o->cash_amt +$totalCash ;
											$totalCard = $o->card_amt +$totalCard ;
											$totalDiscount = $o->dis_amt + $totalDiscount;
											$totalLoyDis = $o->loyalty_discount + $totalLoyDis;
											$total = $o->subtotal + $total;
											$totalVat = $o->v_amt + $totalVat;
										}
									}
								}
								if($this->intVatType != 0){
									$sheet->appendRow(array(
										'#' => '',
										trans('billing.bill_date') =>'',
										trans('billing.bill_no') => '',
										trans('glob.customer') => '',
										trans('billing.sub_total')."(".$currency.")" =>  trans('billing.sub_total')." = ".Common::fixIt($total) ,
										trans('glob.vat')."(".$currency.")" =>  trans('glob.vat')." = ".Common::fixIt($totalVat) ,
										trans('glob.discount')."(".$currency.")" => trans('glob.discount')." = ".Common::fixIt($totalDiscount) ,
										trans('glob.loyalty_discount')."(".$currency.")" => trans('glob.discount')." = ".Common::fixIt($totalLoyDis),
										trans('billing.mode_of_pay') => '',
										trans('billing.cash_amt')."(".$currency.")" => trans('billing.cash_amt')." = ".Common::fixIt($totalCash) ,
										trans('billing.card_amt')."(".$currency.")" =>trans('billing.card_amt')." = ".Common::fixIt($totalCard) ,
									)
								);
								}else{
									$sheet->appendRow(array(
										'#' => '',
										trans('billing.bill_date') =>'',
										trans('billing.bill_no') => '',
										trans('glob.customer') => '',
										trans('billing.sub_total')."(".$currency.")" =>  trans('billing.sub_total')." = ".Common::fixIt($total) ,
										trans('glob.discount')."(".$currency.")" => trans('glob.discount')." = ".Common::fixIt($totalDiscount) ,
										trans('glob.loyalty_discount')."(".$currency.")" => trans('glob.discount')." = ".Common::fixIt($totalLoyDis),
										trans('billing.mode_of_pay') => '',
										trans('glob.currency')  => '',
										trans('billing.cash_amt')."(".$currency.")" => trans('billing.cash_amt')." = ".Common::fixIt($totalCash) ,
										trans('billing.card_amt')."(".$currency.")" =>trans('billing.card_amt')." = ".Common::fixIt($totalCard) ,
									)
								);
								}
								$sheet->row($sheet->getHighestRow(), function ($row) {
									$row->setFontWeight('bold');
								});

								$sheet->appendRow(array('', ''));
								$sheet->appendRow(array(trans('glob.sales_with_vat'),Common::fixIt($total + $totalVat) ,));
								$sheet->row($sheet->getHighestRow(), function ($row) {
									$row->setFontWeight('bold');
								});		
								$sheet->appendRow(array(trans('glob.sales_without_vat'), Common::fixIt($total),));
								$sheet->row($sheet->getHighestRow(), function ($row) {
									$row->setFontWeight('bold');
								});	
							});
})->export('xlsx');
return back();
}

}catch(Exception $e){
	Log::error("SalesReportController@exportToSalesSummary ".$e);
	return View("errors.exception");
}
}

public function exportToCRMReport(Request $request){	
	try{			
		$dateArray 		= 	$request->all(); 
		$fromDate 		= 	new Carbon($dateArray['from']);
		$toDate 		= 	new Carbon($dateArray['to']);
		$customer_id    =   $dateArray['customer_id'];
		$brand_id    =   $dateArray['brand_id'];
		$itemList  		= 	[];	
		$data  			= 	$this->getCRMQuery($fromDate,$toDate,$customer_id,$brand_id);
	    $purchases		= 	$data['purchases'];
		$totalTransaction=$data['totalTransaction'];
		$totalQty=$data['totalQty'];
		$totalCustomer=$data['totalCustomer'];
		if(Input::get('pdf')){	
			$pdf = PDF::loadView('pages.sales_reports.crm_report.crm_report_pdf', [
				"purchases" 	=> 	$purchases,
				"fromDate" 		=> 	(new Carbon($fromDate))->format($this->rDateFormat)  ,
				"toDate" 		=> 	(new Carbon($toDate))->format($this->rDateFormat) ,
				"totalTransaction"=>$totalTransaction,
	            "totalCustomer"=>$totalCustomer,
	            "totalQty"=>$totalQty,
				"i" 			=>	'1',
			]); 
			$pdf->setPaper('a4');
			$carbon = Carbon::today();
			$timestamp = $carbon->timestamp;
			$format = $carbon->format($this->rDateFormat);
			$exportName = 'CRMRexport-'.$format.'.pdf';     
			return $pdf->download($exportName);
		}elseif (Input::get('xls')) {
			$carbon = Carbon::today();
			$timestamp = $carbon->timestamp;
			$format = $carbon->format($this->rDateFormat);
			$exportName = 'CRMRexport-'.$format; 
			Excel::create($exportName, function ($excel) 
				use($purchases,

					$fromDate,
					$toDate,
					$totalTransaction,$totalCustomer,$totalQty) {
					$excel->sheet('CRMReport-', function ($sheet) 
						use($purchases,
							$fromDate,
							$toDate,$totalTransaction,$totalCustomer,$totalQty) {
							$i=0;
							$sheet->appendRow(array('', ''));
							$sheet->appendRow(array(trans('glob.from'), (new Carbon($fromDate))->format($this->rDateFormat),trans('glob.to'),(new Carbon($toDate))->format($this->rDateFormat)));
							$sheet->row($sheet->getHighestRow(), function ($row) {
								$row->setFontWeight('bold');
							});			
							$sheet->appendRow(array('', ''));
							/* setting column names for data - you can of course set it manually */
							
					// 			$sheet->appendRow(array(
					// 				'#',
					// 				trans('glob.bill_no') ,
					// 				trans('glob.customer_name') ,
					// 				trans('glob.mobile') ,
					// 				trans('glob.date_of_birth') ,
					// 				trans('billing.bill_date') ,
					// 				trans('glob.city') ,
					// 				trans('glob.salesman') ,
					// 				trans('glob.prefered_mode') ,
					// 				trans('glob.prefered_language') ,
					// 				trans('glob.brand_name'),
					// 				trans('item.refrence_number'),
					// 				trans('billing.item_name') ,
					// 				trans('glob.value') ,
									
					// 			)); /* column names */
							
					// 		/* getting last row number (the one we already filled and setting it to bold*/
					// 			$sheet->row($sheet->getHighestRow(), function ($row) {
					// 				$row->setFontWeight('bold');
					// 			});
					// 			if(sizeof($purchases)>0){
									
					// 					foreach ($purchases as $o) {
											
					// 						$i++;
					// 						$sheet->appendRow(array(
					// 							'#' => $i,
					// 							trans('glob.bill_no') => " ".Common::fixBill($o->bill_id),
					// 							trans('glob.customer_name') => $custName,
					// 							trans('glob.mobile') => $o->mobile,
					// 							trans('glob.date_of_birth') => $birthday,
					// 							trans('billing.bill_date') => $o->bill_date,
					// 							trans('glob.city') => $city,
					// 							trans('glob.salesman') => $o->salesman,
					// 							trans('glob.prefered_mode') => $prefered_mode,
					// 							trans('glob.prefered_language') => $prefered_language,
					// 							trans('glob.brand_name')=>$o->brand_name,
					// 				            trans('item.refrence_number')=>$o->reference_number,
					// 							trans('billing.item_name') => $o->item_name,
					// 							trans('glob.value')=>" ".Common::fixIt($o->value),
					// 						)
					// 					);
											
					// 					}
					// 					$sheet->appendRow(array(
					// 						'#' => '',
					// 						trans('glob.bill_no') =>trans('glob.totalTransaction') ."=".Common::fixQty($totalTransaction),
					// 						trans('glob.customer_name') =>trans('glob.totalCustomer') ."=".Common::fixQty($totalCustomer),
					// 						trans('glob.mobile') => '',
					// 						trans('glob.date_of_birth') =>'',
					// 						trans('billing.bill_date') =>'',
					// 						trans('glob.city') =>'',
					// 						trans('glob.salesman') => '',
					// 						trans('glob.prefered_mode') => '',
					// 						trans('glob.prefered_language') => '',
					// 						trans('glob.brand_name')=>'',
					// 						trans('item.refrence_number')=>'',
					// 						trans('billing.item_name') => trans('glob.totalItemQty') ."=".Common::fixQty($totalQty),
					// 						trans('glob.value')=>'' ,
					// 					)
					// 				);

					// 				 $sheet->row($sheet->getHighestRow(), function ($row) {
					// 				$row->setFontWeight('bold');
									
					// 			});

					// 			}
								
					// 			// $sheet->row($sheet->getHighestRow(), function ($row) {
					// 			// 	$row->setFontWeight('bold');
					// 			// });

					// 			$sheet->appendRow(array('', ''));
								
					// 			// $sheet->row($sheet->getHighestRow(), function ($row) {
					// 			// 	$row->setFontWeight('bold');
					// 			// });		
								
					// 			// $sheet->row($sheet->getHighestRow(), function ($row) {
					// 			// 	$row->setFontWeight('bold');
					// 			// });	
					// 		});
					// })->export('xlsx');

					// return back();

					
						$sheet->appendRow(array(
							               '#',
											trans('glob.bill_no'),
											trans('glob.mobile'),
											trans('glob.date_of_birth'),
											trans('billing.bill_date'),
											trans('glob.city'),
											trans('glob.salesman'),
											trans('glob.prefered_mode'),
											trans('glob.prefered_language'),
											trans('glob.brand_name'),
											trans('item.refrence_number'),
											trans('billing.item_name'),
											trans('glob.value'),
						)); /* column names */
					
					/* getting last row number (the one we already filled and setting it to bold */
						$sheet->row($sheet->getHighestRow(), function ($row) {
							$row->setFontWeight('bold');
						});
						if(sizeof($purchases)>0){
							
								foreach ($purchases as $p) {
									$sheet->appendRow( array(
										'#'=>$p->name,
										trans('glob.bill_no')=>'' ,
										trans('glob.mobile')=>'',
										trans('glob.date_of_birth')=>'',
										trans('billing.bill_date')=>'',
										trans('glob.city')=>'',
										trans('glob.salesman')=>'',
										trans('glob.prefered_mode')=>'',
										trans('glob.prefered_language')=>'',
										trans('glob.brand_name')=>'',
										trans('item.refrence_number')=>'',
										trans('billing.item_name')=>'',
										trans('glob.value')=>'',
									)
								);									
									$i=0;
									foreach ($p->child as $o) {
										$i++;		
										
										$sheet->appendRow( array(
											"#"  => $i,
											trans('glob.bill_no') => " ".Common::fixBill($o->bill_id),
												trans('glob.mobile') => $o->mobile,
												trans('glob.date_of_birth') => $o->birthday,
												trans('billing.bill_date') => $o->bill_date,
												trans('glob.city') => $o->city,
												trans('glob.salesman') => $o->salesman,
												trans('glob.prefered_mode') => $o->prefered_mode,
												trans('glob.prefered_language') => $o->prefered_language,
												trans('glob.brand_name')=>$o->brand_name,
									            trans('item.refrence_number')=>$o->reference_number,
												trans('billing.item_name') => $o->item_name,
												trans('glob.value')=>" ".Common::fixIt($o->value),
										)
									);
									}
									$sheet->appendRow( array(
										'#'=>'',
										trans('glob.bill_no')=> trans('glob.totalTransaction').'='.$p->totalTransaction,
										trans('glob.mobile')=>'',
										trans('glob.date_of_birth')=>'',
										trans('billing.bill_date')=>'',
										trans('glob.city')=>'',
										trans('glob.salesman')=>'',
										trans('glob.prefered_mode')=>'',
										trans('glob.prefered_language')=>'',
										trans('glob.brand_name')=>'',
										trans('item.refrence_number')=>'',
										trans('billing.item_name')=>'',
										trans('glob.value')=>'',
									)
								);
								$sheet->row($sheet->getHighestRow(), function ($row) {
									$row->setFontWeight('bold');
								});
								}

						}
						
					$sheet->appendRow( array(
						'#'=>'',
						trans('glob.bill_no')=>'',
						trans('glob.mobile')=>'',
						trans('glob.date_of_birth')=>'',
						trans('billing.bill_date')=>'',
						trans('glob.city')=>'',
						trans('glob.salesman')=>'',
						trans('glob.prefered_mode')=>'',
						trans('glob.prefered_language')=>'',
						trans('glob.brand_name')=>'',
						trans('item.refrence_number')=>'',
						trans('billing.item_name')=>'',
						trans('glob.value')=>'',
					)
				);
							$sheet->appendRow( array(
								'#'=>trans('glob.totalTransaction')."=".Common::fixQty($totalTransaction) ,
								trans('glob.bill_no')=>'',
								trans('glob.mobile')=>'',
								trans('glob.date_of_birth')=>'',
								trans('billing.bill_date')=>'',
								trans('glob.city')=>'',
								trans('glob.salesman')=>'',
								trans('glob.prefered_mode')=>'',
								trans('glob.prefered_language')=>'',
								trans('glob.brand_name')=>'',
								trans('item.refrence_number')=>'',
								trans('billing.item_name')=>'',
								trans('glob.value')=>'',
							)
						);
						$sheet->row($sheet->getHighestRow(), function ($row) {
							$row->setFontWeight('bold');
						});
						$sheet->appendRow( array(
							'#'=> trans('glob.totalCustomer')."=".Common::fixQty($totalCustomer) ,
							trans('glob.bill_no')=>'',
							trans('glob.mobile')=>'',
							trans('glob.date_of_birth')=>'',
							trans('billing.bill_date')=>'',
							trans('glob.city')=>'',
							trans('glob.salesman')=>'',
							trans('glob.prefered_mode')=>'',
							trans('glob.prefered_language')=>'',
							trans('glob.brand_name')=>'',
							trans('item.refrence_number')=>'',
							trans('billing.item_name')=>'',
							trans('glob.value')=>'',
						)
					);
					$sheet->row($sheet->getHighestRow(), function ($row) {
						$row->setFontWeight('bold');
					});
					$sheet->appendRow( array(
						'#'=> trans('glob.totalItemQty')."=".Common::fixQty($totalQty) ,
						trans('glob.bill_no')=>'',
						trans('glob.mobile')=>'',
						trans('glob.date_of_birth')=>'',
						trans('billing.bill_date')=>'',
						trans('glob.city')=>'',
						trans('glob.salesman')=>'',
						trans('glob.prefered_mode')=>'',
						trans('glob.prefered_language')=>'',
						trans('glob.brand_name')=>'',
						trans('item.refrence_number')=>'',
						trans('billing.item_name')=>'',
						trans('glob.value')=>'',
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
						Log::error("SalesReportController@exportToCRMReport ".$e);
						return View("errors.exception");
					}			
		
}
public function getSalesSummaryQuery($fromDate,$toDate){
	try{
		$total 				=	0;
		$totalCash 			=	0;
		$totalCard 			=	0;
		$itemList 			=	[];	
		$pay_mode 			=	'';
		$totalDiscount 		=	0;
		$totalLoyDis 		=	0;
		$totalVat 			=	0;
		
		$purchases 			=   DB::select("SELECT *,DATE_FORMAT(created_at,'".$this->rDateFormatSQL." %h:%i %p')as dateTime, 
			(((subtotal)-((subtotal * dis_amt)/(subtotal + v_amt)))) as subtotal,
			(((v_amt)-((v_amt * dis_amt)/(subtotal + v_amt)) )) as v_amt,
			(select customers.name from customers where customers.id = billings.customer_id) as customerName,
			(select currencies.code from currencies where currencies.id = billings.currency) as currencyCode, 
			(case when billings.currency > 0 then (select currencies.amount from currencies where currencies.id = billings.currency) * billings.cash_amt else billings.cash_amt end)as cash_amt,
			(case when billings.currency > 0 then (select currencies.amount from currencies where currencies.id = billings.currency) * billings.card_amt else billings.card_amt end)as card_amt
			FROM billings 
			where billings.billing_status <> 'hold' AND
			CAST(created_at AS DATE) BETWEEN '".$fromDate."' and '".$toDate."'  ORDER BY created_at ASC");
		if(sizeof($purchases)>0){
			foreach ($purchases as $o) {
				if($o->payment_mode == 1){
					$pay_mode = 'Cash';
				}else if($o->payment_mode == 2){
					$pay_mode = 'Card & Cash';
				}else if($o->payment_mode == 3){
					$pay_mode = 'Credit';
				}else if($o->payment_mode == 4){
					$pay_mode = 'Credit Card';
				}else if($o->payment_mode == 5){
					$pay_mode = 'Dubai Mall Online';
				}
				if($o->customerName == null){
					$custName="NA";
				}else{
					$custName=$o->customerName;
				}
				array_push($itemList, array(
					"id"  => Common::fixBill($o->id),
					"dateTime" => $o->dateTime,
					"customerName" => $custName,
					"subtotal" => Common::fixIt($o->subtotal),
					"vat_amt" => Common::fixIt($o->v_amt),
					"dis_amt" => Common::fixIt($o->dis_amt),
					"loydis_amt" => Common::fixIt($o->loyalty_discount),
					"payment_mode" => $pay_mode,
					"cash_amt" => Common::fixIt($o->cash_amt),
					"card_amt" =>Common::fixIt($o->card_amt),
				)
			);
				$totalCash =Common::fixIt($o->cash_amt) +$totalCash ;
				$totalCard = Common::fixIt($o->card_amt) +$totalCard ;
				$totalDiscount = Common::fixIt($o->dis_amt) + $totalDiscount;
				$totalLoyDis =  Common::fixIt($o->loyalty_discount) + $totalLoyDis;
				$total = Common::fixIt($o->subtotal) + $total;
				$totalVat = Common::fixIt($o->v_amt) + $totalVat;
			}
		}
		return array(
			'total' 			=>	$total,	
			'totalCash' 		=>	$totalCash,	
			'totalCard' 		=>	$totalCard,	
			'itemList' 			=>	$itemList,	
			'totalDiscount' 	=>	$totalDiscount,	
			'totalLoyDis' 		=>	$totalLoyDis,	
			'totalVat' 			=>	$totalVat,	
			'purchases'			=>	$purchases,
		);

	}catch(Exception $e){
		Log::error("SalesReportController@getSalesSummaryQuery ".$e);
		return View("errors.exception");
	}
}

/**
	 * Display a listing of the resource. Used for Sales_Return_Report
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
public function indexSalesReturn(Request $request){
	try{
		$date = Carbon::now()->format('m/d/Y');
		$date1 = Carbon::now()->format('m/d/Y');
		$mallDetails = $this->mallDetails();
		$res = array(
			"date" => $date,
			"date1" => $date1,
			"mallDetails" => $mallDetails,
		);
		if($request->ajax()){
			return Response($res,200);
		}else{
			return View('pages.sales_reports.sales_return.sales_return', $res);
		}

	}catch(Exception $e){
		Log::error("SalesReportController@indexSalesReturn ".$e);
		return View("errors.exception");
	}
}

/**
	 * Select data which is displayed on report view table . used for Sales_Return_Report
	 * @param  REquest $request [description]
	 * @return [type]           [description]
	 */
public function getLedgerSalesReturn(Request $request){	
	try{	
		$dateArray 				= 	$request->all()["data"]; 
		$fromDate 				= 	new Carbon($dateArray['fromDate']);
		$toDate 				= 	new Carbon($dateArray['toDate']);		
		$total 					=	0;
		$totalCash 				=	0;
		$totalCard 				=	0;
		$itemList 				= 	[];	
		$totalDiscount 			=	0;
		$totalLoyaltyDiscount 	=	0;
		$totalVatAmt 			= 	0;
		$data 					= 	$this->getSalesreturnQuery($fromDate,$toDate);
		$labels 				= 	$this->getLabels();	
		$itemList 				= 	$data['itemList'];
		$totalCash 				=  	$data['totalCash'];
		$totalCard 				= 	$data['totalCard'];
		$totalDiscount 			= 	$data['totalDiscount'];
		$totalLoyaltyDiscount 	= 	$data['totalLoyaltyDiscount'];
		$total 					= 	$data['total'];
		$totalVatAmt	 		= 	$data['totalVatAmt'];
		$purchases 				= 	$data['purchases'];			
		$res = array(
			"total" 				=> 	Common::fixIt($total),
			"itemList"				=> 	$itemList,
			"purchases" 			=> 	$purchases,
			"totalCash" 			=> 	Common::fixIt($totalCash),
			"totalCard" 			=> 	Common::fixIt($totalCard),	
			"totalDiscount" 		=> 	Common::fixIt($totalDiscount),
			"totalLoyaltyDiscount" 	=> 	Common::fixIt($totalLoyaltyDiscount),
			"floatTotalVatAmt" 		=> 	Common::fixIt($totalVatAmt),
			"labels" 				=> 	$labels,	
			"toDatePrint" 			=> 	(new Carbon($toDate))->format($this->rDateFormat),
			"fromDatePrint" 		=> 	(new Carbon($fromDate))->format($this->rDateFormat),	
		);
		if($request->ajax()){
			return Response($res,200);
		}else{
			return View("pages.sales_reports.sales_return.sales_return",$res);
		}
	}catch(Exception $e){
		Log::error("SalesReportController@getLedgerSalesReturn ".$e);
		return View("errors.exception");
	}
}

	/**
	 * Function created for create and export exel and pdf file . used for Sales_Return_Report
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function exportToSalesReturn(Request $request){	
		try{			
			$dateArray 				= 	$request->all(); 
			$fromDate 				= 	new Carbon($dateArray['from']);
			$toDate 				= 	new Carbon($dateArray['to']);
			$total 					=	0;
			$totalCash 				=	0;
			$totalCard 				=	0;
			$itemList 				= 	[];
			$totalDiscount 			=	0;
			$totalLoyaltyDiscount 	=	0;
			$totalVatAmt 			= 	0;
			$currency 				=	$this->optioncurr;
			$data 					= 	$this->getSalesreturnQuery($fromDate,$toDate);
			$itemList 				= 	$data['itemList'];
			$totalCash 				=  	$data['totalCash'];
			$totalCard 				= 	$data['totalCard'];
			$totalDiscount 			= 	$data['totalDiscount'];
			$totalLoyaltyDiscount 	= 	$data['totalLoyaltyDiscount'];
			$total 					= 	$data['total'];
			$totalVatAmt	 		= 	$data['totalVatAmt'];
			$purchases 				= 	$data['purchases'];
			if(Input::get('pdf')){	
				$pdf = PDF::loadView('pages.sales_reports.sales_return.sales_return_pdf', [
					"total" 				=> 	Common::fixIt($total),
					"itemList"				=> 	$itemList,
					"purchases" 			=> 	$purchases,
					"totalCash" 			=> 	Common::fixIt($totalCash),
					"totalCard" 			=> 	Common::fixIt($totalCard),	
					"totalDiscount" 		=> 	Common::fixIt($totalDiscount),
					"totalLoyaltyDiscount" 	=> 	Common::fixIt($totalLoyaltyDiscount),
					"floatTotalVatAmt" 		=> 	Common::fixIt($totalVatAmt),
					"fromDate" 				=> 	(new Carbon($fromDate))->format($this->rDateFormat),
					"toDate" 				=> 	(new Carbon($toDate))->format($this->rDateFormat),"i" =>'1',
					"curr" 					=>	$currency,
					'intVatType' 			=> 	$this->intVatType,
				]); 
				$pdf->setPaper('a4');
				$carbon = Carbon::today();
				$timestamp = $carbon->timestamp;
				$format = $carbon->format($this->rDateFormat);
				$exportName = 'SalesReturn-'.$format.'.pdf';     
				return $pdf->download($exportName);
			}elseif (Input::get('xls')) {
				$carbon = Carbon::today();
				$timestamp = $carbon->timestamp;
				$format = $carbon->format($this->rDateFormat);
				$exportName = 'SalesReturn-'.$format; 
				Excel::create($exportName, function ($excel) 
					use($itemList,
						$total,
						$totalCash,
						$totalCard,
						$totalDiscount,
						$totalLoyaltyDiscount,
						$fromDate,
						$toDate,
						$totalVatAmt,
						$currency) {
						$excel->sheet('SalesReturn', function ($sheet)
							use($itemList,
								$total,
								$totalCash,
								$totalCard,
								$totalDiscount,
								$totalLoyaltyDiscount,
								$fromDate,
								$toDate,
								$totalVatAmt,
								$currency) {
								$i=0;
								$sheet->appendRow(array('', ''));
								$sheet->appendRow(array(trans('glob.from'), (new Carbon($fromDate))->format($this->rDateFormat) ,trans('glob.to'),(new Carbon($toDate))->format($this->rDateFormat)));
								$sheet->row($sheet->getHighestRow(), function ($row) {
									$row->setFontWeight('bold');
								});			
								$sheet->appendRow(array('', ''));
								/* setting column names for data - you can of course set it manually*/

								if($this->intVatType != 0){
									$sheet->appendRow(array(
										'#',
										trans('glob.sale_return_date') ,
										trans('glob.sale_return_no') ,
										trans('glob.customer') ,
										trans('billing.bill_no') ,
										trans('billing.sub_total')."(".$currency.")" ,
										trans('glob.vat')."(".$currency.")" ,								
										trans('glob.discount')."(".$currency.")" ,
										trans('glob.loyalty_discount')."(".$currency.")",
										trans('billing.mode_of_pay') ,
										trans('billing.cash_amt')."(".$currency.")" ,
										trans('billing.card_amt')."(".$currency.")",
									)); /* column names*/
								}else{
									$sheet->appendRow(array(
										'#',
										trans('glob.sale_return_date') ,
										trans('glob.sale_return_no') ,
										trans('glob.customer') ,
										trans('billing.bill_no') ,
										trans('billing.sub_total')."(".$currency.")" ,								
										trans('glob.discount')."(".$currency.")" ,
										trans('glob.loyalty_discount')."(".$currency.")",
										trans('billing.mode_of_pay') ,
										trans('billing.cash_amt')."(".$currency.")" ,
										trans('billing.card_amt')."(".$currency.")",
									)); /* column names*/
								}
								


								/* getting last row number (the one we already filled and setting it to bold*/
									$sheet->row($sheet->getHighestRow(), function ($row) {
										$row->setFontWeight('bold');
									});
									if(sizeof($itemList)>0){

										if($this->intVatType != 0){
											foreach ($itemList as $o) {
												$i++;
												$sheet->appendRow( array(
													'#' => $i,
													trans('glob.sale_return_date') => $o['dateTime'],
													trans('glob.sale_return_no') =>($o['returnId']),
													trans('glob.customer') => $o['customerName'],
													trans('billing.bill_no') => ($o['id']),
													trans('billing.sub_total')."(".$currency.")" => " ".Common::fixIt($o['subtotal']),
													trans('glob.vat')."(".$currency.")" => " ".Common::fixIt($o['vat_amt']),								
													trans('glob.discount')."(".$currency.")" => " ".Common::fixIt($o['dis_amt']),
													trans('glob.loyalty_discount')."(".$currency.")" => " ".Common::fixIt($o['loyalty_discount']),
													trans('billing.mode_of_pay') => $o['payment_mode'],
													trans('billing.cash_amt')."(".$currency.")" => " ".Common::fixIt($o['cash_amt']),
													trans('billing.card_amt')."(".$currency.")" => " ".Common::fixIt($o['card_amt']),
												)
											);
											}
										}else{
											foreach ($itemList as $o) {
												$i++;
												$sheet->appendRow( array(
													'#' => $i,
													trans('glob.sale_return_date') => $o['dateTime'],
													trans('glob.sale_return_no') =>($o['returnId']),
													trans('glob.customer') => $o['customerName'],
													trans('billing.bill_no') => ($o['id']),
													trans('billing.sub_total')."(".$currency.")" => " ".Common::fixIt($o['subtotal']),								
													trans('glob.discount')."(".$currency.")" => " ".Common::fixIt($o['dis_amt']),
													trans('glob.loyalty_discount')."(".$currency.")" => " ".Common::fixIt($o['loyalty_discount']),
													trans('billing.mode_of_pay') => $o['payment_mode'],
													trans('billing.cash_amt')."(".$currency.")" => " ".Common::fixIt($o['cash_amt']),
													trans('billing.card_amt')."(".$currency.")" => " ".Common::fixIt($o['card_amt']),
												)
											);

											}

										}
									}

									if($this->intVatType != 0){
										$sheet->appendRow( array(
											'#' => '',
											trans('glob.sale_return_date') =>'',
											trans('glob.sale_return_no') => '',
											trans('glob.customer') => '',
											trans('billing.bill_no') => '',
											trans('billing.sub_total')."(".$currency.")" =>  trans('billing.sub_total')." = ".Common::fixIt($total) ,
											trans('glob.vat')." ".trans('glob.total')."(".$currency.")" => trans('glob.vat')." ".trans('glob.total')." = ".Common::fixIt($totalVatAmt) ,
											trans('glob.discount')."(".$currency.")" => trans('glob.discount')." = ".Common::fixIt($totalDiscount) ,
											trans('billing.loyalty_discount')."(".$currency.")" => trans('billing.loyalty_discount')." = ".Common::fixIt($totalLoyaltyDiscount) ,
											trans('billing.mode_of_pay') => '',
											trans('billing.cash_amt')."(".$currency.")" => trans('billing.cash_amt')." = ".Common::fixIt($totalCash) ,
											trans('billing.card_amt')."(".$currency.")" =>trans('billing.card_amt')." = ".Common::fixIt($totalCard) ,
										)
									);
									}else{
										$sheet->appendRow( array(
											'#' => '',
											trans('glob.sale_return_date') =>'',
											trans('glob.sale_return_no') => '',
											trans('glob.customer') => '',
											trans('billing.bill_no') => '',
											trans('billing.sub_total')."(".$currency.")" =>  trans('billing.sub_total')." = ".Common::fixIt($total) ,
											trans('glob.discount')."(".$currency.")" => trans('glob.discount')." = ".Common::fixIt($totalDiscount) ,
											trans('billing.loyalty_discount')."(".$currency.")" => trans('billing.loyalty_discount')." = ".Common::fixIt($totalLoyaltyDiscount) ,
											trans('billing.mode_of_pay') => '',
											trans('billing.cash_amt')."(".$currency.")" => trans('billing.cash_amt')." = ".Common::fixIt($totalCash) ,
											trans('billing.card_amt')."(".$currency.")" =>trans('billing.card_amt')." = ".Common::fixIt($totalCard) ,
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
	Log::error("SalesReportController@exportToSalesReturn ".$e);
	return View("errors.exception");
}
}


public function getSalesreturnQuery($fromDate,$toDate){
	try{
		$total 					=	0;
		$totalCash 				=	0;
		$totalCard 				=	0;
		$itemList  				=	[];	
		$pay_mode 				=	'';
		$totalDiscount 			=	0;
		$totalLoyaltyDiscount	=	0;
		$percentage 			=	0;
		$disAmt 				=	0;
		$percentageLoyalty 		=	0;
		$disLoyaltyAmt 			=	0;
		$cashAmt 				=	0;
		$cardAmt 				=	0;
		$totalReturn 			=	0;
		$billReturn 			=	[];
		$totalVatAmt 			=	0;
		$floatTotalVatAmt 		=	0;
		$purchases 				=	DB::select("SELECT bill_returns.amount,
			bill_returns.id as returnId,
			billings.*,
			DATE_FORMAT(bill_returns.created_at,'".$this->rDateFormatSQL." %h:%i %p')as dateTime,
			customers.name as customerName 
			FROM bill_returns INNER JOIN billings on bill_returns.billing_id=billings.id 
			left outer join customers on customers.id = billings.customer_id 
			where CAST(bill_returns.created_at AS DATE) BETWEEN '".$fromDate."' and '".$toDate."' 
			ORDER BY bill_returns.created_at ASC");
		if(sizeof($purchases)>0){
			foreach ($purchases as $o) {
				if($o->subtotal == null || $o->subtotal == 0){
					$percentage = 0;
					$percentageLoyalty = 0;
				}else{
					$percentage = ($o->dis_amt /($o->subtotal + $o->v_amt)) * 100;
					$percentageLoyalty = ($o->loyalty_discount /($o->subtotal + $o->v_amt)) * 100;
				}	
				$billReturn = DB::select("SELECT 

					CASE WHEN $this->intVatType != 2  THEN
					sum(bill_return_items.qty * bill_return_items.rate) 
					WHEN $this->intVatType = 2 THEN
					sum(bill_return_items.qty * ( bill_return_items.rate + (bill_return_items.rate * bill_return_items.vat)/(100 )) ) 
					END
					as rate ,

					CASE WHEN $this->intVatType != 2  THEN
					sum((bill_return_items.rate * bill_return_items.vat)/(100 + bill_return_items.vat) * bill_return_items.qty) 
					WHEN $this->intVatType = 2 THEN
					sum((bill_return_items.rate * bill_return_items.vat)/(100 ) * bill_return_items.qty) 
					END
					as vat 
					FROM bill_return_items INNER JOIN bill_returns on bill_returns.id=bill_return_items.return_id 
					where bill_returns.id = $o->returnId and 
					CAST(bill_returns.created_at AS DATE) BETWEEN '".$fromDate."' and '".$toDate."'  ORDER BY bill_returns.created_at ASC");
				if(sizeof($billReturn) > 0){
					$totalReturn =  $billReturn[0]->rate;
					$floatTotalVatAmt =  $billReturn[0]->vat;
				}			

				$disAmt = ($percentage * $totalReturn)/100;

				$disLoyaltyAmt = ($percentageLoyalty * $totalReturn)/100;
				if($o->payment_mode == 1){
					$pay_mode = 'Cash';
					$cashAmt= $o->amount;
					$cardAmt = 0;
				}else if($o->payment_mode == 2){
					$pay_mode = 'Credit & Cash';
					$cashAmt= $o->amount;
					$cardAmt = 0;
				}else if($o->payment_mode == 3){
					$pay_mode = 'Credit';
					$cashAmt= 0;
					$cardAmt = 0;
				}else if($o->payment_mode == 4){
					$pay_mode = 'Credit Card';
					$cashAmt= 0;
					$cardAmt = $o->amount;
				}else if($o->payment_mode == 5){
					$pay_mode = 'Dubai Mall Online';
					$cashAmt= 0;
					$cardAmt = 0;
				}
				if($o->customerName == null){
					$custName="NA";
				}else{
					$custName=$o->customerName;
				}
				$tempSubTotal = $o->amount - $floatTotalVatAmt + $disAmt +$disLoyaltyAmt;
				if($tempSubTotal > 0){
					$tempSubTotal = $tempSubTotal - (($tempSubTotal * $percentage)/ 100);
				}
				if($floatTotalVatAmt > 0){
					$floatTotalVatAmt = $floatTotalVatAmt - (($floatTotalVatAmt * $percentage) /100);
				}
				array_push($itemList, array(
					"returnId"  => Common::fixBill($o->returnId),
					"dateTime" => $o->dateTime,
					"customerName" => $custName,
					"id"  => Common::fixBill($o->id),
					"subtotal" => Common::fixIt($tempSubTotal),
					"vat_amt" => Common::fixIt($floatTotalVatAmt),
					"dis_amt" => Common::fixIt($disAmt),
					"loyalty_discount" => Common::fixIt($disLoyaltyAmt),
					"payment_mode" => $pay_mode,
					"cash_amt" => Common::fixIt($cashAmt),
					"card_amt" => Common::fixIt($cardAmt),
				)
			);
				$totalCash = $cashAmt +$totalCash ;
				$totalCard = $cardAmt +$totalCard ;
				$totalDiscount = $disAmt + $totalDiscount;
				$totalLoyaltyDiscount = $disLoyaltyAmt + $totalLoyaltyDiscount;
				$total = $tempSubTotal + $total;
				$totalVatAmt = ($floatTotalVatAmt + $totalVatAmt);
			}
		}
		return array(
			'itemList' 				=> 	$itemList,
			'totalCash' 			=>  	$totalCash,
			'totalCard' 			=> 	$totalCard,
			'totalDiscount' 		=> 	$totalDiscount,
			'totalLoyaltyDiscount' 	=> 	$totalLoyaltyDiscount,
			'total' 				=> 	$total,
			'totalVatAmt'	 		=> 	$totalVatAmt,
			'purchases' 			=> 	$purchases,
		);

	}catch(Exception $e){
		Log::error("SalesReportController@getSalesreturnQuery ".$e);
		return View("errors.exception");
	}
}

	/**
	 * Display a listing of the resource. Used for Itemwise_Sales_Report
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function indexItemSales(Request $request){
		try{
			$items = array();
			$items[] = (object) array('id'=>'0','itemName' => 'All','barcode' => 'All');
			$date = Carbon::now()->format('m/d/Y');
			$date1 = Carbon::now()->format('m/d/Y');
			$mallDetails = $this->mallDetails();
			$res = array(
				"items" => $items,
				"date" => $date,
				"date1"	=> $date1,
				"mallDetails" => $mallDetails,
			);
			if($request->ajax()){
				return Response($res,200);
			}else{
				return View('pages.sales_reports.item_sales.item_sales', $res);
			}

		}catch(Exception $e){
			Log::error("SalesReportController@indexItemSales ".$e);
			return View("errors.exception");
		}
	}

    /**
	 * Select data which is displayed on report view table . used for Item_Sales_Report
	 * @param  REquest $request [description]
	 * @return [type]           [description]
	 */
    public function getLedgerItemSales(REquest $request){
    	try{		
    		$dateArray = $request->all()["data"]; 
    		$fromDate = new Carbon($dateArray['fromDate']);
    		$toDate = new Carbon($dateArray['toDate']);		
    		$item_id = $dateArray['item_id'];
    		$totalRate=0;
    		$itemList = [];
    		$totalQty=0;
    		$total=0;
    		$data 			= 	$this->getItemWiseSalesReportQuery($item_id,$fromDate,$toDate);
    		$totalRate 		=	$data['totalRate'];
    		$totalQty 		=	$data['totalQty'];
    		$total 			=	$data['total'];
    		$purchases		= 	$data['purchases'];

    		$items = array();

    		$items[] = (object) array('id'=>'0','itemName' => 'All','barcode' => 'All');


    		$labels = $this->getLabels();

    		$res = array(	
    			"totalRate" =>$totalRate,
    			"items"	=> $items,
    			"purchases"	=> $purchases,	
    			"totalQty" => $totalQty,
    			"labels" => $labels,	
    			"total"	=> $total,
    			"toDatePrint" => (new Carbon($toDate))->format($this->rDateFormat),
    			"fromDatePrint" => (new Carbon($fromDate))->format($this->rDateFormat),
    		);

    		if($request->ajax()){
    			return Response($res,200);
    		}else{
    			return View("pages.sales_reports.item_sales.item_sales",$res);
    		}

    	}catch(Exception $e){
    		Log::error("SalesReportController@getLedgerItemSales ".$e);
    		return View("errors.exception");
    	}
    }

    /**
	 * Function created for create and export exel and pdf file . used for Itemwise_Sales_Report
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
    public function exportToItemSales(Request $request){	
    	try{			
    		$dateArray 		= 	$request->all(); 
    		$fromDate 		= 	new Carbon($dateArray['from']);
    		$toDate 		= 	new Carbon($dateArray['to']);
    		$item_id 		= 	$dateArray['Item'];		
    		$totalRate 		=	0;
    		$itemList 		= 	[];
    		$totalQty 		=	0;
    		$total 			=	0;
    		$currency 		=	$this->optioncurr;
    		$data 			= 	$this->getItemWiseSalesReportQuery($item_id,$fromDate,$toDate);
    		$totalRate 		=	$data['totalRate'];
    		$totalQty 		=	$data['totalQty'];
    		$total 			=	$data['total'];
    		$purchases		= 	$data['purchases'];
    		if(Input::get('pdf')){	
    			$pdf = PDF::loadView('pages.sales_reports.item_sales.item_sales_pdf', [		
    				"totalRate" 	=>	$totalRate,
    				"purchases" 	=> 	$purchases,	
    				"totalQty" 		=> 	$totalQty,
    				"total"			=> 	$total,
    				"fromDate" 		=> 	(new Carbon($fromDate))->format($this->rDateFormat) ,
    				"toDate" 		=> 	(new Carbon($toDate))->format($this->rDateFormat) ,
    				"i" 			=>	'1',
    				"curr"			=>	$currency,
    				"intVatType" 	=> 	$this->intVatType,
    			]); 
    			$pdf->setPaper('a4');
    			$carbon = Carbon::today();
    			$timestamp = $carbon->timestamp;
    			$format = $carbon->format($this->rDateFormat);
    			$exportName = 'ItemwiseSales-'.$format.'.pdf';     
    			return $pdf->download($exportName);
    		}elseif (Input::get('xls')) {
    			$carbon = Carbon::today();
    			$timestamp = $carbon->timestamp;
    			$format = $carbon->format($this->rDateFormat);
    			$exportName = 'ItemwiseSales-'.$format; 
    			Excel::create($exportName, function ($excel) 
    				use($purchases,
    					$total,
    					$totalRate,
    					$totalQty,
    					$fromDate,
    					$toDate,
    					$currency) {
    					$excel->sheet('ItemwiseSales', function ($sheet) 
    						use($purchases,
    							$total,
    							$totalRate,
    							$totalQty,
    							$fromDate,
    							$toDate,
    							$currency) {
    							$itemList = array();			
    							$sheet->appendRow(array('', ''));					
    							$sheet->appendRow(array(trans('glob.from'), (new Carbon($fromDate))->format($this->rDateFormat) ,trans('glob.to'),(new Carbon($toDate))->format($this->rDateFormat)));
    							$sheet->row($sheet->getHighestRow(), function ($row) {
    								$row->setFontWeight('bold');
    							});	
    							$sheet->appendRow(array('', ''));
    							/* setting column names for data - you can of course set it manually*/

    							if($this->intVatType!=0){
    								$sheet->appendRow(array(
    									"#",
    									trans('billing.bill_date') ,
    									trans('billing.bill_no') ,
    									trans('glob.customer') ,
    									trans('glob.rate') ,
    									trans('glob.vat').' (%)' ,
    									trans('item.qty') ,								
    									trans('billing.amt')."(".$currency.")" ,
    								)); /* column names*/
    							}else{
    								$sheet->appendRow(array(
    									"#",
    									trans('billing.bill_date') ,
    									trans('billing.bill_no') ,
    									trans('glob.customer') ,
    									trans('glob.rate') ,
    									trans('item.qty') ,								
    									trans('billing.amt')."(".$currency.")" ,
    								)); /* column names*/
    							}

    							/* getting last row number (the one we already filled and setting it to bold*/
    								$sheet->row($sheet->getHighestRow(), function ($row) {
    									$row->setFontWeight('bold');
    								});
    								if(sizeof($purchases)>0){
    									if($this->intVatType!=0){
    										foreach ($purchases as $p) {
    											$sheet->appendRow( array(
    												"#"  => $p->itemName,
    												trans('billing.bill_date')  => '',
    												trans('billing.bill_no')  => '',
    												trans('glob.customer') => '',
    												trans('glob.rate') => '',
    												trans('glob.vat').' (%)' => '',
    												trans('item.qty') => '',								
    												trans('billing.amt')."(".$currency.")" => '',
    											)
    										);
    											$i=0;
    											foreach ($p->child as $o) {
    												$i++;
    												$sheet->appendRow(array(
    													"#"  => $i,
    													trans('billing.bill_date')  => $o->dateTime,
    													trans('billing.bill_no')  => " ".Common::fixBill($o->id),
    													trans('glob.customer_name') => isset($o->customerName) ? $o->customerName : 'N/A',
    													trans('glob.rate') => " ".Common::fixIt($o->rate),
    													trans('glob.vat').' (%)' => " ".Common::fixIt($o->vat),
    													trans('item.qty') => " ".Common::fixQty($o->qty),
    													trans('billing.amt')."(".$currency.")=" => " ".Common::fixIt($o->amt),
    												)
    											);
    											}
    											$sheet->appendRow( array(
    												"#"  => '',
    												trans('billing.bill_date')  => '',
    												trans('billing.bill_no')  => '',
    												trans('glob.customer_name') => '',
    												trans('glob.rate') => '',
    												trans('glob.vat').' (%)' => '',
    												trans('item.qty') => trans('item.qty')."=".Common::fixQty($p->qty),
    												trans('billing.amt')."=" => trans('billing.amt')."=".Common::fixIt($p->amt),
    											)
    										);
    										}
    									}else{
    										foreach ($purchases as $p) {
    											$sheet->appendRow( array(
    												"#"  => $p->itemName,
    												trans('billing.bill_date')  => '',
    												trans('billing.bill_no')  => '',
    												trans('glob.customer') => '',
    												trans('glob.rate') => '',
    												trans('item.qty') => '',								
    												trans('billing.amt')."(".$currency.")" => '',
    											)
    										);
    											$i=0;
    											foreach ($p->child as $o) {
    												$i++;
    												$sheet->appendRow(array(
    													"#"  => $i,
    													trans('billing.bill_date')  => $o->dateTime,
    													trans('billing.bill_no')  => " ".Common::fixBill($o->id),
    													trans('glob.customer_name') => isset($o->customerName) ? $o->customerName : 'N/A',
    													trans('glob.rate') => " ".Common::fixIt($o->rate),
    													trans('item.qty') => " ".Common::fixQty($o->qty),
    													trans('billing.amt')."(".$currency.")=" => " ".Common::fixIt($o->amt),
    												)
    											);
    											}
    											$sheet->appendRow( array(
    												"#"  => '',
    												trans('billing.bill_date')  => '',
    												trans('billing.bill_no')  => '',
    												trans('glob.customer_name') => '',
    												trans('glob.rate') => '',
    												trans('item.qty') => trans('item.qty')."=".Common::fixQty($p->qty),
    												trans('billing.amt')."=" => trans('billing.amt')."=".Common::fixIt($p->amt),
    											)
    										);
    										}

    									}

    								}	
    								if($this->intVatType!=0){
    									$sheet->appendRow(array(
    										"#"  => '',
    										trans('billing.bill_date')  => '',
    										trans('billing.bill_no') => '',
    										trans('glob.customer_name') => '',
    										trans('glob.rate') => '',
    										trans('glob.vat').' (%)' => trans('glob.total'),
    										trans('item.qty') => " ".Common::fixQty($totalQty),						
    										trans('billing.amt')."(".$currency.")" => " ".Common::fixIt($total),	
    									)
    								);
    								}else{
    									$sheet->appendRow(array(
    										"#"  => '',
    										trans('billing.bill_date')  => '',
    										trans('billing.bill_no') => '',
    										trans('glob.customer_name') => '',
    										trans('glob.rate') => trans('glob.total'),
    										trans('item.qty') => " ".Common::fixQty($totalQty),						
    										trans('billing.amt')."(".$currency.")" => " ".Common::fixIt($total),	
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
	Log::error("SalesReportController@exportToItemSales ".$e);
	return View("errors.exception");
}
}

public function getItemWiseSalesReportQuery($item_id,$fromDate,$toDate){
	try{
		$item_id = $item_id ?? 0;
		$totalRate=0;
		$totalQty=0;
		$total=0;
		$purchases=DB::select("SELECT items.id,items.itemName,sum(billing_items.qty) as qty,
			sum(billing_items.rate) as rate ,
			CASE when $this->intVatType!=2 THEN
			sum(billing_items.qty *billing_items.rate) 
			when $this->intVatType=2 THEN
			sum(billing_items.qty * ((billing_items.rate)+(billing_items.rate * billing_items.vat/100))  ) 
			END as amt, 
			avg(billing_items.vat) as vat
			FROM billing_items INNER JOIN billings ON billing_items.billing_id = billings.id 
			INNER JOIN items ON billing_items.item_id=items.id 
			WHERE billings.billing_status <> 'hold' AND
			CASE WHEN $item_id > 0 THEN billing_items.item_id = $item_id ELSE true END and
			CAST(billings.created_at AS DATE) BETWEEN '".$fromDate."' and '".$toDate."' 
			GROUP BY billing_items.item_id");
		foreach ($purchases as $row) {		 	
			$p=DB::select("SELECT DATE_FORMAT(billings.created_at,'".$this->rDateFormatSQL." %h:%i %p')as dateTime,
				billings.id,billing_items.qty,billing_items.rate, 
				CASE WHEN $this->intVatType!=2 THEN
				(billing_items.qty * billing_items.rate) 
				WHEN $this->intVatType=2 THEN
				(billing_items.qty * ((billing_items.rate)+(billing_items.rate * billing_items.vat/100))  )
				END as amt,
				customers.name AS customerName,billing_items.vat 
				FROM billing_items INNER JOIN billings ON billing_items.billing_id = billings.id
				LEFT OUTER JOIN customers on customers.id=billings.customer_id 
				WHERE billings.billing_status <> 'hold' AND
				billing_items.item_id = $row->id and 
				CAST(billings.created_at AS DATE) BETWEEN '".$fromDate."' and '".$toDate."' 
				ORDER BY billings.created_at ASC");
			$row->child = $p;
			$totalRate =$row->rate + $totalRate;
			$totalQty = $row->qty + $totalQty;
			$total = $row->amt + $total;
		}
		return array(
			'totalRate'	=>	$totalRate,
			'totalQty'	=>	$totalQty,
			'total'		=>	$total,
			'purchases' =>	$purchases,
		);

	}catch(Exception $e){
		Log::error("SalesReportController@getItemWiseSalesReportQuery ".$e);
		return View("errors.exception");
	}
}

    /**
	 * Display a listing of the resource. Used for Quantitywise_Sales_Report
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
    public function indexQuantitySales(Request $request){
    	try{
    		$date = Carbon::now()->format('m/d/Y');
    		$date1 = Carbon::now()->format('m/d/Y');
    		$mallDetails = $this->mallDetails();
    		$res = array(
    			"date" => $date,
    			"date1" => $date1,
    			"mallDetails" => $mallDetails,
    		);
    		if($request->ajax()){
    			return Response($res,200);
    		}else{
    			return View('pages.sales_reports.quantity_sales.quantity_sales', $res);
    		}

    	}catch(Exception $e){
    		Log::error("SalesReportController@indexQuantitySales ".$e);
    		return View("errors.exception");
    	}
    }

    /**
	 * Select data which is displayed on report view table . used for Quantitywise_Sales_Report
	 * @param  REquest $request [description]
	 * @return [type]           [description]
	 */
    public function getLedgerQuantitySales(Request $request){	
    	try{	
    		$dateArray 		= 	$request->all()["data"]; 
    		$fromDate 		= 	(new Carbon ($dateArray['fromDate']));
    		$toDate 		= 	(new Carbon ($dateArray['toDate']));		
    		$total 			=	0;
    		$itemList 		= 	[];	
    		$totalDiscount 	=	0;
    		$totalLoyDis 	=	0;
    		$totalQty 		=	0;
    		$totalRate 		=	0;
    		$qtyPercentage 	= 	0;
    		$loydisPercent 	= 	0;
    		$loyAmt 		= 	0;
    		$data 			= 	$this->getQuantitySalesQuery($fromDate,$toDate);
    		$labels 		= 	$this->getLabels();
    		$total 		 	=	$data['total'];   
    		$itemList  		=	$data['itemList'];     	
    		$totalDiscount  =	$data['totalDiscount'];   
    		$totalQty  		=	$data['totalQty'];   
    		$totalRate  	=	$data['totalRate'];   
    		$totalLoyDis  	=	$data['totalLoyDis'];   
    		$purchases  	=	$data['purchases'];	
    		$res = array(
    			"total" 		=>	Common::fixIt($total),
    			"itemList"		=> 	$itemList,
    			"totalRate" 	=> 	Common::fixIt($totalRate),
    			"totalQty" 		=> 	Common::fixQty($totalQty),
    			"totalDiscount" => 	Common::fixIt($totalDiscount),
    			"totalLoyDis" 	=> 	Common::fixIt($totalLoyDis),
    			"labels" 		=> 	$labels,
    			"toDatePrint" 	=> 	(new Carbon($toDate))->format($this->rDateFormat),
    			"fromDatePrint" => 	(new Carbon($fromDate))->format($this->rDateFormat),	
    		);
    		if($request->ajax()){
    			return Response($res,200);
    		}else{
    			return View("pages.sales_reports.quantity_sales.quantity_sales",$res);
    		}

    	}catch(Exception $e){
    		Log::error("SalesReportController@getLedgerQuantitySales ".$e);
    		return View("errors.exception");
    	}
    }

	/**
	 * Function created for create and export exel and pdf file . used for Quantitywise_Sales_Report
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function exportToQuantitySales(Request $request){	
		try{			
			$dateArray 		= 	$request->all(); 
			$fromDate 		= 	(new Carbon ($dateArray['from']));
			$toDate 		= 	(new Carbon ($dateArray['to']));	
			$total 			=	0;
			$itemList 		= 	[];	
			$totalDiscount 	=	0;
			$totalQty 		=	0;
			$totalRate 		=	0;
			$qtyPercentage 	= 	0;
			$totalLoyDis 	=	0;
			$loydisPercent 	= 	0;
			$loyAmt 		= 	0;
			$currency 		=	$this->optioncurr;
			$data 			= 	$this->getQuantitySalesQuery($fromDate,$toDate);
			$total 		 	=	$data['total'];   
			$itemList  		=	$data['itemList'];     	
			$totalDiscount  =	$data['totalDiscount'];   
			$totalQty  		=	$data['totalQty'];   
			$totalRate  	=	$data['totalRate'];   
			$totalLoyDis  	=	$data['totalLoyDis'];   
			$purchases  	=	$data['purchases'];
			if(Input::get('pdf')){	
				$pdf = PDF::loadView('pages.sales_reports.quantity_sales.quantity_sales_pdf', [
					"total" 		=>	Common::fixIt($total),
					"itemList"		=> 	$itemList,
					"totalRate" 	=> 	Common::fixIt($totalRate),
					"totalQty" 		=> 	Common::fixQty($totalQty),
					"totalDiscount" => 	Common::fixIt($totalDiscount),
					"totalLoyDis" 	=> 	Common::fixIt($totalLoyDis),
					"fromDate" 		=> 	(new Carbon($fromDate))->format($this->rDateFormat),
					"toDate" 		=> 	(new Carbon($toDate))->format($this->rDateFormat),"i" =>'1',
					"curr"			=>	$currency,
					"intVatType" 	=> 	$this->intVatType,
				]); 
				$pdf->setPaper('a4');
				$carbon = Carbon::today();
				$timestamp = $carbon->timestamp;
				$format = $carbon->format($this->rDateFormat);
				$exportName = 'QuantitywiseSales-'.$format.'.pdf';     
				return $pdf->download($exportName);
			}elseif (Input::get('xls')) {
				$carbon = Carbon::today();
				$timestamp = $carbon->timestamp;
				$format = $carbon->format($this->rDateFormat);
				$exportName = 'QuantitywiseSales-'.$format; 
				Excel::create($exportName, function ($excel) 
					use($itemList,
						$fromDate,
						$toDate,
						$qtyPercentage,
						$totalRate,
						$totalQty,
						$totalDiscount,
						$totalLoyDis,
						$total,
						$currency) {
						$excel->sheet('QuantitywiseSales', function ($sheet) 
							use($itemList,
								$fromDate,
								$toDate,
								$qtyPercentage,
								$totalRate,
								$totalQty,
								$totalDiscount,
								$totalLoyDis,
								$total,
								$currency) {
								$i=0;					
								$sheet->appendRow(array('', ''));
								$sheet->appendRow(array(trans('glob.from'), (new Carbon($fromDate))->format($this->rDateFormat) ,trans('glob.to'),(new Carbon($toDate))->format($this->rDateFormat)));
								$sheet->row($sheet->getHighestRow(), function ($row) {
									$row->setFontWeight('bold');
								});			
								$sheet->appendRow(array('', ''));
								/* setting column names for data - you can of course set it manually*/

								if($this->intVatType !=0){
									$sheet->appendRow(array(
										'#',
										trans('glob.category') ,
										trans('glob.barcode') ,
										trans('item.refrence_number') ,
										trans('item.item_name') ,
										trans('glob.rate') ,
										trans('item.qty') ,
										trans('glob.vat')."(%)",
										trans('glob.discount')."(".$currency.")",
										trans('glob.loyalty_discount')."(".$currency.")" ,
										trans('glob.total')."(".$currency.")" ,
										trans('glob.percentage')."(%)" ,
									)
								); /* column names*/
								}else{
									$sheet->appendRow(array(
										'#',
										trans('glob.category') ,
										trans('glob.barcode') ,
										trans('item.refrence_number') ,
										trans('item.item_name') ,
										trans('glob.rate') ,
										trans('item.qty') ,
										trans('glob.discount')."(".$currency.")",
										trans('glob.loyalty_discount')."(".$currency.")" ,
										trans('glob.total')."(".$currency.")" ,
										trans('glob.percentage')."(%)" ,
									)
								); /* column names*/
								}

								/* getting last row number (the one we already filled and setting it to bold*/
									$sheet->row($sheet->getHighestRow(), function ($row) {
										$row->setFontWeight('bold');
									});
									if(sizeof($itemList)>0){
										if($this->intVatType !=0){
											foreach ($itemList as $o) {					
												$i++;
												$sheet->appendRow(array(
													'#'  => $i,
													trans('glob.category') => $o['category'],
													trans('glob.barcode') => $o['itemCode'],
													trans('item.refrence_number') => $o['serial_number'],
													trans('item.item_name') => $o['itemName'],
													trans('glob.rate') => " ".Common::fixIt($o['rate']),
													trans('item.qty')  => " ".Common::fixQty($o['qty']),
													trans('glob.vat')."(%)" => " ".Common::fixIt($o['vat_percent']),
													trans('glob.discount')."(".$currency.")" => " ".Common::fixIt($o['disAmt']),
													trans('glob.loyalty_discount')."(".$currency.")" => " ".Common::fixIt($o['loydisAmt']),
													trans('glob.total')."(".$currency.")" => " ".Common::fixIt($o['totalAmt']),
													trans('glob.percentage')."(%)" => " ".Common::fixIt($o['percentage']),
												)
											);
											}
										}else{
											foreach ($itemList as $o) {					
												$i++;
												$sheet->appendRow(array(
													'#'  => $i,
													trans('glob.category') => $o['category'],
													trans('glob.barcode') => $o['itemCode'],
													trans('item.refrence_number') => $o['serial_number'],
													trans('item.item_name') => $o['itemName'],
													trans('glob.rate') => " ".Common::fixIt($o['rate']),
													trans('item.qty')  => " ".Common::fixQty($o['qty']),
													trans('glob.discount')."(".$currency.")" => " ".Common::fixIt($o['disAmt']),
													trans('glob.loyalty_discount')."(".$currency.")" => " ".Common::fixIt($o['loydisAmt']),
													trans('glob.total')."(".$currency.")" => " ".Common::fixIt($o['totalAmt']),
													trans('glob.percentage')."(%)" => " ".Common::fixIt($o['percentage']),
												)
											);
											}

										}

									}

									if($this->intVatType !=0){
										$sheet->appendRow(array(
											trans('glob.id') => '',
											trans('glob.category') =>'',
											trans('glob.barcode') => '',
											trans('item.refrence_number') => '',
											trans('item.item_name') => '',
											trans('glob.rate') => trans('glob.total'),
											trans('item.qty') =>  " ".Common::fixQty($totalQty),
											trans('glob.vat') => '',
											trans('glob.discount')."(".$currency.")" => " ".Common::fixIt($totalDiscount),
											trans('glob.loyalty_discount')."(".$currency.")" => " ".Common::fixIt($totalLoyDis),
											trans('glob.total')."(".$currency.")" => " ".Common::fixIt($total),
											trans('glob.percentage') => '',
										)
									);
									}else{
										$sheet->appendRow(array(
											trans('glob.id') => '',
											trans('glob.category') =>'',
											trans('glob.barcode') => '',
											trans('item.refrence_number') => '',
											trans('item.item_name') => '',
											trans('glob.rate') => trans('glob.total'),
											trans('item.qty') =>  " ".Common::fixQty($totalQty),
											trans('glob.discount')."(".$currency.")" => " ".Common::fixIt($totalDiscount),
											trans('glob.loyalty_discount')."(".$currency.")" => " ".Common::fixIt($totalLoyDis),
											trans('glob.total')."(".$currency.")" => " ".Common::fixIt($total),
											trans('glob.percentage') => '',
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
	Log::error("SalesReportController@exportToQuantitySales ".$e);
	return View("errors.exception");
}
}

public function getQuantitySalesQuery($fromDate,$toDate){
	try{
		$total=0;
		$itemList = [];	
		$totalDiscount=0;
		$totalQty=0;
		$totalRate=0;
		$qtyPercentage = 0;
		$totalLoyDis=0;
		$loydisPercent = 0;
		$loyAmt = 0;
		$intTotalAmt=0;
		$purchases = DB::select("SELECT 
			(sum(billing_items.rate*billing_items.qty)/sum(billing_items.qty)) as rate,
			sum(billing_items.qty) as qty,
			sum(billings.subtotal) as subtotal,
			CASE WHEN $this->intVatType != 2 THEN 
			sum((((billings.dis_amt/(billings.subtotal + billings.v_amt))/100) * (billing_items.rate*billing_items.qty)) * 100) 
			WHEN $this->intVatType = 2 THEN 
			sum((((billings.dis_amt/(billings.subtotal + billings.v_amt))/100) * ( ( billing_items.rate + ((billing_items.rate * billing_items.vat)/100) ) * billing_items.qty)) * 100) 
			END  as dis_amt,
			sum(billings.loyalty_discount) as loydis_amt,
			items.itemName as itemName, 
			items.barcode AS barcode,
			items.serial_number AS serial_number,
			AVG(billing_items.vat) as vat_percent,
			sum(billings.v_amt) as v_amt,
			categories.categorie AS categoryName 
			FROM billing_items INNER JOIN billings on billing_items.billing_id=billings.id INNER JOIN items on items.id = billing_items.item_id INNER JOIN categories on categories.id = items.category 
			where billings.billing_status <> 'hold' AND 
			CAST(billings.created_at AS DATE) BETWEEN '".$fromDate."' and '".$toDate."' 
			GROUP BY billing_items.item_id");
		if(sizeof($purchases)>0){
			foreach ($purchases as $o) {
				$qtyPercentage =$o->qty + $qtyPercentage;
			}
		}
		if(sizeof($purchases)>0){
			foreach ($purchases as $o) {
				if($o->subtotal == null || $o->subtotal == 0){
					$percentage = 0;
					$loydisPercent = 0;
				}else{
					$percentage = ($o->dis_amt /($o->subtotal + $o->v_amt)) * 100;
					
					$loydisPercent = ($o->loydis_amt/($o->subtotal + $o->v_amt)) * 100;
				}

				if($this->intVatType !=2){
					//$disAmt = ($percentage * $o->rate)/100;
					$disAmt = $o->dis_amt;
					$loyAmt = ($loydisPercent * $o->rate)/100;
				}else{
					//$disAmt = ($percentage * ( $o->rate + ($o->rate * $o->vat_percent)/100 ))/100;
					$disAmt = $o->dis_amt;
					$loyAmt = ($loydisPercent * ( $o->rate + ($o->rate * $o->vat_percent)/100 ))/100;
				}

				if($o->serial_number == null){
					$serial_number="NA";
				}else{
					$serial_number=$o->serial_number;
				}
				if($this->intVatType!=2){
					$intTotalAmt=((($o->qty * $o->rate) - (($disAmt)+($loyAmt * $o->qty)))) ;
				}
				else{
					$intTotalAmt=((($o->qty * $o->rate + (($o->rate * $o->vat_percent/100)*$o->qty)) - (($disAmt )+($loyAmt * $o->qty))));
				}

				array_push($itemList, array(
					"category" => $o->categoryName,
					"itemCode" => $o->barcode,
					"serial_number" => $serial_number,
					"itemName" => $o->itemName,
					"qty" => Common::fixQty($o->qty),
					"rate" => Common::fixIt($o->rate),
					"disAmt" => Common::fixIt(($disAmt )),
					"loydisAmt" => Common::fixIt(($loyAmt * $o->qty)),
					"totalAmt" => Common::fixIt($intTotalAmt),
					"percentage" => Common::fixIt((($o->qty/$qtyPercentage) * 100)),
					"vat_percent"=> Common::fixIt($o->vat_percent),
				)
			);
				$totalRate = $o->rate +$totalRate ;
				$totalQty = $o->qty +$totalQty ;
				$totalDiscount = ($disAmt ) + $totalDiscount;
				$totalLoyDis =   ($loyAmt * $o->qty) + $totalLoyDis;
				if($this->intVatType !=2){
					$total = (($o->qty * $o->rate ) - (($disAmt )+($loyAmt * $o->qty))) + $total;
				}else{
					$total = (($o->qty * ($o->rate + ($o->rate * $o->vat_percent)/100 ) ) - (($disAmt )+($loyAmt * $o->qty))) + $total;	
				}
				
			}
		}	
		return array(
			'total'		 	=>	$total,   
			'itemList' 		=>	$itemList,     	
			'totalDiscount' =>	$totalDiscount,   
			'totalQty' 		=>	$totalQty,   
			'totalRate' 	=>	$totalRate,   
			'totalLoyDis' 	=>	$totalLoyDis,   
			'purchases' 	=>	$purchases,    
		);
	}catch(Exception $e){
		Log::error("SalesReportController@getQuantitySalesQuery ".$e);
		return View("errors.exception");
	}
}

	/**
	 * Display a listing of the resource. Used for Quantitywise_Sales_3Inches_Report
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function indexQuantitySalesInches(Request $request){
		$date = Carbon::now()->format('m/d/Y');
		$date1 = Carbon::now()->format('m/d/Y');
		$mallDetails = $this->mallDetails();
		$res = array(
			"date" => $date,
			"date1" => $date1,
			"mallDetails" => $mallDetails,
		);
		if($request->ajax()){
			return Response($res,200);
		}else{
			return View('pages.sales_reports.quantity_sales_inches.quantity_sales_inches', $res);
		}
	}

	/**
	 * Select data which is displayed on report view table . used for Quantitywise_Sales_3Inches_Report
	 * @param  REquest $request [description]
	 * @return [type]           [description]
	 */
	public function getLedgerQuantitySalesInches(REquest $request){		
		$dateArray 		= 	$request->all()["data"]; 
		$fromDate 		= 	new Carbon($dateArray['fromDate']);
		$toDate 		= 	new Carbon($dateArray['toDate']);		
		$purchases 		=	$this->getQuantityThreeInchesQuery($fromDate,$toDate);
		$labels 		= 	$this->getLabels();
		$res = array(
			"purchases" 	=>	$purchases,
			"labels" 		=> 	$labels,	
			"toDatePrint" 	=> 	(new Carbon($toDate))->format($this->rDateFormat),
			"fromDatePrint" => 	(new Carbon($fromDate))->format($this->rDateFormat),
		);
		if($request->ajax()){
			return Response($res,200);
		}else{
			return View("pages.sales_reports.quantity_sales_inches.quantity_sales_inches",$res);
		}
	}

	/**
	 * Function created for create and export exel and pdf file . used for Quantitywise_Sales_3Inches_Report
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function exportToQuantitySalesInches(Request $request){				
		$dateArray 		= 	$request->all(); 
		$fromDate 		= 	new Carbon($dateArray['from']);
		$toDate 		= 	new Carbon($dateArray['to']);	
		$purchases 		=	$this->getQuantityThreeInchesQuery($fromDate,$toDate);
		if(Input::get('pdf')){	
			$pdf = PDF::loadView('pages.sales_reports.quantity_sales_inches.quantity_sales_inches_pdf',[
				"purchases" 	=>	$purchases,
				"fromDate" 		=> 	(new Carbon($fromDate))->format($this->rDateFormat),
				"toDate" 		=>	(new Carbon($toDate))->format($this->rDateFormat),
				"i" 			=>	'1'
			]); 
			$pdf->setPaper('a4');
			$carbon = Carbon::today();
			$timestamp = $carbon->timestamp;
			$format = $carbon->format($this->rDateFormat);
			$exportName = 'QuantitywiseSales-'.$format.'.pdf';     
			return $pdf->download($exportName);
		}elseif (Input::get('xls')) {
			$carbon = Carbon::today();
			$timestamp = $carbon->timestamp;
			$format = $carbon->format($this->rDateFormat);
			$exportName = 'QuantitywiseSales-'.$format; 
			Excel::create($exportName, function ($excel) 
				use($purchases,
					$fromDate,
					$toDate) {
					$excel->sheet('QuantitywiseSales', function ($sheet) 
						use($purchases,
							$fromDate,
							$toDate) {
							$i=0;
							$sheet->appendRow(array('', ''));
							$sheet->appendRow(array(trans('glob.from'), (new Carbon($fromDate))->format($this->rDateFormat) ,trans('glob.to'),(new Carbon($toDate))->format($this->rDateFormat)));
							$sheet->row($sheet->getHighestRow(), function ($row) {
								$row->setFontWeight('bold');
							});			
							$sheet->appendRow(array('', ''));
							/* setting column names for data - you can of course set it manually*/
							$sheet->appendRow(array(
								'#',
								trans('item.item_name'),
								trans('item.qty'),
							)); /* column names*/
							/* getting last row number (the one we already filled and setting it to bold*/
								$sheet->row($sheet->getHighestRow(), function ($row) {
									$row->setFontWeight('bold');
								});
								if(sizeof($purchases)>0){
									foreach ($purchases as $o) {
										$i++;
										$sheet->appendRow(array(
											'#'  => $i,
											trans('item.item_name') => $o->itemName,
											trans('item.qty') => " ".Common::fixQty($o->qty),
										)
									);
									}
								}
								$sheet->appendRow( array(
									'#' => '',
									trans('item.item_name') =>'',
									trans('item.qty') => '',
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

	public function getQuantityThreeInchesQuery($fromDate,$toDate){
		return DB::select("SELECT sum(billing_items.qty) as qty,
			items.itemName as itemName
			FROM billing_items INNER JOIN billings on billing_items.billing_id=billings.id 
			INNER JOIN items on items.id=billing_items.item_id
			where billings.billing_status <> 'hold' AND
			CAST(billings.created_at AS DATE) BETWEEN '".$fromDate."' and '".$toDate."' 
			GROUP BY billing_items.item_id");
	}

	/**
	 * Display a listing of the resource. Used for XSales_Report
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function indexXSales(Request $request){
		$date = Carbon::now()->format('m/d/Y');
		$date1 = Carbon::now()->format('m/d/Y');
		$mallDetails = $this->mallDetails();
		$res = array(
			"date" => $date,
			"date1" => $date1,
			"mallDetails" => $mallDetails,
		);
		if($request->ajax()){
			return Response($res,200);
		}else{
			return View('pages.sales_reports.x_sales.x_sales', $res);
		}
	}

	/**
	 * Select data which is displayed on report view table . used for XSales_Report
	 * @param  REquest $request [description]
	 * @return [type]           [description]
	 */
	public function getLedgerXSales(REquest $request){		
		$dateArray 		= 	$request->all()["data"]; 
		$fromDate 		= 	new Carbon($dateArray['fromDate']);
		$toDate 		= 	new Carbon($dateArray['toDate']);	
		$data 			= 	$this->getXSalsReportQuery($fromDate,$toDate);
		$labels 		= 	$this->getLabels();
		$purchases 		= 	$data['purchases'];
		$openingCash 	= 	$data['openingCash'];
		$saleReturn 	= 	$data['saleReturn'];
		$sale 			= 	$data['sale'];
		$saleCredit 	= 	$data['saleCredit'];
		$itemList 		= 	$data['itemList'];
		$res = array(
			"purchases" 	=> 	$itemList,
			"openingCash" 	=> 	Common::fixIt($openingCash[0]->openingAmt) ,	
			"saleReturn"	=> 	Common::fixIt(($saleReturn[0]->saleReturn)),
			"billAmt"		=> 	Common::fixIt($sale[0]->billAmt) ,
			"cashAmt"		=> 	Common::fixIt($sale[0]->cashAmt) ,
			"cardAmt"		=> 	Common::fixIt($sale[0]->cardAmt)  ,
			"saleCredit"	=> 	Common::fixIt($saleCredit[0]->creditAmt) ,
			"labels" 		=> 	$labels,
			"toDatePrint" 	=> 	(new Carbon($toDate))->format("{$this->rDateFormat} h:i A"),
			"fromDatePrint" => 	(new Carbon($fromDate))->format("{$this->rDateFormat} h:i A"),
		);
		if($request->ajax()){
			return Response($res,200);
		}else{
			return View("pages.sales_reports.x_sales.x_sales",$res);
		}
	}

	/**
	 * Function created for create and export exel and pdf file . used for XSales_Report
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function exportToXSales(Request $request){				
		$dateArray 		= 	$request->all(); 
		$fromDate 		= 	new Carbon($dateArray['from']);
		$toDate 		= 	new Carbon($dateArray['to']);
		$currency 		=	$this->optioncurr;
		$data 			= 	$this->getXSalsReportQuery($fromDate,$toDate);
		$purchases 		= 	$data['purchases'];
		$openingCash 	= 	$data['openingCash'];
		$saleReturn 	= 	$data['saleReturn'];
		$sale 			= 	$data['sale'];
		$saleCredit 	= 	$data['saleCredit'];
		$itemList 		= 	$data['itemList'];
		if(Input::get('pdf')){	
			$pdf = PDF::loadView('pages.sales_reports.x_sales.x_sales_pdf', [
				"purchases" 	=>	$itemList,
				"openingCash" 	=> 	Common::fixIt($openingCash[0]->openingAmt) ,	
				"saleReturn"	=> 	Common::fixIt(($saleReturn[0]->saleReturn )),
				"billAmt"		=> 	Common::fixIt($sale[0]->billAmt) ,
				"cashAmt"		=> 	Common::fixIt($sale[0]->cashAmt) ,
				"cardAmt"		=> 	Common::fixIt($sale[0]->cardAmt)  ,
				"saleCredit"	=> 	Common::fixIt($saleCredit[0]->creditAmt) ,
				"fromDate" 		=> 	(new Carbon($fromDate))->format("{$this->rDateFormat} h:i A"),
				"toDate" 		=> 	(new Carbon($toDate))->format("{$this->rDateFormat} h:i A") ,
				"i" 			=>	'1',
				"curr"			=>	$currency
			]); 
			$pdf->setPaper('a4');
			$carbon = Carbon::today();
			$timestamp = $carbon->timestamp;
			$format = $carbon->format($this->rDateFormat);
			$exportName = 'X-Sales-'.$format.'.pdf';     
			return $pdf->download($exportName);
		}elseif (Input::get('xls')) {
			$carbon = Carbon::today();
			$timestamp = $carbon->timestamp;
			$format = $carbon->format($this->rDateFormat);
			$exportName = 'X-Sales-'.$format; 
			Excel::create($exportName, function ($excel) 
				use($purchases,
					$fromDate,
					$toDate,
					$openingCash,
					$saleReturn,
					$sale,
					$saleCredit,
					$currency) {
					$excel->sheet('X-Sales', function ($sheet) 
						use($purchases,
							$fromDate,
							$toDate,
							$openingCash,
							$saleReturn,
							$sale,
							$saleCredit,
							$currency) {
							$i=0;
							$sheet->appendRow(array('', ''));
							$sheet->appendRow(array(trans('glob.from'), (new Carbon($fromDate))->format("{$this->rDateFormat} h:i A") ,trans('glob.to'),(new Carbon($toDate))->format("{$this->rDateFormat} h:i A")));
							$sheet->row($sheet->getHighestRow(), function ($row) {
								$row->setFontWeight('bold');
							});	
							$sheet->appendRow(array(trans('glob.opening_amt')." = ".Common::fixIt($openingCash[0]->openingAmt), ""));
							$sheet->row($sheet->getHighestRow(), function ($row) {
								$row->setFontWeight('bold');
							});			
							$sheet->appendRow(array('', ''));
							/* setting column names for data - you can of course set it manually*/
							$sheet->appendRow(array(
								'#',
								trans('billing.bill_date') ,
								trans('billing.bill_no') ,
								trans('billing.amt')."(".$currency.")" ,
							)); /* column names*/
							/* getting last row number (the one we already filled and setting it to bold*/
								$sheet->row($sheet->getHighestRow(), function ($row) {
									$row->setFontWeight('bold');
								});
								if(sizeof($purchases)>0){
									foreach ($purchases as $o) {
										$i++;
										$sheet->appendRow( array(
											'#'  => $i,
											trans('billing.bill_date') => $o->dateTime,
											trans('billing.bill_no') => " ".Common::fixBill($o->id),
											trans('billing.amt')."(".$currency.")" =>  " ".Common::fixIt($o->bil_amt),
										)
									);
									}
								}
								$sheet->appendRow(array(
									'#'  => '',
									trans('billing.bill_date') => '',
									trans('billing.bill_no') => '',
									trans('billing.amt')."(".$currency.")" => " ".Common::fixIt($sale[0]->billAmt),
								)
							);
								$sheet->row($sheet->getHighestRow(), function ($row) {
									$row->setFontWeight('bold');
								});
								$sheet->appendRow(array(trans('glob.opening_amt')." + ".trans('glob.sale')." (".$currency.") = ".Common::fixIt(($openingCash[0]->openingAmt+$sale[0]->billAmt)), ""));
								$sheet->row($sheet->getHighestRow(), function ($row) {
									$row->setFontWeight('bold');
								});	
								$sheet->appendRow(array(trans('billing.cash')."(".$currency.") = ".Common::fixIt($sale[0]->cashAmt), ""));
								$sheet->row($sheet->getHighestRow(), function ($row) {
									$row->setFontWeight('bold');
								});
								$sheet->appendRow(array(trans('billing.card')."(".$currency.") = ".Common::fixIt($sale[0]->cardAmt), "" ));
								$sheet->row($sheet->getHighestRow(), function ($row) {
									$row->setFontWeight('bold');
								});
								$sheet->appendRow(array(trans('billing.credit')."(".$currency.") = ".Common::fixIt($saleCredit[0]->creditAmt),"" ));
								$sheet->row($sheet->getHighestRow(), function ($row) {
									$row->setFontWeight('bold');
								});	
								$sheet->appendRow(array(trans('billing.sale_return')."(".$currency.") = ".Common::fixIt(($saleReturn[0]->saleReturn )), ""));
								$sheet->row($sheet->getHighestRow(), function ($row) {
									$row->setFontWeight('bold');
								});
								$sheet->appendRow(array(trans('billing.bal')."(".$currency.") = ".Common::fixIt(($openingCash[0]->openingAmt+$sale[0]->cashAmt+$sale[0]->cardAmt+$saleCredit[0]->creditAmt-$saleReturn[0]->saleReturn)), ""));
								$sheet->row($sheet->getHighestRow(), function ($row) {
									$row->setFontWeight('bold');
								});	
							});
				})->export('xlsx');
			return back();
		}
	}

	public function getXSalsReportQuery($fromDate,$toDate){
		$itemList = array();
		$purchases =DB::select("SELECT billings.id,DATE_FORMAT(billings.created_at,
			'".$this->rDateFormatSQL." %h:%i %p')as dateTime,billings.bil_amt 
			FROM billings
			where billings.billing_status <> 'hold' AND 
			billings.created_at  BETWEEN '".$fromDate."' and '".$toDate."' 
			ORDER BY billings.created_at ASC");
		$openingCash = DB::select("SELECT IFNULL(sum(opening_cashes.opening_amt),0) as openingAmt
			FROM opening_cashes
			where opening_cashes.created_at BETWEEN '".$fromDate."' and '".$toDate."'"); 
		$saleReturn = DB::select("SELECT IFNULL(sum(bill_returns.amount),0)as saleReturn , 
			sum((((billings.dis_amt/billings.subtotal)*100)*bill_returns.amount)/100) as dis_amt 
			FROM bill_returns INNER JOIN billings on bill_returns.billing_id=billings.id 
			where billings.billing_status <> 'hold' AND 
			bill_returns.created_at BETWEEN '".$fromDate."' and '".$toDate."'"); 
		$sale = DB::select("SELECT IFNULL(sum(billings.cash_amt),0) as cashAmt,
			IFNULL(sum(billings.card_amt),0)as cardAmt,
			IFNULL(sum(billings.bil_amt),0)as billAmt
			FROM billings
			where billings.billing_status <> 'hold' AND
			billings.created_at BETWEEN '".$fromDate."' and '".$toDate."'");
		$saleCredit = DB::select("SELECT IFNULL(sum(billings.bil_amt),0)as creditAmt 
			FROM billings 
			where billings.billing_status <> 'hold' AND 
			billings.payment_mode = 3 and 
			billings.created_at BETWEEN '".$fromDate."' and '".$toDate."'");
		if(sizeof($purchases)>0){
			foreach ($purchases as $o) {							
				array_push($itemList, array(								
					"dateTime" => $o->dateTime,
					"id" => Common::fixBill($o->id),
					"bil_amt" =>  Common::fixIt($o->bil_amt),
				)
			);

			}
		}
		return array(
			'purchases'	 	=> 	$purchases,
			'itemList' 		=> 	$itemList,
			'openingCash' 	=> 	$openingCash,
			'saleReturn' 	=> 	$saleReturn,
			'sale' 			=> 	$sale,
			'saleCredit' 	=> 	$saleCredit
		);
	}

	/**
	 * Display a listing of the resource. Used for Promotion_Report
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function indexPromotion(Request $request){
		$promotions = array();		
		$promotions[] = (object) array('id'=>'0','name' => 'All');
		$promotions[] = (object) array('id'=>'1','name' => 'Package Promotion');
		$promotions[] = (object) array('id'=>'2','name' => 'XY Promotion');
		$promotions[] = (object) array('id'=>'3','name' => 'Price Promotion');
		$date = Carbon::now()->format('m/d/Y');
		$date1 = Carbon::now()->format('m/d/Y');
		$mallDetails = $this->mallDetails();
		$res = array(
			"date" => $date,
			"date1" => $date1,
			"promotions" => $promotions,
			"mallDetails" => $mallDetails,
		);
		if($request->ajax()){
			return Response($res,200);
		}else{
			return View('pages.sales_reports.promotion.promotion', $res);
		}
	}

	/**
	 * Select data which is displayed on report view table . used for Promotion_Report
	 * @param  REquest $request [description]
	 * @return [type]           [description]
	 */
	public function getLedgerPromotion(REquest $request){		
		$dateArray 			= 	$request->all()["data"]; 
		$fromDate 			= 	new Carbon($dateArray['fromDate']);
		$toDate 			= 	new Carbon($dateArray['toDate']);		
		$promotion_id 		= 	$dateArray['promotion_id'];
		$total 				=	0;
		$totalQty 			=	0;
		$offerTotal 		=	0;
		$offerTotalQty 		=	0;
		$promotions 		=	[];
		$promotions_temp 	=	[];
		$data 			= 	$this->getPromotionreportQuery($fromDate,$toDate,$promotion_id);
		$labels 			= 	$this->getLabels();
		$total 				= 	$data['total'];
		$totalQty 			= 	$data['totalQty'];
		$offerTotal 		= 	$data['offerTotal'];
		$offerTotalQty 		= 	$data['offerTotalQty'];
		$promotions_temp 	= 	$data['promotions_temp'];
		$res = array(	
			"total" 		=>	$total,
			"totalQty" 		=> 	$totalQty,	
			"offerTotal" 	=> 	$offerTotal,
			"offerTotalQty" => 	$offerTotalQty,	
			"promotions"	=> 	$promotions_temp,	
			"labels"		=> 	$labels,
			"toDatePrint" 	=> 	(new Carbon($toDate))->format($this->rDateFormat),
			"fromDatePrint" => 	(new Carbon($fromDate))->format($this->rDateFormat),
		);

		if($request->ajax()){
			return Response($res,200);
		}else{
			return View("pages.sales_reports.promotion.promotion",$res);
		}
	}

	/**
	 * Function created for create and export exel and pdf file . used for Promotion_Report
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function exportToPromotion(Request $request){				
		$dateArray 				= 	$request->all(); 
		$fromDate 				= 	new Carbon($dateArray['from']);
		$toDate 				= 	new Carbon($dateArray['to']);
		$promotion_id 			= 	$dateArray['promotion'];
		$total 					=	0;
		$totalQty 				=	0;
		$offerTotal 			=	0;
		$offerTotalQty 			=	0;
		$promotions 			=	[];
		$promotions_temp 		=	[];
		$currency 				=	$this->optioncurr;
		$data  					= 	$this->getPromotionreportQuery($fromDate,$toDate,$promotion_id);
		$fromDate 				= 	date($this->rDateFormat, strtotime($fromDate));
		$toDate 				= 	date($this->rDateFormat, strtotime($toDate));
		$total 					= 	$data['total'];
		$totalQty 				= 	$data['totalQty'];
		$offerTotal 			= 	$data['offerTotal'];
		$offerTotalQty 			= 	$data['offerTotalQty'];
		$promotions_temp 		= 	$data['promotions_temp'];
		if(Input::get('pdf')){	
			$pdf = PDF::loadView('pages.sales_reports.promotion.promotion_pdf', [
				"purchases" 		=> 	$promotions_temp ,
				"total" 			=> 	$total,
				"totalQty" 			=> 	$totalQty,
				"offerTotal" 		=> 	$offerTotal,
				"offerTotalQty" 	=> 	$offerTotalQty,
				"fromDate" 			=> 	$fromDate,
				"toDate" 			=> 	$toDate,
				"i" 				=>	'1',
				"curr" 				=>	$currency
			]); 
			$pdf->setPaper('a4');
			$carbon = Carbon::today();
			$timestamp = $carbon->timestamp;
			$format = $carbon->format($this->rDateFormat);
			$exportName = 'PromotionReport-'.$format.'.pdf';     
			return $pdf->download($exportName);
		}elseif (Input::get('xls')) {
			$carbon = Carbon::today();
			$timestamp = $carbon->timestamp;
			$format = $carbon->format($this->rDateFormat);
			$exportName = 'PromotionReport-'.$format; 
			Excel::create($exportName, function ($excel)
				use($promotions_temp,
					$total,
					$totalQty,
					$fromDate,
					$toDate,
					$offerTotal,
					$offerTotalQty,
					$currency) {
					$excel->sheet('PromotionReport', function ($sheet) 
						use($promotions_temp,
							$total,
							$totalQty,
							$fromDate,
							$toDate,
							$offerTotal,
							$offerTotalQty,
							$currency) {	
							$sheet->appendRow(array('', ''));
							$sheet->appendRow(array(trans('glob.from'), $fromDate ,trans('glob.to'),$toDate));
							$sheet->row($sheet->getHighestRow(), function ($row) {
								$row->setFontWeight('bold');
							});	
							$sheet->appendRow(array('', ''));
							/* setting column names for data - you can of course set it manually*/
							$sheet->appendRow(array(
								"#" ,	
								trans('billing.bill_no') ,							
								trans('billing.bill_date'),
								trans('item.item_name') ,			
								trans('item.qty') ,
								trans('glob.rate')."(".$currency.")" ,
								trans('glob.offer_item') ,
								trans('glob.offer_qty') ,	
								trans('glob.promo_rate')."(".$currency.")" ,		
							)); /* column names*/
							/* getting last row number (the one we already filled and setting it to bold*/
								$sheet->row($sheet->getHighestRow(), function ($row) {
									$row->setFontWeight('bold');
								});
								foreach($promotions_temp as $p){							
									$sheet->appendRow( array(
										"#"  => $p->name,	
										trans('billing.bill_no') => '',							
										trans('billing.bill_date') => '',
										trans('item.item_name') => '',			
										trans('item.qty') => '',
										trans('glob.rate')."(".$currency.")" => '',
										trans('glob.offer_item') =>'',
										trans('glob.offer_qty') => '',	
										trans('glob.promo_rate')."(".$currency.")" => '',		
									)
								);
									if($p->totalQty >0 ){
										$i=0;					
										foreach ($p->child as $o) {						
											$i++;	
											$OfferQty=0;
											$returnQty=0;
											$offerReturnQty=0;
											if($o->offerItemName != null){
												$offerItemName = $o->offerItemName;
											}else{
												$offerItemName = "NA";
											}
											if($o->OfferQty != null){
												$OfferQty = $o->OfferQty;
											}else{
												$OfferQty = "NA";
											}
											if($o->returnQty != null)
												{$returnQty = $o->returnQty;}
											if($o->offerReturnQty != null)
												{$offerReturnQty=$o->offerReturnQty;}

											if($o->OfferQty == null){
												$OfferQty = "0";
											}else{
												$OfferQty = $o->OfferQty;
											}
											if($OfferQty != 0){
												$OfferQty = $OfferQty - $offerReturnQty;
											}
											if(($o->qty - $returnQty) > 0) {
												$sheet->appendRow( array(
													"#"  => $i,	
													trans('billing.bill_no') => " ".Common::fixBill($o->id),							
													trans('billing.bill_date') => $o->dateTime,
													trans('item.item_name') => $o->ItemName,			
													trans('item.qty') => " ".Common::fixQty((($o->qty) - $returnQty)),
													trans('glob.rate')."(".$currency.")" => " ".Common::fixIt($o->originalRate),
													trans('glob.offer_item') =>$offerItemName,
													trans('glob.offer_qty') => " ".Common::fixQty($OfferQty),	
													trans('glob.promo_rate')."(".$currency.")" => " ".Common::fixIt($o->rate),	
												)
											);
											}
										}
										$sheet->appendRow( array(
											"#"  => '',	
											trans('billing.bill_no') => '',							
											trans('billing.bill_date') => '',
											trans('item.item_name') => '',			
											trans('item.qty') => trans('item.qty') ." = ".Common::fixQty($p->totalQty),
											trans('glob.rate')."(".$currency.")" => trans('glob.total') ." = ".Common::fixIt($p->total),
											trans('glob.offer_item') =>'',
											trans('glob.offer_qty') => trans('glob.offer_qty') ." = ".Common::fixQty($p->offerTotalQty),	
											trans('glob.promo_rate')."(".$currency.")" => trans('glob.total') ." = ".Common::fixIt($p->offerTotal),		
										)
									);
									}
								}
								if($totalQty > 0 ){
									$sheet->appendRow( array(
										"#"  => '',	
										trans('billing.bill_no') => '',							
										trans('billing.bill_date') => '',
										trans('item.item_name') => trans('glob.total'),			
										trans('item.qty') => " ".Common::fixQty($totalQty),
										trans('glob.rate')."(".$currency.")" => " ".Common::fixIt($total),
										trans('glob.offer_item') =>'',
										trans('glob.offer_qty') => " ".Common::fixQty($offerTotalQty),	
										trans('glob.promo_rate')."(".$currency.")" =>" ".Common::fixIt($offerTotal),		
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
}

public function getPromotionreportQuery($fromDate,$toDate,$promotion_id){
	$promotion_id 		= 	$promotion_id ?? 0;
	$total 				=	0;
	$totalQty 			=	0;
	$offerTotal 		=	0;
	$offerTotalQty 		=	0;
	$promotions 		=	[];
	$promotions_temp 	=	[];
	$purchases 			=  	DB::select("SELECT
		(CASE WHEN billing_items.is_promotion = 1 THEN (3)
		WHEN billing_items.offer_type=1 THEN (1) 
		WHEN billing_items.offer_type=2 THEN (2) 
		ELSE 0 END) as type ,

		(CASE WHEN billing_items.is_promotion = 1 THEN 
		(SELECT sum(bi.qty) FROM billing_items bi 
		WHERE bi.item_id = billing_items.item_id and
		bi.billing_id=billing_items.billing_id 
		GROUP BY billing_items.item_id)
		WHEN billing_items.offer_type in(1,2) THEN 
		(SELECT sum(bi.qty) FROM billing_items bi 
		WHERE bi.item_id = billing_items.offerwith and
		bi.billing_id=billing_items.billing_id 
		GROUP BY billing_items.item_id) 
		ELSE 0 END) as qty ,

		(CASE WHEN billing_items.is_promotion = 1 THEN 
		(SELECT SUM(bill_return_items.qty) 
		FROM bill_returns LEFT OUTER JOIN bill_return_items ON bill_returns.id=bill_return_items.return_id 
		WHERE bill_returns.billing_id=billing_items.billing_id and
		bill_return_items.item_id=billing_items.item_id 
		GROUP BY bill_return_items.id)
		WHEN billing_items.offer_type in(1,2) THEN 
		(SELECT SUM(bill_return_items.qty) 
		FROM bill_returns LEFT OUTER JOIN bill_return_items ON bill_returns.id=bill_return_items.return_id 
		WHERE bill_returns.billing_id=billing_items.billing_id and 
		bill_return_items.item_id=billing_items.offerwith 
		GROUP BY bill_return_items.item_id) 
		ELSE 0 END) as returnQty,

		(CASE WHEN billing_items.is_promotion = 1 THEN 
		(SELECT items.itemName FROM items 
		WHERE items.id=billing_items.offerwith) 
		WHEN billing_items.offer_type in(1,2) THEN 
		(SELECT items.itemName FROM items 
		WHERE items.id=billing_items.item_id)
		ELSE 0 END) as offerItemName ,

		(CASE WHEN billing_items.is_promotion = 1 THEN 
		(SELECT sum(bi.qty) FROM billing_items bi 
		WHERE bi.item_id = billing_items.offerwith and bi.billing_id=billing_items.billing_id)
		WHEN billing_items.offer_type in(1,2) THEN (billing_items.qty) 
		ELSE 0 END) as OfferQty ,				

		(CASE WHEN billing_items.is_promotion = 1 THEN 
		(SELECT SUM(bill_return_items.qty) 
		FROM bill_returns LEFT OUTER JOIN bill_return_items ON bill_returns.id=bill_return_items.return_id 
		WHERE bill_returns.billing_id=billing_items.billing_id and 
		bill_return_items.item_id=billing_items.item_id 
		GROUP BY bill_return_items.item_id)
		WHEN billing_items.offer_type in(1,2) THEN 
		(SELECT SUM(bill_return_items.qty) 
		FROM bill_returns LEFT OUTER JOIN bill_return_items ON bill_returns.id=bill_return_items.return_id 
		WHERE bill_returns.billing_id=billing_items.billing_id and 
		bill_return_items.item_id=billing_items.item_id 
		GROUP BY bill_return_items.item_id) 
		ELSE 0 END) as offerReturnQty,

		(CASE WHEN billing_items.is_promotion = 1 THEN 
		(SELECT items.salePrise FROM items WHERE items.id = billing_items.item_id) 
		WHEN billing_items.offer_type in(1,2) THEN 
		(SELECT AVG(bi.rate) FROM billing_items bi
		WHERE bi.item_id = billing_items.offerwith and 
		bi.billing_id=billing_items.billing_id 
		GROUP BY billing_items.item_id) 
		ELSE 0 END) as originalRate ,

		(CASE WHEN billing_items.is_promotion = 1 THEN 
		(SELECT items.itemName FROM items WHERE items.id=billing_items.item_id)
		WHEN billing_items.offer_type in(1,2) THEN 
		(SELECT items.itemName FROM items 
		WHERE items.id=billing_items.offerwith) 
		ELSE 0 END) as ItemName ,

		billings.id,
		DATE_FORMAT(billings.created_at,'{$this->rDateFormatSQL}  %h:%i %p')as dateTime , 
		billing_items.rate
		from billing_items INNER JOIN billings ON billing_items.billing_id = billings.id 
		where  billings.billing_status <> 'hold' AND
		CAST(billings.created_at AS DATE) BETWEEN '".$fromDate."' and '".$toDate."' and 
		CASE WHEN $promotion_id = 0 THEN (billing_items.offer_type in(1,2) or billing_items.is_promotion = 1 ) 
		WHEN  $promotion_id = 3 THEN billing_items.is_promotion = 1 
		WHEN  $promotion_id = 1 THEN billing_items.offer_type = 1 
		WHEN  $promotion_id = 2 THEN billing_items.offer_type = 2 ELSE false END ");
	$promotions[] = (object) array('id'=>'1','name' => 'Package Promotion','child' => [],'total' => '0','totalQty' => '0','offerTotal' => '0' , 'offerTotalQty' => '0');
	$promotions[] = (object) array('id'=>'2','name' => 'XY Promotion','child' => [] ,'total' => '0','totalQty' => '0','offerTotal' => '0' , 'offerTotalQty' => '0');
	$promotions[] = (object) array('id'=>'3','name' => 'Price Promotion','child' => [] ,'total' => '0','totalQty' => '0','offerTotal' => '0' , 'offerTotalQty' => '0');
	if(sizeof($purchases)>0){
		foreach ($purchases as $o) {
			if($o->type == 1){			
				$promotions[0]->child[] = $o;
			}else if($o->type == 2){
				$promotions[1]->child[] = $o;	
			}else if($o->type == 3){
				$promotions[2]->child[] = $o;	
			}
			if($o->returnQty != null){
				$qty = $o->qty - $o->returnQty;
			}else{
				$qty = $o->qty;
			}
			if($o->OfferQty != null){
				$OfferQty = $o->OfferQty - $o->offerReturnQty;
			}else{
				$OfferQty = $o->OfferQty;
			}
			if($qty > 0){
				$total = ($o->originalRate * $qty) + $total;
				$totalQty = $qty + $totalQty;
				$offerTotal = ($o->rate) + $offerTotal;
				$offerTotalQty = $OfferQty + $offerTotalQty;
			}else{
				$total = 0 + $total;
				$totalQty = 0 + $totalQty;
				$offerTotal = 0 + $offerTotal;
				$offerTotalQty = 0 + $offerTotalQty;
			}
		}
	}
	if($totalQty > 0){
		if(sizeof($promotions[0]->child) > 0){
			$totalC = 0;
			$totalQtyC =0;
			$offerTotalC =0;
			$offerTotalQtyC = 0;
			foreach($promotions[0]->child as $o) { 
				if($o->returnQty != null){
					$qty = $o->qty - $o->returnQty;
				}else{
					$qty = $o->qty;
				}
				if($o->OfferQty != null){
					$OfferQty = $o->OfferQty - $o->offerReturnQty;
				}else{
					$OfferQty = $o->OfferQty;
				}
				if($qty > 0){
					$totalC = ($o->originalRate * $qty) + $totalC;
					$totalQtyC = $qty + $totalQtyC;
					$offerTotalC = $o->rate + $offerTotalC;
					$offerTotalQtyC = $OfferQty + $offerTotalQtyC;
				}else{
					$totalC = 0 + $totalC;
					$totalQtyC = 0 + $totalQtyC;
					$offerTotalC = 0 + $offerTotalC;
					$offerTotalQtyC = 0 + $offerTotalQtyC;
				}
			}
			if($totalQtyC > 0){
				$promotions[0]->total = $totalC;
				$promotions[0]->totalQty = $totalQtyC;
				$promotions[0]->offerTotal = $offerTotalC; 
				$promotions[0]->offerTotalQty = $offerTotalQtyC;
			}else{
				$promotions[0]->total = 0;
				$promotions[0]->totalQty = 0;
				$promotions[0]->offerTotal = 0; 
				$promotions[0]->offerTotalQty = 0;
			}
		}
		if(sizeof($promotions[1]->child) > 0){
			$totalC = 0;
			$totalQtyC =0;
			$offerTotalC =0;
			$offerTotalQtyC = 0;
			foreach($promotions[1]->child as $o) { 
				if($o->returnQty != null){
					$qty = $o->qty - $o->returnQty;
				}else{
					$qty = $o->qty;
				}
				if($o->OfferQty != null){
					$OfferQty = $o->OfferQty - $o->offerReturnQty;
				}else{
					$OfferQty = $o->OfferQty;
				}
				if($qty > 0){
					$totalC = ($o->originalRate * $qty) + $totalC;
					$totalQtyC = $qty + $totalQtyC;
					$offerTotalC = $o->rate + $offerTotalC;
					$offerTotalQtyC = $OfferQty + $offerTotalQtyC;
				}else{
					$totalC = 0 + $totalC;
					$totalQtyC = 0 + $totalQtyC;
					$offerTotalC = 0 + $offerTotalC;
					$offerTotalQtyC = 0 + $offerTotalQtyC;
				}
			}
			if($totalQtyC > 0){
				$promotions[1]->total = $totalC;
				$promotions[1]->totalQty = $totalQtyC;
				$promotions[1]->offerTotal = $offerTotalC; 
				$promotions[1]->offerTotalQty = $offerTotalQtyC;
			}else{
				$promotions[1]->total = 0;
				$promotions[1]->totalQty = 0;
				$promotions[1]->offerTotal = 0; 
				$promotions[1]->offerTotalQty = 0;
			}
		}
		if(sizeof($promotions[2]->child) > 0){
			$totalC = 0;
			$totalQtyC =0;
			$offerTotalC =0;
			$offerTotalQtyC = 0;
			foreach($promotions[2]->child as $o) {
				if($o->returnQty != null){
					$qty = $o->qty - $o->returnQty;
				}else{
					$qty = $o->qty;
				}
				if($o->OfferQty != null){
					$OfferQty = $o->OfferQty - $o->offerReturnQty;
				}else{
					$OfferQty = $o->OfferQty;
				}
				if($qty > 0){
					$totalC = ($o->originalRate * $qty) + $totalC;
					$totalQtyC = $qty + $totalQtyC;
					$offerTotalC = $o->rate + $offerTotalC;
					$offerTotalQtyC = $OfferQty + $offerTotalQtyC;
				}else{
					$totalC = 0 + $totalC;
					$totalQtyC = 0 + $totalQtyC;
					$offerTotalC = 0 + $offerTotalC;
					$offerTotalQtyC = 0 + $offerTotalQtyC;
				}
			}
			if($totalQtyC > 0){
				$promotions[2]->total = $totalC;
				$promotions[2]->totalQty = $totalQtyC;
				$promotions[2]->offerTotal = $offerTotalC; 
				$promotions[2]->offerTotalQty = $offerTotalQtyC;
			}else{
				$promotions[2]->total = 0;
				$promotions[2]->totalQty = 0;
				$promotions[2]->offerTotal = 0; 
				$promotions[2]->offerTotalQty = 0;
			}
		}
	}else{
		$total = 0;
		$totalQty = 0;
		$offerTotal = 0;
		$offerTotalQty = 0;
	}
	foreach ($promotions as $o ) {
		if(sizeof($o->child) > 0){
			$promotions_temp[]=$o;

		}			
	}
	return array(
		"total" 			=>	$total,
		"totalQty" 			=> 	$totalQty,	
		"offerTotal" 		=> 	$offerTotal,
		"offerTotalQty" 	=> 	$offerTotalQty,	
		"promotions_temp"	=> 	$promotions_temp,
	);
}

/**
* Display a listing of the resource. Used for Customer_Sales_Summary_Report
* @param  Request $request [description]
* @return [type]           [description]
*/
public function indexLoyalty(Request $request){
	$customers = array();
	$customers1 = Customer::all();	
	$customers[] = (object) array('id'=>'0','name' => 'All');
	foreach ($customers1 as $row) {
		$customers[]=$row;
	}	
	$date = Carbon::now()->format('m/d/Y');
	$date1 = Carbon::now()->format('m/d/Y');
	$mallDetails = $this->mallDetails();
	$res = array(
		"customers" =>$customers,
		"date" => $date,
		"date1" => $date1,
		"mallDetails" => $mallDetails,
	);
	if($request->ajax()){
		return Response($res,200);
	}else{
		return View('pages.sales_reports.loyalty_report.loyalty_report', $res);
	}
}

/**
* Select data which is displayed on report view table . used for Customer_Sales_Summary_Report
* @param  REquest $request [description]
* @return [type]           [description]
*/
public function getLedgerLoyalty(REquest $request){		
	$dateArray 			= 	$request->all()["data"]; 
	$fromDate 			= 	new Carbon($dateArray['fromDate']);
	$toDate 			= 	new Carbon($dateArray['toDate']);		
	$customer_id 		= 	$dateArray['customer_id'];
	$total 				=	0;
	$data 				= 	$this->getLoyalityReportQuery($fromDate, $toDate, $customer_id );
	$customers 			= 	array();
	$customers1 		= 	Customer::all();	
	$customers[] 		= 	(object) array('id'=>'0','name' => 'All');
	foreach ($customers1 as $row) {
		$customers[]=$row;
	}	
	$labels 			= 	$this->getLabels();		
	$purchases 			= 	$data['purchases'];
	$total 				= 	$data['total'];
	$res = array(
		"customers"  	=> 	$customers,			
		"purchases" 	=> 	$purchases,	
		"total" 		=>	$total,
		"labels" 		=> 	$labels,
		"toDatePrint" 	=> 	(new Carbon($toDate))->format($this->rDateFormat),
		"fromDatePrint" => 	(new Carbon($fromDate))->format($this->rDateFormat),		
	);
	if($request->ajax()){
		return Response($res,200);
	}else{
		return View("pages.sales_reports.loyalty_report.loyalty_report",$res);
	}
}

/**
* Function created for create and export exel and pdf file . used for Customer_Sales_Summary_Report
* @param  Request $request [description]
* @return [type]           [description]
*/
public function exportToLoyalty(Request $request){				
	$dateArray 		= 	$request->all(); 
	$fromDate 		= 	new Carbon($dateArray['from']);
	$toDate 		= 	new Carbon($dateArray['to']);
	$customer_id 	= 	$dateArray['customer'];
	$total 			=	0;
	$currency 		=	$this->optioncurr;
	$data 			= 	$this->getLoyalityReportQuery($fromDate, $toDate, $customer_id );
	$fromDate 		= 	date($this->rDateFormat, strtotime($fromDate));
	$toDate 		= 	date($this->rDateFormat, strtotime($toDate));
	$purchases 		= 	$data['purchases'];
	$total 			= 	$data['total'];
	if(Input::get('pdf')){	
		$pdf = PDF::loadView('pages.sales_reports.loyalty_report.loyalty_report_pdf', [
			"purchases" 	=> 	$purchases ,
			"total" 		=> 	$total,
			"fromDate" 		=> 	$fromDate,
			"toDate" 		=> 	$toDate,
			"i" 			=>	'1',
			"curr" 			=> 	$currency
		]); 
		$pdf->setPaper('a4');
		$carbon = Carbon::today();
		$timestamp = $carbon->timestamp;
		$format = $carbon->format($this->rDateFormat);
		$exportName = 'LoyaltyReport-'.$format.'.pdf';     
		return $pdf->download($exportName);
	}elseif (Input::get('xls')) {
		$carbon = Carbon::today();
		$timestamp = $carbon->timestamp;
		$format = $carbon->format($this->rDateFormat);
		$exportName = 'LoyaltyReport-'.$format; 
		Excel::create($exportName, function ($excel)
			use($purchases,
				$total,
				$fromDate,
				$toDate,
				$currency) {
				$excel->sheet('LoyaltyReport', function ($sheet) 
					use($purchases,
						$total,
						$fromDate,
						$toDate,
						$currency) {
						$sheet->appendRow(array('', ''));
						$sheet->appendRow(array(trans('glob.from'), $fromDate ,trans('glob.to'),$toDate));
						$sheet->row($sheet->getHighestRow(), function ($row) {
							$row->setFontWeight('bold');
						});	
						$sheet->appendRow(array('', ''));
						/* setting column names for data - you can of course set it manually*/
						$sheet->appendRow(array(
							"#"  ,	
							trans('billing.bill_no') ,						
							trans('billing.bill_date') ,		
							trans('billing.earned_points') ,
							trans('billing.redeemed_points') ,
							trans('billing.loyalty_discount')."(".$currency.")" ,		
						)); /* column names*/
						/* getting last row number (the one we already filled and setting it to bold*/
							$sheet->row($sheet->getHighestRow(), function ($row) {
								$row->setFontWeight('bold');
							});
							if(sizeof($purchases) >0 ){
								$itemList = array();
								foreach ($purchases as $p) {						
									$sheet->appendRow( array(
										"#"  => $p->name,	
										trans('billing.bill_no') => '',						
										trans('billing.bill_date') => '',		
										trans('billing.earned_points') => '',
										trans('billing.redeemed_points') => '',
										trans('billing.loyalty_discount')."(".$currency.")" => '',		
									)
								);
									$i=0;
									foreach ($p->child as $o) {						
										$i++;						
										$sheet->appendRow( array(
											"#"  => $i,	
											trans('billing.bill_no') => " ".Common::fixBill($o->id),					
											trans('billing.bill_date') => $o->billingDate,		
											trans('billing.earned_points') => " ".Common::fixIt($o->earned_points),
											trans('billing.redeemed_points') => " ".Common::fixIt($o->loyalty_points),
											trans('billing.loyalty_discount')."(".$currency.")" => " ".Common::fixIt($o->loyalty_discount),

										)
									);
									}
									$sheet->appendRow( array(
										"#"  => '',	
										trans('billing.bill_no') => '',						
										trans('billing.bill_date') => '',
										trans('billing.earned_points') => '',
										trans('billing.redeemed_points') => '',
										trans('billing.loyalty_discount')."(".$currency.")" => trans('billing.loyalty_discount')." = ".Common::fixIt($p->loyalty_discount),		
									)
								);
								}
							}
							$sheet->appendRow(array(
								trans('glob.id')  => '',	
								trans('billing.bill_no') => '',							
								trans('billing.bill_date') => '',
								trans('billing.earned_points') => '',
								trans('billing.redeemed_points') => '',
								trans('billing.loyalty_discount')."(".$currency.")" => trans('glob.total').' '.trans('billing.loyalty_discount')." = ".Common::fixIt($total),
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

public function getLoyalityReportQuery($fromDate, $toDate, $customer_id ){
	$customer_id 	= 	$customer_id ?? 0;
	$total 			= 	0;
	$purchases =DB::select("SELECT c.id,CONCAT(c.name, ' - ',c.mobile) as name,
		sum(b.loyalty_discount) as loyalty_discount
		FROM billings b INNER JOIN customers c on b.customer_id=c.id  
		where b.billing_status <> 'hold' AND 
		CASE WHEN $customer_id > 0 THEN b.customer_id = $customer_id ELSE b.customer_id != 'null' END and 
		CAST(b.created_at AS DATE) BETWEEN '".$fromDate."' and '".$toDate."' GROUP BY b.customer_id");
	foreach ($purchases as $row) {		 	
		$p=DB::select("SELECT b.id,DATE_FORMAT(b.created_at,'".$this->rDateFormatSQL."') billingDate,
			b.loyalty_points,b.loyalty_discount,b.earned_points
			FROM billings b 
			where b.billing_status <> 'hold' AND b.customer_id =$row->id and 
			CAST(b.created_at AS DATE) BETWEEN '".$fromDate."' and '".$toDate."'  ORDER BY b.created_at ASC");
		$row->child = $p;
		$total = $row->loyalty_discount + $total;
	}
	return array(
		'purchases'  => $purchases,
		'total' 		=> $total
	);
}


/**
	 * Display a listing of the resource. Used for Sales_Summary_Report
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
public function indexSalesPack(Request $request){
	try{

		$date = Carbon::now()->format('m/d/Y');
		$date1 = Carbon::now()->format('m/d/Y');
		$mallDetails = $this->mallDetails();
		$res = array(
			"date" => $date,
			"date1" => $date1,
			"mallDetails" => $mallDetails,
		);

		if($request->ajax()){
			return Response($res,200);
		}else{
			return View('pages.sales_reports.sales_pack.sales_pack', $res);
		}

	}catch(Exception $e){
		Log::error("SalesReportController@indexSalesPack ".$e);
		return View("errors.exception");
	}
}

/**
	 * Select data which is displayed on report view table . used for Sales_Summary_Report
	 * @param  REquest $request [description]
	 * @return [type]           [description]
	 */
public function getLedgerSalesPack(Request $request){
	try{		
		$dateArray 			=	$request->all()["data"]; 
		$fromDate 			=	new Carbon($dateArray['fromDate']);
		$toDate 			=	new Carbon($dateArray['toDate']);		
		$total 				=	0;
		$totalCash 			=	0;
		$totalCard 			=	0;
		$itemList 			=	[];	
		$pay_mode 			=	'';
		$totalDiscount 		=	0;
		$totalLoyDis 		=	0;
		$totalVat 			=	0;
		$data 				= 	$this->getSalesSummaryQuery($fromDate,$toDate);
		$total 				= 	$data['total'];	
		$totalCash 			= 	$data['totalCash'];	
		$totalCard 			= 	$data['totalCard'];	
		$itemList 			= 	$data['itemList'];	
		$totalDiscount 		= 	$data['totalDiscount'];	
		$totalLoyDis 		= 	$data['totalLoyDis'];	
		$totalVat 			= 	$data['totalVat'];	
		$purchases			= 	$data['purchases'];
		$labels 			= 	$this->getLabels();				
		$res = array(
			"total" 		=> 	Common::fixIt($total),
			"itemList"		=> 	$itemList,
			"purchases" 	=> 	$purchases,
			"totalCash" 	=> 	Common::fixIt($totalCash),
			"totalCard" 	=> 	Common::fixIt($totalCard),	
			"totalDiscount" => 	Common::fixIt($totalDiscount),
			"totalLoyDis" 	=> 	Common::fixIt($totalLoyDis),	
			"totalVat"		=> 	Common::fixIt($totalVat),
			"labels"		=> 	$labels,	
			"toDatePrint" 	=> 	(new Carbon($toDate))->format($this->rDateFormat),
			"fromDatePrint" => 	(new Carbon($fromDate))->format($this->rDateFormat),	
		);
		if($request->ajax()){
			return Response($res,200);
		}else{
			return View("pages.sales_reports.sales_pack.sales_pack",$res);
		}

	}catch(Exception $e){
		Log::error("SalesReportController@getLedgerSalesPack ".$e);
		return View("errors.exception");
	}
}

		/**
	 	* Function created for create and export exel and pdf file . used for Sales_Summary_Report
	 	* @param  Request $request [description]
	 	* @return [type]           [description]
	 	*/
	 	public function exportToSalesPack(Request $request){	
	 		try{			
	 			$dateArray 		= 	$request->all(); 
	 			$fromDate 		= 	new Carbon($dateArray['from']);
	 			$toDate 		= 	new Carbon($dateArray['to']);
	 			$currency 		=	$this->optioncurr;
	 			$arrData  			= 	$this->getSalesPactQuery($fromDate,$toDate);
	 			
	 			if(Input::get('pdf')){	
	 				$pdf = PDF::loadView('pages.sales_reports.sales_summary.sales_summary_pdf', [
	 					"fromDate" 		=> 	(new Carbon($fromDate))->format($this->rDateFormat)  ,
	 					"toDate" 		=> 	(new Carbon($toDate))->format($this->rDateFormat) ,
	 					"i" 			=>	'1',
	 					"curr"			=>	$currency,
	 					'intVatType'	=> 	$this->intVatType,
	 				]); 
	 				$pdf->setPaper('a4');
	 				$carbon = Carbon::today();
	 				$timestamp = $carbon->timestamp;
	 				$format = $carbon->format($this->rDateFormat);
	 				$exportName = 'SALESPACT-'.$format.'.pdf';     
	 				return $pdf->download($exportName);
	 			}elseif (Input::get('xls')) {
	 				$carbon = Carbon::today();
	 				$timestamp = $carbon->timestamp;
	 				$format = $carbon->format($this->rDateFormat);
	 				$exportName = 'SALESPACT-'.$format; 
	 				Excel::create($exportName, function ($excel) 
	 					use($arrData,
	 						$fromDate,
	 						$toDate,
	 						$currency) {
	 						$excel->sheet('SalesSummary', function ($sheet) 
	 							use($arrData,
	 								$fromDate,
	 								$toDate,
	 								$currency) {

	 								
	 								/* setting column names for data - you can of course set it manually */
	 								if($this->intVatType != 0){
	 									$sheet->appendRow(array(
	 										"BillDate" ,
	 										"BillNo" ,
	 										"Customer Name" ,
	 										"Sales Account" ,
	 										"VATSalesType" ,
	 										"VATAccountType" ,
	 										"VATCategory",
	 										"Country",
	 										"State",
	 										"RSPType" ,
	 										"Product Code",
	 										"Qty",
	 										"R",
	 										"Rate",
	 										"Amount",
	 										"DiscAmt",
	 										"TaxbleAmt",
	 										"VATPer",
	 										"VAT Amt",
	 										"CASH",
	 										"CARD",
	 										"Location",
	 										"BillwiseRef"
	 									)); /* column names */
	 								}else{
	 									$sheet->appendRow(array(
	 										"BillDate" ,
	 										"BillNo" ,
	 										"Customer Name" ,
	 										"Sales Account" ,
	 										"Country",
	 										"State",
	 										"RSPType" ,
	 										"Product Code",
	 										"Qty",
	 										"R",
	 										"Rate",
	 										"Amount",
	 										"DiscAmt",
	 										"CASH",
	 										"CARD",
	 										"Location",
	 										"BillwiseRef"
	 									)); /* column names */
	 								}
	 								/* getting last row number (the one we already filled and setting it to bold*/
	 									$sheet->row($sheet->getHighestRow(), function ($row) {
	 										$row->setFontWeight('bold');
	 									});
	 									if(sizeof($arrData)>0){
	 										if($this->intVatType != 0){
	 											foreach ($arrData as $o) {
	 												
	 												if($o->customerName == null){
	 													$custName="NA";
	 												}else{
	 													$custName=$o->customerName;
	 												}
	 												
	 												$sheet->appendRow(array(
	 													"BillDate" => $o->billingDate ,
	 													"BillNo" => " ".Common::fixBill($o->id) ,
	 													"Customer Name" => $custName ,
	 													"Sales Account" => "Outlet Sales - Dubai Mall",
	 													"VATSalesType" => "Standard-rated supplies" ,
	 													"VATAccountType" => "Unregistered",
	 													"VATCategory" => "VAT 5%",
	 													"Country" => "United Arab Emirates",
	 													"State" => "Dubai",
	 													"RSPType" => "AED",
	 													"Product Code" => $o->itemCode,
	 													"Qty" => " ".Common::fixQty($o->qty),
	 													"R" => " ".Common::fixIt($o->rate),
	 													"Rate" => " ".Common::fixIt($o->rate),
	 													"Amount" => " ".Common::fixIt($o->amount),
	 													"DiscAmt" => " ".Common::fixIt($o->dis_amt),
	 													"TaxbleAmt" => " ".Common::fixIt($o->taxbleamt),
	 													"VATPer" => " ".Common::fixIt($o->vat_percent),
	 													"VAT Amt" =>" ".Common::fixIt($o->v_amt),
	 													"CASH" => " ".Common::fixIt($o->cash_amt),
	 													"CARD" => " ".Common::fixIt($o->card_amt),
	 													"Location" => "Emaar Mall Outlet",
	 													"BillwiseRef" => '',
	 												)
	 											);
	 												
	 											}
	 										}else{
	 											foreach ($arrData as $o) {
	 												
	 												if($o->customerName == null){
	 													$custName="NA";
	 												}else{
	 													$custName=$o->customerName;
	 												}
	 												//$i++;
	 												$sheet->appendRow(array(
	 													"BillDate" => $o->billingDate ,
	 													"BillNo" => " ".Common::fixBill($o->id) ,
	 													"Customer Name" => $custName ,
	 													"Sales Account" => "Outlet Sales - Dubai Mall",
	 													"Country"  => "United Arab Emirates",
	 													"State"  => "Dubai",
	 													"RSPType"  => "AED",
	 													"Product Code" => $o->itemCode,
	 													"Qty" => " ".Common::fixQty($o->qty),
	 													"R" => " ".Common::fixIt($o->rate),
	 													"Rate" => " ".Common::fixIt($o->rate),
	 													"Amount" => " ".Common::fixIt($o->amount),
	 													"DiscAmt" => " ".Common::fixIt($o->dis_amt),
	 													"CASH" => " ".Common::fixIt($o->cash_amt),
	 													"CARD" => " ".Common::fixIt($o->card_amt),
	 													"Location" => "Emaar Mall Outlet",
	 													"BillwiseRef" => '',
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
	Log::error("SalesReportController@exportToSalesSummary ".$e);
	return View("errors.exception");
}
}

public function getSalesPactQuery($fromDate, $toDate){

	$arrData =  DB::select("SELECT b.id, DATE_FORMAT(b.created_at,'".$this->rDateFormatSQL."') as billingDate, 
		b.bil_amt,
		b.dis_amt,
		bi.qty,
		bi.rate,
		bi.offer_type,
		bi.vat as vat_percent,
		items.itemName as itemName ,
		c.name as customerName,
		b.subtotal as subtotal,
		b.v_amt as v_amt,
		(case when b.currency > 0 then (select currencies.amount from currencies where currencies.id = b.currency) * b.cash_amt else b.cash_amt end)as cash_amt,
		(case when b.currency > 0 then (select currencies.amount from currencies where currencies.id = b.currency) * b.card_amt else b.card_amt end)as card_amt,
		(bi.qty * bi.rate) as amount,
		items.barcode as itemCode ,
		items.serial_number as refrence_number ,
		bi.serial_no as serial_number ,
		items.costPrise as costPrise ,
		(bi.qty - (SELECT sum(bill_return_items.qty) 
		FROM bill_return_items INNER JOIN bill_returns on bill_return_items.return_id=bill_returns.id 
		where bill_returns.billing_id=b.id and bill_return_items.item_id=bi.item_id)) as return_qty
		FROM billings b INNER JOIN billing_items bi ON b.id = bi.billing_id 
		INNER JOIN items on items.id = bi.item_id 
		LEFT JOIN customers c ON b.customer_id = c.id
		where b.billing_status <> 'hold' AND  CAST(b.created_at AS DATE) BETWEEN '".$fromDate."' and '".$toDate."'  ORDER BY b.created_at ASC");

	foreach ($arrData as $objData) {

		if($objData->subtotal == null || $objData->subtotal == 0){
			$percentage = 0;
		}else{
			$percentage = ($objData->dis_amt /($objData->subtotal + $objData->v_amt)) * 100;
		}

		$objData->percent = $percentage;
		$objData->dis_amt = (($objData->amount * $percentage ) /100 );
		$objData->taxbleamt = ($objData->amount - $objData->dis_amt);
		$objData->v_amt = (($objData->taxbleamt * $objData->vat_percent)/($objData->vat_percent + 100));

	}

	return $arrData;

}


 	 /**
     * This will return resource values of labels for printing purpose
     * @return Array of labels
     */
 	 public function getLabels(){
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
 	 		"bill_date" => trans('billing.bill_date'),
 	 		"dis_amt" => trans('billing.dis_amt'),
 	 		"offer_item" => trans('glob.offer_item'),
 	 		"offer_qty" => trans('glob.offer_qty'),
 	 		"promo_rate" => trans('glob.promo_rate'),
 	 		"barcode" => trans('glob.barcode'),
 	 		"c_p" => trans('item.c_p'),
 	 		"total_s_p" => trans('glob.total_s_p'),
 	 		"total_c_p" => trans('glob.total_c_p'),
 	 		"total_profit" => trans('glob.total_profit'),	
 	 		"sub_total" => trans('billing.sub_total'), 
 	 		"mode_of_pay" => trans('billing.mode_of_pay'),
 	 		"cash_amt" => trans('billing.cash_amt'),
 	 		"card_amt" => trans('billing.card_amt'), 	
 	 		"sale_return_no" => trans('glob.sale_return_no'),
 	 		"category" => trans('glob.category'),	
 	 		"opening_amt" => trans('glob.opening_amt'),
 	 		"sale" => trans('glob.sale'),	
 	 		"cash" => trans('billing.cash'),
 	 		"credit_card" => trans('billing.credit_card'),
 	 		"credit" => trans('billing.credit'),
 	 		"sale_return"	=> trans('billing.sale_return'),
 	 		"bal" => trans('billing.bal'),
 	 		"website" => trans('glob.website'), 
 	 		"phone" => trans('glob.phone'),
 	 		"loyalty_discount" =>trans('billing.loyalty_discount'),
 	 		"redeemed_points" =>trans('billing.redeemed_points'),
 	 		"earned_points" =>trans('billing.earned_points'),
 	 		"vat" =>trans('glob.vat'),
 	 		"sale_return_date" => trans('glob.sale_return_date'),
 	 		"refrence_number" => trans('item.refrence_number'),
 	 		"sales_with_vat" => trans("glob.sales_with_vat"),
 	 		"sales_without_vat" => trans("glob.sales_without_vat"),
 	 		"currency"	=> trans("glob.currency"),
 	 		"mobile"  =>trans("glob.mobile"),
 	 		"birthday"  =>trans("glob.date_of_birth"),
 	 		"city"  =>trans("glob.city"),
 	 		"prefered_mode"  =>trans("glob.prefered_mode"),
 	 		"prefered_language"  =>trans("glob.prefered_language"),
 	 		"item_name"  =>trans("billing.item_name"),
 	 		"salesman" =>trans("glob.salesman"),
 	 		"bill_no" =>trans("glob.bill_no"),
			"grossSale"=>trans("glob.grossSale"),
			"vatValue"=>trans("glob.vatValue"),
			"netSale"=>trans("glob.netSale"),
			"taxRefundTagNumber"=>trans('glob.taxRefundTagNumber'),
			"refrence_number"=>trans('item.refrence_number'),
			"brand_name"=>trans('glob.brand_name'),
			'totalCustomer'=>trans('glob.totalCustomer'),
	        'totalTransaction'=>trans('glob.totalTransaction'),
	        'totalItemQty'=>trans('glob.totalItemQty'),
			'customerType'=>trans('glob.customerType'),
	       'total_purchase'=>trans('glob.total_purchase'),
		   'total_sale'=>trans('glob.total_sale'),
		   'value'=>trans('glob.value')
	
 	 	)
 	 );
 	 	return $labels;
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
 	 		Log::error("SalesReportController@mallDetails ".$e);
 	 		return View("errors.exception");

 	 	}
 	 }

 	public function indexCRM(Request $request){
	try{
		$customers = array();
		$customers1 = Customer::all();	
		$brands1=Category::where('parent_category','=',0)->get();
		$brands[] = (object) array('id'=>'0','categorie' => 'All');
		foreach ($brands1 as $row) {
			$brands[]=$row;
		}
		$customers[] = (object) array('id'=>'0','name' => 'All');
		foreach ($customers1 as $row) {
			$customers[]=$row;
		}		
		$date = Carbon::now()->format('m/d/Y');
		$date1 = Carbon::now()->format('m/d/Y');
		$mallDetails = $this->mallDetails();
		$res = array(
			"date" => $date,
			"date1" => $date1,
			"mallDetails" => $mallDetails,
			"customers" => $customers,
			"brands" => $brands,
		);

		if($request->ajax()){
			return Response($res,200);
		}else{
			return View('pages.sales_reports.crm_report.crm_report', $res);
		}

	}catch(Exception $e){
		Log::error("SalesReportController@indexCRM ".$e);
		return View("errors.exception");
	}
}

	public function getLedgerCRMReport(Request $request){
	try{		
		$dateArray 			=	$request->all()["data"]; 
		$fromDate 			=	new Carbon($dateArray['fromDate']);
		$toDate 			=	new Carbon($dateArray['toDate']);		
		$customer_id  = $dateArray['customer_id'];
		$brand_id=$dateArray['brand_id'];
		$data				= 	$this->getCRMQuery($fromDate,$toDate,$customer_id,$brand_id);
	    $purchases = $data['purchases'];

		$labels 			= 	$this->getLabels();
		$customers 		= 	array();
		$customers1 	= 	Customer::all();	
		$customers[] 	= 	(object) array('id'=>'0','name' => 'All');
		foreach ($customers1 as $row) {
			$customers[]=$row;
		}					
		$res = array(
			"customers"  	=> 	$customers,		
			"toDatePrint" 	=> 	(new Carbon($toDate))->format($this->rDateFormat),
			"fromDatePrint" => 	(new Carbon($fromDate))->format($this->rDateFormat),	
			"purchases"=>$purchases,
			"labels" =>$labels,
			"totalTransaction"=>$data['totalTransaction'],
	        "totalCustomer"=>$data['totalCustomer'],
	        "totalQty"=>$data['totalQty']
		);
		if($request->ajax()){
			return Response($res,200);
		}else{
			return View("pages.sales_reports.crm_report.crm_report",$res);
		}

	}catch(Exception $e){
		Log::error("SalesReportController@getLedgerCRMReport ".$e);
		return View("errors.exception");
	}
}


	public function getCRMQuery($fromDate,$toDate,$customer_id,$brand_id)
	{

		$customer_id = $customer_id ?? 0;
		$itemList = [];
		$totalCustomer=0;
		$totalQty=0;
		$totalTransaction=0;
	// 	$p1=DB::select("SELECT COUNT(DISTINCT(b.id)) as totalTransaction,count(DISTINCT(b.customer_id))as TotalCustomer,SUM(bi.qty) as totalQty
	// 	FROM billings b 
	// 	INNER JOIN customers c on b.customer_id=c.id
	// 	INNER JOIN salesman s on b.salesman_id= s.id
	// 	INNER JOIN 
	// 	billing_items bi
	// 	on b.id = bi.billing_id
	// 	INNER JOIN
	// 	items i
	// 	on bi.item_id = i.id
	// 	INNER JOIN categories ca on ca.id=i.category
	// 	where c.id = b.customer_id AND CASE WHEN  $customer_id > 0 THEN b.customer_id = $customer_id ELSE b.customer_id != 'null' END 
	//    AND CASE WHEN $brand_id > 0 THEN  ca.parent_category = $brand_id ELSE true END 
	// 	AND CAST(b.created_at AS DATE) BETWEEN '".$fromDate."' and '".$toDate."' Group By b.customer_id");
	// 	$p=DB::select("SELECT COUNT(DISTINCT(b.id)) as totalTransaction,count(DISTINCT(b.customer_id))as TotalCustomer,SUM(bi.qty) as totalQty
	// 	FROM billings b 
	// 	INNER JOIN customers c on b.customer_id=c.id
	// 	INNER JOIN salesman s on b.salesman_id= s.id
	// 	INNER JOIN 
	// 	billing_items bi
	// 	on b.id = bi.billing_id
	// 	INNER JOIN
	// 	items i
	// 	on bi.item_id = i.id
	// 	INNER JOIN categories ca on ca.id=i.category
	// 	where c.id = b.customer_id AND CASE WHEN  $customer_id > 0 THEN b.customer_id = $customer_id ELSE b.customer_id != 'null' END 
	//    AND CASE WHEN $brand_id > 0 THEN  ca.parent_category = $brand_id ELSE true END 
	// 	AND CAST(b.created_at AS DATE) BETWEEN '".$fromDate."' and '".$toDate."' Group By b.id");
    //     $purchases = DB::select("SELECT c.id,s.id,b.id as bill_id,c.name as customer_name,c.mobile as mobile,DATE_FORMAT(c.birthday,'".$this->rDateFormatSQL."') as birthday,DATE_FORMAT(b.created_at,'".$this->rDateFormatSQL."') as bill_date,
    //          c.city as city,s.name as salesman,c.prefered_mode as prefered_mode,c.prefered_language,i.itemName as item_name,sum(bi.rate*qty) as value,ca.categorie,(select categorie from categories where id=ca.parent_category) as brand_name,i.serial_number as reference_number,sum(bi.qty)as qty,
	// 		 b.bil_amt as value1
	// 		 FROM billings b 
    //          INNER JOIN customers c on b.customer_id=c.id
    //          INNER JOIN salesman s on b.salesman_id= s.id
    //          INNER JOIN 
    //          billing_items bi
    //          on b.id = bi.billing_id
    //          INNER JOIN
    //          items i
    //          on bi.item_id = i.id
	// 		 INNER JOIN categories ca on ca.id=i.category
    //          where c.id = b.customer_id AND CASE WHEN  $customer_id > 0 THEN b.customer_id = $customer_id ELSE b.customer_id != 'null' END 
	// 		AND CASE WHEN $brand_id > 0 THEN  ca.parent_category = $brand_id ELSE true END 
	// 		 AND CAST(b.created_at AS DATE) BETWEEN '".$fromDate."' and '".$toDate."' GROUP BY  bi.id ORDER BY b.created_at ASC");
	// 	if(sizeof($purchases)>0){
	// 	foreach ($purchases as $o) {
	// 			if($o->prefered_language == 1){
	// 				$prefered_language = 'English';
	// 			}else if($o->prefered_language == 2){
	// 				$prefered_language = 'Arabic';
	// 			}else if($o->prefered_language == null || $o->prefered_language == 0){
	// 				$prefered_language = 'NA';
	// 			}
	// 			if($o->prefered_mode == 1){
	// 				$prefered_mode = 'Email';
	// 			}elseif($o->prefered_mode == 2){
	// 				$prefered_mode = "Phone";
	// 			}elseif($o->prefered_mode == 3){
	// 				$prefered_mode = "sms";
	// 			}elseif($o->prefered_mode == 4){
	// 				$prefered_mode = "whatsapp";
	// 			}elseif($o->prefered_mode == null || $o->prefered_mode ==0){
	// 				$prefered_mode = "NA";
	// 			}
	// 			if($o->city == null){
	// 				$city = "NA";
	// 			}else{
	// 				$city=$o->city;
	// 			}
	// 			if($o->customer_name == null){
	// 				$custName="NA";
	// 			}else{
	// 				$custName=$o->customer_name;
	// 			}
	// 			if($o->birthday == null){
	// 				$birthday="NA";
	// 			}else{
	// 				$birthday=$o->birthday;
	// 			}
	// 			array_push($itemList, array(
	// 				"bill_id"  => Common::fixBill($o->bill_id),
	// 				"customer_name" => $custName,
	// 				"mobile" =>$o->mobile,
	// 				"birthday" =>$birthday,
	// 				"bill_date" => $o->bill_date,
	// 				"city" => $city,
	// 				"salesman" => $o->salesman,
	// 				"prefered_mode" => $prefered_mode,
	// 				"prefered_language" => $prefered_language,
	// 				"brand_name"=>$o->brand_name,
	// 				"reference_number"=>$o->reference_number,
	// 				"item_name" => $o->item_name,
	// 				"value"=>Common::fixIt($o->value)
					
	// 			)
	// 		);

				
			//}

			// foreach($p as $row){
			// 	$totalTransaction=$row->totalTransaction +$totalTransaction;
			// 	// $totalCustomer=$row->TotalCustomer+$totalCustomer;
			// 	$totalQty=$row->totalQty+$totalQty;
			// }
			// foreach($p1 as $row){
				
			// 	 $totalCustomer=$row->TotalCustomer+$totalCustomer;
				
			// }

//}

$purchases =DB::select("SELECT c.id as id ,c.name as name, sum(bi.qty) as totalQty,COUNT(DISTINCT(b.id)) as totalTransaction,COUNT(DISTINCT(b.customer_id))as TotalCustomer from
			 billings b 
			 INNER JOIN customers c on b.customer_id=c.id
			 Inner join billing_items bi on bi.billing_id=b.id
			 INNER JOIN items i on bi.item_id = i.id
	         INNER JOIN categories ca on ca.id=i.category
			 where b.billing_status <> 'hold' AND CASE WHEN  $customer_id > 0 THEN b.customer_id = $customer_id ELSE b.customer_id != 'null' END and CAST(b.created_at AS DATE) BETWEEN '".$fromDate."' and '".$toDate."'
			 AND CASE WHEN $brand_id > 0 THEN  ca.parent_category = $brand_id ELSE true END 
			 GROUP BY b.customer_id order By c.id ASC");


		foreach ($purchases as $row) {	
			 	
	        $p=DB::select("SELECT c.id,s.id,b.id as bill_id,c.name as customer_name,c.mobile as mobile,DATE_FORMAT(c.birthday,'".$this->rDateFormatSQL."') as birthday,DATE_FORMAT(b.created_at,'".$this->rDateFormatSQL."') as bill_date,
			        c.city as city,s.name as salesman,c.prefered_mode as prefered_mode,c.prefered_language,i.itemName as item_name,sum(bi.rate*qty) as value,ca.categorie,(select categorie from categories where id=ca.parent_category) as brand_name,i.serial_number as reference_number,sum(bi.qty)as qty,
			 		 b.bil_amt as value1
					 FROM billings b 
			         INNER JOIN customers c on b.customer_id=c.id
			         INNER JOIN salesman s on b.salesman_id= s.id
			        INNER JOIN 
			         billing_items bi
			        on b.id = bi.billing_id
			         INNER JOIN
			        items i
			         on bi.item_id = i.id
					 INNER JOIN categories ca on ca.id=i.category
			         where c.id = b.customer_id AND b.customer_id=$row->id
				  AND CASE WHEN $brand_id > 0 THEN  ca.parent_category = $brand_id ELSE true END 
	     		 AND CAST(b.created_at AS DATE) BETWEEN '".$fromDate."' and '".$toDate."' GROUP BY  bi.id ORDER BY b.created_at ASC");
				  foreach ($p as $o) {
								if($o->prefered_language == 1){
									$o->prefered_language = 'English';
								}else if($o->prefered_language == 2){
									$o->prefered_language = 'Arabic';
								}else if($o->prefered_language == null || $o->prefered_language == 0){
									$o->prefered_language = 'NA';
								}
								if($o->prefered_mode == 1){
									$o->prefered_mode = 'Email';
								}elseif($o->prefered_mode == 2){
									$o->prefered_mode = "Phone";
								}elseif($o->prefered_mode == 3){
									$o->prefered_mode = "sms";
								}elseif($o->prefered_mode == 4){
									$o->prefered_mode = "whatsapp";
								}elseif($o->prefered_mode == null || $o->prefered_mode ==0){
									$o->prefered_mode = "NA";
								}
								if($o->city == null){
									$o->city = "NA";
								}else{
									$o->city=$o->city;
								}
								if($o->customer_name == null){
									$o->customer_name="NA";
								}else{
									$o->customer_name=$o->customer_name;
								}
								if($o->birthday == null){
									$o->birthday="NA";
								}else{
									$o->birthday=$o->birthday;
								}
							
						}
			$row->child = $p;
			// $total = ($row->amt-$row->loydis_amt) + $total;
			// $totalRate =$row->rate + $totalRate;
			// $totalQty = $row->qty + $totalQty;
			// $totalDis = $row->dis_amt + $totalDis;
			// $totalLoyDis = $row->loydis_amt + $totalLoyDis;

			$totalTransaction=$row->totalTransaction +$totalTransaction;
			$totalCustomer=$row->TotalCustomer+$totalCustomer;
			$totalQty=$row->totalQty+$totalQty;

		}

		return array(
			'purchases' =>$purchases,
			'totalTransaction' => $totalTransaction,
			'totalCustomer'=>$totalCustomer,
			'totalQty'=>$totalQty,
		);

		return array (
			"purchases" => $purchases,
			"itemList" => $itemList,
			"totalTransaction"=>$totalTransaction,
			"totalCustomer"=>$totalCustomer,
			"totalQty"=>$totalQty,
		
		);
	}



	 /* Display a listing of the resource. Used for Sales_Summary_Report
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
public function indexCategorywiseSalesSummary(Request $request){
	try{

	$categoriesList = Category::all();		
	
	$categories= array();
	$categories1= Category::where("parent_category","=", 0)->get();
	//$categories[] = (object) array('id'=>'0','categorie' => 'All' ,'parent_category' => '0');
	foreach ($categories1 as $row) {
		$categories[]=$row;
	}
	$sub_categories = array();
	$sub_categories1= Category::where("parent_category","<>", 0)->get();	
	// $sub_categories[] = (object) array('id'=>'0','categorie' => 'All' ,'parent_category' => '0');
	foreach ($sub_categories1 as $row) {
		$sub_categories[]=$row;
	}

		$date = Carbon::now()->format('m/d/Y');
		$date1 = Carbon::now()->format('m/d/Y');
		$mallDetails = $this->mallDetails();
		$res = array(
			"date" => $date,
			"date1" => $date1,
			"mallDetails" => $mallDetails,
			"categories"=>$categories,
			"sub_categories"=>$sub_categories,
			"categoriesList" => $categoriesList,

		);

		if($request->ajax()){
			return Response($res,200);
		}else{
			return View('pages.sales_reports.categorywise_sales_summary.categorywise_sales_summary', $res);
		}

	}catch(Exception $e){
		Log::error("SalesReportController@indexCategorywiseSalesSummary ".$e);
		return View("errors.exception");
	}
}

/**
	 * Select data which is displayed on report view table . used for Sales_Summary_Report
	 * @param  REquest $request [description]
	 * @return [type]           [description]
	 */
public function getLedgerCategorywiseSalesSummary(REquest $request){
	try{		
		$dateArray 			=	$request->all()["data"]; 
		$fromDate 			=	new Carbon($dateArray['fromDate']);
		$toDate 			=	new Carbon($dateArray['toDate']);
		$category_id 		= 	$dateArray['category_id'];
	$sub_category_id 	= 	$dateArray['sub_category_id'];		
		$total 				=	0;
		$totalCash 			=	0;
		$totalCard 			=	0;
		$itemList 			=	[];	
		$pay_mode 			=	'';
		$totalDiscount 		=	0;
		$totalLoyDis 		=	0;
		$totalVat 			=	0;
		$data 				= 	$this->getCategorywiseSalesSummaryQuery($fromDate,$toDate,$category_id,$sub_category_id);
		$total 				= 	$data['total'];	
		$totalCash 			= 	$data['totalCash'];	
		$totalCard 			= 	$data['totalCard'];	
		$itemList 			= 	$data['itemList'];	
		$totalDiscount 		= 	$data['totalDiscount'];	
		$totalLoyDis 		= 	$data['totalLoyDis'];	
		$totalVat 			= 	$data['totalVat'];	
		$purchases			= 	$data['purchases'];
		$labels 			= 	$this->getLabels();				
		$res = array(
			"total" 		=> 	Common::fixIt($total),
			"itemList"		=> 	$itemList,
			"purchases" 	=> 	$purchases,
			"totalCash" 	=> 	Common::fixIt($totalCash),
			"totalCard" 	=> 	Common::fixIt($totalCard),	
			"totalDiscount" => 	Common::fixIt($totalDiscount),
			"totalLoyDis" 	=> 	Common::fixIt($totalLoyDis),	
			"totalVat"		=> 	Common::fixIt($totalVat),
			"labels"		=> 	$labels,	
			"toDatePrint" 	=> 	(new Carbon($toDate))->format($this->rDateFormat),
			"fromDatePrint" => 	(new Carbon($fromDate))->format($this->rDateFormat),	
		);
		if($request->ajax()){
			return Response($res,200);
		}else{
			return View("pages.sales_reports.categorywise_sales_summary.categorywise_sales_summary",$res);
		}

	}catch(Exception $e){
		Log::error("SalesReportController@getLedgerCategorywiseSalesSummary ".$e);
		return View("errors.exception");
	}
}

/**
	 * Function created for create and export exel and pdf file . used for Sales_Summary_Report
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
public function exportToCategorywiseSalesSummary(Request $request){	
	try{			
		$dateArray 		= 	$request->all(); 
		$fromDate 		= 	new Carbon($dateArray['from']);
		$toDate 		= 	new Carbon($dateArray['to']);
		$category_id 		= 	$dateArray['category'];
	    $sub_category_id 	= 	$dateArray['sub_category'];	
		$total 			=	0;
		$totalCash 		=	0;
		$totalCard 		=	0;
		$itemList  		= 	[];	
		$pay_mode  		= 	'';
		$totalDiscount 	=	0;
		$totalLoyDis 	=	0;
		$totalVat 		=	0;
		$currency 		=	$this->optioncurr;
		$data  			= 	$this->getCategorywiseSalesSummaryQuery($fromDate,$toDate,$category_id,$sub_category_id);
		$total 			= 	$data['total'];	
		$totalCash 		= 	$data['totalCash'];	
		$totalCard 		= 	$data['totalCard'];	
		$itemList 		= 	$data['itemList'];	
		$totalDiscount 	= 	$data['totalDiscount'];	
		$totalLoyDis 	= 	$data['totalLoyDis'];	
		$totalVat 		= 	$data['totalVat'];	
		$purchases		= 	$data['purchases'];
		if(Input::get('pdf')){	
			$pdf = PDF::loadView('pages.sales_reports.categorywise_sales_summary.categorywise_sales_summary_pdf', [
				"total" 		=>	Common::fixIt($total),
				"itemList"		=> 	$itemList,
				"purchases" 	=> 	$purchases,
				"totalCash" 	=> 	Common::fixIt($totalCash),
				"totalCard" 	=> 	Common::fixIt($totalCard),
				"totalDiscount" => 	Common::fixIt($totalDiscount),
				"totalLoyDis" 	=> 	Common::fixIt($totalLoyDis),	
				"totalVat" 		=> 	Common::fixIt($totalVat),
				"fromDate" 		=> 	(new Carbon($fromDate))->format($this->rDateFormat)  ,
				"toDate" 		=> 	(new Carbon($toDate))->format($this->rDateFormat) ,
				"i" 			=>	'1',
				"curr"			=>	$currency,
				'intVatType'	=> 	$this->intVatType,
			]); 
			$pdf->setPaper('a4');
			$carbon = Carbon::today();
			$timestamp = $carbon->timestamp;
			$format = $carbon->format($this->rDateFormat);
			$exportName = 'BrandwiseSalesSummary-'.$format.'.pdf';     
			return $pdf->download($exportName);
		}elseif (Input::get('xls')) {
			$carbon = Carbon::today();
			$timestamp = $carbon->timestamp;
			$format = $carbon->format($this->rDateFormat);
			$exportName = 'BrandwiseSalesSummary-'.$format; 
			Excel::create($exportName, function ($excel) 
				use($purchases,
					$fromDate,
					$toDate,$category_id,$sub_category_id,
					$currency) {
					$excel->sheet('BrandwiseSalesSummary', function ($sheet) 
						use($purchases,
							$fromDate,
							$toDate,$category_id,$sub_category_id,
							$currency) {
							$i=0;
							$itemList = array();
							$total=0;
							$totalCash=0;
							$totalCard=0;
							$itemList = [];	
							$pay_mode = '';
							$totalDiscount=0;
							$totalLoyDis =0;
							$totalVat =0;
							$sheet->appendRow(array('', ''));
							$sheet->appendRow(array(trans('glob.from'), (new Carbon($fromDate))->format($this->rDateFormat),trans('glob.to'),(new Carbon($toDate))->format($this->rDateFormat)));
							$sheet->row($sheet->getHighestRow(), function ($row) {
								$row->setFontWeight('bold');
							});			
							$sheet->appendRow(array('', ''));
							/* setting column names for data - you can of course set it manually */
							if($this->intVatType != 0){
								$sheet->appendRow(array(
									'#',
									trans('billing.bill_date') ,
									trans('billing.bill_no') ,
									trans('item.item_name'),
									trans('glob.customer') ,
									trans('billing.sub_total')."(".$currency.")" ,
									trans('glob.vat')."(".$currency.")" ,
									trans('glob.discount')."(".$currency.")" ,
									trans('glob.loyalty_discount')."(".$currency.")" ,
									trans('billing.mode_of_pay')  ,
									
								)); /* column names */
							}else{
								$sheet->appendRow(array(
									'#',
									trans('billing.bill_date') ,
									trans('billing.bill_no') ,
									trans('item.item_name'),
									trans('glob.customer') ,
									trans('billing.sub_total')."(".$currency.")" ,
									trans('glob.discount')."(".$currency.")" ,
									trans('glob.loyalty_discount')."(".$currency.")" ,
									trans('billing.mode_of_pay')  ,
									// trans('billing.cash_amt')."(".$currency.")" ,
									// trans('billing.card_amt')."(".$currency.")" ,
								)); /* column names */
							}
							/* getting last row number (the one we already filled and setting it to bold*/
								$sheet->row($sheet->getHighestRow(), function ($row) {
									$row->setFontWeight('bold');
								});
								if(sizeof($purchases)>0){
									if($this->intVatType != 0){
										foreach ($purchases as $o) {
											if($o->payment_mode == 1){
												$pay_mode = 'Cash';
											}else if($o->payment_mode == 2){
												$pay_mode = 'Credit & Cash';
											}else if($o->payment_mode == 3){
												$pay_mode = 'Credit';
											}else if($o->payment_mode == 4){
												$pay_mode = 'Credit Card';
											}else if($o->payment_mode == 5){
												$pay_mode = 'Dubai Mall Online';
											}
											if($o->customerName == null){
												$custName="NA";
											}else{
												$custName=$o->customerName;
											}
											$i++;
											$sheet->appendRow(array(
												'#' => $i,
												trans('billing.bill_date') => $o->dateTime,
												trans('billing.bill_no') => " ".Common::fixBill($o->id),
												trans('item.item_name')=>$o->itemName,
												trans('glob.customer') => $custName,
												trans('billing.sub_total')."(".$currency.")" => " ".Common::fixIt($o->subtotal),
												trans('glob.vat')."(".$currency.")" => " ".Common::fixIt($o->v_amt),
												trans('glob.discount')."(".$currency.")" => " ".Common::fixIt($o->dis_amt),
												trans('glob.loyalty_discount')."(".$currency.")" => " ".Common::fixIt($o->loyalty_discount),
												trans('billing.mode_of_pay') => $pay_mode,
												trans('billing.cash_amt')."(".$currency.")" => " ".Common::fixIt($o->cash_amt),
												trans('billing.card_amt')."(".$currency.")" => " ".Common::fixIt($o->card_amt),
											)
										);
											$totalCash = $o->cash_amt +$totalCash ;
											$totalCard = $o->card_amt +$totalCard ;
											$totalDiscount = $o->dis_amt + $totalDiscount;
											$totalLoyDis = $o->loyalty_discount + $totalLoyDis;
											$total = $o->subtotal + $total;
											$totalVat = $o->v_amt + $totalVat;
										}
									}else{
										foreach ($purchases as $o) {
											if($o->payment_mode == 1){
												$pay_mode = 'Cash';
											}else if($o->payment_mode == 2){
												$pay_mode = 'Credit & Cash';
											}else if($o->payment_mode == 3){
												$pay_mode = 'Credit';
											}else if($o->payment_mode == 4){
												$pay_mode = 'Credit Card';
											}else if($o->payment_mode == 5){
												$pay_mode = 'Dubai Mall Online';
											}
											if($o->customerName == null){
												$custName="NA";
											}else{
												$custName=$o->customerName;
											}
											$i++;
											$sheet->appendRow(array(
												'#' => $i,
												trans('billing.bill_date') => $o->dateTime,
												trans('billing.bill_no') => " ".Common::fixBill($o->id),
												trans('item.item_name')=>$o->itemName,
												trans('glob.customer') => $custName,
												trans('billing.sub_total')."(".$currency.")" => " ".Common::fixIt($o->subtotal),
												trans('glob.discount')."(".$currency.")" => " ".Common::fixIt($o->dis_amt),
												trans('glob.loyalty_discount')."(".$currency.")" => " ".Common::fixIt($o->loyalty_discount),
												trans('billing.mode_of_pay') => $pay_mode,
												trans('billing.cash_amt')."(".$currency.")" => " ".Common::fixIt($o->cash_amt),
												trans('billing.card_amt')."(".$currency.")" => " ".Common::fixIt($o->card_amt),
											)
										);
											$totalCash = $o->cash_amt +$totalCash ;
											$totalCard = $o->card_amt +$totalCard ;
											$totalDiscount = $o->dis_amt + $totalDiscount;
											$totalLoyDis = $o->loyalty_discount + $totalLoyDis;
											$total = $o->subtotal + $total;
											$totalVat = $o->v_amt + $totalVat;
										}
									}
								}
								if($this->intVatType != 0){
									$sheet->appendRow(array(
										'#' => '',
										trans('billing.bill_date') =>'',
										trans('billing.bill_no') => '',
										trans('item.itemName')=>'',
										trans('glob.customer') => '',
										trans('billing.sub_total')."(".$currency.")" =>  trans('billing.sub_total')." = ".Common::fixIt($total) ,
										trans('glob.vat')."(".$currency.")" =>  trans('glob.vat')." = ".Common::fixIt($totalVat) ,
										trans('glob.discount')."(".$currency.")" => trans('glob.discount')." = ".Common::fixIt($totalDiscount) ,
										trans('glob.loyalty_discount')."(".$currency.")" => trans('glob.discount')." = ".Common::fixIt($totalLoyDis),
										trans('billing.mode_of_pay') => '',
										trans('billing.cash_amt')."(".$currency.")" => trans('billing.cash_amt')." = ".Common::fixIt($totalCash) ,
										trans('billing.card_amt')."(".$currency.")" =>trans('billing.card_amt')." = ".Common::fixIt($totalCard) ,
									)
								);
								}else{
									$sheet->appendRow(array(
										'#' => '',
										trans('billing.bill_date') =>'',
										trans('billing.bill_no') => '',
										trans('item.itemName')=>'',
										trans('glob.customer') => '',
										trans('billing.sub_total')."(".$currency.")" =>  trans('billing.sub_total')." = ".Common::fixIt($total) ,
										trans('glob.discount')."(".$currency.")" => trans('glob.discount')." = ".Common::fixIt($totalDiscount) ,
										trans('glob.loyalty_discount')."(".$currency.")" => trans('glob.discount')." = ".Common::fixIt($totalLoyDis),
										trans('billing.mode_of_pay') => '',
										trans('glob.currency')  => '',
										trans('billing.cash_amt')."(".$currency.")" => trans('billing.cash_amt')." = ".Common::fixIt($totalCash) ,
										trans('billing.card_amt')."(".$currency.")" =>trans('billing.card_amt')." = ".Common::fixIt($totalCard) ,
									)
								);
								}
								$sheet->row($sheet->getHighestRow(), function ($row) {
									$row->setFontWeight('bold');
								});

								$sheet->appendRow(array('', ''));
								$sheet->appendRow(array(trans('glob.sales_with_vat'),Common::fixIt($total + $totalVat) ,));
								$sheet->row($sheet->getHighestRow(), function ($row) {
									$row->setFontWeight('bold');
								});		
								$sheet->appendRow(array(trans('glob.sales_without_vat'), Common::fixIt($total),));
								$sheet->row($sheet->getHighestRow(), function ($row) {
									$row->setFontWeight('bold');
								});	
							});
})->export('xlsx');
return back();
}

}catch(Exception $e){
	Log::error("SalesReportController@exportToCategorywiseSalesSummary ".$e);
	return View("errors.exception");
}
}



public function getCategorywiseSalesSummaryQuery($fromDate,$toDate,$category_id,$sub_category_id){
	try{
		$total 				=	0;
		$totalCash 			=	0;
		$totalCard 			=	0;
		$itemList 			=	[];	
		$pay_mode 			=	'';
		$totalDiscount 		=	0;
		$totalLoyDis 		=	0;
		$totalVat 			=	0;
		$category_id 		= 	$category_id ?? 0;
	    $sub_category_id 	= 	$sub_category_id ?? 0;
		$purchases 			=   DB::select("SELECT *,billings.id,DATE_FORMAT(billings.created_at,'".$this->rDateFormatSQL." %h:%i %p')as dateTime, 
			items.itemName as itemName,
		CASE WHEN $this->intVatType != 2 THEN 
			((((billings.dis_amt/(billings.subtotal + billings.v_amt))/100) * (billing_items.rate*billing_items.qty)) * 100) 
			WHEN $this->intVatType = 2 THEN 
			((((billings.dis_amt/(billings.subtotal + billings.v_amt))/100) * ( ( billing_items.rate + ((billing_items.rate * billing_items.vat)/100) ) * billing_items.qty)) * 100) 
			END  as dis_amt,
			((billing_items.rate - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) - (((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt))) - ((((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)))*100)/(100+(billing_items.vat)))) ) * billing_items.qty) as subtotal,
			(((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt))) - ((((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)))*100)/(100+(billing_items.vat)))) * (billing_items.qty) as v_amt,
			(select customers.name from customers where customers.id = billings.customer_id) as customerName,
			(select currencies.code from currencies where currencies.id = billings.currency) as currencyCode, 
			(case when billings.currency > 0 then (select currencies.amount from currencies where currencies.id = billings.currency) * billings.cash_amt else billings.cash_amt end)as cash_amt,
			(case when billings.currency > 0 then (select currencies.amount from currencies where currencies.id = billings.currency) * billings.card_amt else billings.card_amt end)as card_amt
			FROM billings 
			INNER JOIN billing_items on billing_items.billing_id=billings.id
			INNER JOIN items on items.id= billing_items.item_id
			INNER JOIN categories on categories.id=items.category
			where billings.billing_status <> 'hold' AND
			CASE WHEN $category_id > 0 THEN  categories.parent_category = $category_id ELSE true END and
		    CASE WHEN $sub_category_id > 0 THEN categories.id = $sub_category_id ELSE true END AND
			CAST(billings.created_at AS DATE) BETWEEN '".$fromDate."' and '".$toDate."'  ORDER BY billings.created_at ASC");
		if(sizeof($purchases)>0){
			foreach ($purchases as $o) {
				if($o->payment_mode == 1){
					$pay_mode = 'Cash';
				}else if($o->payment_mode == 2){
					$pay_mode = 'Card & Cash';
				}else if($o->payment_mode == 3){
					$pay_mode = 'Credit';
				}else if($o->payment_mode == 4){
					$pay_mode = 'Credit Card';
				}else if($o->payment_mode == 5){
					$pay_mode = 'Dubai Mall Online';
				}
				if($o->customerName == null){
					$custName="NA";
				}else{
					$custName=$o->customerName;
				}
				array_push($itemList, array(
					"id"  => Common::fixBill($o->id),
					"itemName"=>$o->itemName,
					"dateTime" => $o->dateTime,
					"customerName" => $custName,
					"subtotal" => Common::fixIt($o->subtotal),
					"vat_amt" => Common::fixIt($o->v_amt),
					"dis_amt" => Common::fixIt($o->dis_amt),
					"loydis_amt" => Common::fixIt($o->loyalty_discount),
					"payment_mode" => $pay_mode,
					"cash_amt" => Common::fixIt($o->cash_amt),
					"card_amt" =>Common::fixIt($o->card_amt),
				)
			);
				$totalCash =Common::fixIt($o->cash_amt) +$totalCash ;
				$totalCard = Common::fixIt($o->card_amt) +$totalCard ;
				$totalDiscount = Common::fixIt($o->dis_amt) + $totalDiscount;
				$totalLoyDis =  Common::fixIt($o->loyalty_discount) + $totalLoyDis;
				$total = Common::fixIt($o->subtotal) + $total;
				$totalVat = Common::fixIt($o->v_amt) + $totalVat;
			}
		}
		return array(
			'total' 			=>	$total,	
			'totalCash' 		=>	$totalCash,	
			'totalCard' 		=>	$totalCard,	
			'itemList' 			=>	$itemList,	
			'totalDiscount' 	=>	$totalDiscount,	
			'totalLoyDis' 		=>	$totalLoyDis,	
			'totalVat' 			=>	$totalVat,	
			'purchases'			=>	$purchases,
		);

	}catch(Exception $e){
		Log::error("SalesReportController@getCategorywiseSalesSummaryQuery ".$e);
		return View("errors.exception");
	}
}


/**
	 * Display a listing of the resource. Used for tax_refund_Report
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function taxRefund(Request $request){

		$categoriesList = Category::all();		
		
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
		$date1 = Carbon::now()->format('m/d/Y');
		$mallDetails = $this->mallDetails();
		$res = array(
			"categoriesList" => $categoriesList,
			"categories" =>$categories,
			"sub_categories" => $sub_categories,
			"date" => $date,
			"date1" => $date1,
			"mallDetails" => $mallDetails,
		);
	
		if($request->ajax()){
			return Response($res,200);
		}else{
			return View('pages.sales_reports.tax_refund.tax_refund', $res);
		}
	}
	
	/**
		 * Select data which is displayed on report view table . used for Cash_Memo_Catagorywise_Report
		 * @param  REquest $request [description]
		 * @return [type]           [description]
		 */
	public function getLedgerTaxRefund(Request $request){		
		$dateArray 			= 	$request->all()["data"]; 
		$fromDate 			= 	new Carbon($dateArray['fromDate']);
		$toDate 			= 	new Carbon($dateArray['toDate']);		
		$category_id 		= 	$dateArray['category_id'];
		$sub_category_id 	= 	$dateArray['sub_category_id'];
		$total 				=	0;
		$totalDis 			=	0;
		$itemList 			= 	[];
		$discountPercent 	=	0;
		$totalQty 			=	0;
		$totalRate 			=	0;
		$totalLoyDis 		=	0;
		$totalGroassSale=0;
		$totalVatValue=0;
		$data 				= 	$this->getTaxRefundQuery($fromDate,$toDate,$category_id,$sub_category_id);
		$labels 			= 	$this->getLabels();
		$total 				=	$data['total'];
		$totalDis 			=	$data['totalDis'];
		$totalQty 			=	$data['totalQty'];
		$totalRate 			=	$data['totalRate'];
		$totalLoyDis 		=	$data['totalLoyDis'];
		$purchases 			=	$data['purchases'];
		$totalGroassSale= $data['totalGroassSale'];
	    $totalVatValue=$data['totalVatValue'];
		$res = array(
			"total" 		=>	$total,
			"itemList"		=> 	$itemList,
			"totalDis" 		=> 	$totalDis,
			"totalLoyDis" 	=> 	$totalLoyDis,
			"totalQty" 		=>	$totalQty,	
			"totalRate" 	=>	$totalRate,	
			"purchases" 	=> 	$purchases,
			"labels" 		=> 	$labels,	
			"toDatePrint" 	=> 	(new Carbon($toDate))->format($this->rDateFormat),
			"fromDatePrint" => 	(new Carbon($fromDate))->format($this->rDateFormat),		
			"totalGroassSale"=>$totalGroassSale,
			"totalVatValue"=>$totalVatValue

		);
	
		if($request->ajax()){
			return Response($res,200);
		}else{
			return View("pages.sales_reports.tax_refund.tax_refund",$res);
		}
	}
	
		/**
		 * Function created for create and export exel and pdf file . used for Cash_Memo_Categorywise_Report
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function exportToTaxRefund(Request $request){				
			$dateArray 			= 	$request->all(); 
			$fromDate 			= 	new Carbon($dateArray['from']);
			$toDate 			= 	new Carbon($dateArray['to']);
			$category_id 		= 	$dateArray['category'];
			$sub_category_id 	= 	$dateArray['sub_category'];
			$total 				=	0;
			$totalDis 			=	0;
			$itemList 			= 	[];
			$discountPercent 	=	0;
			$totalQty 			=	0;
			$totalRate 			=	0;
			$totalLoyDis 		=	0;
			$totalGroassSale=0;
			$totalVatValue=0;
			$currency 			=	$this->optioncurr;
			$data 				= 	$this->getTaxRefundQuery($fromDate,$toDate,$category_id,$sub_category_id);
			$fromDate 			= 	date($this->rDateFormat, strtotime($fromDate));
			$toDate 			= 	date($this->rDateFormat, strtotime($toDate));
			$total 				=	$data['total'];
			$totalDis 			=	$data['totalDis'];
			$totalQty 			=	$data['totalQty'];
			$totalRate 			=	$data['totalRate'];
			$totalLoyDis 		=	$data['totalLoyDis'];
			$purchases 			=	$data['purchases'];
			$totalGroassSale= $data['totalGroassSale'];
	        $totalVatValue=$data['totalVatValue'];
			
			if(Input::get('pdf')){	
				$pdf = PDF::loadView('pages.sales_reports.tax_refund.tax_refund_pdf', [			
					"total" 		=> 	$total,			
					"totalDis" 		=> 	$totalDis,
					"totalLoyDis" 	=> 	$totalLoyDis,
					"totalQty" 		=>	$totalQty,	
					"totalRate" 	=>	$totalRate,	
					"purchases" 	=> 	$purchases,
					"fromDate" 		=> 	$fromDate,
					"toDate" 		=> 	$toDate,
					"i"  			=>	'1',
					"curr" 			=> 	$currency,
					"totalGroassSale"=>$totalGroassSale,
					"totalVatValue"=>$totalVatValue,

				]); 
				$pdf->setPaper('a4');
				$carbon = Carbon::today();
				$timestamp = $carbon->timestamp;
				$format = $carbon->format($this->rDateFormat);
				$exportName = 'taxRefund-'.$format.'.pdf';     
				return $pdf->download($exportName);
			}elseif (Input::get('xls')) {
				$carbon = Carbon::today();
				$timestamp = $carbon->timestamp;
				$format = $carbon->format($this->rDateFormat);
				$exportName = 'taxRefund-'.$format; 
				Excel::create($exportName, function ($excel) 
					use($purchases,
						$totalDis,
						$totalLoyDis,
						$totalQty,
						$totalRate,
						$total,
						$fromDate,
						$toDate,
						$currency,
						$totalGroassSale,
						$totalVatValue) {
						$excel->sheet('taxRefund', function ($sheet) 
							use($purchases,
								$totalDis,
								$totalLoyDis,
								$totalQty,
								$totalRate,
								$total,
								$fromDate,
								$toDate,
								$currency,
								$totalGroassSale,
						        $totalVatValue) {
								$i=0;
								$itemList = array();
								$sheet->appendRow(array('', ''));
								$sheet->appendRow(array(trans('glob.from'), $fromDate ,trans('glob.to'),$toDate));
								$sheet->row($sheet->getHighestRow(), function ($row) {
									$row->setFontWeight('bold');
								});	
								$sheet->appendRow(array('', ''));
								/* setting column names for data - you can of course set it manually*/
								$sheet->appendRow(array(
									"#" ,
									trans('billing.bill_no')  ,
									trans('billing.bill_date') ,
									trans('glob.taxRefundTagNumber'),
									trans('glob.customer') ,
									trans('item.item_name') ,
									trans('glob.barcode') ,
									trans('item.refrence_number') ,
									// trans('item.serial_number') ,
									trans('glob.rate') ,
									trans('item.qty') ,								
									// trans('billing.dis_amt')."(".$currency.")" ,
									// trans('glob.loyalty_discount')."(".$currency.")"  ,
									trans('glob.grossSale')."(".$currency.")",
									trans('glob.vatValue')."(".$currency.")",
									trans('glob.netSale')."(".$currency.")" ,
								)); /* column names*/
								/* getting last row number (the one we already filled and setting it to bold*/
									$sheet->row($sheet->getHighestRow(), function ($row) {
										$row->setFontWeight('bold');
									});
									if(sizeof($purchases)>0){
										foreach ($purchases as $p) {
											$sheet->appendRow( array(
												"#" => $p->name,
												trans('billing.bill_no')  => '',
												trans('billing.bill_date') => '',
												trans('glob.taxRefundTagNumber'),
												trans('glob.customer') => '',
												trans('item.item_name') => '',
												trans('glob.barcode') => '',
												trans('item.refrence_number') => '',
												// trans('item.serial_number') => '',
												trans('glob.rate') => '',
												trans('item.qty') =>'',								
												// trans('billing.dis_amt')."(".$currency.")"  => '',
												// trans('glob.loyalty_discount')."(".$currency.")"  => '',
												trans('glob.grossSale')."(".$currency.")"=>'',
												trans('glob.vatValue')."(".$currency.")"=>'' ,
												trans('glob.netSale')."(".$currency.")" => '',
											)
										);
											$i=0;
											foreach ($p->child as $o) {
												$i++;
												$sheet->appendRow( array(
													"#" => $i,
													trans('glob.cash_memo_no')  => " ".Common::fixBill($o->id),
													trans('glob.cash_memo_date') => $o->billingDate,
													trans('glob.taxRefundTagNumber')=>$o->taxRefundTagNumber,
													trans('glob.customer') => $o->customerName ?? 'NA',
													trans('item.item_name') => $o->itemName,
													trans('glob.barcode') => $o->barcode,
													trans('item.refrence_number') => $o->refrence_number ?? 'NA',
													//trans('item.serial_number') => $o->serial_number ?? 'NA',
													trans('glob.rate') => " ".Common::fixIt($o->rate),
													trans('item.qty') => " ".Common::fixQty($o->qty),									
													// trans('billing.dis_amt')."(".$currency.")" => " ".Common::fixIt($o->dis_amt),
													// trans('glob.loyalty_discount')."(".$currency.")" => " ".Common::fixIt($o->loydis_amt),
													trans('glob.grossSale')."(".$currency.")"=> " ".Common::fixIt($o->grossSale),	
													trans('glob.vatValue')."(".$currency.")" => " ".Common::fixIt($o->vat_value),	
													trans('glob.netSale')."(".$currency.")" => " ".Common::fixIt($o->amt - $o->loydis_amt),
												)
											);
											}
											$sheet->appendRow( array(
												"#"  => '',
												trans('glob.cash_memo_no')  => '',
												trans('glob.cash_memo_date') => '',
												trans('glob.taxRefundTagNumber')=>'',
												trans('glob.customer') => '',
												trans('item.item_name') => '',
												trans('glob.barcode') => '',
												trans('item.refrence_number') => '',
												//trans('item.serial_number') => '',
												trans('glob.rate') => '',
												trans('item.qty') => trans('item.qty')." = ".Common::fixIt($p->qty),								
												//trans('billing.dis_amt')."(".$currency.")" => trans('billing.dis_amt')." = ". Common::fixIt($p->dis_amt),
												//trans('glob.loyalty_discount')."(".$currency.")" => trans('glob.loyalty_discount')." = ". Common::fixIt($p->loydis_amt),	
												trans('glob.grossSale')."(".$currency.")" => trans('glob.grossSale')." = ".Common::fixIt($p->totalgrossSale),					
												trans('glob.vatValue')."(".$currency.")" => trans('glob.vatValue')." = ".Common::fixIt($p->totalvatValue),	
												trans('glob.netSale')."(".$currency.")" => trans('glob.netSale')." = ".Common::fixIt($p->amt - $p->loydis_amt),			
											)
										);
										}
									}	
									$sheet->appendRow( array(
										"#"  => '',
										trans('glob.cash_memo_no')  => '',
										trans('glob.cash_memo_date') => '',
										trans('glob.taxRefundTagNumber')=>'',
										trans('glob.customer_name') => '',
										trans('item.item_name') => '',
										trans('glob.barcode') => '',
										trans('item.refrence_number') => '',
										//trans('item.serial_number') => '',
										trans('glob.rate') => '',
										trans('item.qty') => '',					
										// trans('billing.dis_amt')."(".$currency.")" => " ".Common::fixIt($totalDis),
										// trans('glob.loyalty_discount')."(".$currency.")" => " ".Common::fixIt($totalLoyDis),	
										trans('glob.grossSale')."(".$currency.")"=>trans('glob.total'),
										trans('glob.vatValue')."(".$currency.")"=>" ".Common::fixIt($totalVatValue),
										// trans('glob.grossSale')."(".$currency.")"=>" ".Common::fixIt($totalGroassSale),
										//trans('glob.vatValue')."(".$currency.")"=>" ".Common::fixIt($totalVatValue),
										// trans('glob.netSale')."(".$currency.")" => " ".Common::fixIt($total - $totalLoyDis),			
										trans('glob.netSale')."(".$currency.")" =>''
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
	
	public function getTaxRefundQuery($fromDate,$toDate,$category_id,$sub_category_id){
		$totalQty 			=	0;
		$totalDis 			=	0; 
		$totalLoyDis 		=	0; 
		$totalRate 			=	0;
		$total 				=	0; 
		$totalGroassSale=0;
		$totalVatValue=0;
		$category_id 		= 	$category_id ?? 0;
		$sub_category_id	= 	$sub_category_id ?? 0;
		$purchases 			= 	DB::select("SELECT c.id,c.categorie as name,sum(bi.qty) as qty,sum(bi.rate) as rate  ,
			sum((bi.rate - ((((b.dis_amt/b.subtotal)*100)*bi.rate)/100)) * bi.qty) as amt,sum(b.bil_amt) as bil_amt,
			sum(((((b.dis_amt/b.subtotal)*100)*bi.rate)/100) * bi.qty) as dis_amt,
			sum(((((b.loyalty_discount/b.subtotal)*100)*bi.rate)/100) * bi.qty) as loydis_amt,
			sum((bi.rate - ((b.dis_amt * bi.rate)/(b.subtotal + b.v_amt)) - (((bi.rate) - ((b.dis_amt * bi.rate)/(b.subtotal + b.v_amt))) - ((((bi.rate) - ((b.dis_amt * bi.rate)/(b.subtotal + b.v_amt)))*100)/(100+(bi.vat)))) ) * bi.qty) as totalgrossSale,
			SUM((((bi.rate) - ((b.dis_amt * bi.rate)/(b.subtotal + b.v_amt))) - ((((bi.rate) - ((b.dis_amt * bi.rate)/(b.subtotal + b.v_amt)))*100)/(100+(bi.vat)))) * (bi.qty)) as totalvatValue
			FROM billing_items bi  INNER JOIN billings b ON b.id = bi.billing_id 
			INNER Join billing_tax_refunds bt on bt.billId=b.id
			INNER JOIN items i ON i.id = bi.item_id INNER JOIN categories c ON i.category = c.id
			where  b.billing_status <> 'hold' AND b.tax_refund_flag=1 AND
			CASE WHEN $category_id > 0 THEN  c.parent_category = $category_id ELSE true END and
			CASE WHEN $sub_category_id > 0 THEN c.id = $sub_category_id ELSE true END and 
			CAST(b.created_at AS DATE) BETWEEN '".$fromDate."' and '".$toDate."' GROUP BY c.id");
		foreach ($purchases as $row) {		 	
			$p=DB::select("SELECT b.id,DATE_FORMAT(b.created_at,'".$this->rDateFormatSQL."') billingDate, 
				b.bil_amt,bi.qty,bi.rate, i.itemName,i.barcode,bi.serial_no as serial_number,i.serial_number as refrence_number,
				(((((b.dis_amt/b.subtotal)*100)*bi.rate)/100 ) * bi.qty) as dis_amt ,
				(((((b.loyalty_discount/b.subtotal)*100)*bi.rate)/100 ) * bi.qty) as loydis_amt ,
				((bi.rate - ((((b.dis_amt/b.subtotal)*100)*bi.rate)/100)) * bi.qty) as amt ,
				((bi.rate - ((b.dis_amt * bi.rate)/(b.subtotal + b.v_amt)) - (((bi.rate) - ((b.dis_amt * bi.rate)/(b.subtotal + b.v_amt))) - ((((bi.rate) - ((b.dis_amt * bi.rate)/(b.subtotal + b.v_amt)))*100)/(100+(bi.vat)))) ) * bi.qty)as grossSale,
				(((bi.rate) - ((b.dis_amt * bi.rate)/(b.subtotal + b.v_amt))) - ((((bi.rate) - ((b.dis_amt * bi.rate)/(b.subtotal + b.v_amt)))*100)/(100+(bi.vat)))) * (bi.qty) as vat_value,bt.taxRefundTagNumber,
				(SELECT customers.name FROM customers WHERE customers.id = b.customer_id) as customerName
				FROM billing_items bi  INNER JOIN billings b ON b.id = bi.billing_id INNER JOIN items i ON i.id = bi.item_id 
				INNER Join billing_tax_refunds bt on bt.billId=b.id
				where  b.billing_status <> 'hold' AND b.tax_refund_flag=1 AND
				i.category = ".$row->id." and 
				CAST(b.created_at AS DATE) BETWEEN '".$fromDate."' and '".$toDate."'  ORDER BY b.created_at ASC");
			$row->child 	= 	$p;
			$totalQty 		= 	$row->qty + $totalQty;
			$totalDis 		= 	$row->dis_amt +$totalDis ;
			$totalLoyDis 	= 	$row->loydis_amt +$totalLoyDis;
			$totalRate 		= 	$row->rate +$totalRate ;
			$total 			= 	$row->amt + $total;
			$totalGroassSale=$row->totalgrossSale + $totalGroassSale;
			$totalVatValue=$row->totalvatValue+$totalVatValue;
		}
		return array(
			'purchases' 	=>  $purchases,
			'totalQty' 		=>	$totalQty,
			'totalDis' 		=>	$totalDis, 
			'totalLoyDis' 	=>	$totalLoyDis, 
			'totalRate' 	=>	$totalRate,
			'total' 		=>	$total, 
			'totalGroassSale'=>$totalGroassSale,
			'totalVatValue'=>$totalVatValue
		);
	}



	
	public function indexSalesandSalesReturn(Request $request){
		try{
	
		$categoriesList = Category::all();		
		
		$categories= array();
		$categories1= Category::where("parent_category","=", 0)->get();
		//$categories[] = (object) array('id'=>'0','categorie' => 'All' ,'parent_category' => '0');
		foreach ($categories1 as $row) {
			$categories[]=$row;
		}
		$sub_categories = array();
		$sub_categories1= Category::where("parent_category","<>", 0)->get();	
		// $sub_categories[] = (object) array('id'=>'0','categorie' => 'All' ,'parent_category' => '0');
		foreach ($sub_categories1 as $row) {
			$sub_categories[]=$row;
		}
	
			$date = Carbon::now()->format('m/d/Y');
			$date1 = Carbon::now()->format('m/d/Y');
			$mallDetails = $this->mallDetails();
			$res = array(
				"date" => $date,
				"date1" => $date1,
				"mallDetails" => $mallDetails,
				"categories"=>$categories,
				"sub_categories"=>$sub_categories,
				"categoriesList" => $categoriesList,
	
			);
	
			if($request->ajax()){
				return Response($res,200);
			}else{
				return View('pages.sales_reports.brandwise_sales_and_sales_return.brandwise_sales_and_sales_return', $res);
			}
	
		}catch(Exception $e){
			Log::error("SalesReportController@indexSalesandSalesReturn ".$e);
			return View("errors.exception");
		}
	}
	
	/**
		 * Select data which is displayed on report view table . used for Sales_Summary_Report
		 * @param  REquest $request [description]
		 * @return [type]           [description]
		 */
	public function getLedgerSalesandSalesReturn(REquest $request){
		try{		
			$dateArray 			=	$request->all()["data"]; 
			$fromDate 			=	new Carbon($dateArray['fromDate']);
			$toDate 			=	new Carbon($dateArray['toDate']);
			$category_id 		= 	$dateArray['category_id'];
		$sub_category_id 	= 	$dateArray['sub_category_id'];		
			$total 				=	0;
			$totalCash 			=	0;
			$totalCard 			=	0;
			$itemList 			=	[];	
			$pay_mode 			=	'';
			$totalDiscount 		=	0;
			$totalLoyDis 		=	0;
			$totalVat 			=	0;
			 $data 				= 	$this->getBrandwiseSalesandSalesReturnQuery($fromDate,$toDate,$category_id,$sub_category_id);
				$saletotal 		=	$data['saletotal'];
				$saletotalDiscount 	=	$data['saletotalDiscount'];
				$saletotalLoyDis	=	$data['saletotalLoyDis'];	
				$saletotalVat 		=	$data['saletotalVat'];	
				$purchases		=	$data['purchases'];
			   $returntotalDiscount =$data['returntotalDiscount'];
			   $returntotalLoyaltyDiscount =$data['returntotalLoyaltyDiscount'];
			
				$returntotal=$data['returntotal'];
				$returntotalVat=$data['returntotalVat'];
			
			$labels 			= 	$this->getLabels();				
			$res = array(
				"saletotal" 		=>	Common::fixIt($saletotal),
				"saletotalDiscount" 	=>	Common::fixIt($saletotalDiscount),
				"saletotalLoyDis"	=>	Common::fixIt($saletotalLoyDis),
				"saletotalVat" 		=>	Common::fixIt($saletotalVat),	
				"purchases"		=>	$purchases,
			   "returntotalDiscount" =>Common::fixIt($returntotalDiscount),
			   "returntotalLoyaltyDiscount" =>Common::fixIt($returntotalLoyaltyDiscount),
			   "returntotal"=>Common::fixIt($returntotal),
			   "returntotalVat"=>Common::fixIt($returntotalVat),
			   "labels"		=> 	$labels,	
			   "toDatePrint" 	=> 	(new Carbon($toDate))->format($this->rDateFormat),
			   "fromDatePrint" => 	(new Carbon($fromDate))->format($this->rDateFormat),	
			);
			if($request->ajax()){
				return Response($res,200);
			}else{
				return View("pages.sales_reports.brandwise_sales_and_sales_return.brandwise_sales_and_sales_return",$res);
			}
	
		}catch(Exception $e){
			Log::error("SalesReportController@getLedgerSalesandSalesReturn ".$e);
			return View("errors.exception");
		}
	}
	
	/**
		 * Function created for create and export exel and pdf file . used for Sales_Summary_Report
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
	public function exportToBrandwiseSalesandSalesReturn(Request $request){	
		try{	
	
			$dateArray 		= 	$request->all(); 
			$fromDate 		= 	new Carbon($dateArray['from']);
			$toDate 		= 	new Carbon($dateArray['to']);
			$category_id 		= 	$dateArray['category'];
			$sub_category_id 	= 	$dateArray['sub_category'];	
			$total 			=	0;
			$totalCash 		=	0;
			$totalCard 		=	0;
			$itemList  		= 	[];	
			$pay_mode  		= 	'';
			$totalDiscount 	=	0;
			$totalLoyDis 	=	0;
			$totalVat 		=	0;
			$purchases=array();
			$currency 		=	$this->optioncurr;
			$data  			= 	$this->getBrandwiseSalesandSalesReturnQuery($fromDate,$toDate,$category_id,$sub_category_id);
				$saletotal 		=	$data['saletotal'];
				$saletotalDiscount 	=	$data['saletotalDiscount'];
				$saletotalLoyDis	=	$data['saletotalLoyDis'];	
				$saletotalVat 		=	$data['saletotalVat'];	
				$purchases		=	$data['purchases'];
			   $returntotalDiscount =$data['returntotalDiscount'];
			   $returntotalLoyaltyDiscount =$data['returntotalLoyaltyDiscount'];
			
				$returntotal=$data['returntotal'];
				$returntotalVat=$data['returntotalVat'];
			if(Input::get('pdf')){	
				$pdf = PDF::loadView('pages.sales_reports.brandwise_sales_and_sales_return.brandwise_sales_and_sales_return_pdf', [
				"saletotal" 		=>	Common::fixIt($saletotal),
				"saletotalDiscount" 	=>Common::fixIt($saletotalDiscount),
				"saletotalLoyDis"	=>	Common::fixIt($saletotalLoyDis),	
				"saletotalVat" 		=>	Common::fixIt($saletotalVat),	
				"purchases"		=>	$purchases,
				"returntotalDiscount" =>Common::fixIt($returntotalDiscount),
				"returntotalLoyaltyDiscount" =>Common::fixIt($returntotalLoyaltyDiscount),
				"returntotal"=>Common::fixIt($returntotal),
				"returntotalVat"=>Common::fixIt($returntotalVat),
					"fromDate" 		=> 	(new Carbon($fromDate))->format($this->rDateFormat)  ,
					"toDate" 		=> 	(new Carbon($toDate))->format($this->rDateFormat) ,
					"i" 			=>	'1',
					"curr"			=>	$currency,
					'intVatType'	=> 	$this->intVatType,
				]); 
				$pdf->setPaper('a4');
				$carbon = Carbon::today();
				$timestamp = $carbon->timestamp;
				$format = $carbon->format($this->rDateFormat);
				$exportName = 'BrandwiseSalesAndSalesReturn-'.$format.'.pdf';     
				return $pdf->download($exportName);
			}elseif (Input::get('xls')) {
				$carbon = Carbon::today();
				$timestamp = $carbon->timestamp;
				$format = $carbon->format($this->rDateFormat);
				$exportName = 'BrandwiseSalesandSalesReturn-'.$format; 
				Excel::create($exportName, function ($excel) 
					use($purchases,$saletotal,$saletotalVat,$saletotalDiscount,$saletotalLoyDis,$returntotal,$returntotalVat,$returntotalDiscount,$returntotalLoyaltyDiscount,
						$fromDate,
						$toDate,$category_id,$sub_category_id,
						$currency) {
						$excel->sheet('BrandwiseSalesAndSalesReturn', function ($sheet) 
							use($purchases,$saletotal,$saletotalVat,$saletotalDiscount,$saletotalLoyDis,$returntotal,$returntotalVat,$returntotalDiscount,$returntotalLoyaltyDiscount,
								$fromDate,
								$toDate,$category_id,$sub_category_id,
								$currency) {
								
							$sheet->appendRow(array('', ''));
							$sheet->appendRow(array(trans('glob.from'), ($fromDate) ,trans('glob.to'),($toDate) ));
							$sheet->row($sheet->getHighestRow(), function ($row) {
								$row->setFontWeight('bold');
								
							});	
							$sheet->appendRow(array('', ''));
							
							$itemList = array();
							foreach ($purchases as $p) {						
							   if($this->intVatType!=0){
								array_push($itemList, array(
									"#"  => $p->name,	
									trans('billing.bill_date') => '',						trans('	billing.bill_no') => '',
									trans('item.item_name') => '',
									trans('glob.customer')=>'',
									trans('billing.sub_total')." (".$currency.")" => '',
									trans('glob.vat')." (".$currency.")" => '',
									trans('glob.discount')." (".$currency.")" => '',							
									trans('glob.loyalty_discount')." (".$currency.")" => '',
									trans('billing.mode_of_pay') => '',
								));	
							}
							else{
								array_push($itemList, array(
									"#"  => $p->name,	
									trans('billing.bill_date') => '',						trans('billing.bill_no') => '',
									trans('item.item_name') => '',
									trans('glob.customer')=>'',
									trans('billing.sub_total')." (".$currency.")" => '',
									trans('glob.discount')." (".$currency.")" => '',							
									trans('glob.loyalty_discount')." (".$currency.")" => '',
									trans('billing.mode_of_pay') => '',
								));
	
							}
								$i=1;
									
								foreach ($p->child as $o) {						
									
									if($p->id!=1){
										$id = " ".$o->BillId;
									}else{
										$id = " ".Common::fixBill($o->BillId);
									}
	
									   if($this->intVatType!=0){
										   array_push($itemList, array(
									"#"  => $i++,	
									trans('billing.bill_date') => $o->dateTime,		
									trans('billing.bill_no') => $id,
									trans('item.item_name') => $o->itemName,
									trans('glob.customer')=> $o->customerName,
									trans('billing.sub_total')=>" ".Common::fixIt($o->subtotal),
									trans('glob.vat')=> " ".Common::fixIt($o->v_amt),
									trans('glob.discount')=> " ".Common::fixIt($o->dis_amt),
									trans('glob.loyalty_discount')=> " ".Common::fixIt($o->loyalty_discount),
									trans('billing.mode_of_pay') => $o->payment_mode,
								));	
									   }
									   else{
									array_push($itemList, array(
									"#"  => $i++,	
									trans('billing.bill_date') => $o->dateTime,		
									trans('billing.bill_no') => $id,
									trans('item.item_name') => $o->itemName,
									trans('glob.customer')=> $o->customerName,
									trans('billing.sub_total')=>" ".Common::fixIt($o->subtotal),
									trans('glob.discount')=> " ".Common::fixIt($o->dis_amt),
									trans('glob.loyalty_discount')=> " ".Common::fixIt($o->loyalty_discount),
									trans('billing.mode_of_pay') => $o->payment_mode,
								));	
									   }
								}
								
								
									 if($this->intVatType!=0){
										 if($p->id==1){
											 array_push($itemList, array(
										 
									"#"  => '',	
									trans('billing.bill_date') => '',		
									trans('billing.bill_no') => '',
									trans('item.item_name') => '',
									trans('glob.customer')=> '',
									trans('billing.sub_total')=>trans('billing.sub_total')."=".Common::fixIt($saletotal),
									trans('glob.vat')=>trans('glob.total_s_p')."=".Common::fixIt($saletotalVat),
									trans('glob.discount')=> trans('glob.discount')."=".Common::fixIt($saletotalDiscount),
									trans('glob.loyalty_discount')=> trans('glob.loyalty_discount')."=".Common::fixIt($saletotalLoyDis),
									trans('billing.mode_of_pay') => '',
								));	
										 }
										 else{
									 array_push($itemList, array(
										 
									"#"  => '',	
									trans('billing.bill_date') => '',		
									trans('billing.bill_no') => '',
									trans('item.item_name') => '',
									trans('glob.customer')=> '',
									trans('billing.sub_total')=>trans('billing.sub_total')."=".Common::fixIt($returntotal),
									trans('glob.vat')=>trans('glob.total_s_p')."=".Common::fixIt($returntotalVat),
									trans('glob.discount')=> trans('glob.discount')."=".Common::fixIt($returntotalDiscount),
									trans('glob.loyalty_discount')=> trans('glob.loyalty_discount')."=".Common::fixIt($returntotalLoyaltyDiscount),
									trans('billing.mode_of_pay') =>'',
								));	
										 }
										   
									   }
									   else{
												  if($p->id==1){
											 array_push($itemList, array(
										 
									"#"  => '',	
									trans('billing.bill_date') => '',		
									trans('billing.bill_no') => '',
									trans('item.item_name') => '',
									trans('glob.customer')=> '',
									trans('billing.sub_total')=>trans('billing.sub_total')."=".Common::fixIt($saletotal),
									trans('glob.discount')=> trans('glob.discount')."=".Common::fixIt($saletotalDiscount),
									trans('glob.loyalty_discount')=> trans('glob.loyalty_discount')."=".Common::fixIt($saletotalLoyDis),
									trans('billing.mode_of_pay') => '',
								));	
										 }
										 else{
									 array_push($itemList, array(
										 
									"#"  => '',	
									trans('billing.bill_date') => '',		
									trans('billing.bill_no') => '',
									trans('item.item_name') => '',
									trans('glob.customer')=> '',
									trans('billing.sub_total')=>trans('billing.sub_total')."=".Common::fixIt($returntotal),
									trans('glob.discount')=> trans('glob.discount')."=".Common::fixIt($returntotalDiscount),
									trans('glob.loyalty_discount')=> trans('glob.loyalty_discount')."=".Common::fixIt($returntotalLoyaltyDiscount),
								));	
										 }
										  
									   }
	
							}					
							
	
							
	
							/* setting column names for data - you can of course set it manually*/
							$sheet->appendRow(array_keys($itemList[0])); /* column names*/
	
	
							/* getting last row number (the one we already filled and setting it to bold*/
							$sheet->row($sheet->getHighestRow(), function ($row) {
								$row->setFontWeight('bold');
							});
	
							/* putting users data as next rows*/
							foreach ($itemList as $user) {
								$sheet->appendRow($user);
							}
	
							$sheet->appendRow(array('', ''));
							$sheet->appendRow(array(trans('glob.total').' '.trans('glob.sales_with_vat')."=".Common::fixIt($saletotal+$saletotalVat), '' , '' , '' , '' , '' , '' ,'','',trans('glob.total').' '.trans('glob.sales_return_with_vat')."=".Common::fixIt($returntotal+$returntotalVat)));
							$sheet->row($sheet->getHighestRow(), function ($row) {
								$row->setFontWeight('bold');						
							});
	
							$sheet->appendRow(array(trans('glob.total').' '.trans('glob.sales_without_vat')."=".Common::fixIt($saletotal), '' , '' , '' , '' , '' , '' ,'','',trans('glob.total').' '.trans('glob.sales_return_without_vat')."=".Common::fixIt($returntotal)));
							$sheet->row($sheet->getHighestRow(), function ($row) {
								$row->setFontWeight('bold');						
							});
	
	
							
	
						});
	
	})->export('xlsx');
	
								
	return back();
	}
	
	}catch(Exception $e){
		Log::error("SalesReportController@exportToBrandwiseSalesandSalesReturn ".$e);
		return View("errors.exception");
	}
	}
	
	
	
	public function getBrandwiseSalesandSalesReturnQuery($fromDate,$toDate,$category_id,$sub_category_id){
		try{
			$saletotal 				=	0;
			$saletotalCash 			=	0;
			$saletotalCard 			=	0;
			$itemList 			=	[];	
			$pay_mode 			=	'';
			$saletotalDiscount 		=	0;
			$saletotalLoyDis 		=	0;
			$saletotalVat 			=	0;
			$category_id 		= 	$category_id ?? 0;
			$sub_category_id 	= 	$sub_category_id ?? 0;
			//return variables
			$returntotal 					=	0;
			$returntotalCash 				=	0;
			$returntotalCard 				=	0;
			$returnitemList  				=	array();	
			
			$returntotalDiscount 			=	0;
			$returntotalLoyaltyDiscount	=	0;
			$returnpercentage 			=	0;
			$returndisAmt 				=	0;
			$returnpercentageLoyalty 		=	0;
			$returndisLoyaltyAmt 			=	0;
			$returncashAmt 				=	0;
			$returncardAmt 				=	0;
			$returntotalReturn 			=	0;
			//$billReturn 			=	[];
			$returntotalVatAmt 			=	0;
			$returnfloatTotalVatAmt 		=	0;
			$returntotalVat=0;
			
				$purchases[0] =  (object) array('id'=> 1,'name'=>'Sale');
				$purchases[1] =  (object) array('id'=> 2,'name'=>'Sale Return');
					foreach ($purchases as $row) {
							if($row->id == 1){
							  $arrPurchase= DB::select("SELECT *,billings.id as BillId,DATE_FORMAT(billings.created_at,'".$this->rDateFormatSQL." %h:%i %p')as dateTime, 
				items.itemName as itemName,
			CASE WHEN $this->intVatType != 2 THEN 
				((((billings.dis_amt/(billings.subtotal + billings.v_amt))/100) * (billing_items.rate*billing_items.qty)) * 100) 
				WHEN $this->intVatType = 2 THEN 
				((((billings.dis_amt/(billings.subtotal + billings.v_amt))/100) * ( ( billing_items.rate + ((billing_items.rate * billing_items.vat)/100) ) * billing_items.qty)) * 100) 
				END  as dis_amt,
				((billing_items.rate - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) - (((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt))) - ((((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)))*100)/(100+(billing_items.vat)))) ) * billing_items.qty) as subtotal,
				(((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt))) - ((((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)))*100)/(100+(billing_items.vat)))) * (billing_items.qty) as v_amt,
				(select customers.name from customers where customers.id = billings.customer_id) as customerName,
				(select currencies.code from currencies where currencies.id = billings.currency) as currencyCode, 
				(case when billings.currency > 0 then (select currencies.amount from currencies where currencies.id = billings.currency) * billings.cash_amt else billings.cash_amt end)as cash_amt,
				(case when billings.currency > 0 then (select currencies.amount from currencies where currencies.id = billings.currency) * billings.card_amt else billings.card_amt end)as card_amt
				FROM billings 
				INNER JOIN billing_items on billing_items.billing_id=billings.id
				INNER JOIN items on items.id= billing_items.item_id
				INNER JOIN categories on categories.id=items.category
				where billings.billing_status <> 'hold' AND
				CASE WHEN $category_id > 0 THEN  categories.parent_category = $category_id ELSE true END and
				CASE WHEN $sub_category_id > 0 THEN categories.id = $sub_category_id ELSE true END AND
				CAST(billings.created_at AS DATE) BETWEEN '".$fromDate."' and '".$toDate."'  ORDER BY billings.created_at ASC");
			if(sizeof($arrPurchase)>0){
				foreach ($arrPurchase as $o) {
					if($o->payment_mode == 1){
						$pay_mode = 'Cash';
						$o->payment_mode=$pay_mode;
					}else if($o->payment_mode == 2){
						$pay_mode = 'Card & Cash';
						$o->payment_mode=$pay_mode;
					}else if($o->payment_mode == 3){
						$pay_mode = 'Credit';
						$o->payment_mode=$pay_mode;
					}else if($o->payment_mode == 4){
						$pay_mode = 'Credit Card';
						$o->payment_mode=$pay_mode;
					}else if($o->payment_mode == 5){
						$pay_mode = 'Dubai Mall Online';
						$o->payment_mode=$pay_mode;
					}
					if($o->customerName == null){
						$custName="NA";
						$o->customerName ="NA";
					}else{
						$custName=$o->customerName;
						$o->customerName =$o->customerName;
					}
					array_push($itemList, array(
						"BillId"  => Common::fixBill($o->id),
						"itemName"=>$o->itemName,
						"dateTime" => $o->dateTime,
						"customerName" => $custName,
						"subtotal" => Common::fixIt($o->subtotal),
						"vat_amt" => Common::fixIt($o->v_amt),
						"dis_amt" => Common::fixIt($o->dis_amt),
						"loydis_amt" => Common::fixIt($o->loyalty_discount),
						"payment_mode" => $pay_mode,
						"cash_amt" => Common::fixIt($o->cash_amt),
						"card_amt" =>Common::fixIt($o->card_amt),
					)
				);
	
					$saletotalDiscount = Common::fixIt($o->dis_amt) + $saletotalDiscount;
					$saletotalLoyDis =  Common::fixIt($o->loyalty_discount) + $saletotalLoyDis;
					$saletotal = Common::fixIt($o->subtotal) + $saletotal;
					$saletotalVat = Common::fixIt($o->v_amt) + $saletotalVat;
				}
			}
	
							$row->child = $arrPurchase;
	
	
	
	
							}
	
	//return
	
				else if($row->id == 2){
							 $purchases1=DB::select("SELECT bill_returns.amount,
				bill_returns.id as returnId,bill_return_items.rate as rate,bill_return_items.qty as qty,
				billings.*,items.itemName,concat(bill_returns.id,'(BillNo-',billings.id,')')as BillId,
				DATE_FORMAT(bill_returns.created_at,'".$this->rDateFormatSQL." %h:%i %p')as dateTime,( (bill_return_items.rate - ((billings.dis_amt * bill_return_items.rate)/(billings.subtotal + billings.v_amt)) - (((bill_return_items.rate) - ((billings.dis_amt * bill_return_items.rate)/(billings.subtotal + billings.v_amt))) - ((((bill_return_items.rate) - ((billings.dis_amt * bill_return_items.rate)/(billings.subtotal + billings.v_amt)))*100)/(100+(bill_return_items.vat)))) ) * bill_return_items.qty)  as subtotal,( (((bill_return_items.rate) - ((billings.dis_amt * bill_return_items.rate)/(billings.subtotal + billings.v_amt))) - ((((bill_return_items.rate) - ((billings.dis_amt * bill_return_items.rate)/(billings.subtotal + billings.v_amt)))*100)/(100+(bill_return_items.vat)))) * (bill_return_items.qty) ) as v_amt,
					((bill_return_items.rate - ((billings.dis_amt * bill_return_items.rate)/(billings.subtotal + billings.v_amt)) - (((bill_return_items.rate) - ((billings.dis_amt * bill_return_items.rate)/(billings.subtotal + billings.v_amt))) - ((((bill_return_items.rate) - ((billings.dis_amt * bill_return_items.rate)/(billings.subtotal + billings.v_amt)))*100)/(100+(bill_return_items.vat)))) ) * bill_return_items.qty) as returntotal ,
						((billings.dis_amt /(billings.subtotal + billings.v_amt)) * 100)as percentdiscount,
				customers.name as customerName 
				FROM bill_returns 
				INNER JOIN billings on bill_returns.billing_id=billings.id 
				left outer join customers on customers.id = billings.customer_id 
				Inner join bill_return_items on bill_returns.id=bill_return_items.return_id
				Inner join items on items.id=bill_return_items.item_id
				Inner join categories on categories.id=items.category
				where CAST(bill_returns.created_at AS DATE) BETWEEN '".$fromDate."' and '".$toDate."'  and 
				CASE WHEN $category_id > 0 THEN  categories.parent_category = $category_id ELSE true END and
				CASE WHEN $sub_category_id > 0 THEN categories.id = $sub_category_id ELSE true END
				ORDER BY bill_returns.created_at ASC");
	
					  if(sizeof($purchases1)>0){
	
	
				foreach ($purchases1 as $o) {
					if($o->subtotal == null || $o->subtotal == 0){
						$returnpercentage = 0;
						$returnpercentageLoyalty = 0;
					}else{
						$returnpercentage = round(($o->dis_amt /($o->subtotal + $o->v_amt)) * 100);
	
						$returnpercentageLoyalty = ($o->loyalty_discount /($o->subtotal + $o->v_amt)) * 100;
	
					}
				  
					 
					 $billReturn = DB::select("SELECT 
	
						CASE WHEN $this->intVatType != 2  THEN
						(bill_return_items.qty * bill_return_items.rate) 
						WHEN $this->intVatType = 2 THEN
						(bill_return_items.qty * ( bill_return_items.rate + (bill_return_items.rate * bill_return_items.vat)/(100 )) ) 
						END
						as rate ,
	
						CASE WHEN $this->intVatType != 2  THEN
						((bill_return_items.rate * bill_return_items.vat)/(100 + bill_return_items.vat) * bill_return_items.qty) 
						WHEN $this->intVatType = 2 THEN
						((bill_return_items.rate * bill_return_items.vat)/(100 ) * bill_return_items.qty) 
						END
						as vat,
						((bill_return_items.rate - ((billings.dis_amt * bill_return_items.rate)/(billings.subtotal + billings.v_amt)) - (((bill_return_items.rate) - ((billings.dis_amt * bill_return_items.rate)/(billings.subtotal + billings.v_amt))) - ((((bill_return_items.rate) - ((billings.dis_amt * bill_return_items.rate)/(billings.subtotal + billings.v_amt)))*100)/(100+(bill_return_items.vat)))) ) * bill_return_items.qty) as returntotal 
						FROM bill_return_items
						 INNER JOIN bill_returns on bill_returns.id=bill_return_items.return_id 
						 Inner join billings on billings.id=bill_returns.billing_id
						 Inner join items on items.id=bill_return_items.item_id
						  Inner join categories on categories.id=items.category
						where bill_returns.id = $o->returnId and 
						CASE WHEN $category_id > 0 THEN  categories.parent_category = $category_id ELSE true END and
				CASE WHEN $sub_category_id > 0 THEN categories.id = $sub_category_id ELSE true END AND
						CAST(bill_returns.created_at AS DATE) BETWEEN '".$fromDate."' and '".$toDate."'  ORDER BY bill_returns.created_at ASC");
					if(sizeof($billReturn) > 0){
						$returntotalReturn =  $billReturn[0]->rate;
						$returnfloatTotalVatAmt =  $billReturn[0]->vat;
					}			
					 $o->dis_amt = ((($o->percentdiscount * $o->rate)/100)*$o->qty);
					 
					$returndisLoyaltyAmt = ($returnpercentageLoyalty * $returntotalReturn)/100;
					if($o->payment_mode == 1){
						$pay_mode = 'Cash';
						$o->payment_mode=$pay_mode;
						$returncashAmt= $o->amount;
						$returncardAmt = 0;
					}else if($o->payment_mode == 2){
						$pay_mode = 'Credit & Cash';
						$o->payment_mode=$pay_mode;
						$returncashAmt= $o->amount;
						$returncardAmt = 0;
					}else if($o->payment_mode == 3){
						$pay_mode = 'Credit';
						$o->payment_mode=$pay_mode;
						$returncashAmt= 0;
						$returncardAmt = 0;
					}else if($o->payment_mode == 4){
						$pay_mode = 'Credit Card';
						$o->payment_mode=$pay_mode;
						$returncashAmt= 0;
						$returncardAmt = $o->amount;
					}else if($o->payment_mode == 5){
						$pay_mode = 'Dubai Mall Online';
						$o->payment_mode=$pay_mode;
						$returncashAmt= 0;
						$returncardAmt = 0;
					}
					if($o->customerName == null){
						$custName="NA";
						$o->customerName ="NA";
					}else{
						$custName=$o->customerName;
						$o->customerName =$o->customerName;
					}
					
					$tempSubTotal = $o->amount - $returnfloatTotalVatAmt + $returndisAmt +$returndisLoyaltyAmt;
					if($tempSubTotal > 0){
						$tempSubTotal = $tempSubTotal - (($tempSubTotal * $returnpercentage)/ 100);
					}
					if($returnfloatTotalVatAmt > 0){
						$returnfloatTotalVatAmt = $returnfloatTotalVatAmt - (($returnfloatTotalVatAmt * $returnpercentage) /100);
					}
					array_push($returnitemList, array(
						"BillId"  => Common::fixBill($o->BillId),
						"itemName"=>$o->itemName,
						"dateTime" => $o->dateTime,
						"customerName" => $custName,
						"id"  => Common::fixBill($o->id),
						"subtotal" => Common::fixIt($tempSubTotal),
						"v_amt" => Common::fixIt($returnfloatTotalVatAmt),
						"dis_amt" => Common::fixIt($returndisAmt),
						"loyalty_discount" => Common::fixIt($returndisLoyaltyAmt),
						"payment_mode" => $pay_mode,
						"cash_amt" => Common::fixIt($returncashAmt),
						"card_amt" => Common::fixIt($returncardAmt),
					)
				);
					
					$returntotalDiscount =Common::fixIt($o->dis_amt+ $returntotalDiscount);
					$returntotalLoyaltyDiscount = $returndisLoyaltyAmt + $returntotalLoyaltyDiscount;
					$returntotal =Common::fixIt($returntotal+ $o->returntotal);
					$returntotalVatAmt =Common::fixIt ($returntotalVatAmt+$o->v_amt);
					
					
				}
			}
		
		$row->child = $purchases1;
					
	
	
							}
					}
			
			return array(
				'saletotal' 			=>	$saletotal,	
				'saletotalDiscount' 	=>	$saletotalDiscount,	
				'saletotalLoyDis' 		=>	$saletotalLoyDis,	
				'saletotalVat' 			=>	$saletotalVat,	
				'purchases'			=>	$purchases,
				 'returntotalDiscount' 			=>$returntotalDiscount,
			   'returntotalLoyaltyDiscount' 	=> 	$returntotalLoyaltyDiscount,
			
				'returntotal'=>$returntotal,
				'returntotalVat'=>$returntotalVatAmt
			);
	
		}catch(Exception $e){
			Log::error("SalesReportController@getCategorywiseSalesSummaryQuery ".$e);
			return View("errors.exception");
		}
	}
	
	/**
	 * Display a listing of the resource. Used for tax_refund_Report
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function customerClassification(Request $request){
     try
	 {
		$categories= array();
		$categories1 = Category::where("parent_category","=", 0)->get();
		$brands[] = (object) array('id'=>'0','categorie' => 'All' ,'parent_category' => '0');
		foreach ($categories1 as $row) {
			$brands[]=$row;
		}
         $customers=array();
		 $customers1=Customer::all();
		//$customers[] = (object) array('id'=>'0','name' => 'All' ,'parent_category' => '0');
		foreach ($customers1 as $row) {
			$customers[]=$row;
		}
		$date = Carbon::now()->format('m/d/Y');
		$date1 = Carbon::now()->format('m/d/Y');
		$mallDetails = $this->mallDetails();
		$res = array(
			"customers"=>$customers,
			"brands" =>$brands,
			"date" => $date,
			"date1" => $date1,
			"mallDetails" => $mallDetails,
		);
	
		if($request->ajax()){
			return Response($res,200);
		}else{
			return View('pages.sales_reports.customer_classification_report.customer_classification_report', $res);
		}
	}
	catch(Exception $e){
		Log::error("SalesReportController@customerClassification ".$e);
		return View("errors.exception");
	}
	}
	
	/**
		 * Select data which is displayed on report view table . used for Cash_Memo_Catagorywise_Report
		 * @param  REquest $request [description]
		 * @return [type]           [description]
		 */
	public function getLedgerCustomerClassification(Request $request){	
		try{
		$dateArray 			= 	$request->all()["data"]; 
		$fromDate 			= 	new Carbon($dateArray['fromDate']);
		$toDate 			= 	new Carbon($dateArray['toDate']);		
		$brand_id 		= 	$dateArray['brand_id'];
		$customer_id 		= 	$dateArray['customer_id'];
	    $data 				= 	$this->getCustomerClassificationQuery($fromDate,$toDate,$brand_id,$customer_id);
		$labels 			= 	$this->getLabels();
		$purchases 			=	$data['purchases'];
		$totalTransaction=$data['totalTransaction'];
		$res = array(
			"purchases" 	=> 	$purchases,
			"labels" 		=> 	$labels,	
			"totalPurchase"=>$data['totalPurchase'],
			'totalTransaction'=>$totalTransaction,
			"toDatePrint" 	=> 	(new Carbon($toDate))->format($this->rDateFormat),
			"fromDatePrint" => 	(new Carbon($fromDate))->format($this->rDateFormat),		
			

		);
	
		if($request->ajax()){
			return Response($res,200);
		}else{
			return View("pages.sales_reports.customer_classification_report.customer_classification_report",$res);
		}
		}	
		catch(Exception $e){
			Log::error("SalesReportController@getLedgerCustomerClassification ".$e);
			return View("errors.exception");
		}
		
	}

	public function getCustomerClassificationQuery($fromDate,$toDate,$brand_id,$customer_id)
	{
		try{
			$brand_id 		= 	$brand_id ?? 0;
			$customer_id	= 	$customer_id ?? 0;
			$total_Transaction=0;
			$totalPurchase=0;
			$rowtotal=0;
			$totalamtprivilge=0;
			$p=[];
			$purchases1 =DB::select("SELECT COUNT(DISTINCT(bi.billing_id))as totalTransaction
            FROM billing_items bi  
			INNER JOIN billings b ON b.id = bi.billing_id 
			INNER JOIN items i ON i.id = bi.item_id INNER JOIN categories c ON i.category = c.id
			inner join customers cu on cu.id=b.customer_id
			inner join brand_classifications bc on bc.brand_id=c.parent_category
			where  b.billing_status <> 'hold' AND 
			 cu.id = b.customer_id AND CASE WHEN  $customer_id > 0 THEN b.customer_id = $customer_id ELSE b.customer_id != 'null' END 
			AND CASE WHEN $brand_id > 0 THEN  c.parent_category = $brand_id ELSE true END  and 
			CAST(b.created_at AS DATE) BETWEEN '".$fromDate."' and '".$toDate."' GROUP BY b.id");
		    $purchases =DB::select("SELECT COUNT(DISTINCT(bi.billing_id))as totalTransaction,  c.parent_category as brand_id,(select categorie from categories where id=c.parent_category) as brand_name,COUNT(DISTINCT(b.id)) as totalTransaction,sum(bi.rate * bi.qty)as totalPurchase,
			bc.activation_amount
            FROM billing_items bi  
			INNER JOIN billings b ON b.id = bi.billing_id 
			INNER JOIN items i ON i.id = bi.item_id INNER JOIN categories c ON i.category = c.id
			inner join customers cu on cu.id=b.customer_id
			inner join brand_classifications bc on bc.brand_id=c.parent_category
			where  b.billing_status <> 'hold' AND 
			 cu.id = b.customer_id AND CASE WHEN  $customer_id > 0 THEN b.customer_id = $customer_id ELSE b.customer_id != 'null' END 
			AND CASE WHEN $brand_id > 0 THEN  c.parent_category = $brand_id ELSE true END  and 
			CAST(b.created_at AS DATE) BETWEEN '".$fromDate."' and '".$toDate."' GROUP BY c.parent_category");
			foreach($purchases as $row){
             $p=DB::SELECT("SELECT b.id,DATE_FORMAT(b.created_at,'".$this->rDateFormatSQL."') billingDate,cu.id as customer_id,cu.name as customerName,sum(bi.rate * bi.qty)as amt
		
			 FROM billing_items bi  INNER JOIN billings b ON b.id = bi.billing_id 
			 INNER JOIN items i ON i.id = bi.item_id
			 INNER JOIN categories c ON i.category = c.id
			 inner join customers cu on cu.id=b.customer_id
			 where  b.billing_status <> 'hold' AND 
			 c.parent_category = $row->brand_id and 
			 cu.id = b.customer_id AND CASE WHEN  $customer_id > 0 THEN b.customer_id = $customer_id ELSE b.customer_id != 'null' END 
			 AND CAST(b.created_at AS DATE) BETWEEN '".$fromDate."' and '".$toDate."' GROUP BY c.parent_category,b.id order BY b.id");
			 //$row->child=$p;
			 	//  case when $row->activation_amount > (select sum(rate) from billing_items where  ) then 'Regular Customer' else 'Privilege Customer' End As customerType
			 $totalPurchase=$row->totalPurchase+$totalPurchase;
			 foreach($p as $row1){
				    
					$totalamtprivilge=$row1->amt +  $totalamtprivilge;

					if($row->activation_amount > $totalamtprivilge){
				  $row1->customerType='Regular Customer';
				}
				 else{
					$row1->customerType='Privilege Customer';
				 }

				  
			 }
		    
                $row->child=$p;
			}

			foreach($purchases1 as $row1) {
				$total_Transaction=$row1->totalTransaction+$total_Transaction;
			}

			return array(
				'purchases' 	=>  $purchases,
				'totalPurchase'=>$totalPurchase,
				'totalTransaction'=>$total_Transaction
				
			);
		}
		catch(Exception $e){
			Log::error("SalesReportController@getCustomerClassificationQuery ".$e);
			return View("errors.exception");
		}

	}
public function exportToCustomerClassificationreport(Request $request){
    	$dateArray 			= 	$request->all(); 
		$fromDate 			= 	new Carbon($dateArray['from']);
		$toDate 			= 	new Carbon($dateArray['to']);
		$brand_id 		= 	$dateArray['brand'];
		$customer_id 	= 	$dateArray['customer'];
		$data 				= 	$this->getCustomerClassificationQuery($fromDate,$toDate,$brand_id,$customer_id);
		$fromDate 			= 	date($this->rDateFormat, strtotime($fromDate));
		$toDate 			= 	date($this->rDateFormat, strtotime($toDate));
		$totalPurchase 		=	$data['totalPurchase'];
		$purchases 			=	$data['purchases'];
		$totalTransaction=$data['totalTransaction'];
		if(Input::get('pdf')){	
			$pdf = PDF::loadView('pages.sales_reports.customer_classification_report.customer_classification_report_pdf', [			
				
				"purchases" 	=> 	$purchases,
				"fromDate" 		=> 	$fromDate,
				"toDate" 		=> 	$toDate,
				"totalPurchase"=>$totalPurchase,
				"totalTransaction"=>$totalTransaction,
				"i"  			=>	'1',
				
			]); 
			$pdf->setPaper('a4');
			$carbon = Carbon::today();
			$timestamp = $carbon->timestamp;
			$format = $carbon->format($this->rDateFormat);
			$exportName = 'CustomerClassification-'.$format.'.pdf';     
			return $pdf->download($exportName);
		}elseif (Input::get('xls')) {
			$carbon = Carbon::today();
			$timestamp = $carbon->timestamp;
			$format = $carbon->format($this->rDateFormat);
			$exportName = 'CustomerClassification-'.$format; 
			Excel::create($exportName, function ($excel) 
				use($purchases,
					$totalPurchase,
					$fromDate,
					$toDate,
					$totalTransaction
					) {
					$excel->sheet('CustomerClassification', function ($sheet) 
						use($purchases,
						$totalPurchase,
						$fromDate,
						$toDate,$totalTransaction) {
							$i=0;
							$itemList = array();
							$sheet->appendRow(array('', ''));
							$sheet->appendRow(array(trans('glob.from'), $fromDate ,trans('glob.to'),$toDate));
							$sheet->row($sheet->getHighestRow(), function ($row) {
								$row->setFontWeight('bold');
							});	
							$sheet->appendRow(array('', ''));
							/* setting column names for data - you can of course set it manually*/
							$sheet->appendRow(array(
								"#" ,
								trans('glob.customer')  ,
								trans('billing.bill_date') ,
								trans('billing.bill_no') ,
								trans('glob.customerType') ,
								trans('glob.total_purchase') ,
							)); /* column names*/
							/* getting last row number (the one we already filled and setting it to bold*/
								$sheet->row($sheet->getHighestRow(), function ($row) {
									$row->setFontWeight('bold');
								});
								if(sizeof($purchases)>0){
									foreach ($purchases as $p) {
										$sheet->appendRow( array(
											"#" => $p->brand_name,
											trans('glob.customer')=>'',
											trans('billing.bill_date')=>'',
											trans('billing.bill_no')=>'',
											trans('glob.customerType')=>'' ,
											trans('glob.total_purchase')=>'',
										)
									);
										$i=0;
										foreach ($p->child as $o) {
											$i++;
											$sheet->appendRow( array(
												"#" => $i,
												trans('glob.customer')  => $o->customerName ?? 'NA',
												trans('billing.bill_date') => $o->billingDate,
												trans('billing.bill_no') => " ".Common::fixBill($o->id),
												trans('glob.customerType') => $o->customerType,
												trans('glob.total_purchase') => $o->amt,
											)
										);
										}
										$sheet->appendRow( array(
					                            "#" => '',
												trans('glob.customer')  => '',
												trans('billing.bill_date') => '',
												trans('billing.bill_no') => '',
												trans('glob.customerType') =>'',
												trans('glob.total_purchase') => trans('glob.total_purchase')." = ".Common::fixIt($p->totalPurchase),	
										)
									);
									}
								}	
								$sheet->appendRow( array(
									"#" => '',
									trans('glob.customer')  => '',
									trans('billing.bill_date') => trans('glob.totalTransaction'),
									trans('billing.bill_no') =>" ".Common::fixQty($totalTransaction),
									trans('glob.customerType') =>trans('glob.total_purchase'),
									trans('glob.total_purchase') =>" ".Common::fixIt($totalPurchase),	
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

 	}
