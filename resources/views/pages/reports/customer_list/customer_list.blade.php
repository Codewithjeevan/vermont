@extends('layouts.app')

@section('page-title')@lang('glob.customer_list')@endsection


@section ('breadcrumb')

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> @lang('glob.dashboard')</a></li>
	<li class="active">@lang('glob.customer_list')</li>
</ol>

@endsection

@section('content')
<div class="customer">
	<div class = "row" v-show="filter">
		<div class="col-md-12 margin-bottom">
			{{-- Points Filter Form start --}}
			<form method="get" action="{{ url('reports/customer_list')}}">
				<div class="col-md-12 form-inline margin-bottom no-h-padding">
					<div class="form-group">
						<label for="">Points:</label>
						<select name="filter" class="form-control input-sm">
							<option value="=" @if(isset($filter) && $filter == '=') {{'selected'}} @endif> =</option>
							<option value=">" @if(isset($filter) && $filter == '>') {{'selected'}} @endif > ></option>
							<option value=">=" @if(isset($filter) && $filter == '>=') {{'selected'}} @endif > >= </option>
							<option value="<" @if(isset($filter) && $filter == '<') {{'selected'}} @endif > < </option>
							<option value="<=" @if(isset($filter) && $filter == '<=') {{'selected'}} @endif> <= </option>
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
					<div class="pull-right form-inline">

						<label for="search">Search :</label> 
						<input type="text" name="searchkey" value="{{ $searchkey }}" class="form-control  input-sm seatch-item" placeholder="search" >

					</div> 
				</div>
			</form>
			{{-- Points Filter form end --}}
		</div>
	</div>

	<!-- view table data  -->
	<div class="row">
		<div class="col-md-12 table-responsive no-search">

			<table class="table table-bordered table-striped datatable-paging-false ">

				<thead>
					<tr>
						<th style="width:2%">#</th>
						<th > <input type="checkbox" id='selectAllCheckbox' value="all" checked="checked" @click="selectAll"  ></th>
						<th>@lang('glob.customer') @lang('glob.name')</th>
						<th>@lang('glob.gender')</th>
						<th>@lang('glob.age_group')</th>
						<th>@lang('glob.prefered_mode')</th>
						<th>@lang('glob.prefered_language')</th>
						<th>@lang('glob.comment')</th>
						<th>@lang('glob.mobile')</th>
						<th>@lang('glob.email')</th>
						<th>@lang('glob.city')</th>
						<th>@lang('glob.nationality')</th>
						<th>@lang('glob.documentType')</th>
						<th>@lang('glob.passportnumber')</th>
						<th>@lang('glob.eligibility')</th>
						<th>@lang('glob.points')</th>
						<th>@lang('glob.created_date')</th>
					</tr>
				</thead>

				<tbody>

					@foreach($customers as $row)
					<tr>
						<td>{{ $row->id }}</td>
						<td>
							<input type="checkbox" name="ch[]" id ="{{$row->id}}" value="{{$row->email}}" class = "selectCheckbox">
						</td> 
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
						@if($row->prefered_language == 1) @lang('glob.english')
						@elseif($row->prefered_language == 2) @lang('glob.arabic')
						@else N/A
						@endif
					</td>
						<td>{{ $row->address }}</td>
						<td>{{ $row->mobile }}</td>
						<td class="td-email">{{ $row->email }}</td>
						<td>{{ $row->city }}</td>
						<td>{{ (isset($row->countryCode->code2)) ? $row->countryCode->code2 : '' }}</td>
							<td>{{$row->type}}</td>
							<td>{{$row->number}}</td>
						<td>@if($row->eligibility == 0) {{'Not Eligible'}} @else {{'Eligible'}} @endif</td>
						<td>{{Common::fixIt($row->points)}}</td>
						<td>
							{{ $row->created_at->format('jS F, Y') }} <br>
							<small>
								<em> @lang("glob.updated_at") {{ $row->updated_at->diffForHumans() }}</em>
							</small>
						</td>
					</tr>
					@endforeach
				</tbody>

				<tfoot class="showSearch">
					<tr>
						<th style="width:2%">#</th>
						<th> </th>
						<th class="search">@lang('glob.customer') @lang('glob.name')</th>
						<th class="search">@lang('glob.gender')</th>
						<th class="search">@lang('glob.age_group')</th>
						<th class="search">@lang('glob.prefered_mode')</th>
						<th class="search">@lang('glob.prefered_language')</th>
						<th class="search">@lang('glob.comment')</th>
						<th class="search">@lang('glob.mobile')</th>
						<th class="search">@lang('glob.email')</th>
						<th class="search">@lang('glob.city')</th>
						<th class="search">@lang('glob.nationality')</th>
						<th class="search">@lang('glob.DocumentType')</th>
						<th class="search">@lang('glob.passportnumber')</th>
						<th class="search">@lang('glob.eligibility')</th>
						<th class="search">@lang('glob.points')</th>
						<th class="search">@lang('glob.created_date')</th>
					</tr>
				</tfoot>

			</table>
			<div class="pull-right">
				{{ $customers->appends(['searchkey' => $searchkey ])->links() }}
			</div>

		</div>
		
	</div>
	<!--Table End-->

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
				
				<textarea name="message" id ="message"  class = "form-control input-sm fix-textarea textarea"placeholder=" @lang('glob.message')"  rows="4"></textarea>	
				
			</div>
		</div>

		<div class="col-md-4"></div>
	</div>
	<!-- Send Messsage Body End-->
	<!--Buttons -->

	<div class="row" style="margin-top: 10px;">
		<div class="col-md-8 col-sm-8 ">
			
			<a v-on:click="sendEmail($event)" class="btn btn-success text-left btn-circle" ><i class="fa fa-envelope" aria-hidden="true"></i> @lang('glob.sendmail')</a>	
			<a  v-on:click="sendSms($event)" class="btn btn-success text-left btn-circle" ><i class="fa fa-comment" aria-hidden="true"></i> @lang('glob.send_message')</a>		
			<a  class="btn btn-success  text-left" href="{{ url('reports/customer_list/xls?filter='.$filter.'&points='.$points.'&eligibility='.$eligibility.'&search='.$searchkey.'&type=true' ) }}" ><i class="fa fa-file-excel-o" aria-hidden="true"></i> @lang('glob.export_to_xls')</a>	
			<a  class="btn btn-danger text-left" href="{{ url('reports/customer_list/pdf?filter='.$filter.'&points='.$points.'&eligibility='.$eligibility.'&search='.$searchkey.'&type=true' ) }}" ><i class="fa fa-file-pdf-o" aria-hidden="true"></i> @lang('glob.export_to_pdf') </a>	
		</div>
	</div>
	<!--Buttons End-->
	
</div>	

<!-- End Send Messsage -->

</div>
@endsection