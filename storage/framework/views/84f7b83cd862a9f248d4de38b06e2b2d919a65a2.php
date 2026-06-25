<div class="col-md-12 table-responsive purchase-table" >

	<table class="table table-bordered table-striped datatable1 ">

		<thead>
			<tr>
				<th style="width:2%">#</th>

				<th><?php echo app('translator')->getFromJson('stock.stock_transfer_no'); ?></th>
				<th><?php echo app('translator')->getFromJson('stock.stock_transfer_date'); ?></th>
				<th><?php echo app('translator')->getFromJson('supplier.supplier'); ?></th>
				<th><?php echo app('translator')->getFromJson('stock.po_no'); ?></th>
				<th><?php echo app('translator')->getFromJson('billing.bill'); ?> <?php echo app('translator')->getFromJson('billing.amt'); ?></th>
				<?php if(Common::can_update('purchase') || Common::can_delete('purchase')): ?>
				<th style="width:4%"><?php echo app('translator')->getFromJson('glob.actions'); ?></th>
				<?php endif; ?>
			</tr>
		</thead>

		<tbody>

			<?php $__currentLoopData = $purchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<tr>
				<td><?php echo e($p->id); ?></td>
				<td><?php echo e($p->supplierBillNo); ?></td>
				<td><?php if($p->supplier_billdate!=null || $p->supplier_billdate!=''): ?><?php echo e(date($date_format, strtotime($p->supplier_billdate))); ?><?php endif; ?></td>
				<td><?php echo e((isset($p->_supplier->name)) ? $p->_supplier->name : ''); ?></td>
				<td><?php echo e($p->po_no); ?></td>
				<td><?php echo e(Common::fixIt($p->billAmount)); ?></td>
				<?php if(Common::can_update('purchase') || Common::can_delete('purchase')): ?>
				<td>
					<?php if(Common::can_update('purchase')): ?>
					<a v-on:click="purchaseEdit(<?php echo e($p->id); ?>, $event)" class="btn btn-xs btn-info btn-circle" ><i class="fa fa-pencil" aria-hidden="true"></i></a>
					<?php endif; ?>
					<?php if(Common::can_delete('purchase')): ?>

					<a  v-on:click="purchaseDelete(<?php echo e($p->id); ?>, $event)" class="btn btn-xs btn-danger btn-circle "><i class="fa fa-trash" aria-hidden="true"></i></a> 
					<?php endif; ?>
				</td>
				<?php endif; ?>
			</tr>

			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

		</tbody>

		<tfoot class="showSearch">
			<tr>
				<th style="width:2%">#</th>
				<th class="search"><?php echo app('translator')->getFromJson('stock.stock_transfer_no'); ?></th>
				<th class="search"><?php echo app('translator')->getFromJson('stock.stock_transfer_date'); ?></th>
				<th class="search"><?php echo app('translator')->getFromJson('supplier.supplier'); ?></th>
				<th class="search"><?php echo app('translator')->getFromJson('stock.po_no'); ?></th>
				<th class="search"><?php echo app('translator')->getFromJson('billing.bill'); ?> <?php echo app('translator')->getFromJson('billing.amt'); ?></th>
				<?php if(Common::can_update('purchase') || Common::can_delete('purchase')): ?>
				<th style="width:4%"><?php echo app('translator')->getFromJson('glob.actions'); ?></th>
				<?php endif; ?>
			</tr>
		</tfoot>
	</table>
</div>
