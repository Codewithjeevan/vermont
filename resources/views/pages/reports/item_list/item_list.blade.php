@extends('layouts.app')

@section('page-title')@lang('glob.item_list')@endsection


@section ('breadcrumb')

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> @lang('glob.dashboard')</a></li>
	<li class="active">@lang('glob.item_list')</li>
</ol>

@endsection

@section('content')


<!-- view table data  -->
<div class="row">
	<div class="col-md-12 table-responsive no-search">
		<div class = "row">
			<div class="col-md-12 margin-bottom">
				{{-- VAT Filter Form start --}}
				@if(Common::vatType('vat','vat_type')!=0)
				<form method="get" action="{{ url('reports/item_list')}}">
					<div class="col-md-12 form-inline margin-bottom no-h-padding">
						<div class="form-group">
							<label for="">VAT:</label>
							<select name="filter" class="form-control input-sm">
								<option value="=" @if(isset($filter) && $filter == '=') {{'selected'}} @endif> =</option>
								<option value=">" @if(isset($filter) && $filter == '>') {{'selected'}} @endif > ></option>
								<option value=">=" @if(isset($filter) && $filter == '>=') {{'selected'}} @endif > >= </option>
								<option value="<" @if(isset($filter) && $filter == '<') {{'selected'}} @endif > <</option>
								<option value="<=" @if(isset($filter) && $filter == '<=') {{'selected'}} @endif><=</option>
							</select>	
						</div>
						<div class="form-group">
							<input type="text" name="vat" class="form-control  input-sm" placeholder="@lang('glob.vat')" value="{{{ Form::getValueAttribute('vat', null) }}}">
						</div>
						<div class="form-group">
							<button class="btn bg-purple btn-sm">Filter Result</button>
						</div>
					</div>
				</form>
				@endIf
				{{-- VAT Filter form end --}}
			</div>
		</div>
		<div class="row">
			<div class="col-md-offset-6 col-md-6 pull-right">
				<form id="form-search-item" class="form-inline" methode="GET">
					<div class="form-group pull-right margin" style="margin-bottom: 10px;">
						<label for="search">Search :</label> 
						<input type="text" name="searchkey" value="{{ $searchkey }}" class="form-control input-sm " placeholder="search" >
					</div>
				</form>
			</div>
		</div>
		<table class="table table-bordered table-striped datatable-paging-false ">

			<thead>
				<tr>
					<th style="width:2%">#</th>
					<th>@lang('glob.item') @lang('glob.name')</th>
					<th>@lang('glob.barcode')</th>
					<th>@lang('item.refrence_number')</th>
					<th>@lang('glob.category')</th>
					<th>@lang('glob.sub_category')</th>
					<th>@lang('glob.cost_price')</th>
					@if(Common::vatType('vat','vat_type')!=0)<th>@lang('item.cp_vat_percent')</th>@endIf
					<th>@lang('glob.sale_price')</th>
					@if(Common::vatType('vat','vat_type')!=0)<th>@lang('glob.vat')(%)</th>@endIf
					
				</tr>
			</thead>

			<tbody>

				@foreach($items as $row)
				<tr>
					<td>{{ $row->id }}</td>
					<td>{{ $row->itemName }}</td>
					<td>{{ $row->barcode }}</td>
					<td>{{ (isset($row->serial_number)) ? $row->serial_number : 'NA'  }}</td>
					<td>{{(isset($row->categories->parentCategory->categorie)) ? $row->categories->parentCategory->categorie : '' }}</td>
					<td>{{ (isset($row->categories->categorie)) ? $row->categories->categorie : ''  }}</td>
					<td>{{  Common::fixIt($row->costPrise)}}</td>
					@if(Common::vatType('vat','vat_type')!=0)<td>{{  Common::fixIt($row->cp_vat) }}</td>@endIf
					<td>{{  Common::fixIt($row->salePrise) }}</td>
					@if(Common::vatType('vat','vat_type')!=0)<td>{{  Common::fixIt($row->vat) }}</td>@endIf
					
				</tr>
				@endforeach
			</tbody>

			<tfoot class="showSearch">
				<tr>
					<th style="width:2%">#</th>
					<th class="search">@lang('glob.item') @lang('glob.name')</th>
					<th class="search">@lang('glob.barcode')</th>
					<th class="search">@lang('item.refrence_number')</th>
					<th class="search">@lang('glob.category')</th>
					<th class="search">@lang('glob.sub_category')</th>
					<th class="search">@lang('glob.cost_price')</th>
					@if(Common::vatType('vat','vat_type')!=0)<th class="search">@lang('item.cp_vat_percent')</th>@endIf
					<th class="search">@lang('glob.sale_price')</th>
					@if(Common::vatType('vat','vat_type')!=0)<th class="search">@lang('glob.vat')(%)</th>@endIf
				</tr>
			</tfoot>

		</table>
		<div class="row">
			<div class="col-md-12">
				{{ $items->appends(['searchkey' => $searchkey,'filter' =>$filter,'vat'=>$vat ])->links() }}
			</div>
		</div>

	</div>
	<!--Table End-->

</div>

<!--Export Buttons-->
<div class="row export-btn">
	
	<div class="col-md-2 col-sm-4 ">
		<a  class="btn btn-success btn-block " href="{{ url('reports/item_list/xls?filter='.$filter.'&vat='.$vat.'&searchkey='.$searchkey) }}" ><i class="fa fa-file-excel-o" aria-hidden="true"></i> @lang('glob.export_to_xls')</a>		
	</div>
	<div class="col-md-2 col-sm-4 ">			
		<a  class="btn btn-danger btn-block " href="{{ url('reports/item_list/pdf?filter='.$filter.'&vat='.$vat.'&searchkey='.$searchkey) }}" ><i class="fa fa-file-pdf-o" aria-hidden="true"></i> @lang('glob.export_to_pdf') </a>	
	</div>
	<div class="col-md-8"></div>

</div>	
<!--End Export Buttons-->

@endsection