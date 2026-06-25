<?php $__env->startSection('page-title'); ?><?php echo app('translator')->getFromJson('glob.item'); ?><?php $__env->stopSection(); ?>

<?php $__env->startSection('page-subtitle'); ?><?php echo app('translator')->getFromJson('item.add_edit_item'); ?><?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> <?php echo app('translator')->getFromJson('glob.dashboard'); ?></a></li>
	<li class="active"><?php echo app('translator')->getFromJson('glob.item'); ?></li>
</ol>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="item">

	<?php if(Common::can_create('item')): ?>

	<!-- Button div -->
	<div class="row">
		<div class="col-md-6 margin-bottom">
			<a  class="btn bg-purple new-customer" v-on:click="openPanel" ><?php echo app('translator')->getFromJson('glob.add_new'); ?></a>
			<a class="btn btn-indigo horizontal-margin" data-toggle="modal" data-target="#purchase-import" v-on:click="resetImported"> <i class="fa fa-upload" aria-hidden="true"></i> <?php echo app('translator')->getFromJson('glob.import'); ?></a>
		</div>
		<div class="col-md-6 dropdown margin-bottom ">
			<button class="btn btn-success dropdown-toggle pull-right horizontal-margin" type="button" data-toggle="dropdown" >Export
				<span class="caret" ></span></button>
				<ul class="dropdown-menu pull-right">
					<li><a href="<?php echo e(url('master/items/xls?filter='.$filter.'&vat='.$vat.'&searchkey='.$searchkey )); ?>">Excel</a></li>
					<li><a href="<?php echo e(url('master/items/pdf?filter='.$filter.'&vat='.$vat.'&searchkey='.$searchkey )); ?>">PDF</a></li>				
				</ul>				

				<a href="<?php echo e(asset('sample/import_item.xlsx')); ?>" class="btn btn-primary  horizontal-margin pull-right" download="" >
					<strong><i class="fa fa-file-excel-o" aria-hidden="true"></i> Sample</strong>
				</a>
			</div>		
		</div>
		<!-- end add button -->
      
		<div class = "row" v-show="show_filter">
			<div class="col-md-12 margin-bottom">
				
				   <?php if(Common::vatType('vat','vat_type')!=0): ?>
				<form method="get" action="<?php echo e(url('master/item')); ?>">
					<div class="col-md-12 form-inline margin-bottom no-h-padding">
						<div class="form-group">
							<label for="">VAT:</label>
							<select name="filter" class="form-control input-sm">
								<option value="=" <?php if(isset($filter) && $filter == '='): ?> <?php echo e('selected'); ?> <?php endif; ?>> =</option>
								<option value=">" <?php if(isset($filter) && $filter == '>'): ?> <?php echo e('selected'); ?> <?php endif; ?> > ></option>
								<option value=">=" <?php if(isset($filter) && $filter == '>='): ?> <?php echo e('selected'); ?> <?php endif; ?> > >= </option>
								<option value="<" <?php if(isset($filter) && $filter == '<'): ?> <?php echo e('selected'); ?> <?php endif; ?> > <</option>
								<option value="<=" <?php if(isset($filter) && $filter == '<='): ?> <?php echo e('selected'); ?> <?php endif; ?>><=</option>
							</select>	
						</div>
						<div class="form-group">
							<input type="text" name="vat" class="form-control  input-sm" placeholder="VAT" value="<?php echo e(Form::getValueAttribute('vat', null)); ?>">
						</div>
						<div class="form-group">
							<button class="btn bg-purple btn-sm">Filter Result</button>
						</div>
					</div>
				</form>
				<?php endif; ?>
				
			</div>
		</div>

		<!-- Button div end -->
		<div id="itemFormDiv" class="collapse  <?php if(isset($editItem)): ?>  in <?php endif; ?> ">

			<!-- item create Form start -->
			<?php echo $__env->make("pages.masters.items.item-create", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
			<!-- item create Form end -->

		</div>

		<?php endif; ?>
		<!-- view table data  -->
		<?php echo $__env->make("pages.masters.items.item-table", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
		<!-- view table data end -->

		<!--Export Buttons-->
		<div class="row">
			
			<div class="col-md-12">
				<?php echo $__env->make('pages.masters.items.item-import', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
			</div>
			

		</div>
		<!--End Export Buttons-->
	</div>
	<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>