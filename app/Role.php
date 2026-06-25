<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = array('role','max_discount');

	public function privileges(){
		return $this->hasMany('App\Privilege',"roleId", "id");
	}
}
