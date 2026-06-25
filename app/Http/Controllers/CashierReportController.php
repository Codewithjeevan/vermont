<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Option;
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

class CashierReportController extends Controller
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
		$this->intVatType=Common::vatType();
	}


	/**
	 * Display a listing of the resource.
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function index(Request $request){
		try{
			if (Auth::check()){
				$rollId = \Auth::user()->roleId;
			}
			if($rollId == 1){
				$users1 = User::all();	
			}else{
				$users1 = User::where('roleId', '<>', 1)->get();	
			}
			$users = array();
			$users[] = (object) array('id'=>'0','email' => 'All');
			foreach ($users1 as $row) {
				$users[]=$row;
			}	
			$date = Carbon::now()->format('m/d/Y');
			$date1 = Carbon::now()->format('Y-m-d');
			$res = array(
				"users" =>$users,
				"date" => $date,
				"date1" => $date1,
				);
			if($request->ajax()){
				return Response($res,200);
			}else{
				return View('pages.reports.cashier_report.cashier_report', $res);
			}
		}catch(Exception $e){
			Log::error("CashierReportController@index ".$e);
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
			$user_id = $dateArray['user_id'];
			$total=0;
			$totalDis=0;
			$totalLoyaltyDis=0;
			$totalVatAmount=0;
			$totalSub=0;
			$itemList = array();	
			$cashierName='';
			$rollId = 0;
			if (Auth::check()){
				$rollId = \Auth::user()->roleId;
			}
			$data = $this->getCashiarDetailsQuery($fromDate,$toDate,$user_id,$rollId);
			$address = DB::select("SELECT options.value as address FROM options where options.type = 'general' and options.key = 'register_address'");
			$mallName = DB::select("SELECT options.value as mallName FROM options where options.type = 'general' and options.key = 'mall_name'");
			$logo = DB::select("SELECT options.value as logo FROM options where options.type = 'general' and options.key = 'logo'");
			$telephone = DB::select("SELECT options.value as telephone FROM options where options.type = 'general' and options.key = 'mall_telephone'");
			$mall_website = DB::select("SELECT options.value as mall_website FROM options where options.type = 'general' and options.key = 'mall_website'");
			$labels = $this->getLabels();
			$users = array();
			if($rollId == 1){
				$users1 = User::all();	
			}else{
				$users1 = User::where('roleId', '<>', 1)->get();
			}
			$users[] = (object) array('id'=>'0','email' => 'All');
			foreach ($users1 as $row) {
				$users[]=$row;
			}	
			$total= 				$data['total'];
			$totalDis= 				$data['totalDis'];
			$totalLoyaltyDis= 		$data['totalLoyaltyDis'];
			$totalVatAmount= 		$data['totalVatAmount'];
			$totalSub= 				$data['totalSub'];		
			$purchases = 			$data['purchases'];
			$res = array(		
				"purchases" => 			$purchases,
				"users" =>				$users,
				"total"	=> 				$total,	
				"cashierName" => 		$cashierName,
				"labels" => 			$labels,	
				"totalDis" => 			$totalDis,	
				"address"  => 			(isset($address[0]->address)) ? strip_tags($address[0]->address) : '',	
				"mallName"  => 			(isset($mallName[0]->mallName)) ? $mallName[0]->mallName : '',	
				"logo"  => 				(isset($logo[0]->logo)) ? $logo[0]->logo : '',	
				"telephone"  => 		(isset($telephone[0]->telephone)) ? $telephone[0]->telephone : '',    
				"mall_website"  => 		(isset($mall_website[0]->mall_website)) ? $mall_website[0]->mall_website : '',
				"totalSub" => 			$totalSub,
				"totalLoyaltyDis" => 	$totalLoyaltyDis,
				"totalVatAmount"=>		$totalVatAmount,	
				"toDatePrint" => 		(new Carbon($toDate))->format($this->rDateFormat),
				"fromDatePrint" => 		(new Carbon($fromDate))->format($this->rDateFormat),		
				);
			if($request->ajax()){
				return Response($res,200);
			}else{
				return View("pages.reports.cashier_report.cashier_report",$res);
			}
		}catch(Exception $e){
			Log::error("CashierReportController@getLedger ".$e);
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
			$user_id = $dateArray['user'];
			$total=0;
			$totalDis = 0;
			$totalLoyaltyDis=0;
			$totalVatAmount=0;
			$totalSub = 0;
			$itemList = array();	
			$cashierName='';
			$currency = $this->optioncurr;
			$intVatType=$this->intVatType;
			$rollId = 0;
			if (Auth::check()){
				$rollId = \Auth::user()->roleId;
			}
			$data = $this->getCashiarDetailsQuery($fromDate,$toDate,$user_id,$rollId);
			$address = DB::select("SELECT options.value as address FROM options where options.type = 'general' and options.key = 'register_address'");
			$mallName = DB::select("SELECT options.value as mallName FROM options where options.type = 'general' and options.key = 'mall_name'");
			if($address[0]->address == null){
				$addressResult = "NA";
			}else{
				$addressResult = $address[0]->address;
			}
			if($mallName[0]->mallName == null){
				$mallNameResult = "NA";
			}else{
				$mallNameResult = $mallName[0]->mallName;
			}
			$total= 					$data['total'];
			$totalDis =  				$data['totalDis'];
			$totalLoyaltyDis= 			$data['totalLoyaltyDis'];
			$totalVatAmount= 			$data['totalVatAmount'];
			$totalSub =  				$data['totalSub'];
			$purchases = 				$data['purchases'];
			if(Input::get('pdf')){	
				$fromDate = date($this->rDateFormat, strtotime($fromDate));
				$toDate = date($this->rDateFormat, strtotime($toDate));
				$pdf = PDF::loadView('pages.reports.cashier_report.cashier_report_pdf', [
					"purchases" => 			$purchases ,
					"total"=> 				$total,
					"totalDis" => 			$totalDis,
					"totalLoyaltyDis" => 	$totalLoyaltyDis,
					"totalSub" => 			$totalSub,
					"totalVatAmount"=>		$totalVatAmount,
					"cashierName" => 		$cashierName,
					"mallName" => 			$mallNameResult,
					"address" => 			strip_tags($addressResult),
					"fromDate" => 			$fromDate,
					"toDate" => 			$toDate,
					"i" =>					'1',
					'curr'=>				$currency
					]); 
				$pdf->setPaper('a4');
				$carbon = Carbon::today();
				$timestamp = $carbon->timestamp;
				$format = $carbon->format($this->rDateFormat);
				$exportName = 'CashierReport-'.$format.'.pdf';     
				return $pdf->download($exportName);
			}elseif (Input::get('xls')) {
				$fromDate = date($this->rDateFormat, strtotime($fromDate));
				$toDate = date($this->rDateFormat, strtotime($toDate));
				$carbon = Carbon::today();
				$timestamp = $carbon->timestamp;
				$format = $carbon->format($this->rDateFormat);
				$exportName = 'CashierReport-'.$format; 
				Excel::create($exportName, function ($excel) 
					use($purchases,
						$total,
						$totalDis,
						$totalLoyaltyDis,
						$totalSub,
						$totalVatAmount,
						$cashierName,
						$mallNameResult,
						$addressResult,
						$fromDate,
						$toDate, 
						$currency,
						$intVatType) {
						$excel->sheet('CashierReport', function ($sheet) 
							use($purchases,
								$total,
								$totalDis,
								$totalLoyaltyDis,
								$totalSub,
								$totalVatAmount,
								$cashierName,
								$mallNameResult,
								$addressResult,
								$fromDate,
								$toDate, 
								$currency,
								$intVatType) {	
								$sheet->appendRow(array('', ''));
								$sheet->appendRow(array(trans('glob.shop_name'), ':'.$mallNameResult));
								$sheet->row($sheet->getHighestRow(), function ($row) {
									$row->setFontWeight('bold');
								});	
								$sheet->appendRow(array(trans('glob.address'), ':'.strip_tags($addressResult)));
								$sheet->row($sheet->getHighestRow(), function ($row) {
									$row->setFontWeight('bold');
								});	
								$sheet->appendRow(array('', ''));
								$sheet->appendRow(array(trans('glob.from'), $fromDate ,trans('glob.to'),$toDate));
								$sheet->row($sheet->getHighestRow(), function ($row) {
									$row->setFontWeight('bold');
								});	
								$sheet->appendRow(array('', ''));
								/* setting column names for data - you can of course set it manually */
								if($intVatType !=0){
									$sheet->appendRow(array(
										"#" ,	
										trans('billing.bill_no') ,							
										trans('billing.bill_date') ,
										trans('billing.bill_time') ,	
										trans('billing.sub_total')."(".$currency.")" ,
										trans('glob.vat')."(".$currency.")" ,						
										trans('billing.dis_amt')."(".$currency.")" ,
										trans('billing.loyalty_discount')."(".$currency.")" ,
										trans('billing.amt')."(".$currency.")" ,	
										)); /* column names */

								}
								else{
									$sheet->appendRow(array(
										"#" ,	
										trans('billing.bill_no') ,							
										trans('billing.bill_date') ,
										trans('billing.bill_time') ,	
										trans('billing.sub_total')."(".$currency.")" ,

										trans('billing.dis_amt')."(".$currency.")" ,
										trans('billing.loyalty_discount')."(".$currency.")" ,
										trans('billing.amt')."(".$currency.")" ,	
										)); /* column names */

								}
								/* getting last row number (the one we already filled and setting it to bold */
								$sheet->row($sheet->getHighestRow(), function ($row) {
									$row->setFontWeight('bold');
								});
								$itemList = array();
								if($intVatType!=0){
									foreach ($purchases as $p) {						
										$sheet->appendRow(array(
											"#"  => $p->name,	
											trans('billing.bill_no') => '',							
											trans('billing.bill_date') => '',
											trans('billing.bill_time') => '',	
											trans('billing.sub_total')."(".$currency.")" => '',
											trans('glob.vat')."(".$currency.")" => '',						
											trans('billing.dis_amt')."(".$currency.")" => '',
											trans('billing.loyalty_discount')."(".$currency.")" => '',
											trans('billing.amt')."(".$currency.")" => '',	
											)
										);
										$sheet->row($sheet->getHighestRow(), function ($row) {
											$row->setFontWeight('bold');
										});	
										$i=0;					
										foreach ($p->child as $o) {						
											$i++;						
											$sheet->appendRow( array(
												"#"  => $i,	
												trans('billing.bill_no') => " ".Common::fixBill($o->id),							
												trans('billing.bill_date') => $o->date,
												trans('billing.bill_time') => $o->time,
												trans('billing.sub_total')."(". $currency.")" => " ".Common::fixIt($o->subtotal),
												trans('glob.vat')."(". $currency.")" => " ".Common::fixIt($o->v_amt),		
												trans('billing.dis_amt')."(". $currency.")" => " ".Common::fixIt($o->dis_amt),
												trans('billing.loyalty_discount')."(". $currency.")" => " ".Common::fixIt($o->loyalty_discount),
												trans('billing.amt')."(". $currency.")" => " ".Common::fixIt($o->bil_amt),	
												)
											);
										}
										$sheet->appendRow( array(
											"#"  => '',	
											trans('billing.bill_no') => '',							
											trans('billing.bill_date') => '',
											trans('billing.bill_time') => '',	
											trans('billing.sub_total') => trans('billing.sub_total')." = ".Common::fixIt($p->subtotal),
											trans('glob.vat') => trans('glob.vat')." = ".Common::fixIt($p->v_amt),							
											trans('billing.dis_amt') => trans('billing.dis_amt')." = ".Common::fixIt($p->dis_amt),
											trans('billing.loyalty_discount') => trans('billing.loyalty_discount')." = ".Common::fixIt($p->loyalty_discount),
											trans('billing.amt') =>  trans('billing.amt')." = ".Common::fixIt($p->bil_amt),	
											)
										);
										$sheet->row($sheet->getHighestRow(), function ($row) {
											$row->setFontWeight('bold');
										});	
									}
								}
								else{
									foreach ($purchases as $p) {						
										$sheet->appendRow(array(
											"#"  => $p->name,	
											trans('billing.bill_no') => '',							
											trans('billing.bill_date') => '',
											trans('billing.bill_time') => '',	
											trans('billing.sub_total')."(".$currency.")" => '',					
											trans('billing.dis_amt')."(".$currency.")" => '',
											trans('billing.loyalty_discount')."(".$currency.")" => '',
											trans('billing.amt')."(".$currency.")" => '',	
											)
										);
										$sheet->row($sheet->getHighestRow(), function ($row) {
											$row->setFontWeight('bold');
										});	
										$i=0;					
										foreach ($p->child as $o) {						
											$i++;						
											$sheet->appendRow( array(
												"#"  => $i,	
												trans('billing.bill_no') => " ".Common::fixBill($o->id),							
												trans('billing.bill_date') => $o->date,
												trans('billing.bill_time') => $o->time,
												trans('billing.sub_total')."(". $currency.")" => " ".Common::fixIt($o->subtotal),trans('billing.dis_amt')."(". $currency.")" => " ".Common::fixIt($o->dis_amt),
												trans('billing.loyalty_discount')."(". $currency.")" => " ".Common::fixIt($o->loyalty_discount),
												trans('billing.amt')."(". $currency.")" => " ".Common::fixIt($o->bil_amt),	
												)
											);
										}
										$sheet->appendRow( array(
											"#"  => '',	
											trans('billing.bill_no') => '',							
											trans('billing.bill_date') => '',
											trans('billing.bill_time') => '',	
											trans('billing.sub_total') => trans('billing.sub_total')." = ".Common::fixIt($p->subtotal),
											trans('billing.dis_amt') => trans('billing.dis_amt')." = ".Common::fixIt($p->dis_amt),
											trans('billing.loyalty_discount') => trans('billing.loyalty_discount')." = ".Common::fixIt($p->loyalty_discount),
											trans('billing.amt') =>  trans('billing.amt')." = ".Common::fixIt($p->bil_amt),	
											)
										);
										$sheet->row($sheet->getHighestRow(), function ($row) {
											$row->setFontWeight('bold');
										});	
									}
								}
								if($intVatType!=0){
									$sheet->appendRow( array(
										"#"  => '',	
										trans('billing.bill_no') => '',							
										trans('billing.bill_date') => '',
										trans('billing.bill_time') => trans('glob.total'),
										trans('billing.sub_total')."(". $currency.")" => " ".Common::fixIt($totalSub),
										trans('blog.vat')."(". $currency.")" => " ".Common::fixIt($totalVatAmount),							
										trans('billing.dis_amt')."(". $currency.")" => " ".Common::fixIt($totalDis),
										trans('billing.loyalty_discount')."(". $currency.")" => " ".Common::fixIt($totalLoyaltyDis),
										trans('billing.amt')."(". $currency.")" => " ".Common::fixIt($total),			
										)
									);
								}
								else{
									$sheet->appendRow( array(
										"#"  => '',	
										trans('billing.bill_no') => '',							
										trans('billing.bill_date') => '',
										trans('billing.bill_time') => trans('glob.total'),
										trans('billing.sub_total')."(". $currency.")" => " ".Common::fixIt($totalSub),
										trans('billing.dis_amt')."(". $currency.")" => " ".Common::fixIt($totalDis),
										trans('billing.loyalty_discount')."(". $currency.")" => " ".Common::fixIt($totalLoyaltyDis),
										trans('billing.amt')."(". $currency.")" => " ".Common::fixIt($total),			
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
	Log::error("CashierReportController@exportTo ".$e);
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
				"barcode" => trans('glob.barcode'),
				"sub_total" => trans('billing.sub_total'),
				"shop_name" => trans('glob.shop_name'),
				"address" => trans('glob.address'),
				"website" => trans('glob.website'), 
				"phone" => trans('glob.phone'),
				"loyalty_discount" =>trans('billing.loyalty_discount'),
				"vat" => trans('glob.vat'),
				"bill_date" => trans('billing.bill_date'),
				"bill_time" => trans('billing.bill_time'),
				)
			);
			return $labels;
		}catch(Exception $e){
			Log::error("CashierReportController@getLabels ".$e);
			return View("errors.exception");

		}
	}

	public function getCashiarDetailsQuery($fromDate,$toDate,$user_id,$rollId){
		try{
			$totalVatAmount = 0;
			$total = 0;
			$totalSub = 0;
			$totalDis = 0;
			$totalLoyaltyDis = 0;
			$user_id = $user_id ?? 0;
			$purchases=DB::select("SELECT u.id,u.email as name,sum(b.bil_amt)as bil_amt ,
				sum(b.dis_amt) as dis_amt,sum(b.loyalty_discount) as loyalty_discount,
				sum(((b.subtotal)-((b.subtotal * b.dis_amt)/(b.subtotal + b.v_amt)))) as subtotal,
				sum(b.loyalty_discount) as loyalty_discount,
				sum(((b.v_amt)-((b.v_amt * b.dis_amt)/(b.subtotal + b.v_amt)) )) as v_amt 
				FROM billings b INNER JOIN users u ON b.user_id =u.id 
				where b.billing_status <> 'hold' AND 
				CASE WHEN $user_id > 0 THEN b.user_id = $user_id ELSE 
				CASE WHEN $rollId = 1 THEN true ELSE u.roleId <> 1 END 
				END and 
				CAST(b.created_at AS DATE) BETWEEN '".$fromDate."' and '".$toDate."' GROUP BY b.user_id");
			if(sizeof($purchases)>0){
				foreach ($purchases as $o) {
					$p=DB::select("SELECT id,bil_amt,dis_amt,loyalty_discount,
						((subtotal)-((subtotal * dis_amt)/(subtotal + v_amt))) as subtotal ,
						((v_amt)-((v_amt * dis_amt)/(subtotal + v_amt)) ) as v_amt, 
						DATE_FORMAT(created_at,'".$this->rDateFormatSQL."') date,
						DATE_FORMAT(created_at,'%h:%i %p') as time 
						FROM billings 
						where billings.billing_status <> 'hold' AND 
						user_id = ".$o->id." and
						CAST(created_at AS DATE) BETWEEN '".$fromDate."' and '".$toDate."'  ORDER BY created_at ASC");
					$o->child = $p;
					$totalVatAmount = $o->v_amt + $totalVatAmount;
					$total = $o->bil_amt + $total;
					$totalSub = $o->subtotal + $totalSub;
					$totalDis = $o->dis_amt + $totalDis;
					$totalLoyaltyDis = $o->loyalty_discount + $totalLoyaltyDis;
				}
			}
			return array(
				'purchases' =>			$purchases,
				'totalVatAmount' =>		$totalVatAmount,
				'total' => 				$total,
				'totalSub' =>			$totalSub,
				'totalDis' => 			$totalDis,
				'totalLoyaltyDis' => 	$totalLoyaltyDis
				);
		}catch(Exception $e){
			Log::error("CashierReportController@getCashiarDetailsQuery ".$e);
			return View("errors.exception");
		}

	}

}
