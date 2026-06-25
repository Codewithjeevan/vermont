@extends('layouts.app')
@section('page-title')@lang('supplier.supplier')@endsection
@section('page-subtitle')@lang('supplier.add_edit_supplier')@endsection
@section ('breadcrumb')
<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> @lang('glob.dashboard')</a></li>
	<li class="active">@lang('supplier.supplier')</li>
</ol>
@endsection

@section('content') 
<div class="supplier">

	@if(Common::can_create('supplier'))

	<div class="row">
		<!-- Button div -->
		<div class="col-md-6 margin-bottom">
			<a @if(isset($editSupplier)) href="/common/add/Supplier" @endif  class="btn bg-purple new-customer" data-toggle="collapse" data-target="#supplierFormDiv" v-on:click="addForm">@lang('glob.add_new')</a>
		</div>
		<div class="col-md-6 dropdown margin-bottom ">
			<button class="btn btn-success dropdown-toggle pull-right" type="button" data-toggle="dropdown" >Export
				<span class="caret" ></span></button>
				<ul class="dropdown-menu pull-right">
					<li><a href="{{ url('master/suppliers/xls' ) }}">Excel</a></li>
					<li><a href="{{ url('master/suppliers/pdf' ) }}">PDF</a></li>				
				</ul>				
			</div>	
		</div>
		<!-- Button div end -->

		<!--collapse div starts-->
		<div id="supplierFormDiv" class="collapse @if (session('open') || isset($open) || isset($editSupplier) || !$errors->isEmpty()) in @endif" > 
			<!-- form action for store -->

			@if (isset($editSupplier)) 

			{{ Form::model($editSupplier, ['url'=>array('master/supplier',$editSupplier->id), 'method'=>'PUT','id'=>'supplierForm']) }}
			@else 
			{{ Form::open(['action'=>'SupplierController@store','id'=>'supplierForm']) }}
			@endif

			<div class="row customer-box">
				<div class="col-md-12">

					<!--Box body-primary starts-->
					<div class="box box-primary">

						<!--Box body starts-->
						<div class="box-body">

							<div class="row">
								<!-- supplier ID -->
								<div class="form-group">
									<div class="col-md-3">

										{!! Form::label('title', trans('supplier.supplier_id')) !!} 

										<span>

											@if (isset($editSupplier->id)) {{ $editSupplier->id }} 
											@elseif(isset($newId[0])) {{$newId[0]->auto_increment}}
											@else 1 
											@endif

										</span>
									</div>
								</div>
							</div>
							<!--row end -->

							<div class="row">

								<!-- S.type -->
								<div class="col-md-2">
									<div class="form-group">
										{!! Form::label('title', trans('supplier.s_type')) !!} <label></label>
										<div class="input-group">
											<div class="input-group-addon"><i class="ion ion-person"></i></div>
											<select id="type" name="type"  class="form-control input-sm select2" >
												@foreach(App\Supplier::$supplierType as $s)
												<option value="{{ $s }}" @if(isset($editSupplier) && $editSupplier->type==$s) selected @else @if($s=='Supplier') selected  @endif @endif >{{ $s }}</option>
												@endforeach
											</select>
										</div>
									</div>
								</div>
								<!--end  S.type -->

								<!-- type -->
								<div class="col-md-2">
									<div class="form-group">

										{!! Form::label('title', trans('glob.title')) !!} <label></label>
										<div class="input-group">
											<div class="input-group-addon"><i class="ion ion-person"></i></div>
											
											{!!Form::select('title', array('0' => trans(''),'1' => trans('glob.mr'), '2' => trans('glob.ms'), '3' => trans('glob.mrs')), null, array( 'class'=>'form-control input-sm select2'))!!}

										</div>
									</div>
								</div>
								<!--End type -->

								<!-- supplier name -->
								<div class="col-md-4">
									<div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
										{!! Form::label('title', trans('supplier.company')) !!}<label><span style="color:red">*</span></label>
										<div class="input-group">
											<div class="input-group-addon">	<i class="ion ion-person"></i></div>

											{!! Form::text('name', null, array('required', 'class'=>'form-control input-sm', 'placeholder'=>trans('supplier.company'))) !!}
										</div>
										@if ($errors->has('name'))
										<span class="help-block">
											<strong>{{ $errors->first('name') }}</strong>
										</span>
										@endif
									</div>
								</div>
								<!--End supplier name -->

								<!-- contact -->
								<div class="col-md-4">
									<div class="form-group">
										{!! Form::label('title', trans('glob.name')) !!}<label></label>
										<div class="input-group">
											<div class="input-group-addon"><i class="ion ion-person"></i>	</div>
											{!! Form::text('contact', null, array( 'class'=>'form-control input-sm ', 'placeholder'=>trans('glob.name'))) !!}
										</div>
									</div>
								</div>
								<!--End contact -->

							</div>
							<!-- Row ends-->

							<div class="row">

								<!-- phone -->
								<div class="col-md-3">
									<div class="form-group {{ $errors->has('phone') ? ' has-error' : '' }}">
										{!! Form::label('title', trans('glob.phone')) !!}<label></label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="ion ion-ios-telephone"></i>
											</div>
											{!! Form::text('phone', null, 
											array( 
											'class'=>'form-control input-sm phone', 
											'placeholder'=>trans('glob.phone'))) !!}
										</div>

										@if ($errors->has('phone'))
										<span class="help-block">
											<strong>{{ $errors->first('phone') }}</strong>
										</span>
										@endif

									</div>
								</div>
								<!--End phone -->

								<!-- mobile -->
								<div class="col-md-3">
									<div class="form-group {{ $errors->has('mobile') ? ' has-error' : '' }}">
										{!! Form::label('title', trans('glob.mobile')) !!}<label></label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="ion ion-iphone"></i>
											</div>
											{!! Form::text('mobile', null, 
											array( 
											'class'=>'form-control input-sm mobile', 
											'placeholder'=>trans('glob.mobile'))) !!}
										</div>

										@if ($errors->has('mobile'))
										<span class="help-block">
											<strong>{{ $errors->first('mobile') }}</strong>
										</span>
										@endif

									</div>
								</div>
								<!--End mobile -->

								<!-- fax -->
								<div class="col-md-3">
									<div class="form-group">
										{!! Form::label('title', trans('glob.fax')) !!}<label></label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="fa fa-fax" aria-hidden="true"></i>
											</div>
											{!! Form::text('fax', null, 
											array( 
											'class'=>'form-control input-sm', "maxlength"=>"15", 
											'placeholder'=>trans('glob.fax'))) !!}
										</div>
									</div>
								</div>
								<!--End fax -->

								<!-- email -->
								<div class="col-md-3">
									<div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
										{!! Form::label('title', trans('glob.email')) !!}<label></label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="ion ion-at"></i>
											</div>
											{!! Form::email('email', null, 
											array( 
											'class'=>'form-control input-sm', 
											'placeholder'=>trans('glob.email'))) !!}
										</div>

										@if ($errors->has('email'))
										<span class="help-block">
											<strong>{{ $errors->first('email') }}</strong>
										</span>
										@endif

									</div>
								</div>
								<!--End email -->
							</div>
							<!--row end-->

							<div class="row">

								<!-- address -->
								<div class="col-md-6">
									<div class="form-group">
										{!! Form::label('title', trans('glob.address')) !!}<label></label>
										{!! Form::textarea('address', null, 
										array(
										'rows'=>'4', 
										'class'=>'form-control input-sm fix-textarea', 
										'maxlength'=> '255',
										'placeholder'=> trans('glob.address'))) !!}
									</div>
								</div>
								<!--End address -->

								<!-- div for fax,email,city/state -->
								<div class="col-md-6 no-margin no-padding">

									<!-- sity/state -->
									<div class="col-md-6 no-margin">
										<div class="form-group">
											{!! Form::label('title', trans('glob.city_state')) !!}<label></label>
											<div class="input-group">
												<div class="input-group-addon">
													<i class="ion ion-location"></i>
												</div>
												{!! Form::text('city', null, 
												array( 
												'class'=>'form-control input-sm', 
												'maxlength'=> '40',
												'placeholder'=>trans('glob.city_state'))) !!}
											</div>
										</div>
									</div>
									<!--End sity/state -->
									<!-- ECC No. -->
									<div class="col-md-6">
										<div class="form-group">
											{!! Form::label('title', trans('supplier.ecc_no')) !!}<label></label>
											<div class="input-group">
												<div class="input-group-addon">
													<i class="icon ion-ios-pricetags-outline" aria-hidden="true"></i>
												</div>
												{!! Form::text('ecc', null, 
												array( 
												'class'=>'form-control input-sm ecc-no', 

												'placeholder'=>trans('supplier.ecc_no'))) !!}
											</div>
										</div>
									</div>
									<!--End ECC No. -->

									<!-- Range -->
									<div class="col-md-6">
										<div class="form-group">
											{!! Form::label('title', trans('supplier.range')) !!}<label></label>
											<div class="input-group">
												<div class="input-group-addon">
													<i class="fa fa-arrows-h" aria-hidden="true"></i>
												</div>
												{!! Form::text('range', null, 
												array( 
												'class'=>'form-control input-sm', 
												'maxlength'=> '15',
												'placeholder'=>trans('supplier.range'))) !!}
											</div>
										</div>
									</div>
									<!--End Range -->

									<!-- comm -->
									<div class="col-md-6">
										<div class="form-group">
											{!! Form::label('title', trans('supplier.comm')) !!}<label></label>
											<div class="input-group">
												<div class="input-group-addon">
													<i class="icon ion-ios-pricetags-outline" aria-hidden="true"></i>
												</div>
												{!! Form::text('comm', null, 
												array(
												'class'=>'form-control input-sm', 
												'maxlength'=> '15',
												'placeholder'=>trans('supplier.comm'))) !!}
											</div>
										</div>
									</div>
									<!--End comm -->
								</div>
								<!-- div for fax,email,city/state -->
							</div>
							<!-- row ends-->

							<div class="row">

								<!-- Division -->
								<div class="col-md-3">
									<div class="form-group">
										{!! Form::label('title', trans('supplier.division')) !!}<label></label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="fa fa-university" aria-hidden="true"></i>
											</div>
											{!! Form::text('division', null, 
											array(
											'class'=>'form-control input-sm',
											'maxlength'=> '15', 
											'placeholder'=>trans('supplier.division'))) !!}
										</div>
									</div>
								</div>
								<!--End Division -->

								<!-- C.S.T. No. -->
								<div class="col-md-3">
									<div class="form-group">
										{!! Form::label('title', trans('supplier.cst_no')) !!}<label></label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="icon ion-ios-pricetags-outline" aria-hidden="true"></i>
											</div>
											{!! Form::text('cst', null, 
											array(
											'class'=>'form-control input-sm cst-no',
											
											'placeholder'=>trans('supplier.cst_no'))) !!}
										</div>
									</div>
								</div>
								<!--End C.S.T. No. -->

								<!-- Pan No. -->
								<div class="col-md-3">
									<div class="form-group">
										{!! Form::label('title', trans('supplier.pan_no')) !!}<label></label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="icon ion-card" aria-hidden="true"></i>
											</div>
											
											{!! Form::text('pan', null, 
											array(
											'class'=>'form-control input-sm pan-no', 

											'placeholder'=>trans('supplier.pan_no'))) !!}
										</div>
									</div>
								</div>
								<!--End Pan No. -->

								<!-- Tin No. -->
								<div class="col-md-3">
									<div class="form-group">
										{!! Form::label('title', trans('supplier.tin_no')) !!}<label></label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="icon ion-ios-pricetags-outline" aria-hidden="true"></i>
											</div>
											{!! Form::text('tin', null, 
											array( 
											'class'=>'form-control input-sm tin-no', 
											
											'placeholder'=>trans('supplier.tin_no'))) !!}
										</div>
									</div>
								</div>
								<!--End Tin No. -->
							</div>
							<!-- row ends-->
						</div>
						<!--end .Box-body  -->
						<!-- .box-footer starts -->
						<div class="box-footer">
							<div class="form-group">
								<button type="submit" class="btn btn-primary ">
									<i class="fa fa-floppy-o"></i>
									@if (isset($editSupplier)) @lang('glob.update') @else @lang('glob.save') @endif
								</button>
								<a  class="btn btn-cyan" v-on:click="resetForm()"><i class="fa fa-refresh"></i> @lang('glob.reset')</a>
								<a @if (isset($editSupplier)) href="{{ url('master/supplier') }}" @endif 
								class="btn btn-danger add-customer"  data-toggle="collapse" data-target="#supplierFormDiv" v-on:click="cancelForm">@lang('glob.cancel')</a>
							</div>
						</div>
						<!--end .box-footer  -->
					</div>
					<!--Box body-primary ends-->
				</div>
			</div>
			<!--end .row .customer-box -->
			{{ Form::close() }}
		</div>
		<!--collapse div ends-->

		@endif

		<!-- view table data  -->
		<div class="row">
			<div class="col-md-12 table-responsive">
				<table class="table table-bordered table-striped datatable-order" data-sort-order="desc" data-sort-col="0">
					<thead>
						<tr>
							<th style="width:2%">#</th>
							<th>@lang('supplier.company')</th>
							<th>@lang('supplier.s_type')</th>
							<th>@lang('glob.phone')</th>
							<th>@lang('glob.city')</th>

							@if(Common::can_update('supplier') || Common::can_delete('supplier'))

							<th style="width:4%">@lang('glob.actions')</th>

							@endif

						</tr>
					</thead>
					<tbody>
						@foreach($suppliers as $row)
						<tr>
							<td>{{$row->id}}</td>
							<td>{{$row->name}}</td>
							<td>{{$row->type}}</td>
							<td>{{$row->phone}}</td>
							<td>{{$row->city}}</td> 

							@if(Common::can_update('supplier') || Common::can_delete('supplier'))

							<td>

								@if(Common::can_update('supplier'))

								<a  href="{{route('supplier.edit',$row->id)}}" v-on:click="getLoader($event)"  class="btn btn-xs btn-info btn-circle" ><i class="fa fa-pencil" aria-hidden="true"></i></a>

								@endif
								@if(Common::can_delete('supplier'))


								<a id='{{ $row->id }}' v-on:click="deleteSupplier({{ $row->id }}, $event)" class="btn btn-xs btn-danger btn-circle"><i class="fa fa-trash" aria-hidden="true"></i></a>

								@endif

							</td>

							@endif

						</tr>
						@endforeach 
					</tbody>
					<tfoot class="showSearch">
						<tr>
							<th style="width:2%">#</th>
							<th class="search">@lang('supplier.company')</th>
							<th class="search">@lang('supplier.s_type')</th>
							<th class="search">@lang('glob.phone')</th>
							<th class="search">@lang('glob.city')</th>

							@if(Common::can_update('supplier') || Common::can_delete('supplier'))

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