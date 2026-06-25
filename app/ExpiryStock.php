<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ExpiryStock extends Model
{
	protected $fillable = array('slip_date','enterd_by','approx_value_of');

	protected $dates = array('slip_date');

	public function setSlipDateAttribute($value){
		$this->attributes["slip_date"] =  Carbon::parse($value);
	}
	public function getSlipDateAttribute($value)
	{
		return Carbon::parse($value)->format('m/d/Y');
	}

	public function expiryItemList(){
		return $this->hasMany('App\ExpiryStockItem',"expiry_order_id","id");
	}

	public function users(){
		return $this->hasOne('App\User',"id","enterd_by");
	}

}
