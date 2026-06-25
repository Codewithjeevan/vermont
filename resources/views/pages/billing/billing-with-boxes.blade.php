@extends('layouts.app')

<style type="text/css">
	.content-header{
		display: none !important;
	}
	.content{
		padding-top:10px !important;
	}
	
</style>

@section('content')
<div id="billingVueId" class="billingWithBox" v-cloak>
	<div class="row" >

		<!-- Left section -->
		<div class="col-md-9 col-sm-9   less-padding-right less-padding-left">
			{{-- Billing create start --}}
			@include("pages.billing.billing-create-plain")
			{{-- Billing create end --}}
			@include("pages.billing.billing-item-box")

			<div class="row">
				<div class="col-md-1 col-sm-1 col-xs-12 no-padding">
					<div class="pull-right" style="margin-left:20px;">
						@if(Common::can_option_view('billing','search_items'))
						<div style="margin-top: 10px; margin-bottom: 5px;" class="btn-group btn-group-lg">
							<button class="btn no-shadow btn-primary" data-toggle="modal" data-target="#searchModal" data-backdrop="static" data-keyboard="false"><i class="fa fa-search"></i></button>
						</div>
						@endif
						@include("pages.billing.billing-controls")
					</div>
				</div>
				<div class="col-md-11 col-sm-11  col-xs-12 less-padding-left ">
					{{-- Billing item list table start --}}
					@include("pages.billing.billing-item-list")
					{{-- Billing item list table end --}}
				</div>
			</div>
		</div>
		<div class="col-md-3 col-sm-3  less-padding-right less-padding-left">
			<!-- right sidebar -->

			@include("pages.billing.billing-sidebar")
			{{--Right sidebar end --}}
			
		</div>
	</div>    

	@include("pages.billing.billing-search-item")
	@include("pages.billing.client-buying-history")
	@include("pages.billing.xReport")
	@include("pages.billing.zReport")
	@include("pages.billing.zQtyReport")	
	@include("pages.billing.confirm-modal")
	@include("pages.billing.sms-billing-modal")
</div>
@endsection

