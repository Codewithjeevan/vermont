@extends('layouts.app')
@section('page-title')@lang('glob.role_master') @endsection
@section('page-sub-title') Add New Role @endsection
@section ('breadcrumb')
<ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> @lang('glob.dashboard')</a></li>
    <li><a href="{{ url('/settings/role') }}"> @lang('glob.roles')</a></li>
    @if (isset($eRoles))
    <li class="active">@lang('glob.edit_roles')</li>
    @else
    <li class="active">@lang('glob.add_roles')</li>
    @endif
</ol>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        @if (isset($eRoles))
        {{ Form::model($eRoles, ['url'=>array('settings/role',$eRoles->id), 'method'=>'PUT','id'=>'roleForm']) }}
        @else
        {{ Form::open(['action'=>'RolesController@store','id'=>'roleForm']) }}
        @endif
        <div class="box box-primary">
            <div class="box-header">
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group {{ $errors->has('role') ? ' has-error' : '' }}">
                            {!! Form::label('title', trans('glob.role_name') ) !!}<span style="color:red">*</span>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-tags" aria-hidden="true"></i>
                                </div>
                                {!! Form::text('role', null, 
                                array('required', 
                                'class'=>'form-control input-sm', 
                                'placeholder'=>trans('glob.role_name'))) !!}
                            </div>
                            @if ($errors->has('role'))
                                        <span style="color:red">
                                         <strong>{{ $errors->first('role') }}</strong>
                                        </span>
                                        @endif
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('max_discount') ? ' has-error' : '' }}">
                            {!! Form::label('title', trans('glob.dis_per') ) !!}<span style="color:red">*</span>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-percent" aria-hidden="true"></i>
                                </div>
                                {!! Form::text('max_discount', null, 
                                array('required', 
                                'class'=>'form-control input-sm', 
                                'placeholder'=>trans('glob.dis_per'))) !!}
                            </div>
                            @if ($errors->has('max_discount'))
                                        <span style="color:red">
                                         <strong>{{ $errors->first('max_discount') }}</strong>
                                        </span>
                                        @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-primary">@if (isset($eRoles)) Update @else Submit @endif</button>
               
                <a  href="{{ url('/settings/role')}}"  class="btn btn-danger add-customer">@lang('glob.cancel')</a>
            </div>
        </div>
        {{ Form::close() }}

    </div>
</div>
@if(Common::has_access("privileges"))
@if(isset($roleCreated) &&  $roleCreated)
<h3>Update privileges of roles</h3>
<div class="row" id="privileges">
    <div class="col-md-12 table-responsive">
        {{ Form::open(['url'=>array('settings/privileges', $eRoles), 'method'=>'PUT']) }}
        <input type="hidden" name="role" value="{{ $eRoles->id }}">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th> @lang('glob.resources')</th>
                    <th><input type="checkbox" class="headViewCheck" v-on:click="viewCheckAll"> @lang('glob.view')</th>
                    <th><input type="checkbox" class="headCreateCheck" v-on:click="createCheckAll"> @lang('glob.create')</th>
                    <th><input type="checkbox" class="headUpdateCheck" v-on:click="updateCheckAll"> @lang('glob.update')</th>
                    <th><input type="checkbox" class="headDeleteCheck" v-on:click="deleteCheckAll"> @lang('glob.delete')</th>
                </tr>
            </thead>
            <tbody>
                <?php $i=0; ?>
                @foreach($resources as $ro)
                <?php ++$i; ?>
                @foreach($ro["parent"] as $r)
                <tr class="bg-stylish">
                    <td data-toggle="collapse" data-target=".child{{ $i }}" style="cursor: pointer;"> <i class="ion-ios-plus-outline"></i> {{  $r->res_name }}  </td>
                    <td> <input type="checkbox" class="viewcheck parent-view parent-view-{{ $i }}" data-id="{{ $i }}" name="viewchk[{{ $r->id }}]"    @if(!empty($pbyr[$r->id][0]) && $pbyr[$r->id][0]["view"]==1) {{ 'checked' }}  @endif> </td>
                    <td> <input type="checkbox" class="createcheck parent-create parent-create-{{ $i }}" data-id="{{ $i }}" name="createchk[{{ $r->id }}]"  @if(!empty($pbyr[$r->id][0]) && $pbyr[$r->id][0]["create"]==1) {{ 'checked' }}  @endif> </td>
                    <td> <input type="checkbox" class="updatecheck parent-update parent-update-{{ $i }}" data-id="{{ $i }}" name="updatechk[{{ $r->id }}]"  @if(!empty($pbyr[$r->id][0]) && $pbyr[$r->id][0]["update"]==1) {{ 'checked' }}  @endif> </td>
                    <td> <input type="checkbox" class="deletecheck parent-delete parent-delete-{{ $i }}" data-id="{{ $i }}" name="deletechk[{{ $r->id }}]"  @if(!empty($pbyr[$r->id][0]) && $pbyr[$r->id][0]["delete"]==1) {{ 'checked' }}  @endif> </td>
                </tr>
                @endforeach

                @foreach($ro["child"] as $r)
                <tr class="collapse  child{{ $i }}">
                    <td> <span style="margin-left:40px;">{{  $r->res_name }}</span></td>
                    @if($r->slug=='billing' || $r->slug=='billing2')
                    <td> 
                        <label id="all" class="radio-inline"><input type="radio" class="viewcheck child-view-{{ $i }}" data-id="{{ $i }}" name="viewchk[{{ $r->id }}]"  value="2"  @if(!empty($pbyr[$r->id][0]) && $pbyr[$r->id][0]["view"]==2) {{ 'checked' }}  @endif> All Bill</label> 
                        <label id="one" class="radio-inline"><input type="radio" class="viewcheck child-view-{{ $i }}" data-id="{{ $i }}" name="viewchk[{{ $r->id }}]"  value="1"   @if(!empty($pbyr[$r->id][0]) && $pbyr[$r->id][0]["view"]==1) {{ 'checked' }}  @endif> individual Bill</label> 
                    </td>
                    @else
                    <td> <input type="checkbox" class="viewcheck child-view-{{ $i }}" data-id="{{ $i }}" name="viewchk[{{ $r->id }}]"    @if(!empty($pbyr[$r->id][0]) && $pbyr[$r->id][0]["view"]==1) {{ 'checked' }}  @endif> </td>
                    @endif
                    <td> <input type="checkbox" class="createcheck child-create-{{ $i }}" data-id="{{ $i }}"  name="createchk[{{ $r->id }}]"  @if(!empty($pbyr[$r->id][0]) && $pbyr[$r->id][0]["create"]==1) {{ 'checked' }}  @endif> </td> 
                    <td> <input type="checkbox" class="updatecheck child-update-{{ $i }}" data-id="{{ $i }}" name="updatechk[{{ $r->id }}]"  @if(!empty($pbyr[$r->id][0]) && $pbyr[$r->id][0]["update"]==1) {{ 'checked' }}  @endif> </td>
                    <td> <input type="checkbox" class="deletecheck child-delete-{{ $i }}" data-id="{{ $i }}" name="deletechk[{{ $r->id }}]"  @if(!empty($pbyr[$r->id][0]) && $pbyr[$r->id][0]["delete"]==1) {{ 'checked' }}  @endif> </td>
                </tr>
                @endforeach
                @endforeach
            </tbody>
        </table>
        @if(Common::can_update("privileges"))
        <button  type="submit" class="btn btn-primary">Update Privileges</button>
        @endif
        {{ Form::close() }}
    </div>
</div>
@endif
@endif
@endsection
