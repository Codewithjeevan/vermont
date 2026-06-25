@extends('layouts.app')

@section('page-title')@lang('glob.purchase_return')@endsection

@section('page-subtitle')@lang('stock.add_edit_purchase')@endsection 

@section ('breadcrumb')

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> @lang('glob.dashboard')</a></li>
	<li class="active">@lang('glob.purchase_return')</li>
</ol>

@endsection

@section('content') 
<div id="purchase-return">
	@if(Common::can_create('purchase_return'))
	<!-- Button div -->
	<div class="row">
		<div class="col-md-12 margin-bottom">
			<a  class="btn bg-purple new-customer" v-on:click="addNew()">@lang('glob.add_new')</a>
		</div>
	</div>
	<!-- Button div end -->

	<div id="purchase-return-box" style="display: none">
		<div class="row customer-box" >  
			<div class="col-md-12">
				<div class="box box-primary">
					<div class="box-body">
						<div class="row">
							<div class="col-md-12">
								<label for="mrn_no">@lang('stock.return_no') :@{{ purchase.id }}</label> 
							</div>
						</div>
						<!-- .row end mrn no -->

						<div class="row">
							<!-- return date -->
							<div class="col-md-2">
								<div class="form-group">
									<label for="">@lang('stock.return_date')<span style="color:red">*</span></label>
									<div class="input-group">
										<div class="input-group-addon">
											<i class="ion ion-calendar"></i>
										</div>
										<input v-model="purchase.return_date" id="return_date" type="text" class="form-control  input-sm datepicker" placeholder="@lang('stock.return_date')" required="">
									</div>
								</div>
							</div>
							<!-- end return date -->

							<!-- supplier -->
							<div class="col-md-4">
								<div class="form-group">
									<label for="">@lang('supplier.supplier')<span style="color:red">*</span></label>
									<div class="input-group">
										<div class="input-group-addon">
											<i class="fa fa-user"></i>
										</div>
										<v-select :options="suppliers" :text="'name'" v-on:vchange="getSupplierItems(purchase.supplier_id)" v-model="purchase.supplier_id" :vrequired="true"></v-select>
										
									</div>
								</div>
							</div>
							<!-- end supplier -->

							<!-- s. bill no -->
							<div class="col-md-3">
								<div class="form-group">
									<label for="">@lang('stock.stock_transfer_no')<span style="color:red">*</span></label>
									<div class="input-group">
										<div class="input-group-addon">
											<i class="fa fa-file-text-o"></i>
										</div>
										<v-select  :options="supplierPurchases" :vparent="purchase.supplier_id" :vcheck="'supplier_id'" v-model="purchase.supplier_billno" :text="'supplierBillNo'" :vvalue="'supplierBillNo'"  :vrequired="true"></v-select>
										
									</div>
								</div>
							</div>
							<!-- end s.bill no. -->

							<!-- s.bill date -->
							<div class="col-md-3">
								<div class="form-group">
									<label for="">@lang('stock.stock_transfer_date')<span style="color:red">*</span></label>
									<div class="input-group ">
										<div class="input-group-addon">
											<i class="ion ion-calendar"></i>
										</div>
										<v-select v-model="purchase.supplier_billno"  :options="supplierPurchases" :vparent="purchase.supplier_id" :vcheck="'supplier_id'" :text="'supplier_billdate'" :vvalue="'supplierBillNo'"  :vrequired="true"></v-select>
										
									</div>
								</div>
							</div>
							<!--end s.bill date -->

						</div>
						<!-- .row -->

						{{-- Purchase retun items start --}}
						@include('pages.stock.purchase_return.purchase_return_items')
						{{-- Purchase return items end --}}


						<div class="well margin-top-space">
							<div class="row">
								<div class="col-md-6">
									<div class="row">
										<div class="col-md-8">
											<div class="form-group">
												<label for="">@lang('stock.return_through')<span style="color:red">*</span></label>
												<div class="input-group">
													<div class="input-group-addon">
														<i class="fa fa-user"></i>
													</div>
													<input v-model="return_through_add_text" type="text" class="form-control  input-sm" :class="returnthrough == false ? '' : 'display-none-input' " placeholder="@lang('stock.return_through')" required="">
													<div :class="returnthrough == true ? 'display-block-input' : 'display-none-input' ">
														<v-select  :options="rThrough" :text="'return_through'"  :vvalue="'return_through'" v-model="purchase.return_through" :vrequired="true"></v-select>
													</div>

													<div class="input-group-btn">
														<a  v-if="returnthrough == true" class="btn btn-xs bg-maroon btn-circle btn-input-xs-group" v-on:click="addReturnThrough"><i class="fa fa-plus" aria-hidden="true"></i></a>
														<a  v-if="returnthrough == false" class="btn btn-xs bg-purple btn-circle btn-input-xs-group" v-on:click="addReturnThrough"><i class="fa fa-caret-down" aria-hidden="true"></i></a>
													</div>
												</div>
											</div>
										</div>

										
									</div>
									<div class="row">
										<div class="col-md-12" v-show="purchaseOp=='edit'">	
											<div class="form-group">
												{!! Form::label('title', trans('glob.remark')) !!}<label><span style="color:red">*</span></label>
												<textarea name="remark"  rows="4" class="form-control input-sm fix-textarea" placeholder="{{ trans('glob.remark') }}"   v-model="purchase.remark" :required="(purchaseOp=='edit') ? true : false"></textarea>
											</div>
										</div>
									</div>

								</div>

								<div  class="col-md-6" >
									<div class="row margin-b-2px">
										<div class="col-md-3 "><label class="pull-right">@lang('billing.sub_total')</label></div>
										<div class="col-md-9"> <input type="text" v-model="subTotal" name="subTotal" class="form-control input-sm  billVal" readonly="" ></div>
									</div>
									@if(Common::vatType('vat','vat_type')!=0)
									<div class="row margin-b-2px">
										<div class="col-md-3 "><label class="pull-right">@lang('glob.vat_amt')</label></div>
										<div class="col-md-9"> <input type="text" v-model="vatTotal" name="vatTotal" class="form-control input-sm  billVal" readonly="" ></div>
									</div>
                                     @endif
									<div class="row margin-b-2px">
										<div class="col-md-3 "><label class="pull-right">@lang('glob.discount') @lang('stock.amt')</label></div>
										<div class="col-md-9"> 
											<div class="col-md-6 no-h-padding" style="padding-right: 5px;"><input type="text" name="disAmount" class="form-control input-sm  billVal" readonly=""  v-model=subDiscount></div>
											<div class="col-md-6 no-h-padding">
												<div class="col-md-5 no-h-padding"><label    style="padding-right: 5px;"  for=""> @lang('glob.extra_dis') </label></div>
												<div class="col-md-7 no-h-padding"><input type="text"  name="extraDiscount" class="form-control input-sm  billVal validate-decimal" v-model="purchase.extraDiscount" ></div>
											</div>
										</div>
									</div>

									<div class="row margin-b-2px">
										<div class="col-md-3 "><label for="" class="pull-right">@lang('billing.bill') @lang('billing.amt')</label></div>
										<div class="col-md-9"> <input type="text" name="billAmount" class="form-control input-sm  billVal" readonly="" v-model="billAmount"></div>
									</div>

								</div>

							</div>

						</div>
					</div>


					<div class="box-footer no-shadow">
						<div class="pull-left">
							<button class="btn btn-indigo" v-if="purchaseOp=='add'":disabled="purchasedItems.length == 0 ? true : false" v-on:click="savePrint(purchase, $event)"><i class="fa fa-print" aria-hidden="true" > </i> @lang('glob.save') &  @lang('glob.print')</button>

							<button class="btn btn-indigo" v-if="purchaseOp=='edit'":disabled="purchasedItems.length == 0 ? true : false" v-on:click="savePrint(purchase, $event)"><i class="fa fa-print" aria-hidden="true" > </i> @lang('glob.update') &  @lang('glob.print')</button>

							<button v-if="purchaseOp=='add'" :disabled="purchasedItems.length == 0 ? true : false" v-on:click="savePurchase(purchase, $event)" class="btn btn-success"><i class="fa fa-floppy-o" aria-hidden="true"></i> @lang('glob.save')</button>
							<button v-if="purchaseOp=='edit'" :disabled="purchasedItems.length == 0 ? true : false" v-on:click="updatePurchase(purchase, $event)" class="btn btn-success"><i class="fa fa-floppy-o" aria-hidden="true"></i> @lang('glob.update')</button>

							<button class="btn btn-danger"  v-on:click="slideUp()" data-toggle="collapse" data-target="#purchaseFormDiv">@lang('glob.cancel')</button>
							<button class="btn btn-cyan" v-on:click="addNew()"><i class="fa fa-refresh" aria-hidden="true"></i> @lang('glob.reset')</button>
						</div>
						
					</div>

				</div>
				<!-- .box-body -->
			</div>
			<!-- .box .box-primary -->
		</div>
		<!-- .col-md-12 -->
	</div>
	<!--end .row .customer-box -->


	@endif
	{{-- Purchase return table start--}}
	@include('pages.stock.purchase_return.purchase_return_table')
	{{-- purchase return table end --}}
</div>

@endsection

