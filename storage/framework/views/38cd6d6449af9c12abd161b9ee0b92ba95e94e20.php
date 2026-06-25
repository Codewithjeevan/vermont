<div class="col-md-12 table-responsive">

			<table class="table table-bordered table-striped datatable1 ">
				
				<thead>
					<tr>
						<th style="width:2%">#</th>
						<th><?php echo app('translator')->getFromJson('glob.p_date'); ?></th>
						<th><?php echo app('translator')->getFromJson('glob.payment_to'); ?></th>
						<th><?php echo app('translator')->getFromJson('billing.bill_no'); ?></th>
						<th><?php echo app('translator')->getFromJson('glob.mode'); ?></th>
						<th><?php echo app('translator')->getFromJson('glob.ch_dr_no'); ?></th>
						<th><?php echo app('translator')->getFromJson('glob.ch_dr_date'); ?></th>
						<th><?php echo app('translator')->getFromJson('glob.bank'); ?></th>
						<th><?php echo app('translator')->getFromJson('glob.payment_amt'); ?></th>
						<th><?php echo app('translator')->getFromJson('glob.remark'); ?></th>
						<?php if(Common::can_update('payment') || Common::can_delete('payment')): ?>

						<th style="width:4%"><?php echo app('translator')->getFromJson('glob.actions'); ?></th>

						<?php endif; ?>
					</tr>
				</thead>
				
				<tbody>

					<?php $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<tr>
						<td><?php echo e($p->id); ?></td>
						<td><?php if($p->payment_date!=null || $p->payment_date !=''): ?> <?php echo e(date($date_format, strtotime($p->payment_date))); ?> <?php endif; ?></td>
						<td><?php if(isset($p->_supplier->name)): ?> <?php echo e($p->_supplier->name); ?> <?php endif; ?></td>
						<td><?php echo e($p->bill_no); ?></td>
						<td><?php echo e($p->mode); ?></td>
						<td><?php echo e($p->ch_no); ?></td>
						<td><?php if($p->ch_date!=null || $p->ch_date !=''): ?> <?php echo e(date($date_format, strtotime( $p->ch_date))); ?> <?php endif; ?></td>
						<td><?php echo e($p->bank_id); ?></td>
						<td><?php echo e(Common::fixIt($p->paid_amount)); ?></td>
						<td><?php echo e($p->remark); ?></td>
						
						<?php if(Common::can_update('payment') || Common::can_delete('payment')): ?>

						<td>

							<?php if(Common::can_update('payment')): ?>

							<a v-on:click="edit(<?php echo e($p->id); ?>, $event)" class="btn btn-xs btn-info btn-circle" ><i class="fa fa-pencil" aria-hidden="true"></i></a>
							
							<?php endif; ?>
							<?php if(Common::can_delete('payment')): ?>

							<a v-on:click="deletePayment(<?php echo e($p->id); ?>, $event)" class="btn btn-xs btn-danger btn-circle "><i class="fa fa-trash" aria-hidden="true"></i></a> 
							
							<?php endif; ?>
						</td>

						<?php endif; ?>
						
					</tr>

					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

				</tbody>

				<tfoot class="showSearch">
					<tr>
						<th style="width:2%">#</th>
						<th class="search"><?php echo app('translator')->getFromJson('glob.p_date'); ?></th>
						<th class="search"><?php echo app('translator')->getFromJson('glob.payment_to'); ?></th>
						<th class="search"><?php echo app('translator')->getFromJson('billing.bill_no'); ?></th>
						<th class="search"><?php echo app('translator')->getFromJson('glob.mode'); ?></th>
						<th class="search"><?php echo app('translator')->getFromJson('glob.ch_dr_no'); ?></th>
						<th class="search"><?php echo app('translator')->getFromJson('glob.ch_dr_date'); ?></th>
						<th class="search"><?php echo app('translator')->getFromJson('glob.bank'); ?></th>
						<th class="search"><?php echo app('translator')->getFromJson('glob.payment_amt'); ?></th>
						<th class="search"><?php echo app('translator')->getFromJson('glob.remark'); ?></th>
						<?php if(Common::can_update('payment') || Common::can_delete('payment')): ?>

						<th style="width:4%"><?php echo app('translator')->getFromJson('glob.actions'); ?></th>

						<?php endif; ?>
					</tr>
				</tfoot>
			</table>
		</div>
		