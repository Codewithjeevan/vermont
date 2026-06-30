<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Payment extends Model
{
	protected $fillable = array(
		"payment_date", "supplier_id", "bill_no", "mode", "bank_id", "ch_no", "ch_date", "paid_amount", "remark"
		);

	protected $casts = ['payment_date' => 'datetime', 'ch_date' => 'datetime'];

	public function getPaymentDateAttribute($value){
		return Carbon::parse($value)->format("m/d/Y");
	}


	public function setPaymentDateAttribute($value){
		if($value!=''&& $value!=null){
			$this->attributes["payment_date"] = carbon::parse($value);
		}else{
			$this->attributes["payment_date"] = $value;
		}
	}

	public function getPaidAmountAttribute($value){
		if($value !=null){
			return \Common::fixIt($value);	
		}else{
			return $value;
		}
		
	}


	public function setPaidAmountAttribute($value){
		
			$this->attributes["paid_amount"] =  $value;
		
	}


	public function getChDateAttribute($value){
		if($value!=''&& $value!=null){
			return Carbon::parse($value)->format("m/d/Y");
		}else{
			return $value;
		}
		
	}


	public function setChDateAttribute($value){
		if($value!=''&& $value!=null){
			$this->attributes["ch_date"] = carbon::parse($value);
		}else{
			$this->attributes["ch_date"] = $value;
		}
	}


	public function _supplier(){
		return $this->hasOne("App\Supplier", "id", "supplier_id");
	}
}
