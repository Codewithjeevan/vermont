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
			<label style="font-style: italic; font-weight: bold; font-size:20px;">Item Report</label>
			<br/>		
			<br/>		
		</center>
	</div>
	

	<!-- Customer Details Table -->
	<div class="table-responsive" style="width: 100%;">
		<table class="table table-bordered" style="width: 100%;">
			<thead>
				<tr>
					<th >Sr.No</th>
					<th >Item Name</th>
					<th >Item Code</th>
					<th>Reference Number</th>
					<th >Category</th>
					<th>Sub-Category</th>
					<th >Unit</th>
					<th >Qty</th>
					<th >Selling Price</th>
					@if(Common::vatType('vat','vat_type')!=0)<th >SP VAT(%)</th>@endIf
					<th >Cost Price</th>
					@if(Common::vatType('vat','vat_type')!=0)<th >CP VAT(%)</th>@endIf
					<th >@lang('glob.min')</th>
					<th >@lang('glob.max')</th>
					<th >@lang('glob.product')</th>
					<th >@lang('glob.description')</th>
					<th >@lang('glob.created_date')</th>		


				</tr>
			</thead>
			<tbody >
				@foreach($item as $row)
				<tr>				
					<td>{{ $i++ }}</td>
					<td>{{$row->itemName}}</td>
					<td>{{$row->barcode}}</td>	
					<td>@if($row->serial_number == null) NA @else {{$row->serial_number}} @endif</td>
					<td>{{$row->categories->parentCategory->categorie}}</td>
					<td>{{$row->categories->categorie}}</td>
					<td>{{ (isset($row->units->unit)) ? $row->units->unit : '' }}</td>
					<td>{{ Common::fixQty($row->stock) }}</td>
					<td>{{ Common::fixIt($row->salePrise) }}</td>
					@if(Common::vatType('vat','vat_type')!=0)<td>{{ Common::fixIt($row->vat) }}</td>@endIf
					<td>{{ Common::fixIt($row->costPrise) }}</td>	
					@if(Common::vatType('vat','vat_type')!=0)<td>{{ Common::fixIt($row->cp_vat) }}</td>@endIf
					<td>{{ Common::fixQty($row->min) }}</td>
					<td>{{ Common::fixQty($row->max) }}</td>
					<td>{{ $row->productCode }}</td>
					<td>{{ (isset($row->description)) ? $row->description : '' }}</td>
					<td>{{ (isset($row->created_at)) ? $row->created_at->format('F d, Y') : '' }}</td>										
				</tr>
				@endforeach	


			</tbody>
		</table>
	</div>
	<!-- Customer Details Table End -->	
	<br/>	
	
</body>
</html>