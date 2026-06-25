<div id="confirmBillingModal" class="modal fade" role="dialog">
	<div class="modal-dialog" style="width:25%; margin-top: 14%">
		<!-- Modal content-->
		<div class="modal-content">
			
			<div class="modal-header bg-purple">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Do You Wan't Continue Billing</h4>
			</div>
			<div class="modal-body">	
			<div class="row">
					<div class="col-md-12">	
						<div class="row" style="margin-left: 25%;">					
						<a id="confirm-no" class="btn btn-primary" v-on:click="confirmBilling(0)">No</a>
							<a id="confirm-yes" class="btn btn-primary" :disabled="disabled" v-on:click="confirmBilling(1)">Yes</a>
						</div>
					</div>
				
				</div>
			
			</div>
			

		</div>

	</div>
</div>