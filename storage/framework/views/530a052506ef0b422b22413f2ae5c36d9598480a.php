<?php $__env->startSection('page-title'); ?><?php echo app('translator')->getFromJson('glob.crm_report'); ?><?php $__env->stopSection(); ?>

<?php $__env->startSection('page-subtitle'); ?><?php $__env->stopSection(); ?> 

<?php $__env->startSection('breadcrumb'); ?>

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-home"></i> <?php echo app('translator')->getFromJson('glob.home'); ?></a></li>
	<li><a href="#"><i class="fa fa-file-text-o"></i> <?php echo app('translator')->getFromJson('menu.sales_reports'); ?></a></li>
	<li class="active"><?php echo app('translator')->getFromJson('glob.crm_report'); ?></li>
</ol>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php echo e(Form::open(['action'=>'SalesReportController@exportToCRMReport','id'=>'crm_reportForm'])); ?>

<div class="crm_report">

	<div class="row customer-box"> 

		<div class="col-md-12">

			<div class="box box-primary"> 

				<div class="box-body">

					<!-- .row -->
					<div class="row">
						<div class="col-md-3">
							<div class="form-group no-v-padding no-v-margin">
								<label for="account"><?php echo app('translator')->getFromJson('glob.customer'); ?><span style="color:red">*</span></label>
								
								<v-select  :options="customers" :text="'name'" v-model="customer" :vrequired="true" :vname="'customer_id'"></v-select>
							</div>								
						</div>	
						<div class="col-md-3">
							<div class="form-group no-v-padding no-v-margin">
								<label for="account"><?php echo app('translator')->getFromJson('glob.brand'); ?><span style="color:red">*</span></label>
								
								<v-select  :options="brands" :text="'categorie'" v-model="brand" :vrequired="true" :vname="'brand_id'"></v-select>
							</div>								
						</div>	
						
						<!-- From Date -->
						<div class="col-md-2">
							<div class="form-group">
								<label><?php echo app('translator')->getFromJson('glob.from'); ?><span style="color:red">*</span></label>
								<div class="input-group">
									<div class="input-group-addon">
										<i class="ion ion-calendar"></i>
									</div>
									<input id="from" type="text" name="from" class="form-control  input-sm datepicker" placeholder="<?php echo app('translator')->getFromJson('glob.from'); ?>" required="" v-model="dateArray.fromDate" >
								</div>
							</div>
						</div>
						<!-- End From Date -->	
						<!-- To Date -->
						<div class="col-md-2">
							<div class="form-group">
								<label><?php echo app('translator')->getFromJson('glob.to'); ?><span style="color:red">*</span></label>
								<div class="input-group">
									<div class="input-group-addon">
										<i class="ion ion-calendar"></i>
									</div>
									<input id="to" type="text" name="to" class="form-control  input-sm datepicker" placeholder="<?php echo app('translator')->getFromJson('glob.to'); ?>" required="" v-model="dateArray.toDate">
								</div>
							</div>
						</div>
						<!-- End To Date -->
						<div class="col-md-2" style="margin-top: 2%;">
							<button type="button" v-on:click="getLedger($event)" :disabled="(brand !='' && customer != '' && dateArray.fromDate !='' && dateArray.toDate !='' )? false : true"  class="btn btn-success btn-circle" > <?php echo app('translator')->getFromJson('glob.show'); ?></button>
						</div>
					</div>
					
					<!-- .row -->				
				</div>				

			</div>
			<!-- .box .box-primary -->

		</div>
		<!-- .col-md-12 -->		
	</div>
	<!--end .row .customer-box -->	

	<!-- view table data  -->
	<!-- view table data  -->
	<div class="row">

		<div class="col-md-12 table-responsive">
			<table class="table table-bordered table-striped crm-report-table">
				<thead>
					<tr>
						<th><?php echo app('translator')->getFromJson('glob.customer_name'); ?></th>
						
					</tr>
				</thead>


				<tbody>
					
				</tbody>
				
				<tfoot >
					<tr>
						<th><?php echo app('translator')->getFromJson('glob.customer_name'); ?></th>
					</tr>
				</tfoot>
			</table>

		</div>

	</div>
	<!-- view table data end -->
	<!-- view table data end -->	
	<!-- /.box-body -->
	
	<div class="row">
		<div class="col-md-8">
			<label><?php echo app('translator')->getFromJson('glob.totalTransaction'); ?>  =  {{fixQty(parseFloat(totalTransaction))}}</label>
			
		</div>
	</div>
	<div class="row">
		<div class="col-md-8">
			<label><?php echo app('translator')->getFromJson('glob.totalCustomer'); ?> = {{fixQty(totalCustomer)}}</label>
			
		</div>
	</div>

	<div class="row">
		<div class="col-md-8">
			<label><?php echo app('translator')->getFromJson('glob.totalItemQty'); ?> = {{fixQty(totalQty)}}</label>
			
		</div>
	</div>


	<!--Export Buttons-->
	<div class="row export-btn">

		<div class="col-md-2 col-sm-4">
			<button type ="submit" class="btn btn-success btn-block " name="xls" value="xls" ><i class="fa fa-file-excel-o" aria-hidden="true" ></i> <?php echo app('translator')->getFromJson('glob.export_to_xls'); ?></button> 
			
		</div>
		<div class="col-md-2 col-sm-4">			
			<button type ="submit" class="btn btn-danger btn-block " name="pdf" value="pdf"  ><i class="fa fa-file-pdf-o" aria-hidden="true"></i> <?php echo app('translator')->getFromJson('glob.export_to_pdf'); ?> </button>				
		</div>
		<div class="col-md-2 col-sm-4">			
			<button type="button" class="btn btn-primary btn-block " name="print" value="print" v-on:click="printPage($event)"><i class="fa fa-print" aria-hidden="true" ></i> <?php echo app('translator')->getFromJson('glob.print'); ?> </button>				
		</div>
		<div class="col-md-6"></div>
		

	</div>	
	<!--End Export Buttons-->
</div>
<?php echo e(Form::close()); ?>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>