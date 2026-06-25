 <aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">
    <!-- Sidebar user panel -->
    <div class="user-panel">
      <div class="pull-left image">
        <img src="{{ asset('img/user.png') }}" class="img-circle" alt="User Image">
      </div>
      <div class="pull-left info"> 
        <p>{{ Auth::user()->email }}</p>
        
      </div>
    </div>

    <!-- sidebar menu: : style can be found in sidebar.less -->
    <ul class="sidebar-menu">
      @if(Common::can_view("dashboard"))
      <li {{ Common::setActive('dashboard/dashboard') }}>        
       <a href="{{url('dashboard/dashboard')}}">
        <i class="fa fa-dashboard"></i> <span>@lang('menu.dashboard')</span>        
      </a>     
    </li>
    @endif

    
    
    @if(Common::can_view("master"))
    <!-- Master menu start -->
    <li class="treeview {{ Common::activeMainMenu('master') }}" >
      <a href="#">
        <i class="fa fa-user"></i> <span>@lang('menu.masters')</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <!-- master sub menu start -->
      <ul class="treeview-menu">
        @if(Common::has_access("bank"))
        <li {{ Common::setActive('master/bank') }}><a href="{{url('/master/bank')}}"><i class="fa fa-circle-o"></i> @lang('glob.bank')</a></li>
        @endif
        @if(Common::has_access("category"))
        <li {{ Common::setActive('master/category') }}><a href="{{url('/master/category')}}"><i class="fa fa-circle-o"></i> @lang('glob.category')</a></li>
        @endif
        @if(Common::has_access("customer"))
        <li {{ Common::setActive('master/customer') }}><a href="{{url('/master/customer')}}"><i class="fa fa-circle-o"></i> @lang('glob.customer')</a></li>
        @endif
        @if(Common::has_access("item"))
        <li {{ Common::setActive('master/item') }}><a href="{{url('/master/item')}}"><i class="fa fa-circle-o"></i> @lang('glob.item')</a></li>
        @endif
        @if(Common::has_access("location"))
        <li {{ Common::setActive('master/location') }}><a href="{{url('/master/location')}}"><i class="fa fa-circle-o"></i> @lang('glob.location')</a></li>
        @endif
        @if(Common::has_access("open_stock"))
        <li {{ Common::setActive('master/open_stock') }}><a href="{{url('/master/open_stock')}}"><i class="fa fa-circle-o"></i> @lang('glob.opening_stock')</a></li>
        @endif
        @if(Common::has_access("salesman"))
        <li {{ Common::setActive('master/salesman') }}><a href="{{url('/master/salesman')}}"><i class="fa fa-circle-o"></i> @lang('glob.salesman')</a></li>
        @endif
        @if(Common::has_access("supplier"))
        <li {{ Common::setActive('master/supplier') }}><a href="{{url('/master/supplier')}}"><i class="fa fa-circle-o"></i> @lang('glob.supplier')</a></li>
        @endif
        @if(Common::has_access("user"))
        <li {{ Common::setActive('master/user') }}><a href="{{url('/master/user')}}"><i class="fa fa-circle-o"></i> @lang('glob.user')</a></li>
        @endif
        @if(Common::has_access("unit"))
        <li {{ Common::setActive('master/unit') }}><a href="{{url('/master/unit')}}"><i class="fa fa-circle-o"></i> @lang('glob.unit')</a></li>
        @endif
        @if(Common::has_access("currency"))
        <li {{ Common::setActive('master/currency') }}><a href="{{url('/master/currency')}}"><i class="fa fa-circle-o"></i> @lang('glob.currency')</a></li>
        @endif
      </ul>
      <!-- master submenu end -->
    </li>
    <!-- master menu end -->
    @endif

    @if(Common::can_view("stock"))
    {{-- Stock Menu start --}}
    <li class="treeview {{ Common::activeMainMenu('stock') }}">
      <a href="#">
        <i class="ion ion-cube" aria-hidden="true"></i> 
        <span>@lang('menu.stock')</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        @if(Common::has_access("purchase_order"))
        <li {{ Common::setActive('stock/purchase_order') }}><a href="{{url('/stock/purchase_order')}}"><i class="fa fa-circle-o"></i> @lang('glob.purchase_order')</a></li>
        @endif
        @if(Common::has_access("purchase"))
        <li {{ Common::setActive('stock/purchase') }}><a href="{{url('/stock/purchase')}}"><i class="fa fa-circle-o"></i> @lang('glob.purchase')</a></li>
        @endif
        @if(Common::has_access("purchase_return"))
        <li {{ Common::setActive('stock/purchase_return') }}><a href="{{url('/stock/purchase_return')}}"><i class="fa fa-circle-o"></i> @lang('glob.purchase_return')</a></li>
        @endif
        @if(Common::has_access("display_stock"))
        <li {{ Common::setActive('stock/display_stock') }}><a href="{{url('/stock/display_stock')}}"><i class="fa fa-circle-o"></i> @lang('glob.display_stock')</a></li>
        @endif
        @if(Common::has_access("damage_stock"))
        <li {{ Common::setActive('stock/damage_stock') }}><a href="{{url('/stock/damage_stock')}}"><i class="fa fa-circle-o"></i> @lang('glob.expiry_stock')</a></li>
        @endif
        @if(Common::has_access("club_barcode"))
        <li {{ Common::setActive('stock/club_barcode') }}><a href="{{url('/stock/club_barcode')}}"><i class="fa fa-circle-o"></i> @lang('glob.club_barcode')</a></li>
        @endif
         @if(Common::has_access("stock_adjustment"))
        <li {{ Common::setActive('stock/stock_adjustment') }}><a href="{{url('/stock/stock_adjustment')}}"><i class="fa fa-circle-o"></i> @lang('glob.stock_adjustment')</a></li>
        @endif
      </ul>
    </li>
    {{-- Stock Menu end --}}
    @endif

    @if(Common::can_view("opening_cash"))
    {{-- Opening_Cash Menu Start --}}
    <li class="treeview {{ Common::activeMainMenu('opening_cash') }} ">
      @endif
      

      @if(Common::can_view("opening_cash"))
      <li class="treeview {{ Common::activeMainMenu('opening_cash') }}" >
        <a href="#">
          <i class="ion ion-cash" aria-hidden="true"></i> <span>@lang('menu.opening_cash')</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <!-- Stock sub menu start -->
        <ul class="treeview-menu">
         @if(Common::has_access("opening_cash"))
         <li {{ Common::setActive('opening_cash/opening_cash') }}><a href="{{url('/opening_cash/opening_cash')}}"><i class="fa fa-credit-card" aria-hidden="true"></i> <span>@lang('menu.opening_cash')</span></a></li>   
         @endif        
       </ul>
     </li>
     {{-- Billing Menu End --}}
     @endif

     @if(Common::can_view("billing"))
     {{-- Billing Menu Start --}}
     

     @if(Common::can_billscreen_view('billing','billscreen') == 1)  
     @if(Common::has_access("billing1"))
     <li {{ Common::setActive('billing/billing') }}><a href="{{url('/billing/billing')}}"><i class="ion ion-ios-keypad" aria-hidden="true"></i> <span>Billing</span></a></li>   
     @endif
     @endif

     @if(Common::can_billscreen_view('billing','billscreen') == 2) 
     @if(Common::has_access("billing2"))
     <li {{ Common::setActive('billing/billing2') }}><a href="{{url('/billing/billing2')}}"><i class="ion ion-ios-keypad" aria-hidden="true"></i> <span>Billing</span></a></li>   
     @endif
     @endif
     
     {{-- Billing Menu End --}}
     @endif

     @if(Common::can_view("ledger"))
     <!-- opening_cash menu start -->
     <li class="treeview {{ Common::activeMainMenu('ledger') }}">
      <a href="#">
        <i class="fa fa-money" aria-hidden="true"></i> <span>@lang('menu.ledger')</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <!-- opening_cash sub menu start -->
      <ul class="treeview-menu">
        @if(Common::has_access("customer_ledger"))
        <li {{ Common::setActive('ledger/customer_ledger') }}><a href="{{url('ledger/customer_ledger')}}"><i class="fa fa-circle-o"></i> @lang('glob.customer_ledger')</a></li>
        @endif
        @if(Common::has_access("supplier_ledger"))
        <li {{ Common::setActive('ledger/supplier_ledger') }}><a href="{{url('ledger/supplier_ledger')}}"><i class="fa fa-circle-o"></i> @lang('glob.supplier_ledger')</a></li>
        @endif
      </ul>
      <!-- opening_cash sub menu ends -->
    </li>
    <!-- opening_cash menu end -->
    @endif

    @if(Common::can_view("financial"))
    <!-- financial menu start -->
    <li class="treeview {{ Common::activeMainMenu('financial') }}">
      <a href="#">
        <i class="fa fa-briefcase" aria-hidden="true"></i> <span>@lang('menu.financial')</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <!-- financial sub menu start -->
      <ul class="treeview-menu">
        @if(Common::has_access("customer_receipt"))
        <li {{ Common::setActive('financial/customer_receipt') }}><a href="{{url('financial/customer_receipt')}}"><i class="fa fa-circle-o"></i> @lang('glob.customer_receipt')</a></li>
        @endif
        @if(Common::has_access("payment"))
        <li {{ Common::setActive('financial/payment') }}><a href="{{url('financial/payment')}}"><i class="fa fa-circle-o"></i> @lang('glob.payment')</a></li>
        @endif
      </ul>
      <!-- financial sub menu ends -->
    </li>
    <!-- financial menu end -->
    @endif


    @if(Common::can_view("reports"))
    <li class="treeview {{ Common::activeMainMenu('reports') }}" >
      <a href="#">
        <i class="fa fa-file-text-o"></i> <span>@lang('menu.reports')</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <!-- master sub menu start -->
      <ul class="treeview-menu">
        @if(Common::has_access("birthday"))
        <li {{ Common::setActive('reports/resource') }}><a href="{{ url('reports/birthday') }}"><i class="fa fa-circle-o"></i>@lang('glob.birthday')</a></li>
        @endif
        @if(Common::has_access("anniversary"))
        <li {{ Common::setActive('reports/anniversary') }}><a href="{{ url('reports/anniversary') }}"><i class="fa fa-circle-o"></i>@lang('glob.anniversary')</a></li>
        @endif
        @if(Common::has_access("customer_list"))
        <li {{ Common::setActive('reports/customer_list') }}><a href="{{ url('reports/customer_list') }}"><i class="fa fa-circle-o"></i>@lang('glob.customer_list')</a></li>
        @endif
        @if(Common::has_access("item_list"))
        <li {{ Common::setActive('reports/item_list') }}><a href="{{ url('reports/item_list') }}"><i class="fa fa-circle-o"></i>@lang('glob.item_list')</a></li>
        @endif
        @if(Common::has_access("expiry_list"))
        <li {{ Common::setActive('reports/expiry_list') }}><a href="{{ url('reports/expiry_list') }}"><i class="fa fa-circle-o"></i>@lang('glob.expiry_list')</a></li>
        @endif
        @if(Common::has_access("stock_ledger"))
        <li {{ Common::setActive('reports/stock_ledger') }}><a href="{{ url('reports/stock_ledger') }}"><i class="fa fa-circle-o"></i>@lang('glob.stock_ledger')</a></li>
        @endif
        @if(Common::has_access("stock_balance_categorywise"))
        <li {{ Common::setActive('reports/stock_balance_categorywise') }}><a href="{{ url('reports/stock_balance_categorywise') }}"><i class="fa fa-circle-o"></i>@lang('glob.stock_balance_categorywise')</a></li>
        @endif
        @if(Common::has_access("stock_balance_summary"))
        <li {{ Common::setActive('reports/stock_balance_summary') }}><a href="{{ url('reports/stock_balance_summary') }}"><i class="fa fa-circle-o"></i>@lang('glob.stock_balance_summary')</a></li>
        @endif
        @if(Common::has_access("stock_reorder"))
        <li {{ Common::setActive('reports/stock_reorder') }}><a href="{{ url('reports/stock_reorder') }}"><i class="fa fa-circle-o"></i>@lang('glob.stock_reorder')</a></li>
        @endif
        @if(Common::has_access("purchase_report"))
        <li {{ Common::setActive('reports/purchase_report') }}><a href="{{ url('reports/purchase_report') }}"><i class="fa fa-circle-o"></i>@lang('glob.purchase_report')</a></li>
        @endif
        @if(Common::has_access("purchase_return_report"))
        <li {{ Common::setActive('reports/purchase_return_report') }}><a href="{{ url('reports/purchase_return_report') }}"><i class="fa fa-circle-o"></i>@lang('glob.purchase_return_report')</a></li>
        @endif
        @if(Common::has_access("salesman_report"))
        <li {{ Common::setActive('reports/salesman_report') }}><a href="{{ url('reports/salesman_report') }}"><i class="fa fa-circle-o"></i>@lang('glob.salesman_report')</a></li>
        @endif
        @if(Common::has_access("cashier_report"))
        <li {{ Common::setActive('reports/cashier_report') }}><a href="{{ url('reports/cashier_report') }}"><i class="fa fa-circle-o"></i>@lang('glob.cashier_report')</a></li>
        @endif
        @if(Common::has_access("deleted_bills"))
        <li {{ Common::setActive('reports/deleted_bills') }}><a href="{{ url('reports/deleted_bills') }}"><i class="fa fa-circle-o"></i>@lang('glob.deleted_bills')</a></li>
        @endif
        @if(Common::has_access("audit_report"))
        <li {{ Common::setActive('reports/audit_report') }}><a href="{{ url('reports/audit_report') }}"><i class="fa fa-circle-o"></i>@lang('glob.audit_report')</a></li>
        @endif
        @if(Common::has_access("vat_report") && Common::vatType('vat','vat_type')!=0)
        <li {{ Common::setActive('reports/vat_report') }}><a href="{{ url('reports/vat_report') }}"><i class="fa fa-circle-o"></i>@lang('glob.vat_report')</a></li>
        @endif
        @if(Common::has_access("stock_adjustment_report"))
        <li {{ Common::setActive('reports/stock_adjustment_report') }}><a href="{{ url('reports/stock_adjustment_report') }}"><i class="fa fa-circle-o"></i>@lang('glob.stock_adjustment_report')</a></li>
        @endif
      </ul>
    </li>
    @endif

    @if(Common::can_view("sales_reports"))
    <li class="treeview {{ Common::activeMainMenu('sales_reports') }}" >
      <a href="#">
        <i class="fa fa-file-text-o"></i> <span>@lang('menu.sales_reports')</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <!-- master sub menu start -->
      <ul class="treeview-menu">      
        @if(Common::has_access("customer_sales_report"))
        <li {{ Common::setActive('sales_reports/customer_sales_report') }}><a href="{{ url('sales_reports/customer_sales_report') }}"><i class="fa fa-circle-o"></i>@lang('glob.customer_sales_report')</a></li>
        @endif

        @if(Common::has_access("customer_sales_summary"))
        <li {{ Common::setActive('sales_reports/customer_sales_summary') }}><a href="{{ url('sales_reports/customer_sales_summary') }}"><i class="fa fa-circle-o"></i>@lang('glob.customer_sales_summary')</a></li>
        @endif


        @if(Common::has_access("sales_profit"))
        <li {{ Common::setActive('sales_reports/sales_profit') }}><a href="{{ url('sales_reports/sales_profit') }}"><i class="fa fa-circle-o"></i>@lang('glob.sales_profit')</a></li>
        @endif


        @if(Common::has_access("cash_memo_cat"))
        <li {{ Common::setActive('sales_reports/cash_memo_cat') }}><a href="{{ url('sales_reports/cash_memo_cat') }}"><i class="fa fa-circle-o"></i>@lang('glob.cash_memo_cat')</a></li>
        @endif
        
        @if(Common::has_access("sales_summary"))
        <li {{ Common::setActive('sales_reports/sales_summary') }}><a href="{{ url('sales_reports/sales_summary') }}"><i class="fa fa-circle-o"></i>@lang('glob.sales_summary')</a></li>
        @endif

          @if(Common::has_access("brandwise_sales_and_sales_return"))
        <li {{ Common::setActive('sales_reports/brandwise_sales_and_sales_return') }}><a href="{{ url('sales_reports/brandwise_sales_and_sales_return') }}"><i class="fa fa-circle-o"></i>@lang('glob.brandwise_sales_and_sales_return')</a></li>
        @endif


        @if(Common::has_access("sales_return"))
        <li {{ Common::setActive('sales_reports/sales_return') }}><a href="{{ url('sales_reports/sales_return') }}"><i class="fa fa-circle-o"></i>@lang('glob.sales_return')</a></li>
        @endif


        @if(Common::has_access("item_sales"))
        <li {{ Common::setActive('sales_reports/item_sales') }}><a href="{{ url('sales_reports/item_sales') }}"><i class="fa fa-circle-o"></i>@lang('glob.item_sales')</a></li>
        @endif  

        @if(Common::has_access("quantity_sales"))
        <li {{ Common::setActive('sales_reports/quantity_sales') }}><a href="{{ url('sales_reports/quantity_sales') }}"><i class="fa fa-circle-o"></i>@lang('glob.quantity_sales')</a></li>
        @endif


        @if(Common::has_access("quantity_sales_inches"))
        <li {{ Common::setActive('sales_reports/quantity_sales_inches') }}><a href="{{ url('sales_reports/quantity_sales_inches') }}"><i class="fa fa-circle-o"></i>@lang('glob.quantity_sales_inches')</a></li>
        @endif
       

        @if(Common::has_access("x_sales"))
        <li {{ Common::setActive('sales_reports/x_sales') }}><a href="{{ url('sales_reports/x_sales') }}"><i class="fa fa-circle-o"></i>@lang('glob.x_sales')</a></li>
        @endif

        @if(Common::has_access("promotion"))
        <li {{ Common::setActive('sales_reports/promotion') }}><a href="{{ url('sales_reports/promotion') }}"><i class="fa fa-circle-o"></i>@lang('glob.promotion')</a></li>
        @endif

        @if(Common::has_access("loyalty_report"))
        <li {{ Common::setActive('sales_reports/loyalty_report') }}><a href="{{ url('sales_reports/loyalty_report') }}"><i class="fa fa-circle-o"></i>@lang('glob.loyalty_report')</a></li>
        @endif

        @if(Common::has_access("sales_pack"))
        <li {{ Common::setActive('sales_reports/sales_pack') }}><a href="{{ url('sales_reports/sales_pack') }}"><i class="fa fa-circle-o"></i>@lang('glob.sales_pack')</a></li>
        @endif
         @if(Common::has_access("crm_report"))
        <li {{ Common::setActive('sales_reports/crm_report') }}><a href="{{ url('sales_reports/crm_report') }}"><i class="fa fa-circle-o"></i>@lang('glob.crm_report')</a></li>
        @endif
        @if(Common::has_access("tax_refund"))
        <li {{ Common::setActive('sales_reports/tax_refund') }}><a href="{{ url('sales_reports/tax_refund') }}"><i class="fa fa-circle-o"></i>@lang('glob.tax_refund')</a></li>
        @endif

        @if(Common::has_access("customer_classification"))
        <li {{ Common::setActive('sales_reports/customer_classification') }}><a href="{{ url('sales_reports/customer_classification') }}"><i class="fa fa-circle-o"></i>@lang('glob.customer_classification')</a></li>
        @endif

      </ul>
    </li>
    @endif
    
    @if(Common::can_view("brand_classification"))
    @if(Common::has_access("brand_classification"))
    <li {{ Common::setActive('brand_classification/brand_classification') }} ><a href="{{ url('brand_classification/brand_classification') }}"><i class="fa fa-gift" aria-hidden="true"></i> <span>@lang('glob.brand_classification')</span></a></li>
    @endif
    @endif

    @if(Common::can_view("loyalty"))
    @if(Common::has_access("loyalty"))
    <li {{ Common::setActive('loyalty/loyalty') }} ><a href="{{ url('loyalty/loyalty') }}"><i class="fa fa-gift" aria-hidden="true"></i> <span>@lang('glob.loyalty')</span></a></li>
    @endif
    @endif


    @if(Common::can_view("options"))
    @if(Common::has_access("options"))
    <li {{ Common::setActive('options/options') }} ><a href="{{ url('options/options') }}"><i class="fa fa-sliders"></i> <span>@lang('menu.options')</span></a></li>
    @endif
    @endif

    @if(Common::can_view("settings"))
    <li class="treeview {{ Common::activeMainMenu('settings') }}" >
      <a href="#">
        <i class="fa fa-gear"></i> <span>@lang('menu.settings')</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <!-- master sub menu start -->
      <ul class="treeview-menu">
       @if(Common::has_access("resource"))
       <li {{ Common::setActive('settings/resource') }}><a href="{{ url('settings/resource') }}"><i class="fa fa-circle-o"></i>@lang('glob.resources')</a></li>
       @endif
       @if(Common::has_access("role"))
       <li {{ Common::setActive('settings/role') }}><a href="{{ url('settings/role') }}"><i class="fa fa-circle-o"></i>@lang('glob.roles')</a></li>
       @endif
     </ul>
   </li>
   @endif
 </ul>
</section>
<!-- /.sidebar -->
</aside> 