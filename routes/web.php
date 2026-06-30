<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
|--------------------------------------------------------------------------
*/

/*
|---------------------------------------------------------------------------
|   Url Format
|---------------------------------------------------------------------------
| 	http://domain/prefix/resource_slug
|
|---------------------------------------------------------------------------
*/

/** Application start route */

Route::get('/', 'WelcomeController@validateKey');
Route::get('/getPlanetAuthCredential','WelcomeController@getPlanetAuthCredential');
Route::get('/PlanetLogin','WelcomeController@PlanetLogin');
Route::post('/PlanetAccessToken','WelcomeController@PlanetAccessToken');



/** Test Route */
Route::get('test', 'PageController@test');

Route::get('404', 'PageController@show404');

Route::get('500', 'PageController@show500');



Route::get('error/{page}', 'PageController@showError');


/** Auth Routes */
Auth::routes();


/** Home Route */
Route::get('/home', 'PageController@home');

Route::get("common/add/{controller}",'CommonController@add');



/** alertnative to laravel post logout */
Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');
Route::get('/admin', '\App\Http\Controllers\Auth\LoginController@supperLogin');

Route::post('clientkey', '\App\Http\Controllers\LicenceController@generateClientKey');
Route::post('storedetails', '\App\Http\Controllers\LicenceController@storeDetails');
Route::post('getdetails', '\App\Http\Controllers\LicenceController@getDetails');


/** All Masters route */
Route::group(["prefix"=>"master", "middleware" => ["auth","access"]], function(){

	/**
 	* Customer route
 	*/
 	Route::resource('customer', 'CustomersController');

 	Route::get('customer/{mobile}/getmobile', 'CustomersController@getCustomerByMobile');

 	/** Supplier route */

 	Route::resource('supplier', 'SupplierController');

	/**
 	* Category under master tab route
 	*/
 	Route::resource('category', 'CategoriesController');

	/**
 	* unit under master tab route
 	*/
 	Route::resource('unit', 'UnitsController');

 	/** Item Master */
 	Route::resource('item', 'ItemsController');


 	/**
 	* salesman under master tab route
 	*/
 	Route::resource('/salesman', 'SalesmanController');
 	
 	/**
 	* users under master tab route
 	*/
 	Route::resource('/user', 'UsersController');

 	/**
 	* open_stock under master tab route
 	*/
 	Route::resource('/open_stock', 'OpeningStockController');

 	/** bank master route*/
 	Route::resource('bank', 'BanksController'); 

 	/**
 	* Location route
 	*/
 	Route::resource('location', 'LocationsController');

 	/** bank master route*/
 	Route::resource('bank', 'BanksController'); 

 		/**
 	* open_stock under master tab route
 	*/
 	Route::get('/open_stock/get/{id}', 'OpeningStockController@getItem');

 	Route::get('currency/xls', 'CurrencyController@exportToXls');	
 	Route::get('currency/pdf', 'CurrencyController@exportToPdf');	

 	/** bank master route*/
 	Route::resource('currency', 'CurrencyController'); 



 });

Route::post('uploadItemProfile', 'ItemsController@uploadItemProfile');

