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
			<label style="font-style: italic; font-weight: bold; font-size:20px;">Customer Ledger Details</label>
			<br/>		
			<br/>		
		</center>
	</div>

	<div class="row">
		@foreach($customer as $row)	
		<label style="font-weight: bold; font-size:15px;">@lang('glob.name') : {{$row->name}}</label>
		@endforeach	
	</div>
	<br/>
	

	<!-- Bank Details Table -->
	<div class="table-responsive" style="width: 100%;">
		<table class="table table-bordered" style="width: 100%;">
			<thead>
				<tr>
					<th style="width:10%">#</th>
					<th>@lang('glob.date')</th>
					<th>@lang('glob.perticulars')</th> 
					<th>@lang('billing.credit_amt')</th>	
					<th>@lang('billing.debit_amt')</th>
					<th>@lang('billing.bal')</th>					
				</tr>
			</thead>
			<tbody >
				@foreach($ledger as $row)
				<tr>				
					<td>{{{ $i++ }}}</td>
					<td>{{{ $row->created_at }}}</td>	
					<td>{{{ ($row->type == 1 ) ? "Bill No: " : "Receipt No: " }}}  {{{ \Common::fixBill($row->bill_id) }}}</td>
					<td class="text-right">{{{  (isset($row->credit)) ? Common::fixIt($row->credit) : 0 }}}</td>
					<td class="text-right">{{{  (isset($row->debit)) ? Common::fixIt($row->debit) : 0 }}}</td>
					<td class="text-right">{{{  (isset($row->balance)) ? Common::fixIt($row->balance) : 0 }}}</td>	

					@endforeach	
				</tr>

			</tbody>
			<tfoot >
				<tr>
					<th style="width:10%">@lang('glob.total') : {{{ $total }}}</th>
					<th >-</th>
					<th >-</th> 
					<th class="text-right" >{{{ (isset($totalCredit)) ? Common::fixIt($totalCredit) : 0 }}}</th>	
					<th class="text-right" >{{{ (isset($totalDebit)) ? Common::fixIt($totalDebit) : 0 }}}</th>
					<th class="text-right" >{{{ (isset($totalBalance)) ? Common::fixIt($totalBalance) : 0 }}}</th>						
				</tr>
			</tfoot>
		</table>
	</div>
	<!-- Bank Details Table End -->	
	<br/>

	<div class="row">

		<label style="font-weight: bold; font-size:15px;">@lang('glob.remark') : {{$remark}}</label>
		
	</div>	
	
</body>
</html>