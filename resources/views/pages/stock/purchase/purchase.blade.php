@extends('layouts.app')

@section('page-title')@lang('glob.purchase')@endsection

@section('page-subtitle')@lang('stock.add_edit_purchase')@endsection 

@section ('breadcrumb')

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> @lang('glob.dashboard')</a></li>
	<li class="active">@lang('glob.purchase')</li>
</ol>

@endsection

@section('content') 
<div id="purchase-module">
	@if(Common::can_create('purchase'))
	<!-- Button div -->
	<div class="row">
		<div class="col-md-6">
			<a class="btn bg-purple new-customer" v-on:click="openSlide()" >@lang('glob.add_new')</a>
		</div>
		<div class="col-md-6">
			<a href="{{ asset('sample/purchase_import.xlsx') }}" class="btn btn-primary  pull-right" download="" >
				<i class="fa fa-download" aria-hidden="true"></i>
				<strong> Sample Import Excel</strong>
			</a>
		</div>
	</div>
	<!-- Button div end -->
	<div class="row">
		<div id="purchaseFormDiv" class="purchaseFormDiv  margin-top-space" style="display: none">
			<div class=" customer-box">  
				<div class="col-md-12">
					<div class="box box-primary">
						<div class="box-body">
							<div class="row">
								<div class="col-md-12">
									<label>@lang('stock.mrn_no') :</label> 
									<span> @{{ purchase.id }}</span>
								</div>
							</div>		

							<div class="row">

								<!-- mrn date -->
								<div class="col-md-2">
									<div class="form-group">
										<label>@lang('stock.mrn_date')<span style="color:red">*</span></label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="ion ion-calendar"></i>
											</div> 
											<input type="text" v-model="purchase.mrn_date" name="mrn_date" class="form-control  input-sm datepicker-futureDate" id="mrn_date" placeholder="@lang('stock.mrn_date')" required="">
										</div>
									</div>
								</div>
								<!-- end mrn date -->

								<!-- supplier -->
								<div class="col-md-3">
									<div class="form-group">
										<label >@lang('supplier.supplier')<span style="color:red">*</span></label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="fa fa-user"></i>
											</div>
											<v-select  v-model="purchase.supplier_id" :vrequired="true" :options="suppliers" :text="'name'" v-on:vchange="onsupplierChange()" >
												<option value="">Select</option>
											</v-select>

										</div>
									</div>
								</div>
								<!-- end supplier -->


								<!--po no-->
								<div class="col-md-3">
									<div class="form-group" >
										<label>@lang('stock.po_no')</label>
										<div class="input-group">
											<div class="input-group-addon" >
												<i class="fa fa-cart-plus" v-show="!spinner_po"></i>
												<i class="fa fa-spinner fa-pulse fa-fw" v-show="spinner_po"></i>
											</div>

											<select v-on:change="getItems(purchase.po_no)" v-model="purchase.po_no" name="po_No" class="form-control  input-sm " placeholder="@lang('stock.po_no')" :disabled="(purchaseOp=='edit') ? true : false">
												<option value="" ></option>
												<option :value="p.id" v-if="p.supplier_id==purchase.supplier_id" v-for="p in purchaseOrders" >@{{ p.id }} <small style="font-size: 10px;">(@{{ p.purchase_order_date }})</small> </option>
											</select>
										</div>
									</div>
								</div>
								<!-- end po no -->

								<!-- s. bill no -->
								<div class="col-md-2">
									<div class="form-group">
										<label >@lang('stock.stock_transfer_no')<span style="color:red">*</span></label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="fa fa-file-text-o"></i>
											</div>
											<input type="text" v-model="purchase.supplierBillNo" name="supplierBillNo" class="form-control  input-sm " placeholder="@lang('stock.stock_transfer_no')" required="">
										</div>
										<span v-if="billNoDuplicate" class="help is-danger">Entered Bill No. is already present for selected supplier</span>
									</div>
								</div>
								<!-- end s.bill no. -->


								<!-- s. bill date -->
								<div class="col-md-2">
									<div class="form-group">
										<label>@lang('stock.stock_transfer_date')<span style="color:red">*</span></label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="ion ion-calendar"></i>
											</div>
											<input type="text" v-model="purchase.supplier_billdate" id="supplier_billdate" name="supplier_billdate" class="form-control  input-sm datepicker" placeholder="@lang('stock.stock_transfer_date')" required="">
										</div>
									</div>
								</div>
								<!-- s. bill date -->

							</div>
							<!-- .row -->
							{{-- Purchase item box start--}}
							@include("pages.stock.purchase.purchase-items")
							{{-- Purchase item box end --}}


							<div class="well margin-top-space">
								<div class="row">
									<div class="col-md-5">
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label >@lang('glob.purchase')<span style="color:red">*</span></label>

													<select v-model="purchase.purchase"  name="purchase" id="" class="form-control input-sm"   required>
														<option value=""></option>
														<option value="1">Cash</option>
														<option value="2">Credit</option>
													</select>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label >@lang('glob.receiverd_by')<span style="color:red">*</span></label>
													<v-select  v-model="purchase.receivedBy" :vrequired="true" :options="salesman" :text="'name'"></v-select>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-12" v-show="purchaseOp=='edit'">	
												<div class="form-group">
													{!! Form::label('title', trans('glob.remark')) !!}<label><span style="color:red">*</span></label>
													<textarea name="remark"  rows="4" class="form-control input-sm fix-textarea" placeholder="{{ trans('glob.remark') }}"  v-model="purchase.remark" :required="(purchaseOp=='edit') ? true : false"></textarea>
												</div>
											</div>
										</div>

									</div>

									<div  class="col-md-7" >
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



							<div class="row">
								<div class="col-md-12">


									<button v-if="purchaseOp=='add'" :disabled="purchasedItems.length == 0 ? true : false" v-on:click="savePurchase(purchase, $event)" class="btn btn-success"><i class="fa fa-floppy-o" aria-hidden="true"></i> @lang('glob.save')</button>

									<button v-if="purchaseOp=='edit'" :disabled="purchasedItems.length == 0 ? true : false" v-on:click="updatePurchase(purchase, $event)" class="btn btn-success"><i class="fa fa-floppy-o" aria-hidden="true"></i> @lang('glob.update')</button>

									<button v-if="purchaseOp=='add'" :disabled="purchasedItems.length == 0 ? true : false" v-on:click="savePurchasePrint(purchase, $event)" class="btn btn-success"><i class="fa fa-floppy-o" aria-hidden="true"></i> @lang('glob.save') & @lang('glob.print')</button>

									<button v-if="purchaseOp=='edit'" :disabled="purchasedItems.length == 0 ? true : false" v-on:click="updatePurchasePrint(purchase, $event)" class="btn btn-success"><i class="fa fa-floppy-o" aria-hidden="true"></i> @lang('glob.update') & @lang('glob.print')</button>







									<button class="btn btn-danger"  v-on:click="slideUp()" data-toggle="collapse" data-target="#purchaseFormDiv">@lang('glob.cancel')</button>

									<button class="btn btn-cyan" v-on:click="openSlide()"><i class="fa fa-refresh" aria-hidden="true"></i> @lang('glob.reset')</button>


									<button class="btn btn-indigo pull-right" data-toggle="modal" data-target="#purchase-import"> <i class="fa fa-upload" aria-hidden="true"></i> @lang('glob.import')</button>

									@include('pages.stock.purchase.purchase_import')

								</div>
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	@endif
	<!-- view table data  -->
	<div class="row" style="margin-top: 15px;">
		@include("pages.stock.purchase.purchase-table")
	</div>
	<!-- view table data end -->


</div>

@endsection

