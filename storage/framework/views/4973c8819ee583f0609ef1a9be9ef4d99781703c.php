<?php $__env->startSection('page-title'); ?><?php echo app('translator')->getFromJson('glob.unit'); ?><?php $__env->stopSection(); ?>

<?php $__env->startSection('page-subtitle'); ?><?php echo app('translator')->getFromJson('unit.add_edit_unit'); ?><?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> <?php echo app('translator')->getFromJson('glob.dashboard'); ?></a></li>
	<li class="active"><?php echo app('translator')->getFromJson('glob.unit'); ?></li>
</ol>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="unit">


	<?php if(Common::can_create('unit')): ?>

	<!-- Button div -->
	<div class="row">
		<div class="col-md-6 margin-bottom">
			<a  class="btn bg-purple new-customer" <?php if(isset($editUnit)): ?> href="/common/add/Units" <?php endif; ?> data-toggle="collapse" data-target="#unitFormDiv" v-on:click="addForm"><?php echo app('translator')->getFromJson('glob.add_new'); ?></a>
		</div>
		<div class="col-md-6 dropdown margin-bottom ">
			<button class="btn btn-success dropdown-toggle pull-right" type="button" data-toggle="dropdown" >Export
				<span class="caret" ></span></button>
				<ul class="dropdown-menu pull-right">
					<li><a href="<?php echo e(url('master/units/xls' )); ?>">Excel</a></li>
					<li><a href="<?php echo e(url('master/units/pdf' )); ?>">PDF</a></li>				
				</ul>				
			</div>	
		</div>
		<!-- Button div end -->

		<div id="unitFormDiv" class="collapse <?php if(session('open') || isset($open) || isset($editUnit) || !$errors->isEmpty() ): ?> in <?php endif; ?>">

			<?php if(isset($editUnit)): ?>
			<?php echo e(Form::model($editUnit, ['url'=>array('master/unit',$editUnit->id), 'method'=>'PUT','id'=>'unitForm'])); ?><?php else: ?> 
			<!-- form action for store -->
			<?php echo e(Form::open(['action'=>'UnitsController@store','id'=>'unitForm'])); ?>

			<?php endif; ?>

			<div class="row customer-box">

				<div class="col-md-12">

					<div class="box box-primary">

						<div class="box-body">

							<div class="row">

								<!-- unit -->
								<div class="col-md-5">
									<div class="form-group <?php echo e($errors->has('unit') ? ' has-error' : ''); ?>">
										<?php echo Form::label('title', trans('glob.unit')); ?><span style="color:red">*</span>
										<div class="input-group">
											<div class="input-group-addon"><i class="fa fa-th-large" aria-hidden="true"></i></div>
											<?php echo Form::text('unit', null, array('required', 'class'=>'form-control input-sm', 'placeholder'=>trans('glob.unit'))); ?>

										</div>

										<?php if($errors->has('unit')): ?>
										<span class="help-block">
											<strong><?php echo e($errors->first('unit')); ?></strong>
										</span>
										<?php endif; ?>
										
									</div>
								</div>
								<!-- end type -->

							</div>
							<!-- .row -->
						</div>

						<!-- .box-body -->
						<div class="box-footer">

							<div class="form-group">
								<button type="submit"  class="btn btn-primary " v-on:click="checkValidateLoader($event)"><i class="fa fa-floppy-o"></i><?php if(isset($editUnit)): ?> <?php echo app('translator')->getFromJson('glob.update'); ?> <?php else: ?> <?php echo app('translator')->getFromJson('glob.save'); ?> <?php endif; ?></button>
								<a  class="btn btn-cyan" v-on:click="resetForm"><i class="fa fa-refresh"></i> <?php echo app('translator')->getFromJson('glob.reset'); ?></a>
								<a  <?php if(isset($editUnit)): ?> href="<?php echo e(url('master/unit')); ?>" <?php endif; ?> class="btn btn-danger add-customer" data-toggle="collapse" data-target="#unitFormDiv" v-on:click="cancelForm"><?php echo app('translator')->getFromJson('glob.cancel'); ?></a>
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
							<th style="width:2%">#</th>
							<th><?php echo app('translator')->getFromJson('glob.unit'); ?></th>
							<th ><?php echo app('translator')->getFromJson('glob.created_date'); ?></th>

							<?php if(Common::can_update('unit') || Common::can_delete('unit')): ?>

							<th style="width:4%"><?php echo app('translator')->getFromJson('glob.actions'); ?></th>
							
							<?php endif; ?>

						</tr>
					</thead>
					<tbody>
						<?php $__currentLoopData = $unit; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<tr>
							<td><?php echo e($row->id); ?></td>
							<td><?php echo e($row->unit); ?></td>
							<td ><?php echo e($row->created_at->format('jS F, Y')); ?>

								<br/>
								<em>
									<small >
										<?php echo app('translator')->getFromJson('glob.updated_at'); ?> <?php echo e($row->created_at->diffForHumans()); ?>

									</small>
								</em>
							</td>

							<?php if(Common::can_update('unit') || Common::can_delete('unit')): ?>

							<td>

								<?php if(Common::can_update('unit')): ?>

								<a href="<?php echo e(route('unit.edit',$row->id)); ?>" v-on:click="getLoader($event)" class="btn btn-xs btn-info btn-circle" ><i class="fa fa-pencil" aria-hidden="true"></i></a>

								<?php endif; ?>
								<?php if(Common::can_delete('unit')): ?>

								<a v-on:click="deleteUnit(<?php echo e($row->id); ?>,$event)"  class="btn btn-xs btn-danger btn-circle"><i class="fa fa-trash" aria-hidden="true"></i></a> 

								<?php endif; ?>

							</td>

							<?php endif; ?>

						</tr>

						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

					</tbody>
					<tfoot class="showSearch">
						<tr>
							<th style="width:2%">#</th>
							<th class="search"><?php echo app('translator')->getFromJson('glob.unit'); ?></th>
							<th class="search"><?php echo app('translator')->getFromJson('glob.created_date'); ?></th>

							<?php if(Common::can_update('unit') || Common::can_delete('unit')): ?>

							<th style="width:4%"><?php echo app('translator')->getFromJson('glob.actions'); ?></th>

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