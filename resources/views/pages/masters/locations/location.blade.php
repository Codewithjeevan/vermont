@extends('layouts.app')

@section('page-title')@lang('glob.location')@endsection

@section('page-subtitle')@lang('glob.add_edit_location')@endsection 

@section ('breadcrumb')

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-home"></i> @lang('glob.home')</a></li>
	<li><a href="#"><i class="fa fa-user"></i> @lang('menu.masters')</a></li>
	<li class="active">@lang('glob.location')</li>
</ol>

@endsection

@section('content')
<div class="location"> 

	@if(Common::can_create('location'))

	<!-- Button div -->
	<div class="row">
		<div class="col-md-6 margin-bottom">
			<a @if(isset($editLocation)) href="/common/add/Locations" @endif class="btn bg-purple new-customer" data-toggle="collapse" data-target="#openlocationFormDiv" v-on:click="addForm">@lang('glob.add_new')</a>
		</div>
		<div class="col-md-6 dropdown margin-bottom ">
			<button class="btn btn-success dropdown-toggle pull-right" type="button" data-toggle="dropdown" >Export
				<span class="caret" ></span></button>
				<ul class="dropdown-menu pull-right">
					<li><a href="{{ url('master/locations/xls' ) }}">Excel</a></li>
					<li><a href="{{ url('master/locations/pdf' ) }}">PDF</a></li>				
				</ul>				
			</div>	
		</div>
		<!-- Button div end -->


		<div id="openlocationFormDiv" class="collapse @if ( session('open') || isset($open) || isset($editLocation) || !$errors->isEmpty()) in @endif">


			@if (isset($editLocation)) 

			{{ Form::model($editLocation, ['url'=>array('master/location',$editLocation->id), 'method'=>'PUT','id'=>'locationForm']) }}
			@else 

			{{ Form::open(['action'=>'LocationsController@store','id'=>'locationForm']) }}
			@endif

			<div class="row customer-box"> 

				<div class="col-md-12">

					<div class="box box-primary"> 

						<div class="box-body">

							<div class="row">

								<!-- Location Id -->
								<div class="col-md-3"> 
									<div class="form-group">
										<label for="">@lang('glob.location_id')</label> :
										@if (isset($editLocation->id)) {{ $editLocation->id }} 
										@elseif(isset($newId)) {{$newId}}
										@else 1 
										@endif
										<span> </span>
									</div>
								</div>
								<!-- End Location Id -->

							</div>

							<div class="row">						

								<!-- Location -->
								<div class="col-md-4"> 
									<div class="form-group">
										<label for="">@lang('glob.location')<span style="color:red">*</span></label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="ion ion-location"></i>
											</div>
											<input type="text" name="location" class="form-control  input-sm" placeholder="@lang('glob.location')" value="{{{ Form::getValueAttribute('location', null) }}}" required="">
										</div>
									</div>
								</div>
								<!-- End Location -->

								<!-- Name -->
								<div class="col-md-4"> 
									<div class="form-group">
										<label for="">@lang('glob.name')<span style="color:red">*</span></label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="ion ion-person"></i>
											</div>
											<input type="text" name="name" class="form-control  input-sm" placeholder="@lang('glob.name')" value="{{{ Form::getValueAttribute('name', null) }}}" required="">
										</div>
									</div>
								</div>
								<!-- End Name -->	

								<!--City/State-->
								<div class="col-md-4">
									<div class="form-group">
										<label for="">@lang('glob.city_state')<span style="color:red">*</span></label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="ion ion-location"></i>
											</div>
											<input type="text" name="city" class="form-control  input-sm" placeholder="@lang('glob.city_state')" value="{{{ Form::getValueAttribute('city', null) }}}" required="">
										</div>
									</div>
								</div>	
								<!-- End City/State-->	

							</div>
							<!-- .row -->

							<!-- .row -->
							<div class="row">

								<!-- phone -->
								<div class="col-md-3">
									<div class="form-group {{ $errors->has('phone') ? ' has-error' : '' }}">
										<label for="">@lang('glob.phone')<span style="color:red">*</span></label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="ion ion-ios-telephone"></i>
											</div>
											<input type="text" name="phone" class="form-control  input-sm phone" placeholder="@lang('glob.phone')" value="{{{ Form::getValueAttribute('phone', null) }}}" required="">
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
										<label for="">@lang('glob.mobile')<span style="color:red">*</span></label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="ion ion-iphone"></i>
											</div>
											<input type="text" name="mobile" class="form-control  input-sm mobile" placeholder="@lang('glob.mobile')" value="{{{ Form::getValueAttribute('mobile', null) }}}" required="">
										</div>

										@if ($errors->has('mobile'))
										<span class="help-block">
											<strong>{{ $errors->first('mobile') }}</strong>
										</span>
										@endif

									</div>
								</div>
								<!--End mobile -->


								<!-- email -->
								<div class="col-md-3">
									<div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
										<label for="">@lang('glob.email')<span style="color:red">*</span></label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="ion ion-at"></i>
											</div>
											<input type="email" name="email" class="form-control  input-sm" placeholder="@lang('glob.email')" value="{{{ Form::getValueAttribute('email', null) }}}" required="">
										</div>

										@if ($errors->has('email'))
										<span class="help-block">
											<strong>{{ $errors->first('email') }}</strong>
										</span>
										@endif

									</div>
								</div>
								<!--End email -->

								<!-- Tin No. -->
								<div class="col-md-3">
									<div class="form-group">
										<label for="">@lang('supplier.tin_no')<span style="color:red">*</span></label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="icon ion-ios-pricetags-outline" aria-hidden="true"></i>
											</div>
											<input type="text" name="tin" class="form-control  input-sm" placeholder="@lang('supplier.tin_no')" maxlength="20"  value="{{{ Form::getValueAttribute('tin', null) }}}" required="">
										</div>
									</div>
								</div>
								<!--End Tin No. -->

							</div>						
							<!-- .row -->



							<div class="row">

								<!-- address -->
								<div class="col-md-6">
									<div class="form-group {{ $errors->has('address') ? ' has-error' : '' }}">
										<label for="">@lang('glob.address') <span style="color:red">*</span></label>
										<textarea name="address" id=""   rows="4" class="form-control  input-sm fix-textarea" placeholder="@lang('glob.address')" required="">{{{ Form::getValueAttribute('address', null) }}}</textarea>						
										@if ($errors->has('address'))
										<span class="help-block">
											<strong>{{ $errors->first('address') }}</strong>
										</span>
										@endif
									</div>
								</div>
								<!--End address -->

								<!-- Terms -->
								<div class="col-md-6">
									<div class="form-group {{ $errors->has('terms') ? ' has-error' : '' }}">									
										<label for="">@lang('glob.terms') <span style="color:red">*</span></label>
										<textarea name="terms" id=""   rows="4" class="form-control  input-sm fix-textarea" placeholder="@lang('glob.terms')" required="">{{{ Form::getValueAttribute('terms', null) }}}</textarea>	
										@if ($errors->has('terms'))
										<span class="help-block">
											<strong>{{ $errors->first('terms') }}</strong>
										</span>
										@endif
									</div>
								</div>
								<!--End Terms -->


							</div>
							<!-- row ends-->

						</div>
						<!-- .box-body -->
						<div class="box-footer">
							<div class="form-group">
								<button type="submit" class="btn btn-primary " v-on:click="checkValidateLoader($event)"><i class="fa fa-floppy-o"></i>
									@if (isset($editLocation)) @lang('glob.update') @else @lang('glob.save') @endif
								</button>
								<a  class="btn btn-cyan" v-on:click="resetForm"><i class="fa fa-refresh"></i> @lang('glob.reset')</a>
								<a  class="btn btn-danger add-customer" data-toggle="collapse" data-target="#openlocationFormDiv" v-on:click="cancelForm">@lang('glob.cancel')</a>
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

				<table class="table table-bordered table-striped datatable1">
					<thead>
						<tr>
							<th style="width:10%">#</th>						
							<th>@lang('glob.location')</th> 
							<th>@lang('glob.name')</th>
							<th>@lang('supplier.tin_no')</th>	


							@if(Common::can_update('location') || Common::can_delete('location'))

							<th style="width:4%">@lang('customer.actions')</th>

							@endif


						</tr>
					</thead>
					<tbody>

						@foreach($locations as $row)

						<tr>
							<td>{{$row->id}}</td>						
							<td>{{$row->location}}</td>
							<td>{{$row->name}}</td>						
							<td>{{$row->tin}}</td>

							@if(Common::can_update('location') ||  Common::can_delete('location'))

							<td>

								@if(Common::can_update('location'))

								<a href="{{route('location.edit',$row->id)}}" v-on:click="getLoader($event)" class="btn btn-xs btn-info btn-circle" ><i class="fa fa-pencil" aria-hidden="true"></i></a>

								@endif
								@if(Common::can_delete('location'))

								<a v-on:click="deleteLocation({{ $row->id }}, $event)"  class="btn btn-xs btn-danger btn-circle"><i class="fa fa-trash" aria-hidden="true"></i></a> 

								@endif

							</td>

							@endif

						</tr>

						@endforeach


					</tbody>
					<tfoot class="showSearch">
						<tr>

							<th style="width:10%">#</th>					
							<th>@lang('glob.location')</th> 
							<th>@lang('glob.name')</th>
							<th>@lang('supplier.tin_no')</th>	

							@if(Common::can_update('location') || Common::can_delete('location'))

							<th style="width:4%">@lang('customer.actions')</th>

							@endif


						</tr>
					</tfoot>
				</table>

			</div>
		</div>
	
</div>

@endsection