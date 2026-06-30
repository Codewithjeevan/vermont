<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request; 
use App\Location;
use Carbon\Carbon;
use Toastr;
use Common;
use Excel;
use PDF;

use Exception;
use Log;

class LocationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() 
    {

        try{
            $locations = Location::all();
            $newId = Common::get_autoIncremenetVal('locations');

            return View('pages.masters.locations.location',[
                'locations'=> $locations,
                'newId'    => $newId
                ]);   
        }catch(Exception $e){
            Log::error("LocationsController@index ".$e);
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
            'phone' => 'unique:locations|max:255',
            'mobile' => 'required|unique:locations|max:255',
            'email' => 'required|unique:locations|max:255',
            'address'   => 'required',
            'terms'   => 'required'
            ],[
            'email.unique' => "The email has already been taken.",
            ]);

        try{
            $task = Location::create($request->all());       
            toastr::success('Success', trans("validation.success_store",[ "attribute" => trans('glob.location') ]));
            return redirect()->action('LocationsController@index');
        }catch(Exception $e){
            Log::error("LocationsController@store ".$e);
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
            $locations = Location::all();
            $editLocation = Location::where('id', $id)->first(); 
            return View('pages.masters.locations.location',[
                'locations'=> $locations,
                'editLocation'=> $editLocation,
                ]);
        }catch(Exception $e){
            Log::error("LocationsController@edit ".$e);
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
            'phone' => 'unique:locations,phone,'.$id.'|max:255',
            'mobile' => 'required|unique:locations,mobile,'.$id.'|max:255',
            'email' => 'required|unique:locations,email,'.$id.'|max:255',
            'address'   => 'required',
            'terms'   => 'required'
            ],[
            'email.unique' => "The email has already been taken.",
            ]);

        try{
            $locationData = Location::find($id);

            if(!is_null($locationData)){
                $input = $request->all();
                $locationData->fill($input)->save();     
                toastr::success('Success', trans("validation.success_update",[ "attribute" => trans('glob.location') ]));
            }else{
                toastr::warning('warning', trans("validation.resource_not_available"));
            }
            return redirect()->action('LocationsController@index');

        }catch(Exception $e){
            Log::error("LocationsController@update ".$e);
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
            $location  = Location::findOrFail($id);
            if(!is_null($location)){
                $r = $location->delete();
            } else{ 
                $r = null; 
                $res = array('status' =>  "warning", "data" =>array(), "message" => trans("validation.resource_not_available"));  
            }

            if($r)            
                $res = array('status' =>  "success", "data" =>array(), "message" =>trans("validation.success_delete",["attribute" => trans('glob.location') ]));

            else
                $res = array('status' =>  "error", "data" =>array(), "message" => trans("validation.error_delete",["attribute" => trans('glob.location') ]));            

            return response($res, 200);

        }catch(Exception $e){
            Log::error("LocationsController@destroy ".$e);
            return View("errors.exception");
        }
    }

    /** export to xls Location list */
    public function exportToXls(){
        try{
            $data = Location::all();
            if(sizeof($data)){
               $carbon = Carbon::today();
               $timestamp = $carbon->timestamp;
               $format = $carbon->format('Y-m-d');
               $exportName = 'Location'.$format; 

               \App\Legacy\LegacyExcel::create($exportName, function ($excel) {

                $excel->sheet('Location', function ($sheet) {

                    $dataList = Location::get();      
                    $itemList = array();
                    foreach ($dataList as $o) {              

                      array_push($itemList, array(
                         trans('glob.id')  => $o->id,
                         trans('glob.location')  => $o->location, 
                         trans('glob.name')  => $o->name, 
                         trans('glob.city')  => $o->city, 
                         trans('glob.phone')  => $o->phone, 
                         trans('glob.mobile')  => $o->mobile, 
                         trans('glob.email')  => $o->email, 
                         trans('supplier.tin_no')  => $o->tin, 
                         trans('glob.address')  => $o->address, 
                         trans('glob.terms')  => $o->terms                 

                         )
                      );
                  }          

                  /* setting column names for data - you can of course set it manually */
                  $sheet->appendRow(array_keys($itemList[0])); /* column names */

                  /* getting last row number (the one we already filled and setting it to bold */
                  $sheet->row($sheet->getHighestRow(), function ($row) {
                    $row->setFontWeight('bold');
                });

                  /* putting users data as next rows */
                  foreach ($itemList as $user) {
                    $sheet->appendRow($user);
                }
            });

            })->export('xlsx');


               return back();

           }else{
            toastr::warning('Warning', trans("validation.export_error"));
            return back();
        }

    }catch(Exception $e){
        Log::error("LocationsController@exportToXls ".$e);
        return View("errors.exception");
    }
}
/** export to PDF Location list */
public function exportToPdf(){
    try{
        $data = Location::all();
        if(sizeof($data)){
            $pdf = PDF::loadView('pages.masters.locations.location_pdf', [
                'data' => $data
                ]); 
            $pdf->setPaper('a4');
            $carbon = Carbon::today();
            $timestamp = $carbon->timestamp;
            $format = $carbon->format('Y-m-d');
            $exportName = 'Location'.$format.'.pdf'; 
            return $pdf->download($exportName);
        }else{
            toastr::warning('Warning', trans("validation.export_error"));
            return back();
        }
    }catch(Exception $e){
        Log::error("LocationsController@exportToPdf ".$e);
        return View("errors.exception");
    }
}   

}
