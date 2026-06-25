<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = array('categorie', 'parent_category');

    public function parentCategory(){
    	return $this->hasOne('App\Category',"id", "parent_category");
    }
}
