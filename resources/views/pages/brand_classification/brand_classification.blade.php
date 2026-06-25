@extends('layouts.app')

@section('page-title')@lang('glob.brand_classification')@endsection

@section('page-subtitle')@lang('glob.manage_brand_classification')@endsection

@section ('breadcrumb')

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> @lang('glob.brand_classification')</a></li>
	<li class="active">@lang('glob.brand_classification')</li> 
</ol>

@endsection

@section('content')
<div class="brand_classification">

	@if(Common::can_create('brand_classification')) 

	<!-- Button div -->
	<div class="row">
		<div class="col-md-6 margin-bottom"> 
			<a  @if(isset($editCategory)) href="/common/add/brand_classification" @endif class="btn bg-purple new-customer" data-toggle="collapse" data-target="#brandClassificationFormDiv" v-on:click="addForm()">@lang('glob.add_new')</a>
		</div>
		</div>
		<!-- Button div end -->

		<div id="brandClassificationFormDiv" class="collapse @if (session('open') || isset($open)  || !$errors->isEmpty() ) in @endif">

			
		
			{{ Form::open(['action'=>'BrandClassificationController@store','id'=>'brandClassificationForm']) }}<!-- form action for store -->
		

			<div class="row customer-box">

				<div class="col-md-12">

					<div class="box box-primary">

						<div class="box-body">

							
							<div class="row">

								{{-- parent category --}}
								<div class="col-md-5">
									<div class="form-group {{ $errors->has('brand_id') ? ' has-error' : '' }}">
										{!! Form::label('title', trans('glob.brand')) !!}<span style="color:red">*</span>
										<div class="input-group">
											<div class="input-group-addon"><i class="fa fa-tags" aria-hidden="true"></i></div>
											<select name="brand_id" class="form-control input-sm " required="">
												<option value="" >Select Brand</option>
												@foreach( $parentCategories as $c)
												<option value="{{ $c->id }}">{{ $c->categorie }}</option>
												@endforeach
											</select>
										</div>

										@if ($errors->has('brand_id'))
										<span class="help-block">
											<strong>{{ $errors->first('brand_id') }}</strong>
										</span>
										@endif

									</div>
								</div>

							</div>
							{{-- /.row --}}
							<div class="row">
										<div class="col-md-5">
								<div class="form-group">
									{!! Form::label('title', trans('glob.activation_amount')) !!}<span style="color:red">*</span>
									<div class="input-group">
										<div class="input-group-addon"> <i class="fa fa-money" aria-hidden="true"></i> </div>
										{!! Form::text('activation_amount', null, 
										array('required',  'class'=>'form-control input-sm',  'placeholder'=>trans('glob.activation_amount'))) !!}
									</div>

									@if ($errors->has('brand_id'))
									<span class="help-block">
										<strong>{{ $errors->first('brand_id') }}</strong>
									</span>
									@endif

								</div>
							</div>
							</div>
						</div>

						<!-- .box-body -->
						<div class="box-footer">

							<div class="form-group">

								<button type="submit"  class="btn btn-primary " v-on:click="getLoader($event)"><i class="fa fa-floppy-o"></i>@lang('glob.save') </button>
								<a  class="btn btn-cyan" v-on:click="resetForm()"><i class="fa fa-refresh"></i> @lang('glob.reset')</a>
								<a class="btn btn-danger add-customer" data-toggle="collapse" data-target="#brandClassificationFormDiv" v-on:click="cancelForm">@lang('glob.cancel')</a>

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
							<th>@lang('glob.brand_name')</th>

							<th>@lang('glob.activation_amount')</th>
                     	@if(Common::can_delete('brand_classification'))

							<th style="width:4%">@lang('glob.actions')</th>

							@endif

						</tr>
					</thead>
					<tbody>

						@foreach($brandClassification as $row)

						<tr>
							<td>{{$row->id}}</td>
							<td>{{$row->parentCategory->categorie}}</td>
							<td>{{$row->activation_amount}}</td>
							

							@if(Common::can_delete('brand_classification'))

							<td>

								
								@if(Common::can_delete('brand_classification'))

								<a id='{{ $row->id }}' v-on:click="deleteBrandClassification({{ $row->id }}, $event)" class="btn btn-xs btn-danger btn-circle "><i class="fa fa-trash" aria-hidden="true"></i></a> 

								@endif

							</td> 

							@endif

						</tr>

						@endforeach
						
					</tbody>
					<tfoot class="showSearch">
						<tr>
							<th style="width:2%">#</th>
							<th class="search">@lang('glob.brand_name')</th>
							<th class="search">@lang('glob.activation_amount')</th>
							@if(Common::can_delete('brand_classification'))

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