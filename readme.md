##Master 3.0.2
date:10/12/2019

> Stock Adjustment Report

- Added the reference number in the table view as well as excel,pdf and print.


##Master 3.0.1
date:14/10/2019

> Stock Adjustment
  > Stock Adjustment

- Add Stock Adjustment Form under “Stock” Main Menu.

- Add Migrations for stock Adjustment 

- Main Screen : Stock Adjusted Date, Stock Adjustment ID fields and Save ,refresh ,cancel Button.

- Add Item Screen : Item Name, Barcode, Unit, Stock(nonEditable), Quantity , Adjust Through(Excess, Shortage and Wastage) ,CP ,Value Adjusted(CP Rate * Qty)

- All the data and Logged in user will be saved in database(Not shown on screen).

- Excess will add to stock and shortage and wastage will diduct from stock.[Excess (+), Shortage(-) and Wastage(-)].

> Stock Adjustment Report

- Stock adjustment Report   under  ‘Reports’ Menu

- Add From and To date Filter Itemname (Item Name),Barcode (Barcode or ALL), User,show button.

- In table view columns name #,Item Name,barcode,Qty,Unit,Cost,Amt,User,Date

- To Shows stock Adjustment group wise (Excess, Shortage and Wastage).

- In Table view footer total  Net Loss/Net value Adjusted.

- Export the Stock Adjustment  Report in Xls,Pdf,print 

- By Default when screen loading all data of current date is showing.

> Stock Ledger

- Add new adjustment column after sale column.

- stock adjustment will be shown in to adjustment column 

- In “Particular column” sentence will be  Stock Adjustment Id : ___ Date ___

- Excess will add to stock and shortage and wastage will remove from stock.[Excess (+), Shortage(-) and Wastage(-)].

> Stock Balance

- Adding new stock type in background stock calculations

- Excess will add to stock and shortage and wastage will remove from stock.[Excess (+), Shortage(-) and Wastage(-)].

> Stock Balance Summary

- Adding new stock Adjustment column in table view  

- Excess will add to stock and shortage and wastage will remove from stock.[Excess (+), Shortage(-) and Wastage(-)].


##Master 3.0.0
date:1/08/2019

>Customer Data Report
  - Created the new report  under Sales Report Menu.

  - Table view consists of columns like bill number,Customer name,mobile number,date of birth,date of purchase(bill date),city,preferred mode,preferred language,itemName.(all item names are separated by commas associated to bill no)

  - Export Report in Xls,Pdf,Print.

>Customer Master

  - Renamed the address to comment(comment is not mandatory)

  - Added preferred language field dropdown(not mandatory)


>Billing
  - Added Comment box under Email(in the customer info)

  - Entered comment save/update in the Customer Master comment field.
  



>Birthday Report

  -Added date filter From and ToDate(select only date and month)

>Customer Sales Report

  -Customer sales data is shown with highest purchase will be shown first and followed by others
  (Ascendending by total bill_amount)


>Customer Sales Summary

  -Customer sales summary is shown with the highest purchase will be shown first and followed by others
  (Ascendending by total bill_amount)



##Master 2.0.9
date:18/09/2018

> Billing 

  - Fixed issue in billing # CHANGES made for balance is not calculation as per currency conversion 

##Master 2.0.8
date:05/09/2018

> Customer Master
  
  - Add ID card(not mandatory,no validation) field in view and I database table .
 
  - Show ID card field in tabular view, Excel,PDF.

> Dashboard 

  - Daily chart now showing data month wise(Previously it was displaying only those day on which billings done).

> Billing 

  - Add normal discount and corporate discount(corporate discount is 15% and when corporate discount is selected discount fields are disabled) radio button before discount field in payment screen and changes in database table for maintaing flag for discount type.

  - Add ID card field on payment screen in customer details section.(no validation for ID card). 

  - Customer mobile no., name ,ID card field is manatory if corporate discount is selected.

  - Existing Billing With Currency Billing Calculate with that time currency Rate.


 > Customer Sales Report
  
  - Add Discount type filter (values in dropdown All,Normal,Corporate) .as per selected value result will be shown e.g ; if ALL selected normal and corporate customers will be shown , if normal selected normal customer will be shown, if corporate discount seleted corporate discounted customers will be shown.)
  
  - Show Mobile number and ID Card number with customer name (if corporate discount is applicable) in view table,Excel,PDF,Print.

 > Customer Sales Summary Report
  
  - Add Discount type filter (values in dropdown All,Normal,Corporate) .as per selected value result will be shown e.g ; if ALL selected normal and corporate customers will be shown , if normal selected normal customer will be shown, if corporate discount seleted corporate discounted customers will be shown.)
  
  - Show Mobile number and ID Card number with customer name (if corporate discount is applicable) in view table,Excel,PDF,Print.    

