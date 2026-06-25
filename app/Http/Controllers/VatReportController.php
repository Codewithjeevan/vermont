<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Item;
use App\Category;
use App\Unit;
use App\Purchase;
use App\PurchasedItem;
use App\Billing;
use App\BillingItems;
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



class VatReportController extends Controller
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
		$this->intVatType=common::vatType();

	}
/**
 * [index description]
 * @param  Request $request [description]
 * @return [type]           [description]
 */
public function index(Request $request){

	try{
		$date = Carbon::now()->format('m/d/Y');
		$date1 = Carbon::now()->format('Y-m-d');
		$mallDetails = $this->mallDetails();
		$res = array(
			"date" => $date,
			"date1"	=> $date1,
			"mallDetails" => $mallDetails,
			);

		if($request->ajax()){

			return Response($res,200);
		}else{
			return View('pages.reports.vat_report.vat_report', $res);
		}
	}catch(Exception $e){
		Log::error("VatReportController@index ".$e);
		return View("errors.exception");
	}
}


	/**
	 * [getVatDetails description]
	 * @param  REquest $request [description]
	 * @return [type]           [description]
	 */
	public function getLedger(Request $request){
		try{
			$dateArray = $request->all()["data"]; 
			$fromDate = $dateArray['fromDate'];
			$toDate = $dateArray['toDate'];
			$purchaselist=array();		
			$totalCostPrise=0;
			$totalSalePrice=0;
			$totalPurchaseQty=0;
			$totalSaleQty=0;
			$totalsalevat=0;
			$purchases = array();

			$purchases[0] =  (object) array('id'=> 1,'name'=>'Purchase');
			$purchases[1] =  (object) array('id'=> 2,'name'=>'Sale');
			foreach ($purchases as $row) {

				if($row->id == 1){
					if($this->intVatType!=2){
						$arrPurchase=DB::select('select purchases.id as id, DATE_FORMAT(purchases.mrn_date , "'.$this->rDateFormatSQL.'" ) as date,items.itemName as itemName,items.barcode as barcode,items.serial_number as serial_number,purchased_items.costPrise as price, purchased_items.qty as qty, purchased_items.cp_vat as vat,(purchased_items.cp_vat * purchased_items.costPrise/(100+purchased_items.cp_vat) * purchased_items.qty) as vat_paid from purchases INNER join purchased_items on purchased_items.purchase_id=purchases.id INNER JOIN items on items.id=purchased_items.item_id where purchases.mrn_date BETWEEN "'.$fromDate.'" and "'.$toDate.'"');

						$row->child = $arrPurchase;	

						$p=DB::select('select SUM(purchased_items.qty) as totalqty, SUM(purchased_items.qty * purchased_items.costPrise) as totalCostprise,SUM(purchased_items.cp_vat * purchased_items.costPrise/(100+purchased_items.cp_vat) * purchased_items.qty)as totalPurchaseVat from purchases INNER join purchased_items on purchased_items.purchase_id=purchases.id INNER JOIN items on items.id=purchased_items.item_id where purchases.mrn_date BETWEEN "'.$fromDate.'" and "'.$toDate.'"');

						$totalPurchaseQty = isset($p[0]->totalqty) ? $p[0]->totalqty : 0;
						$totalCostPrise = isset($p[0]->totalCostprise) ?  $p[0]->totalCostprise : 0 ;
						$totalPurchaseVat = isset($p[0]->totalPurchaseVat) ? $p[0]->totalPurchaseVat : 0 ;
					}

					else{
						$arrPurchase=DB::select('select purchases.id as id, DATE_FORMAT(purchases.mrn_date , "'.$this->rDateFormatSQL.'" ) as date,items.itemName as itemName,items.barcode as barcode,items.serial_number as serial_number,purchased_items.costPrise as price, purchased_items.qty as qty, purchased_items.cp_vat as vat,(purchased_items.cp_vat * purchased_items.costPrise/(100) * purchased_items.qty) as vat_paid from purchases INNER join purchased_items on purchased_items.purchase_id=purchases.id INNER JOIN items on items.id=purchased_items.item_id where purchases.mrn_date BETWEEN "'.$fromDate.'" and "'.$toDate.'"');

						$row->child = $arrPurchase;	

						$p=DB::select('select SUM(purchased_items.qty) as totalqty, SUM(purchased_items.qty * purchased_items.costPrise) as totalCostprise,SUM(purchased_items.cp_vat * purchased_items.costPrise/(100) * purchased_items.qty)as totalPurchaseVat from purchases INNER join purchased_items on purchased_items.purchase_id=purchases.id INNER JOIN items on items.id=purchased_items.item_id where purchases.mrn_date BETWEEN "'.$fromDate.'" and "'.$toDate.'"');

						$totalPurchaseQty = isset($p[0]->totalqty) ? $p[0]->totalqty : 0;
						$totalCostPrise = isset($p[0]->totalCostprise) ?  $p[0]->totalCostprise : 0 ;
						$totalPurchaseVat = isset($p[0]->totalPurchaseVat) ? $p[0]->totalPurchaseVat : 0 ;

					}

				}
				else if($row->id == 2){
					if($this->intVatType!=2){
						$arrSale = DB::select('select billings.id as id, DATE_FORMAT(CAST(billings.created_at AS DATE), "'.$this->rDateFormatSQL.'" ) as date,items.itemName as itemName,items.barcode as barcode,items.serial_number as serial_number,(((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt))) - ((((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)))*100)/(100+(billing_items.vat)))) * (billing_items.qty) as vat_paid,(billing_items.rate - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) - (((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt))) - ((((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)))*100)/(100+(billing_items.vat)))) ) as price,billing_items.qty from billings INNER join billing_items on billing_items.billing_id=billings.id INNER join items on items.id=billing_items.item_id where CAST(billings.created_at AS DATE) BETWEEN "'.$fromDate.'" and "'.$toDate.'"');
						$row->child = $arrSale;

						$p = DB::select('select billings.id as id,SUM(billing_items.qty) as totalqty,SUM((billing_items.rate - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) - (((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt))) - ((((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)))*100)/(100+(billing_items.vat)))) ) * billing_items.qty) as totalsaleprice,SUM((((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt))) - ((((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)))*100)/(100+(billing_items.vat)))) * (billing_items.qty)) as totalsalevat from billings INNER join billing_items on billing_items.billing_id=billings.id INNER join items on items.id=billing_items.item_id where CAST(billings.created_at AS DATE) BETWEEN "'.$fromDate.'" and "'.$toDate.'"');


						$totalSaleQty=$p[0]->totalqty ;
						$totalSalePrice=$p[0]->totalsaleprice ;
						$totalsalevat = $p[0]->totalsalevat;

					}
					else{
						$arrSale = DB::select('select billings.id as id, DATE_FORMAT(CAST(billings.created_at AS DATE), "'.$this->rDateFormatSQL.'" ) as date,items.itemName as itemName,items.barcode as barcode,items.serial_number as serial_number,( (((billing_items.rate - 
							((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) ) * billing_items.qty) * billing_items.vat) /100 ) as vat_paid,((billing_items.rate - 
							((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) ) ) as price,billing_items.qty from billings INNER join billing_items on billing_items.billing_id=billings.id INNER join items on items.id=billing_items.item_id where CAST(billings.created_at AS DATE) BETWEEN "'.$fromDate.'" and "'.$toDate.'"');
						$row->child = $arrSale;


						$p = DB::select('select billings.id as id,SUM(billing_items.qty) as totalqty,SUM(((billing_items.rate - 
							((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) ) * billing_items.qty) * billing_items.qty) as totalsaleprice,SUM(( (((billing_items.rate - 
							((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) ) * billing_items.qty) * billing_items.vat) /100 )) as totalsalevat from billings INNER join billing_items on billing_items.billing_id=billings.id INNER join items on items.id=billing_items.item_id where CAST(billings.created_at AS DATE) BETWEEN "'.$fromDate.'" and "'.$toDate.'"');



						$totalSaleQty=$p[0]->totalqty ;
						$totalSalePrice=$p[0]->totalsaleprice ;
						$totalsalevat = $p[0]->totalsalevat;

					}
				}
				
			}
			$labels = $this->getLabels();
			$res = array(		
				"purchases" => $purchases,
				
				"labels" => $labels,
				"totalPurchaseQty"=>Common::fixQty($totalPurchaseQty),	
				"totalCostPrise" => Common::fixIt($totalCostPrise),
				"totalPurchaseVat"=>Common::fixIt($totalPurchaseVat),
				"totalSaleQty" => Common::fixQty($totalSaleQty),	
				'totalSalePrice'=>Common::fixIt($totalSalePrice),
				"totalsalevat"=>common::fixIt($totalsalevat),
				"toDatePrint" => (new Carbon($toDate))->format($this->rDateFormat),
				"fromDatePrint" => (new Carbon($fromDate))->format($this->rDateFormat),
				
				);
			if($request->ajax()){
				return Response($res,200);

			}else{
				return View("pages.reports.vat_report.vat_report",$res);
			}
		}catch(Exception $e){
			Log::error("VatReportController@getLedger ".$e);
			return View("errors.exception");
		}
	}

	public function exportTo(Request $request){
		try{
			$dateArray = $request->all(); 
			$fromDate = $dateArray['from'];
			$toDate = $dateArray['to'];
			$purchaselist=array();		
			$totalCostPrise=0;
			$totalSalePrice=0;
			$totalPurchaseQty=0;
			$totalSaleQty=0;
			$totalsalevat=0;
			$purchases = array();
			$currency = $this->optioncurr;

			$purchases[0] =  (object) array('id'=> 1,'name'=>'Purchase');
			$purchases[1] =  (object) array('id'=> 2,'name'=>'Sale');
			foreach ($purchases as $row) {
				if($row->id == 1){
					if($this->intVatType!=2){
						$arrPurchase=DB::select('select purchases.id as id, DATE_FORMAT(purchases.mrn_date, "'.$this->rDateFormatSQL.'" ) as date,items.itemName as itemName,items.barcode as barcode,items.serial_number as serial_number,purchased_items.costPrise as price, purchased_items.qty as qty, purchased_items.cp_vat as vat,(purchased_items.cp_vat * purchased_items.costPrise/(100+purchased_items.cp_vat) * purchased_items.qty) as vat_paid from purchases INNER join purchased_items on purchased_items.purchase_id=purchases.id INNER JOIN items on items.id=purchased_items.item_id where purchases.mrn_date BETWEEN "'.$fromDate.'" and "'.$toDate.'"');

						$row->child = $arrPurchase;	

						$p=DB::select('select SUM(purchased_items.qty) as totalqty, SUM(purchased_items.qty * purchased_items.costPrise) as totalCostprise,SUM(purchased_items.cp_vat * purchased_items.costPrise/(100+purchased_items.cp_vat) * purchased_items.qty)as totalPurchaseVat from purchases INNER join purchased_items on purchased_items.purchase_id=purchases.id INNER JOIN items on items.id=purchased_items.item_id where purchases.mrn_date BETWEEN "'.$fromDate.'" and "'.$toDate.'"');

						$totalPurchaseQty = isset($p[0]->totalqty) ? $p[0]->totalqty : 0;
						$totalCostPrise = isset($p[0]->totalCostprise) ?  $p[0]->totalCostprise : 0 ;
						$totalPurchaseVat = isset($p[0]->totalPurchaseVat) ? $p[0]->totalPurchaseVat : 0 ;
					}
					else{
						$arrPurchase=DB::select('select purchases.id as id, DATE_FORMAT(purchases.mrn_date , "'.$this->rDateFormatSQL.'" ) as date,items.itemName as itemName,items.barcode as barcode,items.serial_number as serial_number,purchased_items.costPrise as price, purchased_items.qty as qty, purchased_items.cp_vat as vat,(purchased_items.cp_vat * purchased_items.costPrise/(100) * purchased_items.qty) as vat_paid from purchases INNER join purchased_items on purchased_items.purchase_id=purchases.id INNER JOIN items on items.id=purchased_items.item_id where purchases.mrn_date BETWEEN "'.$fromDate.'" and "'.$toDate.'"');

						$row->child = $arrPurchase;	

						$p=DB::select('select SUM(purchased_items.qty) as totalqty, SUM(purchased_items.qty * purchased_items.costPrise) as totalCostprise,SUM(purchased_items.cp_vat * purchased_items.costPrise/(100) * purchased_items.qty)as totalPurchaseVat from purchases INNER join purchased_items on purchased_items.purchase_id=purchases.id INNER JOIN items on items.id=purchased_items.item_id where purchases.mrn_date BETWEEN "'.$fromDate.'" and "'.$toDate.'"');

						$totalPurchaseQty = isset($p[0]->totalqty) ? $p[0]->totalqty : 0;
						$totalCostPrise = isset($p[0]->totalCostprise) ?  $p[0]->totalCostprise : 0 ;
						$totalPurchaseVat = isset($p[0]->totalPurchaseVat) ? $p[0]->totalPurchaseVat : 0 ;

					}

				}
				else if($row->id == 2){
					if($this->intVatType!=2){
						$arrSale = DB::select('select billings.id as id,DATE_FORMAT(CAST(billings.created_at AS DATE), "'.$this->rDateFormatSQL.'" ) as date,items.itemName as itemName,items.barcode as barcode,items.serial_number as serial_number,(((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt))) - ((((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)))*100)/(100+(billing_items.vat)))) * (billing_items.qty) as vat_paid,(billing_items.rate - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) - (((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt))) - ((((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)))*100)/(100+(billing_items.vat)))) ) as price,billing_items.qty from billings INNER join billing_items on billing_items.billing_id=billings.id INNER join items on items.id=billing_items.item_id where CAST(billings.created_at AS DATE) BETWEEN "'.$fromDate.'" and "'.$toDate.'"');
						$row->child = $arrSale;
						
						$p = DB::select('select billings.id as id,SUM(billing_items.qty) as totalqty,SUM((billing_items.rate - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) - (((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt))) - ((((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)))*100)/(100+(billing_items.vat)))) ) * billing_items.qty) as totalsaleprice,SUM((((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt))) - ((((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)))*100)/(100+(billing_items.vat)))) * (billing_items.qty)) as totalsalevat from billings INNER join billing_items on billing_items.billing_id=billings.id INNER join items on items.id=billing_items.item_id where CAST(billings.created_at AS DATE) BETWEEN "'.$fromDate.'" and "'.$toDate.'"');


						
						$totalSaleQty=$p[0]->totalqty ;
						$totalSalePrice=$p[0]->totalsaleprice ;
						$totalsalevat = $p[0]->totalsalevat;
					}
					else{
						$arrSale = DB::select('select billings.id as id, DATE_FORMAT(CAST(billings.created_at AS DATE), "'.$this->rDateFormatSQL.'" ) as date,items.itemName as itemName,items.barcode as barcode,items.serial_number as serial_number,( (((billing_items.rate - 
							((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) ) * billing_items.qty) * billing_items.vat) /100 ) as vat_paid,((billing_items.rate - 
							((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) ) ) as price,billing_items.qty from billings INNER join billing_items on billing_items.billing_id=billings.id INNER join items on items.id=billing_items.item_id where CAST(billings.created_at AS DATE) BETWEEN "'.$fromDate.'" and "'.$toDate.'"');
						$row->child = $arrSale;


						$p = DB::select('select billings.id as id,SUM(billing_items.qty) as totalqty,SUM(((billing_items.rate - 
							((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) ) * billing_items.qty) * billing_items.qty) as totalsaleprice,SUM(( (((billing_items.rate - 
							((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) ) * billing_items.qty) * billing_items.vat) /100 )) as totalsalevat from billings INNER join billing_items on billing_items.billing_id=billings.id INNER join items on items.id=billing_items.item_id where CAST(billings.created_at AS DATE) BETWEEN "'.$fromDate.'" and "'.$toDate.'"');



						$totalSaleQty=$p[0]->totalqty ;
						$totalSalePrice=$p[0]->totalsaleprice ;
						$totalsalevat = $p[0]->totalsalevat;

					}

				}
				
			}

			
			$fromDate = date($this->rDateFormat, strtotime($fromDate));
			$toDate = date($this->rDateFormat, strtotime($toDate));
			if(Input::get('pdf')){	

				$pdf = PDF::loadView('pages.reports.vat_report.vat_report_pdf',["purchases" => $purchases,"totalCostPrise"	=> Common::fixIt($totalCostPrise),
					"totalPurchaseQty" => Common::fixQty($totalPurchaseQty),
					"totalPurchaseVat" => $totalPurchaseVat,
					"totalSalePrice"=> Common::fixIt($totalSalePrice),
					"totalSaleQty" =>Common::fixQty($totalSaleQty),
					"totalsalevat"=>$totalsalevat,"fromDate" => $fromDate,
					"toDate" => $toDate,"i" =>'1',"curr"=>$currency]); 

				$pdf->setPaper('a4');
				$carbon = Carbon::today();
				$timestamp = $carbon->timestamp;
				$format = $carbon->format($this->rDateFormat);
				$exportName = 'VatReport-'.$format.'.pdf';     
				return $pdf->download($exportName);

			}elseif (Input::get('xls')) {
				$carbon = Carbon::today();
				$timestamp = $carbon->timestamp;
				$format = $carbon->format($this->rDateFormat);
				$exportName = 'VatReport-'.$format; 


				Excel::create($exportName, function ($excel) use($purchases,$totalCostPrise,$totalPurchaseQty,$totalPurchaseVat,$totalSalePrice,$totalSaleQty,$totalsalevat,$fromDate,$toDate,$currency) {

					$excel->sheet('VatReport', function ($sheet) use($purchases,$totalCostPrise,$totalPurchaseQty,$totalPurchaseVat,$totalSalePrice,$totalSaleQty,$totalsalevat,$fromDate,$toDate,$currency) {	

						$sheet->appendRow(array('', ''));
						$sheet->appendRow(array(trans('glob.from'), ($fromDate) ,trans('glob.to'),($toDate) ));
						$sheet->row($sheet->getHighestRow(), function ($row) {
							$row->setFontWeight('bold');
							
						});	
						$sheet->appendRow(array('', ''));
						
						$itemList = array();
						foreach ($purchases as $p) {						

							array_push($itemList, array(
								"#"  => $p->name,	
								trans('glob.date') => '',							
								trans('glob.invoice_no') => '',
								trans('glob.invoice_no') => '',
								trans('billing.item_name')=>'',
								trans('glob.barcode') => '',
								trans('item.refrence_number') => '',
								trans('glob.cost_sale_price') => '',							
								trans('billing.qty') => '',
								trans('glob.purchase_sale_value') ." (".$currency.")" => '',
								trans('glob.report_vat')." (".$currency.")" => '',));

							$i=1;
							$amt =0;
							$vat=0;
							$qty=0;
							$vatpaid=0;				
							foreach ($p->child as $o) {						
								$amt = (($o->qty * $o->price)) + $amt;
								$qty=$o->qty + $qty;
								$vat=$o->vat_paid + $vat;
								$id = 0;
								if($p->id==1){
									$id = " ".$o->id;
								}else{
									$id = " ".Common::fixBill($o->id);
								}
								array_push($itemList, array(
									"#"  => $i++,	
									trans('glob.date') => $o->date,
									trans('glob.invoice_no') =>$id,
									trans('billing.item_name') => $o->itemName,
									trans('glob.barcode') => $o->barcode,
									trans('item.refrence_number') =>$o->serial_number==null ? "NA" : $o->serial_number,
									trans('glob.cost_price') => " ".Common::fixIt($o->price),
									trans('glob.qty') => " ".Common::fixQty($o->qty),	
									trans('billing.purchase_value')=>" ".Common::fixIt($o->qty * $o->price ),
									trans('glob.purchase_value') => " ".Common::fixIt($o->vat_paid),	


									)
								);
							}

							array_push($itemList, array(
								"#"  => '',	
								trans('glob.date') => '',							
								trans('glob.invoice_no') => '',
								trans('billing.item_name') => '',
								trans('glob.barcode') => '',
								trans('item.refrence_number') => '',
								trans('glob.cost_price') => '',							
								trans('billing.qty') =>trans('item.qty')."=".Common::fixQty($qty),
								trans('glob.purchase_value') => trans('glob.total')."=".Common::fixIt($amt),
								trans('glob.report_vat') => trans('glob.vat_total')."=".Common::fixIt($vat)));

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
						$sheet->appendRow(array(trans('glob.total_purchase_qty')."=".Common::fixQty($totalPurchaseQty), '' , '' , '' , '' , '' , '' ,'',trans('glob.total_sale_qty')."=".Common::fixQty($totalSaleQty)));
						$sheet->row($sheet->getHighestRow(), function ($row) {
							$row->setFontWeight('bold');						
						});

						$sheet->appendRow(array(trans('glob.total_purchase_price')." (".$currency.") =".Common::fixIt($totalCostPrise), '' , '' , '' , '' , '' , '' ,'',trans('glob.total_sale_price')." (".$currency.")=".Common::fixIt($totalSalePrice)));
						$sheet->row($sheet->getHighestRow(), function ($row) {
							$row->setFontWeight('bold');						
						});

						$sheet->appendRow(array(trans('glob.total_vat_paid')." (".$currency.")=".Common::fixIt($totalPurchaseVat), '' , '' , '' , '' , '' , '' ,'',trans('glob.total_vat_received')." (".$currency.") =".Common::fixIt($totalsalevat)));
						$sheet->row($sheet->getHighestRow(), function ($row) {
							$row->setFontWeight('bold');						
						});	

					});

})->export('xlsx');

return back();

}
}catch(Exception $e){
	Log::error("VatReportController@exportTo ".$e);
	return View("errors.exception");
}

}

