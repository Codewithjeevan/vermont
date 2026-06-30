<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class IssueStock extends Model
{
	protected $fillable = array('slip_date', 'approx_value_of');

	protected $casts = ['slip_date' => 'datetime'];

	public function setSlipDateAttribute($value){
		$this->attributes["slip_date"] =  Carbon::parse($value);
	}

	
	public function getSlipDateAttribute($value)
	{
		return Carbon::parse($value)->format('m/d/Y');
	}


	public function issueItemList(){
		return $this->hasMany('App\IssueStockItem',"issue_stocks_id","id");
	}

	public function getApproxValueOfAttribute($value){
		if($value !=null){
			return \Common::fixIt($value);	
		}else{
			return $value;
		}
		
	}

	public function setApproxValueOfAttribute($value){
		if($value!=''&& $value!=null){
			$this->attributes["approx_value_of"] =  \Common::fixIt($value);
		}else{
			$this->attributes["approx_value_of"] =  $value;
		}
	}

}
