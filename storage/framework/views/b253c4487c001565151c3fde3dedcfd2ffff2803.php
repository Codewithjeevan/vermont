
<div  class="btn-group btn-group-lg pull-right">
	<?php if(Common::can_create('billing')): ?>
	<button id="plus" class="btn btn-success no-shadow control-plus control-btn" :disabled="(return_bill_check  ? true :false)"><i class="fa fa-plus"></i></button>
	<?php endif; ?>
	<?php if(Common::can_create('billing')): ?>
	<button id="minus" class="btn btn-warning no-shadow control-minus control-btn" :disabled="(return_bill_check  ? true :false)"><i class="fa fa-minus"></i></button>
	<?php endif; ?>
	
	<button  class="btn btn-danger no-shadow control-btn" v-on:click="deleteItem()" :disabled="(return_bill_check  ? true :false)"><i class="fa fa-trash"></i></button>
	

	<?php if(Common::can_option_view('billing','hold_bill')): ?>
	<button  class="btn no-shadow btn-danger control-btn" v-if="billing.billing_status=='hold' " v-on:click="unhold()" :disabled="((itemList.length > 0 && !bill_return ) || !home_delivery_check ? false :true)"><i class="fa fa-pause" aria-hidden="true"></i></button>
	<button  class="btn no-shadow btn-success control-btn" v-if="(!get_existing_bill_check && billing.billing_status!='hold') || billing.billing_status=='unhold' " v-on:click="hold()" :disabled="((return_bill_check ) || (itemList.length == 0 && !bill_return) || bill_return || home_delivery_check? true :false)"><i class="fa fa-play" aria-hidden="true"></i></button>
	<?php endif; ?>
</div>
