<?php $__env->startSection('page-title'); ?><?php echo app('translator')->getFromJson('glob.payment'); ?><?php $__env->stopSection(); ?>

<?php $__env->startSection('page-subtitle'); ?><?php echo app('translator')->getFromJson('glob.add_edit_payment'); ?><?php $__env->stopSection(); ?> 

<?php $__env->startSection('breadcrumb'); ?>

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> <?php echo app('translator')->getFromJson('glob.dashboard'); ?></a></li>
	<li class="active"><?php echo app('translator')->getFromJson('glob.payment'); ?></li>
</ol>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?> 
<div class="payment" >

	<?php if(Common::can_create('payment')): ?>

	<!-- Button div -->
	<div class="row">
		<div class="col-md-12 margin-bottom">
			<a  class="btn bg-purple " v-on:click="addNew"><?php echo app('translator')->getFromJson('glob.add_new'); ?></a>
		</div>
	</div>
	<!-- Button div end -->

	<div id="paymentFormDiv" style="display: none" >
		<div  class="row customer-box">  
			<div class="col-md-12">
				<div class="box box-primary">
					<div class="box-body">
					<form v-on:submit.prevent="savePayment(payment)" novalidate="">
							<div class="row">
								<div class="col-md-12">

									<div class="row">
										<!-- p date -->
										<div class="col-md-4">
											<div class="form-group">
												<label for=""><?php echo app('translator')->getFromJson('glob.p_date'); ?><span style="color:red">*</span></label>
												<div class="input-group">
													<div class="input-group-addon">
														<i class="ion ion-calendar"></i>
													</div>
													<input v-model="payment.payment_date" type="text" class="form-control input-sm datepicker" id="paymentDateId" placeholder="<?php echo app('translator')->getFromJson('glob.p_date'); ?>" required="">
												</div>
											</div>
										</div>
										<!-- end p date -->

										<!-- supplier -->
										<div class="col-md-5">
											<div class="form-group">
												<label><?php echo app('translator')->getFromJson('supplier.supplier'); ?> <span style="color:red">*</span></label></label>
												<v-select v-model="payment.supplier_id" :options="suppliers" :text="'name'" v-on:vchange="supplier_change" :vrequired="true"></v-select>
												
											</div>
										</div>
										<!-- end supplier -->


										<!-- against bill -->
										<div class="col-md-3">
											<div class="form-group">
												<label><?php echo app('translator')->getFromJson('glob.against_bill'); ?> <span style="color:red">*</span></label></label>
												<select v-model="payment.supplierPurchase" name="" id="supplier-bill_id" class="form-control input-sm" v-if="operation=='add'" v-on:change="payment.paid_amount = payment.supplierPurchase.credit_amount" required>
													<option value=""></option>
													<option v-for="s in supplierPurchases" :value="s">{{  s.bill_no }}</option>
												</select>

												<input type="text" v-model="payment.supplierPurchase.bill_no" name="" class="form-control input-sm" v-if="operation=='edit'" disabled="">
											</div>
										</div>
									</div>
									<!-- .row -->

									<div class="row">

										
										<div class="col-md-3">
											<div class="form-group">
												<label><?php echo app('translator')->getFromJson('glob.mode'); ?>  <span style="color:red">*</span></label></label> 
												<div class="input-group">
													<div class="input-group-addon">
														<i class="fa fa-th-list"></i>
													</div>
													<select v-model="payment.mode" name="" class="form-control input-sm" id="" v-on:change="chequeValidation(payment.mode)" required="">
														<option value=""></option>
														<option value="Cash">Cash</option>
														<option value="Cheque">Cheque</option>
														<option value="Credit card">Credit card</option>
													</select>
												</div>
											</div>
										</div>
										

										<!-- bank -->
										<div class="col-md-3">
											<div class="form-group">
												<label for=""><?php echo app('translator')->getFromJson('glob.bank'); ?><span v-if="payment.mode=='Cheque'" style="color:red">*</span></label>
												<div class="input-group">
													<div class="input-group-addon">
														<i class="fa fa-bank"></i>
													</div>
													<select v-model="payment.bank_id" name="" id="" class="form-control isCheque input-sm" >
														<option value=""></option>
														<option v-for="bank in banks" :value="bank.id">{{ bank.name }}</option>
													</select>
												</div>
											</div>
										</div>
										<!-- end bank -->

										<div class="col-md-3">
											<div class="form-group">
												<label for=""><?php echo app('translator')->getFromJson('glob.ch_dr_no'); ?><span style="color:red" v-if="payment.mode=='Cheque'">*</span></label>
												<div class="input-group">
													<div class="input-group-addon">
														<i class="fa fa-list-alt"></i>
													</div>
													<input v-model="payment.ch_no" type="text" class="form-control isCheque input-sm " placeholder="<?php echo app('translator')->getFromJson('glob.ch_dr_no'); ?>" >
												</div>
											</div>
										</div>

										<div class="col-md-3">
											<div class="form-group">
												<label for=""><?php echo app('translator')->getFromJson('glob.ch_dr_date'); ?><span v-if="payment.mode=='Cheque'" style="color:red">*</span></label>
												<div class="input-group">
													<div class="input-group-addon">
														<i class="fa fa-list-alt"></i>
													</div>
													<input v-model="payment.ch_date" id="chequeDateId" type="text" class="form-control isCheque  input-sm datepicker" placeholder="<?php echo app('translator')->getFromJson('glob.ch_dr_date'); ?>" >
												</div>
											</div>
										</div>

									</div>
									<!-- .row -->

									<div class="row">

										<div class="col-md-3">
											<div class="form-group">
												<label for=""><?php echo app('translator')->getFromJson('glob.actual_amt'); ?><span style="color:red">*</span></label>
												<div class="input-group">
													<div class="input-group-addon">
														<i class="fa fa-money"></i>
													</div>
													<input v-model="payment.supplierPurchase.actual_amount" type="text" class="form-control  input-sm " placeholder="<?php echo app('translator')->getFromJson('glob.actual_amt'); ?>" disabled="">
												</div>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label for=""><?php echo app('translator')->getFromJson('glob.return_amt'); ?><span style="color:red">*</span></label>
												<div class="input-group">
													<div class="input-group-addon">
														<i class="fa fa-money"></i>
													</div>
													<input  v-model="payment.supplierPurchase.return_amount" type="text" class="form-control  input-sm " placeholder="<?php echo app('translator')->getFromJson('glob.return_amt'); ?>"  disabled>
												</div>
											</div>
										</div>

										<div class="col-md-3">
											<div class="form-group">
												<label for=""><?php echo app('translator')->getFromJson('glob.paid_amount'); ?><span style="color:red">*</span></label>
												<div class="input-group">
													<div class="input-group-addon">
														<i class="fa fa-money"></i>
													</div>
													<input v-model="payment.supplierPurchase.total_paid_amount" type="text" class="form-control  input-sm " placeholder="<?php echo app('translator')->getFromJson('glob.paid_amount'); ?>" required="" readonly="">
												</div>
											</div>
										</div>

										<div class="col-md-3">
											<div class="form-group">
												<label for=""><?php echo app('translator')->getFromJson('glob.pay_amt'); ?><span style="color:red">*</span></label>
												<div class="input-group">
													<div class="input-group-addon">
														<i class="fa fa-money"></i>
													</div>
													<input v-model="payment.paid_amount" type="text" class="form-control  input-sm " placeholder="<?php echo app('translator')->getFromJson('glob.pay_amt'); ?>" required="">
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												<label for=""><?php echo app('translator')->getFromJson('glob.remark'); ?> </label>
												<textarea v-model="payment.remark" name="address" id=""   rows="2" class="form-control  input-sm fix-textarea" placeholder="<?php echo app('translator')->getFromJson('glob.remark'); ?>" ></textarea>
											</div>
										</div>
									</div>
								</div>
							</div>
							<!-- .row -->

							<div class="row">
								<div class="col-md-12">
									<button class="btn btn-primary" v-if="operation=='add'" id="btnSave"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?php echo app('translator')->getFromJson('glob.save'); ?></button>
									<button class="btn btn-primary" v-if="operation=='edit'" id="btnUpdate"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?php echo app('translator')->getFromJson('glob.update'); ?></button>
									<a class="btn btn-danger" v-on:click="cancel()"> <?php echo app('translator')->getFromJson('glob.cancel'); ?></a>
									<a class="btn btn-cyan" v-on:click="reset()"><i class="fa fa-refresh" aria-hidden="true"></i> <?php echo app('translator')->getFromJson('glob.reset'); ?></a>
									<button type="button" class="btn btn-indigo" v-on:click="printPage()" ><i class="fa fa-print" aria-hidden="true"></i> <?php echo app('translator')->getFromJson('glob.print_voucher'); ?></button>
								</div>
							</div>
							<!-- .row -->
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php endif; ?>

	<!-- view table data  -->
	<div class="row" id="paymentTable">
		<?php echo $__env->make("pages.financial.payment.payment-table", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
	</div>
	<!-- view table data end -->



</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>