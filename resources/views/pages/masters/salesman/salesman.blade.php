@extends('layouts.app')

@section('page-title')@lang('glob.salesman')@endsection

@section('page-subtitle')@lang('salesman.add_edit_salesman')@endsection

@section ('breadcrumb')

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> @lang('glob.dashboard')</a></li>
	<li class="active">@lang('glob.salesman')</li>
</ol>

@endsection

@section('content')

<div class="salesman">

	@if(Common::can_create('salesman'))

	<!-- Button div -->
	<div class="row">
		<div class="col-md-6 margin-bottom">
			<a  @if(isset($editSalesman)) href="/common/add/Salesman" @endif class="btn bg-purple new-customer" data-toggle="collapse" data-target="#salesmanFormDiv" v-on:click="addForm">@lang('glob.add_new')
			</a>
		</div>
		<div class="col-md-6 dropdown margin-bottom ">
			<button class="btn btn-success dropdown-toggle pull-right" type="button" data-toggle="dropdown" >Export
				<span class="caret" ></span></button>
				<ul class="dropdown-menu pull-right">
					<li><a href="{{ url('master/salesmans/xls' ) }}">Excel</a></li>
					<li><a href="{{ url('master/salesmans/pdf' ) }}">PDF</a></li>				
				</ul>				
			</div>	
		</div>
		<!-- Button div end -->




		<div id="salesmanFormDiv" class="collapse @if (session('open') || isset($open) || isset($editSalesman) || !$errors->isEmpty()) in @endif">

			@if (isset($editSalesman)) 
			{{ Form::model($editSalesman, ['url'=>array('master/salesman',$editSalesman->id), 'method'=>'PUT','id'=>'salesmanForm']) }}

			@else 
			{{ Form::open(['action'=>'SalesmanController@store','id'=>'salesmanForm']) }}
			@endif

			<div class="row">

				<div class="col-md-12">

					<div class="box box-primary">

						<div class="box-body">

							<div class="row">
								<div class="col-md-3">
									<!-- Emp ID -->
									<div class="form-group">
										{!! Form::label('id', trans('salesman.emp_id')) !!} 
										<span>
											@if (isset($editSalesman->id)) {{ $editSalesman->id }} 
											@elseif(isset($salesman->last()->id)) {{ $salesman->last()->id+1 }}
											@else 1 
											@endif
										</span>
									</div>
									<!-- End Emp ID -->

								</div>
							</div>
							<!-- .row -->

							<div class="row">

								<!-- name -->
								<div class="col-md-5">
									<div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
										{!! Form::label('name', trans('glob.name')) !!}<span style="color:red">*</span>
										<div class="input-group">
											<div class="input-group-addon"><i class="ion ion-person"></i></div>
											{!! Form::text('name', null, array('required', 'class'=>'form-control input-sm ', 'placeholder'=>trans('glob.name'))) !!}
										</div>
										@if ($errors->has('name'))
										<span class="help-block">
											<strong>{{ $errors->first('name') }}</strong>
										</span>
										@endif
									</div>
								</div>
								<!--End name -->

							</div>
							<!-- .row -->

							<!-- .row -->
							<div class="row">

								<!-- designation -->
								<div class="col-md-5">
									<div class="form-group"> 
										{!! Form::label('desigination', trans('glob.desigination')) !!}<span style="color:red">*</span>
										<div class="input-group">
											<div class="input-group-addon"><i class="ion ion-person"></i></div>
											@if(isset($editSalesman->desigination))
											{!!Form::select('desigination', $desiginationType, App\Salesman::getTypeIdAttribute($editSalesman->desigination), array('required', 'class'=>'form-control input-sm select2', 'placeholder'=>trans('supplier.s_type')))!!}
											@else
											{!!Form::select('desigination', $desiginationType, null , array('required', 'class'=>'form-control input-sm select2', 'placeholder'=>trans('glob.desigination')))!!}
											@endif

										</div>
									</div>
								</div>
								<!--End designation -->

							</div>
							<!-- .row -->

						</div>
						<!-- .box-body -->

						<div class="box-footer">

							<div class="form-group">
								<button type="submit" class="btn btn-primary " v-on:click="checkValidateLoader($event)"><i class="fa fa-floppy-o"></i>@if (isset($editSalesman)) @lang('glob.update') @else @lang('glob.save') @endif</button>
								<a  class="btn btn-cyan" v-on:click="resetForm()"><i class="fa fa-refresh"></i> @lang('glob.reset')</a>
								<a  @if (isset($editSalesman)) href="{{ url('master/salesman') }}" @endif  class="btn btn-danger add-customer" data-toggle="collapse" data-target="#salesmanFormDiv" v-on:click="cancelForm">@lang('glob.cancel')</a> 
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
				<table class="table table-bordered table-striped datatable-order" data-sort-order="desc" data-sort-col="0">
					<thead>
						<tr>
							<th style="width:2%">#</th>
							<th>@lang('glob.name')</th>
							<th>@lang('glob.desigination')</th>

							@if(Common::can_update('salesman') || Common::can_delete('salesman'))

							<th style="width:4%">@lang('glob.actions')</th>

							@endif

						</tr>
					</thead>
					<tbody>

						@foreach($salesman as $row)

						<tr>
							<td>{{$row->id}}</td>
							<td>{{$row->name}}</td>
							<td>{{$row->desigination}}</td>

							@if(Common::can_update('salesman') || Common::can_delete('salesman'))

							<td>

								@if(Common::can_update('salesman'))

								<a href="{{route('salesman.edit',$row->id)}}" v-on:click="getLoader($event)"  class="btn btn-xs btn-info btn-circle" ><i class="fa fa-pencil" aria-hidden="true"></i></a>

								@endif
								@if(Common::can_delete('salesman'))

								<a  v-on:click="deleteSalesman({{ $row->id }}, $event)" class="btn btn-xs btn-danger btn-circle"><i class="fa fa-trash" aria-hidden="true"></i></a> 

								@endif

							</td>

							@endif

						</tr>

						@endforeach

					</tbody>
					<tfoot class="showSearch">
						<tr>
							<th style="width:2%">#</th>
							<th class="search">@lang('glob.name')</th>
							<th class="search">@lang('glob.desigination')</th>

							@if(Common::can_update('salesman') || Common::can_delete('salesman'))

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
</div>
@endsection