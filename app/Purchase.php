<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Purchase extends Model
{
	protected $fillable=array(
		'mrn_date', 'po_no', 'supplier_id', 'supplier_billdate', 'supplierBillNo', 'purchase', 'receivedBy', 'subTotal','vat_amt', 'disAmount','extraDiscount', 'billAmount'
	); 

	protected $casts = ['mrn_date' => 'datetime', 'expiryDate' => 'datetime', 'supplier_billdate' => 'datetime'];


	public function purchasedItems(){
		return $this->hasMany("App\PurchasedItem");	
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

	public function _supplier(){
		return $this->hasOne("App\Supplier","id","supplier_id");
	}
	public function _purchasedItem(){
		return $this->hasOne("App\PurchasedItem","purchase_id","id");
	}

	public function purchaseOrder(){
		return $this->hasOne("App\PurchaseOrder","id","po_no");
	}

	

}
