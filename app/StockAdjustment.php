<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class StockAdjustment extends Model
{
	protected $fillable = array('stock_adjustment_date','user_id');

	protected $casts = ['stock_adjustment_date' => 'datetime'];

	public function setStockAdjustmentDateAttribute($value){
		$this->attributes["stock_adjustment_date"] =  Carbon::parse($value);
	}

	
	public function getStockAdjustmentDateAttribute($value)
	{
		return Carbon::parse($value)->format('m/d/Y');
	}


	public function stockAdjustmentItemList(){
		return $this->hasMany('App\StockAdjustmentItem',"stock_adjustment_id","id");
	}	

}
