<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BillingDelete extends Model
{
	protected $fillable = array("bill_id","amount","user_id","remark","taxRefundTagNumber");

	public function _user(){
		return $this->hasOne('App\User',"id","user_id");
	}
}
