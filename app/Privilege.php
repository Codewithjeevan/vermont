<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Privilege extends Model
{
	protected $fillable = ["resourceId", "roleId", "view","create", "update", "delete", "viewAll"];
	public $timestamps = false;

	public function role(){
		return $this->belongsToMany("App/Role");
	}

	public function resource(){
		return $this->belongsToMany("App/Resource");
	}
}
