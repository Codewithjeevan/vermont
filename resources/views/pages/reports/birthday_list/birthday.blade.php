@extends('layouts.app')

@section('page-title')@lang('glob.birthday')@endsection


@section ('breadcrumb')

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> @lang('glob.dashboard')</a></li>
	<li class="active">@lang('glob.birthday')</li>
</ol>

@endsection

@section('content')

<div class="birthday" v-cloak>

	<div class = "row margin-bottom" >
		{{-- Points Filter Form start --}}
		<form method="get" action="{{url('reports/birthday')}}">
		<div class="col-md-12">
			<div class="col-md-4">
				<label class="form-group"><b>@lang('glob.from'):<span style="color:red">*</span></b></label>
			</div>
			<div class="col-md-4">
				<label class="form-group"><b>@lang('glob.to'):<span style="color:red">*</span></b></label>
			</div>
		</div>
		<div class="col-md-2">
				<div class="form-group">
					<label>@lang('customer.day'):<span style="color:red">*</span></label>
					<select name="date1" class="form-control" type="text" required="">
						@foreach($day as $d)
						<option value="{{$d}}" @if($date1==$d) {{'selected'}} @endif>{{$d}}</option>
						@endforeach
					</select>	
				</div>
			</div>
			<div class="col-md-2">
				<div class="form-group">
					<label>@lang('customer.month'):<span style="color:red">*</span></label>
					<select name="month1" class="form-control" type="text" required="">
					@foreach($month as $key=>$m)
					<option value="{{$key}}"  @if($month1==$key) {{'selected'}} @endif>{{$m}}</option>
					@endforeach
					</select>
				</div>
			</div>
			<div class="col-md-2">
				</label>
				<div class="form-group">
					<label>@lang('customer.day'):<span style="color:red">*</span></label>
					<select name="date2" class="form-control" type="text" required="">
					@foreach($day as $dd)
						<option value="{{$dd}}" @if($date2==$dd) {{'selected'}} @endif>{{$dd}}</option>
					@endforeach
					</select>	
				</div>
			</div>
			<div class="col-md-2">
				<div class="form-group">
					<label>@lang('customer.month'):<span style="color:red">*</span></label>
					<select name="month2" class="form-control" type="text" required="">
					@foreach($month as $key1=>$mm)
					<option value="{{$key1}}"  @if($month2==$key1) {{'selected'}} @endif>{{$mm}}</option>
					@endforeach
					</select>	
				</div>
			</div>
			<div class="col-md-2" style="margin-top: 2%;">
				<button type="submit"  class="btn btn-success btn-circle" > @lang('glob.show')</button>
			</div>
			<div class="col-md-12  form-inline">
			    <div class="pull-right">
					<label for="search">Search :</label> 
						<input type="text" name="searchkey" value="{{ $searchkey }}" class="form-control  input-sm seatch-item" placeholder="search" >
				</div>
			</div>
		</form>
	
		{{-- Points Filter form end --}}
	</div>


	{{--<div class="row margin-bottom">


	<div class="col-md-2 ">
		{!! Form::label('title', trans('glob.filter')) !!}
		<div class="input-group">
			<div class="input-group-addon">
				<i class="ion ion-calendar"></i>
			</div>
			{!!Form::select('birtdayFilter', array('1' => trans('All'), '2' => trans('Today'),), '1', array('id'=>'bFilter','required', 'class'=>'form-control input-sm','v-model'=>'filter'))!!}
		</div>
	</div>


</div>--}}


<!-- view table data  -->


<div class="row">
	<div class="col-md-12 table-responsive no-search">
		<table class="table table-bordered table-striped datatable-paging-false" id="birthdayTbl">
			<thead>
				<tr>
					<th style="width:2%">#</th>
					<th > <input type="checkbox" id='selectAllCheckbox' value="all" checked="checked"  @click="selectAll" ></th>
					<th>@lang('glob.customer')</th>
					<th>@lang('glob.birthday')</th>
					<th >@lang('glob.email')</th>
					<th >@lang('glob.mobile')</th>
				</tr>
			</thead>
			<tbody>

				@foreach($birthdays as $row)

				<tr>
					<td>{{$row->id}}</td>
					<td><input type="checkbox" name="ch[]" id ="{{$row->id}}" value="{{$row->email}}" class = "selectCheckbox"></td>
					<td>{{$row->name}}</td>
					<td>{{$row->birthday->format($date_format)}}</td>
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
					<th class="search">@lang('glob.birthday')</th>
					<th class="search" >@lang('glob.email')</th>
					<th class="search" >@lang('glob.mobile')</th>
				</tr>
			</tfoot>

		</table>
		<div class="pull-right">
			{{ $birthdays->appends(['searchkey' => $searchkey ])->links() }}
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
<!-- Send Messsage Subject End-->


<!-- Send Messsage Body-->
<div class="row" style="margin-top: 5px;">	
	<div class="col-md-8">
		<div class="">
			<label> @lang('glob.message')<span style="color:red">*</span></label>

			<textarea name="message" id ="message"  class = "form-control input-sm fix-textarea textarea" placeholder=" @lang('glob.message')"  rows="4"></textarea>	

		</div>
	</div>

	<div class="col-md-4"></div>
</div>
<!-- Send Messsage Body End-->

<!--Send Message Buttons-->
<div class="row" style="margin-top: 10px;">
	<div class="col-md-4 col-sm-8 ">

		<a v-on:click="sendEmail($event)" class="btn btn-success text-left btn-circle" ><i class="fa fa-envelope" aria-hidden="true"></i> @lang('glob.sendmail')</a>	
		<a  v-on:click="sendSms($event)" class="btn btn-success text-left btn-circle" ><i class="fa fa-comment" aria-hidden="true"></i> @lang('glob.send_message')</a>		
	</div>
</div>

<!--Send Message Buttons End-->

</div>	


</div>
@endsection