<div class="row" style="margin-bottom:3px;">
	<div class="col-md-8">
		<button type="button" class="btn btn-primary" v-on:click="openModal($event)"> <i class="fa fa-plus"></i> Add</button>
	</div>
	<div class="col-md-4">
		<input type="text" v-model="search" class="form-control input-sm pull-right" placeholder="Search">		
	</div>
</div>


<!-- Modal -->
<div id="purchase-items" class="modal fade" role="dialog">
	<div class="modal-dialog"  style="width:75%">

		<!-- Modal content-->
		<div class="modal-content">
			<form v-on:submit.prevent="saveItem(item,$event)" >
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title" v-if="itemOp=='add'">Add Item</h4>
					<h4 class="modal-title " v-if="itemOp=='edit'">Update Item</h4>
				</div>
				<div class="modal-body">
					<div class="row">

						<!-- parent category -->
						<div class="col-md-3"> 
							<div class="form-group">
								<label >@lang('glob.category'){{-- <span style="color:red">*</span> --}}</label>
								<v-select  v-model="filter.parentCategory"  :options="categories" :text="'categorie'" :vparent="0" :vcheck="'parent_category'"  :vrequired="false"></v-select>

								
							</div>
						</div>
						<!-- End category -->


						<!-- sub category -->
						<div class="col-md-3"> 
							<div class="form-group">
								<label >@lang('glob.sub_category'){{-- <span style="color:red">*</span> --}}</label>

								<v-select  v-model="filter.category"  :options="categories" :text="'categorie'" :vparent="filter.parentCategory" :vcheck="'parent_category'"  :vrequired="false"></v-select>

							</div>
						</div>
						<!-- End category -->


						<!-- barcode -->
						<div class="col-md-3">
							<div class="form-group">
								<label >@lang('glob.barcode'){{-- <span style="color:red">*</span> --}}</label>
								<div class="input-group">
									<div class="input-group-addon">
										<i class="fa fa-barcode"></i>
									</div>
									<input type="text" class="form-control input-sm prevent-enter barcode-autocomplete" :data-cat="filter.category"  v-on:keyup.enter="getCatSubcat(item.item.barcode,$event)"  placeholder="@lang('glob.barcode')" v-model="item.item.barcode" required="">
									
								</div>
							</div>
						</div>
						<!-- end barcode -->
						<!-- product code -->
						<div class="col-md-3">
							<div class="form-group">
								<label >@lang('item.refrence_number'){{-- <span style="color:red">*</span> --}}</label>
								<div class="input-group">
									<div class="input-group-addon"><i class="icon ion-ios-pricetags"></i></div>
									<input type="text" class="form-control input-sm prevent-enter" v-on:keyup.enter="getCatSubcat(item.item.serial_number,$event)" placeholder="@lang('item.refrence_number')" v-model="item.item.serial_number" required="" disabled="" >
									
								</div>
							</div>
						</div>
						<!-- product code -->

						

					</div>
					<!-- .row -->


					<div class="row">
						<!--  item/product -->
						<div class="col-md-4">
							<div class="form-group">
								<label >@lang('item.item_product')<span style="color:red">*</span></label>

								<div class="input-group">
									<div class="input-group-addon"><i class="icon ion-bag"></i></div>
									<input type="text" class="form-control input-sm prevent-enter itemname-autocomplete" :data-cat="filter.category" v-on:keyup.enter="getCatSubcat(item.item.itemName,$event)" placeholder="@lang('item.item_name')" v-model="item.item.itemName" required="">
									
								</div>
							</div>
						</div>
						<!-- End item/product -->
						<!-- product code -->
						<div class="col-md-3">
							<div class="form-group">
								<label >@lang('item.product_code'){{-- <span style="color:red">*</span> --}}</label>
								<div class="input-group">
									<div class="input-group-addon"><i class="icon ion-ios-pricetags"></i></div>
									<input type="text" class="form-control input-sm prevent-enter" v-on:keyup.enter="getCatSubcat(item.item.productCode,$event)" placeholder="@lang('item.product_code')" v-model="item.item.productCode" >
									
								</div>
							</div>
						</div>
						<!-- product code -->
						<!-- item qty -->
						<div class="col-md-2">
							<div class="form-group">
								<label>@lang('item.qty')<span style="color:red">*</span></label>
								<div class="input-group">
									<div class="input-group-addon">
										<i class="icon ion-bag"></i>
									</div>
									<input type="text" name="qty" class="form-control  input-sm validate-decimal" maxlength="8"  v-model="item.qty" placeholder="@lang('item.qty')" required="">
								</div>
							</div>
						</div>
						<!-- end item qty -->

						{{-- Unit start --}}
						<div class="col-md-2">
							<div class="form-group">
								<label for="">@lang("glob.unit")<span style="color:red">*</span></label>

								<v-select  v-model="item.unit"  :options="units" :text="'unit'"   :vrequired="false"></v-select>

								
							</div>
						</div>

						{{-- Unit end --}}
					</div>
					<div class="row">
						<!-- C.P -->
						<div class="col-md-2">
							<div class="form-group">
								<label >@lang('item.c_p')<span style="color:red">*</span></label>
								<div class="input-group">
									<div class="input-group-addon"><i class="fa fa-money"></i></div>
									<input type="text" v-model="item.costPrise" name="cp" class="form-control input-sm validate-decimal" placeholder="{{ trans('item.c_p') }}"  maxlength = "6" required="">

								</div>
							</div>
						</div>
						<!-- End C.P -->

						<!-- C.P -->
						@if(Common::vatType('vat','vat_type')!=0)
						<div class="col-md-2">
							<div class="form-group">
								<label >@lang('item.cp_vat_percent')<span style="color:red">*</span></label>
								<div class="input-group">
									<div class="input-group-addon"><i class="fa fa-percent"></i></div>
									<input type="text" v-model="item.cp_vat" name="cp_vat" class="form-control input-sm validate-decimal" placeholder="{{ trans('item.cp_vat_percent') }}"  maxlength = "9" required="">

								</div>
							</div>
						</div>
						@endIf
						<!-- End C.P -->

						<div class="col-md-2">
							<div class="form-group">
								<label >@lang('glob.expiry_days')
									
								</label>
								<div class="input-group">
									<div class="input-group-addon">
										<i class="ion ion-calendar"></i>
									</div>
									<input type="text" v-on:change="getExpiryDate()" name="expiryDays" v-model="expiryDays" class="form-control  input-sm " placeholder="@lang('glob.expiry_days')" >
								</div>
							</div>
						</div>

						<!-- expiry date -->
						<div class="col-md-3">
							<div class="form-group">
								<label >@lang('glob.expiry_date')
									
								</label>
								<div class="input-group">
									<div class="input-group-addon">
										<i class="ion ion-calendar"></i>
									</div>
									<input type="text" name="expiryDate" id="expiryDate" v-model="item.expiryDate" class="form-control  input-sm datepicker" placeholder="@lang('glob.expiry_date')" >
								</div>
							</div>
						</div>
						<!-- end expiry date -->


						<!-- dis rt -->
						<div class="col-md-2">
							<div class="form-group">
								<label for="">@lang('glob.discount') %{{-- <span style="color:red">*</span> --}}</label>
								<input type="text" name="disRate" v-model="item.discount" class="form-control input-sm validate-decimal" placeholder="	{{ trans('stock.dis_rt') }}"  maxlength = "6" required="">
							</div>
						</div>
						<!-- end dis rt -->
					</div>
					<div class="row">
						

						{{-- Amount --}}
						<div class="col-md-3 pull-right">
							<div class="form-group well">
								<label >@lang('stock.total_Amount')</label>: <span style="font-weight: 700">@{{ fixtIt(amount) }}</span>
							</div>
						</div>
						{{-- End amount --}}
						<div class="col-md-3 pull-right">
							<div class="form-group">
								<label for="">@lang("stock.total_discount")</label> : 
								<input type="text" class="input-sm form-control" readonly="" v-model="totalDiscount" disabled="">
							</div>
						</div>
					</div>

				</div>
				<div class="modal-footer">
					<button :disabled="(!item_check ? true :false)" type="submit" v-if="itemOp=='add'"  class="btn btn-primary pull-left"> <i class="fa fa-floppy-o"></i> @lang('glob.add') </button>
					<button :disabled="(!item_check ? true :false)" type="submit" v-if="itemOp=='edit'"  class="btn btn-primary pull-left"> <i class="fa fa-floppy-o"></i> @lang('glob.update') </button>
					<a  class="btn btn-cyan pull-left" v-on:click="clearItems()"><i class="fa fa-refresh"></i> @lang('glob.reset')</a>
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</form>
		</div>

	</div>