Route::group(["prefix"=>"master", "middleware" => ["auth"]], function(){
	Route::get('items', 'ItemsController@ajaxindex');	
	Route::get('items/get', 'ItemsController@getItems');	
	Route::post('items', 'ItemsController@ajaxstore');	
	Route::get('items/{id}/edit', 'ItemsController@ajaxedit');
	Route::delete('items/{id}', 'ItemsController@ajaxdelete');
	Route::put('items/{id}', 'ItemsController@ajaxupdate');

	/** get item object from itemMater for all models */
	Route::post('items/search_item', 'ItemsController@searchItem');
	
	Route::post('items/uploadFile', "ItemsController@uploadFile");

	Route::get('items/search_barcode', "ItemsController@searchBarcode");

	Route::get('items/search_name', "ItemsController@searchItemName");
	



	
	/** get items from xy offer and package item search */
	Route::post('item/getitem/itemname', "ItemsController@getSingleItemByName");
	Route::post('item/getitem/barcode', "ItemsController@getSingleItemByBarcode");
	Route::post('item/pkg/getitem/itemname', "ItemsController@getpkgItemByName");
	Route::post('item/pkg/getitem/barcode', "ItemsController@getpkgItemByBarcode");
	
	
	Route::post('items/saveitems', "ItemsController@saveitems");
	Route::get('open_stock', "OpeningStockController@index");
	Route::post('open_stock/uploadFile', "OpeningStockController@uploadFile");
	Route::post('open_stock/saveopeningStock', "OpeningStockController@saveOpenStock");

	/* Routing for Export */
	Route::get('categorys/xls', 'CategoriesController@exportToXls');
	Route::get('categorys/pdf', 'CategoriesController@exportToPdf');
	Route::get('units/xls', 'UnitsController@exportToXls');
	Route::get('units/pdf', 'UnitsController@exportToPdf');
	Route::get('suppliers/xls', 'SupplierController@exportToXls');
	Route::get('suppliers/pdf', 'SupplierController@exportToPdf');
	Route::get('items/xls', 'ItemsController@exportToXlsItemsMaster');
	Route::get('items/pdf', 'ItemsController@exportToPdfItemsMaster');
	Route::get('salesmans/xls', 'SalesmanController@exportToXls');
	Route::get('salesmans/pdf', 'SalesmanController@exportToPdf');
	Route::get('users/xls', 'UsersController@exportToXls');
	Route::get('users/pdf', 'UsersController@exportToPdf');
	Route::get('open_stocks/xls', 'OpeningStockController@exportToXls');
	Route::get('open_stocks/pdf', 'OpeningStockController@exportToPdf');
	Route::get('banks/xls', 'BanksController@exportToXls');
	Route::get('banks/pdf', 'BanksController@exportToPdf');
	Route::get('locations/xls', 'LocationsController@exportToXls');
	Route::get('locations/pdf', 'LocationsController@exportToPdf');	
	Route::get('customers/xls', 'CustomersController@exportToXls');
	Route::get('customers/pdf', 'CustomersController@exportToPdf');
	
	/* End Routing for Export */

});

/** root for send Mail setting option */
Route::post('/option/send_email_settingoption','MailController@sendEmailSettingOption');

/** root for setting option */
Route::get("/settingoption",'CommonController@settingOption');


/** Settings Route */
Route::group(['prefix'=>'settings', "middleware" => ["auth","access"]], function(){
	
	/** Resource Route */
	Route::resource('resource', 'ResourceController');	
	
	/** Role Master Route */
	Route::resource('role', 'RolesController');	

	/** Route for updating privileges */
	Route::PUT('privileges/{id}', 'RolesController@updatePrivileges');

});


