<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Customer extends Model
{
	protected $fillable = array(
		'title', 'name', 'spouse', 'birthday', 'anniversary', 'address', 'phone', 'mobile', 'fax', 'email', 'city', 'gender','age_group','prefered_mode','prefered_language','nationality','countryOfResidence','type','issuedBy','number','firstName','lastName'
		);

	protected $dates = array('birthday', 'anniversary');

	public function setPhoneAttribute($value){
		if(empty($value)){
			$this->attributes["phone"] = NULL;
		}else{
			$this->attributes["phone"] =  $value;
		}
	}

	public function setBirthdayAttribute($value){
		if($value!='' || $value!=null){
			$this->attributes["birthday"] =  Carbon::parse($value);
		}
		
	}

	public function setAnniversaryAttribute($value){
		if($value!='' || $value!=null){
			$this->attributes["Anniversary"] = Carbon::parse($value);
		}	 
	}

	public function homeDCustomer(){
		return $this->hasMany('App\BillingHomeDelivery',"customer_id","id");
	}

	public function countryCode(){
		return $this->hasOne('App\CountryCode',"id", "nationality");
	}
}
