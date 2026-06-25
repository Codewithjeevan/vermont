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
			<label style="font-style: italic; font-weight: bold; font-size:20px;">Tax Refund Report</label>
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
                    <th>@lang('glob.taxRefundTagNumber')</th>
					<th>@lang('glob.customer')</th>
					<th>@lang('item.item_name')</th>
					<th>@lang('glob.barcode')</th>
					<th>@lang('item.refrence_number')</th>
					<!-- <th>@lang('item.serial_number')</th> -->
					<th>@lang('glob.rate')</th>
					<th>@lang('item.qty')</th>					
					<!-- <th>@lang('billing.dis_amt')({{$curr}})</th>
					<th>@lang('glob.loyalty_discount')({{$curr}})</th> -->
					<th>@lang('glob.grossSale')</th>
					<th>@lang('glob.vatValue')</th>	
					<th>@lang('glob.netSale')({{$curr}})</th>					
				</tr>
			</thead>
			<tbody >
				@foreach($purchases as $p)
				<tr>
					<td colspan="13" style="font-weight: bold;
					font-size:10px; background-color: #D1D1D1 ;
					color: #121111 ;">{{$p->name}}</td>


				</tr>
				<?php $index=1 ?>
				@foreach($p->child as $row)
				<tr>
					<td>{{{ $i++ }}}</td>			
					<td >{{Common::fixBill($row->id)}}</td>
					<td >{{$row->billingDate}}</td>
                    <td >{{$row->taxRefundTagNumber}}</td>
					<td >{{$row->customerName ?? 'NA'}}</td>
					<td >{{$row->itemName}}</td>
					<td >{{$row->barcode}}</td>		
					<td >{{$row->refrence_number  ?? 'NA'}}</td>	
					<!-- <td >{{$row->serial_number  ?? 'NA'}}</td>	 -->
					<td >{{ Common::fixIt($row->rate)}}</td>	
					<td >{{ Common::fixQty($row->qty)}}</td>						
					<!-- <td >{{ Common::fixIt($row->dis_amt)}}</td>
					<td >{{ Common::fixIt($row->loydis_amt)}}</td> -->
					<td >{{ Common::fixIt($row->grossSale)}}</td>
					<td >{{ Common::fixIt($row->vat_value)}}</td>
					<td >{{ Common::fixIt($row->amt - $row->loydis_amt)}}</td>		
				</tr>
				@endforeach	

				<tr>
					<th style="width:10%"></th>											
					<th></th>	
					<th></th>
					<th></th>
                    <th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th>@lang('item.qty') = {{ Common::fixQty($p->qty)}}</th>
										
					<!-- <th>@lang('billing.dis_amt') = {{ Common::fixIt($p->dis_amt)}}</th>
					<th>@lang('glob.loyalty_discount') = {{ Common::fixIt($p->loydis_amt)}}</th> -->
					<th>@lang('glob.grossSale') = {{ Common::fixIt($p->totalgrossSale)}}</th>
					<th>@lang('glob.vatValue') = {{ Common::fixIt($p->totalvatValue)}}</th>
					<th>@lang('glob.netSale') = {{ Common::fixIt($p->amt - $p->loydis_amt )}}</th>					
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
					<!-- <th>{{ Common::fixQty($totalQty)}}</th>		 -->
					<!-- <th>{{ Common::fixIt($totalDis)}}</th>
					<th>{{ Common::fixIt($totalLoyDis)}}</th> -->
					<!-- <th>{{ Common::fixIt($totalGroassSale)}}</th>	 -->
						<th></th>
					<th>@lang('glob.total')</th>	
					<th>{{ Common::fixIt($totalVatValue)}}</th>
                    <th></th>
					<!-- <th>{{ Common::fixIt($total - $totalLoyDis)}}</th>							 -->
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