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
			<label style="font-style: italic; font-weight: bold; font-size:20px;">Cashier Report</label>
			<br/>		
			<br/>		
		</center>
	</div>	
	<br/>

	<div class="row"  style="width: 100%;">
		<div class="col-md-8">
			<label style="font-weight: bold;
			font-size:10px;">@lang('glob.shop_name') : {{$mallName}}</label>
		</div>
	</div>	
	<div class="row"  style="width: 100%;">
		<div class="col-md-8">
			<label style="font-weight: bold;
			font-size:10px;">@lang('glob.address') : {{$address}}</label>
		</div>
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
					<th>@lang('billing.bill_no')</th>	
					<th>@lang('billing.bill_date')</th>
					<th>@lang('billing.bill_time')</th>
					<th>@lang('billing.sub_total')({{$curr}})</th>
					@if(Common::vatType('vat','vat_type')!=0)<th>@lang('glob.vat')({{$curr}})</th>@endIf
					<th>@lang('billing.dis_amt')({{$curr}})</th>
					<th>@lang('billing.loyalty_discount')({{$curr}})</th>
					<th>@lang('billing.amt')({{$curr}})</th>								
				</tr>
			</thead>
			<tbody >

				@foreach($purchases as $p)
				<tr>
					@if(Common::vatType('vat','vat_type')!=0)<td colspan="9" style="font-weight: bold;
					font-size:10px; background-color: #D1D1D1 ;
					color: #121111 ;">{{$p->name}}</td>@else
					<td colspan="8" style="font-weight: bold;
					font-size:10px; background-color: #D1D1D1 ;
					color: #121111 ;">{{$p->name}}</td>
					@endIf



				</tr>
				<?php $index=1 ?>
				@foreach($p->child as $row)
				<tr>
					<td>{{{ $index++ }}}</td>			
					<td >{{Common::fixBill($row->id)}}</td>
					<td >{{$row->date}}</td>
					<td >{{$row->time}}</td>
					<td >{{Common::fixIt($row->subtotal)}}</td>
					@if(Common::vatType('vat','vat_type')!=0)<td >{{Common::fixIt($row->v_amt)}}</td>@endIf
					<td >{{Common::fixIt($row->dis_amt)}}</td>
					<td >{{Common::fixIt($row->loyalty_discount)}}</td>
					<td >{{Common::fixIt($row->bil_amt)}}</td>				
				</tr>
				@endforeach	
				<tr>  									
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<th>@lang('billing.sub_total') = {{ Common::fixIt($p->subtotal)}}</th>
					@if(Common::vatType('vat','vat_type')!=0)<th>@lang('glob.vat') = {{ Common::fixIt($p->v_amt)}}</th>	@endIf
					<th>@lang('billing.dis_amt') = {{ Common::fixIt($p->dis_amt)}}</th>	
					<th>@lang('billing.loyalty_discount') = {{ Common::fixIt($p->loyalty_discount)}}</th>	
					<th>@lang('billing.amt') = {{ Common::fixIt($p->bil_amt)}}</th>	
				</tr>
				@endforeach	

			</tbody>
			<tfoot >
				<tr>
					<th></th>											
					<th></th>	
					<th></th>
					<th>@lang('glob.total')</th>
					<th>{{ Common::fixIt($totalSub)}}</th>
					@if(Common::vatType('vat','vat_type')!=0)<th>{{ Common::fixIt($totalVatAmount)}}</th>@endIf
					<th>{{ Common::fixIt($totalDis)}}</th>
					<th>{{ Common::fixIt($totalLoyaltyDis)}}</th>					
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