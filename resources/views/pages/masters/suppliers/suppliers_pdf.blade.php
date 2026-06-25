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
			<label style="font-style: italic; font-weight: bold; font-size:20px;">Supplier Details</label>
			<br/>		
			<br/>		
		</center>
	</div>
	

	<!-- Supplier Details Table -->
	<div class="table-responsive" style="width: 100%;">
		<table class="table table-bordered" style="width: 100%;">
			<thead>
				<tr>
					<th >@lang('glob.id')</th>
					<th class="search">@lang('glob.name')</th>
					<th class="search">@lang('supplier.s_type')</th>
					<th class="search">@lang('glob.phone')</th>
					<th class="search">@lang('glob.city')</th>
				</tr>
			</thead>
			<tbody >
				@foreach($data as $row)
				<tr>
					<td>{{$row->id}}</td>
					<td>{{$row->name}}</td>
					<td>{{$row->type}}</td>
					<td>{{$row->phone}}</td>
					<td>{{$row->city}}</td> 								
				</tr>
				@endforeach	


			</tbody>
		</table>
	</div>
	<!-- Supplier Details Table End -->	
	<br/>	
	
</body>
</html>