/** Reports Route */
Route::group(['prefix'=>'reports', "middleware" =>["auth","access"]], function(){
	
	/** Birthday Route */
	Route::get('/birthday', 'CustomersController@getBirthdays');
	
	/** Aniversary Route */
	Route::get('/anniversary', 'CustomersController@getAnniversary');
	//Route::post('/anniversary/anniversary_list', 'CustomersController@getAnniversaryList');

	/** Customer list Route */
	Route::get('/customer_list', 'CustomersController@getCustomerList');

	/** Item list Route */
	Route::get('/item_list', 'ItemsController@getItemList');

	/** Expiry list Route */
	Route::get('/expiry_list', 'ItemsController@getExpiryList');
	Route::post('/expiry_list/all', 'ItemsController@getExpiryListAll');

	/** Stock Ledger Route */
	Route::get('/stock_ledger', 'StockLedgerController@index');
	Route::post('/stock_ledger/ledger', 'StockLedgerController@getLedger'); 
	Route::post('/stock_ledger/export', 'StockLedgerController@exportTo');

	/** Stock Balance Route */
	Route::get('/stock_balance_categorywise', 'StockBalanceCategorywiseController@index');
	Route::post('/stock_balance_categorywise/ledger', 'StockBalanceCategorywiseController@getLedger'); 
	Route::post('/stock_balance_categorywise/export', 'StockBalanceCategorywiseController@exportTo');

	/** Stock Balance Summary Route */
	Route::get('/stock_balance_summary', 'StockBalanceSummaryController@index');
	Route::post('/stock_balance_summary/ledger', 'StockBalanceSummaryController@getLedger'); 
	Route::post('/stock_balance_summary/export', 'StockBalanceSummaryController@exportTo');

	/** Stock Reorder Route */
	Route::get('/stock_reorder', 'StockReorderController@index');
	Route::post('/stock_reorder/ledger', 'StockReorderController@getLedger'); 
	Route::post('/stock_reorder/export', 'StockReorderController@exportTo');

	/** Puchase Report Route */
	Route::get('/purchase_report', 'PurchaseReportController@index');
	Route::post('/purchase_report/ledger', 'PurchaseReportController@getLedger'); 
	Route::post('/purchase_report/export', 'PurchaseReportController@exportTo');

	/** Salesman Report Route */
	Route::get('/salesman_report', 'SalesmanReportController@index');
	Route::post('/salesman_report/ledger', 'SalesmanReportController@getLedger'); 
	Route::post('/salesman_report/export', 'SalesmanReportController@exportTo');

	/** Cashier Report Route */
	Route::get('/cashier_report', 'CashierReportController@index');
	Route::post('/cashier_report/ledger', 'CashierReportController@getLedger'); 
	Route::post('/cashier_report/export', 'CashierReportController@exportTo');

	/** Puchase Return Report Route  */
	Route::get('/purchase_return_report', 'PurchaseReturnReportController@index');   
	Route::post('/purchase_return_report/ledger', 'PurchaseReturnReportController@getLedger');  
	Route::post('/purchase_return_report/export', 'PurchaseReturnReportController@exportTo'); 
	Route::post('/purchase_return_report/seachitem', 'PurchaseReturnReportController@getItemBySearchAll'); 


	/** deleted bills */
	Route::get('/deleted_bills', 'BillingsController@getDeletedBills');
	Route::get('/deleted_bills/export', 'BillingsController@exportTo');

	/** audit report */
	Route::get('/audit_report', 'AuditReportController@getAudits');
	Route::get('/audit_report/export', 'AuditReportController@exportTo');

	/** vat report */
	Route::get('/vat_report', 'VatReportController@index');
	Route::post('/vat_report/ledger', 'VatReportController@getLedger'); 
	Route::post('/vat_report/export', 'VatReportController@exportTo');

	/** Expense Report Route */
	Route::get('/stock_adjustment_report', 'StockAdjustmentReportController@index');
	Route::post('/stock_adjustment_report/ledger', 'StockAdjustmentReportController@getLedger'); 
	Route::post('/stock_adjustment_report/export', 'StockAdjustmentReportController@exportTo');

});

