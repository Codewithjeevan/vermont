@extends('layouts.app')

@section('page-title')@lang('glob.currency')@endsection

@section('page-subtitle')@lang('glob.add_edit_currency')@endsection 

@section ('breadcrumb')

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> @lang('glob.dashboard')</a></li>
	<li class="active">@lang('glob.currency')</li>
</ol>

@endsection

@section('content') 
<div class="currency">

	@if(Common::can_create('currency')) 

	<!-- Button div -->
	<div class="row">
		<div class="col-md-6 margin-bottom"> 
			<a  @if(isset($editCurrency)) href="/common/add/Currency" @endif class="btn bg-purple new-customer" data-toggle="collapse" data-target="#currencyFormDiv" v-on:click="addForm()">@lang('glob.add_new')</a>
		</div>
		<div class="col-md-6 dropdown margin-bottom ">
			<button class="btn btn-success dropdown-toggle pull-right" type="button" data-toggle="dropdown" >Export
				<span class="caret" ></span></button>
				<ul class="dropdown-menu pull-right">
					<li><a href="{{ url('master/currency/xls' ) }}">Excel</a></li>
					<li><a href="{{ url('master/currency/pdf' ) }}">PDF</a></li>				
				</ul>				
			</div>	
		</div>
		<!-- Button div end -->

		<div id="currencyFormDiv" class="collapse @if (session('open') || isset($open) || isset($editCurrency) || !$errors->isEmpty() ) in @endif">

			@if (isset($editCurrency)) 
			{{ Form::model($editCurrency, ['url'=>array('master/currency',$editCurrency->id),'method'=>'PUT','id'=>'currencyForm']) }}
			@else 
			{{ Form::open(['action'=>'CurrencyController@store','id'=>'currencyForm']) }}<!-- form action for store -->
			@endif

			<div class="row customer-box">

				<div class="col-md-12">

					<div class="box box-primary">

						<div class="box-body">

							<div class="row">

								<!-- type -->
								<div class="col-md-5">
									<div class="form-group {{ $errors->has('currency') ? ' has-error' : '' }}">
										{!! Form::label('title', trans('glob.currency')) !!}<span style="color:red">*</span>
										<div class="input-group">
											<div class="input-group-addon"><i class="fa fa-tags" aria-hidden="true"></i></div>
											{!! Form::text('currency', null, array('required', 'class'=>'form-control input-sm', 'placeholder'=>trans('glob.currency'))) !!}
										</div>

										@if ($errors->has('currency'))
										<span class="help-block">
											<strong>{{ $errors->first('currency') }}</strong>
										</span>
										@endif

									</div>
								</div>
								<!-- end type -->

								<!-- type -->
								<div class="col-md-3">
									<div class="form-group {{ $errors->has('code') ? ' has-error' : '' }}">
										{!! Form::label('title', trans('glob.code')) !!}<span style="color:red">*</span>
										<div class="input-group">
											<div class="input-group-addon"><i class="fa fa-code" aria-hidden="true"></i></div>
											{!! Form::text('code', null, array('required', 'class'=>'form-control input-sm', 'placeholder'=>trans('glob.code'))) !!}
										</div>

										@if ($errors->has('code'))
										<span class="help-block">
											<strong>{{ $errors->first('code') }}</strong>
										</span>
										@endif

									</div>
								</div>
								<!-- end type -->


								<!-- type -->
								<div class="col-md-4">
									<div class="form-group {{ $errors->has('amount') ? ' has-error' : '' }}">
										{!! Form::label('title', trans('glob.amount')) !!}<span style="color:red">*</span>
										<div class="input-group">
											<div class="input-group-addon"><i class="fa fa-money" aria-hidden="true"></i></div>
											{!! Form::text('amount', null, array('required', 'class'=>'form-control input-sm validate-decimal', 'placeholder'=>trans('glob.amount'))) !!}
										</div>

										@if ($errors->has('amount'))
										<span class="help-block">
											<strong>{{ $errors->first('amount') }}</strong>
										</span>
										@endif

									</div>
								</div>
								<!-- end type -->

							</div>
							<!-- /.row -->
							
							
						</div>

						<!-- .box-body -->
						<div class="box-footer">

							<div class="form-group">

								<button type="submit"  class="btn btn-primary " v-on:click="getLoader($event)"><i class="fa fa-floppy-o"></i>@if (isset($editCurrency)) @lang('glob.update') @else @lang('glob.save') @endif</button>
								<a  class="btn btn-cyan" v-on:click="resetForm()"><i class="fa fa-refresh"></i> @lang('glob.reset')</a>
								<a  @if (isset($editCurrency)) href="{{ url('master/currency') }}" @endif class="btn btn-danger add-customer" data-toggle="collapse" data-target="#currencyFormDiv" v-on:click="cancelForm">@lang('glob.cancel')</a>

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
				<table class="table table-bordered table-hover datatable-order" data-sort-order="desc" data-sort-col="0">
					<thead>
						<tr>
							<th style="width:2%">#</th>
							<th>@lang('glob.currency')</th>
							<th>@lang('glob.code')</th>
							<th>@lang('glob.amount')</th>
							<th>@lang('glob.created_date')</th>

							@if(Common::can_update('currency') || Common::can_delete('currency'))

							<th style="width:4%">@lang('glob.actions')</th>

							@endif

						</tr>
					</thead>
					<tbody>
						@foreach($arrCurrency as $objCurrency)

						<tr>
							<td>{{$objCurrency->id}}</td>
							<td>{{$objCurrency->currency}}</td>
							<td>{{$objCurrency->code}}</td>
							<td>{{$objCurrency->amount}}</td>
							<td >{{ $objCurrency->created_at->format('jS F, Y') }}
								<br/>
								<em>
									<small class="font-roboto">
										Last Updated At {{ $objCurrency->created_at->diffForHumans() }}
									</small>
								</em>
							</td>

							@if(Common::can_update('currency') ||  Common::can_delete('currency'))

							<td>

								@if(Common::can_update('currency'))

								<a href="{{route('currency.edit',$objCurrency->id)}}" v-on:click="getLoader($event)" class="btn btn-xs btn-info btn-circle" ><i class="fa fa-pencil" aria-hidden="true"></i></a>

								@endif
								@if(Common::can_delete('currency'))

								<a id='{{ $objCurrency->id }}' v-on:click="deleteCurrency({{ $objCurrency->id }}, $event)" class="btn btn-xs btn-danger btn-circle "><i class="fa fa-trash" aria-hidden="true"></i></a> 

								@endif

							</td> 

							@endif

						</tr>

						@endforeach
						
					</tbody>
					<tfoot class="showSearch">
						<tr>
							<th style="width:2%">#</th>
							<th class="search">@lang('glob.currency')</th>
							<th class="search">@lang('glob.code')</th>
							<th class="search">@lang('glob.amount')</th>
							<th class="search">@lang('glob.created_date')</th>
							@if(Common::can_update('currency') ||  Common::can_delete('currency'))

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