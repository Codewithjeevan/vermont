<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">    

	<style type="text/css">

		table, th, td {
			border: 1px solid black;
			border-collapse: collapse;
			font-size:10px;			
		}		
		.row {
			margin-right: 0px;
			margin-left: 0px;   
		}
		.labelfont{
			font-weight: bold;
			font-size:12px;
			float: left;
		}
		.tablefont{			
			font-size:8px;
			float: left;
		}

	</style>    

</head>
<body>
	
	<div class="row">
		<center>				
			<label style="font-style: italic; font-weight: bold; font-size:20px;">Z-Sales Report</label>
			<br/>		
			<br/>		
		</center>
	</div>	
	<br/>
	<div class="row"  style="width: 100%;">
		<div class="col-md-8">
			<label style="font-weight: bold;
			font-size:10px;"><?php echo app('translator')->getFromJson('glob.from'); ?> : <?php echo e($fromDate); ?> - <?php echo app('translator')->getFromJson('glob.to'); ?> : <?php echo e($toDate); ?></label>
		</div>
	</div>
    <div class="row" style="width: 100%;">
		<div class="col-md-8">
			<label label style="font-weight: bold;
			font-size:10px;"><?php echo app('translator')->getFromJson('glob.opening_amt'); ?> = <?php echo e(Common::fixIt($openingCash)); ?></label>
		</div>		
	</div>
	

	<!-- Bank Details Table -->
	<div class="table-responsive" style="width: 100%;">
		<table class="table table-bordered" style="width: 100%;">
			<thead>
				<tr>
					<th style="width:10%"><?php echo app('translator')->getFromJson('glob.id'); ?></th>											
					<th><?php echo app('translator')->getFromJson('billing.bill_date'); ?></th>
					<th><?php echo app('translator')->getFromJson('billing.bill_no'); ?></th>							
					<th><?php echo app('translator')->getFromJson('billing.amt'); ?>(<?php echo e($curr); ?>)</th>			
				</tr>
			</thead>
			<tbody >
				<?php $__currentLoopData = $purchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				
				<tr>
					<td><?php echo e($i++); ?></td>			
					<td ><?php echo e($row['dateTime']); ?></td>
					<td ><?php echo e($row['id']); ?></td>
					<td ><?php echo e(Common::fixIt($row['bil_amt'])); ?></td>				
				</tr>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>	

			</tbody>
			<tfoot >
				<tr>
					<th></th>	
					<th></th>	
					<th></th>	
					<th><?php echo app('translator')->getFromJson('glob.total'); ?> = <?php echo e(Common::fixIt($billAmt)); ?></th>	
				</tr>
			</tfoot>
		</table>
	</div>
	<!-- Bank Details Table End -->	
	<br/>

	 <div class="row" style="width: 100%;">
		<div class="col-md-8">
			<label label style="font-weight: bold;
			font-size:10px;"><?php echo app('translator')->getFromJson('glob.opening_amt'); ?> + <?php echo app('translator')->getFromJson('glob.sale'); ?> (<?php echo e($curr); ?>) = <?php echo e(Common::fixIt($openingCash + $billAmt)); ?></label>
		</div>		
	</div>
	 <div class="row" style="width: 100%;">
		<div class="col-md-8">
			<label label style="font-weight: bold;
			font-size:10px;"><?php echo app('translator')->getFromJson('billing.cash'); ?> (<?php echo e($curr); ?>) = <?php echo e(Common::fixIt($cashAmt)); ?></label>
		</div>		
	</div>
	 <div class="row" style="width: 100%;">
		<div class="col-md-8">
			<label label style="font-weight: bold;
			font-size:10px;"><?php echo app('translator')->getFromJson('billing.credit_card'); ?> (<?php echo e($curr); ?>) = <?php echo e(Common::fixIt($cardAmt)); ?></label>
		</div>		
	</div>
	 <div class="row" style="width: 100%;">
		<div class="col-md-8">
			<label label style="font-weight: bold;
			font-size:10px;"><?php echo app('translator')->getFromJson('billing.credit'); ?> (<?php echo e($curr); ?>) = <?php echo e(Common::fixIt($saleCredit)); ?></label>
		</div>		
	</div>	
	 <div class="row" style="width: 100%;">
		<div class="col-md-8">
			<label label style="font-weight: bold;
			font-size:10px;"><?php echo app('translator')->getFromJson('billing.sale_return'); ?> (<?php echo e($curr); ?>) = <?php echo e(Common::fixIt($saleReturn)); ?></label>
		</div>		
	</div>
	 <div class="row" style="width: 100%;">
		<div class="col-md-8">
			<label label style="font-weight: bold;
			font-size:10px;"><?php echo app('translator')->getFromJson('billing.bal'); ?> (<?php echo e($curr); ?>) = <?php echo e(Common::fixIt($openingCash+$cashAmt+$cardAmt+$saleCredit-$saleReturn)); ?></label>
		</div>		
	</div>

	
</body>
</html>