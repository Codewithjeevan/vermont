<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BillingHomeDelivery extends Model
{
	public $timestamps = false;

	protected $fillable = [
	"customer_id", "billing_id", "name","email","address", "flat_no", "blg", "street"
	];
	

	
}
