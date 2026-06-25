<?php $__env->startSection('page-title'); ?> Roles <?php $__env->stopSection(); ?>
<?php $__env->startSection('page-sub-title'); ?>  <?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
<ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> <?php echo app('translator')->getFromJson('glob.dashboard'); ?></a></li>
    <li class="active"> <?php echo app('translator')->getFromJson('glob.roles'); ?></li>
</ol>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="role">

    <?php if(Common::can_create('role')): ?>

    <!-- Add button stare -->
    <div class="row">
        <div class="col-md-12 margin-bottom">
            <a href="<?php echo e(route('role.create')); ?>" class="btn bg-purple btn-float bg-orange-n pull-left" ><?php echo app('translator')->getFromJson('glob.add_new'); ?>
            </a>
        </div>
    </div>
    <!-- End add button -->

    <?php endif; ?>

    <!-- view table data  -->
    <div class="row">
        <div class="col-md-12 table-responsive">
            <table class="table table-bordered table-striped datatable1">
                <thead>
                    <tr>
                        <th> #</th>
                        <th><?php echo app('translator')->getFromJson('glob.roles'); ?></th>
                        <th><?php echo app('translator')->getFromJson('glob.dis_per'); ?></th>
                        <th><?php echo app('translator')->getFromJson('glob.created_date'); ?></th>

                        <?php if(Common::can_update('role') || Common::can_delete('role')): ?>

                        <th><?php echo app('translator')->getFromJson('glob.actions'); ?></th>

                        <?php endif; ?>

                    </tr>
                </thead>
                <tbody>
                   <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                   <tr>
                    <td><?php echo e($e->id); ?></td>
                    <td> <?php echo e($e->role); ?> </td>
                    <td> <?php echo e($e->max_discount); ?> </td>
                    <td><?php echo e($e->created_at->format('jS F, Y H:i')); ?> 
                        <br>
                        <small><em> updated at <?php echo e($e->updated_at->diffForHumans()); ?></em></small>
                    </td>

                    <?php if(Common::can_update('role') || Common::can_delete('role')): ?>

                    <td>

                        <?php if(Common::can_update('role')): ?>

                        <a href="<?php echo e(route('role.edit',$e->id)); ?>" v-on:click="getLoader($event)" class="btn btn-xs btn-info btn-circle" ><i class="fa fa-pencil" aria-hidden="true"></i></a>
                        
                        <?php endif; ?>
                        <?php if(Common::can_delete('role')): ?>

                        <a id='<?php echo e($e->id); ?>' v-on:click="deleteRole(<?php echo e($e->id); ?>, $event)" class="btn btn-xs btn-danger btn-circle "><i class="fa fa-trash" aria-hidden="true"></i></a> 
                        
                        <?php endif; ?>

                    </td>

                    <?php endif; ?>

                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
            <tfoot class="showSearch">
                <tr>
                    <th style="width:2%">#</th>
                    <th class="search"><?php echo app('translator')->getFromJson('glob.roles'); ?></th>
                    <th class="search"><?php echo app('translator')->getFromJson('glob.dis_per'); ?></th>
                    <th class="search"><?php echo app('translator')->getFromJson('glob.created_date'); ?></th>

                    <?php if(Common::can_update('role') || Common::can_delete('role')): ?>

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