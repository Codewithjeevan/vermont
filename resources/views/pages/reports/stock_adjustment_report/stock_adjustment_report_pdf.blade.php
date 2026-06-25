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
			<label style="font-style: italic; font-weight: bold; font-size:20px;">Stock Adjustment Report</label>
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
	<br/>

	<!-- Bank Details Table -->
	<div class="table-responsive" style="width: 100%;">
		<table class="table table-bordered" style="width: 100%;">
			<thead>
				<tr>
					<th style="width:10%">#</th>
					<th>@lang('item.item_name')</th>	
					<th>@lang('glob.barcode')</th>	
					<th>@lang('item.qty')</th>			
					<th>@lang('item.refrence_number')</th>			
					<th>@lang('glob.unit')</th>
					<th>@lang('glob.cost')</th>		
					<th>@lang('stock.amt')({{$curr}})</th>	
					<th>@lang('glob.user')</th>	
					<th>@lang('glob.date')</th>
				</tr>
			</thead>
			<tbody >
				@foreach($purchases as $p)
				<tr>
					<td colspan="10" style="font-weight: bold;
					font-size:10px; background-color: #D1D1D1 ;
					color: #121111 ;">{{$p->name}}</td>
					
					
				</tr>
				<?php $index=1 ?>
				@foreach($p->child as $row)
				<tr>
					<td>{{{ $index++ }}}</td>	
					<td >{{$row->itemName}}</td>
					<td >{{$row->barcode}}</td>	
					<td >{{ Common::fixQty($row->quantity) }}</td>	
					<td >{{$row->serial_number}}</td>	
					<td >{{$row->unit}}</td>
					<td >{{ Common::fixIt($row->rate) }}</td>
					<td >{{ Common::fixIt($row->amt)}}</td>
					<td >{{$row->name}}</td>
					<td >{{$row->stock_adjustment_date}}</td>					
				</tr>
				@endforeach	
				<tr>  									
					<td></td>
					<td></td>
					<td></td>
					<th>@lang('item.qty') = {{ Common::fixQty($p->quantity)}}</th> 
					<td></td>
					<td></td>
					<td></td>
					<th>@lang('stock.amt') = {{ Common::fixIt($p->amt)}}</th>
					<th></th>
					<th></th>									
						
				</tr>
				@endforeach	

			</tbody>
			<tfoot >
				<tr>
					
					<th></th>	
					<th></th>
					<th></th>
					<th><!-- {{ Common::fixQty($totalQty)}} --></th>
					<th></th>
					<th></th>
					<th>@lang('glob.net_loss_value_adjusted')</th>
					<th>{{ Common::fixIt($total)}}</th>					
					<th></th>
					<th></th>				 					
											
				</tr>
			</tfoot>
		</table>
	</div>
	<!-- Bank Details Table End -->	
	<br/>

	<div class="row">

		
		
	</div>	
	
</body>
</html>