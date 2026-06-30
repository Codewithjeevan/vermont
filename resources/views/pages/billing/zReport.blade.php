	<div id="zReportModal" class="modal fade" role="dialog">
		<div class="modal-dialog" style="width:75%">
			<!-- Modal content-->
			<div class="modal-content">
				
				<div class="modal-header bg-purple">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title"> @lang("glob.z_sales")</h4>
				</div>

				<div class="modal-body">
					<div class="row">
						<div class="col-md-8">
							<label>@lang('glob.opening_amt') = @{{reportZData.openingCash}}</label>
						</div>		
					</div>

					<!-- view table data  -->
					<div class="row">

						<div class="col-md-12 table-responsive" style="width: 100%">
							<table class="table table-bordered table-striped zsales-report-table" style="width: 100%">
								<thead>
									<tr>
										<th>@lang('glob.id')</th>	
										<th>@lang('billing.bill_date')</th>
										<th>@lang('billing.bill_no')</th>							
										<th>@lang('billing.amt')(@{{curr}})</th>									
									</tr>
								</thead>


								<tbody>
					
				</tbody>
				
				<tfoot class="showSearch">
					<tr>
						<th >@lang('glob.id')</th>	
						<th class="search">@lang('billing.bill_date')</th>
						<th class="search">@lang('billing.bill_no')</th>							
						<th >@lang('billing.amt') = @{{reportZData.billAmt}}</th>	

					</tr>
				</tfoot>
			</table>

		</div>

	</div>
	<!-- view table data end -->	

	<div class="row">
		<div class="col-md-8">
			<label>@lang('glob.opening_amt') + @lang('glob.sale') (@{{curr}}) = @{{fixtIt(Number(reportZData.openingCash) + Number(reportZData.billAmt))}}</label>
		</div>		
	</div>

	<div class="row">
		<div class="col-md-8">
			<label>@lang('billing.cash') (@{{curr}}) = @{{reportZData.cashAmt}}</label>
		</div>		
	</div>

	<div class="row">
		<div class="col-md-8">
			<label>@lang('billing.credit_card') (@{{curr}}) = @{{reportZData.cardAmt}}</label>
		</div>		
	</div>

	<div class="row">
		<div class="col-md-8">
			<label>@lang('billing.credit') (@{{curr}}) = @{{reportZData.saleCredit}}</label>
		</div>		
	</div>

	<div class="row">
		<div class="col-md-8">
			<label>Dubai Mall Online (@{{curr}}) = @{{reportZData.saleNoonCredit}}</label>
		</div>		
	</div>

	<div class="row">
		<div class="col-md-8">
			<label>@lang('billing.sale_return') (@{{curr}}) = @{{reportZData.saleReturn}}</label>
		</div>		
	</div>

	<div class="row">
		<div class="col-md-8">
			<label>@lang('billing.bal') (@{{curr}}) = @{{(fixtIt(Number(reportZData.openingCash)+Number(reportZData.billAmt)-Number(reportZData.saleReturn)))}}</label>
		</div>		
	</div>
	
	

</div>
<div class="modal-footer">
	<div class="pull-right">
		<div class="row">	
			<div class="col-md-12">
				<button type="button" class="btn text-left" id="eod" name="eod" value="eod"   v-on:click="endOfDay($event)" style="background: #0097a7; color: #fff;"><i class="fa fa-power-off" aria-hidden="true" ></i> @lang('glob.end_of_day') </button>
				<button type="button" class="btn btn-primary text-left" id="print" name="print" value="print" :disabled="reportZData.billDetails.length == 0 ? true:false"  v-on:click="printPageZSales($event)"><i class="fa fa-print" aria-hidden="true" ></i> @lang('glob.print') </button>
				<a :disabled="reportZData.billDetails.length == 0 ? true:false" v-on:click="sendEmailZ($event)" class="btn btn-success"><i class="fa fa-envelope" aria-hidden="true"></i> @lang('glob.sendmail')</a>	
				<a class="btn btn-danger" data-toggle="modal" data-target="#zReportModal"> @lang('glob.close')</a>
				
			</div>
		</div>	
	</div>
</div>

</div>

</div>
</div>
