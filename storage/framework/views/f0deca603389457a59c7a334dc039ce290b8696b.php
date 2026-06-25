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
			<label style="font-style: italic; font-weight: bold; font-size:20px;">Brandwise Sales and Sales Return Report</label>
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
	

	<div class="table-responsive" style="width: 100%;">
		<table class="table table-bordered" style="width: 100%;">
			<thead>
				
				<tr>
					<th style="width:10%">#</th>											
					<th><?php echo app('translator')->getFromJson('billing.bill_date'); ?></th>
					<th><?php echo app('translator')->getFromJson('billing.bill_no'); ?></th>	
					<th>Item Name</th>					
					<th><?php echo app('translator')->getFromJson('glob.customer'); ?></th>
					<th><?php echo app('translator')->getFromJson('billing.sub_total'); ?> (<?php echo e($curr); ?>)</th>
					<?php if($intVatType != 0): ?>
					<th><?php echo app('translator')->getFromJson('glob.vat'); ?> (<?php echo e($curr); ?>)</th>
					<?php endif; ?>
					<th><?php echo app('translator')->getFromJson('glob.discount'); ?> (<?php echo e($curr); ?>)</th>
					<th><?php echo app('translator')->getFromJson('glob.loyalty_discount'); ?> (<?php echo e($curr); ?>)</th>
					<th><?php echo app('translator')->getFromJson('billing.mode_of_pay'); ?></th>
				</tr>
			</thead>
			<tbody >
				<?php $__currentLoopData = $purchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<tr>
					<td colspan="10" style="font-weight: bold;
					font-size:10px; background-color: #D1D1D1 ;
					color: #121111 ;"><?php echo e($p->name); ?></td>
				</tr>
            
				<?php $__currentLoopData = $p->child; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<tr>
					<td><?php echo e($i++); ?></td>			
					<td ><?php echo e($row->dateTime); ?></td>
					<td ><?php if($p->id!=1): ?> <?php echo e($row->BillId); ?> <?php else: ?> <?php echo e(Common::fixBill($row->BillId)); ?> <?php endif; ?></td>
					<td><?php echo e($row->itemName); ?></td>
					<td><?php echo e($row->customerName); ?></td>
					<td><?php echo e(Common::fixIt($row->subtotal)); ?></td>
					<td><?php echo e(Common::fixIt($row->v_amt)); ?></td>
					<td><?php echo e(Common::fixIt($row->dis_amt)); ?></td>
					<td><?php echo e(Common::fixIt($row->loyalty_discount)); ?></td>
                    <td><?php echo e($row->payment_mode); ?></td>
				</tr>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			
				<?php if($p->id==1): ?>
				<tr>  									
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<th><?php echo app('translator')->getFromJson('billing.sub_total'); ?> = <?php echo e(Common::fixIt($saletotal)); ?></th>
					<th><?php echo app('translator')->getFromJson('glob.total_s_p'); ?> = <?php echo e(Common::fixIt($saletotalVat)); ?></th>
					<th><?php echo app('translator')->getFromJson('glob.discount'); ?> = <?php echo e(Common::fixIt($saletotalDiscount)); ?></th>
					<th><?php echo app('translator')->getFromJson('glob.loyalty_discount'); ?> = <?php echo e(Common::fixIt($saletotalLoyDis)); ?></th>
					<td></td>
				</tr>
				<?php else: ?>
				<tr>  									
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<th><?php echo app('translator')->getFromJson('billing.sub_total'); ?> = <?php echo e(Common::fixIt($returntotal)); ?></th>
					<th><?php echo app('translator')->getFromJson('glob.total_s_p'); ?> = <?php echo e(Common::fixIt($returntotalVat)); ?></th>
					<th><?php echo app('translator')->getFromJson('glob.discount'); ?> = <?php echo e(Common::fixIt($returntotalDiscount)); ?></th>
					<th><?php echo app('translator')->getFromJson('glob.loyalty_discount'); ?> = <?php echo e(Common::fixIt($returntotalLoyaltyDiscount)); ?></th>
					<td></td>
				</tr>
				<?php endif; ?>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>	

			</tbody>			
		</table>
		<br/>
		<br/>
		<br/>

		<table style="width:100%;">
			<tr>
				<td style="width:50%; font-weight: bold;">
					<?php echo app('translator')->getFromJson('glob.total'); ?> <?php echo app('translator')->getFromJson('glob.sales_with_vat'); ?> = <?php echo e(Common::fixIt(($saletotal)+ ($saletotalVat))); ?><br/>
                    
                     <?php echo app('translator')->getFromJson('glob.total'); ?> <?php echo app('translator')->getFromJson('glob.sales_without_vat'); ?> = <?php echo e(Common::fixIt($saletotal)); ?><br/>
				
				</td>
				<td style="width:50%;font-weight: bold; text-align: right;">
					 <?php echo app('translator')->getFromJson('glob.total'); ?> <?php echo app('translator')->getFromJson('glob.sales_return_with_vat'); ?>   =  <?php echo e(Common::fixIt(($returntotal) + ($returntotalVat))); ?><br/>
                      <?php echo app('translator')->getFromJson('glob.total'); ?> <?php echo app('translator')->getFromJson('glob.sales_return_without_vat'); ?> = <?php echo e(Common::fixIt($returntotal)); ?><br/>
					
				</td>
			</tr>
		</table>
		
		

	</div>
	<!-- Bank Details Table End -->	
	<br/>
	
</body>
</html>