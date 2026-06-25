@extends('layouts.app')

@section('page-title')@lang('glob.expiry_list')@endsection


@section ('breadcrumb')

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> @lang('glob.dashboard')</a></li>
	<li class="active">@lang('glob.expiry_list') </li>
</ol>

@endsection

@section('content')
{{ Form::open(['action'=>'ItemsController@exportToExpiry','id'=>'expiryListForm']) }}
<div class="expiry_list">

	<!-- filter for this month/all -->
	<div class="row">
		<div class="col-md-2 margin-bottom">
			<div class="form-group ">
				<label for="account">@lang('glob.filter')<span style="color:red">*</span></label>
					<select name="expiryDropdown" id="expiryDropdown" class="form-control input-sm" v-on:change="getExpiryList()">
						<option value="0">This Month</option>
						<option value="1">All</option>									
					</select>
			</div>								
		</div>
	</div>
	<!--End filter for this month/all  -->


	<!-- view table data  -->
	<div class="row">
		<div class="col-md-12 table-responsive">

			<table class="table table-bordered table-striped expiryTbl" id="expiryTbl" >

				<thead>
					<tr>
						<th > # </th>
						<th> @lang('glob.item') @lang('glob.name') </th>
						<th> @lang('stock.mrn_no') </th>						
						<th> @lang('glob.expiry_date') </th>
					</tr>
				</thead>

				<tbody>

					

				</tbody>

				<tfoot class="showSearch">
					<tr>
						<th >#</th>
						<th class="search"> @lang('glob.item') @lang('glob.name') </th>
						<th class="search"> @lang('stock.mrn_no') </th>						
						<th class="search"> @lang('glob.expiry_date') </th>
					</tr>
				</tfoot>

			</table>

		</div>

	</div>

	<!--Table End-->

	<!--Export Buttons-->
	<div class="row export-btn">
		
		<div class="col-md-2 col-sm-4 ">
		<button type="submit" name="xls" value="xls" class="btn btn-success btn-block " :disabled="(expiryList.length > 0) ? false :true" ><i class="fa fa-file-excel-o" aria-hidden="true"></i> @lang('glob.export_to_xls')</button>		
		</div>
		<div class="col-md-2 col-sm-4 ">			
			<button type="submit" name="pdf" value="pdf" class="btn btn-danger btn-block " :disabled="(expiryList.length > 0) ? false :true" ><i class="fa fa-file-pdf-o" aria-hidden="true"></i> @lang('glob.export_to_pdf') </button>	
		</div>
		<div class="col-md-8"></div>
		
	</div>	
	<!--End Export Buttons-->

</div>
{{ Form::close() }}
@endsection