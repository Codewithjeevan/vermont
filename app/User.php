<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password','roleId', 'floor'
    ];
    
    public static $floor = array(
           '', 'Floor 1', 'Floor 2', 'Floor 3'
        );

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];



    public function setRoleIdAttribute($value){
        if($value==""){
            $this->attributes['roleId'] = 1;
        }else{
            $this->attributes['roleId']=$value;
        }
    }

    public function setPasswordAttribute($value){
        $this->attributes["password"] = bcrypt($value);
    }

    public function privileges()
    {
        return $this->hasMany("App\Privilege","roleId","roleId");
    }

    public function role()
    {
        return $this->belongsTo("App\Role",'roleId','id');
    }

}
