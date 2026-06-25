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
			<label style="font-style: italic; font-weight: bold; font-size:20px;">Purchase Order Report</label>
			<br/>		
		</center>
	</div>

	<div class="row">			
		<label style="font-style: italic; font-weight: bold; font-size:10px;">@lang('stock.purchase_order_id') : {{$po_id}}</label>
		<br/>		
		
	</div>

	<div class="row">			
		<label style="font-style: italic; font-weight: bold; font-size:10px;">@lang('supplier.supplier_name') : {{$name}}</label>
		<br/>		
	</div>

	<div class="row">			
		<label style="font-style: italic; font-weight: bold; font-size:10px;">@lang('glob.status') : {{$po_no}}</label>
		<br/>		
		<br/>		
	</div>	

	<!-- Purchase Order Details Table -->
	<div class="table-responsive" style="width: 100%;">
		<table class="table table-bordered" style="width: 100%;">
			<thead>
				<tr>
					<th>@lang('glob.id')</th>					
					<th>@lang('item.item_name')</th>
					<th>@lang('item.item_code')</th>
					<th>@lang('item.refrence_number')</th>
					<th>@lang('item.quantity')</th>
				</tr>
			</thead>
			<tbody >
				@foreach($itemList as $row) 

				<tr>
					<td>{{$i++}}</td>						
					<td>{{$row ->items->itemName}}</td>
					<td>{{$row->items->barcode}}</td>	
					<td>{{$row->items->serial_number}}</td>						
					<td>{{Common::fixQty($row->quantity)}}</td>						
				</tr>
				@endforeach	


			</tbody>
		</table>
	</div>
	<!-- Purchase Order Details Table End -->	
	<br/>
	<div class="row">			
		<label style="font-style: italic; font-weight: bold; font-size:10px;">You have placed order on {{ $date }}.</label>
		<br/>		
		<br/>		
	</div>	
	
</body>
</html>