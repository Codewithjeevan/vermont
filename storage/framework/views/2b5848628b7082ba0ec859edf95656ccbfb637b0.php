<div id="smsBillingModal" class="modal fade" role="dialog">
	<div class="modal-dialog" style="width:30%; margin-top: 14%">
		<!-- Modal content-->
		<div class="modal-content">
			
			<div class="modal-header bg-purple">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title"> <?php echo app('translator')->getFromJson("glob.sms_billing"); ?></h4>
			</div>
			<div class="modal-body">	
				
				<div class="row">
					<div class="col-md-6">						
						<div class="form-group">
							<label id=""><?php echo app('translator')->getFromJson('glob.do_you_want_sms_billing'); ?></label>							
						</div>
					</div>
					<div class="col-md-6" style="margin-top: 5%;">	
						<div class="row pull-right" style="margin-right: 1%;">					
							<button type ="button" id="yes" class="btn btn-success" ><?php echo app('translator')->getFromJson('glob.yes'); ?></button>
							<button type ="button" id="no" class="btn btn-danger" > <?php echo app('translator')->getFromJson('glob.no'); ?></button>
						</div>	
					</div>
				</div>
			
			</div>
			
		</div>

	</div>
</div>
