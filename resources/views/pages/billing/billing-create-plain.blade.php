
<!-- Head section -->

<div class="box box-primary" style="margin-bottom:5px">

	<div class="box-body less-top-padding" >
		<div class="col-md-2 col-sm-3 ">
			<label  for="">
				<div v-if="existing_bill_no !=='' && return_bill_no===''" >Bill No: @{{ fixBill(billing.id) }} </div>
				<div  v-else-if="existing_bill_no ==='' && return_bill_no!=='' && billing.billing_id >=0 ">Bill No: @{{ fixBill(billing.billing_id) }} </div>
				<div v-else>Bill No: @{{ fixBill(ai) }} </div>
			</label>
		</div>
		<div class="col-md-3 col-sm-4 ">
			<label  for="">
				<div>Return Bill No: @{{ fixBill(return_bill_id)  }} </div>
			</label>
		</div>
		<div class="col-md-6 col-sm-5 ">
			<label class="pull-right" for="">@{{ (date) }}</label>
		</div>
		<div class="col-md-9 col-sm-9  no-h-padding">
			<div class="row">
				@{{ billing.retId  }}

				<div class="col-md-3 col-sm-6 ">

					<div class="form-group  no-v-margin numpad-input ">
						<label for="NewBill">@lang('billing.existing_bill_no')</label>
						<div :class="((!home_delivery_check && !hold_check) ? 'display-block-input' : 'display-none-input')">
							<input type="text" name="existing_bill"  class="form-control input-xs num-pad"  v-model="existing_bill_no" v-on:change="getExistingBill(existing_bill_no)" />
						</div>
						<div :class="((home_delivery_check) ? 'display-block-input' : 'display-none-input')">
							<v-select :options="homeDeliveryAll" :scroll="false" :vclass="{ class:'bg-red', if:'billing_status', val:'hold' }" :value="'id'" :text="'str_billing_id'"   v-on:vchange="gethomeItems(existing_home_no)" v-model="existing_home_no"  :vrequired="false" >
							</v-select> 
						</div>
						<div :class="((hold_check) ? 'display-block-input' : 'display-none-input')">
							<v-select :options="holdBill" :scroll="false" :vclass="{ class:'bg-red', if:'billing_status', val:'hold' }" :value="'id'" :text="'str_billing_id'"   v-on:vchange="getholdItems(existing_hold_no)" v-model="existing_hold_no"  :vrequired="false" >
							</v-select> 
						</div>
						
					</div>
					<!-- form group -->
				</div>
				<!-- .col-md-3 -->

				<div class="col-md-3 col-sm-6 ">

					<div class="form-group no-v-padding no-v-margin numpad-input">
						<label for="NewBill">@lang('billing.existing_return_no')</label>
						<input type="text" name="return_bill" class="form-control input-xs num-pad" v-model="return_bill_no" v-on:change="getReturnBill(return_bill_no)"/>
					</div>
					<!-- form group -->
				</div>
					<!-- .col-md-3 -->
					<div class="col-md-3 col-sm-6 ">
					<div class="form-group no-v-padding no-v-margin numpad-input">

						<label for="NewBill">@lang('billing.existing_tag_number')</label>
						<input type="text" name="tag_number" class="form-control input-xs keyboard-styled" v-model="tag_number" v-on:change="getTaxRefundBill(tag_number)"/>
					</div>
					<!-- form group -->
				</div>
				<!-- .col-md-3 -->
				<!-- .col-md-3 -->
				<div class="col-md-3 col-sm-6 ">
					<div class="form-group no-v-padding no-v-margin">

						<label for="NewBill">@lang('glob.salesman')<span style="color:red">*</span></label>
						<v-select :options="salesmas" :text="'name'"  v-model="billing.salesman_id"  :vrequired="false" >
							<option disabled value="0">Select one</option>
						</v-select>
						
					</div>
					<!-- form group -->
				</div>
				<!-- .col-md-3 -->
			</div>
			<!-- .row -->

			<div class="row barcode-scanner-placeholder">
				@if(Common::can_option_view('billing','barcode_add'))
				<div class="col-md-5 col-sm-5 ">
					<div class="form-group no-v-margin ">
						<label for="NewBill">@lang('billing.barcode')</label>
						<div class="input-group numpad-input scanner-placeholder">
							<input id="barcode" type="text" placeholder="Enter Barcode" class="barcodeScan form-control input-xs num-pad" :class="(showbarcodenum == true && isKeyboardEnabled == true)? 'display-block-input' : 'display-none-input' " name="barcode"  v-model="barcode"  v-on:change="addItemByBarcode(barcode)" />
							<input id="barcode1" type="text" placeholder="Enter Barcode" class="barcodeScan form-control input-xs " :class="(showbarcodenum == true  && isKeyboardEnabled == false) ? 'display-block-input' : 'display-none-input' " name="barcode"  v-model="barcode"  v-on:keyup.enter="addItemByBarcode(barcode)" />
							<input id="barcode-on" type="text" placeholder="Enter Barcode" class="barcodeScan form-control input-xs  "  :class="showbarcodenum == false ? 'display-block-input' : 'display-none-input' " name="barcode"  v-model="barcode"  />
							<span class="input-group-btn">
								<button id="barcode-add" class="btn btn-xs btn-circle btn-input-xs-group" :class="showbarcodenum==true ? 'bg-green' : 'bg-maroon'"  v-on:click="getnumpad()"><i v-show="barcode_keyboard" class="fa fa-keyboard-o" aria-hidden="true"></i><i  v-show="barcode_loading" class="fa fa-spinner fa-pulse fa-fw barcode-load"></i></button>
							</span>
						</div>
					</div>
				</div>
				@endif
				@if((Common::can_option_view('billing','serial_number') && Common::can_option_view('billing','barcode_add')) )
				<div class="col-md-5 col-sm-5 ">
					<div class="form-group no-v-margin numpad-input">
						{!! Form::label('title', trans('item.serial_number')) !!}
						<input type="text" name="serial_number" class="form-control input-xs num-pad" v-model="serial_number" v-on:keyup.enter="addItemBySerialNo(serial_number)"/>
					</div>
				</div>
				@endif
				@if((Common::can_option_view('billing','serial_number') && Common::can_option_view('billing','barcode_add'))  )
				
				<div class="col-md-2 col-sm-2 ">
					<button class="btn btn-primary margin-top-space" v-on:click="addByBcodeOrSno(barcode,serial_number,$event)" v-if="bill_return==false" :disabled="(home_delivery_check ? true :false)"> @lang('glob.add')</button>
					<button class="btn btn-primary margin-top-space" v-on:click="addByBcodeOrSno(barcode,serial_number,$event)" v-if="bill_return==true" :disabled="(return_bill_no!='' ? true :false)"> @lang('glob.return')</button>
				</div>
				<!-- .col-md-9 -->
				@endif
			</div>
			<!-- .row -->
		</div>
		<div class="col-md-3 col-sm-3  no-h-padding">
			<div  style="height:100px; width:100%; ">
				<img :src="itemImage" class="pull-right img-responsive" width="100" height="100" alt="Item Image">
			</div>
		</div>
		

	</div>

	<!-- /.box-body -->
</div>
<!-- .box -->