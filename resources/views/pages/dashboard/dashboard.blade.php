@extends('layouts.app')

@section('page-title')@lang('glob.dashboard')@endsection

@section('page-subtitle')@lang('glob.control_panel')@endsection 

@section ('breadcrumb')

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> @lang('glob.dashboard')</a></li>
	<li class="active">@lang('glob.dashboard')</li>
</ol>

@endsection

@section('content') 
<div id="dashboardVueId" v-cloak>

  <!-- Small boxes (Stat box) -->
  <div class="row">
    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-maroon">
        <div class="inner">
          <h3>{{$item}}</h3>

          <p>@lang('glob.items')</p>
        </div>
        <div class="icon">
          <i class="ion ion-bag"></i>
        </div>
        <a href="/master/item" class="small-box-footer">@lang('glob.more_info') <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-orange">
        <div class="inner">
          <h3>{{$supplier}}</h3>

          <p>@lang('glob.suppliers')</p>
        </div>
        <div class="icon">
          <i class="ion ion-person-add"></i>
        </div>
        <a href="/master/supplier" class="small-box-footer">@lang('glob.more_info') <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-green">
        <div class="inner">
          <h3>{{$user}}</h3>

          <p>@lang('glob.users')</p>
        </div>
        <div class="icon">
          <i class="ion ion-person"></i>
        </div>
        <a href="/master/user" class="small-box-footer">@lang('glob.more_info') <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-red">
        <div class="inner">
          <h3>{{$lowStockCount}}</h3>

          <p>@lang('glob.low_stock')</p>
        </div>
        <div class="icon">
          <i class="ion ion-battery-low"></i>
        </div>
        <a href="/reports/stock_reorder?showall=1" class="small-box-footer">@lang('glob.more_info') <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- ./col -->
  </div>
  <!-- /.row  First Row-->

  <div class="row">
    <div class="col-md-12">
      <div class="nav-tabs-custom" style="cursor: move;">
        <!-- Tabs within a box -->
        <ul class="nav nav-tabs pull-right ui-sortable-handle">

          <li class="header pull-left"><span>  Sales Chart 
            <i id="spinner" class="fa fa-spinner fa-pulse fa-lg fa-fw" style="display: none"></i>
            <span class="sr-only">Loading...</span></span>
          </li>
          <div class="btn-group pull-right tab-header">
            <button type="button" class="btn  btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
              <i class="fa fa-gear"></i>
            </button>
            <ul class="dropdown-menu pull-right" role="menu">
              <li><a v-on:click="salesChart()"><i class="ion ion-ios-pulse-strong" aria-hidden="true"></i> Line Chart <i class="fa fa-check text-green" v-if="chart=='line'" aria-hidden="true"></i></a></li>
              <li><a v-on:click="barChart()"><i class="ion ion-stats-bars" aria-hidden="true"></i> Bar Chart <i class="fa fa-check text-green" v-if="chart=='bar'" aria-hidden="true"></i></a></li>
            </ul>
          </div>
          <li class="header pull-right tab-header"><a href="#sales-chart" class="chart-tab" data-toggle="tab" aria-expanded="false" v-on:click="updateSales('yearly', $event)">Yearly</a></li>
          <li class="header pull-right tab-header"><a href="#sales-chart" class="chart-tab" data-toggle="tab" aria-expanded="false" v-on:click="updateSales('monthly')">Monthly</a></li>
          <li class="header pull-right tab-header active"><a href="#sales-chart" class="chart-tab" data-toggle="tab" aria-expanded="false" v-on:click="updateSales('daily')">Daily</a></li>
          <li class="header pull-right tab-header "><a href="#sales-chart" data-toggle="tab" aria-expanded="false" v-on:click="updateSales('hourly')">Hourly</a></li>
        </ul>
        <div class="tab-content no-padding">

          <div class="chart tab-pane active" id="sales-chart" style="position: relative; height: 300px;">
            <div class="col-md-8">
              <canvas class="chart tab-pane" id="lineChart"  height="300" style="display: none"></canvas>
              <canvas class="chart tab-pane" id="barChart"  height="300" ></canvas>
            </div>
            <div class="col-md-4">
              <canvas  id="pieChart"  height="250"></canvas>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div >
        <div class="row">

          <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box">
              <span class="info-box-icon bg-green"><i class="ion ion-ios-cart-outline"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Total Sales</span>
                <span class="info-box-number"> {{ Common::curr() }} @{{ fixIt(floatCurrentSalesAmount) }}</span>
                <span><small class="pull-right">Total items sold <b>@{{ intCurrentSale }}</b></small></span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>


          <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box">
              <span class="info-box-icon bg-red"><i class="ion ion-ios-undo-outline"></i></span>

              <div class="info-box-content">
               <span class="info-box-text">Total Returns</span>
               <span class="info-box-number">{{ Common::curr() }} @{{ fixIt(floatCurrentReturnAmount) }}</span>
               <span><small class="pull-right">Total items returned <b>@{{ intCurrentReturn }}</b></small></span>
             </div>
             <!-- /.info-box-content -->
           </div>
           <!-- /.info-box -->
         </div>


         <div class="col-md-4 col-sm-6 col-xs-12">

          <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="ion ion-ios-filing-outline"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Total Transations</span>
              <span class="info-box-number">@{{ intCurrentTransactions }}</span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>

      </div>
    </div>
  </div>
