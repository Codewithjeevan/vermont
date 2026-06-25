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
			<label style="font-style: italic; font-weight: bold; font-size:20px;">Opening Stock Details</label>
			<br/>		
			<br/>		
		</center>
	</div>
	

	<!-- Opening Stock Details Table -->
	<div class="table-responsive" style="width: 100%;">
		<table class="table table-bordered" style="width: 100%;">
			<thead>
				<tr>
					<th >#</th>
					<th>@lang('open_stock.item_id')</th>
						<th>@lang('open_stock.item_cat')</th> 
						<th>@lang('open_stock.item_name')</th>
						<th>@lang('open_stock.opening_stock')</th>
						<th>@lang('open_stock.unit')</th>
				</tr>
			</thead>
			<tbody >
				@foreach($data as $row)
				<tr>
					<td>{{ $row->id }}</td>
						<td>@if(isset($row->item)) {{ $row->item->id }}  @endif</td>
						<td>@if(isset($row->item->categories)) {{ $row->item->categories->categorie }} @endif</td>
						<td>@if(isset($row->item)) {{ $row->item->itemName }} @endif</td>
						<td>{{ $row->openingStock}} </td>
						<td>@if(isset($row->units->unit)) {{ $row->units->unit}} @endif</td>						
				</tr>
				@endforeach	


			</tbody>
		</table>
	</div>
	<!-- Opening Stock Details Table End -->	
	<br/>	
	
</body>
</html>