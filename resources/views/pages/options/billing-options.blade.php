<div class="col-md-12">
	<div class="row">
		<div class="col-md-6">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title">Item Search options</h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
						</button>
					</div>
				</div>
				<!-- /.box-header -->
				<div class="box-body " style="display: block;">
					<ul class="list-group">
						<li class="list-group-item">
							<div class="row">
								<div class="col-md-6">
									<label><input type="checkbox" v-model="billButtons.serial_number"> @lang('billing.serial_number')</label>
								</div>
								<div class="col-md-6">
									<label><input type="checkbox" v-model="billButtons.barcode_add"> @lang('billing.barcode')</label>
								</div>
							</div>
						</li>
					</ul>
				</div>

			</div>
		</div>
		<div class="col-md-6">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title">Billing Screens</h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
						</button>
					</div>
				</div>
				<!-- /.box-header -->
				<div class="box-body " style="display: block;">
					<ul class="list-group">
						<li class="list-group-item">
							<label class="radio-inline"><input type="radio" name="bill" value="1" v-model="billButtons.billscreen" >Billing screen 1</label>
							<label class="radio-inline"><input type="radio" name="bill" value="2" v-model="billButtons.billscreen" >Billing screen 2</label>
						</li>
					</ul>
				</div>
				
			</div>
		</div>
	</div>
</div>
<div class="col-md-12">
	<!-- USERS LIST -->
	<div class="box box-primary">
		<div class="box-header with-border">
			<h3 class="box-title">@lang('glob.billing_options')</h3>
			<div class="box-tools pull-right">
				<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
				</button>
			</div>
		</div>
		<!-- /.box-header -->
		<div class="box-body " style="display: block;">
			<ul class="list-group clearfix">
				<li class="list-group-item">
					<div class="row">
						<div class="col-md-12">
							<label><input type="checkbox" v-model="billAllcheck" v-on:click="billAllCheck"> @lang('glob.check_all')</label>	
						</div>
					</div>
				</li>
				<li class="list-group-item">
					<div class="row">
						<div class="col-md-3">
							<label><input type="checkbox" v-model="billButtons.gift_bill_print"> @lang('billing.gift_bill_print')</label>
						</div>

						<div class="col-md-3">
							<label><input type="checkbox"  v-model="billButtons.hold_bill" > @lang('billing.hold_unhold_bill')</label>
						</div>

						<div class="col-md-3">
							<label><input type="checkbox" v-model="billButtons.retail_invoice"> @lang('billing.retail_wholesale_invoice')</label>
						</div>
						<div class="col-md-3">
							<label><input type="checkbox" v-model="billButtons.report"> @lang('billing.report')</label>
						</div>
					</div>
				</li>
				<li class="list-group-item">
					<div class="row">

						<div class="col-md-3">
							<label><input type="checkbox" v-model="billButtons.reprint_sale_return_receipt"> @lang('billing.reprint_sale_return_receipt')</label>
						</div>

						<div class="col-md-3">
							<label><input type="checkbox" v-model="billButtons.receive_home_delivery_payment"> @lang('billing.receive_home_delivery_payment')</label>
						</div>

						<div class="col-md-3">
							<label><input type="checkbox" v-model="billButtons.client_buying_history"> @lang('billing.client_buying_history')</label>
						</div>

						<div class="col-md-3">
							<label><input type="checkbox" v-model="billButtons.open_cash_drawer"> @lang('billing.open_cash_drawer')</label>
						</div>
					</div>
				</li>
				<li class="list-group-item">
					<div class="row">
						<div class="col-md-3">
							<label><input type="checkbox" v-model="billButtons.customer_details"> @lang('billing.customer_details')</label>
						</div>

						<div class="col-md-3">
							<label><input type="checkbox" v-model="billButtons.search_items"> @lang('billing.search_items')</label>
						</div>

						<div class="col-md-3">
							<label><input type="checkbox" v-model="billButtons.voucher_issue"> @lang('billing.voucher_issue')</label>
						</div>

						<div class="col-md-3">
							<label><input type="checkbox" v-model="billButtons.voucher_redeem"> @lang('billing.voucher_redeem')</label>
						</div>
					</div>
				</li>
				<li class="list-group-item">
					<div class="row">
						<div class="col-md-3">
							<label><input type="checkbox" v-model="billButtons.calc"> @lang('billing.calc')</label>
						</div>
						<div class="col-md-3">
							<label><input type="checkbox" v-model="billButtons.home_delivery"> @lang('billing.home_delivery')</label>
						</div>
						<div class="col-md-3">
							<label><input type="checkbox" v-model="billButtons.sale_return"> @lang('billing.sale_return')</label>
						</div>
						<div class="col-md-3">
							<label><input type="checkbox" v-model="billButtons.loyalty"> @lang('billing.loyalty')</label>
						</div>
						
					</div>
				</li>

			</ul>
			<!-- /.users-list -->
		</div>
		<!-- /.box-body -->
		
	</div>
	<!--/.box -->
</div>

<div class="col-md-12">
	<div style="margin:5px;">
		@if(Common::can_update('options') || Common::can_create('options'))
		<a  class="btn btn-primary" v-on:click="saveBilling(billButtons,$event)"><i class="fa fa-refresh"></i> @lang('glob.save')</a>
		@endif	
	</div>
</div>
		<!-- /.box-footer -->