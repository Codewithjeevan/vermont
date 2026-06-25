@extends('layouts.app')

@section('page-title')@lang('glob.sales_return')@endsection

@section('page-subtitle')@endsection 

@section ('breadcrumb')

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-home"></i> @lang('glob.home')</a></li>
	<li><a href="#"><i class="fa fa-file-text-o"></i> @lang('menu.sales_reports')</a></li>
	<li class="active">@lang('glob.sales_return')</li>
</ol>

@endsection

@section('content')
{{ Form::open(['action'=>'SalesReportController@exportToSalesReturn','id'=>'customer_salesReportForm']) }}
<div class="sales_return" v-cloak>

	<div class="row customer-box" > 

		<div class="col-md-12">

			<div class="box box-primary"> 

				<div class="box-body">

					<!-- .row -->
					<div class="row">
						
						
						<!-- From Date -->
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
						<!-- End From Date -->	
						<!-- To Date -->
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
						<!-- End To Date -->

						<div class="col-md-3" style="margin-top: 2%;">
							<button type="button" :disabled="(dateArray.fromDate !='' && dateArray.toDate !=0 )? false : true" v-on:click="getLedger($event)"  class="btn btn-success btn-circle" > @lang('glob.show')</button>
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

		<div class="col-md-12 table-responsive">
			<table class="table table-bordered table-striped sales-return-table">
				<thead>
					<tr>
						<th>#</th>		
						<th>@lang('glob.sale_return_date')</th>
						<th>@lang('glob.sale_return_no')</th>					
						<th>@lang('glob.customer')</th>
						<th>@lang('billing.bill_no')</th>	
						<th>@lang('billing.sub_total') (@{{curr}})</th>
						<th v-if="intVatType!=0">@lang('glob.vat') (@{{curr}})</th>
						<th>@lang('glob.discount') (@{{curr}})</th>
						<th>@lang('billing.loyalty_discount') (@{{curr}})</th>
						<th>@lang('glob.mode')</th>
						<th>@lang('billing.cash_amt') (@{{curr}})</th>	
						<th>@lang('billing.card_amt') (@{{curr}})</th>	
						
					</tr>
				</thead>


				<tbody>
					
				</tbody>
				
				<tfoot class="showSearch">
					<tr>
						<th>#</th>	
						<th class="search">@lang('glob.cash_memo_date')</th>
						<th class="search">@lang('glob.sale_return_no')</th>					
						<th class="search">@lang('glob.customer')</th>
						<th class="search">@lang('billing.bill_no')</th>			
						<th>@lang('billing.sub_total') = <label style="font-size:12px; font-weight: bold;"> @{{total}} </label></th>
						<th  v-if="intVatType!=0">@lang('glob.vat') @lang('glob.total') = <label style="font-size:12px; font-weight: bold;"> @{{floatTotalVatAmt}} </label></th>
						<th>@lang('glob.discount') = <label style="font-size:12px; font-weight: bold;"> @{{totalDiscount}} </label></th>
						<th>@lang('billing.loyalty_discount') = <label style="font-size:12px; font-weight: bold;"> @{{totalLoyaltyDiscount}} </label></th>
						<th class="search">@lang('glob.mode')</th>
						<th>@lang('billing.cash_amt') = <label style="font-size:12px; font-weight: bold;"> @{{totalCash}} </label></th>	
						<th>@lang('billing.card_amt') = <label style="font-size:12px; font-weight: bold;"> @{{totalCard}} </label></th>				
					</tr>
				</tfoot>
			</table>

		</div>

	</div>
	<!-- view table data end -->	

	<!-- /.box-body -->
	

	<!--Export Buttons-->
	<div class="row export-btn">

		<div class="col-md-2 col-sm-4">
			<button type ="submit" class="btn btn-success btn-block " name="xls" value="xls" :disabled="itemList.length == 0 ? true:false"><i class="fa fa-file-excel-o" aria-hidden="true" ></i> @lang('glob.export_to_xls')</button> 
			
		</div>
		<div class="col-md-2 col-sm-4">			
			<button type ="submit" class="btn btn-danger btn-block " name="pdf" value="pdf" :disabled="itemList.length == 0 ? true:false" ><i class="fa fa-file-pdf-o" aria-hidden="true" ></i> @lang('glob.export_to_pdf') </button>				
		</div>
		<div class="col-md-2 col-sm-4">			
			<button type="button" class="btn btn-primary btn-block " name="print" value="print" :disabled="purchases.length == 0 ? true:false"  v-on:click="printPage($event)"><i class="fa fa-print" aria-hidden="true" ></i> @lang('glob.print') </button>				
		</div>
		<div class="col-md-6"></div>
		

	</div>	
	<!--End Export Buttons-->
</div>
{{ Form::close() }}
@endsection

