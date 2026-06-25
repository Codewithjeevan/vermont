<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Customer;
use App\Bank;
class CustomerReceipt extends Model
{
	protected $fillable = array("id","receipt_date","customer_id","against_bill_id","pay_mode","cheque_draft_no","cheque_draft_date","bank_id","dep_bank","receipt_amt","remark");

	protected $dates = array("receipt_date","cheque_draft_date");

	public function setReceiptDateAttribute($value){
		$this->attributes["receipt_date"] =  Carbon::parse($value);
	}
	public function getReceiptDateAttribute($value){
		if($value!=null){
			return Carbon::parse($value)->format('d M Y');
		}
	}
	
	public function setChequeDraftDateAttribute($value){
		if($value!=null){
			$this->attributes["cheque_draft_date"] =  Carbon::parse($value);
		}
	}
	public function getChequeDraftDateAttribute($value){
		if($value!=null){
			return Carbon::parse($value)->format('d M Y');
		}
	}
	
	public function customer(){
		return $this->hasOne('App\Customer',"id", "customer_id");
	}
	public function bank(){
		return $this->hasOne('App\Bank',"id", "bank_id");
	}


	public function getReceiptAmtAttribute($value){
		if($value !=null){
			return \Common::fixIt($value);	
		}else{
			return $value;
		}
		
	}


	public function setReceiptAmtAttribute($value){
		
			$this->attributes["receipt_amt"] =  $value;
		
	}
}
