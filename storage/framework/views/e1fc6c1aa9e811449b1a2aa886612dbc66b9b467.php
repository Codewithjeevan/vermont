<style type="text/css">
	.content-header{
		display: none !important;
	}
	.content{
		padding-top:10px !important;
	}
	
</style>
<?php $__env->startSection('content'); ?>
<div id="billingVueId" class="billingCls" v-cloak>
	<div class="row">

		<!-- Left section -->
		<div class="col-md-9 col-sm-9 less-padding-right less-padding-left">
			
			<?php echo $__env->make("pages.billing.billing-create-plain", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
			
			
			

			<div class="box-body less-padding-right less-padding-left control-panel">
				<div class="col-md-6 col-sm-6 no-h-padding btn-toolbar"  role="toolbar" aria-label="Toolbar with button groups">
					<?php if(Common::can_option_view('billing','search_items')): ?>
					<button class="btn btn-bg btn-primary search-btn" data-toggle="modal" data-target="#searchModal" v-if="bill_return==false"  data-backdrop="static" data-keyboard="false" :disabled="(home_delivery_check ||(billing.tax_refund_flag==1 && updateBill) ? true :false)">Search Items</button>
					<button class="btn btn-bg btn-primary return-btn return-search" data-toggle="modal" data-target="#searchModal"  data-backdrop="static" data-keyboard="false" v-if="bill_return==true"  :disabled="((return_bill_no!=='' || !show_return_search ) ? true :false)">Return Items</button>
					<?php endif; ?>
					&nbsp; &nbsp;
					<?php if(Common::can_option_view('billing','retail_invoice')): ?>
					<label for="" class="btn bg-indigo radio-place bill-btn">
						<input type="radio" v-model="billing.invoice_type" value="1" name="invoice_type" :disabled="(bill_return || home_delivery_check? true :false)">  
						<?php echo app('translator')->getFromJson('billing.retail_invoice'); ?>
					</label>

					
					<label for="" class="btn bg-indigo radio-place bill-btn">
						<input type="radio" v-model="billing.invoice_type" value="2" name="invoice_type" :disabled="(bill_return || home_delivery_check ? true :false)"> <?php echo app('translator')->getFromJson('billing.wholesale_invoice'); ?>  
					</label>
					<?php endif; ?>
					
				</div>

				<div class="col-md-6 col-sm-6 no-h-padding ">
					<?php echo $__env->make("pages.billing.billing-controls", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
				</div>
			</div>

			
			

			
			<?php echo $__env->make("pages.billing.billing-item-list", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
			
		</div>
		<div class="col-md-3 col-sm-3 less-padding-right less-padding-left">
			<!-- right sidebar -->
			<?php echo $__env->make("pages.billing.billing-sidebar", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
			
		</div>
	</div>    

	<?php echo $__env->make("pages.billing.billing-search-item", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
	<?php echo $__env->make("pages.billing.client-buying-history", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
	<?php echo $__env->make("pages.billing.xReport", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
	<?php echo $__env->make("pages.billing.zReport", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
	<?php echo $__env->make("pages.billing.zQtyReport", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>	
	<?php echo $__env->make("pages.billing.confirm-modal", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
	<?php echo $__env->make("pages.billing.sms-billing-modal", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>