<div class="col-md-12 table-responsive purchase-table" >

	<table class="table table-bordered table-striped datatable1 ">

		<thead>
			<tr>
				<th style="width:2%">#</th>

				<th>@lang('stock.stock_transfer_no')</th>
				<th>@lang('stock.stock_transfer_date')</th>
				<th>@lang('supplier.supplier')</th>
				<th>@lang('stock.po_no')</th>
				<th>@lang('billing.bill') @lang('billing.amt')</th>
				@if(Common::can_update('purchase') || Common::can_delete('purchase'))
				<th style="width:4%">@lang('glob.actions')</th>
				@endif
			</tr>
		</thead>

		<tbody>

			@foreach($purchases as $p)
			<tr>
				<td>{{ $p->id }}</td>
				<td>{{ $p->supplierBillNo }}</td>
				<td>@if($p->supplier_billdate!=null || $p->supplier_billdate!=''){{ date($date_format, strtotime($p->supplier_billdate))  }}@endif</td>
				<td>{{ (isset($p->_supplier->name)) ? $p->_supplier->name : ''  }}</td>
				<td>{{ $p->po_no }}</td>
				<td>{{ Common::fixIt($p->billAmount) }}</td>
				@if(Common::can_update('purchase') || Common::can_delete('purchase'))
				<td>
					@if(Common::can_update('purchase'))
					<a v-on:click="purchaseEdit({{ $p->id }}, $event)" class="btn btn-xs btn-info btn-circle" ><i class="fa fa-pencil" aria-hidden="true"></i></a>
					@endif
					@if(Common::can_delete('purchase'))

					<a  v-on:click="purchaseDelete({{ $p->id }}, $event)" class="btn btn-xs btn-danger btn-circle "><i class="fa fa-trash" aria-hidden="true"></i></a> 
					@endif
				</td>
				@endif
			</tr>

			@endforeach

		</tbody>

		<tfoot class="showSearch">
			<tr>
				<th style="width:2%">#</th>
				<th class="search">@lang('stock.stock_transfer_no')</th>
				<th class="search">@lang('stock.stock_transfer_date')</th>
				<th class="search">@lang('supplier.supplier')</th>
				<th class="search">@lang('stock.po_no')</th>
				<th class="search">@lang('billing.bill') @lang('billing.amt')</th>
				@if(Common::can_update('purchase') || Common::can_delete('purchase'))
				<th style="width:4%">@lang('glob.actions')</th>
				@endif
			</tr>
		</tfoot>
	</table>
</div>
