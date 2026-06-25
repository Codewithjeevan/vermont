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
			<label style="font-style: italic; font-weight: bold; font-size:20px;">Promotion Report</label>
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
					<th>@lang('billing.bill_no')</th>	
					<th>@lang('billing.bill_date')</th>
					<th>@lang('item.item_name')</th>						
					<th>@lang('item.qty')</th>
					<th>@lang('glob.rate')({{$curr}})</th>
					<th>@lang('glob.offer_item')</th>
					<th>@lang('glob.offer_qty')</th> 						
					<th>@lang('glob.promo_rate')({{$curr}})</th>						
				</tr>
			</thead>
			<tbody >
				@foreach($purchases as $p)
				@if($p->totalQty > 0)
				<tr>
					<td colspan="9" style="font-weight: bold;
					font-size:10px; background-color: #D1D1D1 ;
					color: #121111 ;">{{$p->name}}</td>					

				</tr>
				<?php $index=1 ?>
				@foreach($p->child as $row)
				<?php 
				$OfferQty=0;
				$returnQty=0;
				$offerReturnQty=0;
				
				if($row->returnQty != null)
					{$returnQty = $row->returnQty;}
				if($row->offerReturnQty != null)
					{$offerReturnQty=$row->offerReturnQty;}
				
			 	if($row->OfferQty == null){
			 	$OfferQty = "0";
			 	}else{
			 	$OfferQty = $row->OfferQty;
			 	}
				if($OfferQty != 0){
			 	$OfferQty = $OfferQty - $offerReturnQty;
			 	}
			 	if(($row->qty - $returnQty) > 0) {?>
				<tr>
					<td>{{{ $i++ }}}</td>			
					<td >{{Common::fixBill($row->id)}}</td>
					<td >{{$row->dateTime}}</td>
					<td >{{$row->ItemName}}</td>
					<td >{{ Common::fixQty(($row->qty) - $returnQty)}}</td>
					<td >{{ Common::fixIt($row->originalRate)}}</td>
					<td >@if($row->offerItemName != null){{{$row->offerItemName}}}@else{{{"NA"}}}@endif</td>
					<td>{{ Common::fixQty($OfferQty)}}</td>
					
					<td >{{ Common::fixIt($row->rate)}}</td>				
				</tr>
				<?php } ?>
				@endforeach	

				<tr>
					<th></th>											
					<th></th>	
					<th></th>
					<th></th>
					<th>@lang('item.qty') = {{ Common::fixQty($p->totalQty)}}</th> 						
					<th>@lang('glob.total')({{$curr}}) = {{ Common::fixIt($p->total)}}</th>	
					<th></th>
					<th>@lang('glob.offer_qty') = {{ Common::fixQty($p->offerTotalQty)}}</th> 			
					<th>@lang('glob.total')({{$curr}}) = {{ Common::fixIt($p->offerTotal)}}</th> 						
				</tr>

				@endif
				@endforeach	

			</tbody>
			<tfoot >
				@if($totalQty > 0)
				<tr>
					<th></th>											
					<th></th>	
					<th></th>
					<th>@lang('glob.total')</th>
					<th>{{ Common::fixQty($totalQty)}}</th> 						
					<th>{{ Common::fixIt($total)}}</th>	
					<th></th>
					<th>{{ Common::fixQty($offerTotalQty)}}</th> 			
					<th>{{ Common::fixIt($offerTotal)}}</th>	 						
				</tr>
				@endif
			</tfoot>
		</table>
	</div>
	<!-- Bank Details Table End -->	
	<br/>

	<div class="row">

		
		
	</div>	
	
</body>
</html>