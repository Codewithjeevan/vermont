	<form  @submit.prevent="ItemSave(item, opType)"  >
		<div class="row customer-box">
			<div class="col-md-12">

				<div class="box box-primary">
					<div class="box-body">
						<div class="row">

							<div class="col-md-9">

								<div class="row">
									<!-- s. no. -->
									<div class="col-md-4">
										{!! Form::label('title', trans('item.s_no')) !!} 
										<span>
											@{{ newId }}
										</span>
									</div>
									<!-- .row -->
								</div>

								<div class="row">

									<!-- parent category -->
									<div class="col-md-4">

										<div class="form-group">
											{!! Form::label('title', trans('glob.category')) !!}<label></label>
											
											<v-select  v-model="parentCategory"  :options="categories" :text="'categorie'" :vparent="0" :vcheck="'parent_category'">
												<option value="">Select</option>
											</v-select>

										</div>
									</div>
									<!-- End parent category -->

									<!-- category -->
									<div class="col-md-4">

										<div class="form-group">
											{!! Form::label('title', trans('glob.sub_category')) !!}<label><span style="color:red">*</span></label>

											<v-select  v-model="item.category" :vrequired="true" :options="categories" :text="'categorie'" :vparent="parentCategory" :vcheck="'parent_category'">
												<option value="">Select</option>
											</v-select>

											
										</div>
									</div>
									<!-- End category -->

									<!--  barcode-->
									<div class="col-md-4">
										<div class="form-group">
											{!! Form::label('title', trans('glob.barcode')) !!}<label><span style="color:red">*</span></label>
											<div class="input-group">
												<div class="input-group-addon"><i class="fa fa-barcode"></i> </div>
												<input type="text" name="barcode" class="form-control input-sm barcode" placeholder="{{ trans('glob.barcode') }}" v-model.trim="item.barcode"  maxlength="16" v-validate:barcode="'required|max:16'" v-on:change="createImageUrl(item.barcode)" required>
											</div><span v-if="barcodeDuplicate" class="help is-danger">Entered Barcode is already present</span>
										</div>
										<!-- End barcode -->
									</div>
								</div>
								<!-- .row -->
								<div class="row">

									<!--  item/product -->
									<div class="col-md-6">
										<div class="form-group">
											{!! Form::label('title', trans('item.item_product')) !!}<label><span style="color:red">*</span></label>
											<div class="input-group">
												<div class="input-group-addon"><i class="icon ion-bag"></i></div>
												<input  name="item" class="form-control input-sm" placeholder="{{ trans('item.item_product') }}" v-model.trim="item.itemName"  required>
											</div>
										</div>
									</div>
									<!-- End item/product -->

									<!--  item/product -->
									<div class="col-md-6">
										<div class="form-group">
											{!! Form::label('title', trans('item.item_product_arabic')) !!}
											<div class="input-group">
												<div class="input-group-addon"><i class="icon ion-bag"></i></div>
												<input  name="item_arabic" class="form-control input-sm  text-right " dir="rtl" placeholder="{{ trans('item.item_product_arabic') }}" v-model="item.itemNameArabic" >
											</div>
										</div>
									</div>
									<!-- End item/product -->
								</div>

								<div class="row">

									<!-- product code -->
									<div class="col-md-3">
										<div class="form-group">
											{!! Form::label('title', trans('item.product_code')) !!}<label>
											<div class="input-group">
												<div class="input-group-addon"><i class="icon ion-ios-pricetags"></i></div>
												<input type="text" name="productCode" class="input-sm form-control" placeholder="@lang("item.product_code")" v-model="item.productCode" :class="{'input': true, 'is-danger': errors.has('productCode') }">
											</div>
											
											<span v-if="productCodeduplicate" class="help is-danger">Entered Product code is already present</span>
										</div>
									</div>
									<!-- product code -->

									<!-- Serial code -->
									<div class="col-md-3">
										<div class="form-group">
											{!! Form::label('title', trans('item.refrence_number')) !!}<label><span style="color:red">*</span></label>
											<div class="input-group">
												<div class="input-group-addon"><i class="icon ion-ios-pricetags"></i></div>
												<input type="text" name="refrence_number" class="input-sm form-control" placeholder="@lang("item.refrence_number")" v-model.trim="item.serial_number" required>
											</div>
											<span v-show="showErrors && errors.has('refrence_number')" class="help is-danger">@{{ errors.first('cp') }}</span>
										</div>
									</div>
									<!-- Serial code -->
									@if(Common::can_option_view('item','wholesale'))
									<!-- C.P -->
									<div class="col-md-6 sp-padding">
										<div class="col-md-12 sp-padding">
											<div class="col-md-3 sp-padding" >
												<div class="form-group " >
													{!! Form::label('title', trans('item.c_p')) !!}<label></label>
													<div class="input-group">
														<input type="text" name="cp" class="form-control input-sm" placeholder="{{ trans('item.c_p') }}" v-model="item.costPrise" v-on:change="checkPrise()"   maxlength="12"  v-validate:cp="'decimal|max:12'" >
														{{-- {!! Form::number('costPrise', null, array('required', 'class'=>'form-control input-sm', 'placeholder'=>trans('item.c_p'), "v-model" => "costPrise")) !!} --}}
													</div>
													<span v-show="showErrors && errors.has('cp')" class="help is-danger">@{{ errors.first('cp') }}</span>
												</div>
											</div>
											<!-- End C.P -->
											<!--C.P vat-->
											@if(Common::vatType('vat','vat_type')!=0)
											<div class="col-md-3 sp-padding">
												<div class="form-group" >
													{!! Form::label('title', trans('item.cp_vat_percent')) !!}
													<div class="input-group ">
														
														<input type="text" name="cp_vat_amount" class="form-control input-sm decimal" placeholder="{{ trans('item.cp_vat_percent') }}" v-model.lazy="item.cp_vat" maxlength="" v-validate:cp_vat_amount="'decimal|max:8|between:0,100'">
														
													</div>
													<span v-show="showErrors && errors.has('cp_vat_amount')" class="help is-danger">@{{ errors.first('cp_vat_amount') }}</span>
												</div>
											</div>
											@endIf
											<!--C.P vat-->

											<!-- S.P -->
											<div class="col-md-3 sp-padding" >
												<div class="form-group" >
													{!! Form::label('title', trans('item.s_p')) !!}<label><span style="color:red">*</span></label>
													<div class="input-group">
														<input type="text" name="spr" class="form-control input-sm decimal" placeholder="{{ trans('item.s_p') }}" v-model="item.salePrise" v-on:change="checkPrise()"  maxlength="12" v-validate:sp="'decimal|max:12'" required>
														
													</div>
													<span v-show="showErrors && errors.has('spr')" class="help is-danger">@{{ errors.first('spr') }}</span>
												</div>
											</div>
											<!-- End S.P -->
											<!-- VAT-->
											@if(Common::vatType('vat','vat_type')!=0)
											<div class="col-md-3 sp-padding">
												<div class="form-group" >
													{!! Form::label('title', trans('glob.sp_vat')) !!}
													<div class="input-group ">
														
														<input type="text" name="vat_amount" class="form-control input-sm decimal" placeholder="{{ trans('glob.sp_vat') }}" v-model.lazy="item.vat" maxlength="8" v-validate:vat_amount="'decimal|max:8|between:0,100'">
														
													</div>
													<span v-show="showErrors && errors.has('vat_amount')" class="help is-danger">@{{ errors.first('vat_amount') }}</span>
												</div>
											</div>
                                            @endIf
											<!--End  VAT -->
											<div class="row">
												<div class="col-md-4  " >
													<div class="form-group ">
														{!! Form::label('title', trans('item.s_p_w')) !!}<label><span style="color:red">*</span></label>
														<div class="input-group">
															<input type="text" name="spw" class="form-control input-sm decimal" placeholder="{{ trans('item.s_p_w') }}" v-model="item.wholesale_salePrise"   maxlength="12" v-validate:sp="'decimal|max:12'" required>
															
														</div>
														<span v-show="showErrors && errors.has('spw')" class="help is-danger">@{{ errors.first('spw') }}</span>
													</div>
												</div>
												
											</div>
										</div>
									</div>
									@else
									
									<div class="col-md-6 sp-padding">
										<div class="col-md-12 sp-padding">
											<div class="form-group " >
												<!-- C.P -->
												<div class="col-md-3 sp-padding" >
													<div class="form-group " >
														{!! Form::label('title', trans('item.c_p')) !!}<label></label>
														<div class="input-group">
															<!-- <div class="input-group-addon"><i class="fa fa-money"></i></div> -->
															<input type="text" name="cp" class="form-control input-sm" placeholder="{{ trans('item.c_p') }}" v-model="item.costPrise"   maxlength="12" v-on:change="checkPrise()" v-validate:cp="'decimal|max:12'" >
															
														</div>
														<span v-show="showErrors && errors.has('cp')" class="help is-danger">@{{ errors.first('cp') }}</span>
													</div>
												</div>
												<!-- End C.P -->

												<!-- C.P_Vat -->
												@if(Common::vatType('vat','vat_type')!=0)
												<div class="col-md-3 sp-padding">
													<div class="form-group" >
														{!! Form::label('title', trans('item.cp_vat_percent')) !!}
														<div class="input-group ">
															
															<input type="text" name="cp_vat_amount" class="form-control input-sm decimal" placeholder="{{ trans('item.cp_vat_percent') }}" v-model.lazy="item.cp_vat" maxlength="" v-validate:cp_vat_amount="'decimal|max:8|between:0,100'">
															
														</div>
														<span v-show="showErrors && errors.has('cp_vat_amount')" class="help is-danger">@{{ errors.first('cp_vat_amount') }}</span>
													</div>
												</div>
												@endIf
												<!-- End C.P_Vat -->
												<!-- S.P -->
												<div class="col-md-3 sp-padding">
													<div class="form-group" >
														{!! Form::label('title', trans('item.s_p')) !!}<label><span style="color:red">*</span></label>
														<div class="input-group ">
															
															<input type="text" name="sp" class="form-control input-sm decimal" placeholder="{{ trans('item.s_p') }}" v-model.lazy="item.salePrise" v-on:change="checkPrise()"  maxlength="12" v-validate:sp="'decimal|max:12'" required>
															
														</div>
														<span v-show="showErrors && errors.has('sp')" class="help is-danger">@{{ errors.first('sp') }}</span>
													</div>
												</div>

												<!-- VAT-->
												@if(Common::vatType('vat','vat_type')!=0)
												<div class="col-md-3 sp-padding">
													<div class="form-group" >
														{!! Form::label('title', trans('glob.sp_vat')) !!}
														<div class="input-group ">
															
															<input type="text" name="vat_amount" class="form-control input-sm decimal" placeholder="{{ trans('glob.sp_vat') }}" v-model.lazy="item.vat" maxlength="8" v-validate:vat_amount="'decimal|max:8|between:0,100'">
															
														</div>
														<span v-show="showErrors && errors.has('vat_amount')" class="help is-danger">@{{ errors.first('vat_amount') }}</span>
													</div>
												</div>
                                                @endIf
												<!--End  VAT -->
											</div>
											
										</div>
									</div>

									@endif
								</div>
								<!-- .row -->

								<div class="row">

									<!-- description -->
									<div class="col-md-6">	
										<div class="form-group">
											{!! Form::label('title', trans('glob.description')) !!}
											<textarea name="description"  rows="4" class="form-control input-sm fix-textarea" placeholder="{{ trans('glob.description') }}"  v-model="item.description" ></textarea>
											
										</div>

									</div>
									<!--End description -->

									<div class="col-md-3">

										<div class="row">

											<!-- unit -->
											<div class="col-md-12">
												<div class="form-group">
													{!! Form::label('title', trans('glob.unit')) !!}<label><span style="color:red">*</span></label>
													<div class="input-group">
														<div class="input-group-addon"><i class="fa fa-th-large" aria-hidden="true"></i></div>
														<select name="unit" class="form-control input-sm" v-model="item.unit"  v-validate:unit="'required'" required>
															<option value=""></option>
															<option v-for="u in units" :value="u.id">@{{ u.unit }}</option>
														</select>
														
													</div>
													
												</div>
											</div>
											<!-- End unit -->

											<!-- stock -->
											<div class="col-md-12">

												<div class="form-group">
													{!! Form::label('title', trans('menu.stock')) !!}
													<div class="input-group">
														<div class="input-group-addon">	<i class="fa fa-stack-overflow"></i></div>
														<input type="text" name="stock" class="form-control input-sm" placeholder="{{ trans('menu.stock') }}" v-model="item.stock"   disabled="">
														
													</div>
													<span v-show="showErrors && errors.has('stock')" class="help is-danger">@{{ errors.first('stock') }}</span>
												</div>
											</div>
											<!-- end stock -->

										</div>
										<!-- .row -->

									</div>
									<!-- .col-md-3 -->

									<div class="col-md-3">

										<div class="row">

											<!-- min -->
											<div class="col-md-12">
												<div class="form-group">
													{!! Form::label('title', trans('glob.min')) !!}<label></label>
													<div class="input-group">
														<div class="input-group-addon">	<i class="fa fa-th-large" aria-hidden="true"></i>	</div>
														{!! Form::text('min', null, array( 'class'=>'form-control input-sm', 'placeholder'=>trans('glob.min'), "v-model" => "item.min", "v-validate:min"=>"'decimal|max:8'","maxlength"=>"8" )) !!}
													</div>
													<span v-show="showErrors && errors.has('min')" class="help is-danger">@{{ errors.first('min') }}</span>
												</div>
											</div>
											<!-- End min -->

											<!-- max -->
											<div class="col-md-12">
												<div class="form-group">
													{!! Form::label('title', trans('glob.max')) !!}<label></label>
													<div class="input-group">
														<div class="input-group-addon">	<i class="fa fa-stack-overflow"></i></div>
														{!! Form::text('max', null, array(	'class'=>'form-control input-sm', 'placeholder'=>trans('glob.max'), "v-model" => "item.max", "v-validate:max"=>"'decimal|max:8'", "maxlength"=>"8")) !!}
													</div>
													<span v-show="showErrors && errors.has('max')" class="help is-danger">@{{ errors.first('max') }}</span>
												</div>
											</div>
											<!-- end max -->

										</div>
										<!-- .row -->

									</div>
									<!-- .col-md-3 -->

								</div>
								<!-- .row -->

							</div>
							<!-- .col-md-9 -->

							<div class="col-md-3">
								<div class="row">
									<!-- barcode img -->
									<div class="col-md-12">
										<div class="barcodeImageDiv text-center">
											<img :src="imageUrl" class="" id="barcodeImg" style="margin:auto" alt="Barcode" >
										</div>
										<!-- end barcode img -->

										<!-- product profile -->
										<div class="col-md-12">
											<div class="profile-action">
												<img id="productImg" :src="item.image" class="img-thumbnail img-responsive"  style="margin:auto">
												<a class="change-profile" style="color: black" v-on:click="uploadItemProfile"><i class="fa fa-camera fa-lg" aria-hidden="true"></i></a>
											</div>
										</div>
										<!-- end product profile -->
									</div>
								</div>
								<div class="row">
									@if(Common::can_option_view('item','item_with_boxes'))
									<div class="col-md-12" style="margin-top: 7px;">
										{!! Form::label('item_box', trans('item.item_with_boxes')) !!}
										{!! Form::checkbox('item_box', null, false ,array('id'=>'item_box', 'v-model' => 'item.item_with_box' )) !!}
									</div>
									@endif
									<div class="col-md-12" v-show="opType=='edit'">	
										<div class="form-group">
											{!! Form::label('title', trans('glob.remark')) !!}<label><span style="color:red">*</span></label>
											<textarea name="description"  rows="4" class="form-control input-sm fix-textarea" placeholder="{{ trans('glob.remark') }}"  v-model="item.remark" :required="(opType=='edit') ? true : false"></textarea>
										</div>
									</div>
								</div>

							</div>
						</div>
						<div class="row " >
							<div  class="col-md-2">
								<div class="form-group">
									{!! Form::label('userName', trans('item.promotion_from')) !!}
									<input id="p_from" type="text" name="promotion_from" class="form-control input-sm date-time-picker" placeholder="{{ trans('item.promotion_from') }}" v-model="item.promotion_from">
								</div>

							</div>
							<div  class="col-md-2">
								<div class="form-group">
									{!! Form::label('userName', trans('item.promotion_to')) !!}
									<input id="p_to" type="text" name="promotion_to" class="form-control input-sm date-time-picker" placeholder="{{ trans('item.promotion_to') }}" v-model="item.promotion_to">
								</div>

							</div>
							<div  class="col-md-2">
								<div class="form-group">
									{!! Form::label('p_price', trans('glob.p_price')) !!}
									<div class="input-group">
										<div class="input-group-addon"><i class="fa fa-money"></i></div>
										<input  type="text" name="promotion_price" class="form-control input-sm " placeholder="{{ trans('glob.p_price') }}" v-model="item.promotion_price" maxlength="8" v-validate:promotion_price="'decimal|max:8'" >
									</div>
									<span v-show="showErrors && errors.has('promotion_price')" class="help is-danger">@{{ errors.first('promotion_price') }}</span>
								</div>
							</div>
							<!-- expiry date -->
							<div  class="col-md-2">
								<div id="expiryDiv" class="form-group" v-show="item.expiryChk">
									{!! Form::label('userName', trans('glob.expiry_date')) !!}
									<div class="input-group">
										<div class="input-group-addon"><i class="ion ion-calendar"></i></div>
										<input id="exp" type="text" name="expiry_date" class="form-control input-sm datepicker" placeholder="{{ trans('glob.expiry_date') }}" v-model="item.expiry_date">
									</div>
								</div>
							</div>
							<!-- End Expiry date -->

							<!-- item expiry checkbox -->
							<div class="col-md-1 margin-top-space">
								{!! Form::label('expiryId', trans('item.expiry')) !!}
								{!! Form::checkbox('expiryId', null, false ,array('id'=>'expiryId', 'v-model' => 'item.expiryChk', 'v-on:click' => "collapseBox('exp_date','expiryDiv')" )) !!}
							</div>
							<!-- End item expiru checkbox -->

							<!-- package checkbox -->
							<div class="col-md-3 margin-top-space">
								{!! Form::label('xyPromoId', trans('item.x_y_Promo')) !!} 
								{!! Form::checkbox('xyPromoId', null, false ,array('id'=>'xyPromoId', 'v-model' => 'item.x_y_prmoChk', 'v-on:click' => "collapseBox('xyPromo','x-y-promotionDiv')" )) !!} 
								&nbsp;
								{!! Form::label('packageId', trans('item.package')) !!}
								{!! Form::checkbox('packageId', null, false ,array('id'=>'packageId', 'v-model' => 'item.packageChk', 'v-on:click' => "collapseBox('package','packageDiv')" )) !!}
								
							</div>
							<!-- package checkbox -->
						</div>

						<!-- .row -->
						<div class="row">

							{{-- Item x_y_promo box start--}}
							@include("pages.masters.items.x_y_promotion") 
							{{-- Item x_y_promo box end--}}

						</div>
						<!-- .row -->

						<!-- .row -->
						<div class="row">

							{{-- Item package/gift box start--}}
							@include("pages.masters.items.package")
							{{-- Item package/gift box end--}}

						</div>
						<!-- .row -->
					</div>
					<!-- .box-body -->

					<div class="box-footer">

						<!-- save cancel and update actions -->
						
						

						<!-- save cancel and update actions -->
						<div class="form-group">
							<button id="add-button" v-show="opType=='add'" type="submit" class="btn btn-primary "><i class="fa fa-floppy-o"></i> @lang('glob.save') </button>

							<button id="update-button" v-show="opType=='edit'" type="submit"  class="btn btn-primary "><i class="fa fa-floppy-o"></i> @lang('glob.update')</button>

							<a  class="btn btn-cyan" v-on:click="resetForm"><i class="fa fa-refresh"></i> @lang('glob.reset')</a>

							<a class="btn btn-danger"  v-on:click="cancelForm">@lang('glob.cancel')</a>
						</div>
						<!-- box-footer -->

						
						<!-- .box .box-primary -->

					</div>
					<!-- .col-md-12 -->

				</div>
			</div>
		</div>
	</form>
	<!--end .row .customer-box -->
	{{-- {{ Form::close() }} --}}