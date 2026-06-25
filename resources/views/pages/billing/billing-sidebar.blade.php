<div class="well box box-primary text-right" style="margin: 0px 0 0 0;padding-bottom: 30px ">
	<h1>@{{curr}} @{{ fixtIt(_bill_amount - returnTotal) }}</h1>
	<span v-if="fixtIt(parseFloat(this.billing.v_amt) - parseFloat(return_vat_amount)) > 0" v-show="intVatType!=0">VAT @{{curr}} @{{ fixtIt(parseFloat( _discounted_vat ) - parseFloat(_return_vat_amount))}}</span>
	<span v-if="fixtIt(parseFloat(this.billing.v_amt) - parseFloat(return_vat_amount)) <= 0" v-show="intVatType!=0">VAT @{{curr}} @{{ 0 }}</span><br/>
	<!-- <span>Discount @{{curr}} @{{ fixtIt(parseFloat(_dis_amt) - parseFloat(return_dis_amount) + parseFloat(billing.loyalty_discount)) }}</span> -->
</div>
<div class="calc" style="display: none; z-index: 1;"></div>
<ul class="list-group exo2">
	@if(Common::can_option_view('billing','voucher_issue'))
	<button class="btn btn-bg btn-block bg-purple billing-sidebar-btn"><i class="fa fa-sticky-note-o" aria-hidden="true"></i>  @lang("billing.voucher_issue")</button>
	@endif

	@if(Common::can_option_view('billing','voucher_redeem'))
	<button class="btn btn-bg btn-block bg-purple billing-sidebar-btn"> <i class="fa fa-sticky-note-o" aria-hidden="true"></i>  @lang("billing.voucher_redeem")</button>
	@endif

	@if(Common::can_option_view('billing','calc'))
	<button class="btn btn-bg btn-block bg-purple billing-sidebar-btn open-calc"><i class="fa fa-calculator" aria-hidden="true"></i> @lang("billing.calculator")</button>
	@endif

	@if(Common::can_option_view('billing','report'))	
	<button id="reportBtn" class="btn btn-bg btn-block bg-purple billing-sidebar-btn"  type="button" data-toggle="collapse" data-target="#reportMenuDiv" v-on:click="showReportMenuDiv()" style="margin-top: 1px;"><i class="fa fa-file-text-o" aria-hidden="true"></i> @lang("billing.reports")</button>
	
	<div id="reportMenuDiv" style="margin-top: 2px;margin-bottom:2px; ">
		<button class="btn btn-bg btn-block bg-purple " type="button"  data-toggle="modal" data-target="#xReportModal" style="text-align: left; margin-bottom:2px;">
			<a style="font-size: 12px; 
			padding: 2px 0;margin-top: 2px;margin-bottom:2px; color: #fff; font-family: sans-serif;">
			<i class="fa fa-file-text-o"></i> @lang("glob.x_sales")</a>
		</button>
		<button id="z-sls-report" class="btn btn-bg btn-block bg-purple" type="button"   v-on:click="getZSaleReport()" style="text-align: left; margin-top: 2px;margin-bottom:2px;" >
			<a style="font-size: 12px; padding: 2px 0;margin-top: 2px;margin-bottom:2px; color: #fff;  font-family: sans-serif;">
				<i class="fa fa-file-text-o"></i> @lang("glob.z_sales")
			</a>
		</button>
		<button id="z-qty-report" class="btn btn-bg btn-block bg-purple" type="button"  v-on:click="getZQuantitySaleReport()" style="text-align: left; margin-top: 2px;margin-bottom:2px;">
			<a style="font-size: 12px; padding: 5px 0;margin-top: 2px;margin-bottom:2px; color: #fff; font-family: sans-serif;"><i class="fa fa-file-text-o"></i> @lang("glob.z_qty")</a>
		</button>
	</div>

	@endif

	@if(Common::can_option_view('billing','hold_bill'))
	<button class="btn btn-bg btn-block  billing-sidebar-btn"   v-bind:class=" (hold_check) ? 'bg-success-dark': 'bg-purple' " v-on:click="getHoldBill($event)"><i class="fa fa-pause" aria-hidden="true"></i> Hold Bills</button>
	@endif

	@if(Common::can_option_view('billing','home_delivery'))
	<button class="btn btn-bg btn-block  billing-sidebar-btn"  v-bind:class=" (home_delivery_check) ? 'bg-success-dark': 'bg-purple' " v-on:click="getHomeDeliveryBill($event)" ><i class="fa fa-home" aria-hidden="true"></i>  @lang("billing.home_delivery")</button>
	@endif

	@if(Common::can_option_view('billing','receive_home_delivery_payment'))
	<button class="btn btn-bg btn-block bg-purple billing-sidebar-btn" :disabled="((home_delivery_check && itemList.length > 0) ? false :true)" v-on:click="payHomeDelivery()"><i class="fa fa-money" aria-hidden="true"></i> @lang("billing.receive_hdp")</button>
	@endif

	@if(Common::can_option_view('billing','sale_return'))
	<button class="btn btn-bg btn-block  billing-sidebar-btn" v-bind:class=" (bill_return) ? 'bg-success-dark': 'bg-purple'" v-show="!tax_refund_bill" v-on:click="getSaleReturn(billing.id)" ><i class="fa fa-hand-o-left" aria-hidden="true"></i>  @lang("billing.sale_return") </button>
	@endif

	@if(Common::can_option_view('billing','reprint_sale_return_receipt'))
	<button  class="btn btn-bg btn-block bg-purple billing-sidebar-btn" :disabled="((bill_return && return_bill_check) ? false :true)"  v-on:click="printExisting" ><i class="fa fa-repeat" aria-hidden="true"></i> @lang("billing.reprint_rss")</button>
	@endif

	@if(Common::can_option_view('billing','client_buying_history'))
	<button class="btn btn-bg btn-block bg-purple billing-sidebar-btn" data-toggle="modal" data-target="#history-modal" v-on:click="clearBuyingHistory"><i class="fa fa-history" aria-hidden="true"></i> @lang("billing.client_buying_history")</button>
	@endif

</ul>



