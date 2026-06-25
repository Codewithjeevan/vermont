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
			<label style="font-style: italic; font-weight: bold; font-size:20px;">VAT Report</label>
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
					<th>@lang('glob.date')</th>	
					<th>@lang('glob.invoice_no')</th>
					<th>@lang('item.item_name')</th>
					<th>@lang('glob.barcode')</th>
					<th>@lang('item.refrence_number')</th>
					<th>@lang('glob.cost_sale_price')</th>					
					<th>@lang('billing.qty')</th>
					<th>@lang('glob.purchase_sale_value') ({{$curr}})</th>
					<th>@lang('glob.report_vat') ({{$curr}})</th>							
				</tr>
				
				
			</thead>
			<tbody >
				@foreach($purchases as $p)
				<tr>
					<td colspan="10" style="font-weight: bold;
					font-size:10px; background-color: #D1D1D1 ;
					color: #121111 ;">{{$p->name}}</td>
				</tr>
				<?php $amt = 0;
				$qty=0;
				$vat=0; 
				?>
				@foreach($p->child as $row)
				<?php
				$amt = (($row->qty * $row->price)) + $amt;
				$qty=(($row->qty )) + $qty;
				$vat=$row->vat_paid +$vat;
				
				?>
				<tr>
					<td>{{{ $i++ }}}</td>			
					<td >{{$row->date}}</td>
					<td >@if($p->id==1) {{$row->id}} @else {{Common::fixBill($row->id)}} @endif</td>
					<td>{{$row->itemName}}</td>
					<td>@if($row->barcode == null) NA @else {{$row->barcode}} @endif</td>
					<td>@if($row->serial_number == null) NA @else {{$row->serial_number}} @endif</td>
					<td >{{ Common::fixIt($row->price)}}</td>
					<td >{{ Common::fixQty($row->qty)}}</td>
					<td >{{ Common::fixIt(($row->qty * $row->price))}}</td>
					<td >{{ Common::fixIt($row->vat_paid)}}</td>	
				</tr>
				@endforeach
				<tr>  									
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<th>@lang('billing.qty') = {{ Common::fixQty($qty)}}</th>  
					<th>@lang('glob.total') = {{ Common::fixIt($amt)}}</th>	
					<th>@lang('glob.vat_total') = {{ Common::fixIt($vat)}}</th>	
				</tr>
				@endforeach	

			</tbody>			
		</table>
		<br/>
		<br/>
		<br/>

		<table style="width:100%;">
			<tr>
				<td style="width:50%; font-weight: bold;">
					@lang('glob.total_purchase_qty') = {{ Common::fixQty($totalPurchaseQty)}}<br/>
					@lang('glob.total_purchase_price') ({{$curr}}) = {{Common::fixIt($totalCostPrise)}}<br/>
					@lang('glob.total_vat_paid') ({{$curr}}) = {{ Common::fixIt($totalPurchaseVat)}}
				</td>
				<td style="width:50%;font-weight: bold; text-align: right;">
					@lang('glob.total_sale_qty') = {{ Common::fixQty($totalSaleQty)}}<br/>
					@lang('glob.total_sale_price') ({{$curr}}) = {{ Common::fixIt($totalSalePrice)}}<br/>
					@lang('glob.total_vat_received') ({{$curr}}) =  {{ Common::fixIt($totalsalevat)}}
				</td>
			</tr>
		</table>
		

	</div>
	<!-- Bank Details Table End -->	
	<br/>

	<div class="row">

		
		
	</div>	
	
</body>
</html>