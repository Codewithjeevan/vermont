<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Loyalty extends Model
{
    protected $fillable = [
    	"key",
    	"value"
    ];

    public $fields = [
    	"loyalty_activation" => 0,
    	"loyalty_points_on_sale" => 0,
    	"loyalty_points" => 0,
    	"loyalty_redeem_maturity" => 0,
    	"loyalty_on_redeem_points" => 0,
    	"loyalty_on_redeem_discount" => 0
    ];

}
