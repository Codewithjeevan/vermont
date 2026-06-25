<?php $__env->startSection('page-title'); ?> <?php echo app('translator')->getFromJson('glob.resources'); ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('page-sub-title'); ?>  <?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>

<ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> <?php echo app('translator')->getFromJson('glob.dashboard'); ?></a></li>
    <li class="active"><?php echo app('translator')->getFromJson('glob.resources'); ?></li>
</ol>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="resource">

    <?php if(Common::can_create('resource')): ?>

    <!-- Button div -->
    <div class="row">
        <div class="col-md-12 margin-bottom">
            <a <?php if(isset($eResource)): ?> href="/common/add/Resource" <?php endif; ?> class="btn bg-purple new-customer" data-toggle="collapse" data-target="#resourceFormDiv" v-on:click="addForm"><?php echo app('translator')->getFromJson('glob.add_new'); ?></a>
        </div>
    </div>
    <!-- Button div end -->

    <!-- add/update customer form div -->
    <div id="resourceFormDiv" class="collapse <?php if(session('open') || isset($open) || isset($eResource) || !$errors->isEmpty()): ?>  in <?php endif; ?>">

        <?php if(isset($eResource)): ?> 
        <?php echo e(Form::model($eResource, ['url'=>array('settings/resource',$eResource->id), 'method'=>'PUT','id'=>'resourceForm'])); ?>

        <?php else: ?> 
        <?php echo e(Form::open(['action'=>'ResourceController@store','id'=>'resourceForm'])); ?>

        <?php endif; ?>

        <div class="row customer-box">

            <div class="col-md-12">

                <div class="box box-primary">

                    <div class="box-body">

                        <div class="row">

                            <!-- Resource name -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?php echo Form::label('title', trans('glob.resource_name')); ?><span style="color:red">*</span>
                                    <div class="input-group">
                                        <div class="input-group-addon"> <i class="fa fa-tags" aria-hidden="true"></i></div>
                                        <?php echo Form::text('res_name', null,  array('required',  'class'=>'form-control input-sm',  'placeholder'=>trans('glob.resource_name'))); ?>

                                    </div>
                                </div>
                            </div>
                            <!-- end Resource name -->

                        </div>
                        <!-- .row -->

                        <div class="row">

                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="parent-resource">Parent Resource</label>
                                    <select name="parent_resource" id="parent-resource" class="input-sm form-control select2">
                                        <option value="0">Parent Resource</option>
                                        <?php $__currentLoopData = $parentResoureces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($r->id); ?>" <?php if(isset($eResource) && $eResource->parent_resource == $r->id): ?> selected  <?php endif; ?>> <?php echo e($r->res_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>

                        </div>


                        <div class="row"> 

                            <!-- description -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?php echo Form::label('title', trans('glob.description')); ?><span style="color:red">*</span>
                                    <?php echo Form::textarea('res_desc', null,  array('required', 'rows'=>'4',  'class'=>'form-control input-sm',  'placeholder'=> trans('glob.description'))); ?>

                                </div>
                            </div>
                            <!--End description -->

                        </div>
                        

                        <div class="row"> 

                            <!-- slug -->
                            <div class="col-md-6">
                                <div class="form-group <?php echo e($errors->has('slug') ? ' has-error' : ''); ?>">
                                    <?php echo Form::label('title', trans('glob.slug')); ?><span style="color:red">*</span>
                                    <?php echo Form::text('slug', null,  array('required',  'class'=>'form-control input-sm',  'placeholder'=> trans('glob.slug'))); ?>


                                    <?php if($errors->has('slug')): ?>

                                    <span class="help-block"> <strong><?php echo e($errors->first('slug')); ?></strong></span>

                                    <?php endif; ?>
                                    
                                </div>

                            </div>
                            <!--End slug -->

                        </div>
                        


                    </div>
                    <!-- .box-body -->


                    <div class="box-footer">

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary "> <i class="fa fa-floppy-o"></i> <?php if(isset($eResource)): ?> <?php echo app('translator')->getFromJson('glob.update'); ?> <?php else: ?> <?php echo app('translator')->getFromJson('glob.save'); ?> <?php endif; ?></button>
                            <a  class="btn btn-cyan" v-on:click="resetForm()"><i class="fa fa-refresh"></i> <?php echo app('translator')->getFromJson('glob.reset'); ?></a>
                            <a <?php if(isset($eResource)): ?> href="<?php echo e(url('settings/resource')); ?>" <?php endif; ?> 
                            class="btn btn-danger add-customer" data-toggle="collapse" data-target="#resourceFormDiv" v-on:click="cancelForm"><?php echo app('translator')->getFromJson('glob.cancel'); ?></a>
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
            <table class="table table-bordered  datatable2">
                <thead>
                    <tr>
                        <th style="width:2%">#</th>
                        <th><?php echo app('translator')->getFromJson('glob.resource_name'); ?></th>
                        <th><?php echo app('translator')->getFromJson('glob.created_date'); ?></th>

                        <?php if(Common::can_update('resource') || Common::can_delete('resource')): ?>

                        <th style="width:10%"><?php echo app('translator')->getFromJson('glob.actions'); ?></th>

                        <?php endif; ?>

                    </tr>
                </thead>
                <tbody>

                    <?php $i=0; ?>

                    <?php $__currentLoopData = $resources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ro): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php ++$i; ?>
                       <?php $__currentLoopData = $ro["parent"]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                       
                        <tr data-toggle="collapse" class="bg-stylish" data-target=".child<?php echo e($i); ?>">
                            <td><?php echo e($r->id); ?></td>
                            <td> <span style="<?php if($r->parent_resource!=0): ?> margin-left:40px; <?php endif; ?>"><?php echo e($r->res_name); ?></span></td> 
                            <td><?php echo e($r->created_at->format('jS F, Y H:i')); ?></td>

                            <?php if(Common::can_update('resource') || Common::can_delete('resource')): ?>

                            <td>

                                <?php if(Common::can_update('resource')): ?>

                                <a href="<?php echo e(route('resource.edit',$r->id)); ?>" class="btn btn-xs btn-info btn-circle" ><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                
                                <?php endif; ?>
                                <?php if(Common::can_delete('resource')): ?>

                                <a id='<?php echo e($r->id); ?>' v-on:click="deleteResource(<?php echo e($r->id); ?>, $event)" class="btn btn-xs btn-danger btn-circle "><i class="fa fa-trash" aria-hidden="true"></i></a> 
                                
                                <?php endif; ?>

                            </td>
                            <?php endif; ?>
                        </tr>
                        
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            
                                <?php $__currentLoopData = $ro["child"]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="collapse in child<?php echo e($i); ?>">
                                    <td><?php echo e($row->id); ?></td>
                                    <td> <span style="<?php if($row->parent_resource!=0): ?> margin-left:40px; <?php endif; ?>"><?php echo e($row->res_name); ?></span></td> 
                                    <td><?php echo e($row->created_at->format('jS F, Y H:i')); ?></td>

                                    <?php if(Common::can_update('resource') || Common::can_delete('resource')): ?>

                                    <td>

                                        <?php if(Common::can_update('resource')): ?>

                                        <a href="<?php echo e(route('resource.edit',$row->id)); ?>" v-on:click="getLoader($event)"  class="btn btn-xs btn-info btn-circle" ><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                        
                                        <?php endif; ?>
                                        <?php if(Common::can_delete('resource')): ?>

                                        <a id='<?php echo e($row->id); ?>' v-on:click="deleteResource(<?php echo e($row->id); ?>, $event)" class="btn btn-xs btn-danger btn-circle "><i class="fa fa-trash" aria-hidden="true"></i></a> 
                                        
                                        <?php endif; ?>

                                    </td>

                                    <?php endif; ?>

                                </tr>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    
                </tbody>
                <tfoot class="showSearch">
                    <tr>
                        <th style="width:2%">#</th>
                        <th class="search"><?php echo app('translator')->getFromJson('glob.resource_name'); ?></th>
                        <th class="search"><?php echo app('translator')->getFromJson('glob.created_date'); ?></th>

                        <?php if(Common::can_update('resource') || Common::can_delete('resource')): ?>

                        <th style="width:10%"><?php echo app('translator')->getFromJson('glob.actions'); ?></th>

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