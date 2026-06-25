<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Vermont Sales</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="<?php echo e(asset('css/theme.css')); ?>">
  
  <link href="<?php echo e(asset('fonts/font-awesome/css/font-awesome.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('fonts/ionicons-2.0.1/css/ionicons.min.css')); ?>" rel="stylesheet">

  <link rel="stylesheet" href="<?php echo e(asset('css/start.css')); ?>">

 
  <link href="<?php echo e(asset('css/plugin.css')); ?>" rel="stylesheet">
  <script src="<?php echo e(asset('js/preload.js')); ?>"></script>
  <script>
    $(document).ready(function() {
      
    });
  </script>
</head>
<body class="hold-transition login-page">
  <div class="overlay"></div>
  <?php echo $__env->yieldContent('content'); ?>
  <!-- /.login-box -->

</body>
</html>
