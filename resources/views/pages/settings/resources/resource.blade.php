@extends('layouts.app')
@section('page-title') @lang('glob.resources') @endsection
@section('page-sub-title')  @endsection
@section ('breadcrumb')

<ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> @lang('glob.dashboard')</a></li>
    <li class="active">@lang('glob.resources')</li>
</ol>

@endsection
@section('content')
<div class="resource">

    @if(Common::can_create('resource'))

    <!-- Button div -->
    <div class="row">
        <div class="col-md-12 margin-bottom">
            <a @if(isset($eResource)) href="/common/add/Resource" @endif class="btn bg-purple new-customer" data-toggle="collapse" data-target="#resourceFormDiv" v-on:click="addForm">@lang('glob.add_new')</a>
        </div>
    </div>
    <!-- Button div end -->

    <!-- add/update customer form div -->
    <div id="resourceFormDiv" class="collapse @if (session('open') || isset($open) || isset($eResource) || !$errors->isEmpty())  in @endif">

        @if (isset($eResource)) 
        {{ Form::model($eResource, ['url'=>array('settings/resource',$eResource->id), 'method'=>'PUT','id'=>'resourceForm']) }}
        @else 
        {{ Form::open(['action'=>'ResourceController@store','id'=>'resourceForm']) }}
        @endif

        <div class="row customer-box">

            <div class="col-md-12">

                <div class="box box-primary">

                    <div class="box-body">

                        <div class="row">

                            <!-- Resource name -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('title', trans('glob.resource_name')) !!}<span style="color:red">*</span>
                                    <div class="input-group">
                                        <div class="input-group-addon"> <i class="fa fa-tags" aria-hidden="true"></i></div>
                                        {!! Form::text('res_name', null,  array('required',  'class'=>'form-control input-sm',  'placeholder'=>trans('glob.resource_name'))) !!}
                                    </div>
                                </div>
                            </div>
                            <!-- end Resource name -->

                        </div>
                        <!-- .row -->

                        <div class="row">

                            {{-- Parent Resource --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="parent-resource">Parent Resource</label>
                                    <select name="parent_resource" id="parent-resource" class="input-sm form-control select2">
                                        <option value="0">Parent Resource</option>
                                        @foreach($parentResoureces as $r)
                                        <option value="{{ $r->id }}" @if(isset($eResource) && $eResource->parent_resource == $r->id) selected  @endif> {{ $r->res_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>


                        <div class="row"> 

                            <!-- description -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('title', trans('glob.description')) !!}<span style="color:red">*</span>
                                    {!! Form::textarea('res_desc', null,  array('required', 'rows'=>'4',  'class'=>'form-control input-sm',  'placeholder'=> trans('glob.description'))) !!}
                                </div>
                            </div>
                            <!--End description -->

                        </div>
                        {{-- /.row --}}

                        <div class="row"> 

                            <!-- slug -->
                            <div class="col-md-6">
                                <div class="form-group {{ $errors->has('slug') ? ' has-error' : '' }}">
                                    {!! Form::label('title', trans('glob.slug')) !!}<span style="color:red">*</span>
                                    {!! Form::text('slug', null,  array('required',  'class'=>'form-control input-sm',  'placeholder'=> trans('glob.slug'))) !!}

                                    @if ($errors->has('slug'))

                                    <span class="help-block"> <strong>{{ $errors->first('slug') }}</strong></span>

                                    @endif
                                    
                                </div>

                            </div>
                            <!--End slug -->

                        </div>
                        {{-- /.row --}}


                    </div>
                    <!-- .box-body -->


                    <div class="box-footer">

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary "> <i class="fa fa-floppy-o"></i> @if (isset($eResource)) @lang('glob.update') @else @lang('glob.save') @endif</button>
                            <a  class="btn btn-cyan" v-on:click="resetForm()"><i class="fa fa-refresh"></i> @lang('glob.reset')</a>
                            <a @if (isset($eResource)) href="{{ url('settings/resource') }}" @endif 
                            class="btn btn-danger add-customer" data-toggle="collapse" data-target="#resourceFormDiv" v-on:click="cancelForm">@lang('glob.cancel')</a>
                        </div>

                    </div>
                    <!-- box-footer -->


                </div>
                <!-- .box .box-primary -->
            </div>
            <!-- .col-md-12 -->
        </div>
        <!--end .row .customer-box -->
        {{ Form::close() }}
    </div>


    @endif
    <!-- view table data  -->
    <div class="row">
        <div class="col-md-12 table-responsive">
            <table class="table table-bordered  datatable2">
                <thead>
                    <tr>
                        <th style="width:2%">#</th>
                        <th>@lang('glob.resource_name')</th>
                        <th>@lang('glob.created_date')</th>

                        @if(Common::can_update('resource') || Common::can_delete('resource'))

                        <th style="width:10%">@lang('glob.actions')</th>

                        @endif

                    </tr>
                </thead>
                <tbody>

                    <?php $i=0; ?>

                    @foreach($resources as $ro)
                    <?php ++$i; ?>
                       @foreach($ro["parent"] as $r)
                       
                        <tr data-toggle="collapse" class="bg-stylish" data-target=".child{{$i}}">
                            <td>{{$r->id}}</td>
                            <td> <span style="@if($r->parent_resource!=0) margin-left:40px; @endif">{{$r->res_name}}</span></td> 
                            <td>{{$r->created_at->format('jS F, Y H:i')}}</td>

                            @if(Common::can_update('resource') || Common::can_delete('resource'))

                            <td>

                                @if(Common::can_update('resource'))

                                <a href="{{route('resource.edit',$r->id)}}" class="btn btn-xs btn-info btn-circle" ><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                
                                @endif
                                @if(Common::can_delete('resource'))

                                <a id='{{ $r->id }}' v-on:click="deleteResource({{ $r->id }}, $event)" class="btn btn-xs btn-danger btn-circle "><i class="fa fa-trash" aria-hidden="true"></i></a> 
                                
                                @endif

                            </td>
                            @endif
                        </tr>
                        
                        @endforeach
                            
                                @foreach($ro["child"] as $row)
                                <tr class="collapse in child{{ $i }}">
                                    <td>{{$row->id}}</td>
                                    <td> <span style="@if($row->parent_resource!=0) margin-left:40px; @endif">{{$row->res_name}}</span></td> 
                                    <td>{{$row->created_at->format('jS F, Y H:i')}}</td>

                                    @if(Common::can_update('resource') || Common::can_delete('resource'))

                                    <td>

                                        @if(Common::can_update('resource'))

                                        <a href="{{route('resource.edit',$row->id)}}" v-on:click="getLoader($event)"  class="btn btn-xs btn-info btn-circle" ><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                        
                                        @endif
                                        @if(Common::can_delete('resource'))

                                        <a id='{{ $row->id }}' v-on:click="deleteResource({{ $row->id }}, $event)" class="btn btn-xs btn-danger btn-circle "><i class="fa fa-trash" aria-hidden="true"></i></a> 
                                        
                                        @endif

                                    </td>

                                    @endif

                                </tr>

                                @endforeach

                    @endforeach
                    
                </tbody>
                <tfoot class="showSearch">
                    <tr>
                        <th style="width:2%">#</th>
                        <th class="search">@lang('glob.resource_name')</th>
                        <th class="search">@lang('glob.created_date')</th>

                        @if(Common::can_update('resource') || Common::can_delete('resource'))

                        <th style="width:10%">@lang('glob.actions')</th>

                        @endif

                    </tr>
                </tfoot>

            </table>

        </div>
    </div>
    <!-- view table data end -->
</div>
@endsection
