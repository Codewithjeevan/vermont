@extends('layouts.app')

@section('page-title')@lang('menu.options')@endsection

@section('page-subtitle')@lang('glob.add_edit_options')@endsection 

@section ('breadcrumb')

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> @lang('glob.dashboard')</a></li>
	<li class="active">@lang('menu.options')</li>
</ol>

@endsection 

@section('content') 
<div class="row">
	<div class="col-md-12">
		<div class="options">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bhoechie-tab-container">
				<!-- menu strat -->
				<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 bhoechie-tab-menu" >
					<div class="list-group">
						<!-- start general -->
						<a data-toggle="tab" href="#general" class="list-group-item active text-center tab-menu">
							<h4 class="fa fa-cogs fa-2x"></h4>
							<h6 class="title">@lang('menu.general')</h6>
						</a> 
						<!-- end general -->
						<!-- Billing button -->
						<a data-toggle="tab" href="#billing" class="list-group-item text-center tab-menu ">
							<h4 class="fa fa-credit-card fa-2x"></h4>
							<h6 class="title">@lang('menu.billing')</h6>
						</a>
						<!-- end billing -->
						<!-- Item button -->
						<a  data-toggle="tab" href="#item" class="list-group-item text-center tab-menu">
							<h4 class="icon ion-bag fa-3x"></h4><h6 class="title">@lang('glob.item')</h6>
						</a>
						<!-- end item -->

						<!--VAT -->
						@if(Auth::user()->id==1)
						<a  data-toggle="tab" href="#vat" class="list-group-item text-center tab-menu">
							<h4 class="fa fa-file-text fa-2x"></h4><h6 class="title">@lang('glob.vat_setting')</h6>
						</a>
						@endIf
						<!--End Vat-->
						<!--Setting-->
						<!-- <a  data-toggle="tab" href="#settings" class="list-group-item text-center tab-menu">
							<h4 class="fa fa-gear fa-2x"></h4><h6 class="title">@lang('menu.settings')</h6>
						</a> -->
						<!--End Setting-->
					</div>
				</div>
				<!-- End menu -->
				<!-- start content sction -->
				<div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 bhoechie-tab " >

					<!-- start general section -->
					<div id="general" class="bhoechie-tab-content active">
						@include("pages.options.general-options")
					</div> 
					<!-- end general section -->

					<!-- start billing content -->
					<div id="billing" class="bhoechie-tab-content  ">
						@include("pages.options.billing-options")
					</div>
					<!-- end billing section -->

					<!-- start item section -->
					<div id="item" class="bhoechie-tab-content">
						@include("pages.options.item-options")
					</div>
					<!-- end item section -->
					@if(Auth::user()->id==1)
					<div id="vat" class="bhoechie-tab-content">
						@include("pages.options.vat-options")
					</div>
					@endIf
					
					<!--start setting section -->
					{{--<div id="settings" class="bhoechie-tab-content">
						@include("pages.options.setting-options")
					</div>--}}
					<!--End setting section -->
				</div>
				<!-- end content section -->
			</div>

		</div>
	</div>
</div>
@endsection