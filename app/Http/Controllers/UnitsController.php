<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Unit;
use Toastr;
use Excel;
use PDF;
use Carbon\Carbon;
use App\Item;

use Exception;
use Log;

class UnitsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $unit = Unit::all();
            return View('pages.masters.unit.unit',[
                'unit'=> $unit
                ]);
        }catch(Exception $e){
            Log::error("UnitsController@index ".$e);
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
            'unit' => 'required|unique:units|max:255'
            ]);
        try{
            $task = Unit::create($request->all());
            toastr::success('Success', trans("validation.success_store",[ "attribute" => trans('glob.unit') ]));
            return redirect()->action('UnitsController@index');
        }catch(Exception $e){
            Log::error("UnitsController@store ".$e);
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
            $unit = Unit::all();
            $editUnit= Unit::where('id', $id)->first(); 
            return View('pages.masters.unit.unit',[
                'unit'=> $unit,
                'editUnit'=> $editUnit, 
                ]);
        }catch(Exception $e){
            Log::error("UnitsController@edit ".$e);
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
            'unit' => 'required|unique:units,unit,'.$id.'|max:255'
            ]);

        try{
            $unitData = Unit::find($id);

            if(!is_null($unitData)){
                $input = $request->all();
                $unitData->fill($input)->save();
                toastr::success('Success', trans("validation.success_update",[ "attribute" => trans('glob.unit') ]));
                return redirect()->action('UnitsController@index');
            }else{
                toastr::warning('Warning', trans("validation.warning_update_deleted_item",["attribute" => trans('glob.unit') ]));
                return redirect()->action('UnitsController@index');
            }
        }catch(Exception $e){
            Log::error("UnitsController@update ".$e);
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
            $unit  = Unit::find($id);
            $item = Item::where("unit", "=", $id)->get();
            if(!$item->isEmpty()){
                $res = array('status' =>  "warning", "data" =>array(), "message" => "Unit is not able to delete as this is associated with Item ");
                return Response($res);
            }

            (!is_null($unit)) ?  ($r = $unit->delete()) : ($r = null);

            if($r){
                $res = array('status' =>  "success", "data" =>array(), "message" => trans("validation.success_delete",["attribute" => trans('glob.unit') ]));
            }
            else{
                $res = array('status' =>  "error", "data" =>array(), "message" => trans("validation.error_delete",["attribute" => trans('glob.unit') ]));
            }
            return response($res, 200);

        }catch(Exception $e){
            Log::error("UnitsController@destroy ".$e);
            return View("errors.exception");
        }
    }

    /** export to xls Unit list */
    public function exportToXls(){
        try{
            $data = Unit::all();
            if(sizeof($data)){
                $carbon = Carbon::today();
                $timestamp = $carbon->timestamp;
                $format = $carbon->format('Y-m-d');
                $exportName = 'Unit'.$format;
                \App\Legacy\LegacyExcel::create($exportName, function ($excel) {
                    $excel->sheet('Unit', function ($sheet) {
                        $dataList = Unit::get(); 
                        $itemList = array();
                        foreach ($dataList as $o) {              
                          array_push($itemList, array(
                             trans('glob.id')  => $o->id,
                             trans('glob.unit')  => $o->unit                 
                             )
                          );
                      }             
                      /* setting column names for data - you can of course set it manually*/
                      $sheet->appendRow(array_keys($itemList[0])); /* column names*/

                      /* getting last row number (the one we already filled and setting it to bold*/
                      $sheet->row($sheet->getHighestRow(), function ($row) {
                        $row->setFontWeight('bold');
                    });
                      /* putting users data as next rows*/
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
            Log::error("UnitsController@exportToXls ".$e);
            return View("errors.exception");
        }
    }
    /** export to PDF Categories list */
    public function exportToPdf(){
        try{
            $data = Unit::all();
            if(sizeof($data)){
                $pdf = PDF::loadView('pages.masters.unit.unit_pdf', [
                    'data' => $data
                    ]); 

                $pdf->setPaper('a4');
                $carbon = Carbon::today();
                $timestamp = $carbon->timestamp;
                $format = $carbon->format('Y-m-d');
                $exportName = 'Unit'.$format.'.pdf';
                return $pdf->download($exportName);

            }else{
                toastr::warning('Warning', trans("validation.export_error"));
                return back();
            }
        }catch(Exception $e){
            Log::error("UnitsController@exportToPdf ".$e);
            return View("errors.exception");
        }
    }
}
