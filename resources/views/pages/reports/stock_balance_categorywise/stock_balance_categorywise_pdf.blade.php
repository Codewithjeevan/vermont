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
	.whatever { page-break-after: always; }

</style>    

</head>
<body>
	
	<div class="row">
		<center>				
			<label style="font-style: italic; font-weight: bold; font-size:20px;">Stock Balance Report</label>
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
					<th>@lang('glob.stock')</th>					
					<th>@lang('item.c_p')</th>
					<th>@lang('item.s_p')</th>
					<th>@lang('billing.amt')({{$curr}})</th>								
				</tr>
			</thead>
			<tbody >
				<?php $totalStock = 0;	$total = 0;?>				
				@foreach($categoryResponse as $p)
				<tr>
					<td colspan="8" style="font-weight: bold;
					font-size:10px; background-color: #D1D1D1 ;
					color: #121111 ;">{{$p->name}}</td>

				</tr>
				<?php $index=1; $stockM = 0;	$amtM = 0;	?>
				@foreach($p->child as $row)
				<?php $stock = ($row->balanceStock + $row->openingStock + $row->purchase - $row->purchaseReturn - $row->sale - $row->displayStock - $row->damageStock - $row->package - $row->offer + $row->saleReturn - $row->expiryStock + $row->stockAdjustmentExcess + $row->stockAdjustmentShortageWastage );
				$stockM = $stockM + $stock;
				$amtM = (($amtM) + ($stock * $row->costPrise));
				?>
				<tr>
					<td>{{{ $index++ }}}</td>				
					<td>@if($row->itemName == null) NA @else {{$row->itemName}} @endif</td>
					<td>{{{ $row->barcode }}}</td>					
					<td>@if($row->serial_number == null) NA @else {{$row->serial_number}} @endif</td>					
					@if($stock != null)
					<td class="text-right">{{{ Common::fixQty($stock)}}}</td>
					@else
					<td class="text-right">0.000</td>
					@endif
					<td class="text-right">{{{ Common::fixIt($row->costPrise)}}}</td>
					<td class="text-right">{{{ Common::fixIt($row->salePrise)}}}</td>					
					<td class="text-right">{{{ Common::fixIt($stock * $row->costPrise)}}}</td>
				</tr>

				@endforeach

				<?php $totalStock = $totalStock + $stockM;
				$total = $total + $amtM;?>
				
				<tr>					
					<th></th>
					<th></th>						
					<th></th>
					<th></th> 						
					<th>@lang('glob.stock') = @if($stockM == null) 0 @else {{ Common::fixQty($stockM)}} @endif</th>
					<th>@lang('item.c_p') = {{{ Common::fixIt($p->costPrise)}}}</th>
					<th>@lang('item.s_p') = {{{ Common::fixIt($p->salePrise)}}}</th>
					<th>@lang('billing.amt') = @if($amtM == null) 0.000 @else {{ Common::fixIt($amtM)}} @endif</th>	
				</tr>

				@endforeach	

			</tbody>
			<tfoot >
				<tr>					
					<th></th>
					<th></th>						
					<th></th>
					<th>@lang('glob.total') </th> 						
					<th>{{{ Common::fixQty($totalStock)}}}</th>
					<th>{{{ Common::fixIt($totalCP)}}}</th>
					<th>{{{ Common::fixIt($totalSP)}}}</th>
					<th>{{{ Common::fixIt($total)}}}</th>	
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