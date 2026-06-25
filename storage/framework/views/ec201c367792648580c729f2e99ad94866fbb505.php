	<div id="zReportModal" class="modal fade" role="dialog">
		<div class="modal-dialog" style="width:75%">
			<!-- Modal content-->
			<div class="modal-content">
				
				<div class="modal-header bg-purple">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title"> <?php echo app('translator')->getFromJson("glob.z_sales"); ?></h4>
				</div>

				<div class="modal-body">
					<div class="row">
						<div class="col-md-8">
							<label><?php echo app('translator')->getFromJson('glob.opening_amt'); ?> = {{reportZData.openingCash}}</label>
						</div>		
					</div>

					<!-- view table data  -->
					<div class="row">

						<div class="col-md-12 table-responsive" style="width: 100%">
							<table class="table table-bordered table-striped zsales-report-table" style="width: 100%">
								<thead>
									<tr>
										<th><?php echo app('translator')->getFromJson('glob.id'); ?></th>	
										<th><?php echo app('translator')->getFromJson('billing.bill_date'); ?></th>
										<th><?php echo app('translator')->getFromJson('billing.bill_no'); ?></th>							
										<th><?php echo app('translator')->getFromJson('billing.amt'); ?>({{curr}})</th>									
									</tr>
								</thead>


								<tbody>
					
				</tbody>
				
				<tfoot class="showSearch">
					<tr>
						<th ><?php echo app('translator')->getFromJson('glob.id'); ?></th>	
						<th class="search"><?php echo app('translator')->getFromJson('billing.bill_date'); ?></th>
						<th class="search"><?php echo app('translator')->getFromJson('billing.bill_no'); ?></th>							
						<th ><?php echo app('translator')->getFromJson('billing.amt'); ?> = {{reportZData.billAmt}}</th>	

					</tr>
				</tfoot>
			</table>

		</div>

	</div>
	<!-- view table data end -->	

	<div class="row">
		<div class="col-md-8">
			<label><?php echo app('translator')->getFromJson('glob.opening_amt'); ?> + <?php echo app('translator')->getFromJson('glob.sale'); ?> ({{curr}}) = {{fixtIt(Number(reportZData.openingCash) + Number(reportZData.billAmt))}}</label>
		</div>		
	</div>

	<div class="row">
		<div class="col-md-8">
			<label><?php echo app('translator')->getFromJson('billing.cash'); ?> ({{curr}}) = {{reportZData.cashAmt}}</label>
		</div>		
	</div>

	<div class="row">
		<div class="col-md-8">
			<label><?php echo app('translator')->getFromJson('billing.credit_card'); ?> ({{curr}}) = {{reportZData.cardAmt}}</label>
		</div>		
	</div>

	<div class="row">
		<div class="col-md-8">
			<label><?php echo app('translator')->getFromJson('billing.credit'); ?> ({{curr}}) = {{reportZData.saleCredit}}</label>
		</div>		
	</div>

	<div class="row">
		<div class="col-md-8">
			<label><?php echo app('translator')->getFromJson('billing.sale_return'); ?> ({{curr}}) = {{reportZData.saleReturn}}</label>
		</div>		
	</div>

	<div class="row">
		<div class="col-md-8">
			<label><?php echo app('translator')->getFromJson('billing.bal'); ?> ({{curr}}) = {{(fixtIt(Number(reportZData.openingCash)+Number(reportZData.billAmt)-Number(reportZData.saleReturn)))}}</label>
		</div>		
	</div>
	
	

</div>
<div class="modal-footer">
	<div class="pull-right">
		<div class="row">	
			<div class="col-md-12">
				<button type="button" class="btn text-left" id="eod" name="eod" value="eod"   v-on:click="endOfDay($event)" style="background: #0097a7; color: #fff;"><i class="fa fa-power-off" aria-hidden="true" ></i> <?php echo app('translator')->getFromJson('glob.end_of_day'); ?> </button>
				<button type="button" class="btn btn-primary text-left" id="print" name="print" value="print" :disabled="reportZData.billDetails.length == 0 ? true:false"  v-on:click="printPageZSales($event)"><i class="fa fa-print" aria-hidden="true" ></i> <?php echo app('translator')->getFromJson('glob.print'); ?> </button>
				<a :disabled="reportZData.billDetails.length == 0 ? true:false" v-on:click="sendEmailZ($event)" class="btn btn-success"><i class="fa fa-envelope" aria-hidden="true"></i> <?php echo app('translator')->getFromJson('glob.sendmail'); ?></a>	
				<a class="btn btn-danger" data-toggle="modal" data-target="#zReportModal"> <?php echo app('translator')->getFromJson('glob.close'); ?></a>
				
			</div>
		</div>	
	</div>
</div>

</div>

</div>
</div>
