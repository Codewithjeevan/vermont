<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ClubBarcode extends Model
{
	protected $fillable = array(
		"item_id", "quantity","size"
		);

	public function items (){
		return $this->hasOne("App\Item", "id" , "item_id");
	}
}
