<?php $__env->startSection('page-title'); ?><?php echo app('translator')->getFromJson('customer.customer'); ?><?php $__env->stopSection(); ?>

<?php $__env->startSection('page-subtitle'); ?><?php echo app('translator')->getFromJson('customer.add_edit_customer'); ?><?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> <?php echo app('translator')->getFromJson('glob.dashboard'); ?></a></li>
	<li class="active"><?php echo app('translator')->getFromJson('customer.customer'); ?></li>
</ol>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="customer">

	<?php if(Common::can_create('customer')): ?>

	<!-- Button div -->
	<div class="row">
		<div class="col-md-6 margin-bottom">
			<a  <?php if(isset($editCustomer)): ?> href="/common/add/Customers" <?php endif; ?> class="btn bg-purple new-customer" data-toggle="collapse" data-target="#customerFormDiv" v-on:click="addForm"><?php echo app('translator')->getFromJson('glob.add_new'); ?></a>
		</div>
		<div class="col-md-6 dropdown margin-bottom ">
			<button class="btn btn-success dropdown-toggle pull-right" type="button" data-toggle="dropdown" >Export
				<span class="caret" ></span></button>
				<ul class="dropdown-menu pull-right">
					<li><a href="<?php echo e(url('master/customers/xls?filter='.$filter.'&points='.$points.'&eligibility='.$eligibility.'&search='.$searchkey.'&type=false' )); ?>">Excel</a></li>
					<li><a href="<?php echo e(url('master/customers/pdf?filter='.$filter.'&points='.$points.'&eligibility='.$eligibility.'&search='.$searchkey.'&type=false' )); ?>">PDF</a></li>				
				</ul>				
			</div>	
		</div>
		<!-- Button div end -->

		<div class = "row" v-show="filter">
			<div class="col-md-12 margin-bottom">
				
				<?php if(!isset($editCustomer)): ?>
				<form method="get" action="<?php echo e(url('master/customer')); ?>">
					<div class="col-md-12 form-inline margin-bottom no-h-padding">
						<div class="form-group">
							<label for="">Points:</label>
							<select name="filter" class="form-control input-sm">
								<option value="=" <?php if(isset($filter) && $filter == '='): ?> <?php echo e('selected'); ?> <?php endif; ?>> =</option>
								<option value=">" <?php if(isset($filter) && $filter == '>'): ?> <?php echo e('selected'); ?> <?php endif; ?> > ></option>
								<option value=">=" <?php if(isset($filter) && $filter == '>='): ?> <?php echo e('selected'); ?> <?php endif; ?> > >= </option>
								<option value="<" <?php if(isset($filter) && $filter == '<'): ?> <?php echo e('selected'); ?> <?php endif; ?> > <</option>
								<option value="<=" <?php if(isset($filter) && $filter == '<='): ?> <?php echo e('selected'); ?> <?php endif; ?>><=</option>
							</select>	
						</div>
						<div class="form-group">
							<input type="text" name="points" class="form-control  input-sm" placeholder="<?php echo app('translator')->getFromJson('glob.points'); ?>" value="<?php echo e(Form::getValueAttribute('points', null)); ?>">
						</div>
						<div class="form-group">
							<label for="">Eligibility:</label>
							<select name="eligibility" class="form-control input-sm" >
								<option value="">All</option>
								<option value="1" <?php if(isset($eligibility) && $eligibility == '1'): ?> <?php echo e('selected'); ?> <?php endif; ?>>Eligible</option>
								<option value="0" <?php if(isset($eligibility) && $eligibility == '0'): ?> <?php echo e('selected'); ?> <?php endif; ?>>Not Eligible</option>
							</select>	
						</div>
						<div class="form-group">
							<button class="btn bg-purple btn-sm">Filter Result</button>
						</div>
					</div>
					
					<div class="pull-right form-inline">

						<label for="search">Search :</label> 
						<input type="text" name="searchkey" value="<?php echo e($searchkey); ?>" class="form-control  input-sm seatch-item" placeholder="search" >

					</div> 
					

				</form>
				<?php endif; ?>
				
			</div>
		</div>

		<div id="customerFormDiv" class="collapse <?php if( session('open') || isset($open) || isset($editCustomer) || !$errors->isEmpty()): ?> in <?php endif; ?>">
			<!-- form action for store -->

			<?php if(isset($editCustomer)): ?> 

			<?php echo e(Form::model($editCustomer, ['url'=>array('master/customer',$editCustomer->id), 'method'=>'PUT','id'=>'customerForm'])); ?>

			<?php else: ?> 

			<?php echo e(Form::open(['action'=>'CustomersController@store','id'=>'customerForm'])); ?>

			<?php endif; ?>

			<div class="row customer-box">
				<div class="col-md-12">
					<div class="box box-primary">
						<div class="box-body">

							<div class="row">
								<!-- Customer ID -->
								<div class="col-md-3">
									<label for=""><?php echo app('translator')->getFromJson('customer.customer_id'); ?>: 
									</label>
									<?php if(isset($editCustomer->id)): ?> <?php echo e($editCustomer->id); ?> 
									<?php elseif(isset($intNewId)): ?> <?php echo e($intNewId); ?>

									<?php else: ?> 1 
									<?php endif; ?>
									<span> </span>
								</div>
							</div>
							<!-- row end -->

							<div class="row">

								<!-- Title -->
								<div class="col-md-2">
									<div class="form-group">
										<label for=""><?php echo app('translator')->getFromJson('glob.title'); ?></label>
										<div class="input-group">
											<div class="input-group-addon"><i class="ion ion-person"></i></div>
											<select name="title" id="title" class="form-control input-sm select2" placeholder="<?php echo app('translator')->getFromJson('glob.title'); ?>" value="<?php if(isset($editCustomer)): ?> <?php echo e($editCustomer->title); ?> <?php endif; ?>" >
												<option value="">Title</option>
												<option value="1" <?php if(isset($editCustomer) && $editCustomer->title == 1): ?> <?php echo e('selected'); ?> <?php endif; ?>><?php echo app('translator')->getFromJson('glob.mr'); ?></option>
												<option value="2" <?php if(isset($editCustomer) && $editCustomer->title == 2): ?> <?php echo e('selected'); ?> <?php endif; ?>><?php echo app('translator')->getFromJson('glob.ms'); ?></option>
												<option value="3" <?php if(isset($editCustomer) && $editCustomer->title == 3): ?> <?php echo e('selected'); ?> <?php endif; ?>><?php echo app('translator')->getFromJson('glob.mrs'); ?></option>
											</select>
										</div>
									</div>
								</div>
								<!-- End title -->

								<!-- customer name -->
								<div class="col-md-5">
									<div class="form-group <?php echo e($errors->has('name') ? ' has-error' : ''); ?>">
										<label for=""><?php echo app('translator')->getFromJson('glob.name'); ?><span style="color:red">*</span></label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="ion ion-person"></i>
											</div>
											<input type="text" name="name" class="form-control  input-sm" placeholder="<?php echo app('translator')->getFromJson('glob.name'); ?>" value="<?php echo e(Form::getValueAttribute('name', null)); ?>" required="">
										</div>
										<?php if($errors->has('name')): ?>
										<span class="help-block">
											<strong><?php echo e($errors->first('name')); ?></strong>
										</span>
										<?php endif; ?>
									</div>
								</div>
								<!-- End Title -->

								<!-- spouse -->
								<div class="col-md-5">
									<div class="form-group">
										<label for=""><?php echo app('translator')->getFromJson('customer.spouse'); ?></label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="ion ion-ios-person"></i>
											</div>
											<input type="text" name="spouse" class="form-control  input-sm" placeholder="<?php echo app('translator')->getFromJson('customer.spouse'); ?>" value="<?php echo e(Form::getValueAttribute('spouse', null)); ?>">
										</div>
									</div>
								</div>
								<!-- end spouse -->


							</div>
							<!-- row end-->

							<div class="row">

								<!-- Gender -->
								<div class="col-md-2">
									<div class="form-group">
										<label for=""><?php echo app('translator')->getFromJson('glob.gender'); ?></label>
										<div class="input-group">
											<div class="input-group-addon"><i class="ion ion-person"></i></div>
											<select name="gender" id="gender" class="form-control input-sm select2" placeholder="<?php echo app('translator')->getFromJson('glob.gender'); ?>" value="<?php if(isset($editCustomer)): ?> <?php echo e($editCustomer->gender); ?> <?php endif; ?>" >
												<option value="0"><?php echo app('translator')->getFromJson('glob.gender'); ?></option>
												<option value="1" <?php if(isset($editCustomer) && $editCustomer->gender == 1): ?> <?php echo e('selected'); ?> <?php endif; ?>><?php echo app('translator')->getFromJson('glob.male'); ?></option>
												<option value="2" <?php if(isset($editCustomer) && $editCustomer->gender == 2): ?> <?php echo e('selected'); ?> <?php endif; ?>><?php echo app('translator')->getFromJson('glob.female'); ?></option>
											</select>
										</div>
									</div>
								</div>
								<!-- End Gender -->

								<!-- Prefered Mode -->
								<div class="col-md-2">
									<div class="form-group">
										<label for=""><?php echo app('translator')->getFromJson('glob.age_group'); ?></label>
										<div class="input-group">
											<div class="input-group-addon"><i class="ion ion-person"></i></div>
											<select name="age_group" id="age_group" class="form-control input-sm select2" placeholder="<?php echo app('translator')->getFromJson('glob.age_group'); ?>" value="<?php if(isset($editCustomer)): ?> <?php echo e($editCustomer->age_group); ?> <?php endif; ?>" >
												<option value="0"><?php echo app('translator')->getFromJson('glob.age_group'); ?></option>
												<option value="1" <?php if(isset($editCustomer) && $editCustomer->age_group == 1): ?> <?php echo e('selected'); ?> <?php endif; ?>>18 to 29</option>
												<option value="2" <?php if(isset($editCustomer) && $editCustomer->age_group == 2): ?> <?php echo e('selected'); ?> <?php endif; ?>>30 to 36</option>
												<option value="3" <?php if(isset($editCustomer) && $editCustomer->age_group == 3): ?> <?php echo e('selected'); ?> <?php endif; ?>>37 to 45</option>
												<option value="4" <?php if(isset($editCustomer) && $editCustomer->age_group == 4): ?> <?php echo e('selected'); ?> <?php endif; ?>>46 to 55</option>
												<option value="5" <?php if(isset($editCustomer) && $editCustomer->age_group == 5): ?> <?php echo e('selected'); ?> <?php endif; ?>>56+</option>
											</select>
										</div>
									</div>
								</div>
								<!-- End Prefered Mode -->

								<!-- Prefered Mode -->
								<div class="col-md-3">
									<div class="form-group">
										<label for=""><?php echo app('translator')->getFromJson('glob.prefered_mode'); ?></label>
										<div class="input-group">
											<div class="input-group-addon"><i class="ion ion-person"></i></div>
											<select name="prefered_mode" id="prefered_mode" class="form-control input-sm select2" placeholder="<?php echo app('translator')->getFromJson('glob.prefered_mode'); ?>" value="<?php if(isset($editCustomer)): ?> <?php echo e($editCustomer->prefered_mode); ?> <?php endif; ?>" >
												<option value="0"><?php echo app('translator')->getFromJson('glob.prefered_mode'); ?></option>
												<option value="1" <?php if(isset($editCustomer) && $editCustomer->prefered_mode == 1): ?> <?php echo e('selected'); ?> <?php endif; ?>><?php echo app('translator')->getFromJson('glob.email'); ?></option>
												<option value="2" <?php if(isset($editCustomer) && $editCustomer->prefered_mode == 2): ?> <?php echo e('selected'); ?> <?php endif; ?>><?php echo app('translator')->getFromJson('glob.phone'); ?></option>
												<option value="3" <?php if(isset($editCustomer) && $editCustomer->prefered_mode == 3): ?> <?php echo e('selected'); ?> <?php endif; ?>><?php echo app('translator')->getFromJson('glob.sms'); ?></option>
												<option value="4" <?php if(isset($editCustomer) && $editCustomer->prefered_mode == 4): ?> <?php echo e('selected'); ?> <?php endif; ?>><?php echo app('translator')->getFromJson('glob.whatsapp'); ?></option>
											</select>
										</div>
									</div>
								</div>
								<!-- End Prefered Mode -->

								<!-- Prefered Language -->

								<div class="col-md-3">
									<div class="form-group">
										<label for=""><?php echo app('translator')->getFromJson('glob.prefered_language'); ?></label>
										<div class="input-group">
											<div class="input-group-addon"><i class="ion ion-person"></i></div>
											<select name="prefered_language" id="prefered_language" class="form-control input-sm select2" placeholder="<?php echo app('translator')->getFromJson('glob.prefered_language'); ?>" value="<?php if(isset($editCustomer)): ?> <?php echo e($editCustomer->prefered_language); ?> <?php endif; ?>" >
												<option value="0"><?php echo app('translator')->getFromJson('glob.prefered_language'); ?></option>
												<option value="1" <?php if(isset($editCustomer) && $editCustomer->prefered_language == 1): ?> <?php echo e('selected'); ?> <?php endif; ?>><?php echo app('translator')->getFromJson('glob.english'); ?></option>
												<option value="2" <?php if(isset($editCustomer) && $editCustomer->prefered_language == 2): ?> <?php echo e('selected'); ?> <?php endif; ?>><?php echo app('translator')->getFromJson('glob.arabic'); ?></option>
												
											</select>
										</div>
									</div>
								</div>
								<!-- End of Prefered Language -->

							</div>
							<!-- row end-->

							<div class="row">

								<!-- birthday -->
								<div class="col-md-3">
									<div class="form-group">
										<label for=""><?php echo app('translator')->getFromJson('customer.birthday'); ?></label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="ion ion-calendar"></i>
											</div>
											<input type="text" name="birthday" class="form-control  input-sm datepicker" placeholder="<?php echo app('translator')->getFromJson('customer.birthday'); ?>" value="<?php if(isset($editCustomer)): ?> <?php if($editCustomer->birthday!=null || $editCustomer->birthday!=''): ?> <?php echo e($editCustomer->birthday->format('m/d/Y')); ?> <?php endif; ?> <?php endif; ?>" required="">
										</div>
									</div>
								</div>

								<!-- annivarsary -->
								<div class="col-md-3">
									<div class="form-group">
										<label for=""><?php echo app('translator')->getFromJson('customer.anniversary'); ?></label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="ion ion-calendar"></i>
											</div>
											<input type="text" name="anniversary"  class="form-control  input-sm datepicker" placeholder="<?php echo app('translator')->getFromJson('customer.anniversary'); ?>" value="<?php if(isset($editCustomer)): ?> <?php if($editCustomer->anniversary!=null || $editCustomer->anniversary!=''): ?> <?php echo e($editCustomer->anniversary->format('m/d/Y')); ?> <?php endif; ?> <?php endif; ?>">
										</div>
									</div>
								</div>

								
								<div class="col-md-3">
									<div class="form-group <?php echo e($errors->has('phone') ? ' has-error' : ''); ?>">
										<label for=""><?php echo app('translator')->getFromJson('glob.phone'); ?></label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="ion ion-ios-telephone"></i>
											</div>
											<input type="text" name="phone" class="form-control  input-sm phone" placeholder="<?php echo app('translator')->getFromJson('glob.phone'); ?>" value="<?php echo e(Form::getValueAttribute('phone', null)); ?>">
										</div>

										<?php if($errors->has('phone')): ?>
										<span class="help-block">
											<strong><?php echo e($errors->first('phone')); ?></strong>
										</span>
										<?php endif; ?>

									</div>
								</div>

								
								<div class="col-md-3">
									<div class="form-group <?php echo e($errors->has('mobile') ? ' has-error' : ''); ?>">
										<label for=""><?php echo app('translator')->getFromJson('glob.mobile'); ?> <span style="color:red">*</span></label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="ion ion-iphone"></i>
											</div>
											<input type="text" name="mobile" class="form-control  input-sm mobile" placeholder="<?php echo app('translator')->getFromJson('glob.mobile'); ?>" value="<?php echo e(Form::getValueAttribute('mobile', null)); ?>" required="">
										</div>

										<?php if($errors->has('mobile')): ?>
										<span class="help-block">
											<strong><?php echo e($errors->first('mobile')); ?></strong>
										</span>
										<?php endif; ?>

									</div>
								</div>

							</div>

							<div class="row">

								
								<div class="col-md-6">
									<div class="form-group <?php echo e($errors->has('address') ? ' has-error' : ''); ?>">
										<label for=""><?php echo app('translator')->getFromJson('glob.comment'); ?> </label>
										<textarea name="address" id=""   rows="5" class="form-control  input-sm fix-textarea" placeholder="<?php echo app('translator')->getFromJson('glob.comment'); ?>"><?php echo e(Form::getValueAttribute('address', null)); ?></textarea>
										<?php if($errors->has('address')): ?>
										<span class="help-block">
											<strong><?php echo e($errors->first('address')); ?></strong>
										</span>
										<?php endif; ?>
									</div>
								</div>

								<div class="col-md-6 no-margin no-padding">

									
									<div class="col-md-6 no-margin">
										<div class="form-group">
											<label for=""><?php echo app('translator')->getFromJson('glob.fax'); ?></label>
											<div class="input-group">
												<div class="input-group-addon">
													<i class="fa fa-fax" aria-hidden="true"></i>
												</div>
												<input type="text" name="fax" maxlength="15" class="form-control  input-sm" placeholder="<?php echo app('translator')->getFromJson('glob.fax'); ?>" value="<?php echo e(Form::getValueAttribute('fax', null)); ?>">
											</div>
										</div> 
									</div>

									
									<div class="col-md-6">
										<div class="form-group <?php echo e($errors->has('email') ? ' has-error' : ''); ?>">
											<label for=""><?php echo app('translator')->getFromJson('glob.email'); ?></label>
											<div class="input-group">
												<div class="input-group-addon">
													<i class="ion ion-at"></i>
												</div>
												<input type="email" name="email" class="form-control  input-sm" placeholder="<?php echo app('translator')->getFromJson('glob.email'); ?>" value="<?php echo e(Form::getValueAttribute('email', null)); ?>">
											</div>

											<?php if($errors->has('email')): ?>
											<span class="help-block">
												<strong><?php echo e($errors->first('email')); ?></strong>
											</span>
											<?php endif; ?>

										</div>
									</div>

									
									<div class="col-md-6">
										<div class="form-group">
											<label for=""><?php echo app('translator')->getFromJson('glob.city_state'); ?></label>
											<div class="input-group">
												<div class="input-group-addon">
													<i class="ion ion-location"></i>
												</div>
												<input type="text" name="city" class="form-control  input-sm" placeholder="<?php echo app('translator')->getFromJson('glob.city_state'); ?>" value="<?php echo e(Form::getValueAttribute('city', null)); ?>" >
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for=""><?php echo app('translator')->getFromJson('glob.nationality'); ?></label>
											<div class="input-group">
												<div class="input-group-addon">
													<i class="ion ion-location"></i>
												</div>
												<select name="nationality" class="form-control input-sm select2">
												<option value="0" >Nationality</option>
												<?php $__currentLoopData = $nationality; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<?php if(isset($editCustomer) && $editCustomer->id!=$c->id): ?>
												<option value="<?php echo e($c->id); ?>"  <?php if(isset($editCustomer) && $editCustomer->nationality==$c->id ): ?> selected  <?php endif; ?>><?php echo e($c->code2); ?></option>
												<?php elseif(!isset($editCustomer) ): ?>
												<option value="<?php echo e($c->id); ?>"  <?php if(isset($editCustomer) && $editCustomer->nationality==$c->id ): ?> selected  <?php endif; ?>><?php echo e($c->code2); ?></option>
												<?php endif; ?>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											</select>
											</div>
										</div>
									</div>
								</div>
								
							</div>
							

							<div class="row">
								<div class="col-md-3">
								<div class="form-group">
											<label for=""><?php echo app('translator')->getFromJson('glob.countryOfResidence'); ?></label>
											<div class="input-group">
												<!-- <div class="input-group-addon">
													<i class="fa fa-home"></i>
												</div> -->
												<select name="countryOfResidence" class="form-control input-sm select2">
												<option value="0" >countryOfResidence</option>
												<?php $__currentLoopData = $countryOfResidence; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<?php if(isset($editCustomer) && $editCustomer->id!=$c->id): ?>
												<option value="<?php echo e($c->id); ?>"  <?php if(isset($editCustomer) && $editCustomer->countryOfResidence==$c->id ): ?> selected  <?php endif; ?>><?php echo e($c->code2); ?></option>
												<?php elseif(!isset($editCustomer) ): ?>
												<option value="<?php echo e($c->id); ?>"  <?php if(isset($editCustomer) && $editCustomer->countryOfResidence==$c->id ): ?> selected  <?php endif; ?>><?php echo e($c->code2); ?></option>
												<?php endif; ?>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											</select>
											</div>
										</div>
								</div>
									
									<div class="col-md-3">
									<div class="form-group">
											<label for=""><?php echo app('translator')->getFromJson('glob.documentType'); ?></label>
											<div class="input-group">
												
												<input type="text" name="type" class="form-control  input-sm" placeholder="<?php echo app('translator')->getFromJson('glob.documentType'); ?>" value="<?php echo e(Form::getValueAttribute('type', null)); ?>" >
											</div>
										</div>
									</div>
									
									<div class="col-md-3">
									<div class="form-group">
											<label for=""><?php echo app('translator')->getFromJson('glob.issuedBy'); ?></label>
											<div class="input-group">
												<div class="input-group-addon">
													<i class="ion ion-location"></i>
												</div>
												<input type="text" name="issuedBy" class="form-control  input-sm" placeholder="<?php echo app('translator')->getFromJson('glob.issuedBy'); ?>" value="<?php echo e(Form::getValueAttribute('issuedBy', null)); ?>" >
											</div>
										</div>
									</div>
									<div class="col-md-3">
									<div class="form-group">
											<label for=""><?php echo app('translator')->getFromJson('glob.passportnumber'); ?></label>
											<div class="input-group">
												<div class="input-group-addon">
													<i class="ion ion-location"></i>
												</div>
												<input type="text" name="number" class="form-control  input-sm" placeholder="<?php echo app('translator')->getFromJson('glob.passportnumber'); ?>" value="<?php echo e(Form::getValueAttribute('number', null)); ?>" >
											</div>
										</div>
									</div>
							</div>
						</div>
						<!-- /.box-body -->

						<div class="box-footer">
							<div class="form-group">
								<button type="submit" class="btn btn-primary " v-on:click="checkValidateLoader($event)">
									<i class="fa fa-floppy-o"></i>
									<?php if(isset($editCustomer)): ?> <?php echo app('translator')->getFromJson('glob.update'); ?> <?php else: ?> <?php echo app('translator')->getFromJson('glob.save'); ?> <?php endif; ?>
								</button>
								<a  class="btn btn-cyan" v-on:click="resetForm"><i class="fa fa-refresh"></i> <?php echo app('translator')->getFromJson('glob.reset'); ?></a>
								<a  href="<?php echo e(url('master/customer')); ?>"  class="btn btn-danger add-customer" data-toggle="collapse" data-target="#customerFormDiv" v-on:click="cancelForm"><?php echo app('translator')->getFromJson('glob.cancel'); ?></a>
							</div>
						</div>
						<!-- box-footer -->
					</div>
					<!-- /.box -->
				</div>
				
			</div>
			

			<?php echo e(Form::close()); ?>

			<!-- end form -->
		</div>
		<!-- add/update customer div end -->

		<?php endif; ?>
		<!-- view table data  -->
		<div class="row" >
			<div class="col-md-12 table-responsive  no-search">



				<table class="table table-bordered table-striped datatable-paging-false" >

					<thead>
						<tr>
							<th style="width:2%">#</th>
							<th><?php echo app('translator')->getFromJson('glob.name'); ?></th>
							<th><?php echo app('translator')->getFromJson('glob.gender'); ?></th>
							<th><?php echo app('translator')->getFromJson('glob.age_group'); ?></th>
							<th><?php echo app('translator')->getFromJson('glob.prefered_mode'); ?></th>
							<th><?php echo app('translator')->getFromJson('glob.prefered_language'); ?></th>
							<th><?php echo app('translator')->getFromJson('glob.mobile'); ?></th>
							<th><?php echo app('translator')->getFromJson('glob.city'); ?></th>
							<th><?php echo app('translator')->getFromJson('glob.nationality'); ?></th>
							<th><?php echo app('translator')->getFromJson('glob.documentType'); ?></th>
							<th><?php echo app('translator')->getFromJson('glob.passportnumber'); ?></th>
							<th><?php echo app('translator')->getFromJson('glob.eligibility'); ?></th>
							<th><?php echo app('translator')->getFromJson('glob.points'); ?></th>

							<?php if(Common::can_update('customer') || Common::can_delete('customer')): ?>

							<th style="width:4%"><?php echo app('translator')->getFromJson('customer.actions'); ?></th>

							<?php endif; ?>

						</tr>
					</thead>

					<tbody>

						<?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

						<tr>
							<td><?php echo e($row->id); ?></td>
							<td><?php if($row->eligibility == 1): ?><i class="fa fa-shield icon-large text-green" aria-hidden="true"></i><?php endif; ?> <?php echo e($row->name); ?></td>
							<td>
								<?php if($row->gender == 1): ?> <?php echo app('translator')->getFromJson('glob.male'); ?> 
								<?php elseif($row->gender == 2): ?> <?php echo app('translator')->getFromJson('glob.female'); ?>
								<?php else: ?> N/A 
								<?php endif; ?>
							</td>
							<td>
								<?php if($row->age_group == 1): ?> 18 to 29 
								<?php elseif($row->age_group == 2): ?> 30 to 36
								<?php elseif($row->age_group == 3): ?> 37 to 45
								<?php elseif($row->age_group == 4): ?> 46 to 55 
								<?php elseif($row->age_group == 5): ?> 56+
								<?php else: ?> N/A  
								<?php endif; ?>
							</td>
							<td>
								<?php if($row->prefered_mode == 1): ?> <?php echo app('translator')->getFromJson('glob.email'); ?>
								<?php elseif($row->prefered_mode == 2): ?> <?php echo app('translator')->getFromJson('glob.phone'); ?>
								<?php elseif($row->prefered_mode == 3): ?> <?php echo app('translator')->getFromJson('glob.sms'); ?>
								<?php elseif($row->prefered_mode == 4): ?> <?php echo app('translator')->getFromJson('glob.whatsapp'); ?> 						
								<?php else: ?> N/A 		
								<?php endif; ?>
							</td>
							<td>
								<?php if($row->prefered_language == 1): ?>
								<?php echo app('translator')->getFromJson('glob.english'); ?>
								<?php elseif($row->prefered_language == 2): ?>
								<?php echo app('translator')->getFromJson('glob.arabic'); ?>
								<?php else: ?> N/A
								<?php endif; ?>
							</td>						
							<td><?php echo e($row->mobile); ?></td>
							<td><?php echo e($row->city); ?></td>
							<td><?php echo e((isset($row->countryCode->code2)) ? $row->countryCode->code2 : ''); ?></td>
							<td><?php echo e($row->type); ?></td>
							<td><?php echo e($row->number); ?></td>
							<td><?php if($row->eligibility == 0): ?> <?php echo e('Not Eligible'); ?> <?php else: ?> <?php echo e('Eligible'); ?> <?php endif; ?></td>
							<td><?php echo e(Common::fixIt($row->points)); ?></td>
							<?php if(Common::can_update('customer') ||  Common::can_delete('customer')): ?>
							<td>

								<?php if(Common::can_update('customer')): ?>

								<a href="<?php echo e(route('customer.edit',$row->id)); ?>" v-on:click="getLoader($event)" class="btn btn-xs btn-info btn-circle" ><i class="fa fa-pencil" aria-hidden="true"></i></a>

								<?php endif; ?>
								<?php if(Common::can_delete('customer')): ?>

								<a v-on:click="deleteCustomer(<?php echo e($row->id); ?>, $event)"  class="btn btn-xs btn-danger btn-circle"><i class="fa fa-trash" aria-hidden="true"></i></a> 

								<?php endif; ?>

							</td>

							<?php endif; ?>

						</tr>

						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

					</tbody>

					<tfoot class="showSearch">
						<tr>
							<th style="width:2%">#</th>
							<th class="search"><?php echo app('translator')->getFromJson('glob.name'); ?></th>
							<th class="search"><?php echo app('translator')->getFromJson('glob.gender'); ?></th>
							<th class="search"><?php echo app('translator')->getFromJson('glob.age_group'); ?></th>
							<th class="search"><?php echo app('translator')->getFromJson('glob.prefered_mode'); ?></th>
							<th class="search"><?php echo app('translator')->getFromJson('glob.prefered_language'); ?></th>
							<th class="search"><?php echo app('translator')->getFromJson('glob.mobile'); ?></th>
							<th class="search"><?php echo app('translator')->getFromJson('glob.city'); ?></th>
							<th class="search"><?php echo app('translator')->getFromJson('glob.nationality'); ?></th>
							<th class="search"><?php echo app('translator')->getFromJson('glob.documentType'); ?></th>
							<th class="search"><?php echo app('translator')->getFromJson('glob.passportnumber'); ?></th>
							<th class="search"><?php echo app('translator')->getFromJson('glob.eligibility'); ?></th>
							<th class="search"><?php echo app('translator')->getFromJson('glob.points'); ?></th>

							<?php if(Common::can_update('customer') || Common::can_delete('customer')): ?>

							<th style="width:4%"><?php echo app('translator')->getFromJson('customer.actions'); ?></th>

							<?php endif; ?>

						</tr>
					</tfoot>

				</table>

				<div class="pull-right">
					<?php echo e($customers->appends(['searchkey' => $searchkey ])->links()); ?>

				</div>

			</div>
			
		</div>
		<!-- view table data end -->

		
	</div>
	<!-- end #customer div -->
	<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>