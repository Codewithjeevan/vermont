@extends('layouts.app')

@section('page-title')@lang('glob.open_stock')@endsection

@section('page-subtitle')@lang('open_stock.add_edit_stock')@endsection

@section ('breadcrumb')

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> @lang('glob.dashboard')</a></li>
	<li class="active">@lang('glob.open_stock')</li>
</ol>

@endsection

@section('content')
<div class="open_stock"> 
	
	@if(Common::can_create('open_stock'))   

	<!-- Button div -->
	<div class="row">
		<div class="col-md-6 margin-bottom">
			<a class="btn bg-purple new-customer"  v-on:click="openSlide()">@lang('glob.add_new')</a>
		</div>
		<div class="col-md-6 dropdown margin-bottom ">
			<button class="btn btn-success dropdown-toggle pull-right" type="button" data-toggle="dropdown" >Export
				<span class="caret" ></span></button>
				<ul class="dropdown-menu pull-right">
					<li><a href="{{ url('master/open_stocks/xls' ) }}">Excel</a></li>
					<li><a href="{{ url('master/open_stocks/pdf' ) }}">PDF</a></li>				
				</ul>				
			</div>	
		</div>
		<!-- Button div end -->


		<div id="openstockFormDiv" class="collapse">

			<div class="row customer-box"> 

				<div class="col-md-12">

					<div class="box box-primary"> 

						<div class="box-body">

							<div class="row">

								<!-- open stock date -->
								<div class="col-md-3">
									<div class="form-group {{ $errors->has('open_stock_date') ? ' has-error' : '' }}">
										{!! Form::label('open_stock_date', trans('open_stock.open_stock_date')) !!}<label><span style="color:red">*</span></label>
										<div class="input-group">
											<div class="input-group-addon"><i class="ion ion-calendar"></i></div>
											<input type="text" id="openStockDateId" name="openingStock" class="form-control  input-sm datepicker "   placeholder="@lang('open_stock.open_stock_date')" v-model="openStock.open_stock_date" required="" >	
										</div>

										@if ($errors->has('open_stock_date'))
										<span class="help-block">
											<strong>{{ $errors->first('open_stock_date') }}</strong>
										</span>
										@endif
									</div>
								</div>
								<!--End open stock date -->

							</div>
							<!-- .row -->

							<div class="row">
								<!--  item cat -->
								<div class="col-md-3">
									<div class="form-group">
										{!! Form::label('text', trans('glob.category')) !!}
										<div class="input-group">
											<div class="input-group-addon"><i class="ion-bag"></i></div>
											<v-select  v-model="openStock.filter.parentCategory"  :options="categories" :text="'categorie'" :vparent="0" :vcheck="'parent_category'"  :vrequired="false"></v-select>
										</div>
									</div>
								</div>
								<!--End item cat -->

								<!--  item subcat -->
								<div class="col-md-3">
									<div class="form-group">
										{!! Form::label('text', trans('glob.sub_category')) !!}
										<div class="input-group">
											<div class="input-group-addon"><i class="ion-bag"></i></div>
											<v-select  v-model="openStock.filter.category"  :options="categories" :text="'categorie'" :vparent="openStock.filter.parentCategory" :vcheck="'parent_category'"  :vrequired="false"></v-select>
										</div>
									</div>
								</div>
								<!--End item subcat -->

								<!--  item name -->
								<div class="col-md-3">
									<div class="form-group {{ $errors->has('itemId') ? ' has-error' : '' }}">
										{!! Form::label('text', trans('glob.item')) !!}<label><span style="color:red">*</span></label>
										<div class="input-group">
											<div class="input-group-addon"><i class="ion-bag"></i></div>
											<input type="text" name="itemName" class="form-control  input-sm prevent-enter itemname-autocomplete" :data-cat="openStock.filter.category" placeholder="@lang('item.item_name')" v-model="openStock.item.itemName" v-on:keyup.enter="getCatSubcat(openStock.item.itemName,$event)" required=""  >
											
										</div>

										@if ($errors->has('itemId'))
										<span class="help-block">
											<strong>{{ $errors->first('itemId') }}</strong>
										</span>
										@endif
									</div>
								</div>
								<!--End Item name -->

								<!--  item barcode -->
								<div class="col-md-3">
									<div class="form-group">
										{!! Form::label('text', trans('glob.barcode')) !!}
										<div class="input-group">
											<div class="input-group-addon"><i class="ion-bag"></i></div>
											<input type="text" name="barcode" class="form-control  input-sm prevent-enter barcode-autocomplete" :data-cat="openStock.filter.category" placeholder="@lang('glob.barcode')" v-model="openStock.item.barcode" v-on:keyup.enter="getCatSubcat(openStock.item.barcode,$event)" required=""  >
											
										</div>
									</div>
								</div>
								<!--End Item barcode -->
							</div>
							<!-- .row -->

							<!-- .row -->
							<div class="row">
                                 <!--  item refrence number -->
								<div class="col-md-3">
									<div class="form-group">
										{!! Form::label('text', trans('item.refrence_number')) !!}
										<div class="input-group">
											<div class="input-group-addon"><i class="fa fa-tag"></i></div>
											<input type="text" name="serial_number" class="form-control  input-sm" placeholder="@lang('item.refrence_number')" v-model="openStock.item.serial_number" required=""  >
											
										</div>
									</div>
								</div>
								<!--End Item refrence number -->
								<!--  Opening stock -->
								<div class="col-md-3">
									<div class="form-group {{ $errors->has('openingStock') ? ' has-error' : '' }}">
										{!! Form::label('text', trans('open_stock.opening_stock')) !!}<label><span style="color:red">*</span></label>
										<div class="input-group">
											<div class="input-group-addon"><i class="ion-ios-box-outline"></i></div>
											<input type="text" name="openingStock" class="form-control  input-sm validate-decimal" maxlength="8"  placeholder="@lang('open_stock.opening_stock')" v-model="openStock.openingStock"  required="" >
											
										</div>

										@if ($errors->has('openingStock'))
										<span class="help-block">
											<strong>{{ $errors->first('openingStock') }}</strong>
										</span>
										@endif
									</div>
								</div>
								<!--End Opening stock -->

								<!--  unit -->
								<div class="col-md-3">
									<div class="form-group {{ $errors->has('unit') ? ' has-error' : '' }}">
										{!! Form::label('text', trans('open_stock.unit')) !!}<label><span style="color:red">*</span></label>
										<div class="input-group">
											<div class="input-group-addon"><i class="fa fa-th-large" aria-hidden="true"></i></div>										
											<v-select  v-model="openStock.unit"  :options="units" :text="'unit'"  :vrequired="false"></v-select>
											
										</div>

										@if ($errors->has('unit'))
										<span class="help-block">
											<strong>{{ $errors->first('unit') }}</strong>
										</span>
										@endif
									</div>
								</div>
								<!--End Unit -->
							</div>
							<!-- .row -->
						</div>
						<!-- .box-body -->
						<div class="box-footer">
							<div class="form-group">
								<button v-if="buttonAction=='add'" type="button" class="btn btn-primary " v-on:click="addStockInGroup(openStock)"  :disabled="((openStock.open_stock_date=='' || openStock.item.length === 0 || openStock.openingStock=='' || openStock.unit=='' ) ? true :false)"><i class="fa fa-floppy-o"></i> @lang('glob.add')</button>
								<button v-if="buttonAction=='edit'" type="button" class="btn btn-primary " v-on:click="updateStockInGroup(openStock,$event)"  :disabled="((openStock.open_stock_date=='' || openStock.item.length === 0 || openStock.openingStock=='' || openStock.unit=='' ) ? true :false)"><i class="fa fa-floppy-o"></i> @lang('glob.update')</button>
								<a  class="btn btn-cyan" v-on:click="clearOpeningStock"><i class="fa fa-refresh"></i> @lang('glob.reset')</a>
								<a  class="btn btn-danger"  v-on:click="slideUp">@lang('glob.cancel')</a>
							</div>
						</div>
						<!-- box-footer -->
					</div>
					<!-- .box .box-primary -->
				</div>
				<!-- .col-md-12 -->
			</div>
			<!--end .row .customer-box -->
			<div class="row" v-if="stockGroup.length">
				<div class="col-md-12 table-responsive" style="height: 250px; overflow-y: scroll;">
					<table class="table table-bordered table-striped">
						<thead>
							<tr> 
								<th style="width:10%">#</th>
								<th>@lang('glob.sub_category')</th> 
								<th>@lang('open_stock.item_name')</th>
								<th>@lang('open_stock.opening_stock')</th>
								<th>@lang('open_stock.unit')</th>
								<th style="width:10%">@lang('customer.actions')</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="( s, index) in stockGroup">
								<td>@{{  index+1  }}</td>
								<td>@{{s.item.categories.categorie}}</td>	
								<td>@{{s.item.itemName}}</td>
								<td>@{{ fixQty(s.openingStock)}}</td>
								<td>@{{s.item.units.unit}}</td>					
								<td>
									<a   v-on:click="editOpenStockGroup(s, index)" class="btn btn-xs btn-info btn-circle" ><i class="fa fa-pencil" aria-hidden="true"></i></a>
									<a   v-on:click="deleteOpenStockGroup(s, index)" class="btn btn-xs btn-danger btn-circle "><i class="fa fa-trash" aria-hidden="true"></i></a> 														
								</td>						
							</tr>
						</tbody>
					</table>
					<button   type="button" class="btn btn-primary " v-on:click="store($event)"><i class="fa fa-floppy-o"></i> @lang('glob.save')</button>
				</div>

			</div>
			
			<!-- end form -->
		</div>


		@endif

		<!-- view table data  -->
		<div class="row openStock-table">

			<div class="col-md-12 table-responsive item-master-table no-search">
				<div class="col-md-12 form-inline">
					<div class="pull-right">
						<form id="form-search-item" methode="GET" style="margin-bottom: 10px;">
							<label for="search">Search :</label> 
							<input type="text" name="searchkey" value="{{ $searchkey }}" class="form-control  input-sm seatch-item" placeholder="search" >
						</form >
					</div> 
				</div>
				<table class="table table-bordered table-striped datatable-paging-false" >
					<thead>
						<tr>
							<th style="width:10%">#</th>
							
							<th>@lang('glob.sub_category')</th> 
							<th>@lang('open_stock.item_name')</th>
							<th>@lang('open_stock.opening_stock')</th>
							<th>@lang('open_stock.unit')</th>

							@if(Common::can_update('open_stock') || Common::can_delete('open_stock'))

							<th style="width:10%">@lang('customer.actions')</th>

							@endif

						</tr>
					</thead>
					<tbody>

						@foreach($openStockList as $row) 

						<tr>
							<td>{{ $row->id }}</td>
							
							<td>@if(isset($row->item->categories)) {{ $row->item->categories->categorie }} @endif</td>
							<td>@if(isset($row->item)) {{ $row->item->itemName }} @endif</td>
							<td>{{ Common::fixQty($row->openingStock)}} </td>
							<td>@if(isset($row->units->unit)) {{ $row->units->unit}} @endif</td>

							@if(Common::can_update('open_stock') || Common::can_delete('open_stock'))

							<td>

								@if(Common::can_update('open_stock'))

								<a  v-on:click="editOpenStock({{ $row->id }}, $event)" class="btn btn-xs btn-info btn-circle" ><i class="fa fa-pencil" aria-hidden="true"></i></a>

								@endif
								@if(Common::can_delete('open_stock'))

								<a  id='{{ $row->id }}' v-on:click="deleteOpenStock({{ $row->id }}, $event)" class="btn btn-xs btn-danger btn-circle"><i class="fa fa-trash" aria-hidden="true"></i></a> 


								@endif

							</td>

							@endif

						</tr>

						@endforeach

					</tbody>
					<tfoot class="showSearch">
						<tr>
							<th style="width:10%">#</th>
							
							<th class="search">@lang('glob.sub_category')</th>
							<th class="search">@lang('open_stock.item_name')</th>
							<th class="search">@lang('open_stock.opening_stock')</th>
							<th class="search">@lang('open_stock.unit')</th>

							@if(Common::can_update('open_stock') || Common::can_delete('open_stock'))

							<th style="width:10%">@lang('customer.actions')</th>

							@endif

						</tr>
					</tfoot>
				</table>
				<div class="pull-right">
					{{ $openStockList->appends(['searchkey' => $searchkey ])->links() }}
				</div>
			</div>

			

		</div>

		@if(Common::can_create('open_stock'))  
		<!--Export Buttons-->
		<div class="row">
			<div class="col-md-12">
				<a class="btn btn-indigo " data-toggle="modal" data-target="#purchase-import"> <i class="fa fa-upload" aria-hidden="true"></i> @lang('glob.import_stock')</a>
				<a href="{{ asset('sample/opening_stock_import.xlsx') }}" class="btn btn-primary  pull-right" download="" >
					<i class="fa fa-download" aria-hidden="true"></i>
					<strong> Sample Import Excel</strong>
				</a>
				@include('pages.masters.opening_stock_entries.item-import')
			</div>
		</div>
		<!--End Export Buttons-->

		@endif
	</div>

	@endsection