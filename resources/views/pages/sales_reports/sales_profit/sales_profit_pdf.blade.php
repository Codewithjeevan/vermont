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
			<label style="font-style: italic; font-weight: bold; font-size:20px;">Sales profit report</label>
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
					<th>@lang('billing.bill_no')</th>	
					<th>@lang('billing.bill_date')</th>
					<th>@lang('item.item_name')</th>
					<th>@lang('glob.barcode')</th>
					<th>@lang('item.refrence_number')</th>
					<th>@lang('item.serial_number')</th>
					<th>@lang('item.qty')</th>
					<th>@lang('glob.rate')</th>
					<th>@lang('item.c_p')</th>
					<th>@lang('glob.total_s_p')</th>	
					<th>@lang('glob.total_c_p')</th>	
					<th>@lang('glob.total_profit')</th>	
					<th>@lang('glob.percentage')(%)</th>				
				</tr>
			</thead>
			<tbody >
				@foreach($itemList as $row)
				<tr>
					<td>{{{ $i++ }}}</td>			
					<td >{{$row['id']}}</td>
					<td >{{$row['billingDate']}}</td>
					<td >{{$row['itemName']}}</td>	
					<td >{{$row['itemCode']}}</td>		
					<td >{{$row['refrence_number']}}</td>	
					<td >{{$row['serial_number']}}</td>	
					<td >{{ Common::fixQty($row['qty'])}}</td>	
					<td >{{ Common::fixIt($row['rate'])}}</td>
					<td >{{ Common::fixIt($row['costPrise'])}}</td>
					<td >{{ Common::fixIt($row['totalSP'])}}</td>
					<td >{{ Common::fixIt($row['totalCP'])}}</td>
					<td >{{ Common::fixIt($row['totalAmt'])}}</td>
					<td >{{ Common::fixIt($row['percentage'])}}</td>				
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
					<th></th>	
					<th></th>											
					<th>@lang('glob.total_s_p') = {{ Common::fixIt($totalSP)}}</th>	
					<th>@lang('glob.total_c_p') = {{ Common::fixIt($totalCP)}}</th>
					<th>@lang('glob.total_profit') = {{ Common::fixIt($total)}}</th>		
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