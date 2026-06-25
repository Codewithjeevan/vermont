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
			<label style="font-style: italic; font-weight: bold; font-size:20px;">Itemwise Sales Report</label>
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
					<th>@lang('billing.bill_date')</th>	
					<th>@lang('billing.bill_no')</th>
					<th>@lang('glob.customer')</th>		
					<th>@lang('glob.rate')</th>	
					@if($intVatType !=0)
					<th>@lang('glob.vat') (%)</th>	
					@endif	
					<th>@lang('item.qty')</th> 					
					
					<th>@lang('billing.amt')({{$curr}})</th>						
				</tr>
			</thead>
			<tbody >
				@if($intVatType !=0)
				@foreach($purchases as $p)
				<tr>
					<td colspan="8" style="font-weight: bold;
					font-size:10px; background-color: #D1D1D1 ;
					color: #121111 ;">{{$p->itemName}}</td>					

				</tr>
				<?php $index=1 ?>
				@foreach($p->child as $row)
				<tr>
					<td>{{{ $index++ }}}</td>			
					<td >{{$row->dateTime}}</td>
					<td >{{Common::fixBill($row->id)}}</td>
					<td >{{isset($row->customerName) ? $row->customerName : 'N/A'}}</td>
					<td >{{ Common::fixIt($row->rate)}}</td>
					<td >{{ Common::fixIt($row->vat)}}</td>
					<td >{{ Common::fixQty($row->qty)}}</td>
					
					<td >{{ Common::fixIt($row->amt)}}</td>
				</tr>
				@endforeach	

				<tr>
					<td></td>			
					<td ></td>
					<td ></td>
					<td ></td>
					<td ></td>
					<td ></td>
					<td >@lang('item.qty') = {{ Common::fixQty($p->qty)}}</td>
					
					<td >@lang('billing.amt') = {{ Common::fixIt($p->amt)}}</td>
				</tr>

				@endforeach	
				@else
				@foreach($purchases as $p)
				<tr>
					<td colspan="7" style="font-weight: bold;
					font-size:10px; background-color: #D1D1D1 ;
					color: #121111 ;">{{$p->itemName}}</td>					

				</tr>
				<?php $index=1 ?>
				@foreach($p->child as $row)
				<tr>
					<td>{{{ $index++ }}}</td>			
					<td >{{$row->dateTime}}</td>
					<td >{{Common::fixBill($row->id)}}</td>
					<td >{{isset($row->customerName) ? $row->customerName : 'N/A'}}</td>
					<td >{{ Common::fixIt($row->rate)}}</td>
					<td >{{ Common::fixQty($row->qty)}}</td>
					
					<td >{{ Common::fixIt($row->amt)}}</td>
				</tr>
				@endforeach	

				<tr>
					<td></td>			
					<td ></td>
					<td ></td>
					<td ></td>
					<td ></td>
					<td >@lang('item.qty') = {{ Common::fixQty($p->qty)}}</td>
					
					<td >@lang('billing.amt') = {{ Common::fixIt($p->amt)}}</td>
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
					@if($intVatType !=0)
					<th></th>
					@endif
					<th>@lang('glob.total')</th>
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