public function getLabels(){
	try{
		$labels = array();
		array_push($labels, array(
			"id" => trans('glob.id'),
			"date"=>trans('glob.date'),
			"invoice_no"=>trans('glob.invoice_no'),
			"itemName"  => trans('billing.item_name'),   
			"barcode" => trans('glob.barcode'),
			"sale_price"=>trans('glob.sale_price'),
			"qty"=>trans('billing.qty'),
			"purchase_value"=>trans('glob.purchase_value'),
			"vat"=>trans('glob.report_vat'),
			"costPrice"=>trans('glob.cost_price'),
			"billId"  => trans('billing.bill_no'),
			"sale_value"=>trans('glob.sale_value'),
			'vat_received'=>trans('glob.vat_received'),
			'vat_paid'=>trans('glob.vat_paid'),
			"sub_total" => trans('billing.sub_total'),
			"vat_total"=>trans('glob.vat_total'),
			"total_purchase_qty"=>trans('glob.total_purchase_qty'),
			"total_purchase_price"=>trans('glob.total_purchase_price'),
			"total_vat_received"=>trans('glob.total_vat_received'),
			"total_sale_qty"=>trans('glob.total_sale_qty'),
			"total_sale_price"=>trans('glob.total_sale_price'),
			"total_vat_paid"=>trans('glob.total_vat_paid'),
			"total_purchase"=>trans('glob.total_purchase'),
			"total_sale"=>trans('glob.total_sale'),
			"purchase_sale_value"=>trans('glob.purchase_sale_value'),
			"cost_sale_price"=>trans('glob.cost_sale_price'),
			'total'=>trans('glob.total'),
			'refrence_number'=>trans('item.refrence_number'),

			)
		);
		return $labels;
	}catch(Exception $e){
		Log::error("VatReportController@getLabels ".$e);
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
		Log::error("VatReportController@mallDetails ".$e);
		return View("errors.exception");
	}
}


}
