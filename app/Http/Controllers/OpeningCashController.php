<?php

namespace App\Http\Controllers; 

use Illuminate\Http\Request;
use App\User;
use App\OpeningCash;
use Toastr;
use Illuminate\Support\Facades\Auth;
use Common;

use Exception;
use Log;


class OpeningCashController extends Controller
{


    private $rDateFormat;


    public function __construct(){
        $this->rDateFormat = Common::rDateFormat();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {        

        try{     
            if (Auth::check())
            {
                $rollId = \Auth::user()->roleId;

            }

            if($rollId == 1){
                $users = User::all(); 
                $openCashList = OpeningCash::with("user")->get();
            }else{
                $users = User::with("role")->where('roleId', '<>', 1)->get();
                $openCashList = OpeningCash::with("user")
                ->join('users', 'opening_cashes.user_id', '=', 'users.id')
                ->select('opening_cashes.*')
                ->where('users.roleId', '<>', 1)->get();
            }

            return View('pages.opening_cash.opening_cash',[ 
                'openCashList' => $openCashList,           
                "users" => $users ,
                'date_format'   => $this->rDateFormat,       
                ]);

        }catch(Exception $e){
            Log::error("OpeningCashController@index ".$e);
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
        "open_cash_date.required" => 'Please fill out this field.',
        "opening_amt.required" => 'Please fill out this field.',
        ];

        $this->validate($request, [
            'open_cash_date' => 'required',
            "opening_amt"   => 'required'
            ],$messages);


        try{
            $openingCash = OpeningCash::create($request->all());
            toastr::success('Success', trans("validation.success_store",[ "attribute" => trans('menu.opening_cash') ]));
            return redirect()->action('OpeningCashController@index');

        }catch(Exception $e){
            Log::error("OpeningCashController@store ".$e);
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

            if (Auth::check())
            {
                $rollId = \Auth::user()->roleId;

            }

            if($rollId == 1){
                $users = User::all(); 
                $openCashList = OpeningCash::with("user")->get();
            }else{
                $users = User::with("role")->where('roleId', '<>', 1)->get();
                $openCashList = OpeningCash::with("user")
                ->join('users', 'opening_cashes.user_id', '=', 'users.id')
                ->select('opening_cashes.*')
                ->where('users.roleId', '<>', 1)->get();
            }


            $editOpenCash= OpeningCash::find($id); 
            return View('pages.opening_cash.opening_cash',[
               'editOpenCash'=> $editOpenCash,
               'openCashList' => $openCashList,
               "users" => $users ,
               'date_format'   => $this->rDateFormat,          
               ]);

        }catch(Exception $e){
            Log::error("OpeningCashController@edit ".$e);
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

        $messages = [
        "open_cash_date.required" => 'Opening cash date is required',
        "opening_amt.required" => 'Please fill out this field.',
        ];

        $this->validate($request, [
            'open_cash_date' => 'required',
            "opening_amt"   => 'required'
            ],$messages);

        
        try{
            $editOpenCash = OpeningCash::find($id);

            if(!is_null($editOpenCash)){

                $editOpenCash->fill($request->all())->save();
                toastr::success('Success', trans("validation.success_update",[ "attribute" => trans('menu.opening_cash') ]));
                return redirect()->action('OpeningCashController@index');


            }else{

                toastr::warning('Warning', trans("validation.warning_update_deleted_item",["attribute" => trans('menu.opening_cash') ]));
                return redirect()->action('OpeningCashController@index');

            }

        }catch(Exception $e){
            Log::error("OpeningCashController@edit ".$e);
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
            $openingCash  = OpeningCash::find($id);
            
            (!is_null($openingCash)) ?  ($r = $openingCash->delete()) : ($r = null);
            
            if($r){
                $res = array('status' =>  "success", "data" =>array(), "message" => trans("validation.success_delete",["attribute" => trans('menu.opening_cash') ]));
            }
            else{
                $res = array('status' =>  "error", "data" =>array(), "message" => trans("validation.error_delete",["attribute" => trans('menu.opening_cash') ]));
            }

            return response($res, 200);

        }catch(Exception $e){
            Log::error("OpeningCashController@destroy ".$e);
            return View("errors.exception");
        }
    }



}
