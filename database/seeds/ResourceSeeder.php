<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
class ResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('resources')->insert(array(
        array('id' => '1','res_name' => 'Master','res_desc' => 'Master','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'master', 'parent_resource' => 0),        
        array('id' => '2','res_name' => 'Customer Master','res_desc' => 'Customer Master','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'customer', 'parent_resource' => 1),
        array('id' => '3','res_name' => 'Unit Master','res_desc' => 'unit Master','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'unit', 'parent_resource' => 1),
        array('id' => '4','res_name' => 'Category Master','res_desc' => 'Category','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'category', 'parent_resource' => 1),
        array('id' => '5','res_name' => 'Supplier Master','res_desc' => 'Supplier Master','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'supplier', 'parent_resource' => 1),
        array('id' => '6','res_name' => 'Item Master','res_desc' => 'Item Master','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'item', 'parent_resource' => 1),
        array('id' => '7','res_name' => 'Salesman Master','res_desc' => 'Salesman','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'salesman', 'parent_resource' => 1),
        array('id' => '8','res_name' => 'User Master','res_desc' => 'users Master','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'users', 'parent_resource' => 1),
        array('id' => '9','res_name' => 'opening Stock Entries','res_desc' => 'opening Stock Entries','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'open_stock', 'parent_resource' => 1),
        array('id' => '10','res_name' => 'Bank master','res_desc' => 'Bank Master','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'bank', 'parent_resource' => 1),
        array('id' => '11','res_name' => 'Location master','res_desc' => 'Location Master','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'location', 'parent_resource' => 1),

        array('id' => '12','res_name' => 'Stock','res_desc' => 'Stock','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'stock', 'parent_resource' => 0),
        array('id' => '13','res_name' => 'Purchase stock','res_desc' => 'Purchase stock','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'purchase', 'parent_resource' => 12),
        array('id' => '14','res_name' => 'Purchase return stock','res_desc' => 'Purchase return stock','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'purchase_return', 'parent_resource' => 12),
        array('id' => '15','res_name' => 'Display stock','res_desc' => 'Display stock','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'display_stock', 'parent_resource' => 12),
        array('id' => '16','res_name' => 'Damage stock','res_desc' => 'Damage stock','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'damage_stock', 'parent_resource' => 12),
        array('id' => '17','res_name' => 'Club barcode stock','res_desc' => 'Club barcode stock','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'club_barcode', 'parent_resource' => 12),

        array('id' => '18','res_name' => 'Billing','res_desc' => 'Billing','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'billing', 'parent_resource' => 0),
        array('id' => '19','res_name' => 'Billing screen 1','res_desc' => 'Billing','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'billing', 'parent_resource' => 18),
        //array('id' => '20','res_name' => 'Billing screen 2','res_desc' => 'Billing','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'billing2', 'parent_resource' => 18),

        array('id' => '21','res_name' => 'Ledger','res_desc' => 'Ledger','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'ledger', 'parent_resource' => 0),
        array('id' => '22','res_name' => 'Customer Ledger','res_desc' => 'Customer Ledger','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'customer_ledger', 'parent_resource' => 21),
        array('id' => '23','res_name' => 'Supplier Ledger','res_desc' => 'Supplier Ledger','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'supplier_ledger', 'parent_resource' => 21),

        array('id' => '24','res_name' => 'Financial','res_desc' => 'financial','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'financial', 'parent_resource' => 0),
        array('id' => '25','res_name' => 'Receipt','res_desc' => 'receipt','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'customer_receipt', 'parent_resource' => 24),
        array('id' => '26','res_name' => 'payment','res_desc' => 'payment','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'payment', 'parent_resource' => 24),

        array('id' => '27','res_name' => 'Reports','res_desc' => 'Reports','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'reports', 'parent_resource' => 0),
        array('id' => '28','res_name' => 'Birthday List','res_desc' => 'Birthday List','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'birthday', 'parent_resource' => 27),
        array('id' => '29','res_name' => 'Anniversary List','res_desc' => 'Anniversary List','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'anniversary', 'parent_resource' => 27),

        array('id' => '30','res_name' => 'Options','res_desc' => 'Options','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'options', 'parent_resource' => 0),

        array('id' => '31','res_name' => 'Settings','res_desc' => 'Settings','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'settings', 'parent_resource' => 0),
        array('id' => '32','res_name' => 'Resource Master','res_desc' => 'Resource Master','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'resource', 'parent_resource' => 31),
        array('id' => '33','res_name' => 'Role Master','res_desc' => 'Role Master','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'role', 'parent_resource' => 31),
        array('id' => '34','res_name' => 'Privilege Master','res_desc' => 'privileges','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'privileges', 'parent_resource' => 31),
        
        array('id' => '35','res_name' => 'Purchase order stock','res_desc' => 'Purchase order stock','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'purchase_order', 'parent_resource' => 12),
        
        array('id' => '36','res_name' => 'Customert List Report','res_desc' => 'Customert List Report','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'customer_list', 'parent_resource' => 27),
        array('id' => '37','res_name' => 'item List Report','res_desc' => 'Item List Report','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'item_list', 'parent_resource' => 27),
        array('id' => '38','res_name' => 'Expiry List Report','res_desc' => 'Expiry item List Report','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'expiry_list', 'parent_resource' => 27),
        array('id' => '39','res_name' => 'Stock Ledger Report','res_desc' => 'Stock Ledger Report','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'stock_ledger', 'parent_resource' => 27),
        array('id' => '40','res_name' => 'Stock Balance Categorywise','res_desc' => 'Stock Balance Categorywise','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'stock_balance_categorywise', 'parent_resource' => 27),
        array('id' => '41','res_name' => 'Stock Balance Summary','res_desc' => 'Stock Balance Summary','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'stock_balance_summary', 'parent_resource' => 27),
        array('id' => '42','res_name' => 'Stock Reorder','res_desc' => 'Stock Reorder','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'stock_reorder', 'parent_resource' => 27),
        array('id' => '43','res_name' => 'Purchase Report','res_desc' => 'Purchase Report','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'purchase_report', 'parent_resource' => 27),
        array('id' => '44','res_name' => 'Salesman Report','res_desc' => 'Salesman Report','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'salesman_report', 'parent_resource' => 27),  
        array('id' => '45','res_name' => 'Cashier Report','res_desc' => 'Cashier Report','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'cashier_report', 'parent_resource' => 27),  

        array('id' => '46','res_name' => 'Purchase Return Report','res_desc' => 'Purchase Return Report','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'purchase_return_report', 'parent_resource' => 27),      


        array('id' => '47','res_name' => 'Opening Cash','res_desc' => 'Opening Cash','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'opening_cash', 'parent_resource' => 0),
        array('id' => '48','res_name' => 'Dashboard','res_desc' => 'Dashboard','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'dashboard', 'parent_resource' => 0),

        array('id' => '49','res_name' => 'Sales Reports','res_desc' => 'Sales Reports','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'sales_reports', 'parent_resource' => 0),

        array('id' => '50','res_name' => 'Customer Sales Report','res_desc' => 'Customer Sales Report','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'customer_sales_report', 'parent_resource' => 49),
        array('id' => '51','res_name' => 'Customer Sales Summary Report','res_desc' => 'Customer Sales Summary Report','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'customer_sales_summary_report', 'parent_resource' => 49),
        array('id' => '52','res_name' => 'Sales Profit','res_desc' => 'Sales Profit','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'sales_profit', 'parent_resource' => 49),

        array('id' => '53','res_name' => 'Cash Memo Categorywise','res_desc' => 'Cash Memo Categorywise','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'cash_memo_cat', 'parent_resource' => 49),
        array('id' => '54','res_name' => 'Sales Summary','res_desc' => 'Sales Summary','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'sales_summary', 'parent_resource' => 49),
        array('id' => '55','res_name' => 'Sales Return','res_desc' => 'Sales Return','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'sales_return', 'parent_resource' => 49),
        array('id' => '56','res_name' => 'Item Sales','res_desc' => 'Item Sales','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'item_sales', 'parent_resource' => 49),
        array('id' => '57','res_name' => 'Qyantitywise Sales','res_desc' => 'Qyantitywise Sales','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'quantity_sales', 'parent_resource' => 49),
        array('id' => '58','res_name' => 'Qyantitywise Sales 3 Inches','res_desc' => 'Qyantitywise Sales 3 Inches','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'quantity_sales_inches', 'parent_resource' => 49),
        array('id' => '59','res_name' => 'X-Sales','res_desc' => 'X-Sales','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'x_sales', 'parent_resource' => 49),
        array('id' => '60','res_name' => 'Promotion','res_desc' => 'Promotion','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'promotion', 'parent_resource' => 49),

        array('id' => '61','res_name' => 'Deleted Bills','res_desc' => 'you can see deleted bills from here','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'deleted_bills', 'parent_resource' => 27),

        array('id' => '62','res_name' => 'Audit Report','res_desc' => 'you can see updated records and remarks here','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'audit_report', 'parent_resource' => 27),

        array('id' => '64','res_name' => 'Loyalty Program Parent','res_desc' => 'Here you can create or udpate loyalty program','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'loyalty', 'parent_resource' => 0),
        array('id' => '65','res_name' => 'Loyalty Program','res_desc' => 'Here you can create or udpate loyalty program','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'loyalty', 'parent_resource' => 64),

        array('id' => '66','res_name' => 'Loyalty Report','res_desc' => 'Loyalty Report','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'loyalty_report', 'parent_resource' => 49),

        array('id' => '67','res_name' => 'Vat Report','res_desc' => 'Vat Report','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'vat_report', 'parent_resource' => 27),
        array('id' => '68','res_name' => 'currency','res_desc' => 'currency','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'currency', 'parent_resource' => 1),
        array('id' => '69','res_name' => 'sales_pack','res_desc' => 'sales_pack','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'sales_pack', 'parent_resource' => 49),
        array('id' => '70','res_name' => 'CRM Report','res_desc' => 'crm_report','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'crm_report', 'parent_resource' => 49),
          array('id' => '71','res_name' => 'Stock Adjustment','res_desc' => 'stock_adjustment','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'stock_adjustment', 'parent_resource' => 12),
         array('id' => '72','res_name' => 'Stock Adjustment Report','res_desc' => 'stock_adjustment_report','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'stock_adjustment_report', 'parent_resource' => 27),
        array('id' => '73','res_name' => 'Categorywise Sales Summary','res_desc' => 'categorywise_sales_summary','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'categorywise_sales_summary', 'parent_resource' => 49),
        array('id' => '74','res_name' => 'Tax Refund','res_desc' => 'tax_refund','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'tax_refund', 'parent_resource' => 49),
        array('id' => '75','res_name' => 'Brand Classification','res_desc' => 'Brand Classification','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'brand_classification', 'parent_resource' => 0),
        array('id' => '76','res_name' => 'Customer Classification Report','res_desc' => 'customer_classification','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'customer_classification', 'parent_resource' => 49),
        ));
}
}
