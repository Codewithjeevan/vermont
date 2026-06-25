
<div class="col-md-12">
	<div class="box box-primary">
		<div class="box-header with-border">
			<h3 class="box-title"><?php echo app('translator')->getFromJson('glob.general_options'); ?></h3>
			<div class="box-tools pull-right">
				<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
				</button>
			</div>
		</div>
		<!-- /.box-header -->
		<div class="box-body " style="display: block;">
			<ul class="list-group">
				<li class="list-group-item">
					<div class="row">
						<div class="col-md-6">
							<label><input type="checkbox" name="genAllcheck" v-model="genAllcheck" v-on:click="generalAllcheck()"> <?php echo app('translator')->getFromJson('glob.check_all'); ?></label>
						</div>
						<div class="col-md-6">
							<label><input type="checkbox" name="genKeyboard" v-model="genKeyboard" v-on:click="mangeKeyboard()"> <?php echo app('translator')->getFromJson('glob.v_keyboard'); ?></label>
						</div>
					</div>
				</li>
				<li class="list-group-item">
					<label> <?php echo app('translator')->getFromJson('glob.change_com_port'); ?></label>
					<div class="input-group">
						<span class="input-group-addon" id="basic-addon1">COM</span>
						<input type="text" class="form-control input-sm" v-model="com_port" style="width:10%" maxlength="2">
					</div>
				</li>
				<li class="list-group-item">
					<label><input type="checkbox" v-model="generalButtons.enable_sms_billing"> <?php echo app('translator')->getFromJson('glob.enable_sms_billing'); ?></label>
				</li>
			</ul>
		</div>
	</div>
</div>

