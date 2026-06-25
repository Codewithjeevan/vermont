<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Salesman extends Model
{
    public $table = "salesman";
    
	var $desiginationType = array(
			"Salesman" 
		);
	
    
    /**
     * The attributes that are mass assignable. 
     *
     * @var array
     */
    protected $fillable = [
        'name','desigination',
    ];


	 /**
     * changed salesman desigination number to value.
     *
     * @param  Integer  $value
     * @return string
     */
    public function getDesiginationAttribute($value)
    {
     	return $this->desiginationType[$value];   
    }
    public static function getTypeIdAttribute($value)
    {
        $c = new Salesman; 
        return array_search($value, $c->desiginationType);   
    }
}
