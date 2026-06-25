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
			<label style="font-style: italic; font-weight: bold; font-size:20px;">Stock Ledger Report</label>
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
					<th style="width:10%">@lang('glob.id')</th>											
					<th>@lang('billing.bill_date')</th>
					<th>@lang('glob.perticulars')</th> 						
					<th>@lang('glob.purchase')</th>
					<th>@lang('glob.sale')</th>
					<th>@lang('glob.adjustment')</th>
					<th>@lang('glob.balance_stock')</th>							
				</tr>
			</thead>
			<tbody >

				@foreach($purchases as $p)	
				<?php $index=1 ;
				$perticulars = '';				
				$balance_stock =0;
				if($p->balanceStock == null){
					$initial_stock=0;

					$balance_stock = 0;
				}else{
					$initial_stock=$p->balanceStock;

					$balance_stock = $p->balanceStock;
				}
				?>			
				<tr>
					<td colspan="6" style="font-weight: bold;
					font-size:10px; background-color: #D1D1D1 ;
					color: #121111 ;">{{{$p->itemName}}}</td>
					<td colspan="1" style="font-weight: bold;
					font-size:10px; background-color: #D1D1D1 ;
					color: #121111 ;">@lang('glob.balance_stock') = {{Common::fixQty($balance_stock)}}</td>

				</tr>
				

				@foreach($p->child as $row)
				<?php $perticulars = '';	
				$purchase = $row->openingStock+$row->purchasedQty+$row->bilReturnQty;
				$sale = $row->purchaseReturnQty+$row->issueStockQty+$row->damageStockQty+$row->billingQty+$row->packegeQty+$row->offerQty+$row->expiryQty+$row->billDeletedQty;	
				$adjustment = $row->stockAdjustmentQty;
				
				if(($row->type)==1){
					$balance_stock = $balance_stock + ($row->openingStock);
					$perticulars = "Opening Stock No: ".$row->type_id." Date ".$row->date;
				}elseif (($row->type)==2) {
					$balance_stock = $balance_stock + ($row->purchasedQty);
					$perticulars = "Purchase MRN No: ".$row->type_id." Bill Date ".$row->date;
				}
				elseif (($row->type)==3) {
					$balance_stock = $balance_stock - ($row->purchaseReturnQty);
					$perticulars = "Purchase Return MRN No: ".$row->type_id." Supplier BillNo: ".$row->billno." Bill Date ".$row->date;
				}
				elseif (($row->type)==4) {
					$balance_stock = $balance_stock - ($row->issueStockQty);
					$perticulars = "Display Stock MRN No: ".$row->type_id." Bill Date ".$row->date;
				}
				elseif (($row->type)==5) {
					$balance_stock = $balance_stock - ($row->damageStockQty);
					$perticulars = "Damage MRN No: ".$row->type_id." Bill Date ".$row->date;
				}				
				elseif (($row->type)==6) {
					$balance_stock = $balance_stock - ($row->billingQty);
					$perticulars = "Billing No: ". Common::fixBill($row->type_id)." Bill Date ".$row->date;
				}
				elseif (($row->type)==7) {
					$balance_stock = $balance_stock - ($row->packegeQty);
					$perticulars = "Packege MRN No: ".Common::fixBill($row->type_id)." Bill Date ".$row->date;
				}
				elseif (($row->type)==8) {
					$balance_stock = $balance_stock - ($row->offerQty);
					$perticulars = "Offer MRN No: ".Common::fixBill($row->type_id)." Bill Date ".$row->date;
				}
				elseif (($row->type)==9) {
					$balance_stock = $balance_stock + ($row->bilReturnQty);
					$perticulars = "Sale Return Billing No: ".Common::fixBill($row->type_id)." Bill Date ".$row->date;
				}
				elseif (($row->type)==10) {
					$balance_stock = $balance_stock - ($row->expiryQty);
					$perticulars = "Stock Expiry No: ".$row->type_id." Expity Date ".$row->date;
				}elseif (($row->type)==11) {
					$balance_stock = $balance_stock - ($row->billDeletedQty);
					$perticulars = "Bill Deleted!  Billing No: ".Common::fixBill($row->type_id)." Bill Date ".$row->date;
				}elseif (($row->type)==18) {
					$balance_stock = $balance_stock + ($row->stockAdjustmentQty);
					$perticulars = "Stock Adjustment Id : ".$row->type_id." Date ".$row->date;
				}
				?>
				<tr>
					<td>{{{ $index++ }}}</td>			
					<td class="text-right">{{{ $row->date }}}</td>
					<td>{{{ $perticulars }}}</td>
					<td class="text-right">{{{  Common::fixQty($purchase)}}}</td>
					<td class="text-right">{{{  Common::fixQty($sale)}}}</td>
					<td class="text-right">{{{  Common::fixQty($adjustment)}}}</td>
					<td class="text-right">{{{  Common::fixQty($balance_stock)}}}</td>			

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