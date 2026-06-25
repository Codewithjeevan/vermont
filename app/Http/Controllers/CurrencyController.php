<?php

namespace App\Http\Controllers;

use App\Currency;
use Illuminate\Http\Request;
use \Toastr;
use PDF;
use Excel;
use Carbon\Carbon;
use App\Billing;
class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $arrCurrency = Currency::all();
        return View("pages.masters.currency.currency",[
            "arrCurrency"=>$arrCurrency
        ]);

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
            "currency.unique" => 'The Currency is alredy present',
            "code.unique" => 'The Currency code is alredy present',

        ];

        $this->validate($request, [
            'currency' => 'required|unique:currencies|max:255',
            'code' => 'required|unique:currencies|max:255',
            'amount' => 'required|max:255',
        ]);


        $objCurrency = Currency::create($request->all());
        toastr::success('Success', trans("validation.success_store",[ "attribute" => trans('glob.currency') ]));
        return redirect()->action('CurrencyController@index');

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Currency  $currency
     * @return \Illuminate\Http\Response
     */
    public function edit(Currency $currency)
    {

     $arrCurrency = Currency::all();
     $editCurrency= Currency::where('id', $currency->id)->first(); 
     return View('pages.masters.currency.currency',[ 'arrCurrency'=> $arrCurrency, 'editCurrency'=> $editCurrency]);
 }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Currency  $currency
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Currency $currency)
    {


        $messages = [
            "currency.unique" => 'The Currency is alredy present',
            "code.unique" => 'The Currency code is alredy present',

        ];

        $this->validate($request, [
            'currency' => 'required|unique:currencies,currency,'.$currency->id.'|max:255',
            'code' => 'required|unique:currencies,code,'.$currency->id.'|max:255',
            'amount' => 'required|max:255',
        ]);

        $objCurrency = Currency::find($currency->id);
        if(!is_null($objCurrency)){
            $objCurrency->fill($request->all())->save();
            toastr::success('Success', trans("validation.success_update",[ "attribute" => trans('glob.currency') ]));
            return redirect()->action('CurrencyController@index');
        }else{
            toastr::warning('Warning', trans("validation.warning_update_deleted_item",["attribute" => trans('glob.currency') ]));
            return redirect()->action('CurrencyController@index');
        }   

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Currency  $currency
     * @return \Illuminate\Http\Response
     */
    public function destroy(Currency $currency)
    {
       $isBilling = Billing::where("currency","=",$currency->id)->first();
        $objCurrency  = Currency::find($currency->id);
         if(sizeof($isBilling) == 0 )
        {
             (!is_null($objCurrency)) ?  ($r = $objCurrency->delete()) : ($r = null);
        if($r){
            $res = array('status' =>  "success", "data" =>array(), "message" =>trans("validation.success_delete",["attribute" => trans('glob.currency') ]));
        }
        else{
            $res = array('status' =>  "error", "data" =>array(), "message" => trans("validation.error_delete",["attribute" => trans('glob.currency') ]));
        }
      
        }
        else{
           $res = array('status' =>  "warning", "data" =>array(),  "message" =>'Billing Exists, Cant delete Currency!');

        }
          return response($res, 200);
    }


    public function exportToXls(){

        $arrCurrency = Currency::all();
        if(sizeof($arrCurrency)){ 
           $carbon = Carbon::today();
           $timestamp = $carbon->timestamp;
           $format = $carbon->format('Y-m-d');
           $exportName = 'currency'.$format; 

           Excel::create($exportName, function ($excel) use ($arrCurrency) {

            $excel->sheet('currency', function ($sheet) use ($arrCurrency) {

           
                $itemList = array();
                foreach ($arrCurrency as $o) {              

                    array_push($itemList, array(
                        trans('glob.id')  => $o->id,
                        trans('glob.currency')  => $o->currency ,
                        trans('glob.code')  => $o->code,
                        trans('glob.amount')  => $o->amount  ,                                
                    )
                );
                }             


                $sheet->appendRow(array_keys($itemList[0])); 


                $sheet->row($sheet->getHighestRow(), function ($row) {
                    $row->setFontWeight('bold');
                });


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
}

public function exportToPdf(){

    $arrCurrency = Currency::all();
    if(sizeof($arrCurrency)){
        $pdf = PDF::loadView('pages.masters.currency.currency_pdf', [
            'arrCurrency' => $arrCurrency
        ]); 

        $pdf->setPaper('a4');
        $carbon = Carbon::today();
        $timestamp = $carbon->timestamp;
        $format = $carbon->format('Y-m-d');
        $exportName = 'Currency'.$format.'.pdf';

        return $pdf->download($exportName);
    }else{
        toastr::warning('Warning', trans("validation.export_error"));
        return back();
    }

}
}
