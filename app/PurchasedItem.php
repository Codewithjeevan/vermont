<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchasedItem extends Model
{
    protected $fillable= array(
      "purchase_id", "item_id", "qty", "unit", "costPrise", "expiryDate", "discount", "totalDiscount", "amount","cp_vat"
      );

    protected $casts = ['expiryDate' => 'datetime'];

    public function item(){
        return $this->hasOne("App\Item", "id", "item_id");	
    }

    public function _unit(){
        return $this->hasOne("App\Unit", "id", "unit");	
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
            $this->attributes["amount"] =  $value;
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
            $this->attributes["discount"] =  $value;
        }
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
            $this->attributes["qty"] = \Common::fixQty($value);
        }else{
            $this->attributes["qty"]  = $value;
        }
    }

}
