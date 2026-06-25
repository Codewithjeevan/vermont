<div class="row" style="margin-bottom:3px;">
	<div class="col-md-8">
		<button type="button" class="btn btn-primary loadItems" v-on:click="openModal()"> <i class="fa fa-plus"></i> Add Item</button>
	</div>
	<div class="col-md-4">
		<input type="text" v-model="search" class="form-control input-sm pull-right" placeholder="Search items">		
	</div>
</div>


<!-- Purchase Return item add modal -->
<div id="pr-items" class="modal fade" role="dialog">
	<div class="modal-dialog"  style="width:75%">

		<!-- Modal content-->
		<div class="modal-content">
			<form v-on:submit.prevent="saveItem(item,$event)">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title" v-if="itemOp=='add'">Add Item</h4>
					<h4 class="modal-title" v-if="itemOp=='edit'">Update Item</h4>
				</div>
				<div class="modal-body">
					<div class="row">

						<!-- parent category -->
						<div class="col-md-3"> 
							<div class="form-group">
								<label >@lang('glob.category'){{-- <span style="color:red">*</span> --}}</label>
								<v-select  :options="categories" :text="'categorie'" v-model="filter.parentCategory" :vparent="0" :vcheck="'parent_category'" :vrequired="true"></v-select>

							</div>
						</div>
						<!-- End category -->


						<!-- sub category -->
						<div class="col-md-3"> 
							<div class="form-group">
								<label >@lang('glob.sub_category'){{-- <span style="color:red">*</span> --}}</label>

								<v-select  :options="categories" :text="'categorie'" v-model="filter.category" :vparent="filter.parentCategory" :vcheck="'parent_category'" :vrequired="true"></v-select>
								
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
									
									<input type="text" name="barcode" class="form-control  input-sm prevent-enter barcode-autocomplete" :data-cat="filter.category" placeholder="@lang('glob.barcode')" v-model="item.item.barcode" v-on:keyup.enter="getCatSubcat(item.item.barcode,$event)" required=""  >
									
									
								</div>
							</div>
						</div>
						<!-- end barcode -->

						<div class="col-md-3">
							<div class="form-group">
								<label >@lang('item.item_product')<span style="color:red">*</span></label>
								<div class="input-group">
									<div class="input-group-addon"><i class="icon ion-bag"></i></div>
									
									<input type="text" name="itemName" class="form-control  input-sm prevent-enter itemname-autocomplete" :data-cat="filter.category" placeholder="@lang('item.item_name')" v-model="item.item.itemName" v-on:keyup.enter="getCatSubcat(item.item.itemName,$event)" required=""  >
									
								</div>
							</div>
						</div>
					</div>
					<!-- .row -->
					<div class="row">

						<!-- refrence_number -->
						<div class="col-md-2">
							<div class="form-group">
								<label for="">@lang('item.refrence_number')<span style="color:red">*</span></label>
								<div class="input-group">
									<div class="input-group-addon"><i class="fa fa-tag"></i></div>
									<input v-model="item.item.serial_number" type="text" name="" class="form-control input-sm"  placeholder="{{ trans('item.refrence_number') }}" required="" disabled="">

								</div>
							</div>
						</div>
						<!-- End refrence_number -->


						<!-- C.P -->
						<div class="col-md-2">
							<div class="form-group">
								<label for="">@lang('item.c_p')<span style="color:red">*</span></label>
								<div class="input-group">
									<div class="input-group-addon"><i class="fa fa-money"></i></div>
									<input v-model="item.costPrise" type="text" name="" class="form-control input-sm validate-decimal"  placeholder="{{ trans('item.c_p') }}"  maxlength = "8" required="">

								</div>
							</div>
						</div>
						<!-- End C.P -->
						<!--CP_vat-->
							@if(Common::vatType('vat','vat_type')!=0)
						<div class="col-md-2">
							<div class="form-group">
								<label for="">@lang('item.cp_vat_percent')<span style="color:red">*</span></label>
								<div class="input-group">
									<div class="input-group-addon"><i class="fa fa-percent"></i></div>
									<input type="text" v-model="item.cp_vat" name="cp_vat" class="form-control input-sm validate-decimal" placeholder="{{ trans('item.cp_vat_percent') }}"  maxlength = "9" required="">

								</div>
							</div>
						</div>
						@endIf
						<!--End cp_vat-->
						<!-- ite m qty -->
						<div class="col-md-2">
							<div class="form-group">
								<label for="">@lang('item.qty')<span style="color:red">*</span></label>
								<div class="input-group">
									<div class="input-group-addon">
										<i class="icon ion-bag"></i>
									</div>
									<input type="text" name="qty" id="qty" v-model="item.qty" type="text" class="form-control  input-sm validate-decimal" maxlength="8" placeholder="@lang('item.qty')" min="0" :max="tempqty" required="">
								</div>
							</div>
						</div>
						<!-- end item qty -->

						{{-- Unit start --}}
						<div class="col-md-2">
							<div class="form-group">
								<label for="">@lang("glob.unit")<span style="color:red">*</span></label>
								<v-select  :options="units" :text="'unit'" v-model="item.unit" :vrequired="true"></v-select>
								
							</div>
						</div>
						{{-- Unit end --}}

						{{-- Discount --}}
						<div class="col-md-2">
							<div class="form-group">
								<label for="">@lang('glob.discount') %<span style="color:red">*</span></label>
								<input v-model="item.discount" type="text" name="" class="form-control input-sm validate-decimal" placeholder="{{ trans('stock.dis_rt') }}"  maxlength = "6" required="">
							</div>
						</div>
						{{-- /Discount --}}
					</div>
					<div class="row">
						<div class="col-md-3 pull-right">
							<div class="form-group well">
								<label >@lang('stock.total_Amount')</label>: <span style="font-weight: 700">@{{ amount }}</span>
							</div>
						</div>

						<div class="col-md-3 pull-right">
							<div class="form-group well">
								<label >@lang('stock.total_discount')</label>: <span style="font-weight: 700">@{{ totalDiscount }}</span>
							</div>
						</div>
					</div>
					<!-- .row -->

					<div class="modal-footer">
						<button type="submit" v-if="itemOp=='add'"  class="btn btn-primary pull-left"> <i class="fa fa-floppy-o"></i> @lang('glob.add') </button>
						<button type="submit" v-if="itemOp=='edit'"  class="btn btn-primary pull-left"> <i class="fa fa-floppy-o"></i> @lang('glob.update') </button>
						<a  class="btn btn-cyan pull-left" v-on:click="clearItems()"><i class="fa fa-refresh"></i> @lang('glob.reset')</a>
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- /Purchase Return item add modal -->

