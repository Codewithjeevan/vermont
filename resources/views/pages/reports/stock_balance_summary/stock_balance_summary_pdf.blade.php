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
			<label style="font-style: italic; font-weight: bold; font-size:20px;">Stock Balance Summary Report</label>
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
					<th>@lang('item.item_name')</th> 
					<th>@lang('glob.barcode')</th>
					<th>@lang('item.refrence_number')</th>
											
					<th>@lang('glob.initial_stock')</th>
					<th>@lang('glob.open_stock')</th>
					<th>@lang('glob.purchase')</th>
					<th>@lang('glob.purchase_return')</th>
					<th>@lang('glob.issued_sale')</th>
					<th>@lang('glob.sale_return')</th>
					<th>@lang('glob.adjustment')</th>
					<th>@lang('glob.balance_stock')</th>								
				</tr>
			</thead>
			<tbody >
				@foreach($categoryResponse as $p)
				<tr>
					<td colspan="12" style="font-weight: bold;
					font-size:10px; background-color: #D1D1D1 ;
					color: #121111 ;">{{$p->name}}</td>

				</tr>
				<?php $index=1 ?>
				@foreach($p->child as $row)
						
				<tr>
					<td>{{{ $index++ }}}</td>		
					<td>{{{ $row->itemName }}}</td>			
					<td>@if($row->barcode == null) NA @else {{$row->barcode}} @endif</td>
					<td>@if($row->serial_number == null) NA @else {{$row->serial_number}} @endif</td>
					
					<td>@if($row->balanceStock == null) {{Common::fixQty(0)}} @else {{{Common::fixQty($row->balanceStock)}}} @endif</td>	
					<td >{{{ Common::fixQty($row->openingStock)}}}</td>
					<td >{{{ Common::fixQty($row->purchase)}}}</td>						
					<td >{{{ Common::fixQty($row->purchaseReturn)}}}</td>			
					<td >{{{ Common::fixQty(($row->sale + $row->displayStock +$row->damageStock +$row->package + $row->offer + $row->expiryStock))}}}</td>
					<td >{{{ Common::fixQty($row->saleReturn)}}}</td>
					<td >{{{ Common::fixQty($row->stockAdjustmentExcess + $row->stockAdjustmentShortageWastage)}}}</td>					
					<td >{{{ Common::fixQty(($row->balanceStock + $row->openingStock + $row->purchase - $row->purchaseReturn - $row->sale - $row->displayStock - $row->damageStock - $row->package - $row->offer + $row->saleReturn - $row->expiryStock + $row->stockAdjustmentExcess + $row->stockAdjustmentShortageWastage))}}}</td>
					
				</tr>
				@endforeach	

				@endforeach	

			</tbody>
			<tfoot >				
			</tfoot>
		</table>
	</div>
	<!-- Bank Details Table End -->	
	<br/>

	<div class="row">

		
		
	</div>	
	
</body>
</html>