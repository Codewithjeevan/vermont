<?php $__env->startSection('page-title'); ?><?php echo app('translator')->getFromJson('glob.stock_ledger'); ?><?php $__env->stopSection(); ?>

<?php $__env->startSection('page-subtitle'); ?><?php $__env->stopSection(); ?> 

<?php $__env->startSection('breadcrumb'); ?>

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-home"></i> <?php echo app('translator')->getFromJson('glob.home'); ?></a></li>
	<li><a href="#"><i class="fa fa-file-text-o"></i> <?php echo app('translator')->getFromJson('menu.reports'); ?></a></li>
	<li class="active"><?php echo app('translator')->getFromJson('glob.stock_ledger'); ?></li>
</ol>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="stock_ledger">
	
	<?php echo e(Form::open(['action'=>'StockLedgerController@exportTo','id'=>'stockLedgerForm'])); ?>



	<div class="row customer-box"> 

		<div class="col-md-12">

			<div class="box box-primary"> 

				<div class="box-body">

					<!-- .row -->
					<div class="row">
						<div class="col-md-3" >
							<div class="form-group no-v-padding no-v-margin">
								<label for="account">Search</label>
								<input type="text" name="search" placeholder="Search" v-model="search_item" v-on:keyup.enter="searchItem(search_item, $event)" class="prevent-enter input-sm form-control" > 
							</div>
						</div>


						<div class="col-md-3">
							<div class="form-group no-v-padding no-v-margin">
								<label for="account"><?php echo app('translator')->getFromJson('glob.items'); ?><span style="color:red">*</span></label>
								<v-select  :options="items" :text="'itemName'" v-model="item" :vrequired="true" :vname="'Supplier'" id="category"></v-select>
							</div>								
						</div>	
						<div class="col-md-3">
							<div class="form-group no-v-padding no-v-margin">
								<label for="account"><?php echo app('translator')->getFromJson('glob.barcode'); ?><span style="color:red">*</span></label>
								<v-select  :options="items" :text="'barcode'" v-model="item" :vrequired="true" :vname="'Supplier'" id="sub_category"></v-select>
							</div>								
						</div>

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
						
					</div>
					<div class="row">
						
						<div class="col-md-3 ">
							<button type="button" :disabled="(item !='' && dateArray.fromDate !='' && dateArray.toDate !=0 )? false : true" v-on:click="getLedger($event)"  class="btn btn-success" ><?php echo app('translator')->getFromJson('glob.show'); ?></button>	
						</div>	
					</div>
					
				</div>				

			</div>
			<!-- .box .box-primary -->

		</div>
		<!-- .col-md-12 -->		
	</div>
	<!--end .row .customer-box -->					

	<!-- view table data  -->
	<div class="row">

		<div class="table-spinner" style="display: none;margin-top: 225px;
		margin-left: 150px;
		">
		<i class="fa fa-spinner fa-pulse fa-spin  fa-4x fa-fw"></i>
		<span class="sr-only">Loading...</span>
	</div>

	<div class="col-md-12 table-responsive">
		<table class="table table-bordered table-striped stock-ledger-table">
			<thead>
				<tr>
					<th><?php echo app('translator')->getFromJson('item.item_name'); ?></th>
				</tr>
			</thead>
			<tbody>
			</tbody>

			<tfoot class="showSearch">
				<tr>
					<th class="search"><?php echo app('translator')->getFromJson('item.item_name'); ?></th>				
				</tr>
			</tfoot>
		</table>

	</div>

</div>
<!-- view table data end -->	


<!-- box-footer -->

<!--Export Buttons-->
<div class="row export-btn">

	<div class="col-md-2 col-sm-4">
		<button type ="submit" class="btn btn-success btn-block"  name="xls" value="xls" :disabled="purchases.length == 0 ? true:false" ><i class="fa fa-file-excel-o" aria-hidden="true" ></i> <?php echo app('translator')->getFromJson('glob.export_to_xls'); ?></button> 		
	</div>
	<div class="col-md-2 col-sm-4 " >			
		<button type ="submit" class="btn btn-danger btn-block" name="pdf" value="pdf" :disabled="purchases.length == 0 ? true:false" ><i class="fa fa-file-pdf-o" aria-hidden="true" ></i> <?php echo app('translator')->getFromJson('glob.export_to_pdf'); ?> </button>				
	</div>
	<div class="col-md-2 col-sm-4 ">			
		<button type="button" class="btn btn-primary btn-block" name="print" value="print" :disabled="purchases.length == 0 ? true:false"  v-on:click="printPage($event)"><i class="fa fa-print" aria-hidden="true" ></i> <?php echo app('translator')->getFromJson('glob.print'); ?> </button>				
	</div>
	<div class="col-md-6"></div>
</div>


<!--End Export Buttons-->

<?php echo e(Form::close()); ?>

</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>