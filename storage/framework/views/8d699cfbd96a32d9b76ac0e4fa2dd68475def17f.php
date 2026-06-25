	<div class="row">
		<div class="col-md-12 table-responsive item-master-table no-search">
			<div class="col-md-12 form-inline">
				<div class="pull-right">
					<form id="form-search-item" methode="GET" style="margin-bottom: 10px;">
						<label for="search">Search :</label> 
						<input type="text" name="searchkey" value="<?php echo e($searchkey); ?>" class="form-control  input-sm seatch-item" placeholder="search" >
					</form >
				</div> 
			</div>
			<table class="table table-bordered table-striped datatable-paging-false" >
				<thead>
					<tr>
						<th><?php echo app('translator')->getFromJson('item.s_no'); ?></th>
						<th><?php echo app('translator')->getFromJson('glob.barcode'); ?></th>
						<th><?php echo app('translator')->getFromJson('item.refrence_number'); ?></th>
						<th><?php echo app('translator')->getFromJson('glob.category'); ?></th>
						<th><?php echo app('translator')->getFromJson('glob.sub_category'); ?></th>
						<th><?php echo app('translator')->getFromJson('item.item_product'); ?></th>
						<th><?php echo app('translator')->getFromJson('item.c_p'); ?></th>
						<?php if(Common::vatType('vat','vat_type')!=0): ?><th><?php echo app('translator')->getFromJson('item.cp_vat_percent'); ?></th><?php endif; ?>
						<th><?php echo app('translator')->getFromJson('item.s_p'); ?></th>
						<?php if(Common::vatType('vat','vat_type')!=0): ?><th><?php echo app('translator')->getFromJson('glob.sp_vat'); ?></th><?php endif; ?>
						<th><?php echo app('translator')->getFromJson('glob.unit'); ?></th>
						<th><?php echo app('translator')->getFromJson('glob.stock'); ?></th>
						<th><?php echo app('translator')->getFromJson('glob.min'); ?></th>
						<th><?php echo app('translator')->getFromJson('glob.max'); ?></th>
						

						<?php if(Common::can_update('item') || Common::can_delete('item')): ?>

						<th style="width:4%"><?php echo app('translator')->getFromJson('glob.actions'); ?></th>

						<?php endif; ?>

					</tr>
				</thead>
				<tbody>

					<?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					
					<tr>
						<td><?php echo e($item->id); ?></td>
						<td><?php echo e($item->barcode); ?></td>
						<td><?php if($item->serial_number == null): ?> NA <?php else: ?> <?php echo e($item->serial_number); ?> <?php endif; ?></td>
                        <td><?php echo e((isset($item->categories->parentCategory->categorie)) ? $item->categories->parentCategory->categorie : ''); ?></td>
						<td><?php echo e((isset($item->categories->categorie)) ? $item->categories->categorie : ''); ?></td>
						<td><?php echo e($item->itemName); ?></td>
						<td><?php echo e((isset($item->costPrise)) ? Common::fixIt($item->costPrise) : ''); ?></td>
						<?php if(Common::vatType('vat','vat_type')!=0): ?><td><?php echo e((isset($item->cp_vat)) ?  Common::fixIt($item->cp_vat) : ''); ?></td><?php endif; ?>
						<td><?php echo e((isset($item->salePrise)) ?  Common::fixIt($item->salePrise) : ''); ?></td>
						<?php if(Common::vatType('vat','vat_type')!=0): ?><td><?php echo e((isset($item->vat)) ?  Common::fixIt($item->vat) : ''); ?></td><?php endif; ?>
						<td><?php echo e((isset($item->units->unit)) ? $item->units->unit : ''); ?></td>
						<td><?php echo e(Common::fixQty($item->stock)); ?></td>
						<td><?php echo e(Common::fixQty($item->min)); ?></td>
						<td><?php echo e(Common::fixQty($item->max)); ?></td>
						

						<?php if(Common::can_update('item') || Common::can_delete('item')): ?>


						<td>
							<a v-on:click="editItem(<?php echo e($item->id); ?>, $event)"  class="btn btn-xs btn-info btn-circle" ><i class="fa fa-pencil" aria-hidden="true"></i></a>
							<?php if(Common::can_delete('item')): ?>
							<a v-on:click="deleteItem(<?php echo e($item->id); ?>, $event)" class="btn btn-xs btn-danger btn-circle "><i class="fa fa-trash" aria-hidden="true"></i></a> 
							<?php endif; ?>
						</td>

						<?php endif; ?>

					</tr>

					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					
				</tbody>
				<tfoot class="showSearch">
					<tr>
						<th><?php echo app('translator')->getFromJson('item.s_no'); ?></th>
						<th class="search"><?php echo app('translator')->getFromJson('glob.barcode'); ?></th>
						<th class="search"><?php echo app('translator')->getFromJson('item.refrence_number'); ?></th>
						<th class="search"><?php echo app('translator')->getFromJson('glob.category'); ?></th>
						<th class="search"><?php echo app('translator')->getFromJson('glob.sub_category'); ?></th>
						<th class="search"><?php echo app('translator')->getFromJson('item.item_product'); ?></th>
						<th class="search"><?php echo app('translator')->getFromJson('item.c_p'); ?></th>
						<?php if(Common::vatType('vat','vat_type')!=0): ?><th class="search"><?php echo app('translator')->getFromJson('item.cp_vat_percent'); ?></th><?php endif; ?>
						<th class="search"><?php echo app('translator')->getFromJson('item.s_p'); ?></th>
						<?php if(Common::vatType('vat','vat_type')!=0): ?><th class="search"><?php echo app('translator')->getFromJson('glob.sp_vat'); ?>(%)</th><?php endif; ?>
						<th class="search"><?php echo app('translator')->getFromJson('glob.unit'); ?></th>
						<th class="search"><?php echo app('translator')->getFromJson('glob.stock'); ?></th>
						<th class="search"><?php echo app('translator')->getFromJson('glob.min'); ?></th>
						<th class="search"><?php echo app('translator')->getFromJson('glob.max'); ?></th>
						

						<?php if(Common::can_update('item') || Common::can_delete('item')): ?>


						<th style="width:4%"><?php echo app('translator')->getFromJson('glob.actions'); ?></th>

						<?php endif; ?>
						
					</tr>
				</tfoot>
			</table>
			<div class="pull-right">
				<?php echo e($items->appends(['searchkey' => $searchkey,'filter' =>$filter,'vat'=>$vat ])->links()); ?>

			</div>
		</div>
		
	</div>