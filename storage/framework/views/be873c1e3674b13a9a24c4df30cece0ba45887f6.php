<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'Retail Sales')); ?></title>

    <!-- Styles -->
    <link href="<?php echo e(asset("css/theme.css")); ?>" rel="stylesheet">
    <link href="<?php echo e(asset("css/plugin.css")); ?>" rel="stylesheet">
    <link href="<?php echo e(asset("css/app.css")); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('css/plugin-extend.css')); ?>" rel="stylesheet">

    <link href="<?php echo e(asset('fonts/font-awesome/css/font-awesome.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('fonts/ionicons-2.0.1/css/ionicons.min.css')); ?>" rel="stylesheet">

    <!-- Scripts -->
    <script src="<?php echo e(asset("js/preload.js")); ?>"></script>
      
    <style>
        <?php echo $__env->yieldContent('style'); ?>
    </style>
</head>
<body class="hold-transition skin-purple sidebar-mini">
    <div id="app" class="wrapper">
        <?php echo $__env->make('layouts.partials.header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        <?php echo $__env->make('layouts.partials.sidebar', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        <div class="content-wrapper">
        <?php echo Toastr::message(); ?>

            <section class="content-header">
                <h1>
                    <?php echo $__env->yieldContent('page-title'); ?>
                    <small> <?php echo $__env->yieldContent('page-subtitle'); ?> </small>
                </h1>
                <?php echo $__env->yieldContent('breadcrumb'); ?>
            </section>
            <section class="content">

                <?php echo $__env->yieldContent('content'); ?>
            </section>
        </div>
        <?php echo $__env->make('layouts.partials.footer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        
    </div>

    <script src="<?php echo e(asset('js/plugins.js')); ?>"></script>
    <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
    <script src="<?php echo e(asset('js/app.js')); ?>"></script>

</body>
</html>

