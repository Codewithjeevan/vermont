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
			<label style="font-style: italic; font-weight: bold; font-size:20px;">Expiry Item Report</label>
			<br/>		
			<br/>		
		</center>
	</div>
	

<!-- Customer Details Table -->
	<div class="table-responsive" style="width: 100%;">
		<table class="table table-bordered" style="width: 100%;">
			<thead>
				<tr>
					<th >#</th>
					<th >@lang('item.item_name')</th>
					<th >@lang('stock.mrn_no')</th>
					<th >@lang('glob.expiry_date')</th>
					
				</tr>
			</thead>
			<tbody >
				@foreach($expiry as $row)
				<tr>
					<td>{{$i++}}</td>
					<td>{{$row->itemName}}</td>	
					<td>{{$row->purchase_id}}</td>								
					<td>{{$row->expiryDate}}</td>														
				</tr>
				@endforeach	


			</tbody>
		</table>
	</div>
<!-- Customer Details Table End -->	
	<br/>	
	
</body>
</html>