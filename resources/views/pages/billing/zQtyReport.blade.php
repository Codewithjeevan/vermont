	<div id="zQtyReportModal" class="modal fade" role="dialog">
		<div class="modal-dialog" style="width:75%">
			<!-- Modal content-->
			<div class="modal-content">
				
				<div class="modal-header bg-purple">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title"> @lang("glob.z_qty")</h4>
				</div>

				<div class="modal-body">				

					<!-- view table data  -->
					<div class="row">

						<div class="col-md-12 table-responsive" style="width: 100%">
							<table class="table table-bordered table-striped z-qty-sales-report-table" style="width: 100%">
								<thead>
									<tr>
										<th>@lang('glob.id')</th>	
										<th>@lang('item.item_name')</th>
										<th>@lang('item.qty')</th>										
									</tr>
								</thead>


								<tbody>
									
								</tbody>
								
								<tfoot class="showSearch">
									<tr>
										<th >@lang('glob.id')</th>	
										<th class="search">@lang('item.item_name')</th>
										<th class="search">@lang('item.qty')</th>	

									</tr>
								</tfoot>
							</table>

						</div>

					</div>
					<!-- view table data end -->	


					

				</div>
				<div class="modal-footer">
					<div class="pull-right">
						<div class="row">	
							<div class="col-md-12">	
								<button type="button" class="btn btn-primary text-left" name="print" value="print" :disabled="reportZQtyData.billDetails.length == 0 ? true:false"  v-on:click="printPageZQtySales($event)"><i class="fa fa-print" aria-hidden="true" ></i> @lang('glob.print') </button>	
								<a :disabled="reportZQtyData.billDetails.length == 0 ? true:false"  v-on:click="sendEmailZQty($event)" class="btn btn-success"><i class="fa fa-envelope" aria-hidden="true"></i> @lang('glob.sendmail')</a>
								<a class="btn btn-danger" data-toggle="modal" data-target="#zQtyReportModal"> @lang('glob.close')</a>
							</div>		
						</div>	
					</div>
				</div>

			</div>

		</div>
	</div>
