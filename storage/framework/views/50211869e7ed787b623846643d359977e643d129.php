<?php $__env->startSection('page-title'); ?><?php echo app('translator')->getFromJson('menu.options'); ?><?php $__env->stopSection(); ?>

<?php $__env->startSection('page-subtitle'); ?><?php echo app('translator')->getFromJson('glob.add_edit_options'); ?><?php $__env->stopSection(); ?> 

<?php $__env->startSection('breadcrumb'); ?>

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> <?php echo app('translator')->getFromJson('glob.dashboard'); ?></a></li>
	<li class="active"><?php echo app('translator')->getFromJson('menu.options'); ?></li>
</ol>

<?php $__env->stopSection(); ?> 

<?php $__env->startSection('content'); ?> 
<div class="row">
	<div class="col-md-12">
		<div class="options">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bhoechie-tab-container">
				<!-- menu strat -->
				<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 bhoechie-tab-menu" >
					<div class="list-group">
						<!-- start general -->
						<a data-toggle="tab" href="#general" class="list-group-item active text-center tab-menu">
							<h4 class="fa fa-cogs fa-2x"></h4>
							<h6 class="title"><?php echo app('translator')->getFromJson('menu.general'); ?></h6>
						</a> 
						<!-- end general -->
						<!-- Billing button -->
						<a data-toggle="tab" href="#billing" class="list-group-item text-center tab-menu ">
							<h4 class="fa fa-credit-card fa-2x"></h4>
							<h6 class="title"><?php echo app('translator')->getFromJson('menu.billing'); ?></h6>
						</a>
						<!-- end billing -->
						<!-- Item button -->
						<a  data-toggle="tab" href="#item" class="list-group-item text-center tab-menu">
							<h4 class="icon ion-bag fa-3x"></h4><h6 class="title"><?php echo app('translator')->getFromJson('glob.item'); ?></h6>
						</a>
						<!-- end item -->

						<!--VAT -->
						<?php if(Auth::user()->id==1): ?>
						<a  data-toggle="tab" href="#vat" class="list-group-item text-center tab-menu">
							<h4 class="fa fa-file-text fa-2x"></h4><h6 class="title"><?php echo app('translator')->getFromJson('glob.vat_setting'); ?></h6>
						</a>
						<?php endif; ?>
						<!--End Vat-->
						<!--Setting-->
						<!-- <a  data-toggle="tab" href="#settings" class="list-group-item text-center tab-menu">
							<h4 class="fa fa-gear fa-2x"></h4><h6 class="title"><?php echo app('translator')->getFromJson('menu.settings'); ?></h6>
						</a> -->
						<!--End Setting-->
					</div>
				</div>
				<!-- End menu -->
				<!-- start content sction -->
				<div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 bhoechie-tab " >

					<!-- start general section -->
					<div id="general" class="bhoechie-tab-content active">
						<?php echo $__env->make("pages.options.general-options", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
					</div> 
					<!-- end general section -->

					<!-- start billing content -->
					<div id="billing" class="bhoechie-tab-content  ">
						<?php echo $__env->make("pages.options.billing-options", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
					</div>
					<!-- end billing section -->

					<!-- start item section -->
					<div id="item" class="bhoechie-tab-content">
						<?php echo $__env->make("pages.options.item-options", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
					</div>
					<!-- end item section -->
					<?php if(Auth::user()->id==1): ?>
					<div id="vat" class="bhoechie-tab-content">
						<?php echo $__env->make("pages.options.vat-options", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
					</div>
					<?php endif; ?>
					
					<!--start setting section -->
					
					<!--End setting section -->
				</div>
				<!-- end content section -->
			</div>

		</div>
	</div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>