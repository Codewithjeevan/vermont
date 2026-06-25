@extends('layouts.app')

@section('page-title')@lang('glob.supplier_ledger')@endsection

@section('page-subtitle')@lang('glob.add_edit_supplier_ledger')@endsection 

@section ('breadcrumb')

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-home"></i> @lang('glob.home')</a></li>
	<li><a href="#"><i class="fa fa-money"></i> @lang('menu.ledger')</a></li>
	<li class="active">@lang('glob.supplier_ledger')</li>
</ol>

@endsection

@section('content')
{{ Form::open(['action'=>'SupplierLedgerController@exportTo','id'=>'supplierLedgerForm']) }}
<div class="supplier_ledger" v-cloak>

	<div class="row customer-box"> 

		<div class="col-md-12">

			<div class="box box-primary"> 

				<div class="box-body">

					<!-- .row -->
					<div class="row">

						<div class="col-md-4">

							<div class="form-group no-v-padding no-v-margin">
								<label for="account">@lang('glob.supplier')</label>
								<v-select :vname="'Supplier'"  :options="suppliers" :text="'name'" v-on:vchange="getLedger(supplier)" :v-id="'supplierId'" v-model="supplier" :vrequired="true"></v-select>

								
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
						<td>@{{ l.date }}</td>	
						<td>@{{ l.particulars }}</td>
						<td class="text-right">@{{ fixtIt(parseFloat(l.credit)) }}</td>
						<td class="text-right">@{{ fixtIt(parseFloat(l.debit)) }}</td>
						<td class="text-right">@{{ fixtIt(parseFloat(l.balance)) }}</td>				
					</tr>
				</tbody>
				<tfoot >
					<tr>
						<th class="open-sans font-12" style="width:10%">Total: @{{ ledger.length }}</th>
						<th class="open-sans font-12" >-</th>
						<th class="open-sans font-12" >-</th> 
						<th class="open-sans font-12 text-right" >@{{ fixtIt(parseFloat(totalCredit)) }} </th>	
						<th class="open-sans font-12 text-right" >@{{ fixtIt(parseFloat(totalDebit)) }}</th>
						<th class="open-sans font-12 text-right" >@{{ fixtIt(parseFloat(totalBalance)) }} </th>						
					</tr>
				</tfoot>
			</table>

		</div>

	</div>
	<!-- view table data end -->	


	
	


	


	<!-- /.box-body -->
	
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
	
	<!-- box-footer -->

	

	<!--Export Buttons-->
	<div class="row export-btn">
		
		<div class="col-md-2 col-sm-4">
			<button type ="submit" :disabled="(ledger.length && remark!=''> 0 ) ? false :true" class="btn btn-success btn-block " name="xls" value="xls"  ><i class="fa fa-file-excel-o" aria-hidden="true"></i> @lang('glob.export_to_xls')</button> 		
		</div>
		<div class="col-md-2 col-sm-4">			
			<button type ="submit" :disabled="(ledger.length && remark!='' > 0 ) ? false :true" class="btn btn-danger btn-block " name="pdf" value="pdf" ><i class="fa fa-file-pdf-o" aria-hidden="true" ></i> @lang('glob.export_to_pdf') </button>	
		</div>
		<div class="col-md-8"></div>

	</div>	
	<!--End Export Buttons-->
</div>
{{ Form::close() }}
@endsection

