@extends('layouts.app')

@section('page-title')@lang('glob.customer_ledger')@endsection

@section('page-subtitle')@lang('glob.add_edit_customer_ledger')@endsection 

@section ('breadcrumb')

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-home"></i> @lang('glob.home')</a></li>
	<li><a href="#"> @lang('menu.ledger')</a></li>
	<li class="active">@lang('glob.customer_ledger')</li>
</ol>

@endsection

@section('content')
{{ Form::open(['action'=>'CustomerLedgersController@exportTo','id'=>'customerLedgerForm']) }}
<div class="customer_ledger" v-cloak>
	<div class="row customer-box" > 
		<div class="col-md-12">
			<div class="box box-primary"> 

				<div class="box-body">

					<!-- .row -->
					<div class="row">

						<div class="col-md-2">

							<div class="form-group no-v-padding no-v-margin">
								<label for="customer">@lang('customer.customer_id')</label>
								<v-select :vname="'customer'"  :options="customers" :text="'id'" v-on:vchange="getLedger(customer)" :v-id="'customerId'" v-model="customer" :vrequired="true"></v-select>

								
						</div>									

					</div>	

					<div class="col-md-6">

						<div class="form-group no-v-padding no-v-margin">
							<label for="customer">@lang('glob.customer_name')</label>
							<v-select :vname="'customer'"  :options="customers" :text="'name'" v-on:vchange="getLedger(customer)" :v-id="'customerId'" v-model="customer" :vrequired="true"></v-select>

							
					</div>									

				</div>

				<div class="col-md-4">

					<div class="form-group no-v-padding no-v-margin">
						<label for="customer">@lang('glob.mobile')</label>
						<v-select :vname="'customer'"  :options="customers" :text="'mobile'" v-on:vchange="getLedger(customer)" :v-id="'customerId'" v-model="customer" :vrequired="true"></v-select>

						
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
	<div class="col-md-12 table-responsive">
		<table class="table table-bordered table-striped ledger-table">
			<thead>
				<tr>
					<th style="width:10%">#</th>
					<th>@lang('glob.date')</th> 
					<th>@lang('glob.perticulars')</th> 
					<th>@lang('billing.credit_amt')</th>
					<th>@lang('billing.debit_amt')</th>
					<th>@lang('billing.balance')</th>				
				</tr>
			</thead>
			<tbody>
				<tr v-show="ledger.length == 0"> 
					<td colspan="6" class="text-center loader"> No Records found</td>				
				</tr>
				<tr v-show="ledger.length > 0" v-for="(l, index) in ledger">
					<td>@{{ index+1 }}</td>	
					<td>@{{ l.created_at }}</td>
					<td>@{{ ((l.type==1) ? "Bill No:" : "Receipt No:")}} @{{ fixBill(l.bill_id) }}</td>
					<td>@{{ fixtIt(parseFloat(l.credit))}}</td>
					<td>@{{ fixtIt(parseFloat(l.debit)) }}</td>
					<td>@{{ fixtIt(parseFloat(l.balance)) }}</td>					
				</tr>
			</tbody>
			<tfoot >
				<tr>
					<th class="open-sans font-12" style="width:10%">Total: @{{ledger.length}}</th>
					<th class="open-sans font-12">-</th> 
					<th class="open-sans font-12">-</th> 
					<th class="open-sans font-12">@{{ fixtIt(parseFloat(totalCredit)) }}</th>
					<th class="open-sans font-12">@{{ fixtIt(parseFloat(totalDebit)) }}</th>
					<th class="open-sans font-12">@{{ fixtIt(parseFloat(totalBalance)) }}</th>						
				</tr>
			</tfoot>
		</table>
	</div>

</div>
<!-- view table data end -->
<div class="row">
	<!-- customer name -->
	<div class="col-md-12">
		<div class="form-group">
			<label for="">@lang('glob.remark')<span style="color:red">*</span></label>

			<input name="remark" id="remark"  rows="1" class="form-control  input-sm fix-textarea" placeholder="@lang('glob.remark')" required="" v-model="remark">

		</div>
	</div>
	<!-- End Title -->
</div>

<div class="row export-btn">

	<div class="col-md-2 col-sm-4">
		<button type ="submit" class="btn btn-success btn-block " name="xls" value="xls" :disabled="(ledger.length && remark!='' > 0) ? false :true" ><i class="fa fa-file-excel-o" aria-hidden="true"></i> @lang('glob.export_to_xls')</button> 		
	</div>
	<div class="col-md-2 col-sm-4">			
		<button type ="submit" class="btn btn-danger btn-block " name="pdf" value="pdf" :disabled="(ledger.length && remark!='' > 0) ? false :true" ><i class="fa fa-file-pdf-o" aria-hidden="true" ></i> @lang('glob.export_to_pdf') </button>	
	</div>
	<div class="col-md-8"></div>

</div>

</div>
{{ Form::close() }}
@endsection