##Master 2.0.7
Date: 11/07/2018
Customer Master
- Allow Mobile number 18 digit(18 digit mobile number format add)

Billing
- Allow Mobile number 18 digit(18 digit mobile number format add)

Billing Customer History
- Allow Mobile number 18 digit(18 digit mobile number format add)


##Master 2.0.6
Date: 02/07/2018
Sales Summary Report
- Removed currency column in sales summary report and If billing  done by selecteing currency then card and cash amount is calculated in AED(Pdf,Xls,View)

Sales PACT Report
- Removed currency column in sales summary report and If billing  done by selecteing currency then card and cash amount is calculated in AED(Xls,View)

Billing 
- Fixed credit, card and cash mode bug

##Master 2.0.5
Date: 20/06/2018
Sales Summary 
- Added currency column in sales summary report

Billing 
- Fixed card and cash mode bug


##Master 2.0.4
Date: 18/06/2018

> Billing 
 
  - Added Currency Rate textbox on payment screen which is non editable.
  - Disabled Currency selection dropdown when credit paymode selected.
  - Added currency code on receipt print near Paid Amt.
  - Pay mode changes # calculate currency amt accordingly


##Master 2.0.4
Date: 14/06/2018

> Bug Fixing


##Master 2.0.3
Date: 13/06/2018

> Stock Balance Category wise report changes # REPORT SHOWING IN CORRECT STOCK SO CHANGED WHOLE REPORT WHICH IS MATCHING TO OTHER 3 STOCK REPORTS Updated 
> Customer Module # 
  -   Add  Age Group (options of 18 to 29/30 to 36/37 to 45/46 to 55/56+), Gender (options of M/F)
  - Add Age group and Gender column in table view, export, pdf.
> Updated Billing # 
 - add Email, Age Group (options of 18 to 29/30 to 36/37 to 45/46 to 55/56+) radio buttons, Gender (options of M/F), City: to be typed
  - Billing screen: Selecting salesman to be mandatory.
  - Add currency dropdown near Received on paymnet screen.
  - At time of billing, accept payment in any currency but balance to be done in AED.
  - In Billing/Payment Screen Remove Discount AED label 
  - In Payment Billing details, if we add Discount Amt then only show it in receipt else we need to not show the discount Amount label in the receipt. 


> Dashboard

  - To change the Y-axis from no of sales/return to Sales(AED) 

  - Add day (two char below date)along with date on X-axis for Daily chart only. When there is no sales that date/day will not be seen on X-axis

> Puchase Return

  - Rename Supplier bill # to Stock transfer #.

> Salesman Report

  - Add refrence number column in view,pdf,export.

> Customer Sales

  - Add refrence number column in view,pdf,export.

> Sales Profit

  - Add refrence number column in view,pdf,export.

> Cash Memo Categorywise

  -  Add refrence number column in view,pdf,export.

  - Change Category to Sub category label (table name) in view .

> currencies Master

  - Master to predefined exchange rate(according 1 currency) of any currency with option to edit in future.
  - Add Currency textbox, rate for 1 currency
  - View,Excel,PDF.

  - currency master haveing 3 fields currency name , currency code, and mount per unit ex:- 1 AED = 0.0045 Rs

> Item Master

  - Change “Serial Number” to ""Reference Number"" change label.

  - add refrence number fiels in import sheet.

  - Add refrence number column in view,pdf,export.

  - Rename Category to Subcategory and Add Category  column in  view,pdf,export.

> Opening Stock  

  - Add refrence number fiels in import sheet.

> Purchase Order

  - Add Reference Number field .

  - Add Reference Number in itemlist.

  - Add PO id in xls/pdf in Purchase order.

> Purchase

  - Rename Supplier bill # to Stock transfer #.

  - add refrence number fiels in import sheet.

  - Add Reference Number field in table.

 > Purchase Return 

  - Rename Supplier bill # to Stock transfer #.
  - Add Reference Number field .

 > ItemList Report

  - Add refrence number column in view,pdf,export.

  - Rename Category to Subcategory and Add Category  column in  view,pdf,export.

  > Stock Balance Report

  - Change “Serial Number” to ""Reference Number"" (change label column) in view,pdf,export.

  - Change Category to Sub category label (table name) in view .

  > Stock Balance Summary

  - Change “Serial Number” to ""Reference Number"" (change label column) in  view,pdf,export.

  - Change Category to Sub category label (table name) in view .

  > Stock Reorder Report

  - Change “Serial Number” to ""Reference Number"" (change label column) in  refrence number column in view,pdf,export.

  - Change Category to Sub category label (table name) in view .

  > Purchase Report

  - Rename Supplier bill # to Stock transfer #.

  - Add refrence number column in view,pdf,export.

  > Sales Summary Report

  - show Total sales with VAT & Total sales without VAT at bottom of view, pdf,excel.

  > VAT Report

  - Add refrence number column in view,pdf,export.

