<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlanetAuth extends Model
{
    protected $table='planet_auths';
    protected $fillable = array('client_id','client_secret','terminal_code','vat_code','merchandise_group_code','store_name','payment_method_code','user_name','password');

}
