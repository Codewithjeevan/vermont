<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class XYPromotion extends Model
{
	public $timestamps = false;
	protected $fillable = array(
		"x_item_id", "x_quantity","y_item_id","y_quantity","prmo_from","prmo_to"
		);

	protected $dates = array('prmo_from','prmo_to');

	public function item(){
		return $this->hasOne("App\Item", "id", "y_item_id");
	}

	public function setPrmoFromAttribute($value){
		$this->attributes["prmo_from"] = Carbon::parse($value);	 
	}
	public function getPrmoFromAttribute($value){
		if($value!=null){
			return Carbon::parse($value)->format('m/d/Y g:i A');
		}else{
			return $value;
		}
	}
	
	public function setPrmoToAttribute($value){
		$this->attributes["prmo_to"] = Carbon::parse($value);	 
	}
	
	public function getPrmoToAttribute($value){
		if($value!=null){
			return Carbon::parse($value)->format('m/d/Y g:i A');
		}else{
			return $value;
		}
	}

	public function setXQuantityAttribute($value){
		if($value==null || $value==''){
			$this->attributes["x_quantity"] = 0;	 
		}else{
			$this->attributes["x_quantity"] = \Common::fixQty($value);	 
		}
	}

	public function getXQuantityAttribute($value){
		if($value !=null){
			return \Common::fixQty($value);	
		}else{
			return $value;
		}
		
	}

	public function setYQuantityAttribute($value){
		if($value==null || $value==''){
			$this->attributes["y_quantity"] = 0;	 
		}else{
			$this->attributes["y_quantity"] = \Common::fixQty($value);	 
		}
	}

	public function getYQuantityAttribute($value){
		if($value !=null){
			return \Common::fixQty($value);	
		}else{
			return $value;
		}
		
	}

}
