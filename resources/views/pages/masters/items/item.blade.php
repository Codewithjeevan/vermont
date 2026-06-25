@extends('layouts.app')

@section('page-title')@lang('glob.item')@endsection

@section('page-subtitle')@lang('item.add_edit_item')@endsection

@section ('breadcrumb')
<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> @lang('glob.dashboard')</a></li>
	<li class="active">@lang('glob.item')</li>
</ol>
@endsection

@section('content')
<div class="item">

	@if(Common::can_create('item'))

	<!-- Button div -->
	<div class="row">
		<div class="col-md-6 margin-bottom">
			<a  class="btn bg-purple new-customer" v-on:click="openPanel" >@lang('glob.add_new')</a>
			<a class="btn btn-indigo horizontal-margin" data-toggle="modal" data-target="#purchase-import" v-on:click="resetImported"> <i class="fa fa-upload" aria-hidden="true"></i> @lang('glob.import')</a>
		</div>
		<div class="col-md-6 dropdown margin-bottom ">
			<button class="btn btn-success dropdown-toggle pull-right horizontal-margin" type="button" data-toggle="dropdown" >Export
				<span class="caret" ></span></button>
				<ul class="dropdown-menu pull-right">
					<li><a href="{{ url('master/items/xls?filter='.$filter.'&vat='.$vat.'&searchkey='.$searchkey ) }}">Excel</a></li>
					<li><a href="{{ url('master/items/pdf?filter='.$filter.'&vat='.$vat.'&searchkey='.$searchkey ) }}">PDF</a></li>				
				</ul>				

				<a href="{{ asset('sample/import_item.xlsx') }}" class="btn btn-primary  horizontal-margin pull-right" download="" >
					<strong><i class="fa fa-file-excel-o" aria-hidden="true"></i> Sample</strong>
				</a>
			</div>		
		</div>
		<!-- end add button -->
      
		<div class = "row" v-show="show_filter">
			<div class="col-md-12 margin-bottom">
				{{-- VAT Filter Form start --}}
				   @if(Common::vatType('vat','vat_type')!=0)
				<form method="get" action="{{ url('master/item')}}">
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
							<input type="text" name="vat" class="form-control  input-sm" placeholder="VAT" value="{{{ Form::getValueAttribute('vat', null) }}}">
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

		<!-- Button div end -->
		<div id="itemFormDiv" class="collapse  @if (isset($editItem))  in @endif ">

			<!-- item create Form start -->
			@include("pages.masters.items.item-create")
			<!-- item create Form end -->

		</div>

		@endif
		<!-- view table data  -->
		@include("pages.masters.items.item-table")
		<!-- view table data end -->

		<!--Export Buttons-->
		<div class="row">
			
			<div class="col-md-12">
				@include('pages.masters.items.item-import')
			</div>
			

		</div>
		<!--End Export Buttons-->
	</div>
	@endsection

