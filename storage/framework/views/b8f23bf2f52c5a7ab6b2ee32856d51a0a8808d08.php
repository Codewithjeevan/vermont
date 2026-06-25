<?php $__env->startSection('page-title'); ?><?php echo app('translator')->getFromJson('glob.birthday'); ?><?php $__env->stopSection(); ?>


<?php $__env->startSection('breadcrumb'); ?>

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> <?php echo app('translator')->getFromJson('glob.dashboard'); ?></a></li>
	<li class="active"><?php echo app('translator')->getFromJson('glob.birthday'); ?></li>
</ol>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<div class="birthday" v-cloak>

	<div class = "row margin-bottom" >
		
		<form method="get" action="<?php echo e(url('reports/birthday')); ?>">
		<div class="col-md-12">
			<div class="col-md-4">
				<label class="form-group"><b><?php echo app('translator')->getFromJson('glob.from'); ?>:<span style="color:red">*</span></b></label>
			</div>
			<div class="col-md-4">
				<label class="form-group"><b><?php echo app('translator')->getFromJson('glob.to'); ?>:<span style="color:red">*</span></b></label>
			</div>
		</div>
		<div class="col-md-2">
				<div class="form-group">
					<label><?php echo app('translator')->getFromJson('customer.day'); ?>:<span style="color:red">*</span></label>
					<select name="date1" class="form-control" type="text" required="">
						<?php $__currentLoopData = $day; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<option value="<?php echo e($d); ?>" <?php if($date1==$d): ?> <?php echo e('selected'); ?> <?php endif; ?>><?php echo e($d); ?></option>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</select>	
				</div>
			</div>
			<div class="col-md-2">
				<div class="form-group">
					<label><?php echo app('translator')->getFromJson('customer.month'); ?>:<span style="color:red">*</span></label>
					<select name="month1" class="form-control" type="text" required="">
					<?php $__currentLoopData = $month; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<option value="<?php echo e($key); ?>"  <?php if($month1==$key): ?> <?php echo e('selected'); ?> <?php endif; ?>><?php echo e($m); ?></option>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</select>
				</div>
			</div>
			<div class="col-md-2">
				</label>
				<div class="form-group">
					<label><?php echo app('translator')->getFromJson('customer.day'); ?>:<span style="color:red">*</span></label>
					<select name="date2" class="form-control" type="text" required="">
					<?php $__currentLoopData = $day; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dd): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<option value="<?php echo e($dd); ?>" <?php if($date2==$dd): ?> <?php echo e('selected'); ?> <?php endif; ?>><?php echo e($dd); ?></option>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</select>	
				</div>
			</div>
			<div class="col-md-2">
				<div class="form-group">
					<label><?php echo app('translator')->getFromJson('customer.month'); ?>:<span style="color:red">*</span></label>
					<select name="month2" class="form-control" type="text" required="">
					<?php $__currentLoopData = $month; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key1=>$mm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<option value="<?php echo e($key1); ?>"  <?php if($month2==$key1): ?> <?php echo e('selected'); ?> <?php endif; ?>><?php echo e($mm); ?></option>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</select>	
				</div>
			</div>
			<div class="col-md-2" style="margin-top: 2%;">
				<button type="submit"  class="btn btn-success btn-circle" > <?php echo app('translator')->getFromJson('glob.show'); ?></button>
			</div>
			<div class="col-md-12  form-inline">
			    <div class="pull-right">
					<label for="search">Search :</label> 
						<input type="text" name="searchkey" value="<?php echo e($searchkey); ?>" class="form-control  input-sm seatch-item" placeholder="search" >
				</div>
			</div>
		</form>
	
		
	</div>


	


<!-- view table data  -->


<div class="row">
	<div class="col-md-12 table-responsive no-search">
		<table class="table table-bordered table-striped datatable-paging-false" id="birthdayTbl">
			<thead>
				<tr>
					<th style="width:2%">#</th>
					<th > <input type="checkbox" id='selectAllCheckbox' value="all" checked="checked"  @click="selectAll" ></th>
					<th><?php echo app('translator')->getFromJson('glob.customer'); ?></th>
					<th><?php echo app('translator')->getFromJson('glob.birthday'); ?></th>
					<th ><?php echo app('translator')->getFromJson('glob.email'); ?></th>
					<th ><?php echo app('translator')->getFromJson('glob.mobile'); ?></th>
				</tr>
			</thead>
			<tbody>

				<?php $__currentLoopData = $birthdays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

				<tr>
					<td><?php echo e($row->id); ?></td>
					<td><input type="checkbox" name="ch[]" id ="<?php echo e($row->id); ?>" value="<?php echo e($row->email); ?>" class = "selectCheckbox"></td>
					<td><?php echo e($row->name); ?></td>
					<td><?php echo e($row->birthday->format($date_format)); ?></td>
					<td class="td-email"><?php echo e($row->email); ?></td>
					<td><?php echo e($row->mobile); ?></td>
				</tr>

				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

			</tbody>
			<tfoot class="showSearch">
				<tr>
					<th  style="width:2%">#</th>
					<th> </th>
					<th class="search"><?php echo app('translator')->getFromJson('glob.customer'); ?></th>
					<th class="search"><?php echo app('translator')->getFromJson('glob.birthday'); ?></th>
					<th class="search" ><?php echo app('translator')->getFromJson('glob.email'); ?></th>
					<th class="search" ><?php echo app('translator')->getFromJson('glob.mobile'); ?></th>
				</tr>
			</tfoot>

		</table>
		<div class="pull-right">
			<?php echo e($birthdays->appends(['searchkey' => $searchkey ])->links()); ?>

		</div>

	</div>
	
</div>
<!-- view table data end -->


<!-- Send Messsage Subject -->
<div class="row">
	<div class="col-md-8">
		<div class="form-group">
			<label><?php echo app('translator')->getFromJson('glob.subject'); ?><span style="color:red">*</span></label>

			<input type="text" name="subject" id ="subject" class = "form-control input-sm" placeholder=" <?php echo app('translator')->getFromJson('glob.subject'); ?>" required="">

		</div>
	</div>
	<div class="col-md-4"></div>

</div>
<!-- Send Messsage Subject End-->


<!-- Send Messsage Body-->
<div class="row" style="margin-top: 5px;">	
	<div class="col-md-8">
		<div class="">
			<label> <?php echo app('translator')->getFromJson('glob.message'); ?><span style="color:red">*</span></label>

			<textarea name="message" id ="message"  class = "form-control input-sm fix-textarea textarea" placeholder=" <?php echo app('translator')->getFromJson('glob.message'); ?>"  rows="4"></textarea>	

		</div>
	</div>

	<div class="col-md-4"></div>
</div>
<!-- Send Messsage Body End-->

<!--Send Message Buttons-->
<div class="row" style="margin-top: 10px;">
	<div class="col-md-4 col-sm-8 ">

		<a v-on:click="sendEmail($event)" class="btn btn-success text-left btn-circle" ><i class="fa fa-envelope" aria-hidden="true"></i> <?php echo app('translator')->getFromJson('glob.sendmail'); ?></a>	
		<a  v-on:click="sendSms($event)" class="btn btn-success text-left btn-circle" ><i class="fa fa-comment" aria-hidden="true"></i> <?php echo app('translator')->getFromJson('glob.send_message'); ?></a>		
	</div>
</div>

<!--Send Message Buttons End-->

</div>	


</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>