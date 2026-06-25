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
			<label style="font-style: italic; font-weight: bold; font-size:20px;">Purchase Report</label>
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
					<th>@lang('stock.mrn_no')</th>	
					<th>@lang('glob.barcode')</th>
					<th>@lang('item.refrence_number')</th>
					<th>@lang('item.item_name')</th>
					<th>@lang('stock.stock_transfer_no')</th>					
					<th>@lang('stock.stock_transfer_date')</th>
					<th>@lang('glob.rate')</th>
					@if($intVatType != 0)
					<th>@lang('item.cp_vat_percent')</th>
					@endif
					<th>@lang('item.qty')</th>
					<th>@lang('billing.sub_total')({{$curr}})</th>
					<th>@lang('billing.dis_amt')({{$curr}})</th>					
					<th>@lang('stock.amt')({{$curr}})</th>							
				</tr>
			</thead>
			<tbody >
				@foreach($purchases as $p)
				
				<?php $amt = 0;?>

				@if($intVatType == 0)

				<tr>
					<td colspan="12" style="font-weight: bold;
					font-size:10px; background-color: #D1D1D1 ;
					color: #121111 ;">{{$p->name}}</td>
				</tr>

				@foreach($p->child as $row)
				<?php $amt = (($row->qty * $row->costPrise) - $row->dis_amt) + $amt;?>
				<tr>
					<td>{{{ $i++ }}}</td>			
					<td >{{$row->id}}</td>
					<td>@if($row->itemCode == null) NA @else {{$row->itemCode}} @endif</td>
					<td>@if($row->serial_number == null) NA @else {{$row->serial_number}} @endif</td>
					<td >{{$row->itemName}}</td>
					<td >{{$row->supplierBillNo}}</td>
					<td >{{$row->supplier_billdate}}</td>
					<td >{{ Common::fixIt($row->costPrise)}}</td>	
					<td >{{ Common::fixQty($row->qty)}}</td>					
					<td >{{ Common::fixIt(($row->qty * $row->costPrise))}}</td>
					<td >{{ Common::fixIt($row->dis_amt)}}</td>	
					<td >{{ Common::fixIt((($row->qty * $row->costPrise) - $row->dis_amt))}}</td>	
				</tr>
				@endforeach

				@elseif($intVatType == 1)
				<tr>
					<td colspan="13" style="font-weight: bold;
					font-size:10px; background-color: #D1D1D1 ;
					color: #121111 ;">{{$p->name}}</td>
				</tr>
				@foreach($p->child as $row)
				<?php $amt = (($row->qty * $row->costPrise) - $row->dis_amt) + $amt;?>
				<tr>
					<td>{{{ $i++ }}}</td>			
					<td >{{$row->id}}</td>
					<td>@if($row->itemCode == null) NA @else {{$row->itemCode}} @endif</td>
					<td>@if($row->serial_number == null) NA @else {{$row->serial_number}} @endif</td>
					<td >{{$row->itemName}}</td>
					<td >{{$row->supplierBillNo}}</td>
					<td >{{$row->supplier_billdate}}</td>
					<td >{{ Common::fixIt($row->costPrise)}}</td>
					<td >{{ Common::fixIt($row->cp_vat)}}</td>	
					<td >{{ Common::fixQty($row->qty)}}</td>					
					<td >{{ Common::fixIt(($row->qty * $row->costPrise))}}</td>
					<td >{{ Common::fixIt($row->dis_amt)}}</td>	
					<td >{{ Common::fixIt((($row->qty * $row->costPrise) - $row->dis_amt))}}</td>	
				</tr>
				@endforeach
				
				@elseif($intVatType == 2)
				<tr>
					<td colspan="13" style="font-weight: bold;
					font-size:10px; background-color: #D1D1D1 ;
					color: #121111 ;">{{$p->name}}</td>
				</tr>

				@foreach($p->child as $row)
				<?php $amt = (($row->qty * ($row->costPrise + (($row->costPrise * $row->cp_vat)/100 ) )) - $row->dis_amt) + $amt;?>
				<tr>
					<td>{{{ $i++ }}}</td>			
					<td >{{$row->id}}</td>
					<td>@if($row->itemCode == null) NA @else {{$row->itemCode}} @endif</td>
					<td>@if($row->serial_number == null) NA @else {{$row->serial_number}} @endif</td>
					<td >{{$row->itemName}}</td>
					<td >{{$row->supplierBillNo}}</td>
					<td >{{$row->supplier_billdate}}</td>
					<td >{{ Common::fixIt($row->costPrise)}}</td>
					<td >{{ Common::fixIt($row->cp_vat)}}</td>	
					<td >{{ Common::fixQty($row->qty)}}</td>					
					<td >{{ Common::fixIt( $row->qty * ($row->costPrise + (($row->costPrise * $row->cp_vat)/100 ) ) )}}</td>
					<td >{{ Common::fixIt($row->dis_amt)}}</td>	
					<td >{{ Common::fixIt((($row->qty * ($row->costPrise + (($row->costPrise * $row->cp_vat)/100 ) )) - $row->dis_amt))}}</td>	
				</tr>
				@endforeach

				@endif


				<tr>  									
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					@if($intVatType != 0)
					<td></td>
					@endif
					<td></td>
					<td></td>
					<th>@lang('item.qty') = {{ Common::fixQty($p->qty)}}</th>						
					<th>@lang('billing.sub_total') = {{ Common::fixIt($p->amt)}}</th>	
					<th>@lang('billing.dis_amt') = {{ Common::fixIt($p->dis_amt)}}</th>	
					<th>@lang('stock.amt') = {{ Common::fixIt($amt)}}</th>	
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
					@if($intVatType != 0)
					<td></td>
					@endif
					<th></th>					
					<th>@lang('glob.total')</th>
					<th>{{ Common::fixQty($totalQty)}}</th>
					<th>{{ Common::fixIt($totalSubTotal)}}</th>
					<th>{{ Common::fixIt($totalDisAmt)}}</th> 						
					<th>{{ Common::fixIt(($totalSubTotal - $totalDisAmt))}}</th>							
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