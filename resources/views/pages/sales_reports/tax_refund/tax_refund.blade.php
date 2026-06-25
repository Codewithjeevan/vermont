@extends('layouts.app')

@section('page-title')@lang('glob.tax_refund')@endsection

@section('page-subtitle')@endsection 

@section ('breadcrumb')

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-home"></i> @lang('glob.home')</a></li>
	<li><a href="#"><i class="fa fa-file-text-o"></i> @lang('menu.sales_reports')</a></li>
	<li class="active">@lang('glob.tax_refund')</li>
</ol>

@endsection

@section('content')
{{ Form::open(['action'=>'SalesReportController@exportToTaxRefund','id'=>'taxRefundReportForm']) }}
<div class="tax_refund" v-cloak>

	<div class="row customer-box"> 

		<div class="col-md-12">

			<div class="box box-primary"> 

				<div class="box-body">

					<!-- .row -->
					<div class="row">
						
						
						<div class="col-md-3">
							<div class="form-group no-v-padding no-v-margin">
								<label for="account">@lang('glob.category')<span style="color:red">*</span></label>
								<v-select  :options="categories" :text="'categorie'" v-model="category" :vrequired="true" :vname="'category'" v-on:vchange="getSubcat(category,$event)"></v-select>
								
							</div>								
						</div>	

						<div class="col-md-3">
							<div class="form-group no-v-padding no-v-margin">
								<label for="account">@lang('glob.sub_category')<span style="color:red">*</span></label>
								<v-select  :options="sub_categories" :text="'categorie'" v-model="sub_category"  :vrequired="true" :vname="'sub_category'" v-on:vchange="getCat(sub_category,$event)"></v-select>
								
							</div>								
						</div>	
						<!-- End Salesman Dropdown -->					

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
							<button id="show" type="button" :disabled="(category !== '' && sub_category !== '' && dateArray.fromDate !=='' && dateArray.toDate !=='' )? false : true" v-on:click="getLedger($event)"  class="btn btn-success btn-circle" > @lang('glob.show')</button>
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
			<table class="table table-bordered table-striped cash_memo_cat-table">
				<thead>
					<tr>
						<th>@lang('glob.sub_category')</th>

					</tr>
				</thead>


				<tbody>
					
				</tbody>
				
				<tfoot class="showSearch">
					<tr>
						<th class="search">@lang('glob.sub_category')</th>
					</tr>
				</tfoot>
			</table>

		</div>

	</div>
	<!-- view table data end -->	

	<!-- <div class="row">
		<div class="col-md-8">
			<label>@lang('glob.total') @lang('item.qty') = @{{fixQty(totalQty)}}</label>
		</div>		
	</div> -->
	
	<!-- <div class="row">
		<div class="col-md-8">
			<label>@lang('glob.total') @lang('billing.dis_amt') (@{{curr}}) = @{{fixtIt(totalDis)}}</label>
		</div>		
	</div> -->

	<!-- <div class="row">
		<div class="col-md-8">
			<label>@lang('glob.total') @lang('glob.loyalty_discount') (@{{curr}}) = @{{fixtIt(totalLoyDis)}}</label>
		</div>		
	</div> -->

    <!-- <div class="row">
		<div class="col-md-8">
			<label>@lang('glob.total') @lang('glob.grossSale') (@{{curr}}) = @{{fixtIt(totalGroassSale)}}</label>
		</div>		
	</div> -->

    <div class="row">
		<div class="col-md-8">
			<label>@lang('glob.total') @lang('glob.vatValue') (@{{curr}}) = @{{fixtIt(totalVatValue)}}</label>
		</div>		
	</div>

	<!-- <div class="row">
		<div class="col-md-8">
			<label>@lang('glob.total') @lang('glob.netSale') (@{{curr}}) = @{{fixtIt(total - totalLoyDis)}}</label>
		</div>		
	</div> -->
	
	<!-- /.box-body -->
	
	


	<!--Export Buttons-->
	<div class="row export-btn">

		<div class="col-md-2 col-sm-4 ">
			<button type ="submit" class="btn btn-success btn-block " name="xls" value="xls" :disabled="purchases.length == 0 ? true:false" ><i class="fa fa-file-excel-o" aria-hidden="true" ></i> @lang('glob.export_to_xls')</button> 
			
		</div>
		<div class="col-md-2 col-sm-4 ">			
			<button type ="submit" class="btn btn-danger btn-block " name="pdf" value="pdf" :disabled="purchases.length == 0 ? true:false" ><i class="fa fa-file-pdf-o" aria-hidden="true" ></i> @lang('glob.export_to_pdf') </button>				
		</div>
		<div class="col-md-2 col-sm-4 ">			
			<button type="button" class="btn btn-primary btn-block " name="print" value="print" :disabled="purchases.length == 0 ? true:false"  v-on:click="printPage($event)"><i class="fa fa-print" aria-hidden="true" ></i> @lang('glob.print') </button>				
		</div>
		<div class="col-md-6"></div>
		

	</div>	
	<!--End Export Buttons-->
</div>
{{ Form::close() }}
@endsection

