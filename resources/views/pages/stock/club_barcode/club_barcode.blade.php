@extends('layouts.app')

@section('page-title')@lang('glob.club_barcode')@endsection

@section('page-subtitle')@lang('glob.add_edit_club_barcode')@endsection 

@section ('breadcrumb')

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-home"></i> @lang('glob.home')</a></li>
	<li><a href="#"><i class="fa fa-stack-overflow"></i> @lang('glob.stock')</a></li>
	<li class="active">@lang('glob.club_barcode')</li>
</ol>

@endsection

@section('content')
<div id="club-barcode"> 

	@if(Common::can_create('club_barcode'))

	<div class="row customer-box"> 

		<div class="col-md-12">
			<div class="box box-primary"> 

				<div class="box-body">
					<div class="row">
						
					</div>
					<!-- .row end mrn no -->
					<!-- .row -->
					<div class="row">
						<!-- category main -->
						<div class="col-md-3">
							<div class="form-group">
								<label>@lang('glob.parentCategory')</label>
								<div class="input-group">
									<div class="input-group-addon">
										<i class="fa fa-tags"></i>
									</div>
									<v-select  v-model="filter.parentCategory"  :options="categories" :text="'categorie'" :vparent="0" :vcheck="'parent_category'"  :vrequired="false"></v-select>
									
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
									<v-select  v-model="filter.category"  :options="categories" :text="'categorie'" :vparent="filter.parentCategory" :vcheck="'parent_category'"  :vrequired="false"></v-select>
									
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
									<input  type="text"  name="itemName" class="form-control input-sm prevent-enter itemname-autocomplete" :data-cat="filter.category" placeholder="@lang('item.item_name')"   v-model="item_name" v-on:keydown.enter="getItemObjectByName(item_name,$event)">
									
								</div>
							</div>
						</div>
						<!-- end item name -->

						<div class="col-md-3">
							<div class="form-group">
								<label>@lang('glob.barcode')<span style="color:red">*</span></label>
								<div class="input-group">
									<div  class="input-group-addon">
										<i class="icon ion-bag"></i>
									</div>
									<input  type="text"  name="barcode" class="form-control  input-sm prevent-enter barcode-autocomplete" :data-cat="filter.category"  placeholder="@lang('glob.barcode')"   v-model="barcode" v-on:keydown.enter="getItemObjectByBarcode(barcode,$event)">
									
								</div>
							</div>
						</div>
					</div>
					<!-- .row -->

					<!-- .row -->
					<div class="row">
						<!-- quantity -->
						<div class="col-md-3">
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label>@lang('item.quantity')<span style="color:red">*</span></label>
										<input type="text" name="quantity" class="form-control  input-sm validate-decimal" maxlength="8"  placeholder="@lang('item.quantity')" required=""   v-model="clubItem.quantity">
									</div>
								</div>
							</div>


						</div>
						<!-- end quantity -->
						<div class="col-md-3">
							<div class="form-group ">
								<label ><input type="radio" name="optradio" value="width:36mm;height:23mm" v-model="clubItem.size">&emsp; 36 * 23 mm</label>
								<label ><input type="radio" name="optradio" value="width:38mm;height:25mm" v-model="clubItem.size">&emsp; 38 * 25 mm</label> 
								<label ><input type="radio" name="optradio" value="width:40mm;height:28mm" v-model="clubItem.size">&emsp; 40 * 28 mm</label>
							</div>
						</div>	

						<div class="col-md-6">
							<div id="barcodeImageDiv" class="text-center">
								<div style="border:1px solid #d4d4d4;" :style="clubItem.size" >
									<div class="text-center">
										<span v-if="!( this.item === undefined || this.item == null || this.item ==='')" >@{{item.itemName}}</span>
									</div>
									<div>
										<img :src="imageUrl"  id="barcodeImg" style="margin: auto;" width="100%" >
									</div>
									<div>
										<span v-if="!( this.item === undefined || this.item == null || this.item ==='' ||this.item =={})" ><strong>Price @{{curr}}: </strong>@{{fixtIt(item.salePrise)}}</span>
									</div>
								</div>
							</div>
						</div>
						
					</div>
					<!-- .row -->

					<div class="row">
						<div class="col-md-12 margin-top-space">
							<!-- Add Button -->
							<div class="form-group ">
								@if(Common::can_create('club_barcode'))
								<button type="button" class="btn btn-primary " v-on:click="addGroup(clubItem,item)" v-if="btnGroupAdd == 'add'"	:disabled="((clubItem.quantity != '' && clubItem.quantity > 0 ) && clubItem.size !='' && !( this.item === undefined || this.item == null || this.item ==='') && item_name!='' && barcode!='' ) ? false :true">@lang('glob.add')</button>
								@endif	
								@if(Common::can_update('club_barcode'))
								<button type="button" class="btn btn-primary " v-on:click="updateGroupItem(clubItem,item)" v-if="btnGroupAdd == 'edit'"  :disabled="((clubItem.quantity != '' && clubItem.quantity > 0 ) && clubItem.size !='' && !( this.item === undefined || this.item == null || this.item ==='') ) ? false :true"><i class="fa fa-floppy-o" aria-hidden="true"></i> @lang('glob.update')</button>
								@endif	
								<button class="btn btn-cyan" v-on:click="clearClub()" ><i class="fa fa-refresh" aria-hidden="true"></i> @lang('glob.reset')</button>
								
							</div>
							<!-- End Add Button -->
						</div>
					</div>

				</div>					

			</div>
			<!-- .box .box-primary -->

		</div>
		<!-- .col-md-12 -->

	</div>
	<!--end .row .customer-box -->

	{{ Form::close() }}
	<!-- end form -->

	@endif
	<div class="row" >
		<div class="col-md-12 table-responsive" style="height: 250px; overflow-y: scroll;">
			<table class="table table-bordered table-striped">
				<thead>
					<tr>
						<th style="width:10%">@lang('glob.item') @lang('glob.id')</th>
						<th>@lang('item.item_product')</th>
						<th>@lang('glob.barcode')</th>
						<th>@lang('glob.sale_price')</th>
						<th>@lang('item.qty')</th>
						<th>@lang('glob.barcode_size')</th>	
						<th style="width:4%">@lang('glob.actions')</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="( b, index) in barcodeGroup">
						<td>@{{  index+1  }}</td>
						<td>@{{b.itemName}}</td>	
						<td>@{{b.barcode}}</td>
						<td>@{{fixtIt(b.price)}}</td>
						<td>@{{b.quantity}}</td>
						<td v-if="b.size == 'width:36mm;height:23mm'">@{{'36 X 23 MM'}}</td>
						<td v-else-if="b.size == 'width:38mm;height:25mm'">@{{'38 X 25 MM'}}</td>
						<td v-else-if="b.size == 'width:40mm;height:28mm'">@{{'40 X 28 MM'}}</td>
						<td v-else>@{{'-'}}</td>					
						<td>
							@if(Common::can_update('club_barcode'))
							<a  v-on:click="editGroupItem(b , index, $event)" class="btn btn-xs btn-info btn-circle" ><i class="fa fa-pencil" aria-hidden="true"></i></a>
							@endif	
							@if(Common::can_delete('club_barcode'))
							<a  v-on:click="deleteGroupItem(b , index)" class="btn btn-xs btn-danger btn-circle "><i class="fa fa-trash" aria-hidden="true"></i></a> 	
							@endif														
							<a  v-on:click="printBarcode(b, $event)" class="btn btn-xs btn-primary btn-circle"><i class="fa fa-print" aria-hidden="true"></i></a> 
						</td>						
					</tr>
				</tbody>
				
			</table>
		</div>

	</div>
	<div class="row" v-if="barcodeGroup.length > 0">
		<div class="col-md-12">
			<button type="button" class="btn  btn-primary" v-on:click="printGroupBarcode(barcodeGroup,$event)"><i class="fa fa-print" aria-hidden="true"> @lang('glob.print')</i></button> 
		</div>
		
	</div>
	
	

	@endsection

