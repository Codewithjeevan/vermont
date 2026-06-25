@extends('layouts.app')

@section('page-title')@lang('menu.opening_cash')@endsection

@section('page-subtitle')@lang('glob.add_edit_opning_cash')@endsection

@section ('breadcrumb')

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> @lang('glob.dashboard')</a></li>
	<li class="active">@lang('menu.opening_cash')</li>
</ol>

@endsection

@section('content')
<div class="opening_cash"> 
	@if(Common::can_create('opening_cash'))
	

	@if (isset($editOpenCash)) 
	{{ Form::model($editOpenCash, ['url'=>array('opening_cash/opening_cash',$editOpenCash->id),'method'=>'PUT','id'=>'opencashForm']) }}
	@else 
	{{ Form::open(['action'=>'OpeningCashController@store','id'=>'opencashForm']) }}
	@endif

	<div class="row customer-box"> 

		<div class="col-md-12">

			<div class="box box-primary"> 

				<div class="box-body">

					<div class="row">

						<!-- open cash date -->
						<div class="col-md-3">								
							<div class="form-group {{ $errors->has('open_cash_date') ? ' has-error' : '' }}">
								<label for="">@lang('glob.opening_cash_date')<span style="color:red">*</span></label>
								<div class="input-group">
									<div class="input-group-addon">
										<i class="ion ion-calendar"></i>
									</div>
									<input id="open_cash_date" name="open_cash_date" type="text" class="form-control  input-sm datepicker required" value="@if(isset($editOpenCash)) {{ $editOpenCash->open_cash_date }}  @endif"   placeholder="@lang('glob.opening_cash_date')" required="">
								</div>
								@if ($errors->has('open_cash_date'))
								<span class="help-block">
									<strong>{{ $errors->first('open_cash_date') }}</strong>
								</span>
								@endif
							</div>
						</div>
						<!--End open cash date -->

						<!--  Opening cash Amt -->
						<div class="col-md-3">
							<div class="form-group {{ $errors->has('opening_amt') ? ' has-error' : '' }}">
								<label for="">@lang('glob.opening_amt')<span style="color:red">*</span></label>
								<div class="input-group">
									<div class="input-group-addon">
										<i class="fa fa-money"></i>
									</div>

									<input type="text" id="opening_amt" name="opening_amt"  class="form-control  input-sm " value="@if(isset($editOpenCash)){{$editOpenCash->opening_amt}}@endif"  required="">
								</div>
								@if ($errors->has('opening_amt'))
								<span class="help-block">
									<strong>{{ $errors->first('opening_amt') }}</strong>
								</span>
								@endif
							</div>
						</div>
						<!--End Opening cash Amt -->
						<!--User Name-->
						<div class="col-md-3">
							<label >@lang('glob.name')<span style="color:red">*</span></label>
							<div class="input-group">
								<div class="input-group-addon"><i class="ion ion-person"></i></div>
								<select name="user_id" id="user_id" class="form-control input-sm select2"  required>
									<option value=""></option>
									@foreach($users as $row)										
									<option value="{{$row->id}}" @if(isset($editOpenCash) &&  $editOpenCash->user_id ==$row->id ) {{ 'selected' }}  @endif >{{$row->email}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<!--End User Name-->

						<div class="col-md-3" style="margin-top: 2%;">
							<center>
								<a  type="button" class="btn btn-cyan"   v-on:click="reset"  ><i class="fa fa-refresh"></i> @lang('glob.reset')</a>
								<button type="submit" class="btn btn-primary " v-on:click="checkValidateLoader($event)"><i class="fa fa-floppy-o"></i>@if (isset($editOpenCash)) @lang('glob.update') @else @lang('glob.save') @endif	</button>
								<a type="button" class="btn btn-danger" @if(isset($editOpenCash)) href="/opening_cash/opening_cash" @else v-on:click="reset" @endif > @lang('glob.cancel')</a>
							</center>
						</div>

					</div>


				</div>				

			</div>
			<!-- .box .box-primary -->

		</div>
		<!-- .col-md-12 -->

	</div>
	<!--end .row .customer-box -->

	{{ Form::close() }}

	<!-- end form -->	
	

	@endif

	<!-- view table data  -->
	<div class="row">

		<div class="col-md-12 table-responsive">

			<table class="table table-bordered table-striped datatable1">
				<thead>
					<tr>
						<th style="width:10%">#</th>				
						<th>@lang('glob.name')</th>
						<th>@lang('glob.opening_amt')</th> 
						<th>@lang('glob.opening_cash_date')</th>
						
						@if(Common::can_update('opening_cash') || Common::can_delete('opening_cash'))

						<th style="width:10%">@lang('customer.actions')</th>

						@endif

					</tr>
				</thead>
				<tbody>

					@foreach($openCashList as $row) 

					<tr>
						<td>{{ $row->id }}</td>
						<td>@if(isset($row->user->email)) {{  $row->user->email }} @endif </td>
						<td>{{ Common::fixIt($row->opening_amt)   }}</td>						
						<td>{{ $row->open_cash_date->format($date_format) }} </td>						

						@if(Common::can_update('opening_cash') || Common::can_delete('opening_cash'))

						<td>

							@if(Common::can_update('opening_cash'))

							<a  href="{{route('opening_cash.edit',$row->id)}}" v-on:click="getLoader($event)"  class="btn btn-xs btn-info btn-circle" ><i class="fa fa-pencil" aria-hidden="true"></i></a>

							@endif
							@if(Common::can_delete('opening_cash'))

							<a  id='{{ $row->id }}' v-on:click="deleteOpenCash({{ $row->id }}, $event)" class="btn btn-xs btn-danger btn-circle"><i class="fa fa-trash" aria-hidden="true"></i></a> 


							@endif

						</td>

						@endif

					</tr>

					@endforeach

				</tbody>
				<tfoot class="showSearch">
					<tr>
						<th class="search" style="width:10%">#</th>	
						<th class="search" >@lang('glob.name')</th>					
						<th class="search" >@lang('glob.opening_amt')</th>						
						<th class="search" >@lang('glob.opening_cash_date')</th>

						@if(Common::can_update('opening_cash') || Common::can_delete('opening_cash'))

						<th class="search" style="width:10%">@lang('customer.actions')</th>

						@endif

					</tr>
				</tfoot>
			</table>

		</div>
	</div>
	
</div>



@endsection