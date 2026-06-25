<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">    

	<style type="text/css">

	table, th, td {
		border: 1px solid black;
		border-collapse: collapse;
		font-size:10px;			
	}		
	.row {
		margin-right: 0px;
		margin-left: 0px;   
	}
	.labelfont{
		font-weight: bold;
		font-size:12px;
		float: left;
	}
	.tablefont{			
		font-size:8px;
		float: left;
	}

</style>    

</head>
<body>
	
	<div class="row">
		<center>				
			<label style="font-style: italic; font-weight: bold; font-size:20px;">Customer Report</label>
			<br/>		
			<br/>		
		</center>
	</div>
	

	<!-- Customer Details Table -->
	<div class="table-responsive" style="width: 100%;">
		<table class="table table-bordered" style="width: 100%;">
			<thead>
				<tr>
					<th >@lang('glob.id')</th>
					<th >@lang('glob.name')</th>
					<th >@lang('customer.spouse')</th>
					<th>@lang('glob.gender')</th>
					<th>@lang('glob.age_group')</th>
					<th>@lang('glob.prefered_mode')</th>					
					<th>@lang('glob.prefered_language')</th>					
					<th >@lang('glob.comment')</th>
					<th >@lang('glob.mobile')</th>
					<th >@lang('glob.email')</th>
					<th >@lang('glob.city')</th>
					<th>@lang('glob.nationality')</th>
					<th>@lang('glob.documentType')</th>
					<th>@lang('glob.passportnumber')</th>
					<th>@lang('glob.eligibility')</th>
					<th>@lang('glob.points')</th>
				</tr>
			</thead>
			<tbody >
				@foreach($customer as $row)
				<tr>
					<td>{{$row->id}}</td>
					<td>{{$row->name}}</td>								
					<td>{{$row->spouse}}</td>
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
					<td>
						@if($row->address == null) N/A @else{{$row->address}}
						@endif
					</td>		
					<td>{{$row->mobile}}</td>	
					
					<td>{{$row->email}}</td>	
					<td>{{$row->city}}</td>
					<td>@if($row->countryCode== null)  @else {{$row->countryCode->code2}} @endif</td>
					<td>{{$row->type}}</td>
					<td>{{$row->number}}</td>
					<td>
						@if($row->eligibility == 0) {{'Not Eligible'}} 
						@else {{'Eligible'}} 
						@endif
					</td>
					<td>{{Common::fixIt($row->points)}}</td>								
				</tr>
				@endforeach	


			</tbody>
		</table>
	</div>
	<!-- Customer Details Table End -->	
	<br/>	
	
</body>
</html>