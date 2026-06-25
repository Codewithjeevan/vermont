@extends('layouts.app')
@section('page-title') Roles @endsection
@section('page-sub-title')  @endsection
@section ('breadcrumb')
<ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> @lang('glob.dashboard')</a></li>
    <li class="active"> @lang('glob.roles')</li>
</ol>
@endsection

@section('content')
<div class="role">

    @if(Common::can_create('role'))

    <!-- Add button stare -->
    <div class="row">
        <div class="col-md-12 margin-bottom">
            <a href="{{route('role.create')}}" class="btn bg-purple btn-float bg-orange-n pull-left" >@lang('glob.add_new')
            </a>
        </div>
    </div>
    <!-- End add button -->

    @endif

    <!-- view table data  -->
    <div class="row">
        <div class="col-md-12 table-responsive">
            <table class="table table-bordered table-striped datatable1">
                <thead>
                    <tr>
                        <th> #</th>
                        <th>@lang('glob.roles')</th>
                        <th>@lang('glob.dis_per')</th>
                        <th>@lang('glob.created_date')</th>

                        @if(Common::can_update('role') || Common::can_delete('role'))

                        <th>@lang('glob.actions')</th>

                        @endif

                    </tr>
                </thead>
                <tbody>
                   @foreach ($roles as $e)
                   <tr>
                    <td>{{ $e->id }}</td>
                    <td> {{ $e->role }} </td>
                    <td> {{ $e->max_discount }} </td>
                    <td>{{ $e->created_at->format('jS F, Y H:i') }} 
                        <br>
                        <small><em> updated at {{ $e->updated_at->diffForHumans() }}</em></small>
                    </td>

                    @if(Common::can_update('role') || Common::can_delete('role'))

                    <td>

                        @if(Common::can_update('role'))

                        <a href="{{route('role.edit',$e->id)}}" v-on:click="getLoader($event)" class="btn btn-xs btn-info btn-circle" ><i class="fa fa-pencil" aria-hidden="true"></i></a>
                        
                        @endif
                        @if(Common::can_delete('role'))

                        <a id='{{ $e->id }}' v-on:click="deleteRole({{ $e->id }}, $event)" class="btn btn-xs btn-danger btn-circle "><i class="fa fa-trash" aria-hidden="true"></i></a> 
                        
                        @endif

                    </td>

                    @endif

                </tr>
                @endforeach
            </tbody>
            <tfoot class="showSearch">
                <tr>
                    <th style="width:2%">#</th>
                    <th class="search">@lang('glob.roles')</th>
                    <th class="search">@lang('glob.dis_per')</th>
                    <th class="search">@lang('glob.created_date')</th>

                    @if(Common::can_update('role') || Common::can_delete('role'))

                    <th style="width:4%">@lang('glob.actions')</th>

                    @endif

                </tr>
            </tfoot>
        </table>
    </div>
</div>
<!-- view table data end -->
</div>
@endsection
