<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class zSales extends Model
{
    public $timestamps = false;
	protected $fillable = array(
		"day_close");

	protected $dates = array('day_close');	

	public function setDayCloseAttribute($value){
		$this->attributes["day_close"] = Carbon::parse($value);	 
	}
	public function getDayCloseAttribute($value){
		if($value!=null){
			return Carbon::parse($value)->format('m/d/Y g:i A');
		}else{
			return $value;
		}
	}
}
