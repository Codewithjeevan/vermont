@extends('layouts.app')

@section('page-title')@lang('glob.purchase_order')@endsection

@section('page-subtitle')@lang('glob.add_edit_purchase_order')@endsection 

@section ('breadcrumb')

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> @lang('glob.dashboard')</a></li>
	<li class="active">@lang('glob.purchase_order')</li>
</ol>

@endsection

@section('content')
<div class="purchase_order"> 

	@if(Common::can_create('purchase_order'))
	<!-- Button div -->
	<div class="row">
		<div class="col-md-12 margin-bottom">
			<a  class="btn bg-purple new-customer" v-on:click="openSlide()" >@lang('glob.add_new')</a>
		</div>
	</div>
	<!-- Button div end -->

	<!-- collapse form -->

	<div id="pOrderFormDiv" class="collapse" >

		<form  @submit.prevent="purchaseOrderSave(pOrder, opType , $event)" novalidate="">

			<div class="row customer-box"> 

				<div class="col-md-12">

					<div class="box box-primary"> 

						<div class="box-body">

							<div class="row">

								<!-- purchase order id -->
								<div class="col-md-4">
									<div class="form-group">
										<label for="mrn_no">@lang('stock.purchase_order_id') :</label> 
										<span> @{{pOrder.id}} </span>
									</div>
								</div>
								<!-- end purchase order id -->

							</div>
							<!-- .row -->

							<div class="row">
								<!-- purchase order date -->
								<div class="col-md-3">
									<div class="form-group">
										<label>@lang('stock.purchase_order_date')<span style="color:red">*</span></label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="ion ion-calendar"></i>
											</div>
											<input id="poDateId" type="text" name="purchase_order_date" class="form-control  input-sm datepicker" placeholder="@lang('stock.purchase_order_date')" required="" v-model="pOrder.purchase_order_date">
										</div>
									</div>
								</div>
								<!-- end purchase order date -->
								<!-- supplier name -->
								<div class="col-md-5">
									<div class="form-group">
										<label>@lang('supplier.supplier_name')<span style="color:red">*</span></label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="fa fa-user"></i>
											</div>
											<v-select :options="suppliers" :text="'name'"   v-model="pOrder.supplier_id" :vrequired="true" >
											</v-select>
											
										</div>
									</div>
								</div>
								<!-- end supplier name -->

							</div>
							<!-- .row -->
							<div class="box" style="border-top:0">

								<div class="box-body" style="margin:1px;border:2px solid #c8c8c8; border-radius: 5px;">

									<div class="row">
										<!-- category main -->
										<div class="col-md-3">
											<div class="form-group">
												<label>@lang('glob.category')</label>
												<div class="input-group">
													<div class="input-group-addon">
														<i class="fa fa-tags"></i>
													</div>
													<v-select :options="categories" :vcheck="'parent_category'" :vparent="'0'"  :text="'categorie'"  v-model="purchaseItem.filter.parentCat"  :vrequired="false" >
													</v-select>
													
												</div>
											</div>
										</div>
										<!-- end category main-->
										
										<!-- Sub Category -->
										<div class="col-md-3">
											<div class="form-group">
												<label>@lang('glob.sub_category')</label>
												<div class="input-group">
													<div class="input-group-addon">
														<i class="fa fa-tags"></i>
													</div>
													<v-select :options="categories" :vcheck="'parent_category'" :vparent="purchaseItem.filter.parentCat"  :text="'categorie'"  v-model="purchaseItem.filter.cat"  :vrequired="false" >
													</v-select>
													
												</div>
											</div>
										</div>
										<!-- end Sub Category -->
										
										<!-- item name -->
										<div class="col-md-3">
											<div class="form-group">
												<label>@lang('item.item_name')<span style="color:red">*</span></label>
												<div class="input-group">
													<div class="input-group-addon">
														<i class="icon ion-bag"></i>
													</div>
													<input type="text" class="form-control input-sm  prevent-enter itemname-autocomplete" :data-cat="purchaseItem.filter.cat" v-on:keyup.enter="getCatSubcat(purchaseItem.items.itemName,$event)" v-model="purchaseItem.items.itemName">
													
												</div>
											</div>
										</div>
										<!-- end item name -->

										<!-- Sattrt Barcode -->
										<div class="col-md-3">
											<div class="form-group">
												<label >@lang('glob.barcode')<span style="color:red">*</span></label>
												<div class="input-group">
													<div class="input-group-addon">
														<i class="icon ion-bag"></i>
													</div>
													<input type="text" class="form-control input-sm prevent-enter  barcode-autocomplete" :data-cat="purchaseItem.filter.cat" v-on:keyup.enter="getCatSubcat(purchaseItem.items.barcode,$event)" v-model="purchaseItem.items.barcode">
													
												</div>
											</div>
										</div>
										<!-- Sattrt Barcode -->
									</div>
									<!-- .row -->

									<div class="row">
										<!--Refrence_Number-->
                                        <div class="col-md-3">
											<div class="form-group">
												<label>@lang('item.refrence_number')<span style="color:red">*</span></label>
												<div class="input-group">
													<div class="input-group-addon">
														<i class="fa fa-tags"></i>
													</div>
												<input type="text" name="refrence_number" class="form-control  input-sm"  placeholder="@lang('item.refrence_number')" required="" v-model="purchaseItem.items.serial_number" disabled="">
												</div>
											</div>
										</div>
										<!--End Refrence Number-->
										
										<!-- quantity -->
										<div class="col-md-3">
											<div class="form-group">
												<label>@lang('item.quantity')<span style="color:red">*</span></label>
												
												<input type="text" name="quantity" class="form-control  input-sm validate-decimal" maxlength="8"  placeholder="@lang('item.quantity')" required="" v-model="purchaseItem.quantity" >
												
											</div>
										</div>
										<!-- end quantity -->

										<!-- unit -->
										<div class="col-md-3">
											<div class="form-group">
												<label>@lang('glob.unit')<span style="color:red">*</span></label>
												<div class="input-group">
													<div class="input-group-addon"><i class="fa fa-th-large" aria-hidden="true"></i></div>
													<v-select :options="units" :text="'unit'"   v-model="purchaseItem.units.id">
														<option disabled value="0">Select Unit</option>
													</v-select>
													
												</div>

											</div>
										</div>
										<!-- End unit -->

										<!-- C.P -->
										<div class="col-md-3">
											<div class="form-group">
												<label>@lang('item.c_p')<span style="color:red">*</span></label>
												<div class="input-group">
													<div class="input-group-addon"><i class="fa fa-money"></i></div>
													<input type="text" name="cost_price" class="form-control input-sm validate-decimal" placeholder="{{ trans('item.c_p') }}"   v-model="purchaseItem.cost_price" maxlength="8" required>
												</div>
											</div>
										</div>
										<!-- End C.P -->
									</div>
									<div class="row">

										<div class="col-md-1" >
											<div v-if="addbutton == true">
												<button type="button" v-on:click="addpurchaseItem(purchaseItem)" class="btn btn-primary btn-sm" :disabled="((purchaseItem.quantity > 0) && purchaseItem.item_id!=''  && purchaseItem.units.id!=''  && purchaseItem.cost_price!='') ? false :true" ><i class="fa fa-floppy-o"></i> @lang('glob.add')</button>
											</div>
											<div v-if="updatebutton == true" >
												<button  type="button" v-on:click="updatepurchaseItem(purchaseItem)" class="btn btn-primary btn-sm" :disabled="((purchaseItem.quantity > 0) && purchaseItem.item_id!='' && purchaseItem.units.id!=''  && purchaseItem.cost_price!='') ? false :true"><i class="fa fa-floppy-o"></i> @lang('glob.update')</button>
											</div>
										</div>
									</div>
									
									<!--  table view -->
									<div class="box-footer">

										<div  class="row">

											<div class="col-md-12 table-responsive ">

												<table class="table table-bordered table-striped ">
													<thead >
														<tr>
															<th >@lang('glob.barcode')</th>
															<th >@lang('item.refrence_number')</th>
															<th >@lang('item.item_name')</th>
															<th >@lang('glob.unit')</th>
															<th >@lang('item.c_p')</th>
															<th>@lang('item.quantity')</th>
															<th style="width:10%">@lang('glob.actions')</th>
														</tr>
													</thead>
													<tbody>
														<tr v-for="( i, index) in pOrder.itemList">
															<td>@{{i.items.barcode}}</td>
															<td>@{{i.items.serial_number}}</td>
															<td>@{{i.items.itemName}}</td>
															<td>@{{i.units.unit}}</td>
															<td>@{{ fixtIt(i.cost_price)}}</td>
															<td>@{{ fixQty(i.quantity)}}</td>
															<td>
																<a v-on:click="editpurchaseItem(i, index)" class="btn btn-xs btn-info btn-circle" ><i class="fa fa-pencil" aria-hidden="true"></i></a>
																<a  v-on:click="deletepurchaseItem(i, index)" class="btn btn-xs btn-danger btn-circle "><i class="fa fa-trash" aria-hidden="true"></i></a> 
															</td>
														</tr>
													</tbody>
												</table>

											</div>

										</div>
										<!-- .row -->

									</div>
									<!-- end  table view -->

									@{{tempItem.itemList }}

								</div>
							</div>
							<!-- end border box -->

							<div class="row">
								<div class="col-md-4" v-show="opType=='edit'">	
									<div class="form-group">
										{!! Form::label('title', trans('glob.remark')) !!}<label><span style="color:red">*</span></label>
										<textarea name="description"  rows="4" class="form-control input-sm fix-textarea" placeholder="{{ trans('glob.remark') }}"  v-model="pOrder.remark" :required="(opType=='edit') ? true : false"></textarea>
									</div>
								</div>
							</div>
							
							<div class="row">
								<div class="col-md-12">
									
									<button v-if="opType=='add'" type="submit" class="btn btn-success save-loader" :disabled="(pOrder.purchase_order_date !='' && pOrder.supplier_id !='' && pOrder.itemList.length !=0 )? false : true"><i class="fa fa-floppy-o"></i> @lang('glob.save') </button>

									<button v-if="opType=='edit'" type="submit"  class="btn btn-success update-loader" :disabled="(pOrder.purchase_order_date !='' && pOrder.supplier_id !='' && pOrder.itemList.length !=0 && pOrder.remark!='')? false : true"><i class="fa fa-floppy-o"></i> @lang('glob.update')</button>

									<a  class="btn btn-cyan" v-on:click="openSlide()"><i class="fa fa-refresh"></i> @lang('glob.reset')</a>

									<a class="btn  btn-danger"  v-on:click="slideUp()">@lang('glob.cancel')</a>
									{{-- <a class="btn btn-indigo"><i class="fa fa-print" aria-hidden="true"></i> @lang('glob.print') @lang('glob.purchase_order')</a> --}}
									
								</div>
							</div>

						</div>					

					</div>
					<!-- .box .box-primary -->

				</div>
				<!-- .col-md-12 -->

			</div>
			<!--end .row .customer-box -->
		</form>
	</div>
	<!-- end collapse form -->
	@endif
	<!-- view table data  -->
	<div class="row">
		<div class="col-md-12 table-responsive pOrder-table">
			<table class="table table-bordered table-striped datatable1 ">
				
				<thead>
					<tr>
						<th >@lang('stock.purchase_order_id')</th>
						<th>@lang('stock.purchase_order_date')</th>
						<th>@lang('glob.supplier') @lang('glob.name')</th>
						<th>@lang('glob.status')</th>
						<th style="width:15%">@lang('glob.actions')</th>
						
					</tr>
				</thead>
				
				<tbody>
					@foreach($purchaseOrders as $p)					
					<tr>
						<td>{{ $p->id }}</td>
						<td>@if($p->purchase_order_date!=null || $p->purchase_order_date!=''){{ date($date_format, strtotime($p->purchase_order_date)) }} @endif</td>
						<td>{{ (isset($p->_supplier->name)) ? $p->_supplier->name : ''  }} </td>
						@if(is_null($p->po_no))
						<td style="color: red">Pending</td>
						@else
						<td style="color: green">Completed</td>
						@endif
						<td>
							@if(is_null($p->po_no))
							@if(Common::can_update('purchase_order'))							
							<a v-on:click="editOrder({{ $p->id  }}, $event)" class="btn btn-xs btn-info btn-circle" ><i class="fa fa-pencil" aria-hidden="true"></i></a>							
							@endif
							@if(Common::can_delete('purchase_order'))
							<a v-on:click="deleteOrder({{ $p->id }}, $event)" class="btn btn-xs btn-danger btn-circle "><i class="fa fa-trash" aria-hidden="true"></i></a> 
							@endif
							@else
							@if(Common::can_update('purchase_order'))							
							<a class="btn btn-xs btn-info btn-circle" disabled><i class="fa fa-pencil" aria-hidden="true"></i></a>							
							@endif
							@if(Common::can_delete('purchase_order'))
							<a class="btn btn-xs btn-danger btn-circle" disabled><i class="fa fa-trash" aria-hidden="true"></i></a> 
							@endif
							@endif							
							
							<a  class="btn btn-xs btn-indigo btn-circle" href="{{ url('stock/purchase_orders/xls/'.$p->id ) }}" ><i class="fa fa-file-excel-o" aria-hidden="true"></i></a>
							
							<a  class="btn btn-xs btn-danger btn-circle" href="{{ url('stock/purchase_orders/pdf/'.$p->id ) }}" ><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>	

							<a v-on:click="sendMail({{ $p->id }}, $event)" class="btn btn-xs btn-success btn-circle "><i class="fa fa-envelope" aria-hidden="true"></i></a> 
							
							
						</td>
						
					</tr>

					@endforeach
				</tbody>

				<tfoot class="showSearch">
					<tr>
						<th class="search">@lang('stock.purchase_order_id')</th>
						<th class="search">@lang('stock.purchase_order_date')</th>
						<th class="search">@lang('glob.supplier') @lang('glob.name')</th>
						<th class="search">@lang('glob.status')</th>
						<th style="width:15%">@lang('glob.actions')</th>
						
					</tr>
				</tfoot>
			</table>
		</div>
		
	</div>
	<!-- view table data end -->



</div>

@endsection

