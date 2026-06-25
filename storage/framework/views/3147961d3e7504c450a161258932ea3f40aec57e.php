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
			<label style="font-style: italic; font-weight: bold; font-size:20px;">Sales Summary Report</label>
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
	

	<!-- Bank Details Table -->
	<div class="table-responsive" style="width: 100%;">
		<table class="table table-bordered" style="width: 100%;">
			<thead>
				<tr>
					<th style="width:10%">#</th>											
					<th><?php echo app('translator')->getFromJson('billing.bill_date'); ?></th>
					<th><?php echo app('translator')->getFromJson('billing.bill_no'); ?></th>						
					<th><?php echo app('translator')->getFromJson('glob.customer'); ?></th>
					<th><?php echo app('translator')->getFromJson('billing.sub_total'); ?> (<?php echo e($curr); ?>)</th>
					<?php if($intVatType != 0): ?>
					<th><?php echo app('translator')->getFromJson('glob.vat'); ?> (<?php echo e($curr); ?>)</th>
					<?php endif; ?>
					<th><?php echo app('translator')->getFromJson('glob.discount'); ?> (<?php echo e($curr); ?>)</th>
					<th><?php echo app('translator')->getFromJson('glob.loyalty_discount'); ?> (<?php echo e($curr); ?>)</th>
					<th><?php echo app('translator')->getFromJson('billing.mode_of_pay'); ?></th>
					<th><?php echo app('translator')->getFromJson('billing.cash_amt'); ?> (<?php echo e($curr); ?>)</th>	
					<th><?php echo app('translator')->getFromJson('billing.card_amt'); ?> (<?php echo e($curr); ?>)</th>			
				</tr>
			</thead>
			<tbody >
				<?php if($intVatType != 0): ?>
				<?php $__currentLoopData = $itemList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<tr>
					<td><?php echo e($i++); ?></td>			
					<td ><?php echo e($row['dateTime']); ?></td>
					<td ><?php echo e($row['id']); ?></td>
					<td ><?php echo e($row['customerName']); ?></td>		
					<td ><?php echo e(Common::fixIt($row['subtotal'])); ?></td>	
					<td ><?php echo e(Common::fixIt($row['vat_amt'])); ?></td>	
					<td ><?php echo e(Common::fixIt($row['dis_amt'])); ?></td>
					<td ><?php echo e(Common::fixIt($row['loydis_amt'])); ?></td>
					<td ><?php echo e($row['payment_mode']); ?></td>
					<td ><?php echo e(Common::fixIt($row['cash_amt'])); ?></td>
					<td ><?php echo e(Common::fixIt($row['card_amt'])); ?></td>		
				</tr>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>	
				<?php else: ?>
				<?php $__currentLoopData = $itemList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<tr>
					<td><?php echo e($i++); ?></td>			
					<td ><?php echo e($row['dateTime']); ?></td>
					<td ><?php echo e($row['id']); ?></td>
					<td ><?php echo e($row['customerName']); ?></td>		
					<td ><?php echo e(Common::fixIt($row['subtotal'])); ?></td>		
					<td ><?php echo e(Common::fixIt($row['dis_amt'])); ?></td>
					<td ><?php echo e(Common::fixIt($row['loydis_amt'])); ?></td>
					<td ><?php echo e($row['payment_mode']); ?></td>
					<td ><?php echo e(Common::fixIt($row['cash_amt'])); ?></td>
					<td ><?php echo e(Common::fixIt($row['card_amt'])); ?></td>		
				</tr>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>	
				<?php endif; ?>

			</tbody>
			<tfoot >
				<tr>
					<th></th>	
					<th></th>	
					<th></th>	
					<th></th>	
					<th><?php echo app('translator')->getFromJson('billing.sub_total'); ?> = <?php echo e(Common::fixIt($total)); ?></th>	
					<?php if($intVatType != 0): ?>
					<th><?php echo app('translator')->getFromJson('glob.vat'); ?> <?php echo app('translator')->getFromJson('glob.total'); ?> = <?php echo e(Common::fixIt($totalVat)); ?></th>	
					<?php endif; ?>
					<th><?php echo app('translator')->getFromJson('glob.discount'); ?> = <?php echo e(Common::fixIt($totalDiscount)); ?></th>
					<th><?php echo app('translator')->getFromJson('glob.loyalty_discount'); ?> = <?php echo e(Common::fixIt($totalLoyDis)); ?></th>
					<th></th>						
					<th><?php echo app('translator')->getFromJson('billing.cash_amt'); ?> = <?php echo e(Common::fixIt($totalCash)); ?></th>	
					<th><?php echo app('translator')->getFromJson('billing.card_amt'); ?> = <?php echo e(Common::fixIt($totalCard)); ?></th>
				</tr>
			</tfoot>
		</table>
	</div>
	<!-- Bank Details Table End -->	
	<br/>
	<div class="row" style="width: 100%;">
		<div class="col-md-8">
			<label label style="font-weight: bold;
			font-size:10px;"><?php echo app('translator')->getFromJson('glob.total'); ?> <?php echo app('translator')->getFromJson('glob.sales_with_vat'); ?> = <?php echo e(Common::fixIt($total + $totalVat)); ?></label>
		</div>		
	</div>

	<div class="row" style="width: 100%;">
		<div class="col-md-8">
			<label label style="font-weight: bold;
			font-size:10px;"><?php echo app('translator')->getFromJson('glob.total'); ?> <?php echo app('translator')->getFromJson('glob.sales_without_vat'); ?> = <?php echo e(Common::fixIt($total)); ?></label>
		</div>		
	</div>
	
</body>
</html>