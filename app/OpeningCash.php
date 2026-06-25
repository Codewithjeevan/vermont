<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OpeningCash extends Model
{
	public $table = "opening_cashes";

	protected $fillable = array('open_cash_date','opening_amt','user_id'); 

	protected $dates = array('open_cash_date');

	public function setOpeningCashDateAttribute($value){
		$this->attributes["open_cash_date"] = Carbon::parse($value);	 
	}

	public function getOpeningCashDateAttribute($value)
	{
		return Carbon::parse($value)->format('m/d/Y');
	}

	public function user(){
		return $this->hasOne("App\User","id","user_id");
	}


	public function getOpeningAmtAttribute($value){
		if($value !=null){
			return \Common::fixIt($value);	
		}else{
			return $value;	
		}
		
	}


	public function setOpeningAmtAttribute($value){
		if($value!=''&& $value!=null){
			$this->attributes["opening_amt"] =  \Common::fixIt($value);
		}else{
			$this->attributes["opening_amt"] =  $value;
		}
	}
}
