<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DamageStock extends Model
{
	protected $fillable = array('slip_date','enterd_by','approx_value_of', 'approx_value_of_cp','type');

	protected $dates = array('slip_date');

	public function setSlipDateAttribute($value){
		$this->attributes["slip_date"] =  Carbon::parse($value);
	}
	public function getSlipDateAttribute($value)
	{
		return Carbon::parse($value)->format('m/d/Y');
	}

	public function damageItemList(){
		return $this->hasMany('App\DamageStockItem',"damage_order_id","id");
	}

	public function expiryItemList(){
		return $this->hasMany('App\ExpiryItems',"expiry_order_id","id");
	}

	public function users(){
		return $this->hasOne('App\User',"id","enterd_by");
	}

	public function getApproxValueOfAttribute($value){
		if($value !=null){
			return \Common::fixIt($value);	
		}else{
			return 0;
		}
		
	}


	public function setApproxValueOfAttribute($value){
		if($value!=''&& $value!=null){
			$this->attributes["approx_value_of"] =  \Common::fixIt($value);
		}else{
			$this->attributes["approx_value_of"] =  0;
		}
	}

	public function getApproxValueOfCpAttribute($value){
		if($value !=null){
			return \Common::fixIt($value);	
		}else{
			return 0;
		}
		
	}


	public function setApproxValueOfCpAttribute($value){
		if($value!=''&& $value!=null){
			$this->attributes["approx_value_of_cp"] =  \Common::fixIt($value);
		}else{
			$this->attributes["approx_value_of_cp"] =  0;
		}
	}
}
