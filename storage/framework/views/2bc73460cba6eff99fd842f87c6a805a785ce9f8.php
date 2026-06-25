<?php $__env->startSection('page-title'); ?><?php echo app('translator')->getFromJson('glob.role_master'); ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('page-sub-title'); ?> Add New Role <?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
<ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> <?php echo app('translator')->getFromJson('glob.dashboard'); ?></a></li>
    <li><a href="<?php echo e(url('/settings/role')); ?>"> <?php echo app('translator')->getFromJson('glob.roles'); ?></a></li>
    <?php if(isset($eRoles)): ?>
    <li class="active"><?php echo app('translator')->getFromJson('glob.edit_roles'); ?></li>
    <?php else: ?>
    <li class="active"><?php echo app('translator')->getFromJson('glob.add_roles'); ?></li>
    <?php endif; ?>
</ol>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-md-12">
        <?php if(isset($eRoles)): ?>
        <?php echo e(Form::model($eRoles, ['url'=>array('settings/role',$eRoles->id), 'method'=>'PUT','id'=>'roleForm'])); ?>

        <?php else: ?>
        <?php echo e(Form::open(['action'=>'RolesController@store','id'=>'roleForm'])); ?>

        <?php endif; ?>
        <div class="box box-primary">
            <div class="box-header">
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group <?php echo e($errors->has('role') ? ' has-error' : ''); ?>">
                            <?php echo Form::label('title', trans('glob.role_name') ); ?><span style="color:red">*</span>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-tags" aria-hidden="true"></i>
                                </div>
                                <?php echo Form::text('role', null, 
                                array('required', 
                                'class'=>'form-control input-sm', 
                                'placeholder'=>trans('glob.role_name'))); ?>

                            </div>
                            <?php if($errors->has('role')): ?>
                                        <span style="color:red">
                                         <strong><?php echo e($errors->first('role')); ?></strong>
                                        </span>
                                        <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group <?php echo e($errors->has('max_discount') ? ' has-error' : ''); ?>">
                            <?php echo Form::label('title', trans('glob.dis_per') ); ?><span style="color:red">*</span>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-percent" aria-hidden="true"></i>
                                </div>
                                <?php echo Form::text('max_discount', null, 
                                array('required', 
                                'class'=>'form-control input-sm', 
                                'placeholder'=>trans('glob.dis_per'))); ?>

                            </div>
                            <?php if($errors->has('max_discount')): ?>
                                        <span style="color:red">
                                         <strong><?php echo e($errors->first('max_discount')); ?></strong>
                                        </span>
                                        <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-primary"><?php if(isset($eRoles)): ?> Update <?php else: ?> Submit <?php endif; ?></button>
               
                <a  href="<?php echo e(url('/settings/role')); ?>"  class="btn btn-danger add-customer"><?php echo app('translator')->getFromJson('glob.cancel'); ?></a>
            </div>
        </div>
        <?php echo e(Form::close()); ?>


    </div>
