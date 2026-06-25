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
			<label style="font-style: italic; font-weight: bold; font-size:20px;">Purchase Return Report</label>
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
					<th>@lang('glob.sale_return_no')</th>					
					<th>@lang('stock.stock_transfer_no')</th>					
					<th>@lang('stock.return_date')</th>
					<th>@lang('glob.supervisor')</th>
					<th>@lang('item.item_name')</th>
					<th>@lang('glob.cost_price')</th>
					@if($intVatType != 0)
					<th>@lang('item.cp_vat_percent')</th>
					@endif
					<th>@lang('item.qty')</th>
				</tr>
			</thead>
			<tbody >

				@if($intVatType != 0)

				@foreach($purchases as $p)
				<tr>
					<td colspan="9" style="font-weight: bold;
					font-size:10px; background-color: #D1D1D1 ;
					color: #121111 ;">{{$p->name}}</td>
				</tr>
				<?php $amt = 0;?>
				@foreach($p->child as $row)
				<?php $amt = (($row->qty * $row->costPrise) - $row->dis_amt) + $amt;?>
				<tr>
					<td>{{{ $i++ }}}</td>			
					<td >{{$row->id}}</td>
					<td >{{$row->supplierBillNo}}</td>
					<td >{{$row->supplier_billdate}}</td>
					<td >{{$row->return_through}}</td>
					<td >{{$row->itemName}}</td>
					<td >{{ Common::fixIt($row->costPrise)}}</td>
					<td >{{ Common::fixIt($row->cp_vat)}}</td>
					<td >{{ Common::fixQty($row->qty)}}</td>					
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
					<td></td>
					<th>@lang('item.qty') = {{ Common::fixQty($p->qty)}}</th>						
				</tr>
				@endforeach	

				@else

				@foreach($purchases as $p)
				<tr>
					<td colspan="8" style="font-weight: bold;
					font-size:10px; background-color: #D1D1D1 ;
					color: #121111 ;">{{$p->name}}</td>
				</tr>
				<?php $amt = 0;?>
				@foreach($p->child as $row)
				<?php $amt = (($row->qty * $row->costPrise) - $row->dis_amt) + $amt;?>
				<tr>
					<td>{{{ $i++ }}}</td>			
					<td >{{$row->id}}</td>
					<td >{{$row->supplierBillNo}}</td>
					<td >{{$row->supplier_billdate}}</td>
					<td >{{$row->return_through}}</td>
					<td >{{$row->itemName}}</td>
					<td >{{ Common::fixIt($row->costPrise)}}</td>
					<td >{{ Common::fixQty($row->qty)}}</td>					
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
					<th>@lang('item.qty') = {{ Common::fixQty($p->qty)}}</th>						
				</tr>
				@endforeach	

				@endif

			</tbody>
			<tfoot >
				<tr>
					<th></th>						
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>	
					@if($intVatType != 0)				
					<th></th>
					@endif
					<th>@lang('glob.total')</th>
					<th>{{ Common::fixQty($totalQty)}}</th>
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