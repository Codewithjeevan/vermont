<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturnItem extends Model
{
    protected $fillable = array(
    	"purchase_return_id", "item_id",  "costPrise","cp_vat", "qty", "unit",   "discount", "totalDiscount", "amount"
    	);

    public $timestamps = false;

    public function item(){
    	return $this->hasOne("App\Item", "id", "item_id");
    }

    public function _unit(){
    	return $this->hasOne("App\Unit", "id", "unit");	
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
        }
        else{
            $this->attributes["qty"] = $value;
        }
    }


    public function getDiscountAttribute($value){
        if($value !=null){
            return \Common::fixIt($value); 
        }else{
            return $value;
        }
        
    }
    public function setDiscountAttribute($value){
        if($value!=''&& $value!=null){
            $this->attributes["discount"] =  \Common::fixIt($value);
        }else{
            return $value;
        }
    }

    public function getAmountAttribute($value){
        if($value !=null){
            return \Common::fixIt($value); 
        }else{
            return $value;
        }
        
    }
    public function setAmountAttribute($value){
        if($value!=''&& $value!=null){
            $this->attributes["amount"] =  \Common::fixIt($value);
        }else{
            return $value;
        }
    }
}
