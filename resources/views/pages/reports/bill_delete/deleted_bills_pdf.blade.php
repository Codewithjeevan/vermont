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
			<label style="font-style: italic; font-weight: bold; font-size:20px;">Deleted Bill Report</label>
			<br/>		
			<br/>		
		</center>
	</div>
	

<!-- deleted bill Details Table -->
	<div class="table-responsive" style="width: 100%;">
		<table class="table table-bordered" style="width: 100%;">
		
			<thead>
				<tr>
					<th style="width:2%">#</th>
					<th>@lang('billing.bill_no')</th>
					<th>@lang('glob.taxRefundTagNumber')</th>
					<th>@lang('billing.bill_amount')</th>
					<th>@lang('glob.deleted_by')</th>
					<th>@lang('glob.remark')</th>
					<th>@lang('glob.datetime')</th>
				</tr>
			</thead>

			<tbody>

				@foreach($arrDeletedBills as $index=>$bill)
				<tr>
					<td>{{  $bill->id }}</td>
					<td>{{ Common::fixBill($bill->bill_id) }}</td>
					<td>{{$bill->taxRefundTagNumber}}</td>
					<td>{{ Common::fixIt($bill->amount) }}</td>
					<td>{{ (isset($bill->_user->email) ? $bill->_user->email : '')  }}</td>
					<td>{{ $bill->remark}}</td>
					<td>{{ $bill->created_at->format($date_format) }}</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
<!-- deleted bill Details Table End -->	
	<br/>	
	
</body>
</html>