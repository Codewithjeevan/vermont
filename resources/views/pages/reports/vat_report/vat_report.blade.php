@extends('layouts.app')

@section('page-title')@lang('glob.vat_report')@endsection

@section('page-subtitle')@endsection 

@section ('breadcrumb')

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-home"></i> @lang('glob.home')</a></li>
	<li><a href="#"><i class="fa fa-file-text-o"></i> @lang('menu.reports')</a></li>
	<li class="active">@lang('glob.vat_report')</li>
</ol>

@endsection

@section('content')

<div class="vat_report">
	
	
	{{ Form::open(['action'=>'VatReportController@exportTo','id'=>'VatReportForm']) }}
	<div class="row customer-box"> 

		<div class="col-md-12">

			<div class="box box-primary"> 

				<div class="box-body">

					<!-- .row -->
					<div class="row">

						<div class="col-md-3">
							<div class="form-group">
								<label>@lang('glob.from')<span style="color:red">*</span></label>
								<div class="input-group">
									<div class="input-group-addon">
										<i class="ion ion-calendar"></i>
									</div>
									<input id="from" type="text" name="from" class="form-control  input-sm datepicker" placeholder="@lang('glob.from')" required="" v-model="dateArray.fromDate" v-on:change="checkValidation($event)">
								</div>
							</div>
						</div>	
						<div class="col-md-3">
							<div class="form-group">
								<label>@lang('glob.to')<span style="color:red">*</span></label>
								<div class="input-group">
									<div class="input-group-addon">
										<i class="ion ion-calendar"></i>
									</div>
									<input id="to" type="text" name="to" class="form-control  input-sm datepicker" placeholder="@lang('glob.to')" required="" v-model="dateArray.toDate" v-on:change="checkValidation($event)">
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<button type="button" :disabled="(dateArray.fromDate !='' && dateArray.toDate !=0 )? false : true" v-on:click="getLedger($event)"  class="btn btn-success btn-circle" style="margin-top: 20px;"> @lang('glob.show')</button>
								
							</div>
						</div>	
					</div>	
					<!-- .row -->				
				</div>				

			</div>
			<!-- .box .box-primary -->

		</div>
		<!-- .col-md-12 -->		
	</div>
	<!--end .row .customer-box -->					
	<!-- view table data  -->
	<div class="row">

		<div class="table-spinner" style="display: none;margin-top: 225px;
		margin-left: 150px;
		">
		<i class="fa fa-spinner fa-pulse fa-spin  fa-4x fa-fw"></i>
		<span class="sr-only">Loading...</span>
	</div>

	<div class="col-md-12 table-responsive">
		<table class="table table-bordered table-striped purchase-table">
			<thead>
				<tr>
					<th>@lang('glob.vat_report')</th>	
					
				</tr>
			</thead>


			<tbody>
				
			</tbody>
			
			<tfoot >
				<tr>
					<th>@lang('glob.vat_report')</th>	
					
				</tr>
			</tfoot>
		</table>

	</div>

</div>
<!-- view table data end -->	
<br/><br/>
<div class="row">
	<div class="col-md-4">
		<label>@lang('glob.total_purchase_qty')  = @{{fixQty(totalPurchaseQty)}}</label>
	</div>
	<div class="col-md-5"></div>	
	<div class="col-md-3">
		<label>@lang('glob.total_sale_qty')   = @{{fixQty(totalSaleQty)}}</label>
	</div>	
</div>
<div class="row">
	<div class="col-md-4">
		<label>@lang('glob.total_purchase_price') (@{{curr}})  = @{{fixtIt(totalPurchasePrice)}}</label>
	</div>	
	<div class="col-md-5"></div>	
	<div class="col-md-3">
		<label>@lang('glob.total_sale_price') (@{{curr}}) = @{{fixtIt(totalSalePrice)}}</label>
	</div>
</div>
<div class="row">
	<div class="col-md-4">
		<label>@lang('glob.total_vat_paid') (@{{curr}})  = @{{fixtIt(totalPurchaseVat)}}</label>
	</div>	
	<div class="col-md-5"></div>
	<div class="col-md-3">
		<label>@lang('glob.total_vat_received') (@{{curr}})  = @{{fixtIt(totalSaleVat)}}</label>
	</div>		
</div>
<br/><br/>
<div class="row export-btn">

	<div class="col-md-2 col-sm-4 ">
		<button type ="submit" class="btn btn-success btn-block " name="xls" value="xls" :disabled="purchases.length == 0 ? true:false" ><i class="fa fa-file-excel-o" aria-hidden="true" ></i> @lang('glob.export_to_xls')</button> 
		
	</div>
	<div class="col-md-2 col-sm-4">			
		<button type ="submit" class="btn btn-danger btn-block " name="pdf" value="pdf" :disabled="purchases.length == 0 ? true:false" ><i class="fa fa-file-pdf-o" aria-hidden="true" ></i> @lang('glob.export_to_pdf') </button>				
	</div>
	<div class="col-md-2 col-sm-4">			
		<button type="button" class="btn btn-primary btn-block " name="print" value="print" :disabled="purchases.length == 0 ? true:false"  v-on:click="printPage($event)"><i class="fa fa-print" aria-hidden="true" ></i> @lang('glob.print') </button>				
	</div>
	
	

</div>	
<!--End Export Buttons-->

{{ Form::close() }}
</div>
@endsection

