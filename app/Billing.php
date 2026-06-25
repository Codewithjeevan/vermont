<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Billing extends Model
{
	protected $fillable= array(
		"billing_status","invoice_type", "payment_mode","card_no","customer_id","subtotal","loyalty_points","loyalty_discount","earned_points","dis_amt","v_amt","cash_amt","card_amt","bil_amt","received", "balance","salesman_id","user_id","home_delivery",'reference_no','currency','tax_refund_flag'
		);

	public function itemList(){
		return $this->hasMany('App\BillingItems',"billing_id","id");
	}
	public function _customer(){
		return $this->hasOne('App\Customer',"id","customer_id");
	}
	public function homeDeliveryCustomer(){
		return $this->hasOne('App\BillingHomeDelivery',"billing_id","id");
	}
	public function taxRefund(){
		return $this->hasOne('App\BillingTaxRefund',"billId","id");
	}

	/** ROUND all values  */
	public function getDisAmtAttribute($value){
		if($value !=null){
			return \Common::fixIt($value);	
		}else{
			return $value;
		}
		
	}
	public function setDisAmtAttribute($value){
		if($value!=''&& $value!=null){
			$this->attributes["dis_amt"] =  \Common::fixIt($value);
		}else{
			$this->attributes["dis_amt"] =  $value;
		}
	}

	public function getVAmtAttribute($value){
		if($value !=null){
			return \Common::fixIt($value);	
		}else{
			return $value;
		}
		
	}
	public function setVAmtAttribute($value){
		if($value!=''&& $value!=null){
			$this->attributes["v_amt"] =  \Common::fixIt($value);
		}else{
			$this->attributes["v_amt"] =  $value;
		}
	}

	public function getCashAmtAttribute($value){
		if($value !=null){
			return \Common::fixIt( $value);	
		}else{
			return $value;
		}
		
	}
	public function setCashAmtAttribute($value){
		if($value!=''&& $value!=null){
			$this->attributes["cash_amt"] =  \Common::fixIt( $value);
		}else{
			$this->attributes["cash_amt"] =  $value;
		}
	}

	public function getCardAmtAttribute($value){
		if($value !=null){
			return \Common::fixIt( $value);	
		}else{
			return $value;
		}
		
	}
	
	public function setCardAmtAttribute($value){
		if($value!=''&& $value!=null){
			$this->attributes["card_amt"] =  \Common::fixIt($value);
		}else{
			$this->attributes["card_amt"] =  $value;
		}
	}

	public function getBilAmtAttribute($value){
		if($value !=null){
			return \Common::fixIt( $value);	
		}else{
			return $value;
		}
		
	}
	public function setBilAmtAttribute($value){
		if($value!=''&& $value!=null){
			$this->attributes["bil_amt"] =  \Common::fixIt( $value);
		}else{
			$this->attributes["bil_amt"] =  $value;
		}
	}

	public function getReceivedAttribute($value){
		if($value !=null){
			return \Common::fixIt($value);	
		}else{
			return $value;
		}
		
	}
	public function setReceivedAttribute($value){
		if($value!=''&& $value!=null){
			$this->attributes["received"] =  \Common::fixIt($value);
		}else{
			$this->attributes["received"] =  $value;
		}
	}
	

	public function getBalanceAttribute($value){
		if($value !=null){
			return \Common::fixIt( $value);	
		}else{
			return $value;
		}
		
	}
	public function setBalanceAttribute($value){
		if($value!=''&& $value!=null){
			$this->attributes["balance"] =  \Common::fixIt( $value);
		}else{
			$this->attributes["balance"] =  $value;
		}
	}

}
