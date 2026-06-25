<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
	public $timestamps = false;
	protected $fillable = array(
		"itemId", "packageId","qty"
		);

	public function items(){
		return $this->hasMany("App\Item", "id", "packageId");
	}

	public function setQtyAttribute($value){
		if($value==null || $value==''){
			$this->attributes["qty"] = 0;	 
		}else{
			$this->attributes["qty"] = \Common::fixQty($value);	 
		}
	}

	public function getQtyAttribute($value){
		if($value !=null){
			return \Common::fixQty($value);	
		}else{
			return $value;
		}
		
	}
}
