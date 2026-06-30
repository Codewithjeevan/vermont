<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bank;
use \Toastr;
use PDF;
use Excel;
use Carbon\Carbon;
use App\CustomerReceipt;
use App\Payment;

use Exception;
use Log;


class BanksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $bank = Bank::all();
            return View("pages.masters.banks.bank",["bank" => $bank]);

        }catch(Exception $e){
            Log::error("BanksController@index ".$e);
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
        $messages = [
        "name.unique" => 'The bank name is alredy present',
        ];

        $this->validate($request, [
            'name' => 'required|unique:banks|max:255'
            ],$messages);

        try{
            $bank = Bank::create($request->all());
            toastr::success('Success', trans("validation.success_store",[ "attribute" => trans('glob.bank') ]));
            return redirect()->action('BanksController@index');
        }catch(Exception $e){
            Log::error("BanksController@store ".$e);
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
            $bank = Bank::all();
            $editBank= Bank::where('id', $id)->first(); 
            return View('pages.masters.banks.bank',[ 'bank'=> $bank, 'editBank'=> $editBank]);
        }catch(Exception $e){
            Log::error("BanksController@edit ".$e);
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
            'name' => 'required|unique:banks,name,'.$id.'|max:255'
            ]);
        
        try{
            $bank = Bank::find($id);
            if(!is_null($bank)){
                $bank->fill($request->all())->save();
                toastr::success('Success', trans("validation.success_update",[ "attribute" => trans('glob.bank') ]));
                return redirect()->action('BanksController@index');
            }else{
                toastr::warning('Warning', trans("validation.warning_update_deleted_item",["attribute" => trans('glob.bank') ]));
                return redirect()->action('BanksController@index');
            }   

        }catch(Exception $e){
            Log::error("BanksController@update ".$e);
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
            $isCustomerReceipt = CustomerReceipt::where("bank_id","=",$id)->first();
            $isPayment = Payment::where("bank_id","=",$id)->first();
            if(sizeof($isCustomerReceipt) == 0 && sizeof($isPayment) == 0){

                $bank  = Bank::find($id);

                (!is_null($bank)) ?  ($r = $bank->delete()) : ($r = null);

                if($r){
                    $res = array('status' =>  "success", "data" =>array(), "message" =>trans("validation.success_delete",["attribute" => trans('glob.bank') ]));
                }
                else{
                    $res = array('status' =>  "error", "data" =>array(), "message" => trans("validation.error_delete",["attribute" => trans('glob.bank') ]));
                }

            }else{            
                $res = array('status' =>  "warning", "data" =>array(),  "message" =>'Bank have payment exists, Cant delete Bank!' );
            }

            return response($res, 200);

        }catch(Exception $e){
            Log::error("BanksController@destroy ".$e);
            return View("errors.exception");
        }
    }

    /** export to xls Bank list */
    public function exportToXls(){
        try{
            $data = Bank::all();
            if(sizeof($data)){ 
               $carbon = Carbon::today();
               $timestamp = $carbon->timestamp;
               $format = $carbon->format('Y-m-d');
               $exportName = 'Bank'.$format; 

               \App\Legacy\LegacyExcel::create($exportName, function ($excel) {

                $excel->sheet('Bank', function ($sheet) {

                    $dataList = Bank::get(); 
                    $itemList = array();
                    foreach ($dataList as $o) {              

                        array_push($itemList, array(
                            trans('glob.id')  => $o->id,
                            trans('glob.bank_name')  => $o->name                                  
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
        Log::error("BanksController@exportToXls ".$e);
        return View("errors.exception");
    }
}
/** export to PDF Customer list */
public function exportToPdf(){
    try{
        $data = Bank::all();
        if(sizeof($data)){
            $pdf = PDF::loadView('pages.masters.banks.bank_pdf', [
                'data' => $data
                ]); 

            $pdf->setPaper('a4');
            $carbon = Carbon::today();
            $timestamp = $carbon->timestamp;
            $format = $carbon->format('Y-m-d');
            $exportName = 'Bank'.$format.'.pdf';

            return $pdf->download($exportName);
        }else{
            toastr::warning('Warning', trans("validation.export_error"));
            return back();
        }

    }catch(Exception $e){
        Log::error("BanksController@exportToXls ".$e);
        return View("errors.exception");
    }
}
}