/** Sales Reports Route */
Route::group(['prefix'=>'sales_reports', "middleware" =>["auth","access"]], function(){
	

	/** Customer Sales Report Route */
	Route::get('/customer_sales_report', 'SalesReportController@customer');
	Route::post('/customer_sales_report/ledger', 'SalesReportController@getLedger'); 
	Route::post('/customer_sales_report/export', 'SalesReportController@exportTo');

	/** Customer Sales Report Route */
	Route::get('/customer_sales_summary', 'SalesReportController@customerCSummary');
	Route::post('/customer_sales_summary/ledger', 'SalesReportController@getLedgerCSummary'); 
	Route::post('/customer_sales_summary/export', 'SalesReportController@exportToCSummary');
/** BrandWise Sales and Sales Return Summary Report Route */
Route::get('/brandwise_sales_and_sales_return', 'SalesReportController@indexSalesandSalesReturn');
Route::post('/brandwise_sales_and_sales_return/ledger', 'SalesReportController@getLedgerSalesandSalesReturn'); 
Route::post('/brandwise_sales_and_sales_return/export', 'SalesReportController@exportToBrandwiseSalesandSalesReturn');

	/** Sales Profit Report Route */
	Route::get('/sales_profit', 'SalesReportController@customerSProfit');
	Route::post('/sales_profit/ledger', 'SalesReportController@getLedgerSProfit'); 
	Route::post('/sales_profit/export', 'SalesReportController@exportToSProfit');

	/** category Wise Cash Memo Report Route */
	Route::get('/cash_memo_cat', 'SalesReportController@categoryCatCashMemo');
	Route::post('/cash_memo_cat/ledger', 'SalesReportController@getLedgerCatCashMemo'); 
	Route::post('/cash_memo_cat/export', 'SalesReportController@exportToCatCashMemo');

	/** Sales Summary Report Route */
	Route::get('/sales_summary', 'SalesReportController@indexSalesSummary');
	Route::post('/sales_summary/ledger', 'SalesReportController@getLedgerSalesSummary'); 
	Route::post('/sales_summary/export', 'SalesReportController@exportToSalesSummary');

	/** Categorywise Sales Summary Report Route */
	Route::get('/categorywise_sales_summary', 'SalesReportController@indexCategorywiseSalesSummary');
	Route::post('/categorywise_sales_summary/ledger', 'SalesReportController@getLedgerCategorywiseSalesSummary'); 
	Route::post('/categorywise_sales_summary/export', 'SalesReportController@exportToCategorywiseSalesSummary');


	/** Sales Return Report Route */
	Route::get('/sales_return', 'SalesReportController@indexSalesReturn');
	Route::post('/sales_return/ledger', 'SalesReportController@getLedgerSalesReturn'); 
	Route::post('/sales_return/export', 'SalesReportController@exportToSalesReturn');

	/** Item Sales Report Route */
	Route::get('/item_sales', 'SalesReportController@indexItemSales');
	Route::post('/item_sales/ledger', 'SalesReportController@getLedgerItemSales'); 
	Route::post('/item_sales/export', 'SalesReportController@exportToItemSales');

	/** Promotion Report Route */
	Route::get('/promotion', 'SalesReportController@indexPromotion');
	Route::post('/promotion/ledger', 'SalesReportController@getLedgerPromotion'); 
	Route::post('/promotion/export', 'SalesReportController@exportToPromotion');

	/** Quantity Wise Sales Report Route */
	Route::get('/quantity_sales', 'SalesReportController@indexQuantitySales');
	Route::post('/quantity_sales/ledger', 'SalesReportController@getLedgerQuantitySales'); 
	Route::post('/quantity_sales/export', 'SalesReportController@exportToQuantitySales');

	/** Quantity Wise 3 Inches Sales Report Route */
	Route::get('/quantity_sales_inches', 'SalesReportController@indexQuantitySalesInches');
	Route::post('/quantity_sales_inches/ledger', 'SalesReportController@getLedgerQuantitySalesInches'); 
	Route::post('/quantity_sales_inches/export', 'SalesReportController@exportToQuantitySalesInches');
	Route::post('/quantity_sales_inches/sendmail','MailController@sendMailQtySalesInches');

	/** Quantity Wise Sales Report Route */
	Route::get('/x_sales', 'SalesReportController@indexXSales');
	Route::post('/x_sales/ledger', 'SalesReportController@getLedgerXSales'); 
	Route::post('/x_sales/export', 'SalesReportController@exportToXSales');
	Route::post('/x_sales/sendmail','MailController@sendMailSalesX');

	/** Promotion Report Route */
	Route::get('/loyalty_report', 'SalesReportController@indexLoyalty');
	Route::post('/loyalty_report/ledger', 'SalesReportController@getLedgerLoyalty'); 
	Route::post('/loyalty_report/export', 'SalesReportController@exportToLoyalty');

	/** Sales Summary Report Route */
	Route::get('/sales_pack', 'SalesReportController@indexSalesPack');
	Route::post('/sales_pack/ledger', 'SalesReportController@getLedgerSalesPack'); 
	Route::post('/sales_pack/export', 'SalesReportController@exportToSalesPack');

	/**CRM Report Route */

	Route::get('/crm_report','SalesReportController@indexCRM');
	Route::post('/crm_report/ledger', 'SalesReportController@getLedgerCRMReport');
	Route::post('/crm_report/export', 'SalesReportController@exportToCRMReport');

	/** category Wise Tax Refund Report Route */
	Route::get('/tax_refund', 'SalesReportController@taxRefund');
	Route::post('/tax_refund/ledger', 'SalesReportController@getLedgerTaxRefund'); 
	Route::post('/tax_refund/export', 'SalesReportController@exportToTaxRefund');


	/**brandwise Customer Classification Report Route */
	Route::get('/customer_classification', 'SalesReportController@CustomerClassification');
	Route::post('/customer_classification/ledger', 'SalesReportController@getLedgerCustomerClassification'); 
	Route::post('/customer_classification/export', 'SalesReportController@exportToCustomerClassificationreport');
	
});


