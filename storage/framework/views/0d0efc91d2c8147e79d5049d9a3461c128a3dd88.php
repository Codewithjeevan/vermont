<?php $__env->startSection('page-title'); ?><?php echo app('translator')->getFromJson('glob.brand_classification'); ?><?php $__env->stopSection(); ?>

<?php $__env->startSection('page-subtitle'); ?><?php echo app('translator')->getFromJson('glob.manage_brand_classification'); ?><?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> <?php echo app('translator')->getFromJson('glob.brand_classification'); ?></a></li>
	<li class="active"><?php echo app('translator')->getFromJson('glob.brand_classification'); ?></li> 
</ol>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="brand_classification">

	<?php if(Common::can_create('brand_classification')): ?> 

	<!-- Button div -->
	<div class="row">
		<div class="col-md-6 margin-bottom"> 
			<a  <?php if(isset($editCategory)): ?> href="/common/add/brand_classification" <?php endif; ?> class="btn bg-purple new-customer" data-toggle="collapse" data-target="#brandClassificationFormDiv" v-on:click="addForm()"><?php echo app('translator')->getFromJson('glob.add_new'); ?></a>
		</div>
		</div>
		<!-- Button div end -->

		<div id="brandClassificationFormDiv" class="collapse <?php if(session('open') || isset($open)  || !$errors->isEmpty() ): ?> in <?php endif; ?>">

			
		
			<?php echo e(Form::open(['action'=>'BrandClassificationController@store','id'=>'brandClassificationForm'])); ?><!-- form action for store -->
		

			<div class="row customer-box">

				<div class="col-md-12">

					<div class="box box-primary">

						<div class="box-body">

							
							<div class="row">

								
								<div class="col-md-5">
									<div class="form-group <?php echo e($errors->has('brand_id') ? ' has-error' : ''); ?>">
										<?php echo Form::label('title', trans('glob.brand')); ?><span style="color:red">*</span>
										<div class="input-group">
											<div class="input-group-addon"><i class="fa fa-tags" aria-hidden="true"></i></div>
											<select name="brand_id" class="form-control input-sm " required="">
												<option value="" >Select Brand</option>
												<?php $__currentLoopData = $parentCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<option value="<?php echo e($c->id); ?>"><?php echo e($c->categorie); ?></option>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											</select>
										</div>

										<?php if($errors->has('brand_id')): ?>
										<span class="help-block">
											<strong><?php echo e($errors->first('brand_id')); ?></strong>
										</span>
										<?php endif; ?>

									</div>
								</div>

							</div>
							
							<div class="row">
										<div class="col-md-5">
								<div class="form-group">
									<?php echo Form::label('title', trans('glob.activation_amount')); ?><span style="color:red">*</span>
									<div class="input-group">
										<div class="input-group-addon"> <i class="fa fa-money" aria-hidden="true"></i> </div>
										<?php echo Form::text('activation_amount', null, 
										array('required',  'class'=>'form-control input-sm',  'placeholder'=>trans('glob.activation_amount'))); ?>

									</div>

									<?php if($errors->has('brand_id')): ?>
									<span class="help-block">
										<strong><?php echo e($errors->first('brand_id')); ?></strong>
									</span>
									<?php endif; ?>

								</div>
							</div>
							</div>
						</div>

						<!-- .box-body -->
						<div class="box-footer">

							<div class="form-group">

								<button type="submit"  class="btn btn-primary " v-on:click="getLoader($event)"><i class="fa fa-floppy-o"></i><?php echo app('translator')->getFromJson('glob.save'); ?> </button>
								<a  class="btn btn-cyan" v-on:click="resetForm()"><i class="fa fa-refresh"></i> <?php echo app('translator')->getFromJson('glob.reset'); ?></a>
								<a class="btn btn-danger add-customer" data-toggle="collapse" data-target="#brandClassificationFormDiv" v-on:click="cancelForm"><?php echo app('translator')->getFromJson('glob.cancel'); ?></a>

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
				<table class="table table-bordered table-hover datatable-order" data-sort-order="desc" data-sort-col="0">
					<thead>
						<tr>
							<th style="width:2%">#</th>
							<th><?php echo app('translator')->getFromJson('glob.brand_name'); ?></th>

							<th><?php echo app('translator')->getFromJson('glob.activation_amount'); ?></th>
                     	<?php if(Common::can_delete('brand_classification')): ?>

							<th style="width:4%"><?php echo app('translator')->getFromJson('glob.actions'); ?></th>

							<?php endif; ?>

						</tr>
					</thead>
					<tbody>

						<?php $__currentLoopData = $brandClassification; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

						<tr>
							<td><?php echo e($row->id); ?></td>
							<td><?php echo e($row->parentCategory->categorie); ?></td>
							<td><?php echo e($row->activation_amount); ?></td>
							

							<?php if(Common::can_delete('brand_classification')): ?>

							<td>

								
								<?php if(Common::can_delete('brand_classification')): ?>

								<a id='<?php echo e($row->id); ?>' v-on:click="deleteBrandClassification(<?php echo e($row->id); ?>, $event)" class="btn btn-xs btn-danger btn-circle "><i class="fa fa-trash" aria-hidden="true"></i></a> 

								<?php endif; ?>

							</td> 

							<?php endif; ?>

						</tr>

						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						
					</tbody>
					<tfoot class="showSearch">
						<tr>
							<th style="width:2%">#</th>
							<th class="search"><?php echo app('translator')->getFromJson('glob.brand_name'); ?></th>
							<th class="search"><?php echo app('translator')->getFromJson('glob.activation_amount'); ?></th>
							<?php if(Common::can_delete('brand_classification')): ?>

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