<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
	public $timestamps = false;
	protected $fillable = array(
		"purchase_order_id", "item_id", "quantity","unit","cost_price"
		);

	public function items (){
		return $this->hasOne("App\Item", "id" , "item_id");
	}
	public function units (){
		return $this->hasOne("App\Unit", "id" , "unit");
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
			$this->attributes["quantity"]  = $value;
		}
	}

	public function getCostPriceAttribute($value){
		if($value !=null){
			return \Common::fixIt($value); 
		}else{
			return $value;
		}
		
	}
	public function setCostPriceAttribute($value){
		if($value!=''&& $value!=null){
			$this->attributes["cost_price"] =  \Common::fixIt($value);
		}else{
			$this->attributes["cost_price"] = $value;
		}
	}
}
