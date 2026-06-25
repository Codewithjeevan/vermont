@extends('layouts.app')

@section('page-title')@lang('glob.customer_classification')@endsection

@section('page-subtitle')@endsection 

@section ('breadcrumb')

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-home"></i> @lang('glob.home')</a></li>
	<li><a href="#"><i class="fa fa-file-text-o"></i> @lang('menu.sales_reports')</a></li>
	<li class="active">@lang('glob.customer_classification')</li>
</ol>

@endsection

@section('content')
{{ Form::open(['action'=>'SalesReportController@exportToCustomerClassificationreport','id'=>'customerClassification_reportForm']) }}
<div class="customer_classification">

	<div class="row customer-box"> 

		<div class="col-md-12">

			<div class="box box-primary"> 

				<div class="box-body">

					<!-- .row -->
					<div class="row">
                    <div class="col-md-3">
							<div class="form-group no-v-padding no-v-margin">
								<label for="account">@lang('glob.brand')<span style="color:red">*</span></label>
								
								<v-select  :options="brands" :text="'categorie'" v-model="brand" :vrequired="true" :vname="'brand'"></v-select>
							</div>								
						</div>	
						<div class="col-md-3">
							<div class="form-group no-v-padding no-v-margin">
								<label for="account">@lang('glob.customer')<span style="color:red">*</span></label>
								
								<v-select  :options="customers" :text="'name'" v-model="customer" :vrequired="true" :vname="'customer'"></v-select>
							</div>								
						</div>	
						
						
						<!-- From Date -->
						<div class="col-md-2">
							<div class="form-group">
								<label>@lang('glob.from')<span style="color:red">*</span></label>
								<div class="input-group">
									<div class="input-group-addon">
										<i class="ion ion-calendar"></i>
									</div>
									<input id="from" type="text" name="from" class="form-control  input-sm datepicker" placeholder="@lang('glob.from')" required="" v-model="dateArray.fromDate" >
								</div>
							</div>
						</div>
						<!-- End From Date -->	
						<!-- To Date -->
						<div class="col-md-2">
							<div class="form-group">
								<label>@lang('glob.to')<span style="color:red">*</span></label>
								<div class="input-group">
									<div class="input-group-addon">
										<i class="ion ion-calendar"></i>
									</div>
									<input id="to" type="text" name="to" class="form-control  input-sm datepicker" placeholder="@lang('glob.to')" required="" v-model="dateArray.toDate">
								</div>
							</div>
						</div>
						<!-- End To Date -->
						<div class="col-md-2" style="margin-top: 2%;">
							<button type="button" v-on:click="getLedger($event)" :disabled="(brand !='' && customer != '' && dateArray.fromDate !='' && dateArray.toDate !='' )? false : true" class="btn btn-success btn-circle" > @lang('glob.show')</button>
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
    	<!-- view table data  -->
	
	<br/>
	<div class="row">

<div class="col-md-12 table-responsive">
    <table class="table table-bordered table-striped cust-classi-table">
        <thead>
            <tr>
                <th>@lang('glob.brand')</th>

            </tr>
        </thead>


        <tbody>
            
        </tbody>
        
        <tfoot class="showSearch">
            <tr>
                <th class="search">@lang('glob.brand')</th>
            </tr>
        </tfoot>
    </table>

</div>

</div>
<div class="row">
		<div class="col-md-8">
			<label>@lang('glob.total_purchase')= @{{fixtIt(totalPurchase)}}</label>
		</div>		
	</div>
	
	<div class="row">

    <div class="col-md-8">
			<label>@lang('glob.totalTransaction') =@{{fixQty(totalTransaction)}}</label>
			
		</div>
		
	</div>
		<!--Export Buttons-->
	<div class="row export-btn">

		<div class="col-md-2 col-sm-4">
			<button type ="submit" class="btn btn-success btn-block " name="xls" value="xls" ><i class="fa fa-file-excel-o" aria-hidden="true" ></i> @lang('glob.export_to_xls')</button> 
			
		</div>
		<div class="col-md-2 col-sm-4">			
			<button type ="submit" class="btn btn-danger btn-block " name="pdf" value="pdf"  ><i class="fa fa-file-pdf-o" aria-hidden="true"></i> @lang('glob.export_to_pdf') </button>				
		</div>
		<div class="col-md-2 col-sm-4">			
			<button type="button" class="btn btn-primary btn-block " name="print" value="print" v-on:click="printPage($event)"><i class="fa fa-print" aria-hidden="true" ></i> @lang('glob.print') </button>				
		</div>
		<div class="col-md-6"></div>
		

	</div>	
	<!--End Export Buttons-->
</div>
{{ Form::close() }}
@endsection

