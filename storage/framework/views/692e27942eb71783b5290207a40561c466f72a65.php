<?php $__env->startSection('page-title'); ?><?php echo app('translator')->getFromJson('menu.opening_cash'); ?><?php $__env->stopSection(); ?>

<?php $__env->startSection('page-subtitle'); ?><?php echo app('translator')->getFromJson('glob.add_edit_opning_cash'); ?><?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> <?php echo app('translator')->getFromJson('glob.dashboard'); ?></a></li>
	<li class="active"><?php echo app('translator')->getFromJson('menu.opening_cash'); ?></li>
</ol>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="opening_cash"> 
	<?php if(Common::can_create('opening_cash')): ?>
	

	<?php if(isset($editOpenCash)): ?> 
	<?php echo e(Form::model($editOpenCash, ['url'=>array('opening_cash/opening_cash',$editOpenCash->id),'method'=>'PUT','id'=>'opencashForm'])); ?>

	<?php else: ?> 
	<?php echo e(Form::open(['action'=>'OpeningCashController@store','id'=>'opencashForm'])); ?>

	<?php endif; ?>

	<div class="row customer-box"> 

		<div class="col-md-12">

			<div class="box box-primary"> 

				<div class="box-body">

					<div class="row">

						<!-- open cash date -->
						<div class="col-md-3">								
							<div class="form-group <?php echo e($errors->has('open_cash_date') ? ' has-error' : ''); ?>">
								<label for=""><?php echo app('translator')->getFromJson('glob.opening_cash_date'); ?><span style="color:red">*</span></label>
								<div class="input-group">
									<div class="input-group-addon">
										<i class="ion ion-calendar"></i>
									</div>
									<input id="open_cash_date" name="open_cash_date" type="text" class="form-control  input-sm datepicker required" value="<?php if(isset($editOpenCash)): ?> <?php echo e($editOpenCash->open_cash_date); ?>  <?php endif; ?>"   placeholder="<?php echo app('translator')->getFromJson('glob.opening_cash_date'); ?>" required="">
								</div>
								<?php if($errors->has('open_cash_date')): ?>
								<span class="help-block">
									<strong><?php echo e($errors->first('open_cash_date')); ?></strong>
								</span>
								<?php endif; ?>
							</div>
						</div>
						<!--End open cash date -->

						<!--  Opening cash Amt -->
						<div class="col-md-3">
							<div class="form-group <?php echo e($errors->has('opening_amt') ? ' has-error' : ''); ?>">
								<label for=""><?php echo app('translator')->getFromJson('glob.opening_amt'); ?><span style="color:red">*</span></label>
								<div class="input-group">
									<div class="input-group-addon">
										<i class="fa fa-money"></i>
									</div>

									<input type="text" id="opening_amt" name="opening_amt"  class="form-control  input-sm " value="<?php if(isset($editOpenCash)): ?><?php echo e($editOpenCash->opening_amt); ?><?php endif; ?>"  required="">
								</div>
								<?php if($errors->has('opening_amt')): ?>
								<span class="help-block">
									<strong><?php echo e($errors->first('opening_amt')); ?></strong>
								</span>
								<?php endif; ?>
							</div>
						</div>
						<!--End Opening cash Amt -->
						<!--User Name-->
						<div class="col-md-3">
							<label ><?php echo app('translator')->getFromJson('glob.name'); ?><span style="color:red">*</span></label>
							<div class="input-group">
								<div class="input-group-addon"><i class="ion ion-person"></i></div>
								<select name="user_id" id="user_id" class="form-control input-sm select2"  required>
									<option value=""></option>
									<?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>										
									<option value="<?php echo e($row->id); ?>" <?php if(isset($editOpenCash) &&  $editOpenCash->user_id ==$row->id ): ?> <?php echo e('selected'); ?>  <?php endif; ?> ><?php echo e($row->email); ?></option>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</select>
							</div>
						</div>
						<!--End User Name-->

						<div class="col-md-3" style="margin-top: 2%;">
							<center>
								<a  type="button" class="btn btn-cyan"   v-on:click="reset"  ><i class="fa fa-refresh"></i> <?php echo app('translator')->getFromJson('glob.reset'); ?></a>
								<button type="submit" class="btn btn-primary " v-on:click="checkValidateLoader($event)"><i class="fa fa-floppy-o"></i><?php if(isset($editOpenCash)): ?> <?php echo app('translator')->getFromJson('glob.update'); ?> <?php else: ?> <?php echo app('translator')->getFromJson('glob.save'); ?> <?php endif; ?>	</button>
								<a type="button" class="btn btn-danger" <?php if(isset($editOpenCash)): ?> href="/opening_cash/opening_cash" <?php else: ?> v-on:click="reset" <?php endif; ?> > <?php echo app('translator')->getFromJson('glob.cancel'); ?></a>
							</center>
						</div>

					</div>


				</div>				

			</div>
			<!-- .box .box-primary -->

		</div>
		<!-- .col-md-12 -->

	</div>
	<!--end .row .customer-box -->

	<?php echo e(Form::close()); ?>


	<!-- end form -->	
	

	<?php endif; ?>

	<!-- view table data  -->
	<div class="row">

		<div class="col-md-12 table-responsive">

			<table class="table table-bordered table-striped datatable1">
				<thead>
					<tr>
						<th style="width:10%">#</th>				
						<th><?php echo app('translator')->getFromJson('glob.name'); ?></th>
						<th><?php echo app('translator')->getFromJson('glob.opening_amt'); ?></th> 
						<th><?php echo app('translator')->getFromJson('glob.opening_cash_date'); ?></th>
						
						<?php if(Common::can_update('opening_cash') || Common::can_delete('opening_cash')): ?>

						<th style="width:10%"><?php echo app('translator')->getFromJson('customer.actions'); ?></th>

						<?php endif; ?>

					</tr>
				</thead>
				<tbody>

					<?php $__currentLoopData = $openCashList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> 

					<tr>
						<td><?php echo e($row->id); ?></td>
						<td><?php if(isset($row->user->email)): ?> <?php echo e($row->user->email); ?> <?php endif; ?> </td>
						<td><?php echo e(Common::fixIt($row->opening_amt)); ?></td>						
						<td><?php echo e($row->open_cash_date->format($date_format)); ?> </td>						

						<?php if(Common::can_update('opening_cash') || Common::can_delete('opening_cash')): ?>

						<td>

							<?php if(Common::can_update('opening_cash')): ?>

							<a  href="<?php echo e(route('opening_cash.edit',$row->id)); ?>" v-on:click="getLoader($event)"  class="btn btn-xs btn-info btn-circle" ><i class="fa fa-pencil" aria-hidden="true"></i></a>

							<?php endif; ?>
							<?php if(Common::can_delete('opening_cash')): ?>

							<a  id='<?php echo e($row->id); ?>' v-on:click="deleteOpenCash(<?php echo e($row->id); ?>, $event)" class="btn btn-xs btn-danger btn-circle"><i class="fa fa-trash" aria-hidden="true"></i></a> 


							<?php endif; ?>

						</td>

						<?php endif; ?>

					</tr>

					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

				</tbody>
				<tfoot class="showSearch">
					<tr>
						<th class="search" style="width:10%">#</th>	
						<th class="search" ><?php echo app('translator')->getFromJson('glob.name'); ?></th>					
						<th class="search" ><?php echo app('translator')->getFromJson('glob.opening_amt'); ?></th>						
						<th class="search" ><?php echo app('translator')->getFromJson('glob.opening_cash_date'); ?></th>

						<?php if(Common::can_update('opening_cash') || Common::can_delete('opening_cash')): ?>

						<th class="search" style="width:10%"><?php echo app('translator')->getFromJson('customer.actions'); ?></th>

						<?php endif; ?>

					</tr>
				</tfoot>
			</table>

		</div>
	</div>
	
</div>



<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>