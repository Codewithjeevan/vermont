<?php $__env->startSection('page-title'); ?><?php echo app('translator')->getFromJson('glob.quantity_sales'); ?><?php $__env->stopSection(); ?>

<?php $__env->startSection('page-subtitle'); ?><?php $__env->stopSection(); ?> 

<?php $__env->startSection('breadcrumb'); ?>

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-home"></i> <?php echo app('translator')->getFromJson('glob.home'); ?></a></li>
	<li><a href="#"><i class="fa fa-file-text-o"></i> <?php echo app('translator')->getFromJson('menu.sales_reports'); ?></a></li>
	<li class="active"><?php echo app('translator')->getFromJson('glob.quantity_sales'); ?></li>
</ol>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php echo e(Form::open(['action'=>'SalesReportController@exportToQuantitySales','id'=>'customer_salesReportForm'])); ?>

<div class="quantity_sales" v-cloak>

	<div class="row customer-box"> 

		<div class="col-md-12">

			<div class="box box-primary"> 

				<div class="box-body">

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
									<input id="from" type="text" name="from" class="form-control  input-sm datepicker" placeholder="<?php echo app('translator')->getFromJson('glob.from'); ?>" required="" v-model="dateArray.fromDate" v-on:change="checkValidation($event)">
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
									<input id="to" type="text" name="to" class="form-control  input-sm datepicker" placeholder="<?php echo app('translator')->getFromJson('glob.to'); ?>" required="" v-model="dateArray.toDate" v-on:change="checkValidation($event)">
								</div>
							</div>
						</div>
						<!-- End To Date -->

						<div class="col-md-3" style="margin-top: 2%;">
							<button type="button" :disabled="(dateArray.fromDate !='' && dateArray.toDate !=0 )? false : true" v-on:click="getLedger($event)"  class="btn btn-success btn-circle" > <?php echo app('translator')->getFromJson('glob.show'); ?></button>
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
	<div class="row">

		<div class="col-md-12 table-responsive">
			<table class="table table-bordered table-striped quantity-sales-table">
				<thead>
					<tr>
						<th>#</th>	
						<th><?php echo app('translator')->getFromJson('glob.category'); ?></th>
						<th><?php echo app('translator')->getFromJson('glob.barcode'); ?></th>
						<th><?php echo app('translator')->getFromJson('item.refrence_number'); ?></th>							
						<th><?php echo app('translator')->getFromJson('item.item_name'); ?></th>
						<th><?php echo app('translator')->getFromJson('glob.rate'); ?></th>
						<th><?php echo app('translator')->getFromJson('item.qty'); ?></th>
						<th v-if="intVatType!=0"><?php echo app('translator')->getFromJson('glob.vat'); ?> (%) </th>							
						<th><?php echo app('translator')->getFromJson('glob.discount'); ?>({{curr}})</th>
						<th><?php echo app('translator')->getFromJson('glob.loyalty_discount'); ?>({{curr}})</th>
						<th><?php echo app('translator')->getFromJson('billing.amt'); ?>({{curr}})</th>	
						<th><?php echo app('translator')->getFromJson('glob.percentage'); ?>(%)</th>
						
					</tr>
				</thead>


				<tbody>
					
				</tbody>
				
				<tfoot class="showSearch">
					<tr>
						<th>#</th>	
						<th class="search"><?php echo app('translator')->getFromJson('glob.category'); ?></th>
						<th class="search"><?php echo app('translator')->getFromJson('glob.barcode'); ?></th>
						<th class="search"><?php echo app('translator')->getFromJson('item.serial_number'); ?></th>							
						<th class="search"><?php echo app('translator')->getFromJson('item.item_name'); ?></th>
						<th><?php echo app('translator')->getFromJson('glob.rate'); ?></th>	
						<th><?php echo app('translator')->getFromJson('item.qty'); ?> = <label style="font-size:12px; font-weight: bold;"> {{totalQty}} </label></th>
						<th class="search" v-if="intVatType!=0"><?php echo app('translator')->getFromJson('glob.vat'); ?> (%)</th>					
						<th><?php echo app('translator')->getFromJson('glob.discount'); ?> = <label style="font-size:12px; font-weight: bold;"> {{totalDiscount}} </label></th>
						<th><?php echo app('translator')->getFromJson('glob.loyalty_discount'); ?> = <label style="font-size:12px; font-weight: bold;"> {{totalLoyDis}} </label></th>
						<th><?php echo app('translator')->getFromJson('billing.amt'); ?> = <label style="font-size:12px; font-weight: bold;"> {{total}} </label></th>	
						<th><?php echo app('translator')->getFromJson('glob.percentage'); ?></th>			
					</tr>
				</tfoot>
			</table>

		</div>

	</div>
	<!-- view table data end -->	

	<!-- /.box-body -->
	

	<!--Export Buttons-->
	<div class="row export-btn">

		<div class="col-md-2 col-sm-4">
			<button type ="submit" class="btn btn-success btn-block " name="xls" value="xls" :disabled="itemList.length == 0 ? true:false" ><i class="fa fa-file-excel-o" aria-hidden="true" ></i> <?php echo app('translator')->getFromJson('glob.export_to_xls'); ?></button> 
			
		</div>
		<div class="col-md-2 col-sm-4">			
			<button type ="submit" class="btn btn-danger btn-block " name="pdf" value="pdf" :disabled="itemList.length == 0 ? true:false" ><i class="fa fa-file-pdf-o" aria-hidden="true" ></i> <?php echo app('translator')->getFromJson('glob.export_to_pdf'); ?> </button>				
		</div>
		<div class="col-md-2 col-sm-4">			
			<button type="button" class="btn btn-primary btn-block " name="print" value="print" :disabled="itemList.length == 0 ? true:false"  v-on:click="printPage($event)"><i class="fa fa-print" aria-hidden="true" ></i> <?php echo app('translator')->getFromJson('glob.print'); ?> </button>				
		</div>
		<div class="col-md-6"></div>
		

	</div>	
	<!--End Export Buttons-->
</div>
<?php echo e(Form::close()); ?>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>