<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OpeningStock extends Model 
{

	public $table = "opening_stock";

	protected $fillable = array('open_stock_date','itemId','openingStock','unit'); 

	protected $casts = ['open_stock_date' => 'datetime'];

	public function setOpenStockDateAttribute($value){
		if($value!=null){
			$this->attributes["open_stock_date"] =  Carbon::parse($value);
		}else{
			$this->attributes["open_stock_date"] =  $value;
		}
	} 
	public function getOpenStockDateAttribute($value){
		if($value!=null){
			return Carbon::parse($value)->format('m/d/Y');
		}else{
			return $value;
		}
	}


	public function item(){
		return $this->hasOne("App\Item","id","itemId");
	}

	public function units(){
		return $this->hasOne("App\Unit","id","unit");
	}

	
}
