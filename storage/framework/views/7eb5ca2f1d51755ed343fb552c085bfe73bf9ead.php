<?php $__env->startSection('page-title'); ?><?php echo app('translator')->getFromJson('glob.location'); ?><?php $__env->stopSection(); ?>

<?php $__env->startSection('page-subtitle'); ?><?php echo app('translator')->getFromJson('glob.add_edit_location'); ?><?php $__env->stopSection(); ?> 

<?php $__env->startSection('breadcrumb'); ?>

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-home"></i> <?php echo app('translator')->getFromJson('glob.home'); ?></a></li>
	<li><a href="#"><i class="fa fa-user"></i> <?php echo app('translator')->getFromJson('menu.masters'); ?></a></li>
	<li class="active"><?php echo app('translator')->getFromJson('glob.location'); ?></li>
</ol>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="location"> 

	<?php if(Common::can_create('location')): ?>

	<!-- Button div -->
	<div class="row">
		<div class="col-md-6 margin-bottom">
			<a <?php if(isset($editLocation)): ?> href="/common/add/Locations" <?php endif; ?> class="btn bg-purple new-customer" data-toggle="collapse" data-target="#openlocationFormDiv" v-on:click="addForm"><?php echo app('translator')->getFromJson('glob.add_new'); ?></a>
		</div>
		<div class="col-md-6 dropdown margin-bottom ">
			<button class="btn btn-success dropdown-toggle pull-right" type="button" data-toggle="dropdown" >Export
				<span class="caret" ></span></button>
				<ul class="dropdown-menu pull-right">
					<li><a href="<?php echo e(url('master/locations/xls' )); ?>">Excel</a></li>
					<li><a href="<?php echo e(url('master/locations/pdf' )); ?>">PDF</a></li>				
				</ul>				
			</div>	
		</div>
		<!-- Button div end -->


		<div id="openlocationFormDiv" class="collapse <?php if( session('open') || isset($open) || isset($editLocation) || !$errors->isEmpty()): ?> in <?php endif; ?>">


			<?php if(isset($editLocation)): ?> 

			<?php echo e(Form::model($editLocation, ['url'=>array('master/location',$editLocation->id), 'method'=>'PUT','id'=>'locationForm'])); ?>

			<?php else: ?> 

			<?php echo e(Form::open(['action'=>'LocationsController@store','id'=>'locationForm'])); ?>

			<?php endif; ?>

			<div class="row customer-box"> 

				<div class="col-md-12">

					<div class="box box-primary"> 

						<div class="box-body">

							<div class="row">

								<!-- Location Id -->
								<div class="col-md-3"> 
									<div class="form-group">
										<label for=""><?php echo app('translator')->getFromJson('glob.location_id'); ?></label> :
										<?php if(isset($editLocation->id)): ?> <?php echo e($editLocation->id); ?> 
										<?php elseif(isset($newId)): ?> <?php echo e($newId); ?>

										<?php else: ?> 1 
										<?php endif; ?>
										<span> </span>
									</div>
								</div>
								<!-- End Location Id -->

							</div>

							<div class="row">						

								<!-- Location -->
								<div class="col-md-4"> 
									<div class="form-group">
										<label for=""><?php echo app('translator')->getFromJson('glob.location'); ?><span style="color:red">*</span></label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="ion ion-location"></i>
											</div>
											<input type="text" name="location" class="form-control  input-sm" placeholder="<?php echo app('translator')->getFromJson('glob.location'); ?>" value="<?php echo e(Form::getValueAttribute('location', null)); ?>" required="">
										</div>
									</div>
								</div>
								<!-- End Location -->

								<!-- Name -->
								<div class="col-md-4"> 
									<div class="form-group">
										<label for=""><?php echo app('translator')->getFromJson('glob.name'); ?><span style="color:red">*</span></label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="ion ion-person"></i>
											</div>
											<input type="text" name="name" class="form-control  input-sm" placeholder="<?php echo app('translator')->getFromJson('glob.name'); ?>" value="<?php echo e(Form::getValueAttribute('name', null)); ?>" required="">
										</div>
									</div>
								</div>
								<!-- End Name -->	

								<!--City/State-->
								<div class="col-md-4">
									<div class="form-group">
										<label for=""><?php echo app('translator')->getFromJson('glob.city_state'); ?><span style="color:red">*</span></label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="ion ion-location"></i>
											</div>
											<input type="text" name="city" class="form-control  input-sm" placeholder="<?php echo app('translator')->getFromJson('glob.city_state'); ?>" value="<?php echo e(Form::getValueAttribute('city', null)); ?>" required="">
										</div>
									</div>
								</div>	
								<!-- End City/State-->	

							</div>
							<!-- .row -->

							<!-- .row -->
							<div class="row">

								<!-- phone -->
								<div class="col-md-3">
									<div class="form-group <?php echo e($errors->has('phone') ? ' has-error' : ''); ?>">
										<label for=""><?php echo app('translator')->getFromJson('glob.phone'); ?><span style="color:red">*</span></label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="ion ion-ios-telephone"></i>
											</div>
											<input type="text" name="phone" class="form-control  input-sm phone" placeholder="<?php echo app('translator')->getFromJson('glob.phone'); ?>" value="<?php echo e(Form::getValueAttribute('phone', null)); ?>" required="">
										</div>

										<?php if($errors->has('phone')): ?>
										<span class="help-block">
											<strong><?php echo e($errors->first('phone')); ?></strong>
										</span>
										<?php endif; ?>

									</div>
								</div>
								<!--End phone -->

								<!-- mobile -->
								<div class="col-md-3">
									<div class="form-group <?php echo e($errors->has('mobile') ? ' has-error' : ''); ?>">
										<label for=""><?php echo app('translator')->getFromJson('glob.mobile'); ?><span style="color:red">*</span></label>
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
								<!--End mobile -->


								<!-- email -->
								<div class="col-md-3">
									<div class="form-group <?php echo e($errors->has('email') ? ' has-error' : ''); ?>">
										<label for=""><?php echo app('translator')->getFromJson('glob.email'); ?><span style="color:red">*</span></label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="ion ion-at"></i>
											</div>
											<input type="email" name="email" class="form-control  input-sm" placeholder="<?php echo app('translator')->getFromJson('glob.email'); ?>" value="<?php echo e(Form::getValueAttribute('email', null)); ?>" required="">
										</div>

										<?php if($errors->has('email')): ?>
										<span class="help-block">
											<strong><?php echo e($errors->first('email')); ?></strong>
										</span>
										<?php endif; ?>

									</div>
								</div>
								<!--End email -->

								<!-- Tin No. -->
								<div class="col-md-3">
									<div class="form-group">
										<label for=""><?php echo app('translator')->getFromJson('supplier.tin_no'); ?><span style="color:red">*</span></label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="icon ion-ios-pricetags-outline" aria-hidden="true"></i>
											</div>
											<input type="text" name="tin" class="form-control  input-sm" placeholder="<?php echo app('translator')->getFromJson('supplier.tin_no'); ?>" maxlength="20"  value="<?php echo e(Form::getValueAttribute('tin', null)); ?>" required="">
										</div>
									</div>
								</div>
								<!--End Tin No. -->

							</div>						
							<!-- .row -->



							<div class="row">

								<!-- address -->
								<div class="col-md-6">
									<div class="form-group <?php echo e($errors->has('address') ? ' has-error' : ''); ?>">
										<label for=""><?php echo app('translator')->getFromJson('glob.address'); ?> <span style="color:red">*</span></label>
										<textarea name="address" id=""   rows="4" class="form-control  input-sm fix-textarea" placeholder="<?php echo app('translator')->getFromJson('glob.address'); ?>" required=""><?php echo e(Form::getValueAttribute('address', null)); ?></textarea>						
										<?php if($errors->has('address')): ?>
										<span class="help-block">
											<strong><?php echo e($errors->first('address')); ?></strong>
										</span>
										<?php endif; ?>
									</div>
								</div>
								<!--End address -->

								<!-- Terms -->
								<div class="col-md-6">
									<div class="form-group <?php echo e($errors->has('terms') ? ' has-error' : ''); ?>">									
										<label for=""><?php echo app('translator')->getFromJson('glob.terms'); ?> <span style="color:red">*</span></label>
										<textarea name="terms" id=""   rows="4" class="form-control  input-sm fix-textarea" placeholder="<?php echo app('translator')->getFromJson('glob.terms'); ?>" required=""><?php echo e(Form::getValueAttribute('terms', null)); ?></textarea>	
										<?php if($errors->has('terms')): ?>
										<span class="help-block">
											<strong><?php echo e($errors->first('terms')); ?></strong>
										</span>
										<?php endif; ?>
									</div>
								</div>
								<!--End Terms -->


							</div>
							<!-- row ends-->

						</div>
						<!-- .box-body -->
						<div class="box-footer">
							<div class="form-group">
								<button type="submit" class="btn btn-primary " v-on:click="checkValidateLoader($event)"><i class="fa fa-floppy-o"></i>
									<?php if(isset($editLocation)): ?> <?php echo app('translator')->getFromJson('glob.update'); ?> <?php else: ?> <?php echo app('translator')->getFromJson('glob.save'); ?> <?php endif; ?>
								</button>
								<a  class="btn btn-cyan" v-on:click="resetForm"><i class="fa fa-refresh"></i> <?php echo app('translator')->getFromJson('glob.reset'); ?></a>
								<a  class="btn btn-danger add-customer" data-toggle="collapse" data-target="#openlocationFormDiv" v-on:click="cancelForm"><?php echo app('translator')->getFromJson('glob.cancel'); ?></a>
							</div>
						</div>
						<!-- box-footer -->

					</div>
					<!-- .box .box-primary -->

				</div>
				<!-- .col-md-12 -->

			</div>
			<!--end .row .customer-box -->

			<?php echo e(Form::close()); ?>

			<!-- end form -->
		</div>


		<?php endif; ?>
		<!-- view table data  -->
		<div class="row">

			<div class="col-md-12 table-responsive">

				<table class="table table-bordered table-striped datatable1">
					<thead>
						<tr>
							<th style="width:10%">#</th>						
							<th><?php echo app('translator')->getFromJson('glob.location'); ?></th> 
							<th><?php echo app('translator')->getFromJson('glob.name'); ?></th>
							<th><?php echo app('translator')->getFromJson('supplier.tin_no'); ?></th>	


							<?php if(Common::can_update('location') || Common::can_delete('location')): ?>

							<th style="width:4%"><?php echo app('translator')->getFromJson('customer.actions'); ?></th>

							<?php endif; ?>


						</tr>
					</thead>
					<tbody>

						<?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

						<tr>
							<td><?php echo e($row->id); ?></td>						
							<td><?php echo e($row->location); ?></td>
							<td><?php echo e($row->name); ?></td>						
							<td><?php echo e($row->tin); ?></td>

							<?php if(Common::can_update('location') ||  Common::can_delete('location')): ?>

							<td>

								<?php if(Common::can_update('location')): ?>

								<a href="<?php echo e(route('location.edit',$row->id)); ?>" v-on:click="getLoader($event)" class="btn btn-xs btn-info btn-circle" ><i class="fa fa-pencil" aria-hidden="true"></i></a>

								<?php endif; ?>
								<?php if(Common::can_delete('location')): ?>

								<a v-on:click="deleteLocation(<?php echo e($row->id); ?>, $event)"  class="btn btn-xs btn-danger btn-circle"><i class="fa fa-trash" aria-hidden="true"></i></a> 

								<?php endif; ?>

							</td>

							<?php endif; ?>

						</tr>

						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


					</tbody>
					<tfoot class="showSearch">
						<tr>

							<th style="width:10%">#</th>					
							<th><?php echo app('translator')->getFromJson('glob.location'); ?></th> 
							<th><?php echo app('translator')->getFromJson('glob.name'); ?></th>
							<th><?php echo app('translator')->getFromJson('supplier.tin_no'); ?></th>	

							<?php if(Common::can_update('location') || Common::can_delete('location')): ?>

							<th style="width:4%"><?php echo app('translator')->getFromJson('customer.actions'); ?></th>

							<?php endif; ?>


						</tr>
					</tfoot>
				</table>

			</div>
		</div>
	
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>