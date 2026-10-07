<div id="paymentModal" class="modal fade" role="dialog">
   <div class="modal-dialog " style="width:75%;">
      <!-- Modal content-->
      <div class="modal-content payment-modal-content raleway">
         <form id="payform" onsubmit="return false;">
            <div class="modal-header bg-purple" style="padding:10px;">
               <button type="button" class="close" data-dismiss="modal">&times;</button>
               <h4 class="modal-title"> @lang("glob.payment")</h4>
               <div class="row" style="height:20px;" >
                  <!-- right sidebar -->
                  <div class="col-sm-4">
                     <span class=" pull-right modal-title"><b>@{{billing.customer.eligible}}</b></span>
                  </div>
                  <div class="col-sm-4 text-center">
                     <span class="modal-title"><b>@{{billing.customer.message_point}}</b></span>
                  </div>
                  <div class="col-sm-4">
                     <span class="pull-left modal-title"><b>@{{billing.customer.message}}</b></span>
                  </div>
               </div>
            </div>
            <div class="modal-body padding-sm no-bottom-padding ">
               <div class="row">
                  <!-- right sidebar -->
                  <div class="col-md-6 col-sm-6 less-padding-right" >
                     {{-- Customer Details --}}
                     @if(Common::can_option_view('billing','customer_details'))
                     <div class="box box-primary customer_details"  :style="(billing.home_delivery) ? 'margin-bottom:15px' : '' "v-if="tax_refund_bill==0">
                        <div class="padding-sm" style = "padding-top: 5px;">
                           <!-- right sidebar -->
                           <div class="col-sm-4">
                              <b>Customer Details</b>
                           </div>
                           <div class="col-sm-4 less-padding-left less-padding-right">
                              <label  for="">
                                 <div>@lang('customer.new_customer_id'): @{{billing.customer.newcustomerId}} </div>
                              </label>
                           </div>
                           <div class="col-sm-4 numpad-input less-padding-left less-padding-right">
                              <input type="text" placeholder="Customer Id" class="form-control input-xs numF-pad " v-model="billing.customer.id" v-on:change="getCustomerDetails(billing.customer.id,'customerId')">
                           </div>
                        </div>
                        <div class="box-body padding-sm no-top-padding " class="no-bottom-padding">
                           <div class="less-padding-left less-padding-right col-md-6 col-sm-6" >
                              <div class="form-group no-h-padding  margin-b-2px numpad-input">
                                 <label v-if="!billing.home_delivery" class="control-label" >@lang('glob.mobile')<span style="color:red">*</span></label>
                                 <input type="text" placeholder="Mobile"  class="form-control input-xs mobile mobi validate-number"   v-model="billing.customer.customerNumber" name="customerMobile" v-on:change="getCustomerDetails(billing.customer.customerNumber,'customerMobile')" :required="true">
                              </div>
                           </div>
                           <div class="less-padding-left less-padding-right col-md-6 col-sm-6" >
                              <div class="form-group no-h-padding  margin-b-2px numpad-input">
                                 <label v-if="!billing.home_delivery" class="control-label" > @lang('glob.name')<span style="color:red">*</span></label> 
                                 <input type="text" placeholder="Name" class="form-control input-xs keyboard2" id="name" v-model="billing.customer.customerName" :required="true">
                              </div>
                           </div>
                           <div class="col-md-6 col-sm-6 less-padding-left less-padding-right">
                              <div class="form-group no-h-padding  margin-b-2px " >
                              <label class="control-label" >@lang('glob.gender')</label>
                                 <v-select id="gender" :options="arrGender" :text="'name'"  v-model="billing.customer.gender"  :vrequired="false" >														
                                 </v-select>
                              </div>
                           </div>
                           <div class="col-md-6 col-sm-6 less-padding-left less-padding-right">
                              <div class="form-group no-h-padding  margin-b-2px" >
                              <label class="control-label" >@lang('glob.age_group')</label>
                                 <v-select id="age_group" :options="arrAgeGroup" :text="'name'"  v-model="billing.customer.age_group"  :vrequired="false" >
                              </div>
                           </div>
                           <div class="col-md-6 col-sm-6 less-padding-left less-padding-right">
                              <div class="form-group no-h-padding  margin-b-2px">
                            
                                 <input type="text" id="birthday"  class="form-control input-xs  keyboard2 validbirthday" placeholder="BirthDay(YYYY-MM-DD)" v-model="billing.customer.birthday" v-on:change="getAgeGroup(billing.customer.birthday)" :vrequired="false">
                              </div>
                           </div>
                          
                           <div class="col-md-6 col-sm-6 less-padding-left less-padding-right">
                              <div class="form-group no-h-padding  margin-b-2px " >
                                 <input type="text" placeholder="nationality" class="form-control input-xs keyboard2" id="name" v-model="billing.customer.nationality" >											 
                              </div>
                           </div>
                           <div class="col-md-6 col-sm-6 less-padding-left less-padding-right">
                              <div class="form-group no-h-padding  margin-b-2px numpad-input" >
                                 <input type="city" placeholder="@lang('glob.city')" class="form-control input-xs keyboard2" id="city" v-model="billing.customer.city">
                              </div>
                           </div>
                           <div class="col-md-6 col-sm-6 less-padding-left less-padding-right">
                              <div class="form-group no-h-padding  margin-b-2px numpad-input" >
                                 <input type="email" placeholder="@lang('glob.email')" class="form-control input-xs keyboard2" id="email" v-model="billing.customer.email">
                              </div>
                           </div>
                         
                           <div class="col-md-12 col-sm-12 less-padding-left less-padding-right">
                              <div class="form-group no-h-padding  margin-b-2px numpad-input" >
                                 <input type="text" placeholder="@lang('glob.comment')" class="form-control input-xs keyboard2" id="address" v-model="billing.customer.address">
                              </div>
                           </div>
                           <div :class=" (billing.home_delivery) ? 'display-block-input':'display-none-input'">
                              <div class="col-md-6 col-sm-6 less-padding-left less-padding-right">
                                 <div class="form-group no-h-padding  margin-b-2px numpad-input" >
                                    <input type="text" placeholder="@lang('glob.address')" class="form-control input-xs keyboard2" id="address" v-model="billing.customer.address" :required=" (billing.home_delivery )  ? true :false">
                                 </div>
                              </div>
                              <div class="col-md-6 col-sm-6 less-padding-left less-padding-right">
                                 <div class="form-group no-h-padding  margin-b-2px numpad-input" >
                                    <input type="text" placeholder="@lang('billing.flat_no')" class="form-control input-xs keyboard2" id="flat_no" v-model="billing.customer.flat_no">
                                 </div>
                              </div>
                              <div class="col-md-6 col-sm-6 less-padding-left less-padding-right">
                                 <div class="form-group no-h-padding  margin-b-2px numpad-input" >
                                    <input type="text" placeholder="@lang('billing.blg')" class="form-control input-xs keyboard2" id="blg" v-model="billing.customer.blg">
                                 </div>
                              </div>
                              <div class="col-md-6 col-sm-6 less-padding-left less-padding-right">
                                 <div class="form-group no-h-padding  margin-b-2px numpad-input" >
                                    <input type="text" placeholder="@lang('billing.street')" class="form-control input-xs keyboard2" id="street"  v-model="billing.customer.street">
                                 </div>
                              </div>
                              @if(Common::can_update('customer'))
                              <div class="form-group no-h-padding  margin-b-2px numpad-input" >
                                 <label class="control-label col-sm-7" >
                                 {!! Form::checkbox('addressUpdateAtHome', null, false ,array('id'=>'addressUpdateAtHome', 'v-model' => 'billing.addressUpdateAtHome')) !!}
                                 {!! Form::label('addressUpdateAtHome', trans('billing.home_address_update')) !!}
                                 </label>
                              </div>
                              @endif
                           </div>
                        </div>
                        <!-- /.box-body -->
                     </div>



                     <!--Tax Refund Customer-->
                     <div class="box box-primary customer_details"  :style="(billing.home_delivery) ? 'margin-bottom:15px' : '' " v-if="tax_refund_bill==1">
                        <div class="padding-sm" style = "padding-top: 5px;">
                           <!-- right sidebar -->
                           <div class="col-sm-4">
                              <b>Customer Details</b>
                           </div>
                           <div class="col-sm-4 less-padding-left less-padding-right">
                              <label  for="">
                                 <div>@lang('customer.new_customer_id'): @{{billing.customer.newcustomerId}} </div>
                              </label>
                           </div>
                           <div class="col-sm-4 numpad-input less-padding-left less-padding-right">
                              <input type="text" placeholder="Customer Id" class="form-control input-xs numF-pad " v-model="billing.customer.id" v-on:change="getCustomerDetails(billing.customer.id,'customerId')">
                           </div>
                        </div>
                         <div class="col-sm-8 col-md-8">
                         	<textarea type="text" id="passport_number-on" placeholder="passport" class="passportScan form-control input-xs" :class="(showmrzcode == true) ? 'display-block-input' : 'display-none-input' " v-model="passport_number" name="passport_number" v-on:change="getPassportCustomerDetails(passport_number)"></textarea>
                            <input id="passport_number" type="text" placeholder="passport" class="passportScan form-control input-xs  "  :class="showmrzcode == false ? 'display-block-input' : 'display-none-input' " name="passport_number"  v-model="passport_number"  />
                         </div>
                         <div class="col-sm-2 col-md-2">
                         	<span class="input-group-btn">
                                 <a id="passport-add" class="btn btn-xs btn-circle btn-input-xs-group" :class="showmrzcode==true ? 'bg-green' : 'bg-maroon'"  v-on:click="getmrzcode()" ><i v-show="passport_keyboard"  class="fa fa-keyboard-o" aria-hidden="true"></i><i  v-show="passport_loading" class="fa fa-spinner fa-pulse fa-fw barcode-load"></i></a>
                                 </span>
                         </div>
                        <div class="box-body padding-sm no-top-padding " class="no-bottom-padding">
                          <!--  <div class="less-padding-left less-padding-right col-md-8 col-sm-8" >
                              <div class="form-group no-h-padding  margin-b-2px">
                                 <input type="text" id="passport_number" placeholder="passport" class="passportScan form-control input-xs" v-model="passport_number" name="passport_number" v-on:change="getPassportCustomerDetails(passport_number)">
                              </div>
                           </div>
                           <div class="less-padding-left less-padding-right col-md-2 col-sm-2" >
                              <div class="form-group no-h-padding  margin-b-2px">
                                 <span class="input-group-btn">
                                 <button id="passport-add" class="btn btn-xs btn-circle btn-input-xs-group bg-green" ><i v-show="passport_keyboard"  class="fa fa-keyboard-o" aria-hidden="true"></i><i  v-show="passport_loading" class="fa fa-spinner fa-pulse fa-fw barcode-load"></i></button>
                                 </span>
                              </div>
                           </div> -->
                           <div class="less-padding-left less-padding-right col-md-6 col-sm-6" v-show="showmrzcode">
                              <div class="form-group no-h-padding  margin-b-2px numpad-input">
                                 <label v-if="!billing.home_delivery" class="control-label" >@lang('glob.mobile')<span style="color:red">*</span></label> 
                                 <input type="text"  maxlength="16" placeholder="Mobile" class="form-control input-xs mobile mobi validate-number"   v-model="billing.customer.customerNumber" name="customerMobile"  :required=" (billing.payment_mode == 3 || billing.tax_refund_flag==1)  ? true :false">
                                 
                              </div>
                           </div>
                           <div class="less-padding-left less-padding-right col-md-6 col-sm-6" v-show="!showmrzcode" >
                              <div class="form-group no-h-padding  margin-b-2px numpad-input">
                                 <label v-if="!billing.home_delivery" class="control-label" >@lang('glob.mobile')<span style="color:red">*</span></label> 
                                 <input type="text"  maxlength="16" placeholder="Mobile" class="form-control input-xs mobile mobi validate-number"   v-model="billing.customer.customerNumber" name="customerMobile"  :required=" (billing.payment_mode == 3 || billing.tax_refund_flag==1)  ? true :false" v-on:change="getCustomerDetails(billing.customer.customerNumber,'customerMobile')">
                              </div>
                           </div>
                           <div class="less-padding-left less-padding-right col-md-6 col-sm-6" >
                              <div class="form-group no-h-padding  margin-b-2px numpad-input">
                                 <label v-if="!billing.home_delivery" class="control-label" > FirstName<span style="color:red">*</span></label> 
                                 <input type="text" name="firstName" placeholder="FirstName" class="form-control input-xs keyboard2" id="firstName" v-model="billing.customer.firstName" :required=" (billing.payment_mode == 3 || (billing.customer.customerNumber.length==9 || billing.customer.customerNumber.length==10)|| tax_refund_bill==1)? true :false">
                              </div>
                           </div>
                           <div class="less-padding-left less-padding-right col-md-6 col-sm-6" >
                              <div class="form-group no-h-padding  margin-b-2px numpad-input">
                               
                                 <input type="text" name="lastName" placeholder="LastName" class="form-control input-xs keyboard2" id="lastName" v-model="billing.customer.lastName" :required=" (billing.payment_mode == 3 || (billing.customer.customerNumber.length==9 || billing.customer.customerNumber.length==10) || tax_refund_bill==1)? true :false">
                              </div>
                           </div>
                           <div class="col-md-6 col-sm-6 less-padding-left less-padding-right">
                              <div class="form-group no-h-padding  margin-b-2px">
                                 <input type="text" id="birthday"  class="form-control input-xs  keyboard2 validbirthday" placeholder="BirthDay(YYYY-MM-DD)" v-model="billing.customer.birthday" :required=" (billing.payment_mode == 3 || (billing.customer.customerNumber.length==9 || billing.customer.customerNumber.length==10)||tax_refund_bill==1)? true :false">
                              </div>
                           </div>
                           <div class="col-md-6 col-sm-6 less-padding-left less-padding-right">
                              <div class="form-group no-h-padding  margin-b-2px " >
                                 <input type="text" placeholder="nationality" class="form-control input-xs keyboard2" id="name" v-model="billing.customer.nationality" :required=" (billing.payment_mode == 3 || (billing.customer.customerNumber.length==9 || billing.customer.customerNumber.length==10)||tax_refund_bill==1)? true :false">											 
                              </div>
                           </div>
                           <div class="col-md-6 col-sm-6 less-padding-left less-padding-right">
                              <div class="form-group no-h-padding  margin-b-2px numpad-input">
                                 <input type="text" placeholder="@lang('glob.documentType')"  style="text-transform:uppercase" class="form-control input-xs keyboard2" id="document_type" v-model="billing.customer.type" :required=" (billing.payment_mode == 3 || (billing.customer.customerNumber.length==9 || billing.customer.customerNumber.length==10)||tax_refund_bill==1)? true :false">
                              </div>
                           </div>
                           <div class="col-md-6 col-sm-6 less-padding-left less-padding-right">
                              <div class="form-group no-h-padding  margin-b-2px numpad-input" >
                                 <input type="text" placeholder="@lang('glob.passportnumber')" class="form-control input-xs keyboard2" id="document_number" v-model="billing.customer.number" :required=" (billing.payment_mode == 3 || (billing.customer.customerNumber.length==9 || billing.customer.customerNumber.length==10)||tax_refund_bill==1)? true :false">
                              </div>
                           </div>
                           <div class="col-md-6 col-sm-6 less-padding-left less-padding-right">
                              <div class="form-group no-h-padding  margin-b-2px numpad-input">
                                 <input type="text" placeholder="@lang('glob.issuedBy')" class="form-control input-xs keyboard2" id="issuedBy" v-model="billing.customer.issuedBy" :required=" (billing.payment_mode == 3 || (billing.customer.customerNumber.length==9 || billing.customer.customerNumber.length==10)||tax_refund_bill==1)? true :false">
                              </div>
                           </div>
                           <div class="col-md-6 col-sm-6 less-padding-left less-padding-right">
                              <div class="form-group no-h-padding  margin-b-2px numpad-input" >
                                 <input type="text" placeholder="@lang('glob.countryOfResidence')" class="form-control input-xs keyboard2" id="countryOfResidence" v-model="billing.customer.countryOfResidence" :required=" (billing.payment_mode == 3 || (billing.customer.customerNumber.length==9 || billing.customer.customerNumber.length==10)||tax_refund_bill==1)? true :false">
                              </div>
                           </div>
                           {{-- Planet Tax Free: shopper.email is mandatory in the issuance payload from 1 Oct 2026 --}}
                           <div class="col-md-12 col-sm-12 less-padding-left less-padding-right">
                              <div class="form-group no-h-padding  margin-b-2px numpad-input">
                                 <label v-if="!billing.home_delivery" class="control-label" >@lang('glob.email')<span style="color:red">*</span></label>
                                 <input type="email" placeholder="@lang('glob.email')" class="form-control input-xs keyboard2" id="tax_refund_email" v-model="billing.customer.email" :required="tax_refund_bill==1 ? true : false">
                              </div>
                           </div>
                           <!-- <div class="less-padding-left less-padding-right col-md-6 col-sm-6" >
                              <div class="form-group no-h-padding  margin-b-2px numpad-input">
                               
                                 <input type="text" placeholder="customoer" class="form-control input-xs keyboard2" id="lastName" v-model="billing.customer.lastName" :required=" (billing.payment_mode == 3 || (billing.customer.customerNumber.length==9 || billing.customer.customerNumber.length==10)&&tax_refund_bill==1)? true :false">
                              </div>
                           </div> -->
                        </div>
                        <!-- /.box-body -->
                     </div>
                     <!--End Tax Refund Customer-->
                     @endif
                     {{-- Payment Modes --}}
                     <div class="box box-primary">
                        <div style="padding:5px;">
                           <b>Payment Modes</b>
                        </div>
                        <div class="box-body payment-box"  :class="(billing.home_delivery) ? 'payment-box-hd'  : 'payment-box' ">
                           <div class="row">
                              <div class="col-md-12 col-sm-12">
                                 <ul class="list-group">
                                    <label  class="radio-label list-group-item highlight-list ">
                                    <input type="radio" name="optradio" v-on:change="payment_mode_change(billing.payment_mode)" v-model="billing.payment_mode" value="1" class="rad-payment-mode check-highlights"> 
                                    <i class="fa fa-money" aria-hidden="true"></i> @lang("billing.cash")
                                    </label>
                                    <label  class="radio-label list-group-item">
                                    <input type="radio"  name="optradio" v-on:change="payment_mode_change(billing.payment_mode)" v-model="billing.payment_mode" value="2" class="rad-payment-mode check-highlights" > 
                                    <i class="fa fa-credit-card-alt" aria-hidden="true"></i> @lang("billing.card") & <i class="fa fa-money" aria-hidden="true"></i> 
                                    @lang("billing.cash")
                                    </label>
                                    {{-- Credit hidden for now --}}
                                    <label  class="radio-label list-group-item" style="display:none;">
                                    <input type="radio" name="optradio"  v-on:change="payment_mode_change(billing.payment_mode)" v-model="billing.payment_mode" value="3" class="rad-payment-mode check-highlights" > <i class="fa fa-sticky-note" aria-hidden="true"></i>
                                    @lang("billing.credit")
                                    </label>
                                    <label class="radio-label list-group-item">
                                    <input type="radio" name="optradio"  v-on:change="payment_mode_change(billing.payment_mode)" v-model="billing.payment_mode" value="4"  class="rad-payment-mode check-highlights"> <i class="fa fa-credit-card-alt" aria-hidden="true"></i> 
                                    @lang("billing.credit_card")
                                    </label>
                                    <label class="radio-label list-group-item">
                                    <input type="radio" name="optradio"  v-on:change="billing.cash_amt=0;billing.card_amt=fixtIt(billing.bil_amt);billing.received=fixtIt(billing.bil_amt);billing.balance=fixtIt(0)" v-model="billing.payment_mode" value="5"  class="rad-payment-mode check-highlights"> <i class="fa fa-clock-o" aria-hidden="true"></i> 
                                    Dubai Mall Online
                                    </label>
                                 </ul>
                              </div>
                           </div>
                           <div class="row" style="margin-top: 20px;">
                              <div class="col-md-6 col-sm-6">
                                 <div class="form-group numpad-input">
                                    <label for="">@lang('billing.card_no'):</label>
                                    <div class="input-group">
                                       <div class="input-group-addon  bg-theme">
                                          xxxx - xxxx - xxxx -
                                       </div>
                                       <input type="text" class="form-control input-sm  card validate-number" v-model="billing.card_no" placeholder="xxxx" :disabled="(billing.payment_mode == 2 || billing.payment_mode == 4) ? false :true" maxlength="4">
                                    </div>
                                 </div>
                              </div>
                              <div class="col-md-6 col-sm-6">
                                 <div class="form-group numpad-input">
                                    <label for="">@lang('billing.reference_no'):</label>
                                    <div class="input-group">
                                       <div class="input-group-addon  bg-theme">
                                          <i class="fa fa-sticky-note"></i>
                                       </div>
                                       <input type="text" class="form-control input-sm  num-pad validate-number" v-model="billing.reference_no" placeholder="{{ trans('billing.reference_no') }}" maxlength="10" :disabled="(billing.payment_mode == 2 || billing.payment_mode == 4) ? false :true" >
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  {{-- Bill Details --}}
                  <div class="col-md-6 col-sm-6 less-padding-left">
                     {{-- Bill Summery --}}
                     <div class="box box-primary">
                        <div style="padding:5px;">
                           <b>Billing Summary</b>
                        </div>
                        <div class="box-body">
                           <div class="row" style="padding-bottom: 5px;">
                              <div class="col-md-6 col-sm-6">
                                 <div class="pull-left" style="margin-top: 15px;">
                                    <span><label>Total Items: @{{ itemList.length }}</label> </span> <br>
                                    <span><label>Payment Mode: @{{ (billing.payment_mode==1) ? 'Cash' : (billing.payment_mode==2) ? 'Card & Cash' : (billing.payment_mode==3) ? 'Credit' : (billing.payment_mode==5) ? 'Dubai Mall Online' : 'Credit Card'  }} </label></span> <br>
                                    <span><label> SubTotal: @{{curr}} @{{ _discount_subtotal }} </label></span> <br>
                                 </div>
                              </div>
                              <div class="col-md-6 col-sm-6">
                                 <div class="pull-right">
                                    <h1 style="margin-top: 15px;"> @{{curr}} @{{ _bill_amount  }} </h1>
                                    <span v-show="intVatType!=0"><b>VAT @{{curr}} @{{ fixtIt(parseFloat(_discounted_vat) )}}</b></span><br/>
                                    <!-- <span ><b> Discount @{{curr}} @{{ fixtIt(parseFloat(_dis_amt) + parseFloat(billing.loyalty_discount)) }} </b></span> -->
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     {{-- /.bill Summery --}}
                     {{-- Bill Details --}}
                     <div class="box box-primary">
                        <div style="padding:5px;">
                           <b>@lang('billing.bill_details')</b>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                           {{-- Subtotal --}}											
                           <div class="row margin-b-2px ">
                              <div class="col-md-3 col-sm-3 ">
                                 <label for="" class="pull-right">@lang('billing.sub_total')</label>
                              </div>
                              <!-- <div class="col-md-9 col-sm-9"> 
                                 <input type="text" class="form-control" v-model="totalAmount" readonly="">
                                 </div> -->
                              <div class="col-md-9 col-sm-9">
                                 {{-- Subtotal  --}}
                                 <div class="col-md-6 col-sm-6 no-h-padding">
                                    <input type="text" class="form-control" v-model="totalAmount" readonly="">
                                 </div>
                                 {{-- VAT --}}
                                 <div class="col-md-6 col-sm-6 no-h-padding">
                                    <div class="col-md-4 col-sm-4 no-h-padding">
                                       <label style="padding-right: 5px;"  for=""> &nbsp; @lang('billing.v_amt') </label>
                                    </div>
                                    <div class="col-md-8 col-sm-8 no-h-padding">
                                       <input type="text" class="form-control" v-model="billing.v_amt" readonly="true">
                                    </div>
                                 </div>
                              </div>
                           </div>
                           {{-- Vat amount --}}
                           <!-- <div class="row margin-b-2px">
                              <div class="col-md-3 col-sm-3 ">
                              	<label for="" class="pull-right">@lang('billing.v_amt')</label>
                              </div>
                              <div class="col-md-9 col-sm-9 numpad-input">
                              	<input type="text" class="form-control" v-model="billing.v_amt" readonly="true">
                              </div>
                              </div> -->
                           {{-- Discount --}}
                           <div class="row margin-b-2px">
                              <div class="col-md-3 col-sm-3 ">
                                 <label for="" class="pull-right">@lang('billing.dis_amt')</label>
                              </div>
                              <div class="col-md-9 col-sm-9">
                                 <div class="col-md-6 col-sm-6 no-h-padding text-right numpad-input" style="padding-right:2px">
                                    <div class="input-group">
                                       <div class="input-group-addon  bg-theme">
                                          %
                                       </div>
                                       <input type="text" class="form-control numF-pad" v-on:change="calAmountDiscount()" id="per_discount" v-model="flot_dis_per" :disabled="(billing.card_id > 0) ? true : false" :class="(isKeyboardEnabled) ? 'display-block-input' : 'display-none-input' ">
                                       <input type="text" class="form-control " v-on:change="calAmountDiscount()" id="per_discount1" v-model="flot_dis_per" :disabled="(billing.card_id > 0) ? true : false" :class="(!isKeyboardEnabled) ? 'display-block-input' : 'display-none-input' ">
                                    </div>
                                 </div>
                                 <div class="col-md-6 col-sm-6 no-h-padding numpad-input" style="padding-right:2px">
                                    <div class="input-group">
                                       <div class="input-group-addon  bg-theme">
                                          $
                                       </div>
                                       <input type="text" class="form-control numF-pad"  v-on:change="calPerDiscount()" id="dis_amt"  v-model="billing.dis_amt" :disabled="(billing.card_id > 0) ? true : false" :class="(isKeyboardEnabled) ? 'display-block-input' : 'display-none-input' ">
                                       <input type="text" class="form-control "  v-on:change="calPerDiscount()" id="dis_amt1"  v-model.lazy="billing.dis_amt" :disabled="(billing.card_id > 0) ? true : false" :class="(!isKeyboardEnabled) ? 'display-block-input' : 'display-none-input' ">
                                    </div>
                                 </div>
                              </div>
                           </div>
                           {{-- Loyalty Discount and Points --}}
                           <div class="row margin-b-2px" v-show="((mallDetails.loyalty == 1 && (loyaltyMaturity <= billing.customer.points)) || (mallDetails.loyalty == 1 && updateBill)) ? true : false">
                              <div class="col-md-3 col-sm-3 ">
                                 <label for="" class="pull-right">@lang('billing.loyalty_discount')</label>
                              </div>
                              <div class="col-md-9 col-sm-9">
                                 <div class="col-md-6 col-sm-6 no-h-padding numpad-input" style="padding-right:2px">
                                    <div class="input-group">
                                       <div class="input-group-addon  bg-theme">
                                          @
                                       </div>
                                       <input type="text" class="form-control numF-pad validate-decimal"  v-on:change="calLoyaltyAmt()" id="loyalty_points"  v-model="billing.loyalty_points" :disabled="(mallDetails.loyalty == 0 || billing.customer.eligibility == 0 || (loyaltyMaturity > billing.customer.points)) ? true : false">
                                    </div>
                                 </div>
                                 <div class="col-md-6 col-sm-6 no-h-padding text-right numpad-input" style="padding-right:2px">
                                    <div class="input-group">
                                       <div class="input-group-addon  bg-theme">
                                          $
                                       </div>
                                       <input type="text" class="form-control" id="loyalty_discount" v-model="billing.loyalty_discount" readonly="true">
                                    </div>
                                 </div>
                              </div>
                           </div>
                           {{-- Bill Amount --}}
                           <div class="row margin-b-2px">
                              <div class="col-md-3 col-sm-3 ">
                                 <label for="" class="pull-right">@lang('billing.bill') @lang('billing.amt')</label>
                              </div>
                              <div class="col-md-9 col-sm-9">
                                 <input type="text" class="form-control " v-model="_bill_amount" readonly="">
                              </div>
                           </div>
                           {{-- Card and cash --}}
                           <div class="row margin-b-2px">
                              <div class="col-md-3 col-sm-3 ">
                                 <label for="" class="pull-right">@lang('billing.cash') @lang('billing.amt')</label>
                              </div>
                              <div class="col-md-9 col-sm-9">
                                 {{-- Cash  --}}
                                 <div class="col-md-6 col-sm-6 no-h-padding numpad-input">
                                    <input type="text" class="form-control numF-pad" :disabled="(billing.payment_mode==2) ? false : true" v-model="_cash_amount">
                                 </div>
                                 {{-- Card --}}
                                 <div class="col-md-6 col-sm-6 no-h-padding">
                                    <div class="col-md-4 col-sm-4 no-h-padding">
                                       <label style="padding-right: 5px;"  for=""> &nbsp; @lang('billing.card') </label>
                                    </div>
                                    <div class="col-md-8 col-sm-8 no-h-padding numpad-input">
                                       <input type="text" :disabled="true" class="form-control" v-model="_card_amount">
                                    </div>
                                 </div>
                              </div>
                           </div>
                           {{-- Currency --}}
                           <div class="row margin-b-2px">
                              <div class="col-md-3 col-sm-3 ">
                                 <label for="" class="pull-right">@lang('billing.currency') </label>
                              </div>
                              <div class="col-md-9 col-sm-9">
                                 {{-- Currency  --}}
                                 <div class="col-md-6 col-sm-6 no-h-padding numpad-input">
                                    <v-select id="currency" :options="arrCurrency" :text="'code'"  v-model="billing.currency" v-on:vchange="calCurrencyAmt()" :disabled="(billing.payment_mode==3) ? true : false">
                                 </div>
                                 {{-- Curency Amt --}}
                                 <div class="col-md-6 col-sm-6 no-h-padding">
                                    <div class="col-md-4 col-sm-4 no-h-padding">
                                       <label style="padding-right: 5px;"  for=""> &nbsp; @lang('glob.rate') </label>
                                    </div>
                                    <div class="col-md-8 col-sm-8 no-h-padding">
                                       <input type="text" class="form-control" v-model="_currency_amt" readonly="true">
                                    </div>
                                 </div>
                              </div>
                           </div>
                           {{-- Received and balance --}}
                           <div class="row margin-b-2px">
                              <div class="col-md-3 col-sm-3 ">
                                 <label for="" class="pull-right">@lang('billing.received')</label>
                              </div>
                              <div class="col-md-9 col-sm-9 ">
                                 <div class="col-md-6 col-sm-6 no-h-padding numpad-input">
                                    <input type="text" class="form-control numF-pad"  v-model="billing.received" v-on:change="removePrifixZero(billing.received)">
                                 </div>
                                 <div class="col-md-6 col-sm-6 no-h-padding">
                                    <div class="col-md-4 col-sm-4 no-h-padding"><label style="padding-right: 5px;"  for=""> &nbsp;  @lang('billing.bal') </label></div>
                                    <div class="col-md-8 col-sm-8 no-h-padding"><input type="text" class="form-control" v-model="this.billing.balance" readonly=""></div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <!-- /.box-body -->
                     </div>
                  </div>
               </div>
            </div>
            <div class="modal-footer no-top-padding">
               <div class="pull-right">
                  @if(Common::can_create('billing'))
                  <button  type="button" class="btn btn-success" v-show="( updateBill && billing.billing_status == 'unhold' )" v-on:click="save($event)"><i class="fa fa-floppy-o" aria-hidden="true"></i> @lang('glob.save')</button>
                  <button type="button" class="btn btn-success" v-show="updateBill && billing.billing_status == 'unhold'" v-on:click="updatePrint($event)"  ><i class="fa fa-floppy-o" aria-hidden="true"></i>  @lang('glob.save') & <i class="fa fa-print" aria-hidden="true"></i> @lang('glob.print')</button>
                  @endif
                  @if(Common::can_update('billing')) 
                  <button   id="updatepay" type="button" class="btn btn-success" v-show="updateBill && billing.billing_status != 'unhold'" :disabled="(billing.tax_refund_flag == 1) ? true :false" v-on:click="checkValid()" data-backdrop="static" data-keyboard2="false" ><i class="fa fa-floppy-o" aria-hidden="true"></i> @lang('glob.update')</button>
                  @endif
                  @if(Common::can_create('billing'))
                  <button type="button" id="addpay" class="btn btn-success" v-show="saveBill" :disabled="(_bill_amount < 0)" v-on:click="save($event)"><i class="fa fa-floppy-o" aria-hidden="true"></i> @lang('glob.save')</button>
                  @endif
                  @if(Common::can_create('billing'))
                  <button type="button" id="addpay-print" class="btn btn-success" v-show="saveBill" v-on:click="savePrint"><i class="fa fa-floppy-o" aria-hidden="true"></i>  @lang('glob.save') & <i class="fa fa-print" aria-hidden="true"></i> @lang('glob.print')</button>
                  @endif
                  @if(Common::can_update('billing'))
                  <button  type="button" id="updatepay-print" class="btn btn-success" v-show="updateBill && billing.billing_status != 'unhold'" :disabled="(billing.tax_refund_flag == 1) ? true :false" v-on:click="updatePrint($event)"  ><i class="fa fa-floppy-o" aria-hidden="true"></i>  @lang('glob.update') & <i class="fa fa-print" aria-hidden="true"></i> @lang('glob.print')</button>
                  @endif
                  <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#paymentModal"> @lang('glob.close')</button>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>
@include('pages.billing.confirm-billing')	