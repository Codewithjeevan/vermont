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
			<label style="font-style: italic; font-weight: bold; font-size:20px;">CRM Report</label>
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
		<table class="table table-bordered" style="width: 100%;word-wrap:break-word;
              table-layout: fixed;">
			<thead>
				<tr>
					<th style="width:5%">#</th>											
					<th style="width:5%">@lang('glob.bill_no')</th>
					<th style="width:9%">@lang('glob.mobile')</th>
					<th style="width:9%">@lang('glob.date_of_birth')</th>
					<th style="width:9%">@lang('billing.bill_date')</th>
					<th style="width:8%">@lang('glob.city')</th>
					<th style="width:10%">@lang('glob.salesman')</th>
					<th style="width:10%">@lang('glob.prefered_mode')</th>
					<th style="width:10%">@lang('glob.prefered_language')</th>
					<th style="width:10%">@lang('glob.brand_name')</th>
					<th style="width:10%">@lang('item.reference_number')</th>
					<th style="width:15%">@lang('billing.item_name')</th>	
					<th style="width:10%">@lang('glob.value')</th>
				</tr>
			</thead>
			<tbody >
			
				@foreach($purchases as $p)
				<tr>
						<td colspan="13" style="font-weight: bold;
						font-size:10px; background-color: #D1D1D1 ;
						color: #121111 ;">{{$p->name}}</td>
						

					</tr>
					@foreach($p->child as $row)
				<tr>
					<td>{{{ $i++ }}}</td>			
					<td >{{Common::fixBill($row->bill_id)}}</td>	
					<td >{{$row->mobile}}</td>	
					<td >{{$row->birthday}}</td>	
					<td >{{$row->bill_date}}</td>
					<td >{{$row->city}}</td>
					<td >{{$row->salesman}}</td>
					<td >{{$row->prefered_mode}}</td>
					<td >{{$row->prefered_language}}</td>
					<td >{{$row->brand_name}}</td>
					<td >{{$row->reference_number}}</td>
					<td >{{$row->item_name}}</td>		
					<td >{{Common::fixIt($row->value)}}</td>		
				</tr>
				@endforeach	
				<tr>
						<td></td>			
						<td >@lang('glob.totalTransaction') = {{ Common::fixQty($p->totalTransaction)}}</td>
						<td ></td>
						<td ></td>
						<td ></td>
						<td ></td>	
						<td ></td>	
						<td ></td>
						<td ></td>
						<td ></td>
						<td ></td>
						<td ></td>
						<td ></td>
					</tr>
					@endforeach

			</tbody>
			<tfoot >
				
			</tfoot>
		</table>
	</div>
	<!-- Bank Details Table End -->	
	<br/>
	
	<div class="row" style="width: 100%;">
		<div class="col-md-8">
			<label style="font-weight: bold;
			font-size:10px;">@lang('glob.totalTransaction')  = {{ Common::fixQty($totalTransaction)}}</label>
			
		</div>
	</div>
	<div class="row">
		<div class="col-md-8">
			<label style="font-weight: bold;
			font-size:10px;">@lang('glob.totalCustomer') = {{ Common::fixQty($totalCustomer)}}</label>
			
		</div>
	</div>

	<div class="row">
		<div class="col-md-8">
			<label style="font-weight: bold;
			font-size:10px;">@lang('glob.totalItemQty') = {{ Common::fixQty($totalQty)}}</label>
			
		</div>
	</div>


	
</body>
</html>