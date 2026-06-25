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
			<label style="font-style: italic; font-weight: bold; font-size:20px;">Audit Report</label>
			<br/>		
			<br/>		
		</center>
	</div>
	

<!-- deleted bill Details Table -->
	<div class="table-responsive" style="width: 100%;">
		<table class="table table-bordered" style="width: 100%;">
		
			<thead>
				<tr>
					<th style="width:2%">#</th>
					<th>@lang('glob.record_type')</th>
					<th>@lang('glob.record_id')</th>
					<th>@lang('glob.user')</th>
					<th>@lang('glob.remark')</th>
					<th>@lang('glob.datetime')</th>
				</tr>
			</thead>

			<tbody>

				@foreach($arrAudits as $index=>$audit)
				<tr>
					<td>{{ $audit->id }}</td> 
					@if($audit->type == 1)
					<td>Item</td>
					@elseif($audit->type == 2)
					<td>Purchase Order</td>
					@elseif($audit->type == 3)
					<td>Purchase</td>
					@elseif($audit->type == 4)
					<td>Purchase Return</td>
					@elseif($audit->type == 5)
					<td>Expiry</td>
					@elseif($audit->type == 6)
					<td>Damage</td>
					@endif					
					<td>{{ $audit->record_id }}</td>					
					<td>{{ (isset($audit->_user->email) ? $audit->_user->email : '')  }}</td>
					<td>{{ $audit->remark}}</td>
					<td>{{ $audit->created_at->format($date_format) }}</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
<!-- deleted bill Details Table End -->	
	<br/>	
	
</body>
</html>