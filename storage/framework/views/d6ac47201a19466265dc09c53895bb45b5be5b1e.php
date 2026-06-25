<?php $__env->startSection('page-title'); ?><?php echo app('translator')->getFromJson('glob.purchase_order'); ?><?php $__env->stopSection(); ?>

<?php $__env->startSection('page-subtitle'); ?><?php echo app('translator')->getFromJson('glob.add_edit_purchase_order'); ?><?php $__env->stopSection(); ?> 

<?php $__env->startSection('breadcrumb'); ?>

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> <?php echo app('translator')->getFromJson('glob.dashboard'); ?></a></li>
	<li class="active"><?php echo app('translator')->getFromJson('glob.purchase_order'); ?></li>
</ol>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="purchase_order"> 

	<?php if(Common::can_create('purchase_order')): ?>
	<!-- Button div -->
	<div class="row">
		<div class="col-md-12 margin-bottom">
			<a  class="btn bg-purple new-customer" v-on:click="openSlide()" ><?php echo app('translator')->getFromJson('glob.add_new'); ?></a>
		</div>
	</div>
	<!-- Button div end -->

	<!-- collapse form -->

	<div id="pOrderFormDiv" class="collapse" >

		<form  @submit.prevent="purchaseOrderSave(pOrder, opType , $event)" novalidate="">

			<div class="row customer-box"> 

				<div class="col-md-12">

					<div class="box box-primary"> 

						<div class="box-body">

							<div class="row">

								<!-- purchase order id -->
								<div class="col-md-4">
									<div class="form-group">
										<label for="mrn_no"><?php echo app('translator')->getFromJson('stock.purchase_order_id'); ?> :</label> 
										<span> {{pOrder.id}} </span>
									</div>
								</div>
								<!-- end purchase order id -->

							</div>
							<!-- .row -->

							<div class="row">
								<!-- purchase order date -->
								<div class="col-md-3">
									<div class="form-group">
										<label><?php echo app('translator')->getFromJson('stock.purchase_order_date'); ?><span style="color:red">*</span></label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="ion ion-calendar"></i>
											</div>
											<input id="poDateId" type="text" name="purchase_order_date" class="form-control  input-sm datepicker" placeholder="<?php echo app('translator')->getFromJson('stock.purchase_order_date'); ?>" required="" v-model="pOrder.purchase_order_date">
										</div>
									</div>
								</div>
								<!-- end purchase order date -->
								<!-- supplier name -->
								<div class="col-md-5">
									<div class="form-group">
										<label><?php echo app('translator')->getFromJson('supplier.supplier_name'); ?><span style="color:red">*</span></label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="fa fa-user"></i>
											</div>
											<v-select :options="suppliers" :text="'name'"   v-model="pOrder.supplier_id" :vrequired="true" >
											</v-select>
											
										</div>
									</div>
								</div>
								<!-- end supplier name -->

							</div>
							<!-- .row -->
							<div class="box" style="border-top:0">

								<div class="box-body" style="margin:1px;border:2px solid #c8c8c8; border-radius: 5px;">

									<div class="row">
										<!-- category main -->
										<div class="col-md-3">
											<div class="form-group">
												<label><?php echo app('translator')->getFromJson('glob.category'); ?></label>
												<div class="input-group">
													<div class="input-group-addon">
														<i class="fa fa-tags"></i>
													</div>
													<v-select :options="categories" :vcheck="'parent_category'" :vparent="'0'"  :text="'categorie'"  v-model="purchaseItem.filter.parentCat"  :vrequired="false" >
													</v-select>
													
												</div>
											</div>
										</div>
										<!-- end category main-->
										
										<!-- Sub Category -->
										<div class="col-md-3">
											<div class="form-group">
												<label><?php echo app('translator')->getFromJson('glob.sub_category'); ?></label>
												<div class="input-group">
													<div class="input-group-addon">
														<i class="fa fa-tags"></i>
													</div>
													<v-select :options="categories" :vcheck="'parent_category'" :vparent="purchaseItem.filter.parentCat"  :text="'categorie'"  v-model="purchaseItem.filter.cat"  :vrequired="false" >
													</v-select>
													
												</div>
											</div>
										</div>
										<!-- end Sub Category -->
										
										<!-- item name -->
										<div class="col-md-3">
											<div class="form-group">
												<label><?php echo app('translator')->getFromJson('item.item_name'); ?><span style="color:red">*</span></label>
												<div class="input-group">
													<div class="input-group-addon">
														<i class="icon ion-bag"></i>
													</div>
													<input type="text" class="form-control input-sm  prevent-enter itemname-autocomplete" :data-cat="purchaseItem.filter.cat" v-on:keyup.enter="getCatSubcat(purchaseItem.items.itemName,$event)" v-model="purchaseItem.items.itemName">
													
												</div>
											</div>
										</div>
										<!-- end item name -->

										<!-- Sattrt Barcode -->
										<div class="col-md-3">
											<div class="form-group">
												<label ><?php echo app('translator')->getFromJson('glob.barcode'); ?><span style="color:red">*</span></label>
												<div class="input-group">
													<div class="input-group-addon">
														<i class="icon ion-bag"></i>
													</div>
													<input type="text" class="form-control input-sm prevent-enter  barcode-autocomplete" :data-cat="purchaseItem.filter.cat" v-on:keyup.enter="getCatSubcat(purchaseItem.items.barcode,$event)" v-model="purchaseItem.items.barcode">
													
												</div>
											</div>
										</div>
										<!-- Sattrt Barcode -->
									</div>
									<!-- .row -->

									<div class="row">
										<!--Refrence_Number-->
                                        <div class="col-md-3">
											<div class="form-group">
												<label><?php echo app('translator')->getFromJson('item.refrence_number'); ?><span style="color:red">*</span></label>
												<div class="input-group">
													<div class="input-group-addon">
														<i class="fa fa-tags"></i>
													</div>
												<input type="text" name="refrence_number" class="form-control  input-sm"  placeholder="<?php echo app('translator')->getFromJson('item.refrence_number'); ?>" required="" v-model="purchaseItem.items.serial_number" disabled="">
												</div>
											</div>
										</div>
										<!--End Refrence Number-->
										
										<!-- quantity -->
										<div class="col-md-3">
											<div class="form-group">
												<label><?php echo app('translator')->getFromJson('item.quantity'); ?><span style="color:red">*</span></label>
												
												<input type="text" name="quantity" class="form-control  input-sm validate-decimal" maxlength="8"  placeholder="<?php echo app('translator')->getFromJson('item.quantity'); ?>" required="" v-model="purchaseItem.quantity" >
												
											</div>
										</div>
										<!-- end quantity -->

										<!-- unit -->
										<div class="col-md-3">
											<div class="form-group">
												<label><?php echo app('translator')->getFromJson('glob.unit'); ?><span style="color:red">*</span></label>
												<div class="input-group">
													<div class="input-group-addon"><i class="fa fa-th-large" aria-hidden="true"></i></div>
													<v-select :options="units" :text="'unit'"   v-model="purchaseItem.units.id">
														<option disabled value="0">Select Unit</option>
													</v-select>
													
												</div>

											</div>
										</div>
										<!-- End unit -->

										<!-- C.P -->
										<div class="col-md-3">
											<div class="form-group">
												<label><?php echo app('translator')->getFromJson('item.c_p'); ?><span style="color:red">*</span></label>
												<div class="input-group">
													<div class="input-group-addon"><i class="fa fa-money"></i></div>
													<input type="text" name="cost_price" class="form-control input-sm validate-decimal" placeholder="<?php echo e(trans('item.c_p')); ?>"   v-model="purchaseItem.cost_price" maxlength="8" required>
												</div>
											</div>
										</div>
										<!-- End C.P -->
									</div>
									<div class="row">

										<div class="col-md-1" >
											<div v-if="addbutton == true">
												<button type="button" v-on:click="addpurchaseItem(purchaseItem)" class="btn btn-primary btn-sm" :disabled="((purchaseItem.quantity > 0) && purchaseItem.item_id!=''  && purchaseItem.units.id!=''  && purchaseItem.cost_price!='') ? false :true" ><i class="fa fa-floppy-o"></i> <?php echo app('translator')->getFromJson('glob.add'); ?></button>
											</div>
											<div v-if="updatebutton == true" >
												<button  type="button" v-on:click="updatepurchaseItem(purchaseItem)" class="btn btn-primary btn-sm" :disabled="((purchaseItem.quantity > 0) && purchaseItem.item_id!='' && purchaseItem.units.id!=''  && purchaseItem.cost_price!='') ? false :true"><i class="fa fa-floppy-o"></i> <?php echo app('translator')->getFromJson('glob.update'); ?></button>
											</div>
										</div>
									</div>
									
									<!--  table view -->
									<div class="box-footer">

										<div  class="row">

											<div class="col-md-12 table-responsive ">

												<table class="table table-bordered table-striped ">
													<thead >
														<tr>
															<th ><?php echo app('translator')->getFromJson('glob.barcode'); ?></th>
															<th ><?php echo app('translator')->getFromJson('item.refrence_number'); ?></th>
															<th ><?php echo app('translator')->getFromJson('item.item_name'); ?></th>
															<th ><?php echo app('translator')->getFromJson('glob.unit'); ?></th>
															<th ><?php echo app('translator')->getFromJson('item.c_p'); ?></th>
															<th><?php echo app('translator')->getFromJson('item.quantity'); ?></th>
															<th style="width:10%"><?php echo app('translator')->getFromJson('glob.actions'); ?></th>
														</tr>
													</thead>
													<tbody>
														<tr v-for="( i, index) in pOrder.itemList">
															<td>{{i.items.barcode}}</td>
															<td>{{i.items.serial_number}}</td>
															<td>{{i.items.itemName}}</td>
															<td>{{i.units.unit}}</td>
															<td>{{ fixtIt(i.cost_price)}}</td>
															<td>{{ fixQty(i.quantity)}}</td>
															<td>
																<a v-on:click="editpurchaseItem(i, index)" class="btn btn-xs btn-info btn-circle" ><i class="fa fa-pencil" aria-hidden="true"></i></a>
																<a  v-on:click="deletepurchaseItem(i, index)" class="btn btn-xs btn-danger btn-circle "><i class="fa fa-trash" aria-hidden="true"></i></a> 
															</td>
														</tr>
													</tbody>
												</table>

											</div>

										</div>
										<!-- .row -->

									</div>
									<!-- end  table view -->

									{{tempItem.itemList }}

								</div>
							</div>
							<!-- end border box -->

							<div class="row">
								<div class="col-md-4" v-show="opType=='edit'">	
									<div class="form-group">
										<?php echo Form::label('title', trans('glob.remark')); ?><label><span style="color:red">*</span></label>
										<textarea name="description"  rows="4" class="form-control input-sm fix-textarea" placeholder="<?php echo e(trans('glob.remark')); ?>"  v-model="pOrder.remark" :required="(opType=='edit') ? true : false"></textarea>
									</div>
								</div>
							</div>
							
							<div class="row">
								<div class="col-md-12">
									
									<button v-if="opType=='add'" type="submit" class="btn btn-success save-loader" :disabled="(pOrder.purchase_order_date !='' && pOrder.supplier_id !='' && pOrder.itemList.length !=0 )? false : true"><i class="fa fa-floppy-o"></i> <?php echo app('translator')->getFromJson('glob.save'); ?> </button>

									<button v-if="opType=='edit'" type="submit"  class="btn btn-success update-loader" :disabled="(pOrder.purchase_order_date !='' && pOrder.supplier_id !='' && pOrder.itemList.length !=0 && pOrder.remark!='')? false : true"><i class="fa fa-floppy-o"></i> <?php echo app('translator')->getFromJson('glob.update'); ?></button>

									<a  class="btn btn-cyan" v-on:click="openSlide()"><i class="fa fa-refresh"></i> <?php echo app('translator')->getFromJson('glob.reset'); ?></a>

									<a class="btn  btn-danger"  v-on:click="slideUp()"><?php echo app('translator')->getFromJson('glob.cancel'); ?></a>
									
									
								</div>
							</div>

						</div>					

					</div>
					<!-- .box .box-primary -->

				</div>
				<!-- .col-md-12 -->

			</div>
			<!--end .row .customer-box -->
		</form>
	</div>
	<!-- end collapse form -->
	<?php endif; ?>
	<!-- view table data  -->
	<div class="row">
		<div class="col-md-12 table-responsive pOrder-table">
			<table class="table table-bordered table-striped datatable1 ">
				
				<thead>
					<tr>
						<th ><?php echo app('translator')->getFromJson('stock.purchase_order_id'); ?></th>
						<th><?php echo app('translator')->getFromJson('stock.purchase_order_date'); ?></th>
						<th><?php echo app('translator')->getFromJson('glob.supplier'); ?> <?php echo app('translator')->getFromJson('glob.name'); ?></th>
						<th><?php echo app('translator')->getFromJson('glob.status'); ?></th>
						<th style="width:15%"><?php echo app('translator')->getFromJson('glob.actions'); ?></th>
						
					</tr>
				</thead>
				
				<tbody>
					<?php $__currentLoopData = $purchaseOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>					
					<tr>
						<td><?php echo e($p->id); ?></td>
						<td><?php if($p->purchase_order_date!=null || $p->purchase_order_date!=''): ?><?php echo e(date($date_format, strtotime($p->purchase_order_date))); ?> <?php endif; ?></td>
						<td><?php echo e((isset($p->_supplier->name)) ? $p->_supplier->name : ''); ?> </td>
						<?php if(is_null($p->po_no)): ?>
						<td style="color: red">Pending</td>
						<?php else: ?>
						<td style="color: green">Completed</td>
						<?php endif; ?>
						<td>
							<?php if(is_null($p->po_no)): ?>
							<?php if(Common::can_update('purchase_order')): ?>							
							<a v-on:click="editOrder(<?php echo e($p->id); ?>, $event)" class="btn btn-xs btn-info btn-circle" ><i class="fa fa-pencil" aria-hidden="true"></i></a>							
							<?php endif; ?>
							<?php if(Common::can_delete('purchase_order')): ?>
							<a v-on:click="deleteOrder(<?php echo e($p->id); ?>, $event)" class="btn btn-xs btn-danger btn-circle "><i class="fa fa-trash" aria-hidden="true"></i></a> 
							<?php endif; ?>
							<?php else: ?>
							<?php if(Common::can_update('purchase_order')): ?>							
							<a class="btn btn-xs btn-info btn-circle" disabled><i class="fa fa-pencil" aria-hidden="true"></i></a>							
							<?php endif; ?>
							<?php if(Common::can_delete('purchase_order')): ?>
							<a class="btn btn-xs btn-danger btn-circle" disabled><i class="fa fa-trash" aria-hidden="true"></i></a> 
							<?php endif; ?>
							<?php endif; ?>							
							
							<a  class="btn btn-xs btn-indigo btn-circle" href="<?php echo e(url('stock/purchase_orders/xls/'.$p->id )); ?>" ><i class="fa fa-file-excel-o" aria-hidden="true"></i></a>
							
							<a  class="btn btn-xs btn-danger btn-circle" href="<?php echo e(url('stock/purchase_orders/pdf/'.$p->id )); ?>" ><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>	

							<a v-on:click="sendMail(<?php echo e($p->id); ?>, $event)" class="btn btn-xs btn-success btn-circle "><i class="fa fa-envelope" aria-hidden="true"></i></a> 
							
							
						</td>
						
					</tr>

					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				</tbody>

				<tfoot class="showSearch">
					<tr>
						<th class="search"><?php echo app('translator')->getFromJson('stock.purchase_order_id'); ?></th>
						<th class="search"><?php echo app('translator')->getFromJson('stock.purchase_order_date'); ?></th>
						<th class="search"><?php echo app('translator')->getFromJson('glob.supplier'); ?> <?php echo app('translator')->getFromJson('glob.name'); ?></th>
						<th class="search"><?php echo app('translator')->getFromJson('glob.status'); ?></th>
						<th style="width:15%"><?php echo app('translator')->getFromJson('glob.actions'); ?></th>
						
					</tr>
				</tfoot>
			</table>
		</div>
		
	</div>
	<!-- view table data end -->



</div>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>