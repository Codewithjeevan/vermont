@extends('layouts.app')

@section('page-title')@lang('customer.customer')@endsection

@section('page-subtitle')@lang('customer.add_edit_customer')@endsection

@section ('breadcrumb')

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> @lang('glob.dashboard')</a></li>
	<li class="active">@lang('customer.customer')</li>
</ol>

@endsection

@section('content')
<div class="customer">

	@if(Common::can_create('customer'))

	<!-- Button div -->
	<div class="row">
		<div class="col-md-6 margin-bottom">
			<a  @if(isset($editCustomer)) href="/common/add/Customers" @endif class="btn bg-purple new-customer" data-toggle="collapse" data-target="#customerFormDiv" v-on:click="addForm">@lang('glob.add_new')</a>
		</div>
		<div class="col-md-6 dropdown margin-bottom ">
			<button class="btn btn-success dropdown-toggle pull-right" type="button" data-toggle="dropdown" >Export
				<span class="caret" ></span></button>
				<ul class="dropdown-menu pull-right">
					<li><a href="{{ url('master/customers/xls?filter='.$filter.'&points='.$points.'&eligibility='.$eligibility.'&search='.$searchkey.'&type=false' ) }}">Excel</a></li>
					<li><a href="{{ url('master/customers/pdf?filter='.$filter.'&points='.$points.'&eligibility='.$eligibility.'&search='.$searchkey.'&type=false' ) }}">PDF</a></li>				
				</ul>				
			</div>	
		</div>
		<!-- Button div end -->

		<div class = "row" v-show="filter">
			<div class="col-md-12 margin-bottom">
				{{-- Points Filter Form start --}}
				@if (!isset($editCustomer))
				<form method="get" action="{{ url('master/customer')}}">
					<div class="col-md-12 form-inline margin-bottom no-h-padding">
						<div class="form-group">
							<label for="">Points:</label>
							<select name="filter" class="form-control input-sm">
								<option value="=" @if(isset($filter) && $filter == '=') {{'selected'}} @endif> =</option>
								<option value=">" @if(isset($filter) && $filter == '>') {{'selected'}} @endif > ></option>
								<option value=">=" @if(isset($filter) && $filter == '>=') {{'selected'}} @endif > >= </option>
								<option value="<" @if(isset($filter) && $filter == '<') {{'selected'}} @endif > <</option>
								<option value="<=" @if(isset($filter) && $filter == '<=') {{'selected'}} @endif><=</option>
							</select>	
						</div>
						<div class="form-group">
							<input type="text" name="points" class="form-control  input-sm" placeholder="@lang('glob.points')" value="{{{ Form::getValueAttribute('points', null) }}}">
						</div>
						<div class="form-group">
							<label for="">Eligibility:</label>
							<select name="eligibility" class="form-control input-sm" >
								<option value="">All</option>
								<option value="1" @if(isset($eligibility) && $eligibility == '1') {{'selected'}} @endif>Eligible</option>
								<option value="0" @if(isset($eligibility) && $eligibility == '0') {{'selected'}} @endif>Not Eligible</option>
							</select>	
						</div>
						<div class="form-group">
							<button class="btn bg-purple btn-sm">Filter Result</button>
						</div>
					</div>
					
					<div class="pull-right form-inline">

						<label for="search">Search :</label> 
						<input type="text" name="searchkey" value="{{ $searchkey }}" class="form-control  input-sm seatch-item" placeholder="search" >

					</div> 
					

				</form>
				@endif
				{{-- Points Filter form end --}}
			</div>
		</div>

		<div id="customerFormDiv" class="collapse @if ( session('open') || isset($open) || isset($editCustomer) || !$errors->isEmpty()) in @endif">
			<!-- form action for store -->

			@if (isset($editCustomer)) 

			{{ Form::model($editCustomer, ['url'=>array('master/customer',$editCustomer->id), 'method'=>'PUT','id'=>'customerForm']) }}
			@else 

			{{ Form::open(['action'=>'CustomersController@store','id'=>'customerForm']) }}
			@endif

			<div class="row customer-box">
				<div class="col-md-12">
					<div class="box box-primary">
						<div class="box-body">

							<div class="row">
								<!-- Customer ID -->
								<div class="col-md-3">
									<label for="">@lang('customer.customer_id'): 
									</label>
									@if (isset($editCustomer->id)) {{ $editCustomer->id }} 
									@elseif(isset($intNewId)) {{$intNewId}}
									@else 1 
									@endif
									<span> </span>
								</div>
							</div>
							<!-- row end -->

							<div class="row">

								<!-- Title -->
								<div class="col-md-2">
									<div class="form-group">
										<label for="">@lang('glob.title')</label>
										<div class="input-group">
											<div class="input-group-addon"><i class="ion ion-person"></i></div>
											<select name="title" id="title" class="form-control input-sm select2" placeholder="@lang('glob.title')" value="@if(isset($editCustomer)) {{ $editCustomer->title }} @endif" >
												<option value="">Title</option>
												<option value="1" @if(isset($editCustomer) && $editCustomer->title == 1) {{ 'selected' }} @endif>@lang('glob.mr')</option>
												<option value="2" @if(isset($editCustomer) && $editCustomer->title == 2) {{ 'selected' }} @endif>@lang('glob.ms')</option>
												<option value="3" @if(isset($editCustomer) && $editCustomer->title == 3) {{ 'selected' }} @endif>@lang('glob.mrs')</option>
											</select>
										</div>
									</div>
								</div>
								<!-- End title -->

								<!-- customer name -->
								<div class="col-md-5">
									<div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
										<label for="">@lang('glob.name')<span style="color:red">*</span></label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="ion ion-person"></i>
											</div>
											<input type="text" name="name" class="form-control  input-sm" placeholder="@lang('glob.name')" value="{{{ Form::getValueAttribute('name', null) }}}" required="">
										</div>
										@if ($errors->has('name'))
										<span class="help-block">
											<strong>{{ $errors->first('name') }}</strong>
										</span>
										@endif
									</div>
								</div>
								<!-- End Title -->

								<!-- spouse -->
								<div class="col-md-5">
									<div class="form-group">
										<label for="">@lang('customer.spouse')</label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="ion ion-ios-person"></i>
											</div>
											<input type="text" name="spouse" class="form-control  input-sm" placeholder="@lang('customer.spouse')" value="{{{ Form::getValueAttribute('spouse', null) }}}">
										</div>
									</div>
								</div>
								<!-- end spouse -->


							</div>
							<!-- row end-->

							<div class="row">

								<!-- Gender -->
								<div class="col-md-2">
									<div class="form-group">
										<label for="">@lang('glob.gender')</label>
										<div class="input-group">
											<div class="input-group-addon"><i class="ion ion-person"></i></div>
											<select name="gender" id="gender" class="form-control input-sm select2" placeholder="@lang('glob.gender')" value="@if(isset($editCustomer)) {{ $editCustomer->gender }} @endif" >
												<option value="0">@lang('glob.gender')</option>
												<option value="1" @if(isset($editCustomer) && $editCustomer->gender == 1) {{ 'selected' }} @endif>@lang('glob.male')</option>
												<option value="2" @if(isset($editCustomer) && $editCustomer->gender == 2) {{ 'selected' }} @endif>@lang('glob.female')</option>
											</select>
										</div>
									</div>
								</div>
								<!-- End Gender -->

								<!-- Prefered Mode -->
								<div class="col-md-2">
									<div class="form-group">
										<label for="">@lang('glob.age_group')</label>
										<div class="input-group">
											<div class="input-group-addon"><i class="ion ion-person"></i></div>
											<select name="age_group" id="age_group" class="form-control input-sm select2" placeholder="@lang('glob.age_group')" value="@if(isset($editCustomer)) {{ $editCustomer->age_group }} @endif" >
												<option value="0">@lang('glob.age_group')</option>
												<option value="1" @if(isset($editCustomer) && $editCustomer->age_group == 1) {{ 'selected' }} @endif>18 to 29</option>
												<option value="2" @if(isset($editCustomer) && $editCustomer->age_group == 2) {{ 'selected' }} @endif>30 to 36</option>
												<option value="3" @if(isset($editCustomer) && $editCustomer->age_group == 3) {{ 'selected' }} @endif>37 to 45</option>
												<option value="4" @if(isset($editCustomer) && $editCustomer->age_group == 4) {{ 'selected' }} @endif>46 to 55</option>
												<option value="5" @if(isset($editCustomer) && $editCustomer->age_group == 5) {{ 'selected' }} @endif>56+</option>
											</select>
										</div>
									</div>
								</div>
								<!-- End Prefered Mode -->

								<!-- Prefered Mode -->
								<div class="col-md-3">
									<div class="form-group">
										<label for="">@lang('glob.prefered_mode')</label>
										<div class="input-group">
											<div class="input-group-addon"><i class="ion ion-person"></i></div>
											<select name="prefered_mode" id="prefered_mode" class="form-control input-sm select2" placeholder="@lang('glob.prefered_mode')" value="@if(isset($editCustomer)) {{ $editCustomer->prefered_mode }} @endif" >
												<option value="0">@lang('glob.prefered_mode')</option>
												<option value="1" @if(isset($editCustomer) && $editCustomer->prefered_mode == 1) {{ 'selected' }} @endif>@lang('glob.email')</option>
												<option value="2" @if(isset($editCustomer) && $editCustomer->prefered_mode == 2) {{ 'selected' }} @endif>@lang('glob.phone')</option>
												<option value="3" @if(isset($editCustomer) && $editCustomer->prefered_mode == 3) {{ 'selected' }} @endif>@lang('glob.sms')</option>
												<option value="4" @if(isset($editCustomer) && $editCustomer->prefered_mode == 4) {{ 'selected' }} @endif>@lang('glob.whatsapp')</option>
											</select>
										</div>
									</div>
								</div>
								<!-- End Prefered Mode -->

								<!-- Prefered Language -->

								<div class="col-md-3">
									<div class="form-group">
										<label for="">@lang('glob.prefered_language')</label>
										<div class="input-group">
											<div class="input-group-addon"><i class="ion ion-person"></i></div>
											<select name="prefered_language" id="prefered_language" class="form-control input-sm select2" placeholder="@lang('glob.prefered_language')" value="@if(isset($editCustomer)) {{ $editCustomer->prefered_language }} @endif" >
												<option value="0">@lang('glob.prefered_language')</option>
												<option value="1" @if(isset($editCustomer) && $editCustomer->prefered_language == 1) {{ 'selected' }} @endif>@lang('glob.english')</option>
												<option value="2" @if(isset($editCustomer) && $editCustomer->prefered_language == 2) {{ 'selected' }} @endif>@lang('glob.arabic')</option>
												
											</select>
										</div>
									</div>
								</div>
								<!-- End of Prefered Language -->

							</div>
							<!-- row end-->

							<div class="row">

								<!-- birthday -->
								<div class="col-md-3">
									<div class="form-group">
										<label for="">@lang('customer.birthday')</label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="ion ion-calendar"></i>
											</div>
											<input type="text" name="birthday" class="form-control  input-sm datepicker" placeholder="@lang('customer.birthday')" value="@if(isset($editCustomer)) @if($editCustomer->birthday!=null || $editCustomer->birthday!='') {{ $editCustomer->birthday->format('m/d/Y') }} @endif @endif" required="">
										</div>
									</div>
								</div>

								<!-- annivarsary -->
								<div class="col-md-3">
									<div class="form-group">
										<label for="">@lang('customer.anniversary')</label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="ion ion-calendar"></i>
											</div>
											<input type="text" name="anniversary"  class="form-control  input-sm datepicker" placeholder="@lang('customer.anniversary')" value="@if(isset($editCustomer)) @if($editCustomer->anniversary!=null || $editCustomer->anniversary!='') {{ $editCustomer->anniversary->format('m/d/Y') }} @endif @endif">
										</div>
									</div>
								</div>

								{{-- Phone --}}
								<div class="col-md-3">
									<div class="form-group {{ $errors->has('phone') ? ' has-error' : '' }}">
										<label for="">@lang('glob.phone')</label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="ion ion-ios-telephone"></i>
											</div>
											<input type="text" name="phone" class="form-control  input-sm phone" placeholder="@lang('glob.phone')" value="{{{ Form::getValueAttribute('phone', null) }}}">
										</div>

										@if ($errors->has('phone'))
										<span class="help-block">
											<strong>{{ $errors->first('phone') }}</strong>
										</span>
										@endif

									</div>
								</div>

								{{-- Mobile --}}
								<div class="col-md-3">
									<div class="form-group {{ $errors->has('mobile') ? ' has-error' : '' }}">
										<label for="">@lang('glob.mobile') <span style="color:red">*</span></label>
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

							</div>

							<div class="row">

								{{-- Address --}}
								<div class="col-md-6">
									<div class="form-group {{ $errors->has('address') ? ' has-error' : '' }}">
										<label for="">@lang('glob.comment') </label>
										<textarea name="address" id=""   rows="5" class="form-control  input-sm fix-textarea" placeholder="@lang('glob.comment')">{{{ Form::getValueAttribute('address', null) }}}</textarea>
										@if ($errors->has('address'))
										<span class="help-block">
											<strong>{{ $errors->first('address') }}</strong>
										</span>
										@endif
									</div>
								</div>

								<div class="col-md-6 no-margin no-padding">

									{{-- Fax --}}
									<div class="col-md-6 no-margin">
										<div class="form-group">
											<label for="">@lang('glob.fax')</label>
											<div class="input-group">
												<div class="input-group-addon">
													<i class="fa fa-fax" aria-hidden="true"></i>
												</div>
												<input type="text" name="fax" maxlength="15" class="form-control  input-sm" placeholder="@lang('glob.fax')" value="{{{ Form::getValueAttribute('fax', null) }}}">
											</div>
										</div> 
									</div>

									{{-- Email --}}
									<div class="col-md-6">
										<div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
											<label for="">@lang('glob.email')</label>
											<div class="input-group">
												<div class="input-group-addon">
													<i class="ion ion-at"></i>
												</div>
												<input type="email" name="email" class="form-control  input-sm" placeholder="@lang('glob.email')" value="{{{ Form::getValueAttribute('email', null) }}}">
											</div>

											@if ($errors->has('email'))
											<span class="help-block">
												<strong>{{ $errors->first('email') }}</strong>
											</span>
											@endif

										</div>
									</div>

									{{-- lcoation --}}
									<div class="col-md-6">
										<div class="form-group">
											<label for="">@lang('glob.city_state')</label>
											<div class="input-group">
												<div class="input-group-addon">
													<i class="ion ion-location"></i>
												</div>
												<input type="text" name="city" class="form-control  input-sm" placeholder="@lang('glob.city_state')" value="{{{ Form::getValueAttribute('city', null) }}}" >
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="">@lang('glob.nationality')</label>
											<div class="input-group">
												<div class="input-group-addon">
													<i class="ion ion-location"></i>
												</div>
												<select name="nationality" class="form-control input-sm select2">
												<option value="0" >Nationality</option>
												@foreach($nationality as $c)
												@if(isset($editCustomer) && $editCustomer->id!=$c->id)
												<option value="{{ $c->id }}"  @if(isset($editCustomer) && $editCustomer->nationality==$c->id ) selected  @endif>{{ $c->code2 }}</option>
												@elseif(!isset($editCustomer) )
												<option value="{{ $c->id }}"  @if(isset($editCustomer) && $editCustomer->nationality==$c->id ) selected  @endif>{{ $c->code2 }}</option>
												@endif
												@endforeach
											</select>
											</div>
										</div>
									</div>
								</div>
								{{-- .Col-md-6 end --}}
							</div>
							{{-- .row --}}

							<div class="row">
								<div class="col-md-3">
								<div class="form-group">
											<label for="">@lang('glob.countryOfResidence')</label>
											<div class="input-group">
												<!-- <div class="input-group-addon">
													<i class="fa fa-home"></i>
												</div> -->
												<select name="countryOfResidence" class="form-control input-sm select2">
												<option value="0" >countryOfResidence</option>
												@foreach($countryOfResidence as $c)
												@if(isset($editCustomer) && $editCustomer->id!=$c->id)
												<option value="{{ $c->id }}"  @if(isset($editCustomer) && $editCustomer->countryOfResidence==$c->id ) selected  @endif>{{ $c->code2 }}</option>
												@elseif(!isset($editCustomer) )
												<option value="{{ $c->id }}"  @if(isset($editCustomer) && $editCustomer->countryOfResidence==$c->id ) selected  @endif>{{ $c->code2 }}</option>
												@endif
												@endforeach
											</select>
											</div>
										</div>
								</div>
									
									<div class="col-md-3">
									<div class="form-group">
											<label for="">@lang('glob.documentType')</label>
											<div class="input-group">
												
												<input type="text" name="type" class="form-control  input-sm" placeholder="@lang('glob.documentType')" value="{{{ Form::getValueAttribute('type', null) }}}" >
											</div>
										</div>
									</div>
									
									<div class="col-md-3">
									<div class="form-group">
											<label for="">@lang('glob.issuedBy')</label>
											<div class="input-group">
												<div class="input-group-addon">
													<i class="ion ion-location"></i>
												</div>
												<input type="text" name="issuedBy" class="form-control  input-sm" placeholder="@lang('glob.issuedBy')" value="{{{ Form::getValueAttribute('issuedBy', null) }}}" >
											</div>
										</div>
									</div>
									<div class="col-md-3">
									<div class="form-group">
											<label for="">@lang('glob.passportnumber')</label>
											<div class="input-group">
												<div class="input-group-addon">
													<i class="ion ion-location"></i>
												</div>
												<input type="text" name="number" class="form-control  input-sm" placeholder="@lang('glob.passportnumber')" value="{{{ Form::getValueAttribute('number', null) }}}" >
											</div>
										</div>
									</div>
							</div>
						</div>
						<!-- /.box-body -->

						<div class="box-footer">
							<div class="form-group">
								<button type="submit" class="btn btn-primary " v-on:click="checkValidateLoader($event)">
									<i class="fa fa-floppy-o"></i>
									@if (isset($editCustomer)) @lang('glob.update') @else @lang('glob.save') @endif
								</button>
								<a  class="btn btn-cyan" v-on:click="resetForm"><i class="fa fa-refresh"></i> @lang('glob.reset')</a>
								<a  href="{{ url('master/customer') }}"  class="btn btn-danger add-customer" data-toggle="collapse" data-target="#customerFormDiv" v-on:click="cancelForm">@lang('glob.cancel')</a>
							</div>
						</div>
						<!-- box-footer -->
					</div>
					<!-- /.box -->
				</div>
				{{-- .col-md-12 --}}
			</div>
			{{-- .row.customer-box --}}

			{{ Form::close() }}
			<!-- end form -->
		</div>
		<!-- add/update customer div end -->

		@endif
		<!-- view table data  -->
		<div class="row" >
			<div class="col-md-12 table-responsive  no-search">



				<table class="table table-bordered table-striped datatable-paging-false" >

					<thead>
						<tr>
							<th style="width:2%">#</th>
							<th>@lang('glob.name')</th>
							<th>@lang('glob.gender')</th>
							<th>@lang('glob.age_group')</th>
							<th>@lang('glob.prefered_mode')</th>
							<th>@lang('glob.prefered_language')</th>
							<th>@lang('glob.mobile')</th>
							<th>@lang('glob.city')</th>
							<th>@lang('glob.nationality')</th>
							<th>@lang('glob.documentType')</th>
							<th>@lang('glob.passportnumber')</th>
							<th>@lang('glob.eligibility')</th>
							<th>@lang('glob.points')</th>

							@if(Common::can_update('customer') || Common::can_delete('customer'))

							<th style="width:4%">@lang('customer.actions')</th>

							@endif

						</tr>
					</thead>

					<tbody>

						@foreach($customers as $row)

						<tr>
							<td>{{$row->id}}</td>
							<td>@if($row->eligibility == 1)<i class="fa fa-shield icon-large text-green" aria-hidden="true"></i>@endif {{$row->name}}</td>
							<td>
								@if($row->gender == 1) @lang('glob.male') 
								@elseif($row->gender == 2) @lang('glob.female')
								@else N/A 
								@endif
							</td>
							<td>
								@if($row->age_group == 1) 18 to 29 
								@elseif($row->age_group == 2) 30 to 36
								@elseif($row->age_group == 3) 37 to 45
								@elseif($row->age_group == 4) 46 to 55 
								@elseif($row->age_group == 5) 56+
								@else N/A  
								@endif
							</td>
							<td>
								@if($row->prefered_mode == 1) @lang('glob.email')
								@elseif($row->prefered_mode == 2) @lang('glob.phone')
								@elseif($row->prefered_mode == 3) @lang('glob.sms')
								@elseif($row->prefered_mode == 4) @lang('glob.whatsapp') 						
								@else N/A 		
								@endif
							</td>
							<td>
								@if($row->prefered_language == 1)
								@lang('glob.english')
								@elseif($row->prefered_language == 2)
								@lang('glob.arabic')
								@else N/A
								@endif
							</td>						
							<td>{{$row->mobile}}</td>
							<td>{{$row->city}}</td>
							<td>{{ (isset($row->countryCode->code2)) ? $row->countryCode->code2 : '' }}</td>
							<td>{{$row->type}}</td>
							<td>{{$row->number}}</td>
							<td>@if($row->eligibility == 0) {{'Not Eligible'}} @else {{'Eligible'}} @endif</td>
							<td>{{Common::fixIt($row->points)}}</td>
							@if(Common::can_update('customer') ||  Common::can_delete('customer'))
							<td>

								@if(Common::can_update('customer'))

								<a href="{{route('customer.edit',$row->id)}}" v-on:click="getLoader($event)" class="btn btn-xs btn-info btn-circle" ><i class="fa fa-pencil" aria-hidden="true"></i></a>

								@endif
								@if(Common::can_delete('customer'))

								<a v-on:click="deleteCustomer({{ $row->id }}, $event)"  class="btn btn-xs btn-danger btn-circle"><i class="fa fa-trash" aria-hidden="true"></i></a> 

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
							<th class="search">@lang('glob.gender')</th>
							<th class="search">@lang('glob.age_group')</th>
							<th class="search">@lang('glob.prefered_mode')</th>
							<th class="search">@lang('glob.prefered_language')</th>
							<th class="search">@lang('glob.mobile')</th>
							<th class="search">@lang('glob.city')</th>
							<th class="search">@lang('glob.nationality')</th>
							<th class="search">@lang('glob.documentType')</th>
							<th class="search">@lang('glob.passportnumber')</th>
							<th class="search">@lang('glob.eligibility')</th>
							<th class="search">@lang('glob.points')</th>

							@if(Common::can_update('customer') || Common::can_delete('customer'))

							<th style="width:4%">@lang('customer.actions')</th>

							@endif

						</tr>
					</tfoot>

				</table>

				<div class="pull-right">
					{{ $customers->appends(['searchkey' => $searchkey ])->links() }}
				</div>

			</div>
			{{-- .col-md-12 --}}
		</div>
		<!-- view table data end -->

		
	</div>
	<!-- end #customer div -->
	@endsection
