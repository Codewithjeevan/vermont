@extends('layouts.app')

@section('page-title')@lang('glob.stock_adjustment_report')@endsection

@section('page-subtitle')@endsection 

@section ('breadcrumb')

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-file-text-o"></i> @lang('menu.reports')</a></li>
	<li class="active">@lang('glob.stock_adjustment_report')</li>
</ol>

@endsection

@section('content')
{{ Form::open(['action'=>'StockAdjustmentReportController@exportTo','id'=>'stockAdjustmentReportForm']) }}
<div class="stock_adjustment_report" v-cloak>

	<div class="row customer-box"> 

		<div class="col-md-12">

			<div class="box box-primary"> 

				<div class="box-body">

					<div class="row">
						<div class="col-md-3" >
							<div class="form-group no-v-padding no-v-margin">
								<label for="account">Search</label>
								<input type="text" name="search" placeholder="Search" v-model="search_item" v-on:keyup.enter="searchItem(search_item, $event)" class="prevent-enter input-sm form-control" > 
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group no-v-padding no-v-margin">
								<label for="account">@lang('glob.items')<span style="color:red">*</span></label>
								<v-select  :options="items" :text="'itemName'" v-model="item" :vrequired="true" :vname="'itemName'" id="category"></v-select>
							</div>								
						</div>	
						<div class="col-md-3">
							<div class="form-group no-v-padding no-v-margin">
								<label for="account">@lang('glob.barcode')<span style="color:red">*</span></label>
								<v-select  :options="items" :text="'barcode'" v-model="item" :vrequired="true" :vname="'barcode'" id="sub_category"></v-select>
							</div>								
						</div>

						<div class="col-md-3">
							<div class="form-group no-v-padding no-v-margin">
								<label for="account">@lang('glob.user')<span style="color:red">*</span></label>

								<v-select  :options="users" :text="'name'" v-model="user" :vrequired="true" :vname="'user'"></v-select>
							</div>								
						</div>
					</div>

					<!-- .row -->
					<div class="row">
							
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

						<div class="col-md-3" style="margin-top: 2%;  ">
							<button type="button" :disabled="(item != '' && user != '' && dateArray.fromDate !='' && dateArray.toDate !=0 )? false : true" v-on:click="getLedger($event)"  class="btn btn-success btn-circle" > @lang('glob.show')</button>
							
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
			<table class="table table-bordered table-striped stock-adjustment-report-table">
				<thead>
					<tr>
						
						<th >@lang('glob.stock_adjustment')</th>
						
					</tr>
				</thead>

				<tbody>
					
				</tbody>


				<tfoot class="showSearch">
					<tr>
						
						<th class="search">@lang('glob.stock_adjustment')</th>
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
	</br>
	<div class="row">
		<div class="col-md-8">
			<label>@lang('glob.net_loss_value_adjusted') (@{{curr}}) = @{{fixtIt(total)}}</label>
		</div>		
	</div>
	</br>
	
	<!-- /.box-body -->

	<!--Export Buttons-->
	<div class="row export-btn">

		<div class="col-md-2 col-sm-4 ">
			<button type ="submit" class="btn btn-success btn-block" name="xls" value="xls" :disabled="purchases.length == 0 ? true:false" ><i class="fa fa-file-excel-o" aria-hidden="true" ></i> @lang('glob.export_to_xls')</button> 
			
		</div>
		<div class="col-md-2 col-sm-4 ">			
			<button type ="submit" class="btn btn-danger btn-block" name="pdf" value="pdf" :disabled="purchases.length == 0 ? true:false"><i class="fa fa-file-pdf-o" aria-hidden="true" ></i> @lang('glob.export_to_pdf') </button>				
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

