<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    
    protected $fillable = array(
		'location', 'name', 'city', 'phone', 'mobile', 'email', 'tin' , 'address', 'terms'
		);
}
