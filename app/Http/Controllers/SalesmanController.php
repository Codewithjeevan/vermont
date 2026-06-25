<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Salesman;
use Toastr;
use Excel;
use PDF;
use Carbon\Carbon;
use App\Billing;
use App\Purchase;

use Exception;
use Log;

class SalesmanController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        try{
            $salesmanobj = new Salesman;
            $salesman = Salesman::all();
            return View('pages.masters.salesman.salesman',[
                'salesman'=> $salesman,
                'desiginationType'  => $salesmanobj->desiginationType
                ]);

        }catch(Exception $e){
            Log::error("SalesmanController@index ".$e);
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
            'name' => 'required',
            ]);

        try{
            $task = Salesman::create($request->all());
            toastr::success('Success', trans("validation.success_store",[ "attribute" => trans('glob.salesman') ]));
            return redirect()->action('SalesmanController@index');
        }catch(Exception $e){
            Log::error("SalesmanController@store ".$e);
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
            $salesmanobj = new Salesman;
            $salesman = Salesman::all();
            $editSalesman= Salesman::where('id', $id)->first(); 
            return View('pages.masters.salesman.salesman',[
                'salesman'=> $salesman,
                'editSalesman'=> $editSalesman,
                'desiginationType'  => $salesmanobj->desiginationType
                ]);
        }catch(Exception $e){
            Log::error("SalesmanController@edit ".$e);
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
            'name' => 'required',
            ]);

        try{
            $salesmanData = Salesman::find($id);
            if(!is_null($salesmanData )){
                $input = $request->all();
                $salesmanData->fill($input)->save();
                toastr::success('Success', trans("validation.success_update",[ "attribute" => trans('glob.salesman') ]));
                return redirect()->action('SalesmanController@index');
            }else{
                toastr::warning('Warning', trans("validation.warning_update_deleted_item",["attribute" => trans('glob.salesman') ]));
                return redirect()->action('SalesmanController@index');
            }
        }catch(Exception $e){
            Log::error("SalesmanController@update ".$e);
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
            $isBill = Billing::where("salesman_id","=",$id)->first();
            $isPurchase = Purchase::where("receivedBy","=",$id)->first();
            if(sizeof($isBill) == 0 && sizeof($isPurchase) == 0){

                $salesman  = Salesman::find($id);
                (!is_null($salesman)) ?  ($r = $salesman->delete()) : ($r = null);

                if($r)
                {
                    $res = array('status' =>  "success", "data" =>array(), "message" => trans("validation.success_delete",["attribute" => trans('glob.salesman') ]));
                }
                else{
                    $res = array('status' =>  "error", "data" =>array(), "message" =>  trans("validation.error_delete",["attribute" => trans('glob.salesman') ]));
                }
            }else{
                if(sizeof($isBill) > 0)
                    $res = array('status' =>  "warning", "data" =>array(),  "message" =>' Salesman have Bill, Cant delete Salesman!' );
                else if(sizeof($isPurchase) > 0)
                    $res = array('status' =>  "warning", "data" =>array(),  "message" =>' Salesman have Purchase, Cant delete Salesman!' );
            }

            return response($res, 200);

        }catch(Exception $e){
            Log::error("SalesmanController@destroy ".$e);
            return View("errors.exception");
        }
    }

    /** export to xls Salesman list */
    public function exportToXls(){
        try{
            $data = Salesman::all();
            if(sizeof($data)){
                $carbon = Carbon::today();
                $timestamp = $carbon->timestamp;
                $format = $carbon->format('Y-m-d');
                $exportName = 'Salesman'.$format; 
                Excel::create($exportName, function ($excel) {

                    $excel->sheet('Salesman', function ($sheet) {

                        $dataList = Salesman::get();   
                        $itemList = array();
                        foreach ($dataList as $o) {              

                            array_push($itemList, array(
                                trans('glob.id')  => $o->id,
                                trans('glob.name')  => $o->name,
                                trans('glob.desigination')  => $o->desigination

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
            Log::error("SalesmanController@exportToXls ".$e);
            return View("errors.exception");
        }
    }
    /** export to PDF Salesman list */
    public function exportToPdf(){
        try{
            $data = Salesman::all();
            if(sizeof($data)){
                $pdf = PDF::loadView('pages.masters.salesman.salesman_pdf', [
                    'data' => $data
                    ]); 

                $pdf->setPaper('a4');
                $carbon = Carbon::today();
                $timestamp = $carbon->timestamp;
                $format = $carbon->format('Y-m-d');
                $exportName = 'Salesman'.$format.'.pdf'; 
                return $pdf->download($exportName);

            }else{
                toastr::warning('Warning', trans("validation.export_error"));
                return back();
            }
        }catch(Exception $e){
            Log::error("SalesmanController@exportToPdf ".$e);
            return View("errors.exception");
        }
    }
}