</div>

<div class="box box-default" style="border-top:0">

	<div class="box-body" style="margin:1px;border:2px solid #c8c8c8; border-radius: 5px;">
		<div class="table-spinner" style="display: none">
			<i class="fa fa-spinner fa-pulse fa-spin  fa-4x fa-fw"></i>
			<span class="sr-only">Loading...</span>
		</div>
		<!-- .row -->
		<div class="row">
			<div class="col-md-12 table-responsive item-table">
				<table class="table table-bordered table-striped">

					<thead >
						<tr>
							<th style="width:108.33px">@lang('stock.item_code')</th>
							<th style="width:108.33px">@lang('item.refrence_number')</th>
							<th style="width:108.33px">@lang('item.item_name')</th>
							<th style="width:108.33px">@lang('glob.sub_category')</th>
							<th style="width:108.33px">@lang('item.qty')</th>
							<th style="width:108.33px">@lang('glob.unit')</th>
							<th style="width:108.33px">@lang('glob.rate')</th>
							@if(Common::vatType('vat','vat_type')!=0)
							<th style="width:108.33px">@lang('item.cp_vat_percent')</th>
							@endIf
							<th style="width:108.33px">@lang('stock.amt')</th>
							<th style="width:108.33px">@lang('glob.discount') %</th>
							<th style="width:10.33px">@lang('glob.actions')</th>

						</tr>
					</thead>
					<tbody>
						<tr v-bind:class="{ 'bg-green': (p.type=='new') }" v-show="filterBySearch(p)" v-for="(p, index) in purchasedItems">
							<td><input type="text" class="form-control input-sm" v-model="p.item.barcode" v-show="(p.type=='new')"><span v-show="(p.type!='new')">@{{ p.item.barcode }}</span> </td>
							<td> <input type="text" class="form-control input-sm validate-decimal" v-model="p.serial_number" required></td>
							<td> <input type="text" class="form-control input-sm" v-model="p.item.itemName" v-show="(p.type=='new')"><span v-show="(p.type!='new')">@{{ p.item.itemName }} </span> </td>
							<td> <input type="text" class="form-control input-sm" v-model="p.item.categories.categorie" v-show="(p.type=='new')"> <span v-show="(p.type!='new')">@{{ p.item.categories.categorie }}</span></td>
							<td>  <input type="text" class="form-control input-sm validate-decimal" v-model="p.qty"></td>
							<td> <select name="unit" class="form-control input-sm " v-model="p.unit"  required>
								<option value=""></option>
								<option v-for="u in units" :value="u.id">@{{ u.unit }}</option>
							</select></td>
							<td> <input type="text" class="form-control input-sm validate-decimal" v-model="p.costPrise"></td>
							@if(Common::vatType('vat','vat_type')!=0)
							<td> <input type="text" class="form-control input-sm validate-decimal" v-model="p.cp_vat"></td>@endIf
							@if(Common::vatType('vat','vat_type')!=2)
							<td> @{{ fixtIt( p.amount = p.qty *  p.costPrise ) }} </td>
							@else
							<td> @{{ fixtIt( p.amount = ((p.costPrise * p.cp_vat/100) *  p.qty) + p.qty *  p.costPrise ) }} </td>
							@endIf
							<td> <input type="text" class="form-control input-sm validate-decimal" v-model="p.discount"></td>
							<td>
								
								<a  v-on:click="deleteItem(p, index)" class="btn btn-xs btn-danger btn-circle "><i class="fa fa-trash" aria-hidden="true"></i></a> 							
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			{{-- .col-md-12 --}}
		</div> 
	</div>
</div>