/** Billing Route */
Route::group(['prefix'=>'billing', "middleware" =>["auth","access"]], function(){

	/** normal billing screen */
	Route::resource('billing', 'BillingsController');

	/** Billing Route */
	Route::get('billing2', 'BillingsController@billing2Index');

	Route::post('/billing/savereturn', 'BillingsController@saveReturn');	

	Route::post('/billing/payHomeDelivery', 'BillingsController@payHomeDelivery');
	/** Billing PDF Route */
	
	
	/** to get client buying history */
	Route::post('/billing/history', 'BillingsController@history');
	/*To get report details*/
	Route::post('/billing/ledger', 'BillingsController@getLedgerXSales');
	Route::post('/billing/sendmailz','MailController@sendMailZ');
	Route::post('/billing/sendmailz_qty','MailController@sendMailZQty');
	Route::post('/billing/sendmailx','MailController@sendMailX');
	Route::post('/billing/verify','BillingsController@verify');
	Route::post('/billing/eod','BillingsController@eod');
	Route::post('/billing/sendsms','MailController@sendBillingSMS');

	/** get items from server for billing */
	Route::post('/billing/getItem/barcode', 'BillingsController@getItemByBarcode');
	Route::post('/billing/getItem/barcodewithserial', 'BillingsController@getItemByBarcodeWithSerial');
	Route::post('/billing/getItem/seachitem', 'BillingsController@getItemBySearchAll');
	Route::post('/billing/getbill/getexisting', 'BillingsController@getExistingBill');
	Route::post('/billing/getbill/getexistingreturn', 'BillingsController@getExistingBill');
	Route::post('/billing/getreturn/getreturnbill', 'BillingsController@getReturnBill');
	Route::post('/billing/getbill/gethomebillexisting', 'BillingsController@getHomeBill');
	Route::post('/billing/getbill/activatebilling', 'BillingsController@activateBilling');
	/** added url for get hold bill from dropdown list  */
	Route::post('/billing/getbill/getexistinghold', 'BillingsController@getHoldBill');


	Route::get('/billing/search/bill', 'BillingsController@searchBill');

	/** for check bill have any return bill or not  */
	Route::post('/billing/getbill/checkBillReturnExist', 'BillingsController@checkBillReturnExist');
	
	/**get Z-Sale Report Data*/
	Route::get('/billing/zsales/getdata', 'BillingsController@reportZData');
	
	/**get Z-QtySale Report Data*/
	Route::get('/billing/zsales/getzqtydata', 'BillingsController@reportZQtyData');

	/** get hold bill list */
	Route::get('/billing/getbill/getholdbillids', "BillingsController@getHoldBillAll");

	/** get Home Bill bill list */
	Route::get('/billing/getbill/gethomebillids', "BillingsController@getHomeBillAll");

	/**post Passport barcode and get Customer details */
	Route::post('/billing/getCustomer/passport', 'BillingsController@getCustomerByPassport');

	Route::post('/billing/getbill/getexistingtaxRefund', 'BillingsController@getexistingtaxRefund');

	
	
});



/** Reports Route */
Route::group(['prefix'=>'financial', "middleware" =>["auth","access"]], function(){
	
	/** Receipt Route */
	Route::resource('/customer_receipt', 'CustomerReceiptsController');
	Route::PUT('/customer_receipt/addBank', "CustomerReceiptsController@addBank");

	Route::post('/customer_receipt/get/get_bills_per_customer', 'CustomerReceiptsController@getBillsPerCustomer');

	/**
 	* payment route
 	*/
 	Route::resource('/payment', 'PaymentsController');

 	Route::get('/payment/{id}/getbills', 'PaymentsController@getBills');
 	Route::get('/payment/get/labels_mall_details', 'PaymentsController@getLabelsMallDetails');

 	
 	
 });


