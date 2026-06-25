<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
use App\Item;
use Toastr;
use PDF;
use Excel;
use Carbon\Carbon;

use Exception;
use Log;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $category = Category::all();
            $parentCategories = Category::where("parent_category","=","0")->get();         
            return View('pages.masters.category.category',[
                'category'=> $category,
                'parentCategories' => $parentCategories
                ]);
        }catch(Exception $e){
            Log::error("CategoriesController@index ".$e);
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
            'categorie' => 'required|unique:categories|max:255'
            ],[
            'categorie.unique' => "The category has already been taken.",
            'categorie.required' => "The category has required field.",
            ]);

        try{
            $task = Category::create($request->all()); 
            toastr::success('Success', trans("validation.success_store",[ "attribute" => trans('glob.category') ]));
            return redirect()->action('CategoriesController@index');

        }catch(Exception $e){
            Log::error("CategoriesController@store ".$e);
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
            $category = Category::all();
            $parentCategories = Category::where("parent_category","=","0")->get();
            $editCategory= Category::where('id', $id)->first(); 
            return View('pages.masters.category.category',[
                'category'=> $category,
                'editCategory'=> $editCategory,
                'parentCategories' => $parentCategories
                ]);
        }catch(Exception $e){
            Log::error("CategoriesController@edit ".$e);
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
            'categorie' => 'required|unique:categories,categorie,'.$id.'|max:255'
            ],[
            'categorie.unique' => "The category has already been taken.",
            'categorie.required' => "The category has required field.",
            ]);
        

        try{
            $categoryData = Category::find($id);
            if(!is_null($categoryData)){
                $input = $request->all();
                $categoryData->fill($input)->save();
                toastr::success('Success', trans("validation.success_update",[ "attribute" => trans('glob.category') ]));
                return redirect()->action('CategoriesController@index');
            }else{
                toastr::warning('Warning', trans("validation.warning_update_deleted_item",["attribute" => trans('glob.category') ]));
                return redirect()->action('CategoriesController@index');
            }
        }catch(Exception $e){
            Log::error("CategoriesController@update ".$e);
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
            $category  = Category::find($id);
            $productCode = Category::where("parent_category","=",$id)->first(); 
            $itemCheck = Item::where("category","=",$id)->first();
            if(is_null($itemCheck) && is_null($productCode)){
                (!is_null($category)) ?  ($r = $category->delete()) : ($r = null);
                if($r){
                    $res = array('status' =>  'success' , "data" =>array(), "message" => trans("validation.success_delete",["attribute" => trans('glob.category') ]));
                } else{
                    $res = array('status' =>  "error", "data" =>array(), "message" => trans("validation.error_delete",["attribute" => trans('glob.category') ]));
                }
                return response($res, 200);
            }else{
                return response( $res = array('status' =>  'warning' , "data" =>array(), "message" =>''.$category["categorie"].' Category have childs, Cant delete parent category' ), 200);
            }
        }catch(Exception $e){
            Log::error("CategoriesController@destroy ".$e);
            return View("errors.exception");
        }
    }

    /** export to xls Categories list */
    public function exportToXls(){
        try{
            $data = Category::all();    
            if(sizeof($data)){ 
              $carbon = Carbon::today();
              $timestamp = $carbon->timestamp;
              $format = $carbon->format('Y-m-d');
              $exportName = 'Category'.$format; 

              Excel::create($exportName, function ($excel) {

                $excel->sheet('Category', function ($sheet) {

                    $dataList = Category::get();  
                    $parentCategories = Category::where("parent_category","=","0")->get(); 

                    $itemList = array();
                    foreach ($dataList as $o) {
                        $parentcat = '';
                        if($o->parent_category === 0){
                            $parentcat = 'Parent Category';
                        } else{
                            $parentcat = $o->parentCategory->categorie; 
                        }

                        array_push($itemList, array(
                            trans('glob.id')  => $o->id,
                            trans('glob.category')  => $o->categorie,
                            trans('glob.parentCategory') => $parentcat                                    
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
        Log::error("CategoriesController@exportToXls ".$e);
        return View("errors.exception");
    }
}
/** export to PDF Categories list */
public function exportToPdf(){
    try{
        $data = Category::all();    
        if(sizeof($data)){
            $parentCategories = Category::where("parent_category","=","0")->get();
            $pdf = PDF::loadView('pages.masters.category.category_pdf', [
                'data' => $data,
                'parentCategories' => $parentCategories
                ]); 
            $pdf->setPaper('a4');
            $carbon = Carbon::today();
            $timestamp = $carbon->timestamp;
            $format = $carbon->format('Y-m-d');
            $exportName = 'Category'.$format.'.pdf';
            return $pdf->download($exportName);
        }else{
            toastr::warning('Warning', trans("validation.export_error"));
            return back();
        }
    }catch(Exception $e){
        Log::error("CategoriesController@exportToPdf ".$e);
        return View("errors.exception");
    }
}

}
