<div class="col-md-12 table-responsive">

			<table class="table table-bordered table-striped datatable1 ">
				
				<thead>
					<tr>
						<th style="width:2%">#</th>
						<th>@lang('glob.p_date')</th>
						<th>@lang('glob.payment_to')</th>
						<th>@lang('billing.bill_no')</th>
						<th>@lang('glob.mode')</th>
						<th>@lang('glob.ch_dr_no')</th>
						<th>@lang('glob.ch_dr_date')</th>
						<th>@lang('glob.bank')</th>
						<th>@lang('glob.payment_amt')</th>
						<th>@lang('glob.remark')</th>
						@if(Common::can_update('payment') || Common::can_delete('payment'))

						<th style="width:4%">@lang('glob.actions')</th>

						@endif
					</tr>
				</thead>
				
				<tbody>

					@foreach($payments as $p)
					<tr>
						<td>{{ $p->id }}</td>
						<td>@if($p->payment_date!=null || $p->payment_date !='') {{ date($date_format, strtotime($p->payment_date))  }} @endif</td>
						<td>@if(isset($p->_supplier->name)) {{ $p->_supplier->name }} @endif</td>
						<td>{{ $p->bill_no }}</td>
						<td>{{ $p->mode }}</td>
						<td>{{ $p->ch_no }}</td>
						<td>@if($p->ch_date!=null || $p->ch_date !='') {{date($date_format, strtotime( $p->ch_date)) }} @endif</td>
						<td>{{ $p->bank_id }}</td>
						<td>{{ Common::fixIt($p->paid_amount) }}</td>
						<td>{{ $p->remark }}</td>
						
						@if(Common::can_update('payment') || Common::can_delete('payment'))

						<td>

							@if(Common::can_update('payment'))

							<a v-on:click="edit({{ $p->id }}, $event)" class="btn btn-xs btn-info btn-circle" ><i class="fa fa-pencil" aria-hidden="true"></i></a>
							
							@endif
							@if(Common::can_delete('payment'))

							<a v-on:click="deletePayment({{ $p->id  }}, $event)" class="btn btn-xs btn-danger btn-circle "><i class="fa fa-trash" aria-hidden="true"></i></a> 
							
							@endif
						</td>

						@endif
						
					</tr>

					@endforeach

				</tbody>

				<tfoot class="showSearch">
					<tr>
						<th style="width:2%">#</th>
						<th class="search">@lang('glob.p_date')</th>
						<th class="search">@lang('glob.payment_to')</th>
						<th class="search">@lang('billing.bill_no')</th>
						<th class="search">@lang('glob.mode')</th>
						<th class="search">@lang('glob.ch_dr_no')</th>
						<th class="search">@lang('glob.ch_dr_date')</th>
						<th class="search">@lang('glob.bank')</th>
						<th class="search">@lang('glob.payment_amt')</th>
						<th class="search">@lang('glob.remark')</th>
						@if(Common::can_update('payment') || Common::can_delete('payment'))

						<th style="width:4%">@lang('glob.actions')</th>

						@endif
					</tr>
				</tfoot>
			</table>
		</div>
		