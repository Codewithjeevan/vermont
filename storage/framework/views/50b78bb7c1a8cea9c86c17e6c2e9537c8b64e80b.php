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
			<label style="font-style: italic; font-weight: bold; font-size:20px;">Salesman Report</label>
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
	<br/>

	<!-- Bank Details Table -->
	<div class="table-responsive" style="width: 100%;">
		<table class="table table-bordered" style="width: 100%;">
			<thead>
				<tr>
					<th style="width:10%">#</th>											
					<th><?php echo app('translator')->getFromJson('billing.bill_no'); ?></th>	
					<th><?php echo app('translator')->getFromJson('billing.bill_date'); ?></th>
					<th><?php echo app('translator')->getFromJson('glob.customer'); ?></th>
					<th><?php echo app('translator')->getFromJson('item.item_name'); ?></th>	
					<th><?php echo app('translator')->getFromJson('glob.barcode'); ?></th>
					<th><?php echo app('translator')->getFromJson('item.refrence_number'); ?></th>
					<th><?php echo app('translator')->getFromJson('item.serial_number'); ?></th>
					<th><?php echo app('translator')->getFromJson('glob.rate'); ?></th>						
					<th><?php echo app('translator')->getFromJson('item.qty'); ?></th>						
					<th><?php echo app('translator')->getFromJson('stock.amt'); ?>(<?php echo e($curr); ?>)</th>						
				</tr>
			</thead>
			<tbody >
				<?php $__currentLoopData = $purchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<tr>
					<td colspan="11" style="font-weight: bold;
					font-size:10px; background-color: #D1D1D1 ;
					color: #121111 ;"><?php echo e($p->name); ?></td>
					
					
				</tr>
				<?php $index=1 ?>
				<?php $__currentLoopData = $p->child; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<tr>
					<td><?php echo e($index++); ?></td>			
					<td ><?php echo e(Common::fixBill($row->id)); ?></td>
					<td ><?php echo e($row->billingDate); ?></td>	
					<?php if($row->customerName != ''): ?>				
					<td ><?php echo e($row->customerName); ?></td>
					<?php else: ?>
					<td>NA</td>
					<?php endif; ?>
					<td ><?php echo e($row->itemName); ?></td>
					<td ><?php echo e($row->itemCode); ?></td>
					<td ><?php if($row->refrence_number == null): ?> NA <?php else: ?> <?php echo e($row->refrence_number); ?> <?php endif; ?></td>
					<?php if($row->serial_number != ''): ?>				
					<td ><?php echo e($row->serial_number); ?></td>
					<?php else: ?>
					<td>NA</td>
					<?php endif; ?>
					<td ><?php echo e(Common::fixIt($row->rate)); ?></td>
					<td ><?php echo e(Common::fixQty($row->qty)); ?></td>					
					<td ><?php echo e(Common::fixIt(($row->qty * $row->rate))); ?></td>					
				</tr>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>	
				<tr>  									
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<th></th>
					<th></th>
					<th><?php echo app('translator')->getFromJson('item.qty'); ?> = <?php echo e(Common::fixQty($p->qty)); ?></th> 				
					<th><?php echo app('translator')->getFromJson('stock.amt'); ?> = <?php echo e(Common::fixIt($p->amt)); ?></th>	
				</tr>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>	

			</tbody>
			<tfoot >
				<tr>
					
					<th></th>	
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>					
					<th></th>
					<th><?php echo app('translator')->getFromJson('glob.total'); ?> </th>
					<th><?php echo e(Common::fixQty($totalQty)); ?></th> 					
					<th><?php echo e(Common::fixIt($total)); ?></th>							
				</tr>
			</tfoot>
		</table>
	</div>
	<!-- Bank Details Table End -->	
	<br/>

	<div class="row">

		
		
	</div>	
	
</body>
</html>