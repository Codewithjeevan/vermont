<?php

namespace App\Http\Controllers;
use App\Category;
use App\BrandClassification;
use Toastr;
use PDF;
use Excel;
use Carbon\Carbon;
use Exception;
use Log;

use Illuminate\Http\Request;

class BrandClassificationController extends Controller
{
    public function index()
    {
        try{
           
            $parentCategories = Category::where("parent_category","=","0")->get();         
            $brandClassification=BrandClassification::with('parentCategory')->get();
            return View('pages.brand_classification.brand_classification',[
                'parentCategories' => $parentCategories,
                'brandClassification'=>$brandClassification
                ]);
        }catch(Exception $e){
            Log::error("BrandClassificationController@index ".$e);
            return View("errors.exception");
        }
    }

    public function store(Request $request){
        $this->validate($request, [
            'brand_id' => 'required|unique:brand_classifications'
            ],[
            'brand_id.unique' => "The Brand has already been taken.",
            'brand_id.required' => "The Brand has required field.",
            ]);

        try{
            $task = BrandClassification::create($request->all()); 
            toastr::success('Success', trans("validation.success_store",[ "attribute" => trans('glob.brand_classification') ]));
            return redirect()->action('BrandClassificationController@index');

        }catch(Exception $e){
            Log::error("BrandClassificationController@store ".$e);
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
            $brandclassification  = BrandClassification::find($id);
           
                (!is_null($brandclassification)) ?  ($r = $brandclassification->delete()) : ($r = null);
                if($r){
                    $res = array('status' =>  'success' , "data" =>array(), "message" => trans("validation.success_delete",["attribute" => trans('glob.brand_classification') ]));
                } else{
                    $res = array('status' =>  "error", "data" =>array(), "message" => trans("validation.error_delete",["attribute" => trans('glob.brand_classification') ]));
                }
                return response($res, 200);
          
        }catch(Exception $e){
            Log::error("CategoriesController@destroy ".$e);
            return View("errors.exception");
        }
    }

}
