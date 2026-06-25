@extends('layouts.app')

@section('page-title')@lang('glob.unit')@endsection

@section('page-subtitle')@lang('unit.add_edit_unit')@endsection

@section ('breadcrumb')

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> @lang('glob.dashboard')</a></li>
	<li class="active">@lang('glob.unit')</li>
</ol>

@endsection

@section('content')
<div class="unit">


	@if(Common::can_create('unit'))

	<!-- Button div -->
	<div class="row">
		<div class="col-md-6 margin-bottom">
			<a  class="btn bg-purple new-customer" @if(isset($editUnit)) href="/common/add/Units" @endif data-toggle="collapse" data-target="#unitFormDiv" v-on:click="addForm">@lang('glob.add_new')</a>
		</div>
		<div class="col-md-6 dropdown margin-bottom ">
			<button class="btn btn-success dropdown-toggle pull-right" type="button" data-toggle="dropdown" >Export
				<span class="caret" ></span></button>
				<ul class="dropdown-menu pull-right">
					<li><a href="{{ url('master/units/xls' ) }}">Excel</a></li>
					<li><a href="{{ url('master/units/pdf' ) }}">PDF</a></li>				
				</ul>				
			</div>	
		</div>
		<!-- Button div end -->

		<div id="unitFormDiv" class="collapse @if (session('open') || isset($open) || isset($editUnit) || !$errors->isEmpty() ) in @endif">

			@if (isset($editUnit))
			{{ Form::model($editUnit, ['url'=>array('master/unit',$editUnit->id), 'method'=>'PUT','id'=>'unitForm']) }}@else 
			<!-- form action for store -->
			{{ Form::open(['action'=>'UnitsController@store','id'=>'unitForm']) }}
			@endif

			<div class="row customer-box">

				<div class="col-md-12">

					<div class="box box-primary">

						<div class="box-body">

							<div class="row">

								<!-- unit -->
								<div class="col-md-5">
									<div class="form-group {{ $errors->has('unit') ? ' has-error' : '' }}">
										{!! Form::label('title', trans('glob.unit')) !!}<span style="color:red">*</span>
										<div class="input-group">
											<div class="input-group-addon"><i class="fa fa-th-large" aria-hidden="true"></i></div>
											{!! Form::text('unit', null, array('required', 'class'=>'form-control input-sm', 'placeholder'=>trans('glob.unit')))!!}
										</div>

										@if ($errors->has('unit'))
										<span class="help-block">
											<strong>{{ $errors->first('unit') }}</strong>
										</span>
										@endif
										
									</div>
								</div>
								<!-- end type -->

							</div>
							<!-- .row -->
						</div>

						<!-- .box-body -->
						<div class="box-footer">

							<div class="form-group">
								<button type="submit"  class="btn btn-primary " v-on:click="checkValidateLoader($event)"><i class="fa fa-floppy-o"></i>@if (isset($editUnit)) @lang('glob.update') @else @lang('glob.save') @endif</button>
								<a  class="btn btn-cyan" v-on:click="resetForm"><i class="fa fa-refresh"></i> @lang('glob.reset')</a>
								<a  @if (isset($editUnit)) href="{{ url('master/unit') }}" @endif class="btn btn-danger add-customer" data-toggle="collapse" data-target="#unitFormDiv" v-on:click="cancelForm">@lang('glob.cancel')</a>
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
			<!-- end form -->
		</div>

		@endif
		
		<!-- view table data  -->
		<div class="row">
			<div class="col-md-12 table-responsive">
				<table class="table table-bordered table-striped datatable-order" data-sort-order="desc" data-sort-col="0">
					<thead>
						<tr>
							<th style="width:2%">#</th>
							<th>@lang('glob.unit')</th>
							<th >@lang('glob.created_date')</th>

							@if(Common::can_update('unit') || Common::can_delete('unit'))

							<th style="width:4%">@lang('glob.actions')</th>
							
							@endif

						</tr>
					</thead>
					<tbody>
						@foreach($unit as $row)
						<tr>
							<td>{{$row->id}}</td>
							<td>{{$row->unit}}</td>
							<td >{{ $row->created_at->format('jS F, Y') }}
								<br/>
								<em>
									<small >
										@lang('glob.updated_at') {{ $row->created_at->diffForHumans() }}
									</small>
								</em>
							</td>

							@if(Common::can_update('unit') || Common::can_delete('unit'))

							<td>

								@if(Common::can_update('unit'))

								<a href="{{route('unit.edit',$row->id)}}" v-on:click="getLoader($event)" class="btn btn-xs btn-info btn-circle" ><i class="fa fa-pencil" aria-hidden="true"></i></a>

								@endif
								@if(Common::can_delete('unit'))

								<a v-on:click="deleteUnit({{ $row->id }},$event)"  class="btn btn-xs btn-danger btn-circle"><i class="fa fa-trash" aria-hidden="true"></i></a> 

								@endif

							</td>

							@endif

						</tr>

						@endforeach

					</tbody>
					<tfoot class="showSearch">
						<tr>
							<th style="width:2%">#</th>
							<th class="search">@lang('glob.unit')</th>
							<th class="search">@lang('glob.created_date')</th>

							@if(Common::can_update('unit') || Common::can_delete('unit'))

							<th style="width:4%">@lang('glob.actions')</th>

							@endif
							
						</tr>
					</tfoot>

				</table>

			</div>
			{{-- .col-md-12 --}}
		</div>
		<!-- view table data end -->


	</div>

	@endsection