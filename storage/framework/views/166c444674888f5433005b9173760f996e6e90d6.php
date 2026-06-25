 <aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">
    <!-- Sidebar user panel -->
    <div class="user-panel">
      <div class="pull-left image">
        <img src="<?php echo e(asset('img/user.png')); ?>" class="img-circle" alt="User Image">
      </div>
      <div class="pull-left info"> 
        <p><?php echo e(Auth::user()->email); ?></p>
        
      </div>
    </div>

    <!-- sidebar menu: : style can be found in sidebar.less -->
    <ul class="sidebar-menu">
      <?php if(Common::can_view("dashboard")): ?>
      <li <?php echo e(Common::setActive('dashboard/dashboard')); ?>>        
       <a href="<?php echo e(url('dashboard/dashboard')); ?>">
        <i class="fa fa-dashboard"></i> <span><?php echo app('translator')->getFromJson('menu.dashboard'); ?></span>        
      </a>     
    </li>
    <?php endif; ?>

    
    
    <?php if(Common::can_view("master")): ?>
    <!-- Master menu start -->
    <li class="treeview <?php echo e(Common::activeMainMenu('master')); ?>" >
      <a href="#">
        <i class="fa fa-user"></i> <span><?php echo app('translator')->getFromJson('menu.masters'); ?></span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <!-- master sub menu start -->
      <ul class="treeview-menu">
        <?php if(Common::has_access("bank")): ?>
        <li <?php echo e(Common::setActive('master/bank')); ?>><a href="<?php echo e(url('/master/bank')); ?>"><i class="fa fa-circle-o"></i> <?php echo app('translator')->getFromJson('glob.bank'); ?></a></li>
        <?php endif; ?>
        <?php if(Common::has_access("category")): ?>
        <li <?php echo e(Common::setActive('master/category')); ?>><a href="<?php echo e(url('/master/category')); ?>"><i class="fa fa-circle-o"></i> <?php echo app('translator')->getFromJson('glob.category'); ?></a></li>
        <?php endif; ?>
        <?php if(Common::has_access("customer")): ?>
        <li <?php echo e(Common::setActive('master/customer')); ?>><a href="<?php echo e(url('/master/customer')); ?>"><i class="fa fa-circle-o"></i> <?php echo app('translator')->getFromJson('glob.customer'); ?></a></li>
        <?php endif; ?>
        <?php if(Common::has_access("item")): ?>
        <li <?php echo e(Common::setActive('master/item')); ?>><a href="<?php echo e(url('/master/item')); ?>"><i class="fa fa-circle-o"></i> <?php echo app('translator')->getFromJson('glob.item'); ?></a></li>
        <?php endif; ?>
        <?php if(Common::has_access("location")): ?>
        <li <?php echo e(Common::setActive('master/location')); ?>><a href="<?php echo e(url('/master/location')); ?>"><i class="fa fa-circle-o"></i> <?php echo app('translator')->getFromJson('glob.location'); ?></a></li>
        <?php endif; ?>
        <?php if(Common::has_access("open_stock")): ?>
        <li <?php echo e(Common::setActive('master/open_stock')); ?>><a href="<?php echo e(url('/master/open_stock')); ?>"><i class="fa fa-circle-o"></i> <?php echo app('translator')->getFromJson('glob.opening_stock'); ?></a></li>
        <?php endif; ?>
        <?php if(Common::has_access("salesman")): ?>
        <li <?php echo e(Common::setActive('master/salesman')); ?>><a href="<?php echo e(url('/master/salesman')); ?>"><i class="fa fa-circle-o"></i> <?php echo app('translator')->getFromJson('glob.salesman'); ?></a></li>
        <?php endif; ?>
        <?php if(Common::has_access("supplier")): ?>
        <li <?php echo e(Common::setActive('master/supplier')); ?>><a href="<?php echo e(url('/master/supplier')); ?>"><i class="fa fa-circle-o"></i> <?php echo app('translator')->getFromJson('glob.supplier'); ?></a></li>
        <?php endif; ?>
        <?php if(Common::has_access("user")): ?>
        <li <?php echo e(Common::setActive('master/user')); ?>><a href="<?php echo e(url('/master/user')); ?>"><i class="fa fa-circle-o"></i> <?php echo app('translator')->getFromJson('glob.user'); ?></a></li>
        <?php endif; ?>
        <?php if(Common::has_access("unit")): ?>
        <li <?php echo e(Common::setActive('master/unit')); ?>><a href="<?php echo e(url('/master/unit')); ?>"><i class="fa fa-circle-o"></i> <?php echo app('translator')->getFromJson('glob.unit'); ?></a></li>
        <?php endif; ?>
        <?php if(Common::has_access("currency")): ?>
        <li <?php echo e(Common::setActive('master/currency')); ?>><a href="<?php echo e(url('/master/currency')); ?>"><i class="fa fa-circle-o"></i> <?php echo app('translator')->getFromJson('glob.currency'); ?></a></li>
        <?php endif; ?>
      </ul>
      <!-- master submenu end -->
    </li>
    <!-- master menu end -->
    <?php endif; ?>

    <?php if(Common::can_view("stock")): ?>
    
    <li class="treeview <?php echo e(Common::activeMainMenu('stock')); ?>">
      <a href="#">
        <i class="ion ion-cube" aria-hidden="true"></i> 
        <span><?php echo app('translator')->getFromJson('menu.stock'); ?></span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <?php if(Common::has_access("purchase_order")): ?>
        <li <?php echo e(Common::setActive('stock/purchase_order')); ?>><a href="<?php echo e(url('/stock/purchase_order')); ?>"><i class="fa fa-circle-o"></i> <?php echo app('translator')->getFromJson('glob.purchase_order'); ?></a></li>
        <?php endif; ?>
        <?php if(Common::has_access("purchase")): ?>
        <li <?php echo e(Common::setActive('stock/purchase')); ?>><a href="<?php echo e(url('/stock/purchase')); ?>"><i class="fa fa-circle-o"></i> <?php echo app('translator')->getFromJson('glob.purchase'); ?></a></li>
        <?php endif; ?>
        <?php if(Common::has_access("purchase_return")): ?>
        <li <?php echo e(Common::setActive('stock/purchase_return')); ?>><a href="<?php echo e(url('/stock/purchase_return')); ?>"><i class="fa fa-circle-o"></i> <?php echo app('translator')->getFromJson('glob.purchase_return'); ?></a></li>
        <?php endif; ?>
        <?php if(Common::has_access("display_stock")): ?>
        <li <?php echo e(Common::setActive('stock/display_stock')); ?>><a href="<?php echo e(url('/stock/display_stock')); ?>"><i class="fa fa-circle-o"></i> <?php echo app('translator')->getFromJson('glob.display_stock'); ?></a></li>
        <?php endif; ?>
        <?php if(Common::has_access("damage_stock")): ?>
        <li <?php echo e(Common::setActive('stock/damage_stock')); ?>><a href="<?php echo e(url('/stock/damage_stock')); ?>"><i class="fa fa-circle-o"></i> <?php echo app('translator')->getFromJson('glob.expiry_stock'); ?></a></li>
        <?php endif; ?>
        <?php if(Common::has_access("club_barcode")): ?>
        <li <?php echo e(Common::setActive('stock/club_barcode')); ?>><a href="<?php echo e(url('/stock/club_barcode')); ?>"><i class="fa fa-circle-o"></i> <?php echo app('translator')->getFromJson('glob.club_barcode'); ?></a></li>
        <?php endif; ?>
         <?php if(Common::has_access("stock_adjustment")): ?>
        <li <?php echo e(Common::setActive('stock/stock_adjustment')); ?>><a href="<?php echo e(url('/stock/stock_adjustment')); ?>"><i class="fa fa-circle-o"></i> <?php echo app('translator')->getFromJson('glob.stock_adjustment'); ?></a></li>
        <?php endif; ?>
      </ul>
    </li>
    
    <?php endif; ?>

    <?php if(Common::can_view("opening_cash")): ?>
    
    <li class="treeview <?php echo e(Common::activeMainMenu('opening_cash')); ?> ">
      <?php endif; ?>
      

      <?php if(Common::can_view("opening_cash")): ?>
      <li class="treeview <?php echo e(Common::activeMainMenu('opening_cash')); ?>" >
        <a href="#">
          <i class="ion ion-cash" aria-hidden="true"></i> <span><?php echo app('translator')->getFromJson('menu.opening_cash'); ?></span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <!-- Stock sub menu start -->
        <ul class="treeview-menu">
         <?php if(Common::has_access("opening_cash")): ?>
         <li <?php echo e(Common::setActive('opening_cash/opening_cash')); ?>><a href="<?php echo e(url('/opening_cash/opening_cash')); ?>"><i class="fa fa-credit-card" aria-hidden="true"></i> <span><?php echo app('translator')->getFromJson('menu.opening_cash'); ?></span></a></li>   
         <?php endif; ?>        
       </ul>
     </li>
     
     <?php endif; ?>

     <?php if(Common::can_view("billing")): ?>
     
     

     <?php if(Common::can_billscreen_view('billing','billscreen') == 1): ?>  
     <?php if(Common::has_access("billing1")): ?>
     <li <?php echo e(Common::setActive('billing/billing')); ?>><a href="<?php echo e(url('/billing/billing')); ?>"><i class="ion ion-ios-keypad" aria-hidden="true"></i> <span>Billing</span></a></li>   
     <?php endif; ?>
     <?php endif; ?>

     <?php if(Common::can_billscreen_view('billing','billscreen') == 2): ?> 
     <?php if(Common::has_access("billing2")): ?>
     <li <?php echo e(Common::setActive('billing/billing2')); ?>><a href="<?php echo e(url('/billing/billing2')); ?>"><i class="ion ion-ios-keypad" aria-hidden="true"></i> <span>Billing</span></a></li>   
     <?php endif; ?>
     <?php endif; ?>
     
     
     <?php endif; ?>

     <?php if(Common::can_view("ledger")): ?>
     <!-- opening_cash menu start -->
     <li class="treeview <?php echo e(Common::activeMainMenu('ledger')); ?>">
      <a href="#">
        <i class="fa fa-money" aria-hidden="true"></i> <span><?php echo app('translator')->getFromJson('menu.ledger'); ?></span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <!-- opening_cash sub menu start -->
      <ul class="treeview-menu">
        <?php if(Common::has_access("customer_ledger")): ?>
        <li <?php echo e(Common::setActive('ledger/customer_ledger')); ?>><a href="<?php echo e(url('ledger/customer_ledger')); ?>"><i class="fa fa-circle-o"></i> <?php echo app('translator')->getFromJson('glob.customer_ledger'); ?></a></li>
        <?php endif; ?>
        <?php if(Common::has_access("supplier_ledger")): ?>
        <li <?php echo e(Common::setActive('ledger/supplier_ledger')); ?>><a href="<?php echo e(url('ledger/supplier_ledger')); ?>"><i class="fa fa-circle-o"></i> <?php echo app('translator')->getFromJson('glob.supplier_ledger'); ?></a></li>
        <?php endif; ?>
      </ul>
      <!-- opening_cash sub menu ends -->
    </li>
    <!-- opening_cash menu end -->
    <?php endif; ?>

    <?php if(Common::can_view("financial")): ?>
    <!-- financial menu start -->
    <li class="treeview <?php echo e(Common::activeMainMenu('financial')); ?>">
      <a href="#">
        <i class="fa fa-briefcase" aria-hidden="true"></i> <span><?php echo app('translator')->getFromJson('menu.financial'); ?></span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <!-- financial sub menu start -->
      <ul class="treeview-menu">
        <?php if(Common::has_access("customer_receipt")): ?>
        <li <?php echo e(Common::setActive('financial/customer_receipt')); ?>><a href="<?php echo e(url('financial/customer_receipt')); ?>"><i class="fa fa-circle-o"></i> <?php echo app('translator')->getFromJson('glob.customer_receipt'); ?></a></li>
        <?php endif; ?>
        <?php if(Common::has_access("payment")): ?>
        <li <?php echo e(Common::setActive('financial/payment')); ?>><a href="<?php echo e(url('financial/payment')); ?>"><i class="fa fa-circle-o"></i> <?php echo app('translator')->getFromJson('glob.payment'); ?></a></li>
        <?php endif; ?>
      </ul>
      <!-- financial sub menu ends -->
    </li>
    <!-- financial menu end -->
    <?php endif; ?>


    <?php if(Common::can_view("reports")): ?>
    <li class="treeview <?php echo e(Common::activeMainMenu('reports')); ?>" >
      <a href="#">
        <i class="fa fa-file-text-o"></i> <span><?php echo app('translator')->getFromJson('menu.reports'); ?></span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <!-- master sub menu start -->
      <ul class="treeview-menu">
        <?php if(Common::has_access("birthday")): ?>
        <li <?php echo e(Common::setActive('reports/resource')); ?>><a href="<?php echo e(url('reports/birthday')); ?>"><i class="fa fa-circle-o"></i><?php echo app('translator')->getFromJson('glob.birthday'); ?></a></li>
        <?php endif; ?>
        <?php if(Common::has_access("anniversary")): ?>
        <li <?php echo e(Common::setActive('reports/anniversary')); ?>><a href="<?php echo e(url('reports/anniversary')); ?>"><i class="fa fa-circle-o"></i><?php echo app('translator')->getFromJson('glob.anniversary'); ?></a></li>
        <?php endif; ?>
        <?php if(Common::has_access("customer_list")): ?>
        <li <?php echo e(Common::setActive('reports/customer_list')); ?>><a href="<?php echo e(url('reports/customer_list')); ?>"><i class="fa fa-circle-o"></i><?php echo app('translator')->getFromJson('glob.customer_list'); ?></a></li>
        <?php endif; ?>
        <?php if(Common::has_access("item_list")): ?>
        <li <?php echo e(Common::setActive('reports/item_list')); ?>><a href="<?php echo e(url('reports/item_list')); ?>"><i class="fa fa-circle-o"></i><?php echo app('translator')->getFromJson('glob.item_list'); ?></a></li>
        <?php endif; ?>
        <?php if(Common::has_access("expiry_list")): ?>
        <li <?php echo e(Common::setActive('reports/expiry_list')); ?>><a href="<?php echo e(url('reports/expiry_list')); ?>"><i class="fa fa-circle-o"></i><?php echo app('translator')->getFromJson('glob.expiry_list'); ?></a></li>
        <?php endif; ?>
        <?php if(Common::has_access("stock_ledger")): ?>
        <li <?php echo e(Common::setActive('reports/stock_ledger')); ?>><a href="<?php echo e(url('reports/stock_ledger')); ?>"><i class="fa fa-circle-o"></i><?php echo app('translator')->getFromJson('glob.stock_ledger'); ?></a></li>
        <?php endif; ?>
        <?php if(Common::has_access("stock_balance_categorywise")): ?>
        <li <?php echo e(Common::setActive('reports/stock_balance_categorywise')); ?>><a href="<?php echo e(url('reports/stock_balance_categorywise')); ?>"><i class="fa fa-circle-o"></i><?php echo app('translator')->getFromJson('glob.stock_balance_categorywise'); ?></a></li>
        <?php endif; ?>
        <?php if(Common::has_access("stock_balance_summary")): ?>
        <li <?php echo e(Common::setActive('reports/stock_balance_summary')); ?>><a href="<?php echo e(url('reports/stock_balance_summary')); ?>"><i class="fa fa-circle-o"></i><?php echo app('translator')->getFromJson('glob.stock_balance_summary'); ?></a></li>
        <?php endif; ?>
        <?php if(Common::has_access("stock_reorder")): ?>
        <li <?php echo e(Common::setActive('reports/stock_reorder')); ?>><a href="<?php echo e(url('reports/stock_reorder')); ?>"><i class="fa fa-circle-o"></i><?php echo app('translator')->getFromJson('glob.stock_reorder'); ?></a></li>
        <?php endif; ?>
        <?php if(Common::has_access("purchase_report")): ?>
        <li <?php echo e(Common::setActive('reports/purchase_report')); ?>><a href="<?php echo e(url('reports/purchase_report')); ?>"><i class="fa fa-circle-o"></i><?php echo app('translator')->getFromJson('glob.purchase_report'); ?></a></li>
        <?php endif; ?>
        <?php if(Common::has_access("purchase_return_report")): ?>
        <li <?php echo e(Common::setActive('reports/purchase_return_report')); ?>><a href="<?php echo e(url('reports/purchase_return_report')); ?>"><i class="fa fa-circle-o"></i><?php echo app('translator')->getFromJson('glob.purchase_return_report'); ?></a></li>
        <?php endif; ?>
        <?php if(Common::has_access("salesman_report")): ?>
        <li <?php echo e(Common::setActive('reports/salesman_report')); ?>><a href="<?php echo e(url('reports/salesman_report')); ?>"><i class="fa fa-circle-o"></i><?php echo app('translator')->getFromJson('glob.salesman_report'); ?></a></li>
        <?php endif; ?>
        <?php if(Common::has_access("cashier_report")): ?>
        <li <?php echo e(Common::setActive('reports/cashier_report')); ?>><a href="<?php echo e(url('reports/cashier_report')); ?>"><i class="fa fa-circle-o"></i><?php echo app('translator')->getFromJson('glob.cashier_report'); ?></a></li>
        <?php endif; ?>
        <?php if(Common::has_access("deleted_bills")): ?>
        <li <?php echo e(Common::setActive('reports/deleted_bills')); ?>><a href="<?php echo e(url('reports/deleted_bills')); ?>"><i class="fa fa-circle-o"></i><?php echo app('translator')->getFromJson('glob.deleted_bills'); ?></a></li>
        <?php endif; ?>
        <?php if(Common::has_access("audit_report")): ?>
        <li <?php echo e(Common::setActive('reports/audit_report')); ?>><a href="<?php echo e(url('reports/audit_report')); ?>"><i class="fa fa-circle-o"></i><?php echo app('translator')->getFromJson('glob.audit_report'); ?></a></li>
        <?php endif; ?>
        <?php if(Common::has_access("vat_report") && Common::vatType('vat','vat_type')!=0): ?>
        <li <?php echo e(Common::setActive('reports/vat_report')); ?>><a href="<?php echo e(url('reports/vat_report')); ?>"><i class="fa fa-circle-o"></i><?php echo app('translator')->getFromJson('glob.vat_report'); ?></a></li>
        <?php endif; ?>
        <?php if(Common::has_access("stock_adjustment_report")): ?>
        <li <?php echo e(Common::setActive('reports/stock_adjustment_report')); ?>><a href="<?php echo e(url('reports/stock_adjustment_report')); ?>"><i class="fa fa-circle-o"></i><?php echo app('translator')->getFromJson('glob.stock_adjustment_report'); ?></a></li>
        <?php endif; ?>
      </ul>
    </li>
    <?php endif; ?>

    <?php if(Common::can_view("sales_reports")): ?>
    <li class="treeview <?php echo e(Common::activeMainMenu('sales_reports')); ?>" >
      <a href="#">
        <i class="fa fa-file-text-o"></i> <span><?php echo app('translator')->getFromJson('menu.sales_reports'); ?></span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <!-- master sub menu start -->
      <ul class="treeview-menu">      
        <?php if(Common::has_access("customer_sales_report")): ?>
        <li <?php echo e(Common::setActive('sales_reports/customer_sales_report')); ?>><a href="<?php echo e(url('sales_reports/customer_sales_report')); ?>"><i class="fa fa-circle-o"></i><?php echo app('translator')->getFromJson('glob.customer_sales_report'); ?></a></li>
        <?php endif; ?>

        <?php if(Common::has_access("customer_sales_summary")): ?>
        <li <?php echo e(Common::setActive('sales_reports/customer_sales_summary')); ?>><a href="<?php echo e(url('sales_reports/customer_sales_summary')); ?>"><i class="fa fa-circle-o"></i><?php echo app('translator')->getFromJson('glob.customer_sales_summary'); ?></a></li>
        <?php endif; ?>


        <?php if(Common::has_access("sales_profit")): ?>
        <li <?php echo e(Common::setActive('sales_reports/sales_profit')); ?>><a href="<?php echo e(url('sales_reports/sales_profit')); ?>"><i class="fa fa-circle-o"></i><?php echo app('translator')->getFromJson('glob.sales_profit'); ?></a></li>
        <?php endif; ?>


        <?php if(Common::has_access("cash_memo_cat")): ?>
        <li <?php echo e(Common::setActive('sales_reports/cash_memo_cat')); ?>><a href="<?php echo e(url('sales_reports/cash_memo_cat')); ?>"><i class="fa fa-circle-o"></i><?php echo app('translator')->getFromJson('glob.cash_memo_cat'); ?></a></li>
        <?php endif; ?>
        
        <?php if(Common::has_access("sales_summary")): ?>
        <li <?php echo e(Common::setActive('sales_reports/sales_summary')); ?>><a href="<?php echo e(url('sales_reports/sales_summary')); ?>"><i class="fa fa-circle-o"></i><?php echo app('translator')->getFromJson('glob.sales_summary'); ?></a></li>
        <?php endif; ?>

          <?php if(Common::has_access("brandwise_sales_and_sales_return")): ?>
        <li <?php echo e(Common::setActive('sales_reports/brandwise_sales_and_sales_return')); ?>><a href="<?php echo e(url('sales_reports/brandwise_sales_and_sales_return')); ?>"><i class="fa fa-circle-o"></i><?php echo app('translator')->getFromJson('glob.brandwise_sales_and_sales_return'); ?></a></li>
        <?php endif; ?>


        <?php if(Common::has_access("sales_return")): ?>
        <li <?php echo e(Common::setActive('sales_reports/sales_return')); ?>><a href="<?php echo e(url('sales_reports/sales_return')); ?>"><i class="fa fa-circle-o"></i><?php echo app('translator')->getFromJson('glob.sales_return'); ?></a></li>
        <?php endif; ?>


        <?php if(Common::has_access("item_sales")): ?>
        <li <?php echo e(Common::setActive('sales_reports/item_sales')); ?>><a href="<?php echo e(url('sales_reports/item_sales')); ?>"><i class="fa fa-circle-o"></i><?php echo app('translator')->getFromJson('glob.item_sales'); ?></a></li>
        <?php endif; ?>  

        <?php if(Common::has_access("quantity_sales")): ?>
        <li <?php echo e(Common::setActive('sales_reports/quantity_sales')); ?>><a href="<?php echo e(url('sales_reports/quantity_sales')); ?>"><i class="fa fa-circle-o"></i><?php echo app('translator')->getFromJson('glob.quantity_sales'); ?></a></li>
        <?php endif; ?>


        <?php if(Common::has_access("quantity_sales_inches")): ?>
        <li <?php echo e(Common::setActive('sales_reports/quantity_sales_inches')); ?>><a href="<?php echo e(url('sales_reports/quantity_sales_inches')); ?>"><i class="fa fa-circle-o"></i><?php echo app('translator')->getFromJson('glob.quantity_sales_inches'); ?></a></li>
        <?php endif; ?>
       

        <?php if(Common::has_access("x_sales")): ?>
        <li <?php echo e(Common::setActive('sales_reports/x_sales')); ?>><a href="<?php echo e(url('sales_reports/x_sales')); ?>"><i class="fa fa-circle-o"></i><?php echo app('translator')->getFromJson('glob.x_sales'); ?></a></li>
        <?php endif; ?>

        <?php if(Common::has_access("promotion")): ?>
        <li <?php echo e(Common::setActive('sales_reports/promotion')); ?>><a href="<?php echo e(url('sales_reports/promotion')); ?>"><i class="fa fa-circle-o"></i><?php echo app('translator')->getFromJson('glob.promotion'); ?></a></li>
        <?php endif; ?>

        <?php if(Common::has_access("loyalty_report")): ?>
        <li <?php echo e(Common::setActive('sales_reports/loyalty_report')); ?>><a href="<?php echo e(url('sales_reports/loyalty_report')); ?>"><i class="fa fa-circle-o"></i><?php echo app('translator')->getFromJson('glob.loyalty_report'); ?></a></li>
        <?php endif; ?>

        <?php if(Common::has_access("sales_pack")): ?>
        <li <?php echo e(Common::setActive('sales_reports/sales_pack')); ?>><a href="<?php echo e(url('sales_reports/sales_pack')); ?>"><i class="fa fa-circle-o"></i><?php echo app('translator')->getFromJson('glob.sales_pack'); ?></a></li>
        <?php endif; ?>
         <?php if(Common::has_access("crm_report")): ?>
        <li <?php echo e(Common::setActive('sales_reports/crm_report')); ?>><a href="<?php echo e(url('sales_reports/crm_report')); ?>"><i class="fa fa-circle-o"></i><?php echo app('translator')->getFromJson('glob.crm_report'); ?></a></li>
        <?php endif; ?>
        <?php if(Common::has_access("tax_refund")): ?>
        <li <?php echo e(Common::setActive('sales_reports/tax_refund')); ?>><a href="<?php echo e(url('sales_reports/tax_refund')); ?>"><i class="fa fa-circle-o"></i><?php echo app('translator')->getFromJson('glob.tax_refund'); ?></a></li>
        <?php endif; ?>

        <?php if(Common::has_access("customer_classification")): ?>
        <li <?php echo e(Common::setActive('sales_reports/customer_classification')); ?>><a href="<?php echo e(url('sales_reports/customer_classification')); ?>"><i class="fa fa-circle-o"></i><?php echo app('translator')->getFromJson('glob.customer_classification'); ?></a></li>
        <?php endif; ?>

      </ul>
    </li>
    <?php endif; ?>
    
    <?php if(Common::can_view("brand_classification")): ?>
    <?php if(Common::has_access("brand_classification")): ?>
    <li <?php echo e(Common::setActive('brand_classification/brand_classification')); ?> ><a href="<?php echo e(url('brand_classification/brand_classification')); ?>"><i class="fa fa-gift" aria-hidden="true"></i> <span><?php echo app('translator')->getFromJson('glob.brand_classification'); ?></span></a></li>
    <?php endif; ?>
    <?php endif; ?>

    <?php if(Common::can_view("loyalty")): ?>
    <?php if(Common::has_access("loyalty")): ?>
    <li <?php echo e(Common::setActive('loyalty/loyalty')); ?> ><a href="<?php echo e(url('loyalty/loyalty')); ?>"><i class="fa fa-gift" aria-hidden="true"></i> <span><?php echo app('translator')->getFromJson('glob.loyalty'); ?></span></a></li>
    <?php endif; ?>
    <?php endif; ?>


    <?php if(Common::can_view("options")): ?>
    <?php if(Common::has_access("options")): ?>
    <li <?php echo e(Common::setActive('options/options')); ?> ><a href="<?php echo e(url('options/options')); ?>"><i class="fa fa-sliders"></i> <span><?php echo app('translator')->getFromJson('menu.options'); ?></span></a></li>
    <?php endif; ?>
    <?php endif; ?>

    <?php if(Common::can_view("settings")): ?>
    <li class="treeview <?php echo e(Common::activeMainMenu('settings')); ?>" >
      <a href="#">
        <i class="fa fa-gear"></i> <span><?php echo app('translator')->getFromJson('menu.settings'); ?></span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <!-- master sub menu start -->
      <ul class="treeview-menu">
       <?php if(Common::has_access("resource")): ?>
       <li <?php echo e(Common::setActive('settings/resource')); ?>><a href="<?php echo e(url('settings/resource')); ?>"><i class="fa fa-circle-o"></i><?php echo app('translator')->getFromJson('glob.resources'); ?></a></li>
       <?php endif; ?>
       <?php if(Common::has_access("role")): ?>
       <li <?php echo e(Common::setActive('settings/role')); ?>><a href="<?php echo e(url('settings/role')); ?>"><i class="fa fa-circle-o"></i><?php echo app('translator')->getFromJson('glob.roles'); ?></a></li>
       <?php endif; ?>
     </ul>
   </li>
   <?php endif; ?>
 </ul>
</section>
<!-- /.sidebar -->
</aside> 