> sales PACT Report

  - report same as sales summary report.

  - excel report format change (consider CounterSalesintegrationPACT.xls) ask for CounterSalesintegrationPACT.xls

  
##Master 2.0.2
Date: 30/4/2018

> billing 

  - invoice bug fixed at the time of return invoice .

> quantity wise sales

  - bug fixed in quantitywise sales discount amount and total of amt calculations

> purchase and purchase return 

  - on model fixed discount % label insted of dis. rt

> vat report

  - bug fixed in vat report

  

##Master 2.0.1
Date: 27/4/2018

> Billing 

 - changes in exclusive-VAT print receipt.

 - Rate (Exclusive of VAT)  which is shown in tabular structure.

 - Net (Exclusive of VAT)   which is shown in tabular structure.
 
 - Adding new VAT amt field (below dis amt and above total).

> vat Report
 
 -VAT Report as per inclusive Vat and Exclusive vat

> Bug Fixing patch no mention in sheet 

   


##Master 2.0.0
Date: 24/4/2018


> billing 

- billing updated for 3 vat types i.e no vat , inclusive vat and exclusive vat 

- invlice updated for arabic labels 

Note: - at the time of change vat type database must be changed or refreshed

> purchase report

- purchase report as per 3 vat type's i.e no vat , inclusive vat and exclusive vat

Note: - at the time of change vat type database must be changed or refreshed

> purchase return report

- purchase return report as per 3 vat type's i.e no vat , inclusive vat and exclusive vat

Note: - at the time of change vat type database must be changed or refreshed

> Sales Summary report

- Sales Summary report as per 3 vat type's i.e no vat , inclusive vat and exclusive vat

Note: - at the time of change vat type database must be changed or refreshed

> Sales Return report

- Sales Return report as per 3 vat type's i.e no vat , inclusive vat and exclusive vat

Note: - at the time of change vat type database must be changed or refreshed

> ItemWise Sales report

- ItemWise Sales report as per 3 vat type's i.e no vat , inclusive vat and exclusive vat

Note: - at the time of change vat type database must be changed or refreshed


> Quantitywise Sales report

- Quantitywise Sales report as per 3 vat type's i.e no vat , inclusive vat and exclusive vat

Note: - at the time of change vat type database must be changed or refreshed

>Item Master    

- item master updated for 3 vat types i.e no vat , inclusive vat and exclusive vat 

Note: - at the time of change vat type database must be changed or refreshed

> Opening Stock

- opening stock for 3 vat types i.e no vat , inclusive vat and exclusive vat 

Note: - at the time of change vat type database must be changed or refreshed

> Purchase 

- Purchase updated for 3 vat types i.e no vat , inclusive vat and exclusive vat 

Note: - at the time of change vat type database must be changed or refreshed

> Purchase Return

- Purchase Return updated for 3 vat types i.e no vat , inclusive vat and exclusive vat 

Note: - at the time of change vat type database must be changed or refreshed

> Item List Report

-Item list Report as per 3 vat type's i.e no vat , inclusive vat and exclusive vat

Note: - at the time of change vat type database must be changed or refreshed

> cashier Report

-  Cashier Report as per 3 vat type's i.e no vat , inclusive vat and exclusive vat

Note: - at the time of change vat type database must be changed or refreshed

> VAT Report

- VAT Report as per inclusive Vat and Exclusive vat

Note: - at the time of change vat type database must be changed or refreshed

> Customer Sales Report

-  Customer Sales Report as per 3 vat type's i.e no vat , inclusive vat and exclusive vat

Note: - at the time of change vat type database must be changed or refreshed



##Master 1.1.9
Date: 16/4/2018


> customer master

  - performance optimization and code optimization

> opening stock
  
  - performance optimization and code optimization

> item master

  - performance optimization and code optimization

> purchase order

  - performance optimization and code optimization

> purchase 

  - performance optimization and code optimization

> purchase return

  - performance optimization and code optimization

> stock discplay

  - performance optimization and code optimization

> stock expiry

  - performance optimization and code optimization

> billing

  - performance optimization and code optimization

> customer ledger

  - performance optimization and code optimization

> supplier ledger

  - performance optimization and code optimization

> credit customer receipt

  - performance optimization and code optimization

> financial

  - performance optimization and code optimization

> all reports

  - code optimization

> all sales reports

  -  code optimization

  > Add vat Setting options  in options page