<div class="col-md-12">
	<div class="row">
		
		<div class="col-md-6">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title"><?php echo app('translator')->getFromJson('glob.register_address'); ?></h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
						</button>
					</div>
				</div>
				<!-- /.box-header -->
				<div class="box-body " style="display: block;">
					<div class="row">
						<div class="col-md-8">
							<div class="row">
								
								<div class="col-md-12">
									<label><?php echo app('translator')->getFromJson('glob.mall_name'); ?> :
										<?php if(Common::can_update('options') || Common::can_create('options')): ?> 
										<a  v-if="mall_name_action" class="btn btn-xs btn-info btn-circle" v-on:click="mallNameAction">
											<i class="fa fa-pencil" aria-hidden="true"></i>
										</a>
										<a  v-if="!mall_name_action" v-on:click="mallNameAction" class="btn btn-xs btn-danger btn-circle ">
											<i class="fa fa-times" aria-hidden="true"></i>
										</a>
										<?php endif; ?>	
									</label>
									<pre v-if="mall_name_action" class="well" >{{generalButtons.mall_name}}</pre>
									<input v-if="!mall_name_action" type="text" class="form-control  input-sm" placeholder="<?php echo app('translator')->getFromJson('glob.mall_name'); ?>" v-model="generalButtons.mall_name">
								</div>
								
								
								<div class="col-md-12">
									<label><?php echo app('translator')->getFromJson('glob.telephone'); ?> :
										<?php if(Common::can_update('options') || Common::can_create('options')): ?> 
										<a  v-if="mall_telephone_action" class="btn btn-xs btn-info btn-circle" v-on:click="malltelephoneAction">
											<i class="fa fa-pencil" aria-hidden="true"></i>
										</a>
										<a  v-if="!mall_telephone_action" v-on:click="malltelephoneAction" class="btn btn-xs btn-danger btn-circle ">
											<i class="fa fa-times" aria-hidden="true"></i>
										</a>
										<?php endif; ?>	
									</label>
									<pre v-if="mall_telephone_action" class="well" >{{generalButtons.mall_telephone}}</pre>
									<input id="general-phone" v-if="!mall_telephone_action" type="text" class="form-control input-sm validate-number" placeholder="<?php echo app('translator')->getFromJson('glob.telephone'); ?>" v-model="generalButtons.mall_telephone" maxlength="10" v-on:change="validatePhone">
								</div>
								
							</div>
						</div>
						
						<div class="col-md-4">
							<div class="profile-action">
								<img id="productImg" :src="generalButtons.logo" class="img-thumbnail img-responsive logo"  style="margin:auto">
								<a class="change-logo" style="color: black" v-on:click="uploadLogo"><i class="fa fa-camera fa-lg" aria-hidden="true" ></i></a>
							</div>
						</div>
						
					</div>
					<div class="row" >

						
						<div class="col-md-12">
							<label><?php echo app('translator')->getFromJson('glob.email'); ?> :
								<?php if(Common::can_update('options') || Common::can_create('options')): ?> 
								<a  v-if="mall_email_action" class="btn btn-xs btn-info btn-circle" v-on:click="mallEmailAction">
									<i class="fa fa-pencil" aria-hidden="true"></i>
								</a>
								<a  v-if="!mall_email_action" v-on:click="mallEmailAction" class="btn btn-xs btn-danger btn-circle ">
									<i class="fa fa-times" aria-hidden="true"></i>
								</a>
								<?php endif; ?>	
							</label>
							<pre v-if="mall_email_action" class="well" >{{generalButtons.mall_email}}</pre>
							<input id="general-email" v-if="!mall_email_action" type="email" class="form-control  input-sm" placeholder="<?php echo app('translator')->getFromJson('glob.email'); ?>" v-model="generalButtons.mall_email" v-on:change="validateEmail">
						</div>
						
					</div>

					<div class="row" >

						
						<div class="col-md-12">
							<label><?php echo app('translator')->getFromJson('glob.website'); ?> :
								<?php if(Common::can_update('options') || Common::can_create('options')): ?> 
								<a  v-if="mall_website_action" class="btn btn-xs btn-info btn-circle" v-on:click="mallWebsiteAction">
									<i class="fa fa-pencil" aria-hidden="true"></i>
								</a>
								<a  v-if="!mall_website_action" v-on:click="mallWebsiteAction" class="btn btn-xs btn-danger btn-circle ">
									<i class="fa fa-times" aria-hidden="true"></i>
								</a>
								<?php endif; ?>	
							</label>
							<pre v-if="mall_website_action" class="well" >{{generalButtons.mall_website}}</pre>
							<input v-if="!mall_website_action" type="text" class="form-control  input-sm" placeholder="<?php echo app('translator')->getFromJson('glob.website'); ?>" v-model="generalButtons.mall_website">
						</div>
						
					</div>

					<div class="row" style="margin-top: 15px;">
						<!-- address start -->
						<div class="col-md-12" v-show="show_register_address">
							<label for=""><?php echo app('translator')->getFromJson('glob.address'); ?> : 
								<?php if(Common::can_update('options') || Common::can_create('options')): ?>
								<a  class="btn btn-xs btn-info btn-circle" v-on:click="editRegAddress"><i class="fa fa-pencil" aria-hidden="true"></i></a>
								<?php endif; ?>	
							</label>
							<address v-html="generalButtons.register_address" class="well"></address>
						</div>
						<div class="col-md-12" v-show="edit_register_address">
							<label for=""><?php echo app('translator')->getFromJson('glob.address'); ?>: <a  v-on:click="cancelRegEdit" class="btn btn-xs btn-danger btn-circle "><i class="fa fa-times" aria-hidden="true"></i></a> </label>
							<textarea id="address" class="textarea form-control" name="address"   rows="4" class="form-control  input-sm fix-textarea"  >  <?php if(isset($res['options']['general']) && $res['options']['general']['register_address']!== null): ?> <?php echo $res['options']['general']['register_address']; ?> <?php endif; ?></textarea>
						</div>
						<!-- end address start  -->
					</div>

					<div class="row" style="margin-top: 15px;">
						<!-- footer start -->
						<div class="col-md-12" v-show="show_change_footer">
							<label for=""><?php echo app('translator')->getFromJson('glob.footer'); ?> : 
								<?php if(Common::can_update('options') || Common::can_create('options')): ?>
								<a  class="btn btn-xs btn-info btn-circle" v-on:click="editChangeFooter"><i class="fa fa-pencil" aria-hidden="true"></i></a>
								<?php endif; ?>	
							</label>
							<address v-html="generalButtons.footer" class="well"></address>
						</div>
						<div class="col-md-12" v-show="edit_change_footer">
							<label for=""><?php echo app('translator')->getFromJson('glob.footer'); ?>: <a  v-on:click="cancelChangeFooter" class="btn btn-xs btn-danger btn-circle "><i class="fa fa-times" aria-hidden="true"></i></a> </label>
							<textarea id="footer" class="textarea form-control" name="footer"   rows="4" class="form-control  input-sm fix-textarea"  > <?php if(isset($res['options']['general'])): ?> <?php if( isset($res['options']['general']['footer']) && $res['options']['general']['footer']!== null): ?> <?php echo $res['options']['general']['footer']; ?> <?php endif; ?> <?php endif; ?></textarea>
						</div>
						<!-- end footer start  -->
					</div>

				</div>
			</div>
		</div>

		
		<div class="col-md-6">
			<div class="row">
				<div class="col-md-12">
					<div class="box box-primary">
						<div class="box-header with-border">
							<h3 class="box-title"><?php echo app('translator')->getFromJson('glob.change_sms_message'); ?></h3>
							<div class="box-tools pull-right">
								<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
								</button>
							</div>
						</div>
						<!-- /.box-header  sms change-->
						<div class="box-body " style="display: block;">
							<div class="col-md-12" v-show="show_change_SMS_message">
								<label for=""><?php echo app('translator')->getFromJson('glob.sms_message'); ?> : 
									<?php if(Common::can_update('options') || Common::can_create('options')): ?>
									<a  class="btn btn-xs btn-info btn-circle" v-on:click="editChangeSms"><i class="fa fa-pencil" aria-hidden="true"></i></a>
									<?php endif; ?>	
								</label>
								<div v-html="generalButtons.change_sms_message" class="well"></div>
							</div>
							<div class="col-md-12" v-show="edit_change_SMS_message">
								<label for=""><?php echo app('translator')->getFromJson('glob.sms_message'); ?>: <a  v-on:click="cancelChangeSms" class="btn btn-xs btn-danger btn-circle "><i class="fa fa-times" aria-hidden="true"></i></a> </label>
								<textarea id="change_sms" class="textarea form-control" name="change_sms"    rows="4" class="form-control  input-sm fix-textarea" > <?php if(isset($res['options']['general']) && $res['options']['general']['change_sms_message']!== null): ?> <?php echo $res['options']['general']['change_sms_message']; ?> <?php endif; ?> </textarea>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-12">
					<div class="box box-primary">
						<div class="box-header with-border">
							<h3 class="box-title"><?php echo app('translator')->getFromJson('glob.tnc'); ?></h3>
							<div class="box-tools pull-right">
								<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
								</button>
							</div>
						</div>
						<!-- /.box-header  t&c english-->
						<div class="box-body " style="display: block;">
							<div class="col-md-12">
								
								<div class="row">
									<div class="col-md-12" v-show="show_change_tnc_english">
										<label for=""><?php echo app('translator')->getFromJson('glob.tncEng'); ?> : 
											<?php if(Common::can_update('options') || Common::can_create('options')): ?>
											<a  class="btn btn-xs btn-info btn-circle" v-on:click="editChangeTncEng"><i class="fa fa-pencil" aria-hidden="true"></i></a>
											<?php endif; ?>	
										</label>
										<div v-html="generalButtons.tnc_eng" class="well"></div>
									</div>
									<div class="col-md-12" v-show="edit_change_tnc_english">
										<label for=""><?php echo app('translator')->getFromJson('glob.tncEng'); ?>: <a  v-on:click="cancelChangeTncEng" class="btn btn-xs btn-danger btn-circle "><i class="fa fa-times" aria-hidden="true"></i></a> </label>
										<textarea id="tnc_eng" class="textarea form-control" name="tnc_eng"    rows="4" class="form-control  input-sm fix-textarea" > <?php if(isset($res['options']['general'])): ?> <?php if( isset($res['options']['general']['tnc_eng']) && $res['options']['general']['tnc_eng']!== null): ?> <?php echo $res['options']['general']['tnc_eng']; ?> <?php endif; ?> <?php endif; ?> </textarea>
									</div>
								</div>
								
								
								<div class="row">
									<div class="col-md-12" v-show="show_change_tnc_arabic">
										<label for=""><?php echo app('translator')->getFromJson('glob.tncAre'); ?> : 
											<?php if(Common::can_update('options') || Common::can_create('options')): ?>
											<a  class="btn btn-xs btn-info btn-circle" v-on:click="editChangeTncAr"><i class="fa fa-pencil" aria-hidden="true"></i></a>
											<?php endif; ?>	
										</label>
										<div v-html="generalButtons.tnc_ar" class="well"></div>
									</div>
									<div class="col-md-12" v-show="edit_change_tnc_arabic">
										<label for=""><?php echo app('translator')->getFromJson('glob.tncAre'); ?>: <a  v-on:click="cancelChangeTncAr" class="btn btn-xs btn-danger btn-circle "><i class="fa fa-times" aria-hidden="true"></i></a> </label>
										<textarea id="tnc_ar" class="textarea form-control" name="tnc_ar"    rows="4" class="form-control  input-sm fix-textarea" > <?php if(isset($res['options']['general'])): ?> <?php if( isset($res['options']['general']['tnc_ar']) && $res['options']['general']['tnc_ar']!== null): ?> <?php echo $res['options']['general']['tnc_ar']; ?> <?php endif; ?> <?php endif; ?> </textarea>
									</div>
								</div>
								
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<div class="col-md-12">
	<div style="margin:5px;">
		<?php if(Common::can_update('options') || Common::can_create('options')): ?>
		<a 	class="btn btn-primary" v-on:click="saveGeneral(generalButtons,$event)"><i class="fa fa-refresh"></i> <?php echo app('translator')->getFromJson('glob.save'); ?></a>
		<?php endif; ?>
	</div>
</div>
