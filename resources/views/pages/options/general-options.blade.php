
<div class="col-md-12">
	<div class="box box-primary">
		<div class="box-header with-border">
			<h3 class="box-title">@lang('glob.general_options')</h3>
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
							<label><input type="checkbox" name="genAllcheck" v-model="genAllcheck" v-on:click="generalAllcheck()"> @lang('glob.check_all')</label>
						</div>
						<div class="col-md-6">
							<label><input type="checkbox" name="genKeyboard" v-model="genKeyboard" v-on:click="mangeKeyboard()"> @lang('glob.v_keyboard')</label>
						</div>
					</div>
				</li>
				<li class="list-group-item">
					<label>{{-- <input type="checkbox" v-model="generalButtons.change_com_port"> --}} @lang('glob.change_com_port')</label>
					<div class="input-group">
						<span class="input-group-addon" id="basic-addon1">COM</span>
						<input type="text" class="form-control input-sm" v-model="com_port" style="width:10%" maxlength="2">
					</div>
				</li>
				<li class="list-group-item">
					<label><input type="checkbox" v-model="generalButtons.enable_sms_billing"> @lang('glob.enable_sms_billing')</label>
				</li>
			</ul>
		</div>
	</div>
</div>

<div class="col-md-12">
	<div class="row">
		{{-- adderess --}}
		<div class="col-md-6">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title">@lang('glob.register_address')</h3>
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
								{{-- mall name --}}
								<div class="col-md-12">
									<label>@lang('glob.mall_name') :
										@if(Common::can_update('options') || Common::can_create('options')) 
										<a  v-if="mall_name_action" class="btn btn-xs btn-info btn-circle" v-on:click="mallNameAction">
											<i class="fa fa-pencil" aria-hidden="true"></i>
										</a>
										<a  v-if="!mall_name_action" v-on:click="mallNameAction" class="btn btn-xs btn-danger btn-circle ">
											<i class="fa fa-times" aria-hidden="true"></i>
										</a>
										@endif	
									</label>
									<pre v-if="mall_name_action" class="well" >@{{generalButtons.mall_name}}</pre>
									<input v-if="!mall_name_action" type="text" class="form-control  input-sm" placeholder="@lang('glob.mall_name')" v-model="generalButtons.mall_name">
								</div>
								{{-- end mall name --}}
								{{-- mall telephone number  --}}
								<div class="col-md-12">
									<label>@lang('glob.telephone') :
										@if(Common::can_update('options') || Common::can_create('options')) 
										<a  v-if="mall_telephone_action" class="btn btn-xs btn-info btn-circle" v-on:click="malltelephoneAction">
											<i class="fa fa-pencil" aria-hidden="true"></i>
										</a>
										<a  v-if="!mall_telephone_action" v-on:click="malltelephoneAction" class="btn btn-xs btn-danger btn-circle ">
											<i class="fa fa-times" aria-hidden="true"></i>
										</a>
										@endif	
									</label>
									<pre v-if="mall_telephone_action" class="well" >@{{generalButtons.mall_telephone}}</pre>
									<input id="general-phone" v-if="!mall_telephone_action" type="text" class="form-control input-sm validate-number" placeholder="@lang('glob.telephone')" v-model="generalButtons.mall_telephone" maxlength="10" v-on:change="validatePhone">
								</div>
								{{-- end mall telephone number --}}
							</div>
						</div>
						{{-- logo image --}}
						<div class="col-md-4">
							<div class="profile-action">
								<img id="productImg" :src="generalButtons.logo" class="img-thumbnail img-responsive logo"  style="margin:auto">
								<a class="change-logo" style="color: black" v-on:click="uploadLogo"><i class="fa fa-camera fa-lg" aria-hidden="true" ></i></a>
							</div>
						</div>
						{{-- end logo image --}}
					</div>
					<div class="row" >

						{{-- mall email id --}}
						<div class="col-md-12">
							<label>@lang('glob.email') :
								@if(Common::can_update('options') || Common::can_create('options')) 
								<a  v-if="mall_email_action" class="btn btn-xs btn-info btn-circle" v-on:click="mallEmailAction">
									<i class="fa fa-pencil" aria-hidden="true"></i>
								</a>
								<a  v-if="!mall_email_action" v-on:click="mallEmailAction" class="btn btn-xs btn-danger btn-circle ">
									<i class="fa fa-times" aria-hidden="true"></i>
								</a>
								@endif	
							</label>
							<pre v-if="mall_email_action" class="well" >@{{generalButtons.mall_email}}</pre>
							<input id="general-email" v-if="!mall_email_action" type="email" class="form-control  input-sm" placeholder="@lang('glob.email')" v-model="generalButtons.mall_email" v-on:change="validateEmail">
						</div>
						{{-- end mall email id --}}
					</div>

					<div class="row" >

						{{-- mall email id --}}
						<div class="col-md-12">
							<label>@lang('glob.website') :
								@if(Common::can_update('options') || Common::can_create('options')) 
								<a  v-if="mall_website_action" class="btn btn-xs btn-info btn-circle" v-on:click="mallWebsiteAction">
									<i class="fa fa-pencil" aria-hidden="true"></i>
								</a>
								<a  v-if="!mall_website_action" v-on:click="mallWebsiteAction" class="btn btn-xs btn-danger btn-circle ">
									<i class="fa fa-times" aria-hidden="true"></i>
								</a>
								@endif	
							</label>
							<pre v-if="mall_website_action" class="well" >@{{generalButtons.mall_website}}</pre>
							<input v-if="!mall_website_action" type="text" class="form-control  input-sm" placeholder="@lang('glob.website')" v-model="generalButtons.mall_website">
						</div>
						{{-- end mall email id --}}
					</div>

					<div class="row" style="margin-top: 15px;">
						<!-- address start -->
						<div class="col-md-12" v-show="show_register_address">
							<label for="">@lang('glob.address') : 
								@if(Common::can_update('options') || Common::can_create('options'))
								<a  class="btn btn-xs btn-info btn-circle" v-on:click="editRegAddress"><i class="fa fa-pencil" aria-hidden="true"></i></a>
								@endif	
							</label>
							<address v-html="generalButtons.register_address" class="well"></address>
						</div>
						<div class="col-md-12" v-show="edit_register_address">
							<label for="">@lang('glob.address'): <a  v-on:click="cancelRegEdit" class="btn btn-xs btn-danger btn-circle "><i class="fa fa-times" aria-hidden="true"></i></a> </label>
							<textarea id="address" class="textarea form-control" name="address"   rows="4" class="form-control  input-sm fix-textarea"  >  @if(isset($res['options']['general']) && $res['options']['general']['register_address']!== null) {!! $res['options']['general']['register_address'] !!} @endif</textarea>
						</div>
						<!-- end address start  -->
					</div>

					<div class="row" style="margin-top: 15px;">
						<!-- footer start -->
						<div class="col-md-12" v-show="show_change_footer">
							<label for="">@lang('glob.footer') : 
								@if(Common::can_update('options') || Common::can_create('options'))
								<a  class="btn btn-xs btn-info btn-circle" v-on:click="editChangeFooter"><i class="fa fa-pencil" aria-hidden="true"></i></a>
								@endif	
							</label>
							<address v-html="generalButtons.footer" class="well"></address>
						</div>
						<div class="col-md-12" v-show="edit_change_footer">
							<label for="">@lang('glob.footer'): <a  v-on:click="cancelChangeFooter" class="btn btn-xs btn-danger btn-circle "><i class="fa fa-times" aria-hidden="true"></i></a> </label>
							<textarea id="footer" class="textarea form-control" name="footer"   rows="4" class="form-control  input-sm fix-textarea"  > @if(isset($res['options']['general'])) @if( isset($res['options']['general']['footer']) && $res['options']['general']['footer']!== null) {!! $res['options']['general']['footer'] !!} @endif @endif</textarea>
						</div>
						<!-- end footer start  -->
					</div>

				</div>
			</div>
		</div>

		{{-- adderess --}}
		<div class="col-md-6">
			<div class="row">
				<div class="col-md-12">
					<div class="box box-primary">
						<div class="box-header with-border">
							<h3 class="box-title">@lang('glob.change_sms_message')</h3>
							<div class="box-tools pull-right">
								<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
								</button>
							</div>
						</div>
						<!-- /.box-header  sms change-->
						<div class="box-body " style="display: block;">
							<div class="col-md-12" v-show="show_change_SMS_message">
								<label for="">@lang('glob.sms_message') : 
									@if(Common::can_update('options') || Common::can_create('options'))
									<a  class="btn btn-xs btn-info btn-circle" v-on:click="editChangeSms"><i class="fa fa-pencil" aria-hidden="true"></i></a>
									@endif	
								</label>
								<div v-html="generalButtons.change_sms_message" class="well"></div>
							</div>
							<div class="col-md-12" v-show="edit_change_SMS_message">
								<label for="">@lang('glob.sms_message'): <a  v-on:click="cancelChangeSms" class="btn btn-xs btn-danger btn-circle "><i class="fa fa-times" aria-hidden="true"></i></a> </label>
								<textarea id="change_sms" class="textarea form-control" name="change_sms"    rows="4" class="form-control  input-sm fix-textarea" > @if(isset($res['options']['general']) && $res['options']['general']['change_sms_message']!== null) {!! $res['options']['general']['change_sms_message'] !!} @endif </textarea>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-12">
					<div class="box box-primary">
						<div class="box-header with-border">
							<h3 class="box-title">@lang('glob.tnc')</h3>
							<div class="box-tools pull-right">
								<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
								</button>
							</div>
						</div>
						<!-- /.box-header  t&c english-->
						<div class="box-body " style="display: block;">
							<div class="col-md-12">
								{{-- t&c english --}}
								<div class="row">
									<div class="col-md-12" v-show="show_change_tnc_english">
										<label for="">@lang('glob.tncEng') : 
											@if(Common::can_update('options') || Common::can_create('options'))
											<a  class="btn btn-xs btn-info btn-circle" v-on:click="editChangeTncEng"><i class="fa fa-pencil" aria-hidden="true"></i></a>
											@endif	
										</label>
										<div v-html="generalButtons.tnc_eng" class="well"></div>
									</div>
									<div class="col-md-12" v-show="edit_change_tnc_english">
										<label for="">@lang('glob.tncEng'): <a  v-on:click="cancelChangeTncEng" class="btn btn-xs btn-danger btn-circle "><i class="fa fa-times" aria-hidden="true"></i></a> </label>
										<textarea id="tnc_eng" class="textarea form-control" name="tnc_eng"    rows="4" class="form-control  input-sm fix-textarea" > @if(isset($res['options']['general'])) @if( isset($res['options']['general']['tnc_eng']) && $res['options']['general']['tnc_eng']!== null) {!! $res['options']['general']['tnc_eng'] !!} @endif @endif </textarea>
									</div>
								</div>
								{{--end  t&c English  --}}
								{{-- t&c arabic --}}
								<div class="row">
									<div class="col-md-12" v-show="show_change_tnc_arabic">
										<label for="">@lang('glob.tncAre') : 
											@if(Common::can_update('options') || Common::can_create('options'))
											<a  class="btn btn-xs btn-info btn-circle" v-on:click="editChangeTncAr"><i class="fa fa-pencil" aria-hidden="true"></i></a>
											@endif	
										</label>
										<div v-html="generalButtons.tnc_ar" class="well"></div>
									</div>
									<div class="col-md-12" v-show="edit_change_tnc_arabic">
										<label for="">@lang('glob.tncAre'): <a  v-on:click="cancelChangeTncAr" class="btn btn-xs btn-danger btn-circle "><i class="fa fa-times" aria-hidden="true"></i></a> </label>
										<textarea id="tnc_ar" class="textarea form-control" name="tnc_ar"    rows="4" class="form-control  input-sm fix-textarea" > @if(isset($res['options']['general'])) @if( isset($res['options']['general']['tnc_ar']) && $res['options']['general']['tnc_ar']!== null) {!! $res['options']['general']['tnc_ar'] !!} @endif @endif </textarea>
									</div>
								</div>
								{{--end t&c arabic --}}
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
		@if(Common::can_update('options') || Common::can_create('options'))
		<a 	class="btn btn-primary" v-on:click="saveGeneral(generalButtons,$event)"><i class="fa fa-refresh"></i> @lang('glob.save')</a>
		@endif
	</div>
</div>
