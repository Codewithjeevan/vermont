<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PurchaseOrder extends Model
{
	protected $fillable = array('purchase_order_date', "supplier_id");

	protected $casts = ['purchase_order_date' => 'datetime'];

	public function setPurchaseOrderDateAttribute($value){
		$this->attributes["purchase_order_date"] = Carbon::parse($value);	 
	}

	public function getPurchaseOrderDateAttribute($value)
	{
		return Carbon::parse($value)->format('m/d/Y');
	}
	public function orderItemList(){
		return $this->hasMany('App\PurchaseOrderItem',"purchase_order_id","id");
	}

	public function _supplier(){
		return $this->hasOne('App\Supplier',"id","supplier_id");
	}

	public function _purchase(){
		return $this->hasOne('App\Purchase',"po_no","id");
	}
}
