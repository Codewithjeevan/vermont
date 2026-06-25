@extends('layouts.app')
<style type="text/css">
	.content-header{
		display: none !@important;
	}
</style>
@section('content')
<div id="billing_2_VueId">

	<div class="row">

		<!-- Left section -->
		<div class="col-md-8">

			<!-- Head section -->
			<div class="box box-primary" style="margin-bottom:5px">
				<div class="box-header with-border">
					<h3 class="box-title">@lang('billing.billing')</h3>
					<div class="box-tools pull-right no-shadow">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div>
				<!-- /.box-header -->
				<div class="box-body no-top-padding" >
					<div class="row">
						<div class="col-md-3">
							<div class="form-group  no-v-margin">
								<label for="NewBill">@lang('billing.new_bill_no')</label>
								<input type="text" class="form-control input-sm" name="newBill"/>
							</div>
							<!-- form group -->
						</div>
						<!-- .col-md-3 -->

						<div class="col-md-3">
							<div class="form-group  no-v-margin">
								<label for="NewBill">@lang('billing.existing_bill_no')</label>
								<input type="text" class="form-control input-sm" name="existingBill"/>
							</div>
							<!-- form group -->
						</div>
						<!-- .col-md-3 -->

						<div class="col-md-3">
							<div class="form-group no-v-padding no-v-margin">
								<label for="NewBill">@lang('billing.return')</label>
								<input type="text" class="form-control input-sm" name="return"/>
							</div>
							<!-- form group -->
						</div>
						<!-- .col-md-3 -->

						<div class="col-md-3">
							<div class="form-group no-v-padding no-v-margin">
								<label for="NewBill">@lang('billing.bill_date')</label>
								<input type="text" class="form-control input-sm datepicker" name="billDate"/>
							</div>
							<!-- form group -->
						</div>
						<!-- .col-md-3 -->

					</div>
					<!-- .row -->

					<div class="row">

						<div class="col-md-6">
							<div class="form-group no-v-margin">
								<label for="NewBill">@lang('billing.barcode')</label>
								<input type="text" class="form-control input-sm" name="barcode"/>
							</div>
						</div>
						<!-- .col-md-9 -->

						<div class="col-md-3">
							<div class="form-group no-v-padding no-v-margin">
								<label for="serial_no">@lang('billing.serial_no')</label>
								<select name="serial_no" class="form-control input-sm select2">
									<option value=""></option>
									<option value=""></option>
								</select>
							</div>
						</div>
						<!-- .col-md-3 -->

						<div class="col-md-3">
							<div class="form-group no-v-padding no-v-margin">
								<label for="salesMan">@lang('billing.sales_man')</label>
								<select name="salesman" class="form-control input-sm select2">
									<option value=""></option>
									<option value=""></option>
								</select>
							</div>
						</div>
						<!-- .col-md-3 -->

					</div>
					<!-- .row -->

				</div>
				<!-- /.box-body -->
			</div>
			<!-- .box -->

			<div class="box-body">
				<div class="col-md-8 no-h-padding btn-toolbar"  role="toolbar" aria-label="Toolbar with button groups">
					<div class="btn-group btn-group-lg numpad" style="width:100%">
						<button class="btn bg-stylish no-shadow">1</button>
						<button class="btn bg-stylish no-shadow">2</button>
						<button class="btn bg-stylish no-shadow">3</button>
						<button class="btn bg-stylish no-shadow">4</button>
						<button class="btn bg-stylish no-shadow">5</button>
						<button class="btn bg-stylish no-shadow">6</button>
						<button class="btn bg-stylish no-shadow">7</button>
						<button class="btn bg-stylish no-shadow">8</button>
						<button class="btn bg-stylish no-shadow">9</button>
						<button class="btn bg-stylish no-shadow">0</button>
						<button class="btn bg-stylish no-shadow">.</button>
						<button class="btn bg-stylish no-shadow">C</button>
					</div>
				</div>
				<div class="col-md-4 no-h-padding">
					<div class="btn-group btn-group-lg pull-right">
						<button class="btn btn-danger no-shadow"><i class="fa fa-trash"></i></button>
						<button class="btn btn-success no-shadow"><i class="fa fa-plus"></i></button>
						<button class="btn btn-warning no-shadow"><i class="fa fa-minus"></i></button>
						<button class="btn bg-black no-shadow " data-toggle="control-sidebar"><i class="fa fa-gears"></i></button>
					</div>
				</div>
			</div>
			<!-- box-body -->

			<div class="box" style="border-top:0">

				<div class="box-body" style="margin:1px;border:2px solid #c8c8c8; border-radius: 5px;">
					<div class="table-responsive" style="height: 250px; overflow-y: scroll;">
						<table class="table table-bordered">
							<tr>
								<thead>
									<th>@lang('billing.item_name')</th>
									<th>@lang('billing.qty')</th>
									<th>@lang('billing.unit')</th>
									<th>@lang('billing.rate')</th>
									<th>@lang('billing.amt')(@{{curr}})</th>
									<th>@lang('billing.serial_no')</th>
								</thead>
							</tr>
							<tbody >
								<tr class="item-p" data-toggle="modal" data-target="#itemModal">
									<td>Mouse</td>
									<td>1</td>
									<td>pc</td>
									<td>200</td>
									<td>200</td>
									<td>11111</td>
								</tr>
								<tr class="item-p" data-toggle="modal" data-target="#itemModal">
									<td>Lenovo G580</td>
									<td>1</td>
									<td>pc</td>
									<td>2000</td>
									<td>2000</td>
									<td>14551</td>
								</tr>
							</tbody>
						</table>
						<!-- table -->
					</div>
					<!-- .table-responsive -->

				</div>
				<!-- /.box-body -->

				<div class="box-footer no-shadow">
					<div class="pull-left">
						<h4>@lang('billing.total_items'): 2 @lang('glob.total')</h4>
					</div>
					<div class="pull-right">
						<button class="btn btn-indigo"><i class="fa fa-print" aria-hidden="true"></i> @lang('glob.print')</button>
						<button class="btn btn-success"><i class="fa fa-floppy-o" aria-hidden="true"></i> @lang('glob.save')</button>
						<button class="btn btn-warning"><i class="fa fa-pencil" aria-hidden="true"></i> @lang('glob.update')</button>
						<button class="btn btn-danger"><i class="fa fa-trash" aria-hidden="true"></i> @lang('glob.delete')</button>
						<button class="btn btn-cyan"><i class="fa fa-refresh" aria-hidden="true"></i> @lang('glob.reset')</button>
						<button class="btn btn-success"><i class="fa fa-floppy-o" aria-hidden="true"></i>  @lang('glob.save') & <i class="fa fa-print" aria-hidden="true"></i> @lang('glob.print')</button>
					</div>
				</div>

			</div>
		</div>

		<!-- right sidebar -->
		<div class="col-md-4">
			<div class="box box-primary" style="margin-bottom:20px;">
				<div class="box-header with-border">
					<h3 class="box-title">@lang('billing.customer_details')</h3>
					<div class="box-tools pull-right no-shadow">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div>
				<!-- /.box-header -->
				<div class="box-body no-top-padding">
					<div class="col-md-6 no-h-padding">
						<div class="form-group no-v-padding no-v-margin">
							<label for="customerMobile">@lang('glob.customer') @lang('glob.mobile')</label>
							<input type="text" class="form-control input-sm numpad" name="customerMobile" />
						</div>
						<!-- .form-group no-v-padding no-v-margin -->
					</div>
					<div class="col-md-6 no-right-padding">
						<div class="form-group no-v-padding no-v-margin">
							<label for="customerMobile">@lang('glob.customer') @lang('glob.name')</label>
							<input type="text" class="form-control input-sm" name="customerName" />
						</div>
						<!-- .form-group no-v-padding no-v-margin -->
					</div>

				</div>
				<!-- /.box-body -->
			</div>

			<div class="box box-warning">
				<div class="box-header with-border">
					<h3 class="box-title">@lang('billing.payment_details')</h3>
					<div class="box-tools pull-right no-shadow">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div>
				<div class="box-body ">
					<div class="col-md-6 no-h-padding no-v-margin">
						<div class="radio radio-padding bg-indigo no-v-margin">
							<label><input type="radio" name="optradio"> @lang('billing.cash')</label>
						</div>
					</div>
					<div class="col-md-6 no-h-padding no-v-margin">
						<div class="radio radio-padding bg-teal no-v-margin">
							<label><input type="radio" name="optradio"> @lang('billing.card') & @lang('billing.cash')</label>
						</div>
					</div>
					<div class="col-md-6 no-h-padding no-v-margin">
						<div class="radio radio-padding bg-red no-v-margin">
							<label><input type="radio" name="optradio"> @lang('billing.credit') @lang('billing.card')</label>
						</div>
					</div>
					<div class="col-md-6 no-h-padding no-v-margin">
						<div class="radio radio-padding bg-orange no-v-margin">
							<label><input type="radio" name="optradio">  @lang('billing.credit')</label>
						</div>
					</div>  
					<div class="col-md-3 no-h-padding margin-top"> <label for="">@lang('billing.card_no'):</label></div>
					<div class="col-md-9 no-h-padding margin-top"> <input type="text" class="form-control input-sm"></div>
				</div>
			</div>


			<div class="box box-danger">
				<div class="box-header with-border">
					<h3 class="box-title">@lang('billing.bill_details')</h3>
					<div class="box-tools pull-right no-shadow">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div>
				<!-- /.box-header -->
				<div class="box-body payment-cal">
					<div class="row margin-b-2px">
						<div class="col-md-3 "><label for="" class="pull-right">@lang('billing.sub_total')</label></div>
						<div class="col-md-9"> <input type="text" class="form-control input-sm numpad" value="2200.00"></div>
					</div>
					<div class="row margin-b-2px">
						<div class="col-md-3 "><label for="" class="pull-right">@lang('billing.dis_amt')</label></div>
						<div class="col-md-9"> 
							<div class="col-md-4 no-h-padding text-right" style="padding-right:2px"><input type="text" class="form-control input-sm numpad" value="0.00"></div>
							<div class="col-md-4 no-h-padding" style="padding-right:2px"><input type="text" class="form-control input-sm numpad" value="0.00"></div>
							<div class="col-md-4 no-h-padding"><input type="text" class="form-control input-sm numpad" value="0.00"></div>
						</div>
					</div>
					<div class="row margin-b-2px">
						<div class="col-md-3 "><label for="" class="pull-right">@lang('billing.v_amt')</label></div>
						<div class="col-md-9"> <input type="text" class="form-control input-sm numpad" value="0.00"></div>
					</div>
					<div class="row margin-b-2px">
						<div class="col-md-3 "><label for="" class="pull-right">@lang('billing.cash') @lang('billing.amt')</label></div>
						<div class="col-md-9"> 
							<div class="col-md-6 no-h-padding"><input type="text" class="form-control input-sm numpad" value="2200.00"></div>
							<div class="col-md-6 no-h-padding">
								<div class="col-md-4 no-h-padding"><label    style="padding-right: 5px;"  for=""> @lang('billing.card') </label></div>
								<div class="col-md-8 no-h-padding"><input type="text" class="form-control input-sm numpad" value="0.00"></div>
							</div>
						</div>
					</div>
					<div class="row margin-b-2px">
						<div class="col-md-3 "><label for="" class="pull-right">@lang('billing.bill') @lang('billing.amt')</label></div>
						<div class="col-md-9"> <input type="text" class="form-control input-sm numpad" value="0.00"></div>
					</div>
					<div class="row margin-b-2px">
						<div class="col-md-3 "><label for="" class="pull-right">@lang('billing.received')</label></div>
						<div class="col-md-9">
							<div class="col-md-6 no-h-padding"><input type="text" class="form-control input-sm numpad" value="2200.00"></div>
							<div class="col-md-6 no-h-padding">
								<div class="col-md-4 no-h-padding"><label style="padding-right: 5px;"  for=""> @lang('billing.bal') </label></div>
								<div class="col-md-8 no-h-padding"><input type="text" class="form-control input-sm numpad" value="2200.00"></div>
							</div>
						</div>
					</div>
				</div>
				<!-- /.box-body -->
			</div>

		</div>
	</div>    

	<aside class="control-sidebar control-sidebar-dark ">
		<div class="row">
			<div class="col-md-12" style="padding:0 20px; margin-top: 65px">
				<div class="side-header"><i class="fa fa-gears"></i> @lang('billing.tools')</div>
				<button class="btn bg-purple btn-block text-left" data-toggle="modal" data-target="#searchModal"><i class="fa fa-search" aria-hidden="true"></i> @lang('billing.search_items')</button>
				<button class="btn bg-purple btn-block text-left"><i class="fa fa-reply" aria-hidden="true"></i> @lang('billing.sale_return')</button>
				<button class="btn bg-purple btn-block text-left"><i class="fa fa-print" aria-hidden="true"></i>@lang('billing.reprint_s_r_r') </button>
				<div class="btn-group btn-block">
					<button type="button" class="btn bg-purple btn-block text-left dropdown-toggle" data-toggle="dropdown">
						<i class="fa fa-area-chart" aria-hidden="true"></i> @lang('billing.reports') <span class="caret"></span></button>
						<ul class="dropdown-menu" role="menu">
							<li><a href="#"> @lang('billing.y_report') </a></li>
							<li><a href="#">@lang('billing.z_report')</a></li>
							<li><a href="#"> @lang('billing.z_qnt_report') </a></li>
						</ul>
					</div>
				</div>
			</div>
		</aside>
		<div class="control-sidebar-bg" style="position: fixed; height: auto;"></div>


		<div id="itemModal" class="modal fade" role="dialog">
			<div class="modal-dialog">

				Modal content
				<div class="modal-content">
					<div class="modal-header bg-purple">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">@lang('billing.item_details')</h4>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-md-3 form-group"><label for="">@lang('glob.item') @lang('glob.name'):</label></div>
							<div class="col-md-9 form-group">Mouse</div>
						</div>
						<div class="row">
							<div class="col-md-3 form-group"><label for="">@lang('billing.quantity'):</label></div>
							<div class="col-md-9 form-group"><input type="number" class="form-control input-sm" value="1"></div>
						</div>
						<div class="row">
							<div class="col-md-3 form-group"><label for="">@lang('billing.unit'):</label></div>
							<div class="col-md-9 form-group">pc</div>
						</div>
						<div class="row">
							<div class="col-md-3 form-group"><label for="">@lang('billing.rate'):</label></div>
							<div class="col-md-9 form-group"><input type="number" class="form-control input-sm" value="200"></div>
						</div>
						<div class="row">
							<div class="col-md-3 form-group"><label for="">@lang('billing.serial_no'):</label></div>
							<div class="col-md-9 form-group">11111</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-success pull-left" data-dismiss="modal">@lang('glob.submit')</button>
						<button type="button" class="btn bg-black" data-dismiss="modal">@lang('glob.close')</button>
					</div>
				</div>

			</div>
		</div> 

		<div id="searchModal" class="modal fade" role="dialog">
			<div class="modal-dialog" style="width:80%">

				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header bg-purple">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">@lang('billing.search_items')</h4>
					</div>
					<div class="modal-body">
						<div class="table-responsive">
							<table class="table table-bordered datatable1">
								<thead>
									<tr>
										<th>@lang('billing.barcode')</th>
										<th>@lang('glob.category')</th>
										<th>@lang('glob.item')/@lang('glob.product')</th>
										<th>@lang('billing.sp')</th>
										<th>@lang('billing.unit')</th>
										<th>@lang('billing.stock')</th>
										<th>@lang('billing.min_level_stock')</th>
										<th>@lang('billing.max_level_stock')</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>11111</td>
										<td>Glass Vase</td>
										<td>BB144-20 Glass Vase</td>
										<td>2150.00</td>
										<td>Unit</td>
										<td>5</td>
										<td>0</td>
										<td>0</td>
									</tr>
									<tr>
										<td>1454511</td>
										<td>Computer</td>
										<td>Lenovo</td>
										<td>21500.00</td>
										<td>Unit</td>
										<td>5</td>
										<td>0</td>
										<td>0</td>
									</tr>

									<tr>
										<td>1165656</td>
										<td>Computer</td>
										<td>Dell</td>
										<td>26500.00</td>
										<td>Unit</td>
										<td>5</td>
										<td>0</td>
										<td>0</td>
									</tr>
									<tr>
										<td>118787</td>
										<td>Computer</td>
										<td>HP</td>
										<td>24500.00</td>
										<td>Unit</td>
										<td>5</td>
										<td>0</td>
										<td>0</td>
									</tr>
									<tr>
										<td>11111</td>
										<td>Computer</td>
										<td>Asus</td>
										<td>20000.00</td>
										<td>Unit</td>
										<td>5</td>
										<td>0</td>
										<td>0</td>
									</tr>
									<tr>
										<td>6565611</td>
										<td>Computer</td>
										<td>Lenovo G580</td>
										<td>21500.00</td>
										<td>Unit</td>
										<td>5</td>
										<td>0</td>
										<td>0</td>
									</tr>

								</tbody>
							</table>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn bg-black" data-dismiss="modal">@lang('glob.close')</button>
					</div>
				</div>

			</div>
		</div>
	</div>
	@endsection