<div class="box box-default" style="border-top:0">

	<div class="box-body" style="margin:1px;border:2px solid #c8c8c8; border-radius: 5px;">
		
		<div class="table-spinner" style="display: none">
			<i class="fa fa-spinner fa-pulse fa-spin  fa-4x fa-fw"></i>
			<span class="sr-only">Loading...</span>
		</div>
		<!-- .row -->
		
		<div class="row">
			<div class="col-md-12 table-responsive" >
				
				<table class="table table-bordered table-striped">

					<thead >
						<tr>
							
							<th style="width:108.33px">@lang('item.item_name')</th>
							<th style="width:108.33px">@lang('glob.sub_category')</th>
							<th style="width:108.33px">@lang('item.qty')</th>
							<th style="width:108.33px">@lang('glob.unit')</th>
							<th style="width:108.33px">@lang('glob.rate')</th>
								@if(Common::vatType('vat','vat_type')!=0)<th style="width:108.33px">@lang('item.cp_vat_percent')</th>@endIf
							<th style="width:108.33px">@lang('stock.amt')</th>
							<th style="width:108.33px">@lang('glob.discount') %</th>
							<th style="width:108.33px">@lang('glob.actions')</th>

						</tr>
					</thead>

					<tbody>



						<tr v-show="filterBySearch(p)" v-for="(p, index) in purchasedItems">
							
							<td> @{{ p.item.itemName }}</td>
							<td> @{{ p.item.categories.categorie }}</td>
							<td> @{{ fixQty(p.qty) }}</td>
							<td> @{{ p._unit.unit }}</td>
							<td> @{{ fixtIt(p.costPrise) }}</td>
							@if(Common::vatType('vat','vat_type')!=0)<td>@{{p.cp_vat}}</td>@endIf
							<td> @{{ p.amount }}</td>
							<td> @{{ fixtIt(p.discount) }}</td>
							<td>
								<a  v-on:click="editItem(p, index)"  class="btn btn-xs btn-info btn-circle" ><i class="fa fa-pencil" aria-hidden="true"></i></a>
								<a  v-on:click="deleteItem(p, index)" class="btn btn-xs btn-danger btn-circle "><i class="fa fa-trash" aria-hidden="true"></i></a> 							
							</td>
						</tr>

						

					</tbody>

				</table>
				<!-- </div> -->
			</div>
			{{-- .col-md-12 --}}
		</div> 

	</div>
	<!-- /.box-body -->
</div>
						<!-- end table box -->