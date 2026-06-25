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
			<label style="font-style: italic; font-weight: bold; font-size:20px;">Stock Reorder Report</label>
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
	

	<!-- Bank Details Table -->
	<div class="table-responsive" style="width: 100%;">
		<table class="table table-bordered" style="width: 100%;">
			<thead>
				<tr>
					<th style="width:10%">#</th>									
					<th>@lang('item.item_name')</th> 
					<th>@lang('glob.barcode')</th>
					<th>@lang('item.refrence_number')</th>					
					<th>@lang('glob.stock')</th>
					<th>@lang('glob.reorder_level')</th>	
					<th>@lang('glob.purchase_qty')</th>	
					<th>@lang('glob.unit')</th>										
				</tr>
			</thead>
			<tbody >
				@foreach($categoryResponse as $p)
				<tr>
					<td colspan="8" style="font-weight: bold;
					font-size:10px; background-color: #D1D1D1 ;
					color: #121111 ;">{{$p->name}}</td>

				</tr>
				<?php $index=1 ?>
				@foreach($p->child as $row)
				
				<tr @if($row->stock < $row->min) style="background: #e14e3c; color: white;" @else style="background: blue;  color: white;" @endif >
					<td>{{{ $index++ }}}</td>					
					<td>{{$row->itemName}}</td>
					<td>@if($row->barcode == null) NA @else {{$row->barcode}} @endif</td>
					<td>@if($row->serial_number == null) NA @else {{$row->serial_number}} @endif</td>
					<td>@if($row->stock != null) {{ Common::fixQty($row->stock)}} @else 0.000 @endif</td>				
					<td>{{ Common::fixQty($row->min)}}</td>
					<td>{{ Common::fixQty(($row->min - $row->stock))}}</td>	
					<td>{{$row->unitName}}</td>			

				</tr>
				@endforeach	

				@endforeach	

			</tbody>
			<tfoot >
			</tfoot>
		</table>
	</div>
	<!-- Bank Details Table End -->	
	<br/>

	
</body>
</html>