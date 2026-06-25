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
			<label style="font-style: italic; font-weight: bold; font-size:20px;">Salesman Report</label>
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
					<th>@lang('billing.bill_no')</th>	
					<th>@lang('billing.bill_date')</th>
					<th>@lang('glob.customer')</th>
					<th>@lang('item.item_name')</th>	
					<th>@lang('glob.barcode')</th>
					<th>@lang('item.refrence_number')</th>
					<th>@lang('item.serial_number')</th>
					<th>@lang('glob.rate')</th>						
					<th>@lang('item.qty')</th>						
					<th>@lang('stock.amt')({{$curr}})</th>						
				</tr>
			</thead>
			<tbody >
				@foreach($purchases as $p)
				<tr>
					<td colspan="11" style="font-weight: bold;
					font-size:10px; background-color: #D1D1D1 ;
					color: #121111 ;">{{$p->name}}</td>
					
					
				</tr>
				<?php $index=1 ?>
				@foreach($p->child as $row)
				<tr>
					<td>{{{ $index++ }}}</td>			
					<td >{{ Common::fixBill($row->id)}}</td>
					<td >{{$row->billingDate}}</td>	
					@if($row->customerName != '')				
					<td >{{$row->customerName}}</td>
					@else
					<td>NA</td>
					@endif
					<td >{{$row->itemName}}</td>
					<td >{{$row->itemCode}}</td>
					<td >@if($row->refrence_number == null) NA @else {{$row->refrence_number}} @endif</td>
					@if($row->serial_number != '')				
					<td >{{$row->serial_number}}</td>
					@else
					<td>NA</td>
					@endif
					<td >{{ Common::fixIt($row->rate) }}</td>
					<td >{{ Common::fixQty($row->qty) }}</td>					
					<td >{{ Common::fixIt(($row->qty * $row->rate))}}</td>					
				</tr>
				@endforeach	
				<tr>  									
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<th></th>
					<th></th>
					<th>@lang('item.qty') = {{ Common::fixQty($p->qty)}}</th> 				
					<th>@lang('stock.amt') = {{ Common::fixIt($p->amt)}}</th>	
				</tr>
				@endforeach	

			</tbody>
			<tfoot >
				<tr>
					
					<th></th>	
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>					
					<th></th>
					<th>@lang('glob.total') </th>
					<th>{{ Common::fixQty($totalQty)}}</th> 					
					<th>{{ Common::fixIt($total)}}</th>							
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