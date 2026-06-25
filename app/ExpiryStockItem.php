<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExpiryStockItem extends Model
{
	public $timestamps = false;
	protected $fillable = array(
		"expiry_order_id", "item_id", "quantity","rate","amt","remark"
		);

	public function items (){
		return $this->hasOne("App\Item", "id" , "item_id");
	}
}
