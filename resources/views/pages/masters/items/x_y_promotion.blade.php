	<!-- Div for x_y_promotion  -->
	<div id="x-y-promotionDiv" class="col-md-12" v-show="item.x_y_prmoChk">

		<div class="box box-primary">

			<div class="box-body">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<h4 class="text-center"> X on Y Promotion Item</h4>
						</div>
					</div>
				</div>
				<div class="row">
					<div  class="col-md-3">
						<div class="form-group">
							{!! Form::label('userName', trans('glob.from')) !!}
							<div class="input-group">
								<div class="input-group-addon">
									<i class="ion ion-calendar"></i>
								</div>
								<input id="promo_from" type="text" name="promo_from" class="form-control input-sm date-time-picker" placeholder="{{ trans('glob.from') }}" v-model="item.xyPromo.prmo_from">
							</div>
							
						</div>
					</div>
					<div  class="col-md-3">
						<div class="form-group">
							{!! Form::label('userName', trans('glob.to')) !!}
							<div class="input-group">
								<div class="input-group-addon">
									<i class="ion ion-calendar"></i>
								</div>
								<input id="promo_to" type="text" name="prmo_to" class="form-control input-sm date-time-picker" placeholder="{{ trans('glob.to') }}" v-model="item.xyPromo.prmo_to">
							</div>
							
						</div>
					</div>
					
					<div class="col-md-3">
						<div class="form-group">
							<label>@lang('item.x_item_name')</label>
							<div class="input-group">
								<div class="input-group-addon">
									<i class="icon ion-bag"></i>
								</div>
								<input type="text"  class="form-control  input-sm"  placeholder="@lang('item.x_item_name')" readonly="" v-model="item.itemName">
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label>@lang('item.x_quantity')</label>
							<div class="input-group">
								<div class="input-group-addon">
									<i class="icon ion-bag"></i>
								</div>
								<input id="x_quantity" type="text" name="x_quantity" class="form-control  input-sm validate-decimal" maxlength="8"  placeholder="@lang('item.x_quantity')"  v-model="item.xyPromo.x_quantity" >
							</div>
							
						</div>
					</div>
				</div>
				<div class="row">
					<!-- category main -->
					<div class="col-md-3">
						<div class="form-group">
							<label>@lang('glob.parentCategory')</label>
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-tags"></i>
								</div>
								<v-select  v-model="filter.parentCategory" :vrequired="false" :options="categories" :text="'categorie'" :vparent="0" :vcheck="'parent_category'">
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
								<v-select  v-model="filter.category" :vrequired="false" :options="categories" :text="'categorie'" :vparent="filter.parentCategory" :vcheck="'parent_category'" :vscroll="false">
								</v-select>
								
							</div>
						</div>
					</div>
					
					<!-- end Sub Category -->
					<!-- item name -->
					<div class="col-md-3">
						<div class="form-group">
							<label>@lang('item.y_item_name')</label>
							<div class="input-group">
								<div class="input-group-addon">
									<i class="icon ion-bag"></i>
								</div>
								<input id="y_item" type="text"  name="y_itemName" class="form-control  input-sm prevent-enter itemname-autocomplete"  :data-cat="filter.category" :data-index="0"  placeholder="@lang('item.y_item_name')"   v-model="item.xyPromo.y_itemName" v-on:keydown.enter="getItem(item.xyPromo.y_itemName,$event)">
								
							</v-select>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label>@lang('item.y_item_barcode')</label>
						<div class="input-group">

							<div class="input-group-addon">
								<i class="fa fa-barcode"></i>
							</div>
							<input id="y_barcode" type="text"  name="y_barcode" class="form-control  input-sm prevent-enter barcode-autocomplete" :data-cat="filter.category"  :data-index="0"  placeholder="@lang('item.y_item_barcode')"   v-model="item.xyPromo.y_barcode" v-on:keydown.enter="getBarcode(item.xyPromo.y_barcode,$event)">
							

						</div>

					</div>
				</div>

			</div>
			<!-- .row -->

			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
						<label>@lang('item.y_quantity')</label>
						<div class="input-group">
							<div class="input-group-addon">
								<i class="icon ion-bag"></i>
							</div>
							<input id="y_quantity" type="text"  name="y_quantity" class="form-control  input-sm validate-decimal" maxlength="8"  placeholder="@lang('item.y_quantity')"   v-model="item.xyPromo.y_quantity">
						</div>
						
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-1">
					<a  class="btn bg-purple new-customer margin-top-space" v-on:click="resetPromo">@lang('item.reset_promo')</a>
				</div>

			</div>

		</div>
		<!-- end box body -->

		<!-- footer -->
		<div class="box-footer">
		</div>
		<!-- end footer -->
	</div>
	<!-- .box end -->

</div>
<!-- End div for x_y_promotion -->