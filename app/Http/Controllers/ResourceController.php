<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Resource;
use Toastr;

use Exception;
use Log;

class ResourceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        try{
            $parentResource = Resource::where("parent_resource","=", "0")->get();

            foreach ($parentResource as $r) {
                $resources[] = array(
                    'parent' => array($r),
                    'child' => Resource::where("parent_resource","=", $r->id)->get()
                    );
            }

            return View('pages.settings.resources.resource',[
                'resources'=> $resources,
                'parentResoureces' => $parentResource
                ]);

        }catch(Exception $e){
            Log::error("ResourceController@index ".$e);
            return View("errors.exception");
        }
    }

    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'slug' => 'required|unique:resources|max:255',
            ]);

        try{
            $task = Resource::create($request->all());
            toastr::success('Success', trans("validation.success_store",[ "attribute" => trans('glob.resource') ]));
            return redirect()->action('ResourceController@index');
        }catch(Exception $e){
            Log::error("ResourceController@store ".$e);
            return View("errors.exception");
        }

    }

    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try{
            $parentResource = Resource::where("parent_resource","=", "0")->get();
            foreach ($parentResource as $r) {
                $resources[] = array(
                    'parent' => array($r),
                    'child' => Resource::where("parent_resource","=", $r->id)->get()
                    );
            }
            $eResource= Resource::where('id', $id)->first(); 
            return View('pages.settings.resources.resource',[
                'resources'=> $resources,
                'eResource'=> $eResource,
                'parentResoureces' => $parentResource
                ]);
        }catch(Exception $e){
            Log::error("ResourceController@edit ".$e);
            return View("errors.exception");
        }
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $this->validate($request, [
            'slug' => 'required|unique:resources,slug,'.$id.'|max:255',
            ]);

        try{
            $resourceData = Resource::find($id);

            if(!is_null($resourceData)){

                $input = $request->all();
                $resourceData->fill($input)->save();
                toastr::success('Success', trans("validation.success_update",[ "attribute" => trans('glob.resource') ]));
                return redirect()->action('ResourceController@index');

            }else{
                toastr::warning('Warning', trans("validation.warning_update_deleted_item",["attribute" => trans('glob.resource') ]));
                return redirect()->action('ResourceController@index');
            }

        }catch(Exception $e){
            Log::error("ResourceController@update ".$e);
            return View("errors.exception");
        }
        

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        try{
            $resource  = Resource::find($id);
            
            (!is_null($resource)) ?  ($r = $resource->delete()) : ($r = null);
            
            if($r){
                $res = array('status' =>  "success", "data" =>array(), "message" =>trans("validation.success_delete",["attribute" => trans('glob.resource') ]));
            }
            else{
                $res = array('status' =>  "error", "data" =>array(), "message" =>  trans("validation.error_delete",["attribute" => trans('glob.resource') ]));
            }

            return response($res, 200);
        }catch(Exception $e){
            Log::error("ResourceController@destroy ".$e);
            return View("errors.exception");
        }
        
    }
}
