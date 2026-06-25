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
			<label style="font-style: italic; font-weight: bold; font-size:20px;">X-Sales Report</label>
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
    <div class="row" style="width: 100%;">
		<div class="col-md-8">
			<label label style="font-weight: bold;
			font-size:10px;">@lang('glob.opening_amt') = {{$openingCash}}</label>
		</div>		
	</div>
	

	<!-- Bank Details Table -->
	<div class="table-responsive" style="width: 100%;">
		<table class="table table-bordered" style="width: 100%;">
			<thead>
				<tr>
					<th style="width:10%">@lang('glob.id')</th>											
					<th>@lang('billing.bill_date')</th>
					<th>@lang('billing.bill_no')</th>							
					<th>@lang('billing.amt')({{$curr}})</th>			
				</tr>
			</thead>
			<tbody >
				@foreach($purchases as $row)
				<tr>
					<td>{{{ $i++ }}}</td>			
					<td >{{$row['dateTime']}}</td>
					<td >{{ $row['id']}}</td>
					<td >{{ Common::fixIt($row['bil_amt']) }}</td>				
				</tr>
				@endforeach	

			</tbody>
			<tfoot >
				<tr>
					<th></th>	
					<th></th>	
					<th></th>	
					<th>@lang('glob.total') = {{ Common::fixIt($billAmt) }}</th>	
				</tr>
			</tfoot>
		</table>
	</div>
	<!-- Bank Details Table End -->	
	<br/>

	 <div class="row" style="width: 100%;">
		<div class="col-md-8">
			<label label style="font-weight: bold;
			font-size:10px;">@lang('glob.opening_amt') + @lang('glob.sale') ({{$curr}}) = {{ Common::fixIt($openingCash + $billAmt)}}</label>
		</div>		
	</div>
	 <div class="row" style="width: 100%;">
		<div class="col-md-8">
			<label label style="font-weight: bold;
			font-size:10px;">@lang('billing.cash') ({{$curr}}) = {{ Common::fixIt($cashAmt) }}</label>
		</div>		
	</div>
	 <div class="row" style="width: 100%;">
		<div class="col-md-8">
			<label label style="font-weight: bold;
			font-size:10px;">@lang('billing.credit_card') ({{$curr}}) = {{ Common::fixIt($cardAmt)}}</label>
		</div>		
	</div>
	 <div class="row" style="width: 100%;">
		<div class="col-md-8">
			<label label style="font-weight: bold;
			font-size:10px;">@lang('billing.credit') ({{$curr}}) = {{  Common::fixIt($saleCredit) }}</label>
		</div>		
	</div>	
	 <div class="row" style="width: 100%;">
		<div class="col-md-8">
			<label label style="font-weight: bold;
			font-size:10px;">@lang('billing.sale_return') ({{$curr}}) = {{ Common::fixIt($saleReturn)  }}</label>
		</div>		
	</div>
	 <div class="row" style="width: 100%;">
		<div class="col-md-8">
			<label label style="font-weight: bold;
			font-size:10px;">@lang('billing.bal') ({{$curr}}) = {{ Common::fixIt($openingCash+$cashAmt+$cardAmt+$saleCredit-$saleReturn) }}</label>
		</div>		
	</div>

	
</body>
</html>