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
			<label style="font-style: italic; font-weight: bold; font-size:20px;">Customer Classification Report</label>
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
					<th>@lang('glob.customer')</th>		
					<th>@lang('billing.bill_date')</th>								
					<th>@lang('billing.bill_no')</th>	
				    <th>@lang('glob.customerType')</th>
					<th>@lang('glob.total_purchase')</th>
					
				</tr>
			</thead>
			<tbody >
				@foreach($purchases as $p)
				<tr>
					<td colspan="6" style="font-weight: bold;
					font-size:10px; background-color: #D1D1D1 ;
					color: #121111 ;">{{$p->brand_name}}</td>


				</tr>
				<?php $index=1 ?>
				@foreach($p->child as $row)
				<tr>
					<td>{{{ $i++ }}}</td>			
					<td >{{$row->customerName ?? 'NA'}}</td>
					<td >{{$row->billingDate}}</td>
					<td >{{Common::fixBill($row->id)}}</td>
					<td >{{$row->customerType}}</td>
					<td >{{ Common::fixIt($row->amt)}}</td>
						
				</tr>
				@endforeach	

				<tr>
					<th style="width:10%"></th>											
					<th></th>	
					<th></th>
					<th></th>
                    <th></th>
					<th>@lang('glob.total_purchase') = {{ Common::fixIt($p->totalPurchase)}}</th>					
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
				</tr>
			</tfoot>
		</table>
	</div>
	<!-- Bank Details Table End -->	
	<br/>

	<div class="row">

    <div class="col-md-8">
			<label style="font-weight: bold;
			font-size:10px;">@lang('glob.total_purchase') = {{ Common::fixIt($totalPurchase)}}</label>
			
		</div>
		
	</div>	

	<div class="row">

    <div class="col-md-8">
			<label style="font-weight: bold;
			font-size:10px;">@lang('glob.totalTransaction') = {{ Common::fixQty($totalTransaction)}}</label>
			
		</div>
		
	</div>
	
</body>
</html>