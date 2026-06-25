@extends('layouts.app')

@section('page-title')@lang('glob.anniversary')@endsection


@section ('breadcrumb')

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> @lang('glob.dashboard')</a></li>
	<li class="active">@lang('glob.anniversary')</li>
</ol>

@endsection

@section('content')
<div class="anniversary" v-cloak>
<div class = "row margin-bottom" >
		{{-- Points Filter Form start --}}
		<form method="get" action="{{ url('reports/anniversary')}}">
			<div class="col-md-6 form-inline ">
				<div class="form-group">
					<label for="">@lang('glob.filter'):</label>
					<select name="filter" class="form-control input-sm" onchange="this.form.submit()">
						<option value="1" @if( $filter == '1') {{'selected'}} @endif>Today</option>
						<option value="2" @if( $filter == '2') {{'selected'}} @endif>All</option>
					</select>	
				</div>
			</div>
			<div class="col-md-6  form-inline">
				<div class="pull-right">
					<label for="search">Search :</label> 
					<input type="text" name="searchkey" value="{{ $searchkey }}" class="form-control  input-sm seatch-item" placeholder="search" >
				</div>
			</div> 
		</form>
		{{-- Points Filter form end --}}
	</div>

	<!-- filter for today/all -->
	{{--<div class="row">
		<div class="col-md-2 margin-bottom">
			{!! Form::label('title', trans('glob.filter')) !!}			
			<div class="input-group">
				<div class="input-group-addon">
					<i class="ion ion-calendar"></i>
				</div>
				{!!Form::select('anniversaryFilter', array('1' => trans('All'), '2' => trans('Today'),), '1', array('id'=>'bFilter','required', 'class'=>'form-control input-sm','v-model'=>'filter'))!!}

			</div>
		</div>
	</div>--}}
	<!--End filter for today/all -->


	<!-- view table data  -->
	<div class="row">
		<div class="col-md-12 table-responsive">
			<table class="table table-bordered table-striped" id="anniversaryTbl" >
				<thead>
					<tr>
						<th style="width:2%">#</th>	
						<th > <input type="checkbox" id='selectAllCheckbox' value="all" checked="checked" @click="selectAll"  ></th>					
						<th>@lang('glob.customer')</th>
						<th>@lang('glob.anniversary')</th>
						<th >@lang('glob.email')</th>
						<th >@lang('glob.mobile')</th>
					</tr>
				</thead>
				<tbody>

					@foreach($anniversary as $row)

					<tr >
						<td>{{$row->id}}</td>
						<td><input type="checkbox" name="ch[]" id ="{{$row->id}}" value="{{$row->email}}" class = "selectCheckbox"></td> 
						<td>{{$row->name}}</td>
						<td>{{$row->anniversary->format($date_format)}}</td>
						<td class="td-email">{{$row->email}}</td>
						<td>{{$row->mobile}}</td>
					</tr>

					@endforeach


				</tbody>
				<tfoot class="showSearch">
					<tr>
						<th  style="width:2%">#</th>	
						<th> </th>					
						<th class="search">@lang('glob.customer')</th>						
						<th class="search">@lang('glob.anniversary')</th>
						<th class="search" >@lang('glob.email')</th>
						<th class="search" >@lang('glob.mobile')</th>
					</tr>
				</tfoot>

			</table>
			<div class="pull-right">
			{{ $anniversary->appends(['searchkey' => $searchkey, 'filter' => $filter ])->links() }}
		</div>

		</div>
		{{-- .col-md-12 --}}
	</div>
	<!-- view table data end -->

	<!-- Send Messsage Subject -->
	<div class="row">
		<div class="col-md-8">
			<div class="form-group">
				<label>@lang('glob.subject')<span style="color:red">*</span></label>
				
				<input type="text" name="subject" id ="subject" class = "form-control input-sm" placeholder=" @lang('glob.subject')" required="">
				
			</div>
		</div>
		<div class="col-md-4"></div>

	</div>



	<!-- Send Messsage -->
	<div class="row" style="margin-top: 5px;">	
		<div class="col-md-8">
			<div class="">
				<label> @lang('glob.message')<span style="color:red">*</span></label>
				
				<textarea name="message" id ="message"  class = "form-control input-sm fix-textarea textarea"placeholder=" @lang('glob.message')"  rows="4"></textarea>	
				
			</div>
		</div>

		<div class="col-md-4"></div>
	</div>

	<div class="row" style="margin-top: 10px;">
		<div class="col-md-4 col-sm-8">			
			<a v-on:click="sendEmail($event)" class="btn btn-success text-left btn-circle" ><i class="fa fa-envelope" aria-hidden="true" ></i> @lang('glob.sendmail')</a>	
			<a  v-on:click="sendSms($event)" class="btn btn-success text-left btn-circle" ><i class="fa fa-comment" aria-hidden="true"></i> @lang('glob.send_message')</a>		
		</div>
	</div>
	
</div>	


<!-- End Send Messsage -->


@endsection