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
			<label style="font-style: italic; font-weight: bold; font-size:20px;">User Details</label>
			<br/>		
			<br/>		
		</center>
	</div>
	

	<!-- User Details Table -->
	<div class="table-responsive" style="width: 100%;">
		<table class="table table-bordered" style="width: 100%;">
			<thead>
				<tr>
					<th >@lang('glob.id')</th>
					<th class="search">@lang('user.user_name')</th>							
					<th class="search">@lang('user.assign_floor')</th>
				</tr>
			</thead>
			<tbody >
				@foreach($data as $row)
				<tr>
					<td>{{$row->id}}</td>
					<td>{{ $row->email }}</td>					
					<td>{{ $row->floor }}</td>									
				</tr>
				@endforeach	


			</tbody>
		</table>
	</div>
	<!-- User Details Table End -->	
	<br/>	
	
</body>
</html>