<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
	protected $fillable = array("res_name", "res_desc", "slug", "parent_resource");

	public function privileges(){
		return $this->hasMany("App/Privilege");
	}

	public function parentResource(){
		return $this->hasOne("App/Resource", "id", "parent_resource");
	}
}
