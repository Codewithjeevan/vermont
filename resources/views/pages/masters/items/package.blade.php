	<!-- Div for package  -->
	<div id="packageDiv" class="col-md-12" v-show="item.packageChk">

		<div class="box box-primary">

			<div class="box-body">
				<div class="row">
					<div class="col-md-12">
						<h4 class="text-center"> Package Items</h4>
					</div>
				</div>

				<div class="row">
					<div class="col-md-3">
						<div class="form-group ">
							{!! Form::label('title', trans('glob.category')) !!}
							<v-select  v-model="pkg.pkgParentCategory" :vrequired="false" :options="categories" :text="'categorie'" :vparent="0" :vcheck="'parent_category'">
								<option value="">Select</option>
							</v-select>
						</div>
					</div>

					<!-- category -->
					<div class="col-md-3">
						<div class="form-group"> 
							{!! Form::label('title', trans('glob.sub_category')) !!} 

							<v-select  v-model="pkg.category" :vrequired="false" :options="categories" :text="'categorie'" :vparent="pkgParentCategory" :vcheck="'parent_category'">
								<option value="">Select</option>
							</v-select>


						</div>
					</div>
					<!-- end category -->
					<div class="col-md-3">
						<div class="form-group ">
							{!! Form::label('title', trans('item.item_product')) !!}
							<input  type="text"  name="itemName" class="form-control  input-sm prevent-enter itemname-autocomplete"  :data-cat="pkg.category"  :data-index="1"  placeholder="@lang('item.item_name')"   v-model="pkg.item_name" v-on:keydown.enter="getpkgItem(pkg.item_name,$event)">
							
						</div>
					</div>
					<!-- end item product -->
					<!-- end category -->
					<div class="col-md-3">
						<div class="form-group ">
							{!! Form::label('title', trans('glob.barcode')) !!}
							<input  type="text"  name="barcode" class="form-control  input-sm prevent-enter barcode-autocomplete"  :data-cat="pkg.category"  :data-index="1"  placeholder="@lang('glob.barcode')"   v-model="pkg.barcode" v-on:keydown.enter="getpkgBarcode(pkg.barcode,$event)">
							
						</div>
					</div>
					<!-- end item product -->
				</div>
				<!-- .row -->
				<div class="row">
					<!-- QTY -->
					<div class="col-md-3">
						<div class="form-group">
							{!! Form::label('title', trans('item.qty')) !!}

							<div class="input-group">
								<div class="input-group-addon"> <i class="icon ion-bag"></i> </div>
								{!! Form::text('pckgProductQnty', null,  array(  'class'=>'form-control input-sm validate-decimal',  'placeholder'=>trans('item.qty'),  "v-model" => "pkg.qty" )) !!}
							</div>

						</div>
					</div>
					<!-- End QTY -->
				</div>

				<div class="row">
					<!-- add button -->
					<div class="col-md-3">
						<div v-if="addbutton == true" >
							<button type="button" :disabled="(pkg.item!=''&& pkg.qty!='') ? false :true" v-on:click="addPackageItem(pkg)" class="btn bg-purple new-customer margin-top-space" >@lang('item.add_package')</button>
						</div>
						<div v-if="updatebutton == true" >
							<button type="button"  :disabled="(pkg.item!=''&& pkg.qty!='') ? false :true" v-on:click="updatePackageItem(pkg)" class="btn bg-purple new-customer margin-top-space">@lang('item.update_package')</button>
						</div>
					</div>
					<!-- End item button -->
				</div>

			</div>
			<!-- end box body -->

			<!-- footer table view -->
			<div class="box-footer">

				<div  class="row">

					<div class="col-md-12 table-responsive">

						<table class="table table-bordered table-striped datatable3">
							<thead >
								<tr>
									<th >@lang('item.item_name')</th>
									<th>@lang('item.qty')</th>
									<th style="width:10%">@lang('glob.actions')</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="( i, index) in item.packageList">
									<td>@{{ i.itemName }}</td>
									<td>@{{ fixQty(i.qty) }}</td>
									<td>
										<a v-on:click="editPackage(i,index)" class="btn btn-xs btn-info btn-circle" ><i class="fa fa-pencil" aria-hidden="true"></i></a>
										<a v-on:click="deletePackage(i,index)" class="btn btn-xs btn-danger btn-circle "><i class="fa fa-trash" aria-hidden="true"></i></a> 
									</td>
								</tr>
							</tbody>
						</table>

					</div>

				</div>
				<!-- .row -->

			</div>
			<!-- end footer table view -->
		</div>
		<!-- .box end -->

	</div>
<!-- End div for package -->