</div>
<?php if(Common::has_access("privileges")): ?>
<?php if(isset($roleCreated) &&  $roleCreated): ?>
<h3>Update privileges of roles</h3>
<div class="row" id="privileges">
    <div class="col-md-12 table-responsive">
        <?php echo e(Form::open(['url'=>array('settings/privileges', $eRoles), 'method'=>'PUT'])); ?>

        <input type="hidden" name="role" value="<?php echo e($eRoles->id); ?>">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th> <?php echo app('translator')->getFromJson('glob.resources'); ?></th>
                    <th><input type="checkbox" class="headViewCheck" v-on:click="viewCheckAll"> <?php echo app('translator')->getFromJson('glob.view'); ?></th>
                    <th><input type="checkbox" class="headCreateCheck" v-on:click="createCheckAll"> <?php echo app('translator')->getFromJson('glob.create'); ?></th>
                    <th><input type="checkbox" class="headUpdateCheck" v-on:click="updateCheckAll"> <?php echo app('translator')->getFromJson('glob.update'); ?></th>
                    <th><input type="checkbox" class="headDeleteCheck" v-on:click="deleteCheckAll"> <?php echo app('translator')->getFromJson('glob.delete'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $i=0; ?>
                <?php $__currentLoopData = $resources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ro): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php ++$i; ?>
                <?php $__currentLoopData = $ro["parent"]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="bg-stylish">
                    <td data-toggle="collapse" data-target=".child<?php echo e($i); ?>" style="cursor: pointer;"> <i class="ion-ios-plus-outline"></i> <?php echo e($r->res_name); ?>  </td>
                    <td> <input type="checkbox" class="viewcheck parent-view parent-view-<?php echo e($i); ?>" data-id="<?php echo e($i); ?>" name="viewchk[<?php echo e($r->id); ?>]"    <?php if(!empty($pbyr[$r->id][0]) && $pbyr[$r->id][0]["view"]==1): ?> <?php echo e('checked'); ?>  <?php endif; ?>> </td>
                    <td> <input type="checkbox" class="createcheck parent-create parent-create-<?php echo e($i); ?>" data-id="<?php echo e($i); ?>" name="createchk[<?php echo e($r->id); ?>]"  <?php if(!empty($pbyr[$r->id][0]) && $pbyr[$r->id][0]["create"]==1): ?> <?php echo e('checked'); ?>  <?php endif; ?>> </td>
                    <td> <input type="checkbox" class="updatecheck parent-update parent-update-<?php echo e($i); ?>" data-id="<?php echo e($i); ?>" name="updatechk[<?php echo e($r->id); ?>]"  <?php if(!empty($pbyr[$r->id][0]) && $pbyr[$r->id][0]["update"]==1): ?> <?php echo e('checked'); ?>  <?php endif; ?>> </td>
                    <td> <input type="checkbox" class="deletecheck parent-delete parent-delete-<?php echo e($i); ?>" data-id="<?php echo e($i); ?>" name="deletechk[<?php echo e($r->id); ?>]"  <?php if(!empty($pbyr[$r->id][0]) && $pbyr[$r->id][0]["delete"]==1): ?> <?php echo e('checked'); ?>  <?php endif; ?>> </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <?php $__currentLoopData = $ro["child"]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="collapse  child<?php echo e($i); ?>">
                    <td> <span style="margin-left:40px;"><?php echo e($r->res_name); ?></span></td>
                    <?php if($r->slug=='billing' || $r->slug=='billing2'): ?>
                    <td> 
                        <label id="all" class="radio-inline"><input type="radio" class="viewcheck child-view-<?php echo e($i); ?>" data-id="<?php echo e($i); ?>" name="viewchk[<?php echo e($r->id); ?>]"  value="2"  <?php if(!empty($pbyr[$r->id][0]) && $pbyr[$r->id][0]["view"]==2): ?> <?php echo e('checked'); ?>  <?php endif; ?>> All Bill</label> 
                        <label id="one" class="radio-inline"><input type="radio" class="viewcheck child-view-<?php echo e($i); ?>" data-id="<?php echo e($i); ?>" name="viewchk[<?php echo e($r->id); ?>]"  value="1"   <?php if(!empty($pbyr[$r->id][0]) && $pbyr[$r->id][0]["view"]==1): ?> <?php echo e('checked'); ?>  <?php endif; ?>> individual Bill</label> 
                    </td>
                    <?php else: ?>
                    <td> <input type="checkbox" class="viewcheck child-view-<?php echo e($i); ?>" data-id="<?php echo e($i); ?>" name="viewchk[<?php echo e($r->id); ?>]"    <?php if(!empty($pbyr[$r->id][0]) && $pbyr[$r->id][0]["view"]==1): ?> <?php echo e('checked'); ?>  <?php endif; ?>> </td>
                    <?php endif; ?>
                    <td> <input type="checkbox" class="createcheck child-create-<?php echo e($i); ?>" data-id="<?php echo e($i); ?>"  name="createchk[<?php echo e($r->id); ?>]"  <?php if(!empty($pbyr[$r->id][0]) && $pbyr[$r->id][0]["create"]==1): ?> <?php echo e('checked'); ?>  <?php endif; ?>> </td> 
                    <td> <input type="checkbox" class="updatecheck child-update-<?php echo e($i); ?>" data-id="<?php echo e($i); ?>" name="updatechk[<?php echo e($r->id); ?>]"  <?php if(!empty($pbyr[$r->id][0]) && $pbyr[$r->id][0]["update"]==1): ?> <?php echo e('checked'); ?>  <?php endif; ?>> </td>
                    <td> <input type="checkbox" class="deletecheck child-delete-<?php echo e($i); ?>" data-id="<?php echo e($i); ?>" name="deletechk[<?php echo e($r->id); ?>]"  <?php if(!empty($pbyr[$r->id][0]) && $pbyr[$r->id][0]["delete"]==1): ?> <?php echo e('checked'); ?>  <?php endif; ?>> </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <?php if(Common::can_update("privileges")): ?>
        <button  type="submit" class="btn btn-primary">Update Privileges</button>
        <?php endif; ?>
        <?php echo e(Form::close()); ?>

    </div>
</div>
<?php endif; ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>