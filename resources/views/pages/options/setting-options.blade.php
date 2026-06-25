<div class="col-md-12">
	<div class="box box-primary">
		<div class="box-header with-border">
			<h3 class="box-title">@lang('glob.setting_options')</h3>
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
						<div class="col-md-4">
							<label>@lang('glob.currency')</label>
							<div class="input-group">
								<input type="text" id="setting-curr" class="form-control input-sm" v-model="settingButtons.curr" maxlength="4" v-on:change="validationCurrency">
							</div>
						</div>

						<div class="col-md-4">
							<label>@lang('glob.text_currency')</label>
							<div class="input-group">
								<input type="text" id="setting-currText" class="form-control input-sm" v-model="settingButtons.textCurrency" maxlength="25" v-on:change="validationCurrencyText">
							</div>
						</div>
						<div class="col-md-4">
							<label>@lang('glob.text_currency_after_decimal')</label>
							<div class="input-group">
								<input type="text" id="setting-currTextAfterDecimal" class="form-control input-sm" v-model="settingButtons.textCurrencyAfterDecimal" maxlength="20" v-on:change="validationCurrencyTextAfterDecimal">
							</div>
						</div>
						
					</div>
				</li>
				<li class="list-group-item">
					<div class="row">
						<div class="col-md-4">
							<label>@lang('glob.amt_decimal')</label>
							<div class="input-group">
								<input type="text" id="setting-amount" name="amt_decimal" class="form-control input-sm" v-model="settingButtons.amount_decimal" maxlength="1" v-on:change="validationAmountDecimal">
							</div>

						</div>
						<div class="col-md-4">
							<label>@lang('glob.qty_decimal')</label>
							<div class="input-group">
								<input type="text" id="setting-quantity" class="form-control input-sm" v-model="settingButtons.quantity_decimal" maxlength="1" v-on:change="validationQuantityDecimal">
							</div>
						</div>
						<div class="col-md-4">
							<label>@lang('glob.bill_pad')</label>
							<div class="input-group">
								<input type="text" id="setting-bill" class="form-control input-sm" v-model="settingButtons.bill_pad" maxlength="1" v-on:change="validationBillPadding">
							</div>
						</div>
						
					</div>
				</li>
				<li class="list-group-item">
					<div class="row">
						<div class="col-md-4">
							<label>@lang('glob.bill_prefix_word')</label>
							<div class="input-group">
								<input type="text" id="setting-billPrefixWord" class="form-control input-sm" v-model="settingButtons.bill_prefix_word" maxlength="4">
							</div>
						</div>
						<div class="col-md-4">
							<label>@lang('glob.email')</label>
							<div class="input-group">
								<input id="setting-email" type="email" name="email" class="form-control input-sm" placeholder="@lang('glob.email')"  style="width: 100%;" v-model="settingButtons.email" v-on:change="validateEmail">
							</div>
						</div>
						<div class="col-md-4">
							<label>@lang('glob.barcode_type')</label>
							<div class="input-group">
								<select class="form-control input-sm " v-model="settingButtons.barcode_type" >
									<option v-for="barcode in arrBarcode" v-bind:value="barcode.id"  >
										@{{ barcode.type }}
									</option>
								</select>
							</div>
						</div>
					</div>
					
				</li>

				<li class="list-group-item">
					<div class="row">
						<div class="col-md-4">
							<label>@lang('glob.barcode_rate_prefix')</label>
							<div class="input-group">
								<input type="text" id="setting-barcode_rate_prefix" class="form-control input-sm" v-model="settingButtons.barcode_rate_prefix" maxlength="2" v-on:change="validationBarcodeRatePrefix">
							</div>
						</div>

						<div class="col-md-4">
							<label>@lang('glob.barcode_weight_prefix')</label>
							<div class="input-group">
								<input type="text" id="setting-barcode_weight_prefix" class="form-control input-sm" v-model="settingButtons.barcode_weight_prefix" maxlength="2" v-on:change="validationBarcodeWeightPrefix">
							</div>
						</div>
						<div class="col-md-4">
							<label>@lang('glob.barcode_decimal_price_limit')</label>
							<div class="input-group">
								<input type="text" id="setting-barcode_decimal_price_limit" class="form-control input-sm" v-model="settingButtons.barcode_decimal_price_limit" maxlength="1" v-on:change="validationBarcodePriceLimit">
							</div>
						</div>
					</div>
				</li>
				<li class="list-group-item">
					<div class="row">
						<div class="col-md-4">
							<label>@lang('glob.reports_date_format')</label>
							<div class="input-group">
								<select class="form-control input-sm " v-model="settingButtons.reports_date_format" >
									<option v-for="date in arrDateFormats" v-bind:value="date.formatCode"  >
										@{{ date.formatView }}
									</option>
								</select>
							</div>
						</div>

						<div class="col-md-4">

						</div>
						<div class="col-md-4">

						</div>
					</div>
				</li>

			</ul>
		</div>
		<div class="box-footer">
			@if(Common::can_update('options') || Common::can_create('options'))
			<a class="btn btn-primary" v-on:click="saveSetting(settingButtons,$event)"><i class="fa fa-refresh"></i> @lang('glob.save')</a>
			@endif	
		</div>
	</div>
</div>

