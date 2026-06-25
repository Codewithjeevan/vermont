<?php $__env->startSection('page-title'); ?><?php echo app('translator')->getFromJson('glob.user'); ?><?php $__env->stopSection(); ?>

<?php $__env->startSection('page-subtitle'); ?><?php echo app('translator')->getFromJson('user.add_edit_user'); ?><?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> <?php echo app('translator')->getFromJson('glob.dashboard'); ?></a></li>
	<li class="active"><?php echo app('translator')->getFromJson('glob.user'); ?></li>
</ol>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<div id="users">

	<?php if(Common::can_create('user')): ?> 

	<!-- Button div -->
	<div class="row">
		<div class="col-md-6 margin-bottom">
			<a <?php if(isset($editUser)): ?> href="/common/add/Users" <?php endif; ?> class="btn bg-purple new-customer" data-toggle="collapse" data-target="#userFormDiv" v-on:click="addForm"><?php echo app('translator')->getFromJson('glob.add_new'); ?></a>
		</div>
		<div class="col-md-6 dropdown margin-bottom ">
			<button class="btn btn-success dropdown-toggle pull-right" type="button" data-toggle="dropdown" >Export
				<span class="caret" ></span></button>
				<ul class="dropdown-menu pull-right">
					<li><a href="<?php echo e(url('master/users/xls' )); ?>">Excel</a></li>
					<li><a href="<?php echo e(url('master/users/pdf' )); ?>">PDF</a></li>				
				</ul>				
			</div>	
		</div>
		<!-- Button div end -->

		<!-- Button div -->	
		<div id="userFormDiv" class="collapse <?php if(session('open') || isset($open) || isset($editUser) || !$errors->isEmpty()): ?> in <?php endif; ?>">

			<?php if(isset($editUser)): ?> 
			<?php echo e(Form::model($editUser, ['url'=>array('master/user',$editUser->id), 'method'=>'PUT','id'=>'userForm'])); ?>


			<?php else: ?>
			<?php echo e(Form::open(['action'=>'UsersController@store','id'=>'userForm'])); ?>

			<!-- form action for store -->
			<?php endif; ?>
			<div class="row customer-box">
				<div class="col-md-12">
					<div class="box box-primary">
						<div class="box-body">
							<div class="row">

								<!-- user name -->
								<div class="col-md-4">
									<div class="form-group <?php echo e($errors->has('email') ? ' has-error' : ''); ?>">
										<?php echo Form::label('email', trans('user.user_name')); ?><span style="color:red">*</span>

										<div class="input-group">
											<div class="input-group-addon"> <i class="ion ion-person"></i> </div>
											<?php echo Form::text('email',null,array('required','class' => 'form-control input-sm', 'placeholder' => trans('user.user_name'))); ?>

										</div>

										<?php if($errors->has('email')): ?>
										<span class="help-block">
											<strong><?php echo e($errors->first('email')); ?></strong>
										</span>
										<?php endif; ?>

									</div>
								</div>
								<!--End user name -->

							</div>
							<!-- .row -->


							<div class="row">

								<!--  password -->
								<div class="col-md-3">
									<div class="form-group <?php echo e($errors->has('password') ? ' has-error' : ''); ?>">
										<?php echo Form::label('password', trans('glob.password')); ?><span style="color:red">*</span>

										<div class="input-group">
											<div class="input-group-addon"> <i class="ion-android-lock"></i> </div>
											<?php echo e(Form::password('password', array('required', 'class' => 'form-control input-sm', 'placeholder'=>trans('glob.password')))); ?>

										</div>

										<?php if($errors->has('password')): ?>
										<span class="help-block">
											<strong><?php echo e($errors->first('password')); ?></strong>
										</span>
										<?php endif; ?>

									</div>
								</div>
								<!--End password -->


								<!--  confirm password -->
								<div class="col-md-3">
									<div class="form-group">
										<?php echo Form::label('password-confirm', trans('glob.conf_password')); ?><span style="color:red">*</span>

										<div class="input-group">
											<div class="input-group-addon"> <i class="ion-android-lock"></i> </div>
											<?php echo e(Form::password('password_confirmation', array('required', 'class' => 'form-control input-sm', 'placeholder'=>trans('glob.conf_password')))); ?>

										</div>

									</div>
								</div>
								<!--End confirm password -->


							</div>
							<!-- .row --> 


							<div class="row">

								<!-- Assign Role -->
								<div class="col-md-3">
									<div class="form-group">
										<?php echo Form::label('title', trans('user.assign_role')); ?>


										<span style="color:red">*</span>
										<div class="input-group">
											<div class="input-group-addon"> <i class="fa fa-user-plus"></i> </div>
											<select name="roleId" class="form-control input-sm select2" required>
												<option value="">Select Role</option>
												<?php $__currentLoopData = $roleList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<option value="<?php echo e($role->id); ?>" <?php if(isset($editUser) && $editUser->roleId== $role->id): ?> selected  <?php endif; ?>><?php echo e($role->role); ?></option>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											</select>
											
										</div>

									</div>
								</div>
								<!--End Assign Role -->

								<!-- Assign floor -->
								<div class="col-md-3">
									<div class="form-group">
										<?php echo Form::label('title', trans('user.assign_floor')); ?>

										
										<div class="input-group">
											<div class="input-group-addon"> <i class="fa fa-list-ol"></i></div>
											<select name="floor" class="form-control input-sm select2" >
												<?php $__currentLoopData = $floor; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<option value="<?php echo e($f); ?>"  <?php if(isset($editUser) && $editUser->floor==$f ): ?> selected  <?php endif; ?>><?php echo e($f); ?></option>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											</select>

											
										</div>

									</div>
								</div>
								<!--End Assign floor -->

							</div>
							<!-- .row -->
						</div>
						<!-- .box-body -->


						<div class="box-footer">
							<div class="form-group"> 

								
								<button type="submit" class="btn btn-primary" v-on:click="checkValidateLoader($event)">
									<i class="fa fa-floppy-o"></i>
									<?php if(isset($editUser)): ?> <?php echo app('translator')->getFromJson('glob.update'); ?> <?php else: ?> <?php echo app('translator')->getFromJson('glob.save'); ?> <?php endif; ?>
								</button>

								
								<a  class="btn btn-cyan" v-on:click="resetForm"><i class="fa fa-refresh"></i> <?php echo app('translator')->getFromJson('glob.reset'); ?></a>

								
								<a  <?php if(isset($editUser)): ?> href="<?php echo e(url('master/user')); ?>" <?php endif; ?> class="btn btn-danger add-customer" data-toggle="collapse" data-target="#userFormDiv" v-on:click="cancelForm"> <?php echo app('translator')->getFromJson('glob.cancel'); ?> </a>

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
				<table class="table table-bordered table-striped datatable-order" data-sort-order="desc" data-sort-col="0">
					<thead>
						<tr>
							<th style="width:10%">#</th>
							<th><?php echo app('translator')->getFromJson('user.user_name'); ?></th>
							<th><?php echo app('translator')->getFromJson('user.assign_role'); ?></th>
							<th><?php echo app('translator')->getFromJson('user.assign_floor'); ?></th>
							<th><?php echo app('translator')->getFromJson('glob.created_date'); ?></th>

							<?php if(Common::can_update('user') || Common::can_delete('user')): ?>

							<th style="width:10%"><?php echo app('translator')->getFromJson('customer.actions'); ?></th>

							<?php endif; ?>

						</tr>
					</thead>
					<tbody>

						<?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<tr>
							<td><?php echo e($user->id); ?></td>
							<td><?php echo e($user->email); ?></td>
							<td> <?php if(isset($user->role->role)): ?> <?php echo e($user->role->role); ?> <?php endif; ?></td>
							<td><?php echo e($user->floor); ?></td>
							<td><?php echo e($user->created_at->format('jS F, Y')); ?> <br> <small><em> <?php echo app('translator')->getFromJson("glob.updated_at"); ?> <?php echo e($user->updated_at->diffForHumans()); ?> </em> </small></td>

							<?php if(Common::can_update('user') || Common::can_delete('user')): ?>

							<td>

								<?php if(Common::can_update('user')): ?>

								<a  href="<?php echo e(url('/master/user/'.$user->id.'/edit')); ?>" v-on:click="getLoader($event)"  class="btn btn-xs btn-info btn-circle" ><i class="fa fa-pencil" aria-hidden="true"></i></a>

								<?php endif; ?>
								<?php if(Common::can_delete('user')): ?>

								<a  v-on:click="deleteUser(<?php echo e($user->id); ?>, $event)" class="btn btn-xs btn-danger btn-circle"><i class="fa fa-trash" aria-hidden="true"></i></a> 

								<?php endif; ?>

							</td>

							<?php endif; ?>

						</tr>

						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

					</tbody>
					<tfoot class="showSearch">
						<tr>
							<th style="width:10%">#</th>
							<th class="search"><?php echo app('translator')->getFromJson('user.user_name'); ?></th>
							<th class="search"><?php echo app('translator')->getFromJson('user.assign_role'); ?></th>
							<th class="search"><?php echo app('translator')->getFromJson('user.assign_floor'); ?></th>
							<th><?php echo app('translator')->getFromJson('glob.created_date'); ?></th>

							<?php if(Common::can_update('user') || Common::can_delete('user')): ?>

							<th style="width:10%"><?php echo app('translator')->getFromJson('customer.actions'); ?></th>

							<?php endif; ?>

						</tr>
					</tfoot>
				</table>
			</div>
		</div>
		<!-- view table data end -->

		

	</div>

	<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>