@extends('layouts.app')
<style type="text/css">
	.content-header{
		display: none !important;
	}
	.content{
		padding-top:10px !important;
	}
	
</style>
@section('content')
<div id="billingVueId" class="billingCls" v-cloak>
	<div class="row">

		<!-- Left section -->
		<div class="col-md-9 col-sm-9 less-padding-right less-padding-left">
			{{-- Billing create start --}}
			@include("pages.billing.billing-create-plain")
			{{-- Billing create end --}}
			
			{{-- Billing Controls start--}}

			<div class="box-body less-padding-right less-padding-left control-panel">
				<div class="col-md-6 col-sm-6 no-h-padding btn-toolbar"  role="toolbar" aria-label="Toolbar with button groups">
					@if(Common::can_option_view('billing','search_items'))
					<button class="btn btn-bg btn-primary search-btn" data-toggle="modal" data-target="#searchModal" v-if="bill_return==false"  data-backdrop="static" data-keyboard="false" :disabled="(home_delivery_check ||(billing.tax_refund_flag==1 && updateBill) ? true :false)">Search Items</button>
					<button class="btn btn-bg btn-primary return-btn return-search" data-toggle="modal" data-target="#searchModal"  data-backdrop="static" data-keyboard="false" v-if="bill_return==true"  :disabled="((return_bill_no!=='' || !show_return_search ) ? true :false)">Return Items</button>
					@endif
					&nbsp; &nbsp;
					@if(Common::can_option_view('billing','retail_invoice'))
					<label for="" class="btn bg-indigo radio-place bill-btn">
						<input type="radio" v-model="billing.invoice_type" value="1" name="invoice_type" :disabled="(bill_return || home_delivery_check? true :false)">  
						@lang('billing.retail_invoice')
					</label>

					
					<label for="" class="btn bg-indigo radio-place bill-btn">
						<input type="radio" v-model="billing.invoice_type" value="2" name="invoice_type" :disabled="(bill_return || home_delivery_check ? true :false)"> @lang('billing.wholesale_invoice')  
					</label>
					@endif
					
				</div>

				<div class="col-md-6 col-sm-6 no-h-padding ">
					@include("pages.billing.billing-controls")
				</div>
			</div>

			
			{{-- Billing Controls end--}}

			{{-- Billing item list table start --}}
			@include("pages.billing.billing-item-list")
			{{-- Billing item list table end --}}
		</div>
		<div class="col-md-3 col-sm-3 less-padding-right less-padding-left">
			<!-- right sidebar -->
			@include("pages.billing.billing-sidebar")
			{{--Right sidebar end --}}
		</div>
	</div>    

	@include("pages.billing.billing-search-item")
	@include("pages.billing.client-buying-history")
	@include("pages.billing.xReport")
	@include("pages.billing.zReport")
	@include("pages.billing.zQtyReport")	
	@include("pages.billing.confirm-modal")
	@include("pages.billing.sms-billing-modal")
</div>
@endsection