/** All Stock route */
Route::group(["prefix"=>"stock", "middleware" => ["auth","access"]], function(){



	/**
 	* Purchase route
 	*/
 	Route::resource('purchase', "PurchaseController");

 	Route::get('purchase/{id}/getpo', "PurchaseController@getPurchaseOrder");
 	Route::post('purchase/uploadFile', "PurchaseController@uploadFile");
 	Route::post('purchase/getcpvat', "PurchaseController@getCPVatClub");
 	Route::get('purchase/get/category_list', "PurchaseController@getCatAll");
 	Route::post('purchase/get/po_list', "PurchaseController@getPOList");

 	/**
 	* Issue route
 	*/
 	Route::resource('display_stock','IssueStockController');
 	/**
 	* Expiry stock route
 	*/
 	Route::resource('damage_stock', 'DamageStockController');
 	/**
 	* Club barcode route
 	*/
 	Route::resource('club_barcode','ClubBarcodeController');

 	/** getItem for opeartions */
 	Route::post('club_barcode/getItem/byname', "ClubBarcodeController@getItemByName");
 	Route::post('club_barcode/getItem/bybarcode', "ClubBarcodeController@getItemByBarcode");
 	

 	/** Purchase order route */
 	Route::resource('purchase_order', 'PurchaseOrderController');

 	/** Purchase return route */
 	Route::resource('purchase_return', 'PurchaseReturnsController');
 	Route::get('purchase_return/{id}/getitems', "PurchaseReturnsController@getSupplierItems");

 	/** getpurchase return items */
 	Route::post('purchase_return/getitemsearch', "PurchaseReturnsController@getItem");
 	Route::post('purchase_return/maxqty', "PurchaseReturnsController@getMaxQty");

 });
/** Stock route end */


/** Ajax routing for stock/purchase_order */
Route::group(["prefix"=>"stock", "middleware" => ["auth"]], function(){
	Route::get('purchase_orders', 'PurchaseOrderController@ajaxindex');	
	Route::post('purchase_orders', 'PurchaseOrderController@ajaxstore');	
	Route::delete('purchase_orders/{id}', 'PurchaseOrderController@ajaxdelete');
	Route::get('purchase_orders/{id}/edit', 'PurchaseOrderController@ajaxedit');
	Route::put('purchase_orders/{id}', 'PurchaseOrderController@ajaxupdate');
	Route::get('purchase_orders/xls/{id}', 'PurchaseOrderController@exportToXls');
	Route::get('purchase_orders/sendmail/{id}','MailController@purchaseOrdersendMail');
	Route::get('purchase_orders/pdf/{id}', 'PurchaseOrderController@exportToPdf');	
});


/** Ajax routing for stock/damage_stock  */
Route::group(["prefix"=>"stock", "middleware" => ["auth"]], function(){
	Route::get('damage_stocks', 'DamageStockController@ajaxindex');	
	Route::post('damage_stocks', 'DamageStockController@ajaxstore');	
	Route::delete('damage_stocks/{id}', 'DamageStockController@ajaxdelete');
	Route::get('damage_stocks/{id}/edit', 'DamageStockController@ajaxedit');
	Route::put('damage_stocks/{id}', 'DamageStockController@ajaxupdate');
	Route::get('damage_stocks/getitems/{id}', 'DamageStockController@getItemsByMRN');

});

/** Ajax routing for stock/issue  */
Route::group(["prefix"=>"stock", "middleware" => ["auth"]], function(){
	Route::get('issues', 'IssueStockController@ajaxindex');	
	Route::post('issues', 'IssueStockController@ajaxstore');	
	Route::delete('issues/{id}', 'IssueStockController@ajaxdelete');
	Route::get('issues/{id}/edit', 'IssueStockController@ajaxedit');
	Route::put('issues/{id}', 'IssueStockController@ajaxupdate');
});

