<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CountryCode extends Model
{
    protected $fillable=array('country_id','Name','code','code2','code3digit');
   
}
