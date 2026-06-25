	<div class="box" style="border-top:0">

		<div class="box-body " style="margin:0px;padding:0px; border:2px solid #c8c8c8; border-radius: 5px;">

			<div class="table-responsive table-list-box" >
				<div class="table-spinner" style="display: none">
					<i class="fa fa-spinner fa-pulse fa-spin  fa-4x fa-fw"></i>
					<span class="sr-only">Loading...</span>
				</div>

				<table class="table table-bordered item-table item-table-loading">
					<tr>
						<thead>
							<th style="width:5%">#</th>
							<th style="width:30%">@lang('billing.item_name')</th>
							<th style="width:15%">@lang('billing.qty')</th>
							<th style="width:15%">@lang('billing.unit')</th>
							<th style="width:15%">@lang('billing.rate')</th>
							<th style="width:15%">@lang('billing.amt')(@{{curr}})</th>
							
						</thead>
					</tr>
					<tbody class="biiling-item-body">
						<tr v-for="i in itemList" class="item-p table-select-row" v-on:click="itemImage=i.image" :class=" i.itemType != 0 ? 'bg-item-free' : '' ">
							<td><input class="itemchk big-checkbox" type="checkbox" v-model="i.checked"></td>
							<td>@{{ i.itemName  }}</td>
							<td class="numpad-input" :class=" i.itemType == 0 ? 'tbl-change-val' : '' " ><input type="text" class="form-control validate-decimal input-xs" :class=" i.itemType == 0 ? 'billing-item-qty bill-numpad billing-tb' : 'read-only-itemlist' "  v-on:change="checkStock(i)" v-model.lazy="i.qty" :data-stock="i.stock" :readonly="( return_bill_no!='' || (i.itemType > 0) ? true :false)"></td>
							<td>@{{ i.units.unit }}</td>
							<td class="numpad-input" ><input type="text" class="form-control  input-xs  validate-decimal"  :class=" i.itemType == 0 ? 'bill-numpad billing-tb' : 'read-only-itemlist' " v-model.lazy="i.rate" data-stock="null" :readonly="( return_bill_no!='' || (i.itemType> 0) ? true :false) "></td>
							<td v-show="intVatType != 2">@{{ fixtIt(i.totalPrise = i.rate * i.qty) }}</td>
							<td v-show="intVatType == 2">@{{ fixtIt(i.totalPrise = ( ( parseFloat( i.rate ) + ( ( parseFloat( i.rate ) * parseFloat( i.vat ) ) / 100 ) ) * parseFloat( i.qty ) ) ) }}</td>
						</tr>
					</tbody>
				</table>
				<!-- table -->
				
			</div>
			<!-- .table-responsive -->
			<div class="pull-right" v-if="getReturnExisting.length && !bill_return" style="margin-right: 15px;"><h5><strong>Total AMT :</strong> @{{fixtIt(_bill_amount)}}</h5></div>
		</div>
		<!-- /.box-body -->

		<div class="box-body" style="margin:1px;border:2px solid #c8c8c8; border-radius: 5px;" v-if="getReturnExisting.length && !bill_return">
			<div class="table-responsive " style="height: 250px; overflow-y: scroll;">
				<div class="table-spinner" style="display: none">
					<i class="fa fa-spinner fa-pulse fa-spin  fa-4x fa-fw"></i>
					<span class="sr-only">Loading...</span>
				</div>

				<table class="table table-bordered item-table" >
					<tr>
						
						<thead style="background-color: #9c4949;">
							<th>#</th>
							<th>@lang('billing.return_bill_no')</th>
							<th>@lang('billing.return_item_name')</th>
							<th>@lang('billing.return_date')</th>
							<th>@lang('billing.qty')</th>
							<th>@lang('billing.unit')</th>
							<th>@lang('billing.rate')</th>
							<th>@lang('billing.amt')(@{{curr}})</th>
						</thead>
					</tr>
					<tbody>
						<tr v-for="(i, index) in getReturnExisting" class="item-p table-select-row" >
							<td id="c1">@{{index+1}}</td>
							<td>@{{ fixBill(i.return_id) }}</td>
							<td>@{{ i.itemName  }}</td>
							<td v-if="i.created_at!=null">@{{ i.created_at}}</td>
							<td v-else>-</td>
							<td>@{{i.qty}}</td>
							<td>@{{ i.units.unit }}</td>
							<td >@{{i.rate}}</td>
							<td v-show="intVatType != 2">@{{ fixtIt(i.totalReturnPrise = i.rate * i.qty) }}</td>
							<td v-show="intVatType == 2">@{{ fixtIt(i.totalReturnPrise = ( ( parseFloat( i.rate ) + ( ( parseFloat( i.rate ) * parseFloat( i.vat ) ) / 100 ) ) * parseFloat( i.qty ) ) ) }}</td>
						</tr>
					</tbody>
				</table>

			</div>
			<!-- .table-responsive -->
			<div class="pull-right" style="margin-right: 15px;"><h5><strong>Total AMT :</strong>  @{{fixtIt(returnTotal)}}</h5></div>
		</div>
		<!-- /.box-body -->

		<div class="box-footer no-shadow">
			<div class="pull-left">
				<h4>@lang('glob.total_quantity'): @{{ fixQty(totalQty) }} </h4> 
				
			</div>
			
			<div class="pull-right ">
				
				<button class="btn bill-btn-2 reset-btn" v-on:click="activateBilling()" style="background-color: rgb(182, 78, 78); color: #fff;"><i style="font-size:15px;" class="ion-ios-printer-outline" aria-hidden="true"></i> @lang('glob.activate_billing')</button>

				<button class="btn bill-btn-2 " v-on:click="openCashDrawer()" v-if="(mallDetails.open_cash_drawer == 1)" style="background-color: #ea6822; color: #fff; padding: 9px 20px;"><i style="font-size:20px;" class="ion ion-ios-filing-outline" aria-hidden="true"></i> @lang('glob.open_cash_drawer')</button>
				
				
				@if(Common::can_option_view('billing','home_delivery'))
				<label for="" class="radio-place btn bg-maroon bill-btn-home ">
					{!! Form::checkbox('item_box', null, false ,array('id'=>'item_box', 'v-model' => 'billing.home_delivery' , 'v-on:change' => 'homeCheck(billing.home_delivery)')) !!}
					@lang('billing.home_delivery')
				</label>
				@endif

				

				<button class="btn btn-indigo bill-btn-2 reset-btn" v-if="(( get_existing_bill_check && !bill_return && billing.billing_status != 'hold' && !home_delivery_check) || (bill_return && return_bill_check)  )" v-on:click="printExisting"><i class="fa fa-print" aria-hidden="true"></i> @lang('glob.print')</button>
				@if(Common::can_delete('billing'))
				<button class="btn btn-danger bill-btn-2 reset-btn" v-if="((get_existing_bill_check &&  !bill_return) || (bill_return && return_bill_check)) || (existing_hold_no!='' && billing.billing_status != 'hold' && itemList.length > 0)" v-on:click="deleteBill($event)"><i class="fa fa-trash" aria-hidden="true"></i> @lang('glob.delete')</button>
				@endif

				<button class="btn btn-cyan bill-btn-2 reset-btn" v-on:click="clearBilling"><i class="fa fa-refresh" aria-hidden="true"></i> @lang('glob.reset')</button>
				
				@if(Common::can_create('billing') || Common::can_update('billing'))
				<button id="returnBillBtn" class="btn btn-success bill-btn-2" v-if="bill_return==true"  :disabled="((itemList.length > 0 && ( !return_bill_check )) ? false :true) " v-on:click="returnItemValidate($event)" ><i class="fa fa-money" ></i> @lang('glob.return')</button>				
				<button class="btn btn-success bill-btn-2 pay-btn" v-on:click="payCall"  :disabled="((itemList.length > 0) ? false :true) || (billing.billing_status == 'hold') || billing.salesman_id == 0" v-if="bill_return==false" ><i class="fa fa-money" ></i> Pay</button>				
				@endif
				@include('pages.billing.confirm-billing-type')		
				{{-- Billing Payment Module added start --}}
				@include('pages.billing.billing-payment')				
				{{-- Billing Payment Module added start --}}
			</div>
		</div>
	</div>