<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Supplier;
use Carbon\Carbon;
use Toastr;
use PDF;
use Excel;
use App\PurchaseOrder;
use App\Purchase;
use App\PurchaseReturn;

use Exception;
use Log;

class SupplierController extends Controller 
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      try{
        $suppliers = Supplier::all();
        $newId = DB::select("SELECT auto_increment FROM INFORMATION_SCHEMA.TABLES WHERE table_name = 'customers' ");
        return View('pages.masters.suppliers.supplier',[
          'suppliers'=> $suppliers,
          'newId'    => $newId
          ]); 

      }catch(Exception $e){
        Log::error("SupplierController@index ".$e);
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
        'phone' => 'nullable|unique:suppliers|max:255',
        'mobile' => 'nullable|unique:suppliers|max:255',
        'email' => 'nullable|unique:suppliers|max:255',
        'name' => 'required',
        ],[
        'email.unique' => "The email has already been taken.",
        'name.required' => "The Company field is required.",
        ]);

      try{
        $task = Supplier::create($request->all());
        toastr::success('Success', trans("validation.success_store",[ "attribute" => trans('glob.supplier') ]));
        return redirect()->action('SupplierController@index');
      }catch(Exception $e){
        Log::error("SupplierController@store ".$e);
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
        $suppliers = Supplier::all();
        $editSupplier = Supplier::where('id', $id)->first(); 
        return View('pages.masters.suppliers.supplier',[
          'suppliers'=> $suppliers,
          'editSupplier'=> $editSupplier,
          ]);
      }catch(Exception $e){
        Log::error("SupplierController@edit ".$e);
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
        'phone' => 'nullable|unique:suppliers,phone,'.$id.'|max:255',
        'mobile' => 'nullable|unique:suppliers,mobile,'.$id.'|max:255',
        'email' => 'nullable|unique:suppliers,email,'.$id.'|max:255',
        'name' => 'required',
        ],[
        'email.unique' => "The email has already been taken.",
        'name.required' => "The Company field is required.",
        ]);


      try{
        $supplierData = Supplier::find($id);

        if(!is_null($supplierData)){

          $input = $request->all();
          $supplierData->fill($input)->save();
          toastr::success('Success', trans("validation.success_update",[ "attribute" => trans('glob.supplier') ]));
          return redirect()->action('SupplierController@index');

        }else{

          toastr::warning('Warning', trans("validation.warning_update_deleted_item",["attribute" => trans('glob.supplier') ]));
          return redirect()->action('SupplierController@index');
        }

      }catch(Exception $e){
        Log::error("SupplierController@update ".$e);
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
        $isPurchase = Purchase::where("supplier_id","=",$id)->first();
        $isPurchaseOrder = PurchaseOrder::where("supplier_id","=",$id)->first();
        $isPurchaseReturn = PurchaseReturn::where("supplier_id","=",$id)->first();
        if(sizeof($isPurchase) == 0 && sizeof($isPurchaseOrder) == 0 && sizeof($isPurchaseReturn) == 0){

          $supplier  = Supplier::find($id);
          (!is_null($supplier)) ?  ($r = $supplier->delete()) : ($r = null);

          if($r)
            $res = array('status' =>  "success", "data" =>array(), "message" => trans("validation.success_delete",["attribute" => trans('glob.supplier') ]));
          else
            $res = array('status' =>  "error", "data" =>array(), "message" =>  trans("validation.error_delete",["attribute" => trans('glob.supplier') ]));

        }else{
          if(sizeof($isPurchase) > 0)
            $res = array('status' =>  "warning", "data" =>array(),  "message" =>' Supplier have Purchase, Cant delete Supplier!' );
          else if(sizeof($isPurchaseOrder) > 0)
            $res = array('status' =>  "warning", "data" =>array(),  "message" =>'Supplier have Purchase Order, Cant delete Supplier!' );
          else if(sizeof($isPurchaseReturn) > 0)
            $res = array('status' =>  "warning", "data" =>array(),  "message" =>'Supplier have Purchase Return, Cant delete Supplier!' );
        }

        return response($res, 200);

      }catch(Exception $e){
        Log::error("SupplierController@destroy ".$e);
        return View("errors.exception");
      }
    }

    /** export to xls Supplier list */
    public function exportToXls(){
      try{
       $data = Supplier::all();
       if(sizeof($data)){
         $carbon = Carbon::today();
         $timestamp = $carbon->timestamp;
         $format = $carbon->format('Y-m-d');
         $exportName = 'Supplier'.$format; 
         \App\Legacy\LegacyExcel::create($exportName, function ($excel) {

          $excel->sheet('Supplier', function ($sheet) {

            $dataList = Supplier::get();

            $itemList = array();
            foreach ($dataList as $o) {              

              array_push($itemList, array(
               trans('glob.id')  => $o->id,
               trans('glob.name')  => $o->name,   
               trans('supplier.s_type') => $o->type,
               trans('glob.contact')  => $o->contact,
               trans('glob.address')  => $o->address,
               trans('glob.phone') => $o->phone,
               trans('glob.mobile')  => $o->mobile,
               trans('glob.fax')  => $o->fax,
               trans('glob.email')  => $o->email,
               trans('glob.city') => $o->city, 
               trans('supplier.ecc_no')  => $o->ecc,
               trans('supplier.cst_no')  => $o->cst,
               trans('supplier.range')  => $o->range,
               trans('supplier.division')  => $o->division,
               trans('supplier.comm')  => $o->comm,
               trans('supplier.pan_no')  => $o->pan,
               trans('supplier.tin_no')  => $o->tin               

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
      Log::error("SupplierController@exportToXls ".$e);
      return View("errors.exception");
    }
  }
  /** export to PDF Supplier list */
  public function exportToPdf(){
    try{
      $data = Supplier::all();
      if(sizeof($data)){
        $pdf = PDF::loadView('pages.masters.suppliers.suppliers_pdf', [
          'data' => $data
          ]); 

        $pdf->setPaper('a4');
        $carbon = Carbon::today();
        $timestamp = $carbon->timestamp;
        $format = $carbon->format('Y-m-d');
        $exportName = 'Supplier'.$format.'.pdf'; 
        return $pdf->download($exportName);

      }else{
        toastr::warning('Warning', trans("validation.export_error"));
        return back();
      }

    }catch(Exception $e){
      Log::error("SupplierController@exportToPdf ".$e);
      return View("errors.exception");
    }
  }
}
