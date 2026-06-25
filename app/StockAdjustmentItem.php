<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockAdjustmentItem extends Model
{
	public $timestamps = false;
	protected $fillable = array(
		"stock_adjustment_id", "item_id", "quantity","rate","amt","adjust_through"
		);

	public function items (){
		return $this->hasOne("App\Item", "id" , "item_id");
	}


	public function getQuantityAttribute($value){
		if($value !=null){
			return \Common::fixQty($value);	
		}else{
			return $value;
		}
		
	}
	public function setQuantityAttribute($value){
		if($value!=''&& $value!=null){
			$this->attributes["quantity"] =  \Common::fixQty($value);
		}else{
			$this->attributes["quantity"] =  $value;
		}
	}

	public function getAmtAttribute($value){
		if($value !=null){
			return \Common::fixIt($value);	
		}else{
			return $value;
		}
		
	}
	public function setAmtAttribute($value){
		if($value!=''&& $value!=null){
			$this->attributes["amt"] =  \Common::fixIt($value);
		}else{
			$this->attributes["amt"] =  $value;
		}
	}

	public function getRateAttribute($value){
		if($value !=null){
			return \Common::fixIt($value);	
		}else{
			return $value;
		}
		
	}
	public function setRateAttribute($value){
		if($value!=''&& $value!=null){
			$this->attributes["rate"] =  \Common::fixIt($value);
		}else{
			$this->attributes["rate"] =  $value;
		}
	}
}
