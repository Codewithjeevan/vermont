<?php $__env->startSection('page-title'); ?><?php echo app('translator')->getFromJson('glob.category'); ?><?php $__env->stopSection(); ?>

<?php $__env->startSection('page-subtitle'); ?><?php echo app('translator')->getFromJson('category.add_edit_category'); ?><?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> <?php echo app('translator')->getFromJson('glob.dashboard'); ?></a></li>
	<li class="active"><?php echo app('translator')->getFromJson('glob.category'); ?></li> 
</ol>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="category">

	<?php if(Common::can_create('category')): ?> 

	<!-- Button div -->
	<div class="row">
		<div class="col-md-6 margin-bottom"> 
			<a  <?php if(isset($editCategory)): ?> href="/common/add/Categories" <?php endif; ?> class="btn bg-purple new-customer" data-toggle="collapse" data-target="#categoryFormDiv" v-on:click="addForm()"><?php echo app('translator')->getFromJson('glob.add_new'); ?></a>
		</div>
		<div class="col-md-6 dropdown margin-bottom ">
			<button class="btn btn-success dropdown-toggle pull-right" type="button" data-toggle="dropdown" >Export
				<span class="caret" ></span></button>
				<ul class="dropdown-menu pull-right">
					<li><a href="<?php echo e(url('master/categorys/xls' )); ?>">Excel</a></li>
					<li><a href="<?php echo e(url('master/categorys/pdf' )); ?>">PDF</a></li>				
				</ul>				
			</div>	
		</div>
		<!-- Button div end -->

		<div id="categoryFormDiv" class="collapse <?php if(session('open') || isset($open) || isset($editCategory) || !$errors->isEmpty() ): ?> in <?php endif; ?>">

			<?php if(isset($editCategory)): ?> 
			<?php echo e(Form::model($editCategory, ['url'=>array('master/category',$editCategory->id),'method'=>'PUT','id'=>'categoryForm'])); ?>

			<?php else: ?> 
			<?php echo e(Form::open(['action'=>'CategoriesController@store','id'=>'categoryForm'])); ?><!-- form action for store -->
			<?php endif; ?>

			<div class="row customer-box">

				<div class="col-md-12">

					<div class="box box-primary">

						<div class="box-body">

							<div class="row">

								<!-- type -->
								<div class="col-md-5">
									<div class="form-group <?php echo e($errors->has('categorie') ? ' has-error' : ''); ?>">
										<?php echo Form::label('title', trans('glob.sub_category')); ?><span style="color:red">*</span>
										<div class="input-group">
											<div class="input-group-addon"><i class="fa fa-tags" aria-hidden="true"></i></div>
											<?php echo Form::text('categorie', null, array('required', 'class'=>'form-control input-sm', 'placeholder'=>trans('glob.sub_category'))); ?>

										</div>

										<?php if($errors->has('categorie')): ?>
										<span class="help-block">
											<strong><?php echo e($errors->first('categorie')); ?></strong>
										</span>
										<?php endif; ?>

									</div>
								</div>
								<!-- end type -->

							</div>
							<!-- /.row -->
							
							<!-- .row -->
							<div class="row">

								
								<div class="col-md-5">
									<div class="form-group <?php echo e($errors->has('parent_category') ? ' has-error' : ''); ?>">
										<?php echo Form::label('title', trans('glob.category')); ?><span style="color:red">*</span>
										<div class="input-group">
											<div class="input-group-addon"><i class="fa fa-tags" aria-hidden="true"></i></div>
											<select name="parent_category" class="form-control input-sm " required="">
												<option value="0" >Parent Category</option>
												<?php $__currentLoopData = $parentCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<?php if(isset($editCategory) && $editCategory->id!=$c->id): ?>
												<option value="<?php echo e($c->id); ?>"  <?php if(isset($editCategory) && $editCategory->parent_category==$c->id ): ?> selected  <?php endif; ?>><?php echo e($c->categorie); ?></option>
												<?php elseif(!isset($editCategory) ): ?>
												<option value="<?php echo e($c->id); ?>"  <?php if(isset($editCategory) && $editCategory->parent_category==$c->id ): ?> selected  <?php endif; ?>><?php echo e($c->categorie); ?></option>
												<?php endif; ?>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											</select>
										</div>

										<?php if($errors->has('parent_category')): ?>
										<span class="help-block">
											<strong><?php echo e($errors->first('parent_category')); ?></strong>
										</span>
										<?php endif; ?>

									</div>
								</div>

							</div>
							
						</div>

						<!-- .box-body -->
						<div class="box-footer">

							<div class="form-group">

								<button type="submit"  class="btn btn-primary " v-on:click="getLoader($event)"><i class="fa fa-floppy-o"></i><?php if(isset($editCategory)): ?> <?php echo app('translator')->getFromJson('glob.update'); ?> <?php else: ?> <?php echo app('translator')->getFromJson('glob.save'); ?> <?php endif; ?></button>
								<a  class="btn btn-cyan" v-on:click="resetForm()"><i class="fa fa-refresh"></i> <?php echo app('translator')->getFromJson('glob.reset'); ?></a>
								<a  <?php if(isset($editCategory)): ?> href="<?php echo e(url('master/category')); ?>" <?php endif; ?> class="btn btn-danger add-customer" data-toggle="collapse" data-target="#categoryFormDiv" v-on:click="cancelForm"><?php echo app('translator')->getFromJson('glob.cancel'); ?></a>

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
							<th><?php echo app('translator')->getFromJson('glob.category'); ?></th>

							<th><?php echo app('translator')->getFromJson('glob.parentCategory'); ?></th>

							<th><?php echo app('translator')->getFromJson('glob.created_date'); ?></th>

							<?php if(Common::can_update('category') || Common::can_delete('category')): ?>

							<th style="width:4%"><?php echo app('translator')->getFromJson('glob.actions'); ?></th>

							<?php endif; ?>

						</tr>
					</thead>
					<tbody>

						<?php $__currentLoopData = $category; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

						<tr>
							<td><?php echo e($row->id); ?></td>
							<td><?php echo e($row->categorie); ?></td>
							<td><?php if($row->parent_category === 0): ?> Parent Category <?php elseif(isset($row->parentCategory->categorie)): ?> <?php echo e($row->parentCategory->categorie); ?>  <?php endif; ?> </td>
							<td ><?php echo e($row->created_at->format('jS F, Y')); ?>

								<br/>
								<em>
									<small class="font-roboto">
										Last Updated At <?php echo e($row->created_at->diffForHumans()); ?>

									</small>
								</em>
							</td>

							<?php if(Common::can_update('category') ||  Common::can_delete('category')): ?>

							<td>

								<?php if(Common::can_update('category')): ?>

								<a href="<?php echo e(route('category.edit',$row->id)); ?>" v-on:click="getLoader($event)" class="btn btn-xs btn-info btn-circle" ><i class="fa fa-pencil" aria-hidden="true"></i></a>

								<?php endif; ?>
								<?php if(Common::can_delete('category')): ?>

								<a id='<?php echo e($row->id); ?>' v-on:click="deleteCategory(<?php echo e($row->id); ?>, $event)" class="btn btn-xs btn-danger btn-circle "><i class="fa fa-trash" aria-hidden="true"></i></a> 

								<?php endif; ?>

							</td> 

							<?php endif; ?>

						</tr>

						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						
					</tbody>
					<tfoot class="showSearch">
						<tr>
							<th style="width:2%">#</th>
							<th class="search"><?php echo app('translator')->getFromJson('glob.category'); ?></th>
							<th class="search"><?php echo app('translator')->getFromJson('glob.parentCategory'); ?></th>
							<th class="search"><?php echo app('translator')->getFromJson('glob.created_date'); ?></th>

							<?php if(Common::can_update('category') ||  Common::can_delete('category')): ?>

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