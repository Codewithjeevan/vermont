@extends('layouts.app')

@section('page-title')@lang('glob.issue')@endsection

@section('page-subtitle')@lang('stock.add_edit_issue')@endsection 

@section ('breadcrumb')

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> @lang('glob.dashboard')</a></li>
	<li class="active">@lang('glob.issue')</li>
</ol>

@endsection

@section('content') 
<div class="issue">
	@if(Common::can_create('display_stock'))
	<!-- Button div -->
	<div class="row">
		<div class="col-md-12 margin-bottom">
			<a  class="btn bg-purple new-customer"  v-on:click="openSlide()" >@lang('glob.add_new')</a>
		</div>
	</div>
	<!-- Button div end -->

	<div id="issueFormDiv" class="collapse">
		<div  class="row customer-box">  
			<div class="col-md-12">
				<div class="box box-primary">
					<div class="box-body">

						<div class="row">

							<!-- mrn no -->
							<div class="col-md-4">
								<div class="form-group">
									<label for="mrn_no">@lang('stock.slip_no') :</label> 
									<span> @{{ issueStock.id }}</span>
								</div>
							</div>
							<!-- end mrn no -->

						</div>
						<!-- .row end mrn no -->

						<div class="row">

							<!-- Slip date -->
							<div class="col-md-4">
								<div class="form-group">
									<label for="">@lang('stock.slip_date')<span style="color:red">*</span></label>
									<div class="input-group">
										<div class="input-group-addon">
											<i class="ion ion-calendar"></i>
										</div>
										<input id="slip_date_id" type="text" class="form-control  input-sm datepicker" placeholder="@lang('stock.slip_date')" v-model="issueStock.slip_date" required="">
									</div>
								</div>
							</div>
							<!-- end slip date -->
							<div class="col-md-2">
								<a  v-on:click="openModal()" class="btn btn-primary btn-sm margin-top-space" ><i class="fa fa-plus"></i> @lang('glob.add') @lang('glob.item')</a>
							</div>
							
						</div>
						<!-- .row -->

						<!-- modal start -->
						<div id="issue-items" class="modal fade" role="dialog">
							<div class="modal-dialog"  style="width:75%">
								<!-- Modal content-->
								<div class="modal-content">
									<form v-on:submit.prevent="saveItem(item)" >
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal">&times;</button>
											<h4 class="modal-title">@lang('glob.display_stock')</h4>
										</div>
										<div class="modal-body">

											<div class="col-md-7">
												<div class="row">

													<!-- item name -->
													<div class="col-md-6">
														<div class="form-group">
															<label for="">@lang('item.item_name')<span style="color:red">*</span></label>
															<div class="input-group">
																<div class="input-group-addon">
																	<i class="icon ion-bag"></i>
																</div>
																<input type="text" name="itemName" class="form-control  input-sm prevent-enter itemname-autocomplete"  placeholder="@lang('item.item_name')" v-model="item.items.itemName" v-on:keyup.enter="getStock(item.items.itemName,$event)" required=""  >
																
															</div>
														</div>
													</div>
													<!-- end item name -->

													<!-- barcode -->
													<div class="col-md-6">
														<div class="form-group">
															<label >@lang('glob.barcode'){{-- <span style="color:red">*</span> --}}</label>
															<div class="input-group">
																<div class="input-group-addon">
																	<i class="fa fa-barcode"></i>
																</div>
																<input type="text" name="barcode" class="form-control  input-sm prevent-enter barcode-autocomplete"  placeholder="@lang('glob.barcode')" v-model="item.items.barcode" v-on:keyup.enter="getStock(item.items.barcode,$event)" required=""  >
																
															</div>
														</div>
													</div>
													<!-- end barcode -->

													
												</div>
												<!-- .row -->

												<div class="row">
													<!-- item rate -->
													<div class="col-md-4">
														<div class="form-group">
															<label for="">@lang('billing.rate')<span style="color:red">*</span></label>
															<div class="input-group">
																<div class="input-group-addon">
																	<i class="fa fa-money"></i>
																</div>
																<input type="text" name="rate" class="form-control  input-sm validate-decimal" placeholder="@lang('billing.rate')" v-model="item.rate" required="">
															</div>
														</div>
													</div>
													<!-- end item rate -->

													<!-- stock -->
													<div class="col-md-4">
														<div class="form-group">
															<label for="">@lang('glob.stock')<span style="color:red">*</span></label>
															<div class="input-group">
																<div class="input-group-addon">
																	<i class="fa fa-stack-overflow"></i>
																</div>
																<input type="text" name="stock" class="form-control  input-sm " placeholder="@lang('glob.stock')" v-model="item.stock"  readonly>
															</div>
														</div>
													</div>
													<!-- end stock -->

													<!-- item qty -->
													<div class="col-md-4">
														<div class="form-group">
															<label for="">@lang('item.qty')<span style="color:red">*</span></label>
															<div class="input-group">
																<div class="input-group-addon">
																	<i class="icon ion-bag"></i>
																</div>
																<input type="text" name="qty" class="form-control  input-sm validate-decimal" placeholder="@lang('item.qty')" v-model="item.qty" required="" maxlength="6" >
															</div>
														</div>
													</div>
													<!-- end item qty -->
												</div>
												<!-- .row -->

												
											</div>
											<!-- .col-md-7 -->

											<!-- remark -->
											<div class="col-md-5">
												<div class="form-group">
													<label for="">@lang('glob.remark')</label>
													<textarea name="remark" rows="4" class="form-control  input-sm fix-textarea" placeholder="@lang('glob.remark')" v-model="item.remark" ></textarea>
												</div>
											</div>
											<!-- end remark -->
										</div>
										<div class="modal-footer">
											<button type="submit"   v-if="itemOp=='add'" class="btn btn-primary pull-left"> <i class="fa fa-floppy-o"></i> @lang('glob.add') </button>
											<button type="submit"  v-if="itemOp=='edit'" class="btn btn-primary pull-left"> <i class="fa fa-floppy-o"></i> @lang('glob.update') </button>
											<a  class="btn btn-cyan pull-left" v-on:click="clearItem()"><i class="fa fa-refresh"></i> @lang('glob.reset')</a>
											<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
										</div>
									</form>
								</div>

							</div>
						</div>
						<!-- end modal -->


						<div class="box" style="border-top:0">
							<div class="box-body" style="margin:1px;border:2px solid #c8c8c8; border-radius: 5px;">
								
								<div class="col-md-12 table-responsive" >
									<table class="table table-bordered table-striped ">
										<thead>
											<tr>	
												<th>@lang('glob.barcode')</th>
												<th>@lang('item.item_name')</th>
												<th>@lang('billing.qty')</th>
												<th>@lang('billing.rate')</th>
												<th>@lang('billing.amt')</th>
												<th>@lang('glob.remark')</th>
												<th >@lang('glob.actions')</th>
											</tr>
										</thead>

										<tbody>
											<tr v-for="( i, index) in issueStock.itemList">
												<td>@{{  i.items.barcode  }}</td>
												<td>@{{  i.items.itemName  }}</td>
												<td>@{{ fixQty(i.qty)  }}</td>
												<td>@{{  fixtIt(i.rate)  }}</td>
												<td>@{{   fixtIt(i.amt)  }}</td>
												<td>@{{  i.remark  }}</td>
												<td>
													<a  v-on:click="editIssueItems(i , index)" class="btn btn-xs btn-info btn-circle" ><i class="fa fa-pencil" aria-hidden="true"></i></a>
													<a  v-on:click="deleteIssueItem(i , index)" class="btn btn-xs btn-danger btn-circle "><i class="fa fa-trash" aria-hidden="true"></i></a> 							
												</td>
											</tr>

										</tbody>
									</table>

								</div>
								{{-- .col-md-12 --}}
								
								<div class="row">
									<div class="col-md-4"> 
										<div class="form-group margin-top-space">
											<label for="">@lang('stock.approx_value_of')<span style="color:red">*</span></label>
											<div class="input-group">
												<div class="input-group-addon">
													<i class="fa fa-file-text-o"></i>
												</div>
												<input type="text" name="approx_value_of"  class="form-control  input-sm validate-decimal" placeholder="@lang('stock.approx_value_of')" required=""  v-model="issueStock.approx_value_of">
											</div>
										</div>
									</div>
								</div>

							</div>
							<!-- /.box-body -->

						</div>
						<!-- end table box -->

						<div class="box-footer no-shadow">

							<button class="btn btn-indigo" v-on:click="printPage($event)" :disabled="(issueStock.itemList.length != 0 && issueStock.slip_date !='' && issueStock.approx_value_of!='') ? false :true"><i class="fa fa-print" aria-hidden="true"></i> @lang('stock.print_slip')</button>
							<button class="btn btn-success" v-if="saveIssueOrder" v-on:click="store(issueStock, $event)" :disabled="(issueStock.itemList.length != 0 && issueStock.slip_date !='' && issueStock.approx_value_of!='') ? false :true" ><i class="fa fa-floppy-o" aria-hidden="true"></i> @lang('glob.save')</button>
							<button class="btn btn-success" v-if="updateIssueOrder" v-on:click="updateIssueStock(issueStock, $event)" :disabled="(issueStock.itemList.length != 0 && issueStock.slip_date !='' && issueStock.approx_value_of!='') ? false :true"><i class="fa fa-floppy-o" aria-hidden="true"></i> @lang('glob.update')</button>
							<button class="btn btn-cyan" v-on:click="openSlide()"><i class="fa fa-refresh" aria-hidden="true"></i> @lang('glob.reset')</button>
							<a  class="btn btn-danger add-bank" v-on:click="slideUp" >@lang('glob.cancel')</a>
							
						</div>
					</div>
					<!-- .box-body -->
				</div>
				<!-- .box .box-primary -->
			</div>
			<!-- .col-md-12 -->
		</div>
		<!--end .row  -->

	</div>
	
	@endif
	<!-- view table data  -->
	<div class="row">
		<div class="col-md-12 table-responsive issue-stock-table">

			<table class="table table-bordered table-striped datatable1 ">
				
				<thead>
					<tr>
						
						<th>@lang('stock.slip_no')</th>
						<th>@lang('stock.slip_date')</th>
						@if(Common::can_update('display_stock') || Common::can_delete('display_stock'))
						<th >@lang('glob.actions')</th>
						@endif
					</tr>
				</thead>
				
				<tbody>

					@foreach($issueData as $i)
					<tr>
						<td>{{$i->id}}</td>
						<td>@if($i->slip_date!=null || $i->slip_date!=''){{ date($date_format, strtotime($i->slip_date))}} @endif</td>
						@if(Common::can_update('display_stock') || Common::can_delete('display_stock'))
						<td>
							@if(Common::can_update('display_stock'))
							<a v-on:click="editIssueStock({{ $i->id  }}, $event)" class="btn btn-xs btn-info btn-circle" ><i class="fa fa-pencil" aria-hidden="true"></i></a>
							@endif
							@if(Common::can_delete('display_stock'))

							<a v-on:click="deleteIssueStock({{ $i->id }}, $event)" class="btn btn-xs btn-danger btn-circle "><i class="fa fa-trash" aria-hidden="true"></i></a> 
							@endif
						</td>
						@endif
					</tr>
					@endforeach


				</tbody>

				<tfoot class="showSearch">
					<tr>
						<th class="search">@lang('stock.slip_no')</th>
						<th class="search">@lang('stock.slip_date')</th>
						@if(Common::can_update('display_stock') || Common::can_delete('display_stock'))
						<th >@lang('glob.actions')</th>
						@endif
					</tr>
				</tfoot>
			</table>
		</div>
		
	</div>
	<!-- view table data end -->



	
</div>

@endsection

