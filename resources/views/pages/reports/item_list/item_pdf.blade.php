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
					<th >@lang('glob.id')</th>
					<th >@lang('item.item_name')</th>
					<th >@lang('glob.barcode')</th>	
					<th>@lang('item.refrence_number')</th>
					<th>@lang('glob.category')</th>
					<th>@lang('glob.sub_category')</th>									
					<th >@lang('item.c_p')</th>
					@if(Common::vatType('vat','vat_type')!=0)<th >@lang('item.cp_vat_percent')</th>@endIf
					<th >@lang('item.s_p')</th>
					@if(Common::vatType('vat','vat_type')!=0)<th >@lang('glob.sp_vat')</th>@endIf					
				</tr>
			</thead>
			<tbody >
				@foreach($item as $row)
				<tr>
					<td>{{$row->id}}</td>
					<td>{{$row->itemName}}</td>	
					<td>{{$row->barcode}}</td>
					<td>@if($row->serial_number == null) NA @else {{$row->serial_number}} @endif</td>
					<td>{{$row->categories->parentCategory->categorie}}</td>										
					<td>{{$row->categories->categorie}}</td>
					<td>{{ Common::fixIt($row->costPrise)}}</td>	
					@if(Common::vatType('vat','vat_type')!=0)<td>{{ Common::fixIt($row->cp_vat)}}</td>@endIf
					<td>{{ Common::fixIt($row->salePrise)}}</td>
					@if(Common::vatType('vat','vat_type')!=0)<td>{{ Common::fixIt($row->vat)}}</td>@endIf

				</tr>
				@endforeach	


			</tbody>
		</table>
	</div>
	<!-- Customer Details Table End -->	
	<br/>	
	
</body>
</html>