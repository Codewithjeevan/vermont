<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BrandClassification extends Model
{
    protected $fillable = array('brand_id', 'activation_amount');

public function parentCategory(){
    return $this->hasOne('App\Category',"id", "brand_id");
}
}
