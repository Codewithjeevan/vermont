<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
	public static $supplierType = array(
		"Supplier","Branch","Dealer","Godown","Manufacturer", "Retail"
		);
	
	protected $fillable = array(
		'type','title', 'name', 'contact', 
		'address',  'phone', 'mobile', 'fax',
		'email','city', 'ecc', 'cst', 'range',
		'division', 'comm', 'pan', 'tin'
		);

	public function setTitleAttribute($value){
		
		if($value==null || $value==''){
			$this->attributes["title"] = 0;	 
		}else{
			$this->attributes["title"] = $value;
		}
	}



	
}
