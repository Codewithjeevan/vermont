<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExpiryItems extends Model
{
	public $timestamps = false;
	protected $fillable = array(
		"expiry_order_id","mrn_no", "item_id", "quantity","rate","amt","remark"
		);

	public function items (){
		return $this->hasOne("App\Item", "id" , "item_id");
	}

	public function getQuantityAttribute($value){
		if($value !=null){
			return \Common::fixQty($value);	
		}else{
			return 0;
		}
		
	}
	public function setQuantityAttribute($value){
		if($value!=''&& $value!=null){
			$this->attributes["quantity"] =  \Common::fixQty($value);
		}else{
			$this->attributes["quantity"] =  0;
		}
	}
	public function getRateAttribute($value){
		if($value !=null){
			return \Common::fixIt($value);	
		}else{
			return 0 ;
		}
		
	}
	public function setRateAttribute($value){
		if($value!=''&& $value!=null){
			$this->attributes["rate"] =  \Common::fixIt($value);
		}else{
			$this->attributes["rate"] =  0;
		}
	}


	public function getAmtAttribute($value){
		if($value !=null){
			return \Common::fixIt($value);	
		}else{
			return 0;
		}
		
	}
	public function setAmtAttribute($value){
		if($value!=''&& $value!=null){
			$this->attributes["amt"] =  \Common::fixIt($value);
		}else{
			$this->attributes["amt"] =  0;
		}
	}
}
