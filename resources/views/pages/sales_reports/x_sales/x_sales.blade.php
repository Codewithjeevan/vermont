@extends('layouts.app')

@section('page-title')@lang('glob.x_sales')@endsection

@section('page-subtitle')@endsection 

@section ('breadcrumb')

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-home"></i> @lang('glob.home')</a></li>
	<li><a href="#"><i class="fa fa-file-text-o"></i> @lang('menu.sales_reports')</a></li>
	<li class="active">@lang('glob.x_sales')</li>
</ol>

@endsection

@section('content')
{{ Form::open(['action'=>'SalesReportController@exportToXSales','id'=>'customer_salesReportForm']) }}
<div class="x_sales" v-cloak>

	<div class="row customer-box"> 

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
									
									<input id="from" type="text" name="from" class="form-control input-sm date-time-picker" placeholder="{{ trans('glob.from') }}" v-model="dateArray.fromDate" v-on:change="checkValidation($event)">
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
									
									<input id="to" type="text" name="to" class="form-control input-sm date-time-picker" placeholder="{{ trans('glob.to') }}" v-model="dateArray.toDate" v-on:change="checkValidation($event)">
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

	<div class="row">
		<div class="col-md-8">
			<label>@lang('glob.opening_amt') = @{{fixtIt(openingCash)}}</label>
		</div>		
	</div>

	<!-- view table data  -->
	<div class="row">

		<div class="col-md-12 table-responsive">
			<table class="table table-bordered table-striped x-sales-table">
				<thead>
					<tr>
						<th>#</th>	
						<th>@lang('billing.bill_date')</th>
						<th>@lang('billing.bill_no')</th>							
						<th>@lang('billing.amt') (@{{curr}})</th>									
					</tr>
				</thead>


				<tbody>
					
				</tbody>
				
				<tfoot class="showSearch">
					<tr>
						<th >#</th>	
						<th class="search">@lang('billing.bill_date')</th>
						<th class="search">@lang('billing.bill_no')</th>							
						<th >@lang('billing.amt') = @{{billAmt}}</th>	
						
					</tr>
				</tfoot>
			</table>

		</div>

	</div>
	<!-- view table data end -->	

	<div class="row">
		<div class="col-md-8">
			<label>@lang('glob.opening_amt') + @lang('glob.sale') (@{{curr}})= @{{fixtIt(Number(openingCash) + Number(billAmt))}}</label>
		</div>		
	</div>

	<div class="row">
		<div class="col-md-8">
			<label>@lang('billing.cash') (@{{curr}})= @{{fixtIt(cashAmt)}}</label>
		</div>		
	</div>

	<div class="row">
		<div class="col-md-8">
			<label>@lang('billing.credit_card') (@{{curr}})= @{{fixtIt(cardAmt)}}</label>
		</div>		
	</div>

	<div class="row">
		<div class="col-md-8">
			<label>@lang('billing.credit') (@{{curr}})= @{{fixtIt(saleCredit)}}</label>
		</div>		
	</div>

	<div class="row">
		<div class="col-md-8">
			<label>Dubai Mall Online (@{{curr}})= @{{fixtIt(saleNoonCredit)}}</label>
		</div>		
	</div>

	<div class="row">
		<div class="col-md-8">
			<label>@lang('billing.sale_return') (@{{curr}})= @{{fixtIt(saleReturn)}}</label>
		</div>		
	</div>

	<div class="row">
		<div class="col-md-8">
			<label>@lang('billing.bal') (@{{curr}})= @{{fixtIt(Number(openingCash
				)+Number(cashAmt)+Number(cardAmt)+Number(saleCredit)+Number(saleNoonCredit)-Number(saleReturn))}}</label>
			</div>		
		</div>
		
		
		<!-- /.box-body -->
		
		
		<!--Export Buttons-->
		<div class="row export-btn">

			<div class="col-md-2 col-sm-4">
				<button type ="submit" class="btn btn-success btn-block " name="xls" value="xls" :disabled="purchases.length == 0 ? true:false" ><i class="fa fa-file-excel-o" aria-hidden="true" ></i> @lang('glob.export_to_xls')</button> 
				
			</div>
			<div class="col-md-2 col-sm-4">			
				<button type ="submit" class="btn btn-danger btn-block " name="pdf" value="pdf" :disabled="purchases.length == 0 ? true:false" ><i class="fa fa-file-pdf-o" aria-hidden="true" ></i> @lang('glob.export_to_pdf') </button>				
			</div>
			<div class="col-md-2 ">			
				<button type="button" class="btn btn-primary btn-block " name="print" value="print" :disabled="purchases.length == 0 ? true:false"  v-on:click="printPage($event)"><i class="fa fa-print" aria-hidden="true" ></i> @lang('glob.print') </button>				
			</div>
			<div class="col-md-2 ">			
				<button type="button" class="btn btn-success btn-block " name="mail" value="mail" :disabled="purchases.length == 0 ? true:false"  v-on:click="sendEmail($event)"><i class="fa fa-envelope" aria-hidden="true" ></i> @lang('glob.sendmail') </button>				
			</div>
			<div class="col-md-4"></div>
			

		</div>	
		<!--End Export Buttons-->
	</div>
	{{ Form::close() }}
	@endsection

