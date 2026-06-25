<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-5" style="margin-top: 22%">
            <div class="text-right header">
                <h1 class="title">Vermont<span class="subtitle">Retail</span></h1>
            </div>
        </div>
        <div class="col-md-4 " style="margin-top: 15%">
            <div class="">
                <div class=""></div>
                <div class="panel-body">
                    <form id="loginForm" class="form" role="form" method="POST" action="<?php echo e(url('/login')); ?>">
                        <?php echo e(csrf_field()); ?>


                        <div class="form-group <?php echo e($errors->has('email') ? ' has-error' : ''); ?>" >
                            <label for="email" class="control-label">Username</label>
                            <select id="email"  name="email"  class="form-control text-center select2">
                                <option value="" style="height: 25px;""></option>
                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($u->email); ?>" style="height: 25px;"><?php echo e($u->email); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>

                            
                           

                            <?php if($errors->has('email')): ?>
                            <span class="help-block">
                                <strong><?php echo e($errors->first('email')); ?></strong>
                            </span>
                            <?php endif; ?>
                            
                        </div>

                        <div class="form-group<?php echo e($errors->has('password') ? ' has-error' : ''); ?>">
                            <label for="password" class="control-label">Password</label>
                            <input  id="loginPass" type="password" class="form-control keyboard text-center" name="password" required>

                            <?php if($errors->has('password')): ?>
                            <span class="help-block">
                                <strong><?php echo e($errors->first('password')); ?></strong>
                            </span>
                            <?php endif; ?>
                        </div>

                      

                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary btn-block">
                                Login
                            </button>
                            
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.start', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>