##Master 1.1.8
Date: 5/4/2018

> billing 

  - billing optimized and some actions are changed , get data as per need 

> masters (Customer master, opening stock and item master)

  - customer master updated as per item master table actions and search action 

> stock 

  - purchase order, purchase, purchase return, display stock , damage/expiry stock updated as per optimization needd
  


##Master 1.1.7
Date: 26/3/2018


> fixed bug in master bugsheet from bug no 105 to 110

##Master 1.1.6
Date: 1/3/2018

> opening stock

  - bug fixed in opening stock global search as per item master 

##Master 1.1.5
Date: 28/2/2018

> date globaly changed as per options 

##Master 1.1.4
Date: 22/2/2018

> bug fixed in master bug sheet 

##Master 1.1.3
Date: 21/2/2018

> purchase 
  
  - at the time of edit po_no get selected now and disabled if edit purchase

> billing 

  - in billing invoice when bill with card and cash invoice format issue fixed it

##Master 1.1.2
Date: 19/2/2018

> bug fixed#

  - bug fixed in master sheet bug no. 52 to 88 , except bug no 82

##Master 1.1.1
Date: 15/2/2018

>option

   -code review for performance

   -bug Fixing purchase,role,user and options

> reports All

    - bug fixing with all bug no 5 to 30 

    - date format changed m/d/Y except birthday and anniversary



##Master 1.1.0
Date: 13/2/2018

> billing 

  - bug fixed in billing able to return item twice after bill update 

  - If amount is 0 in received amount then it should not allow to save the bill and should display some toaster alert.

  - Displaying Incorrect toaster alert while incrementing the Qty

  - Incorrect amount in words after decimal i.e,for 10.11 Displaying eleven one after decimal. 

  - Removing complete dis. amt is showing NaN on billing screen.
  
  - Displaying negative AED on screen


##Master 1.0.9
Date: 12/2/2018

>option

 - Bug Fixing

##Master 1.0.8
Date: 9/2/2018

>option

  - send setting option mail in option

  - Vat Report apply FixBill for bill_no

  - Bug Fixing

##Master 1.0.7
Date: 7/2/2018

>option

  - Add barcode_type,barcode_rate_prefix_number and barcode_weight_prefix_number,barcode_Decimal_Price_Limit on Setting Tab  
  
  - barcode_type option test changes ItemMaster,ClubBarcode

  - barcode_rate_prefix_number and barcode_weight_prefix_number,barcode_Decimal_Price_Limit test changes billing screen

  - Fixed Issue Bill Padding throughout


##Master 1.0.6
Date: 2/2/2018

> option

  - Added Setting Tab for customization (currancy,TextCurrancy,CurrancyTextAfterDecimal,Amount Decimal,Quantity Decimal,Bill Padding,billPrefixWord,Email)

  -Setting Tab options test changes Master,stock,billing,Finacial,Legder,Dashboard,opening Cash,Reports,Sales Report,Loyalty


##Master 1.0.5
Date: 25/1/2018

> offline icons updated

##Master 1.0.4
Date: 5/1/2018

> billing 

	- vat with discount and subtotal with discount show in summary and side bar

> sales return 

	- subtotal and vat is discounted values

> cashier Report

    - subtotal and vat is discounted values

> Sales Summary Report
 
     - subtotal and vat is discounted values

> Vat Report

     - sales subtotal and vat is discounted values

##Master 1.0.3
Date: 3/1/2018

> Import Functionality 
	- Import changes for Blanck category check and
> Billing 
	- Billing Receipt VAT Summary change for deducting discount

##Master 1.0.2
Date: 21/12/2017

> Report

	- New VAT Report Added (Please check(View,Print Exports))


##Master 1.0.1
Date: 18/112/2017


> purchase stock

	- added cp vat in purchase 

	- print updated and two more buttons added 'save & print'  and 'update & print'

> purchase report

	- cp vat added in report

##Master 1.0.0
Date: 31/10/2017

> Billing 

- Check Billing for is SP is showing with vat in Item List

- Check on payment Screen and Top right on billing screeen vat is shown

- Check print receipt formate for vat(u have that on whatsapp)

- Do New, Existing,Return,Updated Bill and check vat calculations shown properly

> Report (Vat column added)

- Item List(Shown vat %)

- Cashier Report(Shown vat amount)

- Customer Sales(Shown vat %)

- QuantityWise Sales(Shown vat %)

- Sales Summary(Shown vat amount)

- Sales Return(Shown vat amount)

- Item Wise Sale(Shown vat %)

> Master

- Item master => VAT fiels Added 

- Check Import functionality in item master

- Check Import functionality in Opening Stock

- Check Import functionality in Purchase


