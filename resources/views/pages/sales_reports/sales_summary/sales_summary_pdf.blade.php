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
			<label style="font-style: italic; font-weight: bold; font-size:20px;">Sales Summary Report</label>
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
					<th>@lang('billing.bill_date')</th>
					<th>@lang('billing.bill_no')</th>						
					<th>@lang('glob.customer')</th>
					<th>@lang('billing.sub_total') ({{$curr}})</th>
					@if($intVatType != 0)
					<th>@lang('glob.vat') ({{$curr}})</th>
					@endif
					<th>@lang('glob.discount') ({{$curr}})</th>
					<th>@lang('glob.loyalty_discount') ({{$curr}})</th>
					<th>@lang('billing.mode_of_pay')</th>
					<th>@lang('billing.cash_amt') ({{$curr}})</th>	
					<th>@lang('billing.card_amt') ({{$curr}})</th>			
				</tr>
			</thead>
			<tbody >
				@if($intVatType != 0)
				@foreach($itemList as $row)
				<tr>
					<td>{{{ $i++ }}}</td>			
					<td >{{$row['dateTime']}}</td>
					<td >{{$row['id']}}</td>
					<td >{{$row['customerName']}}</td>		
					<td >{{ Common::fixIt($row['subtotal'])}}</td>	
					<td >{{ Common::fixIt($row['vat_amt'])}}</td>	
					<td >{{ Common::fixIt($row['dis_amt'])}}</td>
					<td >{{ Common::fixIt($row['loydis_amt'])}}</td>
					<td >{{$row['payment_mode']}}</td>
					<td >{{ Common::fixIt($row['cash_amt'])}}</td>
					<td >{{Common::fixIt($row['card_amt'])}}</td>		
				</tr>
				@endforeach	
				@else
				@foreach($itemList as $row)
				<tr>
					<td>{{{ $i++ }}}</td>			
					<td >{{$row['dateTime']}}</td>
					<td >{{$row['id']}}</td>
					<td >{{$row['customerName']}}</td>		
					<td >{{ Common::fixIt($row['subtotal'])}}</td>		
					<td >{{ Common::fixIt($row['dis_amt'])}}</td>
					<td >{{ Common::fixIt($row['loydis_amt'])}}</td>
					<td >{{$row['payment_mode']}}</td>
					<td >{{ Common::fixIt($row['cash_amt'])}}</td>
					<td >{{Common::fixIt($row['card_amt'])}}</td>		
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
					<th>@lang('billing.sub_total') = {{ Common::fixIt($total)}}</th>	
					@if($intVatType != 0)
					<th>@lang('glob.vat') @lang('glob.total') = {{ Common::fixIt($totalVat)}}</th>	
					@endif
					<th>@lang('glob.discount') = {{ Common::fixIt($totalDiscount)}}</th>
					<th>@lang('glob.loyalty_discount') = {{ Common::fixIt($totalLoyDis)}}</th>
					<th></th>						
					<th>@lang('billing.cash_amt') = {{ Common::fixIt($totalCash)}}</th>	
					<th>@lang('billing.card_amt') = {{ Common::fixIt($totalCard)}}</th>
				</tr>
			</tfoot>
		</table>
	</div>
	<!-- Bank Details Table End -->	
	<br/>
	<div class="row" style="width: 100%;">
		<div class="col-md-8">
			<label label style="font-weight: bold;
			font-size:10px;">@lang('glob.total') @lang('glob.sales_with_vat') = {{ Common::fixIt($total + $totalVat)}}</label>
		</div>		
	</div>

	<div class="row" style="width: 100%;">
		<div class="col-md-8">
			<label label style="font-weight: bold;
			font-size:10px;">@lang('glob.total') @lang('glob.sales_without_vat') = {{Common::fixIt($total)}}</label>
		</div>		
	</div>
	
</body>
</html>