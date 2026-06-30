<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Role;
use App\User;
use Toastr;
use Excel;
use PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\DamageStock;
use App\Billing;

use Exception;
use Log;

class UsersController extends Controller
{
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
                $users = User::with("role")->get();
                $roleList = Role::all();
            }else{
                $users = User::with("role")->where('roleId', '<>', 1)->get();
                $roleList = Role::where('id', '<>', 1)->get();
            }
            return View('pages.masters.users.user',[
                'users' => $users,
                'roleList'  => $roleList,
                'floor' => User::$floor
                ]);
        }catch(Exception $e){
            Log::error("UsersController@index ".$e);
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
            'email' => 'required|unique:users|max:255',
            'password' => 'required|min:1|confirmed',
            ]);
        try{
            User::create($request->all());
            toastr::success('Success', trans("validation.success_store",[ "attribute" => trans('glob.user') ]));
            return back();
        }catch(Exception $e){
            Log::error("UsersController@store ".$e);
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
                $users = User::with("role")->get();
                $roleList = Role::all();
            }else{
                $users = User::with("role")->where('roleId', '<>', 1)->get();
                $roleList = Role::where('id', '<>', 1)->get();
            }
            $editUser= User::find($id);
            $floor = User::$floor;
            return View('pages.masters.users.user',[
                'editUser' => $editUser,
                'roleList'  => $roleList,
                'users' => $users,
                'floor' => $floor
                ]);
        }catch(Exception $e){
            Log::error("UsersController@edit ".$e);
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
            'email' => 'required|unique:users,email,'.$id.'|max:255',
            'password' => 'required|min:1|confirmed',
            ]);

        try{
            $user = User::find($id);

            if(!is_null($user)){
                $input = $request->all();
                $user->fill($input)->save();
                toastr::success('Success', trans("validation.success_update",[ "attribute" => trans('glob.user') ]));
                return redirect()->action('UsersController@index');
            }else{
                toastr::warning('Warning', trans("validation.warning_update_deleted_item",["attribute" => trans('glob.user') ]));
                return redirect()->action('UsersController@index');
            }
        }catch(Exception $e){
            Log::error("UsersController@update ".$e);
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
            $isBill = Billing::where("user_id","=",$id)->first();
            $isDamageExpiry = DamageStock::where("enterd_by","=",$id)->first();
            if(sizeof($isBill) == 0 && sizeof($isDamageExpiry) == 0){
                $user  = User::find($id);
                (!is_null($user)) ?  ($r = $user->delete()) : ($r = null);
                if($r){
                    $res = array('status' =>  "success", "data" =>array(), "message" => trans("validation.success_delete",["attribute" => trans('glob.user') ]));
                }
                else{
                    $res = array('status' =>  "error", "data" =>array(), "message" => trans("validation.error_delete",["attribute" => trans('glob.user') ]));
                }
            }else{
                if(sizeof($isBill) > 0)
                    $res = array('status' =>  "warning", "data" =>array(),  "message" =>' User have Bill, Cant delete User!' );
                else if(sizeof($isDamageExpiry) > 0)
                    $res = array('status' =>  "warning", "data" =>array(),  "message" =>'User have Damage or Expiry Stock Entry, Cant delete User!' );
            }
            return response($res, 200);
        }catch(Exception $e){
            Log::error("UsersController@destroy ".$e);
            return View("errors.exception");
        }
    }

    /** export to xls User list */
    public function exportToXls(){
        try{
           $data = User::all();
           if(sizeof($data)){
            $carbon = Carbon::today();
            $timestamp = $carbon->timestamp;
            $format = $carbon->format('Y-m-d');
            $exportName = 'User'.$format;
            \App\Legacy\LegacyExcel::create($exportName, function ($excel) {

                $excel->sheet('User', function ($sheet) {

                    if (Auth::check())
                    {
                        $rollId = \Auth::user()->roleId;
                    }

                    if($rollId == 1){
                        $dataList = User::get();  
                    }else{
                        $dataList = User::where('roleId', '<>', 1)->get();
                    }

                    $itemList = array();
                    foreach ($dataList as $o) {              

                      array_push($itemList, array(
                       trans('glob.id')  => $o->id,
                       trans('user.user_name')  => $o->email, 
                       trans('user.assign_floor')  => $o->floor                

                       )
                      );
                  }             


                  /* setting column names for data - you can of course set it manually */
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
        Log::error("UsersController@exportToXls ".$e);
        return View("errors.exception");
    }
}
/** export to PDF User list */
public function exportToPdf(){
    try{
        if (Auth::check())
        {
            $rollId = \Auth::user()->roleId;
        }

        if($rollId == 1){
            $data = User::all();
        }else{
            $data = User::where('roleId', '<>', 1)->get();
        }


        if(sizeof($data)){
            $pdf = PDF::loadView('pages.masters.users.user_pdf', [
                'data' => $data
                ]); 

            $pdf->setPaper('a4');
            $carbon = Carbon::today();
            $timestamp = $carbon->timestamp;
            $format = $carbon->format('Y-m-d');
            $exportName = 'User'.$format.'.pdf';
            return $pdf->download($exportName);

        }else{
            toastr::warning('Warning', trans("validation.export_error"));
            return back();
        }
    }catch(Exception $e){
        Log::error("UsersController@exportToPdf ".$e);
        return View("errors.exception");
    }
}
}