</div>





<div class="row">

  <div class="col-md-4">
    <!-- Widget: user widget style 1 -->
    <div class="box box-widget widget-user-2">
      <!-- Add the bg color to the header using any of the bg-* classes -->
      <div class="widget-user-header bg-maroon">
        <div class="row no-padding">
          <div class="col-md-2">
            <i class="ion ion-arrow-graph-up-right" style="font-size: 40px;"></i>
          </div>
          <div class="col-md-10">
            <h4>Top Selling Items</h4>
          </div>
        </div>
      </div>
      <div class="box-footer no-padding widget-box" >
        <ul class="nav nav-stacked">
          <li v-for="i in arrTopSalingItems"><a >@{{ i.itemName }} <span class="pull-right badge bg-maroon">@{{ i.total_sales }}</span></a></li>
          <h4 v-if="arrTopSalingItems.length == 0" style="padding:30px">No Items have sold in current <span v-if="dur=='hourly'">hour</span> <span v-if="dur=='daily'">day</span> <span v-if="dur=='monthly'">month</span> <span v-if="dur=='yearly'">year</span></h4>
        </ul>
      </div>
    </div>
    <!-- /.widget-user -->
  </div>

  <div class="col-md-4">
    <!-- Widget: user widget style 1 -->
    <div class="box box-widget widget-user-2">
      <!-- Add the bg color to the header using any of the bg-* classes -->
      <div class="widget-user-header bg-dark-green">
        <div class="row no-padding">
          <div class="col-md-2">
            <i class="ion ion-android-people" style="font-size: 40px;"></i>
          </div>
          <div class="col-md-10">
            <h4>Cashier Sales</h4>
          </div>
        </div>
      </div>
      <div class="box-footer no-padding widget-box widget-scroll">
        <ul class="nav nav-stacked">
          <li v-for="i in arrCashierSales"><a >@{{ i.email }} <span class="pull-right badge bg-dark-green">@{{ i.cashier_sales }}</span></a></li>
          <h4 v-if="arrCashierSales.length == 0" style="padding:30px">There is nothing to show</h4>
        </ul>
      </div>
    </div>
    <!-- /.widget-user -->
  </div>

  <div class="col-md-4">
    <!-- Widget: user widget style 1 -->
    <div class="box box-widget widget-user-2">
      <!-- Add the bg color to the header using any of the bg-* classes -->
      <div class="widget-user-header bg-indigo">
        <div class="row no-padding">
          <div class="col-md-2">
            <i class="ion ion-ios-people" style="font-size: 40px;"></i>
          </div>
          <div class="col-md-10">
            <h4>Salesman Performance</h4>
          </div>
        </div>
      </div>
      <div class="box-footer no-padding widget-box widget-scroll">
        <ul class="nav nav-stacked">
         <li v-for="i in arrSalesmanPer"><a>@{{ i.name }} <span class="pull-right badge bg-indigo">@{{ i.salesman_per }}</span></a></li>
         <h4 v-if="arrSalesmanPer.length == 0" style="padding:30px">There are no salesmans</h4>
       </ul>
     </div>
   </div>
   <!-- /.widget-user -->
 </div>

</div>

</div>
@endsection