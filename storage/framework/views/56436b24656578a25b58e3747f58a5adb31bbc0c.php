<div id="confirmBilltypeModal" class="modal fade" role="dialog">
	<div class="modal-dialog" style="width:35%; margin-top: 14%">
		<!-- Modal content-->
		<div class="modal-content">
			
			<div class="modal-header bg-purple">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title"> <?php echo app('translator')->getFromJson("billing.billing_type_confirm"); ?></h4>
			</div>
			<div class="modal-body">	
			<div class="row">
					<div class="col-md-12">	
						<div class="row" style="margin-left: 5%;">					
						<a class="btn btn-primary" v-on:click="confirmBillingConfirm(0)"> <?php echo app('translator')->getFromJson('billing.normal_invoice'); ?></a>
							<a class="btn btn-primary" v-on:click="confirmBillingConfirm(1)"> <?php echo app('translator')->getFromJson('billing.tax_refund_invoice'); ?></a>
						</div>
					</div>
				
				</div>
			
			</div>
			

		</div>

	</div>
</div>