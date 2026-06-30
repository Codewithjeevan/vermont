<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class Item extends Model
{
	protected $fillable = array(
		'productCode','serial_number', 'category', 'barcode', 'itemName','itemNameArabic', 'costPrise', 'salePrise','wholesale_salePrise','vat','cp_vat','description', 'unit', 'stock', 'min', 'max','expiryChk','packageChk','item_with_box','expiry_date', "image","promotion_from","promotion_to","promotion_price","x_y_prmoChk"
		);
	protected $casts = ['expiry_date' => 'datetime', 'promotion_from' => 'datetime', 'promotion_to' => 'datetime'];

	

	public function setMinAttribute($value){
		if($value==null || $value==''){
			$this->attributes["min"] = 0;	 
		}else{
			$this->attributes["min"] = \Common::fixQty($value);	 
		}
	}

	public function getMinAttribute($value){
		if($value !=null){
			return \Common::fixQty($value);	
		}else{
			return $value;
		}
		
	}

	public function setMaxAttribute($value){
		if($value==null || $value==''){
			$this->attributes["max"] = 0;	 
		}else{
			$this->attributes["max"] = \Common::fixQty($value);
		}
	}

	public function getMaxAttribute($value){
		if($value !=null){
			return \Common::fixQty($value);	
		}else{
			return $value;
		}
		
	}

	public function setPromotionPriceAttribute($value){
		if($value==null || $value==''){
			$this->attributes["promotion_price"] = 0;	 
		}else{
			$this->attributes["promotion_price"] = \Common::fixIt($value);
		}
	}

	public function getPromotionPriceAttribute($value){
		if($value !=null){
			return \Common::fixIt($value);	
		}else{
			return $value;
		}
	}
	

	public function packageList(){
		return $this->hasMany('App\Package',"itemId","id");
	}

	public function categories(){
		return $this->hasOne('App\Category',"id", "category");
	}

	public function units(){
		return $this->hasOne('App\Unit',"id", "unit");
	}

	public function offer(){
		return $this->belongsTo('App\XYPromotion','id', 'x_item_id');	
	}

	public function setExpiryDateAttribute($value){
		if($value!=null){
			$this->attributes["expiry_date"] = Carbon::parse($value);
		}else{
			$this->attributes["expiry_date"] = $value;
		}
		
	}

	public function setWholesaleSalepriseAttribute($value){
		if($value==null || $value==''){
			$this->attributes["wholesale_salePrise"] = 0;
		}else{
			$this->attributes["wholesale_salePrise"] = $value;
		}
		
	}

	public function getExpiryDateAttribute($value)
	{
		if($value!=null){
			return Carbon::parse($value)->format('m/d/Y');
		}else{
			return $value;
		}
		
	}
	public function setPromotionFromAttribute($value){
		if($value!=null || $value!=''){
			$this->attributes["promotion_from"] = Carbon::parse($value);	
		} else{
			$this->attributes["promotion_from"] = $value;	
		}
	}
	

	public function getPromotionFromAttribute($value){

		if($value!=null){
			return Carbon::parse($value)->format('m/d/Y g:i A');
		}else{
			return $value;
		}
		
	}
	public function setPromotiontoAttribute($value){
		if($value!=null || $value!=''){
			$this->attributes["promotion_to"] = Carbon::parse($value);	
		}else{
			$this->attributes["promotion_to"] = $value;	
		} 
	}

	public function getPromotionToAttribute($value){

		if($value!=null){
			return Carbon::parse($value)->format('m/d/Y g:i A');
		}else{
			return $value;
		}
	}

	public function SupplierItems(){
		return $this->belongsTo("App\PurchasedItem", "item_id");
	}


	public function getStockAttribute($value){
		if($value !=null){
			return \Common::fixQty($value);	
		}else{
			return $value;
		}
		
	}
	public function setStockAttribute($value){
		if($value!=''&& $value!=null){
			$this->attributes["stock"] =  \Common::fixQty($value);
		}else{
			$this->attributes["stock"] =  $value;
		}
	}

	public function setVatAttribute($value){
		if($value==null || $value==''){
			$this->attributes["vat"] = 0;	 
		}else{
			$this->attributes["vat"] = \Common::fixIt($value);	 
		}
	}

	public function getVatAttribute($value){
		if($value !=null){
			return \Common::fixIt($value);	
		}else{
			return \Common::fixIt(0);
		}
		
	}
	public function setCpVatAttribute($value){
		if($value==null || $value==''){
			$this->attributes["cp_vat"] = 0;	 
		}else{
			$this->attributes["cp_vat"] = \Common::fixIt($value);	 
		}
	}

	public function getCpVatAttribute($value){
		if($value !=null){
			return \Common::fixIt($value);	
		}else{
			return \Common::fixIt(0);
		}
		
	}

	
}
