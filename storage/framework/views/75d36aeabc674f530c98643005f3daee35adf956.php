	<div id="xReportModal" class="modal fade" role="dialog">
		<div class="modal-dialog" style="width:75%">
			<!-- Modal content-->
			<div class="modal-content">
				
				<div class="modal-header bg-purple">
					<button type="button" class="close" data-dismiss="modal" v-on:click="closeModal($event)">&times;</button>
					<h4 class="modal-title"> <?php echo app('translator')->getFromJson("glob.x_sales"); ?></h4>
				</div>

				<div class="modal-body">
					
					<!-- .row -->
					<div class="row">		

						<!-- From Date -->
						<div class="col-md-3">
							<div class="form-group">
								<label><?php echo app('translator')->getFromJson('glob.from'); ?><span style="color:red">*</span></label>
								<div class="input-group">
									<div class="input-group-addon">
										<i class="ion ion-calendar"></i>
									</div>
									
									<input id="from" type="text" name="from" class="form-control input-sm date-time-picker" placeholder="<?php echo e(trans('glob.from')); ?>" v-model="dateArray.fromDate" v-on:change="checkValidation($event)">
								</div>
							</div>
						</div>
						<!-- End From Date -->	
						<!-- To Date -->
						<div class="col-md-3">
							<div class="form-group">
								<label><?php echo app('translator')->getFromJson('glob.to'); ?><span style="color:red">*</span></label>
								<div class="input-group">
									<div class="input-group-addon">
										<i class="ion ion-calendar"></i>
									</div>
									
									<input id="to" type="text" name="to" class="form-control input-sm date-time-picker" placeholder="<?php echo e(trans('glob.to')); ?>" v-model="dateArray.toDate" v-on:change="checkValidation($event)">
								</div>
							</div>
						</div>
						<!-- End To Date -->

						<div class="col-md-3" style="margin-top: 2%;">
							<button type="button" :disabled="(dateArray.fromDate !='' && dateArray.toDate !=0 )? false : true" v-on:click="getLedger($event)"  class="btn btn-success btn-circle" > <?php echo app('translator')->getFromJson('glob.show'); ?>
							</button>
						</div>
					</div>
					
					<!-- .row -->	

					
					<div class="row">
						<div class="col-md-8">
							<label><?php echo app('translator')->getFromJson('glob.opening_amt'); ?> = {{reportXData.openingCash}}</label>
						</div>		
					</div>

					<!-- view table data  -->
					<div class="row">

						<div class="col-md-12 table-responsive" style="width: 100%">
							<table class="table table-bordered table-striped xsales-report-table" style="width: 100%">
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
										<th ><?php echo app('translator')->getFromJson('billing.amt'); ?> = {{reportXData.billAmt}}</th>	

									</tr>
								</tfoot>
							</table>

						</div>

					</div>
					<!-- view table data end -->	

					<div class="row">
						<div class="col-md-8">
							<label><?php echo app('translator')->getFromJson('glob.opening_amt'); ?> + <?php echo app('translator')->getFromJson('glob.sale'); ?> ({{curr}}) = {{fixtIt(Number(reportXData.openingCash) + Number(reportXData.billAmt))}}</label>
						</div>		
					</div>

					<div class="row">
						<div class="col-md-8">
							<label><?php echo app('translator')->getFromJson('billing.cash'); ?> ({{curr}}) = {{fixtIt(reportXData.cashAmt)}}</label>
						</div>		
					</div>

					<div class="row">
						<div class="col-md-8">
							<label><?php echo app('translator')->getFromJson('billing.credit_card'); ?> ({{curr}}) = {{fixtIt(reportXData.cardAmt)}}</label>
						</div>		
					</div>

					<div class="row">
						<div class="col-md-8">
							<label><?php echo app('translator')->getFromJson('billing.credit'); ?> ({{curr}}) = {{fixtIt(reportXData.saleCredit)}}</label>
						</div>		
					</div>

					<div class="row">
						<div class="col-md-8">
							<label><?php echo app('translator')->getFromJson('billing.sale_return'); ?> ({{curr}}) = {{fixtIt(reportXData.saleReturn)}}</label>
						</div>		
					</div>

					<div class="row">
						<div class="col-md-8">
							<label><?php echo app('translator')->getFromJson('billing.bal'); ?> ({{curr}}) = {{fixtIt(Number(reportXData.openingCash)+Number(reportXData.billAmt)-Number(reportXData.saleReturn))}}</label>
						</div>		
					</div>


				</div>
				<div class="modal-footer">
					<div class="pull-right">
						<div class="row">		
							<div class="col-md-12">
								<button class="btn btn-primary text-left" name="print" value="print" :disabled="reportXData.billDetails.length == 0 ? true:false"  v-on:click="printPageXSales($event)"><i class="fa fa-print" aria-hidden="true" ></i> <?php echo app('translator')->getFromJson('glob.print'); ?> </button>	
								<a :disabled="reportXData.billDetails.length == 0 ? true:false"  v-on:click="sendEmailX($event)" class="btn btn-success"><i class="fa fa-envelope" aria-hidden="true"></i> <?php echo app('translator')->getFromJson('glob.sendmail'); ?></a>	
								<a class="btn btn-danger" data-toggle="modal" data-target="#xReportModal" v-on:click="closeModal($event)"> <?php echo app('translator')->getFromJson('glob.close'); ?></a>
							</div>			

						</div>
					</div>
				</div>

			</div>

		</div>
	</div>
