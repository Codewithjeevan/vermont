<?php
namespace App\Http\ViewComposers;

use Illuminate\View\View;

class CommonViewComposer { 

	public function compose(View $view)
    {
    	 $view->with([]);
    }
}
