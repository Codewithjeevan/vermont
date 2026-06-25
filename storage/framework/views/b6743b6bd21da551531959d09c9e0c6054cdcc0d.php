<?php $__env->startSection('page-title'); ?><?php echo app('translator')->getFromJson('glob.salesman'); ?><?php $__env->stopSection(); ?>

<?php $__env->startSection('page-subtitle'); ?><?php echo app('translator')->getFromJson('salesman.add_edit_salesman'); ?><?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> <?php echo app('translator')->getFromJson('glob.dashboard'); ?></a></li>
	<li class="active"><?php echo app('translator')->getFromJson('glob.salesman'); ?></li>
</ol>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<div class="salesman">

	<?php if(Common::can_create('salesman')): ?>

	<!-- Button div -->
	<div class="row">
		<div class="col-md-6 margin-bottom">
			<a  <?php if(isset($editSalesman)): ?> href="/common/add/Salesman" <?php endif; ?> class="btn bg-purple new-customer" data-toggle="collapse" data-target="#salesmanFormDiv" v-on:click="addForm"><?php echo app('translator')->getFromJson('glob.add_new'); ?>
			</a>
		</div>
		<div class="col-md-6 dropdown margin-bottom ">
			<button class="btn btn-success dropdown-toggle pull-right" type="button" data-toggle="dropdown" >Export
				<span class="caret" ></span></button>
				<ul class="dropdown-menu pull-right">
					<li><a href="<?php echo e(url('master/salesmans/xls' )); ?>">Excel</a></li>
					<li><a href="<?php echo e(url('master/salesmans/pdf' )); ?>">PDF</a></li>				
				</ul>				
			</div>	
		</div>
		<!-- Button div end -->




		<div id="salesmanFormDiv" class="collapse <?php if(session('open') || isset($open) || isset($editSalesman) || !$errors->isEmpty()): ?> in <?php endif; ?>">

			<?php if(isset($editSalesman)): ?> 
			<?php echo e(Form::model($editSalesman, ['url'=>array('master/salesman',$editSalesman->id), 'method'=>'PUT','id'=>'salesmanForm'])); ?>


			<?php else: ?> 
			<?php echo e(Form::open(['action'=>'SalesmanController@store','id'=>'salesmanForm'])); ?>

			<?php endif; ?>

			<div class="row">

				<div class="col-md-12">

					<div class="box box-primary">

						<div class="box-body">

							<div class="row">
								<div class="col-md-3">
									<!-- Emp ID -->
									<div class="form-group">
										<?php echo Form::label('id', trans('salesman.emp_id')); ?> 
										<span>
											<?php if(isset($editSalesman->id)): ?> <?php echo e($editSalesman->id); ?> 
											<?php elseif(isset($salesman->last()->id)): ?> <?php echo e($salesman->last()->id+1); ?>

											<?php else: ?> 1 
											<?php endif; ?>
										</span>
									</div>
									<!-- End Emp ID -->

								</div>
							</div>
							<!-- .row -->

							<div class="row">

								<!-- name -->
								<div class="col-md-5">
									<div class="form-group <?php echo e($errors->has('name') ? ' has-error' : ''); ?>">
										<?php echo Form::label('name', trans('glob.name')); ?><span style="color:red">*</span>
										<div class="input-group">
											<div class="input-group-addon"><i class="ion ion-person"></i></div>
											<?php echo Form::text('name', null, array('required', 'class'=>'form-control input-sm ', 'placeholder'=>trans('glob.name'))); ?>

										</div>
										<?php if($errors->has('name')): ?>
										<span class="help-block">
											<strong><?php echo e($errors->first('name')); ?></strong>
										</span>
										<?php endif; ?>
									</div>
								</div>
								<!--End name -->

							</div>
							<!-- .row -->

							<!-- .row -->
							<div class="row">

								<!-- designation -->
								<div class="col-md-5">
									<div class="form-group"> 
										<?php echo Form::label('desigination', trans('glob.desigination')); ?><span style="color:red">*</span>
										<div class="input-group">
											<div class="input-group-addon"><i class="ion ion-person"></i></div>
											<?php if(isset($editSalesman->desigination)): ?>
											<?php echo Form::select('desigination', $desiginationType, App\Salesman::getTypeIdAttribute($editSalesman->desigination), array('required', 'class'=>'form-control input-sm select2', 'placeholder'=>trans('supplier.s_type'))); ?>

											<?php else: ?>
											<?php echo Form::select('desigination', $desiginationType, null , array('required', 'class'=>'form-control input-sm select2', 'placeholder'=>trans('glob.desigination'))); ?>

											<?php endif; ?>

										</div>
									</div>
								</div>
								<!--End designation -->

							</div>
							<!-- .row -->

						</div>
						<!-- .box-body -->

						<div class="box-footer">

							<div class="form-group">
								<button type="submit" class="btn btn-primary " v-on:click="checkValidateLoader($event)"><i class="fa fa-floppy-o"></i><?php if(isset($editSalesman)): ?> <?php echo app('translator')->getFromJson('glob.update'); ?> <?php else: ?> <?php echo app('translator')->getFromJson('glob.save'); ?> <?php endif; ?></button>
								<a  class="btn btn-cyan" v-on:click="resetForm()"><i class="fa fa-refresh"></i> <?php echo app('translator')->getFromJson('glob.reset'); ?></a>
								<a  <?php if(isset($editSalesman)): ?> href="<?php echo e(url('master/salesman')); ?>" <?php endif; ?>  class="btn btn-danger add-customer" data-toggle="collapse" data-target="#salesmanFormDiv" v-on:click="cancelForm"><?php echo app('translator')->getFromJson('glob.cancel'); ?></a> 
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

		</div>

		<?php endif; ?>

		<!-- view table data  -->
		<div class="row">

			<div class="col-md-12 table-responsive">
				<table class="table table-bordered table-striped datatable-order" data-sort-order="desc" data-sort-col="0">
					<thead>
						<tr>
							<th style="width:2%">#</th>
							<th><?php echo app('translator')->getFromJson('glob.name'); ?></th>
							<th><?php echo app('translator')->getFromJson('glob.desigination'); ?></th>

							<?php if(Common::can_update('salesman') || Common::can_delete('salesman')): ?>

							<th style="width:4%"><?php echo app('translator')->getFromJson('glob.actions'); ?></th>

							<?php endif; ?>

						</tr>
					</thead>
					<tbody>

						<?php $__currentLoopData = $salesman; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

						<tr>
							<td><?php echo e($row->id); ?></td>
							<td><?php echo e($row->name); ?></td>
							<td><?php echo e($row->desigination); ?></td>

							<?php if(Common::can_update('salesman') || Common::can_delete('salesman')): ?>

							<td>

								<?php if(Common::can_update('salesman')): ?>

								<a href="<?php echo e(route('salesman.edit',$row->id)); ?>" v-on:click="getLoader($event)"  class="btn btn-xs btn-info btn-circle" ><i class="fa fa-pencil" aria-hidden="true"></i></a>

								<?php endif; ?>
								<?php if(Common::can_delete('salesman')): ?>

								<a  v-on:click="deleteSalesman(<?php echo e($row->id); ?>, $event)" class="btn btn-xs btn-danger btn-circle"><i class="fa fa-trash" aria-hidden="true"></i></a> 

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
							<th class="search"><?php echo app('translator')->getFromJson('glob.desigination'); ?></th>

							<?php if(Common::can_update('salesman') || Common::can_delete('salesman')): ?>

							<th style="width:4%"><?php echo app('translator')->getFromJson('glob.actions'); ?></th>

							<?php endif; ?>

						</tr>
					</tfoot>

				</table>

			</div>
			
		</div>
		<!-- view table data end -->

	</div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>