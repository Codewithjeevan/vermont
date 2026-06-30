<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class PurchaseReturn extends Model
{
    protected $fillable=array(
		'return_date', 'supplier_id', 'supplier_billno', 'return_through', 'subTotal', 'disAmount','extraDiscount', 'billAmount'
		); 

	protected $casts = ['return_date' => 'datetime'];

	public function _supplier(){
		return $this->hasOne("App\Supplier","id", "supplier_id");
	}

	public function returnItems(){
		return $this->hasMany("App\PurchaseReturnItem","purchase_return_id", "id");
	}

	public function getMrnDateAttribute($value){
		return Carbon::parse($value)->format("m/d/Y");
	}


	public function setMrnDateAttribute($value){
		$this->attributes["mrn_date"] = carbon::parse($value);
	}


	public function getSupplierBilldateAttribute($value){
		return Carbon::parse($value)->format("m/d/Y");
	}

	public function setSupplierBilldateAttribute($value){
		$this->attributes["supplier_billdate"] = carbon::parse($value);
	}


	public function getReturnDateAttribute($value){
		return Carbon::parse($value)->format("m/d/Y");
	}

	public function setReturnDateAttribute($value){
		$this->attributes["return_date"] = carbon::parse($value);
	}

	public function getLrDateAttribute($value){
		return Carbon::parse($value)->format("m/d/Y");
	}

	public function setLrDateAttribute($value){
		$this->attributes["lr_date"] = carbon::parse($value);
	}
}
