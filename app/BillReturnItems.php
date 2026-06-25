<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BillReturnItems extends Model
{
	protected $fillable = [
	"return_id", "item_id","serial_no", "qty","offer_type","rate","vat","existId"
	];

	public $timestamps = false;

	public function items(){
		return $this->hasOne('App\Item',"id","item_id");
	}


	public function getQtyAttribute($value){
		if($value !=null){
			return \Common::fixQty($value);	
		}else{
			return $value;
		}
		
	}
	public function setQtyAttribute($value){
		if($value!=''&& $value!=null){
			$this->attributes["qty"] =  \Common::fixQty($value);
		}else{
			$this->attributes["qty"] =  $value;
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
