	<div class="row">
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
						<th>@lang('item.s_no')</th>
						<th>@lang('glob.barcode')</th>
						<th>@lang('item.refrence_number')</th>
						<th>@lang('glob.category')</th>
						<th>@lang('glob.sub_category')</th>
						<th>@lang('item.item_product')</th>
						<th>@lang('item.c_p')</th>
						@if(Common::vatType('vat','vat_type')!=0)<th>@lang('item.cp_vat_percent')</th>@endIf
						<th>@lang('item.s_p')</th>
						@if(Common::vatType('vat','vat_type')!=0)<th>@lang('glob.sp_vat')</th>@endIf
						<th>@lang('glob.unit')</th>
						<th>@lang('glob.stock')</th>
						<th>@lang('glob.min')</th>
						<th>@lang('glob.max')</th>
						{{-- <th >@lang('glob.description')</th> --}}

						@if(Common::can_update('item') || Common::can_delete('item'))

						<th style="width:4%">@lang('glob.actions')</th>

						@endif

					</tr>
				</thead>
				<tbody>

					@foreach($items as $item)
					
					<tr>
						<td>{{ $item->id }}</td>
						<td>{{ $item->barcode }}</td>
						<td>@if($item->serial_number == null) NA @else {{$item->serial_number}} @endif</td>
                        <td>{{(isset($item->categories->parentCategory->categorie)) ? $item->categories->parentCategory->categorie : '' }}</td>
						<td>{{ (isset($item->categories->categorie)) ? $item->categories->categorie : '' }}</td>
						<td>{{ $item->itemName }}</td>
						<td>{{ (isset($item->costPrise)) ? Common::fixIt($item->costPrise) : '' }}</td>
						@if(Common::vatType('vat','vat_type')!=0)<td>{{ (isset($item->cp_vat)) ?  Common::fixIt($item->cp_vat) : '' }}</td>@endIf
						<td>{{ (isset($item->salePrise)) ?  Common::fixIt($item->salePrise) : '' }}</td>
						@if(Common::vatType('vat','vat_type')!=0)<td>{{ (isset($item->vat)) ?  Common::fixIt($item->vat) : '' }}</td>@endIf
						<td>{{ (isset($item->units->unit)) ? $item->units->unit : '' }}</td>
						<td>{{ Common::fixQty($item->stock) }}</td>
						<td>{{ Common::fixQty($item->min) }}</td>
						<td>{{ Common::fixQty($item->max) }}</td>
						{{-- <td>{{ $item->description }}</td> --}}

						@if(Common::can_update('item') || Common::can_delete('item'))


						<td>
							<a v-on:click="editItem({{ $item->id }}, $event)"  class="btn btn-xs btn-info btn-circle" ><i class="fa fa-pencil" aria-hidden="true"></i></a>
							@if(Common::can_delete('item'))
							<a v-on:click="deleteItem({{ $item->id }}, $event)" class="btn btn-xs btn-danger btn-circle "><i class="fa fa-trash" aria-hidden="true"></i></a> 
							@endif
						</td>

						@endif

					</tr>

					@endforeach
					
				</tbody>
				<tfoot class="showSearch">
					<tr>
						<th>@lang('item.s_no')</th>
						<th class="search">@lang('glob.barcode')</th>
						<th class="search">@lang('item.refrence_number')</th>
						<th class="search">@lang('glob.category')</th>
						<th class="search">@lang('glob.sub_category')</th>
						<th class="search">@lang('item.item_product')</th>
						<th class="search">@lang('item.c_p')</th>
						@if(Common::vatType('vat','vat_type')!=0)<th class="search">@lang('item.cp_vat_percent')</th>@endIf
						<th class="search">@lang('item.s_p')</th>
						@if(Common::vatType('vat','vat_type')!=0)<th class="search">@lang('glob.sp_vat')(%)</th>@endIf
						<th class="search">@lang('glob.unit')</th>
						<th class="search">@lang('glob.stock')</th>
						<th class="search">@lang('glob.min')</th>
						<th class="search">@lang('glob.max')</th>
						{{-- <th class="search">@lang('glob.description')</th> --}}

						@if(Common::can_update('item') || Common::can_delete('item'))


						<th style="width:4%">@lang('glob.actions')</th>

						@endif
						
					</tr>
				</tfoot>
			</table>
			<div class="pull-right">
				{{ $items->appends(['searchkey' => $searchkey,'filter' =>$filter,'vat'=>$vat ])->links() }}
			</div>
		</div>
		
	</div>