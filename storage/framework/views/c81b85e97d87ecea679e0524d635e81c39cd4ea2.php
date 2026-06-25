<div id="confirmModal" class="modal fade" role="dialog">
	<div class="modal-dialog" style="width:30%; margin-top: 14%">
		<!-- Modal content-->
		<div class="modal-content">
			
			<div class="modal-header bg-purple">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title"> <?php echo app('translator')->getFromJson("glob.enter_password"); ?></h4>
			</div>
			<div class="modal-body">	
			<form v-on:submit.prevent="verify($event)">		
				<div class="row">
					<div class="col-md-6">						
						<div class="form-group">
							<label for=""><?php echo app('translator')->getFromJson('glob.password'); ?></label>
							<div class="input-group col-md-12" >
								<input type="password" name="password"  id="password" v-model="confirmPass" class="form-control input-sm"  placeholder="<?php echo app('translator')->getFromJson('glob.password'); ?>" value="" required="">
							</div>
							<span v-if="password == 'false'" style="color:red"> <?php echo app('translator')->getFromJson('glob.please_enter_valid_password'); ?></span>
						</div>
					</div>
					<div class="col-md-6" style="margin-top: 5%;">	
						<div class="row pull-right" style="margin-right: 1%;">					
							<button id="confirm-model-btn" class="btn btn-success" :disabled="confirmPass== '' ? true:false" ><?php echo app('translator')->getFromJson('glob.submit'); ?></button>
							<a class="btn btn-danger" data-toggle="modal" data-target="#confirmModal"> <?php echo app('translator')->getFromJson('glob.close'); ?></a>
						</div>	
					</div>
				</div>
			</form>	
			</div>
			

		</div>

	</div>
</div>



