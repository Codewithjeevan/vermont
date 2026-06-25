<?php

namespace App\Http\Middleware;

use Closure;

class checkAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        /** View Access */
        if($request->segment(2)!=''){

            $slug= $request->segment(2);
            if($slug=="billing"){
                $slug ="billing1";
            }
            if(\Common::has_access($slug))
                return $next($request);    
            else
                return Redirect('/error/'.$request->segment(2));
        }else{
            return Redirect('/home');
        }

        }
}
