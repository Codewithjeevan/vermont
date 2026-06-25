@extends('layouts.app')
@section('page-title')
@lang('menu.financial')
@endsection
@section('page-subtitle') @lang('glob.customer_receipt') @endsection
@section ('breadcrumb')
<ol class="breadcrumb">
	<li><a href=""><i class="fa fa-dashboard"></i>@lang('glob.dashboard')</a></li>
	<li class="active">@lang('menu.financial')</li>
	<li class="active">@lang('glob.customer_receipt')</li>
</ol>
@endsection
@section('content')
<div class="customerReceipt">
	
	@if(Common::can_create('customer_receipt'))
	<div class="row">
		<div class="col-md-6 margin-bottom">
			<a class="btn bg-purple new-customer"  v-on:click="openSlide()">@lang('glob.add_new')</a>
		</div>
	</div>
	<div id="receiptFormDiv" class="row collapse">

		<div class="col-md-12">

			<div class="box box-primary">
				<form v-on:submit.prevent="saveReceipt()">
					<div class="box-body">
						<div class="row">
							<!-- receipt no -->
							<div class="col-md-6">
								<div class="form-group">
									<label for="">@lang('customerReceipt.receipt_no') : </label>
									<span>@{{ receipt.id }}</span>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group pull-right">
									Date: <strong > @{{ dateFormat(receipt.receipt_date)}}</strong>
								</div>
							</div>
							<!-- End receipt no -->
						</div>
						<div class="row">


							<!-- customer id  -->
							<div class="col-md-2">
								<div class="form-group">
									<label for="">@lang('customer.customer_id') <span style="color:red">*</span></label>
									<div class="input-group">
										<div class="input-group-addon">
											<i class="ion ion-person"></i>
										</div>
										<v-select  :options="customers" :text="'id'" v-model="receipt.customer_id" :vrequired="true" v-on:vchange="getBills(receipt.customer_id)"></v-select>
									</div>
								</div>
							</div>
							<!-- End customer id  -->
							<!-- customer -->
							<div class="col-md-4">
								<div class="form-group">
									<label for="">@lang('glob.customer') <span style="color:red">*</span></label>
									<div class="input-group">
										<div class="input-group-addon">
											<i class="ion ion-person"></i>
										</div>
										<v-select  :options="customers" :text="'name'" v-model="receipt.customer_id" :vrequired="true" v-on:vchange="getBills(receipt.customer_id)"></v-select>
									</div>
								</div>
							</div>
							<!-- End customer -->
							<!-- customer -->
							<div class="col-md-3">
								<div class="form-group">
									<label for="">@lang('glob.mobile') <span style="color:red">*</span></label>
									<div class="input-group">
										<div class="input-group-addon">
											<i class="ion ion-iphone"></i>
										</div>
										<v-select  :options="customers" :text="'mobile'" v-model="receipt.customer_id" :vrequired="true" v-on:vchange="getBills(receipt.customer_id)"></v-select>
									</div>
								</div>
							</div>
							<!-- End customer -->

							<!-- Against bill -->
							<div class="col-md-3 ">
								<div class="form-group">
									<label for="">@lang('customerReceipt.against_bill') <span style="color:red">*</span></label>
									<div class="input-group">
										<div class="input-group-addon">
											<i class="fa fa-file-text-o" v-show="!spinner_bill"></i>
											<i class="fa fa-spinner fa-pulse fa-fw" v-show="spinner_bill"></i>
										</div>
										<select name="" id="" class="form-control input-sm " placeholder="@lang('customerReceipt.against_bill')" v-model="receipt.against_bill_id" v-on:change="getAmtAgainstBill(receipt.against_bill_id)" required>
											<option value=""></option>
											<option v-for="b in bills" :value="b.id " v-if="receipt.customer_id == b.customer_id && b.balance > 0 || operation == 'edit' ">@{{ fixBill(b.id) }}</option>
										</select>
									</div>
								</div>
							</div>
							<!--End Against bitt -->

						</div>
						<!-- .row -->
						
						<div class="row" >
							<!-- Mode -->
							<div class="col-md-3">
								<div class="form-group">
									<label for="">@lang('customerReceipt.mode') <span style="color:red">*</span></label>
									<select   name="moed" id="" class="form-control input-sm" v-model="receipt.pay_mode"  v-on:change="payCheque(receipt.pay_mode)" required>
										<option value=""></option>
										<option value="1">Cash</option>
										<option value="4">Credit card</option>
										<option value="5">Cheque</option>
									</select>
								</div>
							</div>
							<!-- End mode -->
							<!-- Check/draft no -->
							<div class="col-md-3" >
								<div class="form-group">
									<label for="">@lang('customerReceipt.ch_dr_no') <span  v-if="show_pay_cheque" style="color:red">*</span></label>
									<input type="text" class="form-control isCheque input-sm" placeholder="@lang('glob.check_number')" v-model="receipt.cheque_draft_no" >
								</div>
							</div>
							<!--End Check/draft no -->

							<!-- Check/draft Date -->
							<div class="col-md-3" >
								<div class="form-group">
									<label for="">@lang('customerReceipt.ch_dr_dt') <span  v-if="show_pay_cheque" style="color:red">*</span></label>
									<div class="input-group" >
										<div class="input-group-addon"><i class="fa fa-calendar-check-o" aria-hidden="true"></i></div>
										<input id="chq_date" type="text" class="form-control isCheque input-sm datepicker" placeholder="@lang('glob.check_date')" v-model="receipt.cheque_draft_date" >
									</div>
								</div>
							</div>
							<!--End Check/draft Date -->


							<!-- Bank name -->
							<div class="col-md-3" >
								<div class="form-group">
									<label for="">@lang('glob.bank') @lang('glob.name') <span v-if="show_pay_cheque"  style="color:red">*</span></label>
									<div class="input-group">
										<div class="input-group-addon">
											<i class="fa fa-university" aria-hidden="true"></i>
										</div>
										<select name="" id="" class="form-control isCheque input-sm " placeholder="@lang('glob.bank') @lang('glob.name')" v-model="receipt.bank_id" >
											<option value=""></option>
											<option v-for=" b in banks" :value="b.id">@{{b.name}}</option>
										</select>
									</div>
								</div>
							</div>
							<!-- End bank name -->
							

						</div>
						<!-- .row -->

						<div class="row">

							<div class="col-md-6">
								<div class="row">
									<!-- bank Dept -->
									<div class="col-md-6">
										<div class="form-group">
											<label for="">@lang('customerReceipt.dep_bank')</label>
											<input type="text" name="" id="" class="form-control input-sm" placeholder="@lang('customerReceipt.dep_bank')" v-model="receipt.dep_bank" >
										</div>
									</div>
									<!-- End bank dept -->

									<!-- Start paid amount -->
									<div class="col-md-6">
										<div class="form-group">
											<label for="">@lang('glob.paid_amount') <span style="color:red">*</span></label>
											<input type="text" name="" id="" class="form-control input-sm"  v-model="receipt.paid_amount" disabled="">
										</div>
									</div>
									<!-- End paid amount -->

									
								</div>
								<div class="row">
									<div class="col-md-12">
										<!-- Mode -->
										<div class="form-group">
											<label for="">@lang('customerReceipt.receipt_amount') <span style="color:red">*</span></label>
											<div class="input-group">
												<div class="input-group-addon">
													<i class="fa fa-money" aria-hidden="true"></i>
												</div>
												<input type="text" name="" id="" class="form-control input-sm" placeholder="@lang('customerReceipt.receipt_amount')" v-model="receipt.receipt_amt"  required="">
											</div>
										</div>
										<!-- End mode -->
									</div>
								</div>
								
							</div>
							<div class="col-md-6">
								<div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<label for="">@lang('glob.remark')</label>
											<textarea name="" class="form-control fix-textarea" rows="3" placeholder="@lang('glob.remark')" v-model="receipt.remark"></textarea>
										</div>
									</div>
								</div>
							</div>

						</div>
						<!-- .row -->

					</div>
					<!-- .nox-body -->

					<div class="box-footer">
						<div class="form-group">
							<button type="submit" id="btnAdd" class="btn btn-primary add-customer" v-if="operation=='add'"><i class="fa fa-floppy-o"></i> @lang('glob.save')</button>
							<button type="submit" id="btnUpdate" class="btn btn-primary add-customer" v-if="operation=='edit'"><i class="fa fa-floppy-o"></i> @lang('glob.update')</button>
							<button type="button" class="btn btn-cyan" v-on:click="clearReceipt"><i class="fa fa-refresh"></i> @lang('glob.reset')</button>
							<button type="button" class="btn btn-danger add-customer" v-on:click="slideUp"> @lang('glob.cancel')</button>
						</div>
					</div>
					<!-- box-footer -->
				</form>

			</div>
			<!-- .box-primary -->
		</div>
		<!-- .col-md-12 -->

		<div id="Bank-modal" class="modal  fade" role="dialog">
			<div class="modal-dialog">
				<form v-on:submit.prevent="addBank()">
					<!-- Modal content-->
					<div class="modal-content">

						<div class="modal-header bg-faint-black">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title">@lang('glob.add') @lang('glob.bank')</h4>
						</div>

						<div class="modal-body">
							<div class="form-group">
								<label for="">@lang('glob.bank') @lang('glob.name')</label>
								<div class="input-group">
									<div class="input-group-addon"><i class="fa fa-bank"></i></div>
									<input type="text" class="form-control" v-model="new_bank" required="">
								</div>
							</div>
						</div>

						<div class="modal-footer">
							<button type="submit" class="btn btn-primary pull-left"  >@lang('glob.add')</button>
							<button type="button" class="btn btn-default" data-dismiss="modal">@lang('glob.close')</button>
						</div>

					</div>
				</form>
			</div>
		</div>
	</div>
	@endif

	<div id="receipt-table" class="row">
		<div class="col-md-12">
			<div class="table-responsive">
				<table class="table datatable1">
					<thead>
						<tr>
							<th>#</th>
							<th >@lang('customerReceipt.receipt_date')</th>
							<th >@lang('glob.customer')</th>
							<th >@lang('customerReceipt.bill_no')</th>
							<th >@lang('customerReceipt.mode')</th>
							<th >@lang('customerReceipt.ch_dr_no')</th>
							<th >@lang('customerReceipt.ch_dr_dt')</th>
							<th >@lang('glob.bank')</th>
							<th >@lang('customerReceipt.receipt_amt')</th>
							<th >@lang('glob.remark')</th>							
							@if(Common::can_update('customer_receipt') || Common::can_delete('customer_receipt'))

							<th style="width:4%">@lang('glob.actions')</th>

							@endif
						</tr>
					</thead>
					<tbody>
						@foreach($receiptAll as $r)
						<tr>
							<td>{{ $r->id }}</td>
							<td>@if($r->receipt_date!=null || $r->receipt_date!='') {{ date($date_format, strtotime($r->receipt_date))  }} @endif</td>
							<td>@if(isset($r->customer->name)) {{$r->customer->name}} @endif</td>
							<td>{{ \Common::fixBill($r->against_bill_id) }}</td>
							<td>@if($r->pay_mode == 1) Cash @elseif($r->pay_mode == 4) Credit card @elseif($r->pay_mode == 5) Cheque @else - @endif </td>
							<td>@if($r->cheque_draft_no !=null){{$r->cheque_draft_no}} @else - @endif</td>
							<td>@if($r->cheque_draft_date !=null){{ date($date_format, strtotime($r->cheque_draft_date))}} @else - @endif</td>
							<td>@if($r->bank !=null){{$r->bank->name}} @else - @endif</td>
							<td>{{ Common::fixIt($r->receipt_amt)}} </td>
							<td>@if($r->remark !=null){{$r->remark}} @else - @endif</td>
							@if(Common::can_update('bank') || Common::can_delete('bank'))

							<td>

								@if(Common::can_update('customer_receipt'))

								<a  v-on:click="editReceipt({{ $r->id  }}, $event)" class="btn btn-xs btn-info btn-circle" ><i class="fa fa-pencil" aria-hidden="true"></i></a>

								@endif
								@if(Common::can_delete('customer_receipt'))

								<a  v-on:click="deleteReceipt({{ $r->id  }}, $event)" class="btn btn-xs btn-danger btn-circle "><i class="fa fa-trash" aria-hidden="true"></i></a>  

								@endif
							</td>

							@endif
							
						</tr>
						@endforeach
					</tbody>
					<tfoot class="showSearch">
						<tr>
							<th class="search">#</th>
							<th class="search">@lang('customerReceipt.receipt_date')</th>
							<th class="search">@lang('glob.customer')</th>
							<th class="search">@lang('customerReceipt.bill_no')</th>
							<th class="search">@lang('customerReceipt.mode')</th>
							<th class="search">@lang('customerReceipt.ch_dr_no')</th>
							<th class="search">@lang('customerReceipt.ch_dr_dt')</th>
							<th class="search">@lang('glob.bank')</th>
							<th class="search">@lang('customerReceipt.receipt_amt')</th>
							<th class="search">@lang('glob.remark')</th>
							@if(Common::can_update('customer_receipt') || Common::can_delete('customer_receipt'))

							<th style="width:4%">@lang('glob.actions')</th>

							@endif
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>

</div>
@endsection