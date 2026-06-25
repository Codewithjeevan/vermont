<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Role;
use App\Resource;
use App\Privilege;
use Common;
use Toastr;
use App\User;
use Illuminate\Support\Facades\Auth;

use Exception;
use Log;

class RolesController extends Controller
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
				$roles = Role::all();
			}else{
				$roles = Role::where('id','<>',1)->get();
			}
			return View('pages.settings.roles.view',["roles"=> $roles]);

		}catch(Exception $e){
			Log::error("RolesController@index ".$e);
			return View("errors.exception");
		}

	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		try{
			return View('pages.settings.roles.create');
		}catch(Exception $e){
			Log::error("RolesController@create ".$e);
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
			'max_discount' => 'numeric|between:0,100',
			'role' => 'required|unique:roles',
			]);
		try{
			$roles = $request->all();
			$role = Role::create($roles);
			$insertedId = $role->id;
			$resources = \App\Resource::all();
			$roleCreated = true;
			toastr::success('Success', trans("validation.success_store",[ "attribute" => trans('glob.role') ]));
			return redirect('/settings/role/'.$insertedId.'/edit');
		}catch(Exception $e){
			Log::error("RolesController@store ".$e);
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
			$eRole = Role::findOrFail($id);
			$privilegesByResources = $eRole->privileges->groupBy('resourceId')->toArray();
			$parentResource = Resource::where("parent_resource","=", "0")->get();

			foreach ($parentResource as $r) {
				$resources[] = array(
					'parent' => array($r),
					'child' => Resource::where("parent_resource","=", $r->id)->get()
					);
			}
			$roleCreated = true;
			return View('pages.settings.roles.create', [
				'eRoles'=> $eRole, 
				"resources" => $resources, 
				"roleCreated" => $roleCreated, 
				"pbyr"=> $privilegesByResources
				]);

		}catch(Exception $e){
			Log::error("RolesController@edit ".$e);
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
		$role = Role::findOrFail($id);
		
		$this->validate($request, [
			'max_discount' => 'numeric|between:0,100',
			'role' => 'required|unique:roles,role,'.$id.'',
			]);

		try{
			if(!is_null($role)){

				$input = $request->all();
				$role->fill($input)->save();
				toastr::success('Success', trans("validation.success_update",[ "attribute" => trans('glob.role') ]));
				return redirect()->action('RolesController@index');

			}else{
				toastr::warning('Warning', trans("validation.warning_update_deleted_item",["attribute" => trans('glob.role') ]));
				return redirect()->action('RolesController@index');
			}

		}catch(Exception $e){
			Log::error("RolesController@update ".$e);
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
			$role  = Role::find($id);
			$isRole = User::where("roleId","=",$id)->first();
			if(sizeof($isRole) == 0){
				(!is_null($role)) ?  ($r = $role->delete()) : ($r = null);

				if($r){
					$res = array('status' =>  "success", "data" =>array(), "message" => trans("validation.success_delete",["attribute" => trans('glob.role') ]));
				}
				else{
					$res = array('status' =>  "error", "data" =>array(), "message" => trans("validation.error_delete",["attribute" => trans('glob.role') ]));
				}
			}
			else{
				if(sizeof($isRole) > 0){
					$res = array('status' =>  "warning", "data" =>array(),  "message" =>'Role have User, Cant delete Role!' );
				}

			}

			return response($res, 200);

		}catch(Exception $e){
			Log::error("RolesController@destroy ".$e);
			return View("errors.exception");
		}
	}

	public function updatePrivileges(Request $request, $id)
	{
		try{
			$resources = Resource::all();
			$inputs = $request->all();
			if(isset( $inputs["viewchk"]))
				$viewchk =  $inputs["viewchk"];
			if(isset( $inputs["createchk"]))
				$createchk = $inputs["createchk"];
			if(isset( $inputs["updatechk"]))
				$updatechk = $inputs["updatechk"];
			if(isset( $inputs["deletechk"]))
				$deletechk = $inputs["deletechk"];

			$roleId = $inputs["role"];
			$role = Privilege::where("roleId","=",$roleId);
			$role->delete();
			foreach ($resources as $r) {
				$view = 0; $create = 0; $update = 0; $delete = 0; 
				if(isset($viewchk[$r->id])){
					if(($r->slug=='billing' && $viewchk[$r->id]==2) || ($r->slug=='billing2' && $viewchk[$r->id]==2)){
						$view = 2;
					}else{
						$view = 1;
					}
				}
				if(isset($createchk[$r->id])){
					$create = 1;
				}
				if(isset($updatechk[$r->id])){
					$update = 1;
				}
				if(isset($deletechk[$r->id])){
					$delete = 1;
				}
				

				$p = new Privilege();
				$p->resourceId = $r->id;
				$p->roleId = $roleId;
				$p->view = $view;
				$p->create = $create;
				$p->update = $update;
				$p->delete = $delete;

				$p->save();
			}
			toastr::success('Success', trans("validation.success_update",[ "attribute" => trans('glob.privileges') ]));
			return back();

		}catch(Exception $e){
			Log::error("RolesController@updatePrivileges ".$e);
			return View("errors.exception");
		}
	}

}
