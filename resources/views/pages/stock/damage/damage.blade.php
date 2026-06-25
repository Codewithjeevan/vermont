@extends('layouts.app')

@section('page-title')@lang('stock.expiry_stock')@endsection

@section('page-subtitle')@lang('stock.add_edit_expiry_stock')@endsection 

@section ('breadcrumb')

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> @lang('glob.dashboard')</a></li>
	<li class="active">@lang('stock.expiry_stock')</li>
</ol>

@endsection

@section('content') 
<div class="damage_stock">
	@if(Common::can_create('damage_stock'))
	<!-- Button div -->
	<div class="row">
		<div class="col-md-12 margin-bottom">
			<a  class="btn bg-purple new-customer"  v-on:click="openSlide()" >@lang('glob.add_new')</a>
		</div>
	</div>
	<!-- Button div end -->

	<div id="damageFormDiv" class="collapse">

		<div class="row customer-box">  
			<div class="col-md-12">
				<div class="box box-primary">
					<div class="box-body">

						<div class="row">
							<!-- mrn no -->
							<div class="col-md-4">
								<div class="form-group">
									<label for="mrn_no">@lang('stock.damage_no') :</label> 
									<span>@{{ expStock.id }}</span>
								</div>
							</div>
							<!-- end mrn no -->
						</div>
						<!-- .row end mrn no -->

						<div class="row">
							<!-- Slip date -->
							<div class="col-md-3">
								<div class="form-group">
									<label for="">@lang('stock.slip_date')<span style="color:red">*</span></label>
									<div class="input-group">
										<div class="input-group-addon">
											<i class="ion ion-calendar"></i>
										</div>
										<input id="slip_date_id" type="text" name="slip_date" class="form-control  input-sm datepicker" placeholder="@lang('stock.slip_date')" required="" v-model="expStock.slip_date">
									</div>
								</div>
							</div>
							<!-- enterd by-->
							<div class="col-md-3">
								<div class="form-group">
									<label for="">@lang('stock.type')<span style="color:red">*</span></label>
									<div class="input-group">
										<div class="input-group-addon">
											<i class="fa fa-list"></i>
										</div>
										<select name="item_id"  class="form-control input-sm"  v-model="expStock.type" :disabled="(updateExpiryOrder) ? true : false" required >
											<option value="0">Damage Item</option>
											<option value="1">Expiry Item</option>
										</select>
									</div>
								</div>
							</div>
							<!-- end enterd by -->
							<!-- end slip date -->
							<div class="col-md-2">
								<a  v-on:click="openModal()" class="btn btn-primary btn-sm margin-top-space" ><i class="fa fa-plus"></i> @lang('glob.add') @lang('glob.item')</a>
							</div>
						</div>
						<!-- .row -->
						<!-- modal start -->
						<div id="damage-items" class="modal fade" role="dialog">
							<div class="modal-dialog"  style="width:75%">
								<!-- Modal content-->
								<div class="modal-content">
									<form v-on:submit.prevent="saveItem(item, $event)" >
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal">&times;</button>
											<h4 class="modal-title">@lang(
												'stock.add_expiry_item')</h4>
											</div>
											<div class="modal-body">

												<div class="col-md-8">
													<div class="row">

														<!-- item name -->
														<div class="col-md-3" v-if="expStock.type == 1" >
															<div class="form-group">
																<label for="">@lang('stock.mrn_no')<span style="color:red">*</span></label>
																<div class="input-group">
																	<input id="mrn_no" type="text" name="mrn_no" class="form-control input-sm prevent-enter "  placeholder="@lang('stock.mrn_no')" v-model="item.mrn_no" v-on:keyup.enter="getItemByMRN(item.mrn_no,$event)" required=""  >
																</div>
															</div>
														</div>
														<!-- end item name -->

														<!-- item name -->
														<div :class="{'col-md-6': (expStock.type == 0), 'col-md-5': (expStock.type == 1)}">
															<div class="form-group">
																<label for="">@lang('item.item_name')<span style="color:red">*</span></label>
																<div class="input-group">
																	<div class="input-group-addon loader">
																		<i class="icon ion-bag"></i>
																	</div>
																	<div :class=" (expStock.type == 1) ? 'display-none-input':'display-block-input'">
																		<input type="text" name="itemName" class="form-control input-sm prevent-enter itemname-autocomplete"  placeholder="@lang('item.item_name')" v-model="item.items.itemName" v-on:keyup.enter="getStock(item.items.itemName,$event)" required=""  >
																	</div >

																	<div :class=" (expStock.type == 0) ? 'display-none-input':'display-block-input'">
																		<v-select :options="items" :text="'itemName'" v-model="item.itemId" v-on:vchange="getItemDetails(item.itemId)" :vrequired="(expStock.type == 0) ? false : true "></v-select>
																	</div>
																</div>
															</div>
														</div>
														<!-- end item name -->

														<!-- barcode -->
														<div :class="{'col-md-6': (expStock.type == 0), 'col-md-4': (expStock.type == 1)}">
															<div class="form-group">
																<label >@lang('glob.barcode')<span style="color:red">*</span></label>
																<div class="input-group">
																	<div class="input-group-addon loader">
																		<i class="fa fa-barcode"></i>
																	</div>
																	<div :class=" (expStock.type == 1) ? 'display-none-input':'display-block-input'">
																		<input type="text" name="barcode" class="form-control  input-sm prevent-enter barcode-autocomplete"  placeholder="@lang('glob.barcode')" v-model="item.items.barcode" v-on:keyup.enter="getStock(item.items.barcode,$event)" required=""  >
																	</div>
																	<div :class=" (expStock.type == 0) ? 'display-none-input':'display-block-input'">
																		<v-select :options="items" :text="'barcode'" v-model="item.itemId" v-on:vchange="getItemDetails(item.itemId)" :vrequired="(expStock.type == 0) ? false : true"></v-select>
																	</div>
																</div>
															</div>
														</div>
														<!-- end barcode -->
													</div>
													<!-- .row -->

													<div class="row">
														<!-- item rate -->
														<div class="col-md-4">
															<div class="form-group">
																<label for="">@lang('billing.rate')<span style="color:red">*</span></label>
																<div class="input-group">
																	<div class="input-group-addon">
																		<i class="fa fa-money"></i>
																	</div>
																	<input type="text" name="rate" class="form-control  input-sm validate-decimal" placeholder="@lang('billing.rate')" v-model="item.rate" readonly >
																</div>
															</div>
														</div>
														<!-- end item rate -->

														<!-- stock -->
														<div class="col-md-4">
															<div class="form-group">
																<label for="">@lang('glob.stock')<span style="color:red">*</span></label>
																<div class="input-group">
																	<div class="input-group-addon">
																		<i class="fa fa-stack-overflow"></i>
																	</div>
																	<input type="text" name="stock" class="form-control  input-sm" placeholder="@lang('glob.stock')" v-model="item.stock"  readonly>
																</div>
															</div>
														</div>
														<!-- end stock -->

														<!-- item qty -->
														<div class="col-md-4">
															<div class="form-group">
																<label for="">@lang('item.qty')<span style="color:red">*</span></label>
																<div class="input-group">
																	<div class="input-group-addon">
																		<i class="icon ion-bag"></i>
																	</div>
																	<input type="text" name="qty" class="form-control  input-sm validate-decimal" placeholder="@lang('item.qty')" v-model="item.qty" required="" maxlength="6" >
																</div>
															</div>
														</div>
														<!-- end item qty -->
													</div>
													<!-- .row -->
												</div>
												<!-- .col-md-7 -->

												<!-- remark -->
												<div class="col-md-4">
													<div class="form-group">
														<label for="">@lang('glob.remark')<span style="color:red">*</span></label>
														<textarea name="remark" rows="4" class="form-control  input-sm fix-textarea" placeholder="@lang('glob.remark')" v-model="item.remark" required=""></textarea>
													</div>
												</div>
												<!-- end remark -->
											</div>
											<div class="modal-footer">
												<button type="submit" v-if="itemOp=='add'"  class="btn btn-primary pull-left"> <i class="fa fa-floppy-o"></i> @lang('glob.add') </button>
												<button type="submit" v-if="itemOp=='edit'"  class="btn btn-primary pull-left"> <i class="fa fa-floppy-o"></i> @lang('glob.update') </button>
												<a  class="btn btn-cyan pull-left" v-on:click="resetItemModel()"><i class="fa fa-refresh"></i> @lang('glob.reset')</a>
												<button type="button" class="btn btn-default" v-on:click="resetItemModel()" data-dismiss="modal">Close</button>
											</div>
										</form>
									</div>

								</div>
							</div>
							<!-- end modal -->

							<div class="box" style="border-top:0">
								<div class="box-body" style="margin:1px;border:2px solid #c8c8c8; border-radius: 5px;">

									<div class="col-md-12 table-responsive" >
										<table class="table table-bordered table-striped  ">
											<thead>
												<tr>	
													<th>@lang('glob.barcode')</th>
													<th>@lang('item.item_name')</th>
													<th>@lang('billing.qty')</th>
													<th>@lang('item.c_p')</th>
													<th>@lang('billing.rate')</th>
													<th>@lang('billing.amt')</th>
													<th>@lang('glob.remark')</th>
													<th >@lang('glob.actions')</th>
												</tr>
											</thead>

											<tbody>
												<tr v-for="( i, index) in expStock.itemList">
													<td>@{{  i.items.barcode  }}</td>
													<td>@{{  i.items.itemName  }}</td>
													<td>@{{  fixQty(i.qty) }}</td>
													<td>@{{  i.cp  }}</td>
													<td>@{{  i.rate  }}</td>
													<td>@{{   fixtIt(i.amt)  }}</td>
													<td>@{{  i.remark  }}</td>
													<td>
														<a  v-on:click="editExpiryItems(i , index, $event)" class="btn btn-xs btn-info btn-circle " ><i class="fa fa-pencil" aria-hidden="true"></i></a>
														<a  v-on:click="deleteexpItem(i , index)" class="btn btn-xs btn-danger btn-circle "><i class="fa fa-trash" aria-hidden="true"></i></a> 							
													</td>
												</tr>

											</tbody>
										</table>

									</div>
									{{-- .col-md-12 --}}


								</div>
								<!-- /.box-body -->
							</div>
							<!-- end table box -->
							<div class="row">
								<!-- enterd by-->
								<div class="col-md-4">
									<div class="form-group">
										<label for="">@lang('stock.enterd_by')<span style="color:red">*</span></label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="fa fa-user"></i>
											</div>
											<select name="item_id"  class="form-control input-sm"  v-model="expStock.enterd_by" required>
												<option value=" "></option>
												<option v-for="u in users" :value=" u.id ">@{{u.email}}</option>
											</select>
										</div>
									</div>
								</div>
								<!-- end enterd by -->
								<div class="col-md-4">
									<div class="form-group">
										<label for="">@lang('stock.approx_value_of_cp')<span style="color:red">*</span></label>
										<input type="text" name="approx_value_of_cp"  class="form-control  input-sm validate-decimal" placeholder="@lang('stock.approx_value_of_cp')" required=""  v-model="expStock.approx_value_of_cp">
									</div>
								</div>

								<div class="col-md-4">
									<div class="form-group">
										<label for="">@lang('stock.approx_value_of')<span style="color:red">*</span></label>
										<input type="text" name="approx_value_of"  class="form-control  input-sm validate-decimal" placeholder="@lang('stock.approx_value_of')" required=""  v-model="expStock.approx_value_of">
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-4" v-show="updateExpiryOrder">	
									<div class="form-group">
										{!! Form::label('title', trans('glob.remark')) !!}<label><span style="color:red">*</span></label>
										<textarea name="remark"  rows="4" class="form-control input-sm fix-textarea" placeholder="{{ trans('glob.remark') }}"   v-model="expStock.remark" :required="(updateExpiryOrder) ? true : false"></textarea>
									</div>
								</div>
							</div>

							<div class="box-footer no-shadow">
								<div class="pull-left">
									<button class="btn btn-success" v-on:click="store(expStock, $event)" v-if="saveExpiryOrder" :disabled="(expStock.itemList.length != 0 && expStock.slip_date !='' && expStock.enterd_by > 0 && expStock.approx_value_of!='' &&  expStock.approx_value_of_cp!='') ? false :true" ><i class="fa fa-floppy-o" aria-hidden="true"></i> @lang('glob.save')</button>
									<button class="btn btn-success" v-on:click="updateExpiryStock(expStock, $event)" v-if="updateExpiryOrder" :disabled="(expStock.itemList.length != 0 && expStock.slip_date !='' && expStock.enterd_by > 0 && expStock.approx_value_of!='' && expStock.approx_value_of_cp!='' && expStock.remark!='') ? false :true"><i class="fa fa-floppy-o" aria-hidden="true"></i> @lang('glob.update')</button>
									<button class="btn btn-cyan" v-on:click="openSlide()"><i class="fa fa-refresh" aria-hidden="true"></i> @lang('glob.reset')</button>
									<a  class="btn btn-danger add-bank" v-on:click="slideUp()">@lang('glob.cancel')</a>
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
		</div>

		@endif
		<!-- view table data  -->
		<div class="row">
			<div class="col-md-12 table-responsive damage-stock-table">

				<table class="table table-bordered table-striped datatable1 ">

					<thead>
						<tr>
							<th>#{{-- @lang('stock.new_damage_no') --}}</th>
							<th>@lang('stock.slip_date')</th>
							<th>@lang('stock.type')</th>
							<th>@lang('stock.enterd_by')</th>
							<th>@lang('stock.approx_value_of_cp')</th>
							<th>@lang('stock.approx_value_of')</th>
							@if(Common::can_update('damage_stock') || Common::can_delete('damage_stock'))
							<th style="width:4%">@lang('glob.actions')</th>
							@endif
						</tr>
					</thead>
					<tbody>
						@foreach($damageOrders as $e)
						<tr>
							<td>{{$e->id}}</td>
							<td>@if($e->slip_date!=null || $e->slip_date!=''){{ date($date_format, strtotime($e->slip_date))}} @endif</td>
							<td>@if(isset($e->type) && $e->type == 0) {{ 'Damage Item Slip' }} @elseif(isset($e->type) && $e->type == 1){{ 'Expiry Item Slip'}} @endif</td>
							<td>{{$e->users->email}}</td>
							<td>{{Common::fixIt($e->approx_value_of_cp)}}</td>
							<td>{{ Common::fixIt($e->approx_value_of)}}</td>
							@if(Common::can_update('damage_stock') || Common::can_delete('damage_stock'))
							<td>
								@if(Common::can_update('damage_stock'))
								<a v-on:click="editDamageStock({{ $e->id  }}, $event)" class="btn btn-xs btn-info btn-circle" ><i class="fa fa-pencil" aria-hidden="true"></i></a>
								@endif
								@if(Common::can_delete('damage_stock'))

								<a v-on:click="deleteExpiryStock({{ $e->id }}, $event)" class="btn btn-xs btn-danger btn-circle"><i class="fa fa-trash" aria-hidden="true"></i></a> 
								@endif
							</td>
							@endif
						</tr>
						@endforeach
					</tbody>
					<tfoot class="showSearch">
						<tr>
							<th > #{{-- @lang('stock.new_damage_no') --}}</th>
							<th class="search">@lang('stock.slip_date')</th>
							<th class="search">@lang('stock.type')</th>
							<th class="search">@lang('stock.enterd_by')</th>
							<th class="search">@lang('stock.approx_value_of_cp')</th>
							<th class="search">@lang('stock.approx_value_of')</th>
							@if(Common::can_update('damage_stock') || Common::can_delete('damage_stock'))
							<th style="width:4%">@lang('glob.actions')</th>
							@endif
						</tr>
					</tfoot>
				</table>
			</div>

		</div>
		<!-- view table data end -->




	</div>

	@endsection

