<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BillReturns extends Model
{
	
	protected $fillable= array(
		"billing_id","amount",
		);
	
	
	public function rItem(){
		return $this->hasMany('App\BillReturnItems',"return_id","id");
	}
	public function bill(){
		return $this->hasOne('App\Billing',"id","billing_id");
	}


	public function getAmountAttribute($value){
		if($value !=null){
			return \Common::fixIt($value);	
		}else{
			return $value;
		}
		
	}
	public function setAmountAttribute($value){
		if($value!=''&& $value!=null){
			$this->attributes["amount"] =  \Common::fixIt($value);
		}else{
			$this->attributes["amount"] =  $value;
		}
	}
	
}
