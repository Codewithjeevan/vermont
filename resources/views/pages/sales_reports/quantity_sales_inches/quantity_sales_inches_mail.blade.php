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
			<label style="font-style: italic; font-weight: bold; font-size:20px;">Quantitywise Sales 3 Inches Details</label>
			<br/>		
			<br/>		
		</center>
	</div>	
	<br/>
	<div class="row"  style="width: 100%;">
		<div class="col-md-8">
			<label style="font-weight: bold;
			font-size:10px;">@lang('glob.from') : {{$fromDate}} - @lang('glob.to') : {{$toDate}}</label>
		</div>
	</div>
    

	<!-- Zsales Qty Details Table -->
	<div class="table-responsive" style="width: 100%;">
		<table class="table table-bordered" style="width: 100%;">
			<thead>
				<tr>
					<th style="width:10%">@lang('glob.id')</th>											
					<th>@lang('item.item_name')</th>
					<th>@lang('item.qty')</th>								
				</tr>
			</thead>
			<tbody >
				@foreach($purchases as $row)
				
				<tr>
					<td>{{{ $i++ }}}</td>			
					<td >{{$row['itemName']}}</td>
					<td >{{ Common::fixQty($row['qty'])}}</td>			
				</tr>
				@endforeach	

			</tbody>
			<tfoot >
				
			</tfoot>
		</table>
	</div>
	<!-- Zsales Qty Details Table End -->	

	
</body>
</html>