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
			<label style="font-style: italic; font-weight: bold; font-size:20px;">Brandwise Sales and Sales Return Report</label>
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
	

	<div class="table-responsive" style="width: 100%;">
		<table class="table table-bordered" style="width: 100%;">
			<thead>
				
				<tr>
					<th style="width:10%">#</th>											
					<th>@lang('billing.bill_date')</th>
					<th>@lang('billing.bill_no')</th>	
					<th>Item Name</th>					
					<th>@lang('glob.customer')</th>
					<th>@lang('billing.sub_total') ({{$curr}})</th>
					@if($intVatType != 0)
					<th>@lang('glob.vat') ({{$curr}})</th>
					@endif
					<th>@lang('glob.discount') ({{$curr}})</th>
					<th>@lang('glob.loyalty_discount') ({{$curr}})</th>
					<th>@lang('billing.mode_of_pay')</th>
				</tr>
			</thead>
			<tbody >
				@foreach($purchases as $p)
				<tr>
					<td colspan="10" style="font-weight: bold;
					font-size:10px; background-color: #D1D1D1 ;
					color: #121111 ;">{{$p->name}}</td>
				</tr>
            
				@foreach($p->child as $row)
				<tr>
					<td>{{{ $i++ }}}</td>			
					<td >{{$row->dateTime}}</td>
					<td >@if($p->id!=1) {{$row->BillId}} @else {{Common::fixBill($row->BillId)}} @endif</td>
					<td>{{$row->itemName}}</td>
					<td>{{$row->customerName}}</td>
					<td>{{ Common::fixIt($row->subtotal)}}</td>
					<td>{{ Common::fixIt($row->v_amt)}}</td>
					<td>{{ Common::fixIt($row->dis_amt)}}</td>
					<td>{{ Common::fixIt($row->loyalty_discount)}}</td>
                    <td>{{$row->payment_mode}}</td>
				</tr>
				@endforeach
			
				@if($p->id==1)
				<tr>  									
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<th>@lang('billing.sub_total') = {{ Common::fixIt($saletotal)}}</th>
					<th>@lang('glob.total_s_p') = {{ Common::fixIt($saletotalVat)}}</th>
					<th>@lang('glob.discount') = {{ Common::fixIt($saletotalDiscount)}}</th>
					<th>@lang('glob.loyalty_discount') = {{ Common::fixIt($saletotalLoyDis)}}</th>
					<td></td>
				</tr>
				@else
				<tr>  									
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<th>@lang('billing.sub_total') = {{ Common::fixIt($returntotal)}}</th>
					<th>@lang('glob.total_s_p') = {{ Common::fixIt($returntotalVat)}}</th>
					<th>@lang('glob.discount') = {{ Common::fixIt($returntotalDiscount)}}</th>
					<th>@lang('glob.loyalty_discount') = {{ Common::fixIt($returntotalLoyaltyDiscount)}}</th>
					<td></td>
				</tr>
				@endif
				@endforeach	

			</tbody>			
		</table>
		<br/>
		<br/>
		<br/>

		<table style="width:100%;">
			<tr>
				<td style="width:50%; font-weight: bold;">
					@lang('glob.total') @lang('glob.sales_with_vat') = {{ Common::fixIt(($saletotal)+ ($saletotalVat))}}<br/>
                    
                     @lang('glob.total') @lang('glob.sales_without_vat') = {{ Common::fixIt($saletotal)}}<br/>
				
				</td>
				<td style="width:50%;font-weight: bold; text-align: right;">
					 @lang('glob.total') @lang('glob.sales_return_with_vat')   =  {{Common::fixIt(($returntotal) + ($returntotalVat))}}<br/>
                      @lang('glob.total') @lang('glob.sales_return_without_vat') = {{ Common::fixIt($returntotal)}}<br/>
					
				</td>
			</tr>
		</table>
		
		

	</div>
	<!-- Bank Details Table End -->	
	<br/>
	
</body>
</html>