/** Ajax routing for stock adjustment  */
Route::group(["prefix"=>"stock", "middleware" => ["auth"]], function(){ 
	Route::get('stock_adjustment', 'StockAdjustmentController@index');			
	Route::post('stock_adjustment', 'StockAdjustmentController@ajaxstore');	
	Route::get('view/stock_adjustment/{id}/edit', 'StockAdjustmentController@ajaxedit');
});

/** All ledger route */
Route::group(["prefix"=>"ledger", "middleware" => ["auth","access"]], function(){

	/**
 	* Customer Ledger route
 	*/
 	Route::get('/customer_ledger', 'CustomerLedgersController@index');
 	Route::get('/customer_ledger/{id}', 'CustomerLedgersController@getLedger'); 
 	Route::post('/customer_ledger/exportTo', 'CustomerLedgersController@exportTo');	
 	
 	/**
 	* Supplier Ledger return route
 	*/
 	Route::get('/supplier_ledger', 'SupplierLedgerController@index'); 	
 	Route::get('/supplier_ledger/{id}', 'SupplierLedgerController@getLedger'); 	
 	Route::post('supplier_ledger/export', 'SupplierLedgerController@exportTo');

 });
/** Ledger route end */

/** Ajax routing for reports */
Route::group(["prefix"=>"reports", "middleware" => ["auth"]], function(){	
	Route::get('customer_list/xls', 'CustomersController@exportToXls');
	Route::get('customer_list/pdf', 'CustomersController@exportToPdf');
	Route::post('expiry_list/export', 'ItemsController@exportToExpiry');
	Route::get('item_list/xls', 'ItemsController@exportToXlsItems');
	Route::get('item_list/pdf', 'ItemsController@exportToPdfItems');
	Route::get('birthday_list/xls', 'CustomersController@exportToXlsBirthday');
	Route::get('birthday_list/pdf', 'CustomersController@exportToPdfBirthday');
	Route::get('anniversary_list/xls', 'CustomersController@exportToXlsAnniversary');
	Route::get('anniversary_list/pdf', 'CustomersController@exportToPdfAnniversary');
	Route::post('anniversary_list/sendmail','MailController@aniversarysendMail');		
	Route::post('birthday_list/sendmail','MailController@birthdaysendMail');
	Route::post('customer_list/sendmail', 'MailController@customersendMail');
	Route::post('sendsms','MailController@sendSms');			
});

/** Opening Cash Route */
Route::group(['prefix'=>'opening_cash', "middleware" =>["auth","access"]], function(){
	
	/** Opening Cash Route */
	Route::resource('/opening_cash', 'OpeningCashController');	

});

/** Dashboard Route */
Route::group(['prefix'=>'dashboard', "middleware" =>["auth","access"]], function(){
	
	/** Dashboard route */
	Route::resource('/dashboard', 'DashboardController');	
	Route::get('/dashboard/daily/sales', 'DashboardController@getSalesDaily');	
	Route::get('/dashboard/monthly/sales', 'DashboardController@getSalesmonthly');	
	Route::get('/dashboard/yearly/sales', 'DashboardController@getSalesYearly');	

});

/** Dashboard Route */
Route::group(['prefix'=>'options', "middleware" =>["auth","access"]], function(){
	
	/** Option route Route */
	Route::resource('/options', 'OptionsController');	
	Route::post('options/uploadlogo', 'OptionsController@uploadLogo');	

	/** root for check vat Records */
	Route::get('options/get/check_vat_records','OptionsController@checkVatRecords');

});

/** Routing for Billing */
Route::group(["prefix"=>"billing", "middleware" => ["auth"]], function(){
	
	Route::get('pdf', 'BillingsController@billingPdf');
	
});

Route::group(["prefix"=>"loyalty", "middleware" => ["auth"]], function(){
	
	Route::get('loyalty', 'LoyaltyController@index');

	Route::post('loyalty', 'LoyaltyController@update');
	
});

/** Opening Cash Route */
Route::group(['prefix'=>'brand_classification', "middleware" =>["auth","access"]], function(){
	
	/** Opening Cash Route */
	Route::resource('/brand_classification', 'BrandClassificationController');	

});











