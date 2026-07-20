<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Customer;
use App\Supplier;
use App\Item;
use App\Salesman;
use App\User;
use DB;
use Common;
use Carbon\Carbon;
class DashboardController extends Controller
{

    private $amt_fix;
    private $qty_fix;
    private $bill_pad;
    private $curr;

    public function __construct(){

        $this->amt_fix = common::dFixIt();
        $this->qty_fix =common::dQty();
        $this->bill_pad = common::dBill();
        $this->curr = common::curr();
    }
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function index(Request $request)
     { 
        try{      

            if (\Auth::check()){
                $userId = \Auth::id();
            }else{
                $userId = 1;
            }

            if($userId == 1){
                $user = User::count();
            }else{
                $user = User::where('id', '<>' ,1)->count(); 
            } 
            $customer = Customer::count();
            $supplier = Supplier::count();
            $item = Item::count();
            $salesman = Salesman::count();
            
            $intLowStockCount = 0;

            $arrLowStockCount = Item::select(db::raw("count(*) as lowStockCount"))->where("min", "<>", "0")->where("stock", "<=", "min")->get();
            if(count($arrLowStockCount)>0){
                $intLowStockCount = $arrLowStockCount[0]->lowStockCount;
            }


            if($request->ajax()){
                return $arrSalesHourly = $this->getSalesHourly();
            }else{
                return View("pages.dashboard.dashboard",[
                   "customer" => $customer ,
                   "supplier" => $supplier ,
                   "item" => $item ,
                   "salesman" => $salesman ,
                   "lowStockCount" =>$intLowStockCount,
                   "user" => $user ]);
            }
        }
        catch(Exception $e){
            Log::error("DashboardController@index ".$e);
            return View("errors.exception");
        }
    }

    function getSalesHourly(){
     try{
        $intCurrentTransactions = 0;
        $intCashTransactions = 0;
        $intCardTransactions = 0;
        $intCashNCardTransactions = 0;
        $intCreditTransactions = 0;
        $intNoonCreditTransactions = 0;
        $currentUserRole = \Auth::user()->roleId;
        $hourlySalesCount=[];
        $hourlyReturnCount=[];

        $hourlySalesAmount=[];
        $hourlyReturnAmount=[];

        $currentHour = date('H');
        $intCurrentSale =0;
        $intCurrentReturn =0;

        $hourlyData = DB::select("select atHour, sum(saleAmount) as salesAmount, sum(sale) as sales   from(
            select hour(created_at) as atHour, b.id, b.bil_amt as saleAmount, count(bi.item_id) as sale
            from billings as b
            inner join billing_items bi on b.id = bi.billing_id
            where 
            date(created_at) = CURRENT_DATE
            group by b.id
            ) as d

            group by atHour");

        $hourlyReturnData = DB::select("select atHour, sum(saleAmount) as returnAmount, sum(sale) as returns   from(
            select hour(created_at) as atHour, b.id, b.amount as saleAmount, count(bi.item_id) as sale
            from bill_returns as b
            inner join bill_return_items bi on b.id = bi.return_id
            where 
            date(created_at) = CURRENT_DATE
            group by b.id
            ) as d

            group by atHour");
        

        for($h=0; $h < 24; $h++){
            $flg=0; $returnFlg=0;
            for($i=0; $i < count($hourlyData); $i++){
                if($hourlyData[$i]->atHour == $h){
                    $hourlySalesCount[$h] = $hourlyData[$i]->sales;
                    $hourlySalesAmount[$h] =$hourlyData[$i]->salesAmount;
                    $flg++;
                }

                if($hourlyData[$i]->atHour == $currentHour){
                    $intCurrentSale = $hourlyData[$i]->sales;
                }
            }
            if($flg==0){
                $hourlySalesCount[$h] = 0;
                $hourlySalesAmount[$h] = 0;
            }

            for($i=0; $i < count($hourlyReturnData); $i++){
                if($hourlyReturnData[$i]->atHour == $h){
                    $hourlyReturnCount[$h] = $hourlyReturnData[$i]->returns;
                    $hourlyReturnAmount[$h] =$hourlyReturnData[$i]->returnAmount;
                    $returnFlg++;
                }

                if($hourlyReturnData[$i]->atHour == $currentHour){
                    $intCurrentReturn = $hourlyReturnData[$i]->returns;
                }
            }

            if($returnFlg==0){
                $hourlyReturnCount[$h] = 0;
                $hourlyReturnAmount[$h] = 0;
            }
        }

        $arrCurrentTransactions = DB::select("select hour(created_at) as atHour, count(b.id) as transactions
            from billings as b
            where 
            hour(created_at) = hour(CURRENT_TIME) and date(created_at) = CURRENT_DATE
            group by hour(created_at)");

        if(count($arrCurrentTransactions)!=0){
            $intCurrentTransactions = $arrCurrentTransactions[0]->transactions;
        }

        $arrSalesBreakdown = DB::select("select hour(created_at) as atHour, count(b.id) as transactions, payment_mode
            from billings as b
            where 
            hour(created_at) = hour(CURRENT_TIME) and date(created_at) = CURRENT_DATE
            group by hour(created_at), payment_mode
            order by payment_mode");


        if(count($arrSalesBreakdown) !=0){
            foreach ($arrSalesBreakdown as $isb) {
                $intCashTransactions = ($isb->payment_mode == 1) ? $isb->transactions : $intCashTransactions;
                $intCashNCardTransactions = ($isb->payment_mode == 2) ? $isb->transactions : $intCashNCardTransactions;
                $intCreditTransactions = ($isb->payment_mode == 3) ? $isb->transactions : $intCreditTransactions;   
                $intCardTransactions = ($isb->payment_mode == 4) ? $isb->transactions : $intCardTransactions;
                $intNoonCreditTransactions = ($isb->payment_mode == 5) ? $isb->transactions : $intNoonCreditTransactions;
            }
        }

        $arrTopSalingItems = DB::select("SELECT i.*, count(bi.item_id) as total_sales FROM `items` as i
            inner JOIN billing_items bi on bi.item_id = i.id
            inner join billings b on b.id=bi.billing_id
            where hour(b.created_at) = hour(CURRENT_TIME)
            and date(b.created_at) = CURRENT_DATE
            group by bi.item_id
            order by total_sales DESC
            limit 5");

        $arrCashierSales = DB::select("SELECT u.email, CONCAT('".$this->curr." ',round(COALESCE(sum(b.bil_amt),0), $this->amt_fix)) as cashier_sales, sum(b.bil_amt) as co
            from users u 
            left outer join billings b on b.user_id = u.id and hour(b.created_at) = hour(CURRENT_TIME) and  date(b.created_at) = CURRENT_DATE
            where (case when $currentUserRole = 1 then 1=1 else u.roleId <> 1 end)
            group by b.user_id, u.email
            order by co DESC
            ");

        $arrSalesmanPer = DB::select("SELECT s.name, CONCAT('".$this->curr." ',round(COALESCE(sum(b.bil_amt),0),$this->amt_fix)) as salesman_per, sum(b.bil_amt) as co
            from salesman s 
            left outer join billings b on b.salesman_id = s.id and hour(b.created_at) = hour(CURRENT_TIME) and  date(b.created_at) = CURRENT_DATE
            group by b.salesman_id, s.name
            order by co DESC");



        $floatTotalSales =  DB::select("SELECT round( sum(b.bil_amt),$this->amt_fix) as totalSales
            from billings as b
            where 
            hour(b.created_at) = hour(CURRENT_TIME) and date(b.created_at) = date(CURRENT_TIME)");

        $floatTotalReturns =  DB::select("SELECT round( sum(b.amount),$this->amt_fix) as totalReturns
            from bill_returns as b
            where 
            hour(b.created_at) = hour(CURRENT_TIME) and date(b.created_at) = date(CURRENT_TIME)");

        $floatCurrentSaleAmount = (isset($floatTotalSales[0]) && $floatTotalSales[0]->totalSales!=null) ? $floatTotalSales[0]->totalSales : 0;
        $floatCurrentReturnAmount = (isset($floatTotalReturns[0]) && $floatTotalReturns[0]->totalReturns != null) ? $floatTotalReturns[0]->totalReturns : 0;

        return Response([
            "arrSalesHourlyCount" => $hourlySalesCount,
            "arrReturnsHourlyCount" => $hourlyReturnCount,
            "arrSalesHourlyAmount" => $hourlySalesAmount,
            "arrReturnsHourlyAmount" => $hourlyReturnAmount,

            'intCurrentSale' => $intCurrentSale,
            'intCurrentReturn' => $intCurrentReturn,
            'floatCurrentSaleAmount' => $floatCurrentSaleAmount,
            'floatCurrentReturnAmount' => $floatCurrentReturnAmount,

            'intCurrentTransactions' => $intCurrentTransactions,
            'intCashTransactions' => $intCashTransactions,
            'intCashNCardTransactions' => $intCashNCardTransactions,
            'intCreditTransactions' => $intCreditTransactions,
            'intCardTransactions' => $intCardTransactions,
            'intNoonCreditTransactions' => $intNoonCreditTransactions,
            'arrTopSalingItems' => $arrTopSalingItems,
            'arrCashierSales' => $arrCashierSales,
            'arrSalesmanPer' => $arrSalesmanPer,
        ]);
    }
    catch(Exception $e){
        Log::error("DashboardController@getSalesHourly ".$e);
        return View("errors.exception");
    }
}


function getSalesDaily(){
    try{
        //return date('m');
        //return date('Y');
        //cal_days_in_month(CAL_GREGORIAN, 5, 2013);
        $intCashTransactions = 0;
        $intCardTransactions = 0;
        $intCashNCardTransactions = 0;
        $intCreditTransactions = 0;
        $intNoonCreditTransactions = 0;
        $currentUserRole = \Auth::user()->roleId;

        $dailySalesCount=array();
        $dailyReturnsCount = array();

        $dailySalesAmount=array();
        $dailyReturnAmount=array();
        $arrXAxis =array();

        $currentDay = date('d');
        $intCurrentSale =0;
        $intCurrentReturn= 0;

        $now = Carbon::now();
        $month = $now->month;
        $year  = $now->year;

        $countOfDaysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $dailyData = DB::select("select atDay, sum(items) as sales, sum(amount) as sales_amount, dayName
            from (
            select dayofmonth(created_at) as atDay, b.id ,count(bi.billing_id) as items, b.bil_amt as amount, DATE_FORMAT(created_at, '%a') as dayName
            from billings as b
            inner join billing_items bi on b.id = bi.billing_id
            where 
            month(created_at) = month(CURRENT_DATE) and year(created_at)=year(CURRENT_DATE)
            group by b.id
            ) d
            group by atDay");

        $dailyReturnData = DB::select("select atDay, sum(items) as returns, sum(amount) as return_amount, dayName
            from (
            select dayofmonth(created_at) as atDay, b.id ,count(bi.return_id) as items, b.amount as amount, DATE_FORMAT(b.created_at, '%a') as dayName
            from bill_returns as b
            inner join bill_return_items bi on b.id = bi.return_id
            where 
            month(created_at) = month(CURRENT_DATE) and year(created_at)=year(CURRENT_DATE)
            group by b.id
            ) d
            group by atDay");

        for($h=1; $h <= $countOfDaysInMonth; $h++){
            $flg=0; $returnFlg=0;
                        
            $data = Carbon::parse($h.'-'.$month.'-'.$year);        
            $data->format('l');
            array_push($arrXAxis, $data->day.'/'.substr( $data->format('l'), 0, 2));            
            
            for($i=0; $i < count($dailyData); $i++){
                if($dailyData[$i]->atDay == $h){
                    array_push($dailySalesCount, $dailyData[$i]->sales);
                    array_push($dailySalesAmount, $dailyData[$i]->sales_amount);

                    $flg++;
                }
                if($currentDay == $dailyData[$i]->atDay){
                    $intCurrentSale = $dailyData[$i]->sales;
                }
            }
            if($flg==0){
                array_push($dailySalesCount, 0);
                array_push($dailySalesAmount, 0);
            }

            for($i=0; $i < count($dailyReturnData); $i++){
                if($dailyReturnData[$i]->atDay == $h){
                    array_push($dailyReturnsCount, $dailyReturnData[$i]->returns);
                    array_push($dailyReturnAmount, $dailyReturnData[$i]->return_amount);

                    $returnFlg++;
                }

                if($dailyReturnData[$i]->atDay == $currentDay){
                    $intCurrentReturn = $dailyReturnData[$i]->returns;
                }
            }
            if($returnFlg==0){
                array_push($dailyReturnsCount, 0);
                array_push($dailyReturnAmount, 0);
            }
        }

        /*$arrdateData = DB::select("select atDay, dayName, created_at
            from ( 
            select  dayofmonth(created_at) as atDay,DATE_FORMAT(created_at, '%a') as dayName, created_at  from billings 
            where 
            month(created_at) = month(CURRENT_DATE) and year(created_at) and year(CURRENT_DATE) 

            UNION 

            select  dayofmonth(created_at) as atDay,DATE_FORMAT(created_at, '%a') as dayName, created_at from bill_returns 
            where 
            month(created_at) = month(CURRENT_DATE) and year(created_at) and year(CURRENT_DATE) 

            ) d
            group by atDay");

        $arrXAxis=array();
        $contData = count($arrdateData);
        if($contData>0){
        if( $contData < 7){
            $arrtemp = array();
            for($h=0; $h <= 6; $h++){
                
                if( $contData < ($h+1)){
                    $data = Carbon::parse($arrdateData[$h-1]->created_at); 
                    $objDate = $data->addDay();
                    array_push($arrdateData, (object)array('atDay'=> $objDate->day , 'dayName'=>$objDate->format('l'), 'created_at'=> $data ));
                }
            }
        }
 }
        for($h=0; $h < count($arrdateData); $h++){

            array_push($arrXAxis, $arrdateData[$h]->atDay.'/'.substr($arrdateData[$h]->dayName, 0, 2));
            $flg=0; $returnFlg=0;
            for($i=0; $i < count($dailyData); $i++){
                if($dailyData[$i]->atDay == $arrdateData[$h]->atDay){
                    array_push($dailySalesCount, $dailyData[$i]->sales);
                    array_push($dailySalesAmount, $dailyData[$i]->sales_amount);

                    $flg++;
                }
                if($currentDay == $dailyData[$i]->atDay){
                    $intCurrentSale = $dailyData[$i]->sales;
                }
            }
            if($flg==0){
                array_push($dailySalesCount, 0);
                array_push($dailySalesAmount, 0);
            }

            for($i=0; $i < count($dailyReturnData); $i++){
                if($dailyReturnData[$i]->atDay == $arrdateData[$h]->atDay){
                    array_push($dailyReturnsCount, $dailyReturnData[$i]->returns);
                    array_push($dailyReturnAmount, $dailyReturnData[$i]->return_amount);

                    $returnFlg++;
                }

                if($dailyReturnData[$i]->atDay == $currentDay){
                    $intCurrentReturn = $dailyReturnData[$i]->returns;
                }
            }
            if($returnFlg==0){
                array_push($dailyReturnsCount, 0);
                array_push($dailyReturnAmount, 0);
            }
        }*/


        $intCurrentDayTransactions = 0;
        $arrCurrentDayTransactions = DB::select("select date(created_at) as atDay, count(b.id) as transactions
            from billings as b
            where 
            date(created_at) = date(CURRENT_DATE)
            group by date(created_at)");

        if(count($arrCurrentDayTransactions)!=0){
            $intCurrentDayTransactions= $arrCurrentDayTransactions[0]->transactions;
        }

        $arrSalesBreakdown = DB::select("select date(created_at) as atDay, count(b.id) as transactions, payment_mode
            from billings as b
            where 
            date(created_at) = date(CURRENT_DATE)
            group by date(created_at), payment_mode
            order by payment_mode");

        if(count($arrSalesBreakdown) !=0){
            foreach ($arrSalesBreakdown as $isb) {
                $intCashTransactions = ($isb->payment_mode == 1) ? $isb->transactions : $intCashTransactions;
                $intCashNCardTransactions = ($isb->payment_mode == 2) ? $isb->transactions : $intCashNCardTransactions;
                $intCreditTransactions = ($isb->payment_mode == 3) ? $isb->transactions : $intCreditTransactions;   
                $intCardTransactions = ($isb->payment_mode == 4) ? $isb->transactions : $intCardTransactions;
                $intNoonCreditTransactions = ($isb->payment_mode == 5) ? $isb->transactions : $intNoonCreditTransactions;
            }
        }



        $arrSalesBreakdown = DB::select("select date(created_at) as atDay, count(b.id) as transactions, payment_mode
            from billings as b
            where 
            date(created_at) = date(CURRENT_DATE)
            group by date(created_at), payment_mode 
            order by payment_mode");

        if(count($arrSalesBreakdown) !=0){
            foreach ($arrSalesBreakdown as $isb) {
                $intCashTransactions = ($isb->payment_mode == 1) ? $isb->transactions : $intCashTransactions;
                $intCashNCardTransactions = ($isb->payment_mode == 2) ? $isb->transactions : $intCashNCardTransactions;
                $intCreditTransactions = ($isb->payment_mode == 3) ? $isb->transactions : $intCreditTransactions;   
                $intCardTransactions = ($isb->payment_mode == 4) ? $isb->transactions : $intCardTransactions;
                $intNoonCreditTransactions = ($isb->payment_mode == 5) ? $isb->transactions : $intNoonCreditTransactions;
            }
        }

        $arrTopSalingItems = DB::select("SELECT i.*, count(bi.item_id) as total_sales FROM `items` as i
            inner JOIN billing_items bi on bi.item_id = i.id
            inner join billings b on b.id=bi.billing_id
            where  date(b.created_at) = date(CURRENT_DATE)
            group by bi.item_id
            order by total_sales DESC
            limit 5");

        $arrCashierSales = DB::select("SELECT u.email, CONCAT('".$this->curr." ',round(COALESCE(sum(b.bil_amt),0),$this->amt_fix)) as cashier_sales, sum(b.bil_amt) as co
            from users u 
            left outer join billings b on b.user_id = u.id and date(b.created_at) = date(CURRENT_DATE)
            where (case when $currentUserRole = 1 then 1=1 else u.roleId <> 1 end)
            group by b.user_id, u.email
            order by co DESC
            ");

        $arrSalesmanPer = DB::select("SELECT s.name, CONCAT('".$this->curr." ',round(COALESCE(sum(b.bil_amt),0),$this->amt_fix)) as salesman_per, sum(b.bil_amt) as co
            from salesman s 
            left outer join billings b on b.salesman_id = s.id and date(b.created_at) = date(CURRENT_DATE)
            group by b.salesman_id, s.name
            order by co DESC");



        $floatTotalSales =  DB::select("SELECT round(sum(b.bil_amt),$this->amt_fix) as totalSales
            from billings as b
            where 
            date(b.created_at) = date(CURRENT_DATE)");

        $floatTotalReturns =  DB::select("SELECT round(sum(b.amount),$this->amt_fix) as totalReturns
            from bill_returns as b
            where 
            date(b.created_at) = date(CURRENT_DATE)");

        $floatCurrentSaleAmount = (isset($floatTotalSales[0]) && $floatTotalSales[0]->totalSales!=null) ? $floatTotalSales[0]->totalSales : 0;
        $floatCurrentReturnAmount = (isset($floatTotalReturns[0]) && $floatTotalReturns[0]->totalReturns != null) ? $floatTotalReturns[0]->totalReturns : 0;

        return Response([
            "arrSalesDailyCount" => $dailySalesCount,
            "arrReturnsDailyCount" => $dailyReturnsCount,
            "arrSalesDailyAmount" => $dailySalesAmount,
            "arrReturnsDailyAmount" => $dailyReturnAmount,
            "arrXAxis"  => $arrXAxis,

            
            'intCurrentSale' => $intCurrentSale,
            'intCurrentReturn' => $intCurrentReturn,
            'floatCurrentSaleAmount' => $floatCurrentSaleAmount,
            'floatCurrentReturnAmount' => $floatCurrentReturnAmount,


            'intCurrentTransactions' => $intCurrentDayTransactions,
            'intCashTransactions' => $intCashTransactions,
            'intCashNCardTransactions' => $intCashNCardTransactions,
            'intCreditTransactions' => $intCreditTransactions,
            'intCardTransactions' => $intCardTransactions,
            'intNoonCreditTransactions' => $intNoonCreditTransactions,
            'arrTopSalingItems' => $arrTopSalingItems,
            'arrCashierSales' => $arrCashierSales,
            'arrSalesmanPer' => $arrSalesmanPer,
        ]);
    }
    catch(Exception $e){
        Log::error("DashboardController@getSalesDaily ".$e);
        return View("errors.exception");
    }
}


function getSalesMonthly(){
    try{
       $intCashTransactions = 0;
       $intCardTransactions = 0;
       $intCashNCardTransactions = 0;
       $intCreditTransactions = 0;
       $intNoonCreditTransactions = 0;
       $currentUserRole = \Auth::user()->roleId;

       $monthlyData = DB::select("SELECT atMonth, SUM(sale) sales, SUM(amount) sales_amount FROM
          (
          SELECT MONTH(created_at) AS atMonth, COUNT(bi.billing_id) AS sale, b.id, b.bil_amt AS amount
          FROM billings AS b INNER JOIN billing_items bi ON b.id = bi.billing_id
          WHERE YEAR(created_at) = YEAR(CURRENT_DATE)
          GROUP BY
          b.id ) AS d
          GROUP BY atMonth");

       $monthlyReturnData = DB::select("SELECT atMonth, SUM(sale) returns, SUM(amount) return_amount FROM
          (
          SELECT MONTH(created_at) AS atMonth, COUNT(bi.return_id) AS sale, b.id, b.amount AS amount
          FROM bill_returns AS b INNER JOIN bill_return_items bi ON b.id = bi.return_id
          WHERE YEAR(created_at) = YEAR(CURRENT_DATE)
          GROUP BY
          b.id ) AS d
          GROUP BY atMonth");

       $intCurrentMonthTransactions = 0;
       $arrCurrentMonthTransactions = DB::select("select month(created_at) as atMonth, count(b.id) as transactions
        from billings as b
        where 
        month(created_at) = month(CURRENT_DATE)
        and  year(created_at) = year(CURRENT_DATE)
        group by month(created_at)");

       if(count($arrCurrentMonthTransactions)!=0){
        $intCurrentMonthTransactions= $arrCurrentMonthTransactions[0]->transactions;
    }

    $arrSalesBreakdown = DB::select("select month(created_at) as atMonth, count(b.id) as transactions, payment_mode
        from billings as b
        where 
        month(created_at) = month(CURRENT_DATE) and  year(created_at) = year(CURRENT_DATE)
        group by month(created_at), payment_mode
        order by payment_mode");

    if(count($arrSalesBreakdown) !=0){
        foreach ($arrSalesBreakdown as $isb) {
            $intCashTransactions = ($isb->payment_mode == 1) ? $isb->transactions : $intCashTransactions;
            $intCashNCardTransactions = ($isb->payment_mode == 2) ? $isb->transactions : $intCashNCardTransactions;
            $intCreditTransactions = ($isb->payment_mode == 3) ? $isb->transactions : $intCreditTransactions;   
            $intCardTransactions = ($isb->payment_mode == 4) ? $isb->transactions : $intCardTransactions;
            $intNoonCreditTransactions = ($isb->payment_mode == 5) ? $isb->transactions : $intNoonCreditTransactions;
        }
    }

    $arrTopSalingItems = DB::select("SELECT i.*, count(bi.item_id) as total_sales FROM `items` as i
        inner JOIN billing_items bi on bi.item_id = i.id
        inner join billings b on b.id=bi.billing_id
        where month(b.created_at) = month(CURRENT_DATE)
        and  year(b.created_at) = year(CURRENT_DATE)
        group by bi.item_id
        order by total_sales DESC
        limit 5");

    $arrCashierSales = DB::select("SELECT u.email, CONCAT('".$this->curr." ',round(COALESCE(sum(b.bil_amt),0),$this->amt_fix)) as cashier_sales, sum(b.bil_amt) as co
        from users u 
        left outer join billings b on b.user_id = u.id and month(b.created_at) = month(CURRENT_DATE) and  year(b.created_at) = year(CURRENT_DATE)
        where (case when $currentUserRole = 1 then 1=1 else u.roleId <> 1 end)
        group by b.user_id, u.email
        order by co DESC
        ");

    $arrSalesmanPer = DB::select("SELECT s.name, CONCAT('".$this->curr." ',round(COALESCE(sum(b.bil_amt),0),$this->amt_fix)) as salesman_per, sum(b.bil_amt) as co
        from salesman s 
        left outer join billings b on b.salesman_id = s.id and month(b.created_at) = month(CURRENT_DATE) and  year(b.created_at) = year(CURRENT_DATE)
        group by b.salesman_id, s.name
        order by co DESC");




    $monthlySalesCount=array();
    $monthlyReturnsCount=array();

    $monthlySalesAmount=array();
    $monthlyReturnsAmount=array();

    $currentMonth = date('m');

    $intCurrentSale =0;
    $intCurrentReturn =0;

    $floatCurrentSaleAmount =0;
    $floatCurrentReturnAmount =0;

    for($h=1; $h < 13; $h++){
        $flg=0; $returnFlg=0;

        for($i=0; $i < count($monthlyData); $i++){
            if($monthlyData[$i]->atMonth == $h){
                array_push($monthlySalesCount, $monthlyData[$i]->sales);
                array_push($monthlySalesAmount, $monthlyData[$i]->sales_amount);

                $flg++;
            }
            if($currentMonth == $monthlyData[$i]->atMonth){
                $intCurrentSale = $monthlyData[$i]->sales;
            }
        }
        if($flg==0){
            array_push($monthlySalesCount, 0);
            array_push($monthlySalesAmount, 0);
        }

        for($i=0; $i < count($monthlyReturnData); $i++){
            if($monthlyReturnData[$i]->atMonth == $h){
                array_push($monthlyReturnsCount, $monthlyReturnData[$i]->returns);
                array_push($monthlyReturnsAmount, $monthlyReturnData[$i]->return_amount);

                $returnFlg++;
            }

            if($monthlyReturnData[$i]->atMonth == $currentMonth){
                $intCurrentReturn = $monthlyReturnData[$i]->returns;
            }
        }
        if($returnFlg==0){
            array_push($monthlyReturnsCount, 0);
            array_push($monthlyReturnsAmount, 0);
        }
    }

    $floatTotalSales =  DB::select("SELECT round(sum(b.bil_amt),$this->amt_fix) as totalSales
        from billings as b
        where 
        month(b.created_at) = month(CURRENT_DATE)
        and  year(b.created_at) = year(CURRENT_DATE)
        ");

    $floatTotalReturns =  DB::select("SELECT round(sum(b.amount),$this->amt_fix) as totalReturns
        from bill_returns as b
        where 
        month(b.created_at) = month(CURRENT_DATE)
        and  year(b.created_at) = year(CURRENT_DATE)
        ");

    $floatCurrentSaleAmount = (isset($floatTotalSales[0]) && $floatTotalSales[0]->totalSales!=null) ? $floatTotalSales[0]->totalSales : 0;
    $floatCurrentReturnAmount = (isset($floatTotalReturns[0]) && $floatTotalReturns[0]->totalReturns != null) ? $floatTotalReturns[0]->totalReturns : 0;

    return Response([
        "arrSalesMonthlyCount" => $monthlySalesCount,
        "arrReturnsMonthlyCount" => $monthlyReturnsCount,
        "arrSalesMonthlyAmount" => $monthlySalesAmount,
        "arrReturnsMonthlyAmount" => $monthlyReturnsAmount,

        "intCurrentSale" => $intCurrentSale,
        'intCurrentReturn' => $intCurrentReturn,
        'floatCurrentSaleAmount' => $floatCurrentSaleAmount,
        'floatCurrentReturnAmount' => $floatCurrentReturnAmount,

        'intCurrentTransactions' => $intCurrentMonthTransactions,
        'intCashTransactions' => $intCashTransactions,
        'intCashNCardTransactions' => $intCashNCardTransactions,
        'intCreditTransactions' => $intCreditTransactions,
        'intCardTransactions' => $intCardTransactions,
        'intNoonCreditTransactions' => $intNoonCreditTransactions,
        'arrTopSalingItems' => $arrTopSalingItems,
        'arrCashierSales' => $arrCashierSales,
        'arrSalesmanPer' => $arrSalesmanPer,
    ]);
}
catch(Exception $e){
    Log::error("DashboardController@getSalesMonthly ".$e);
    return View("errors.exception");
}
}


function getSalesYearly(){
    try{

     $intCashTransactions = 0;
     $intCardTransactions = 0;
     $intCashNCardTransactions = 0;
     $intCreditTransactions = 0;
     $intNoonCreditTransactions = 0;
     $yearlyData = DB::select("select atYear, sum(sale) as sales, sum(amount) as sales_amount
        from(
        select year(created_at) as atYear, b.id, count(bi.billing_id) as sale, b.bil_amt as amount
        from billings as b
        inner join billing_items bi on b.id = bi.billing_id
        where 
        year(created_at) >= year(CURRENT_DATE)-5
        group by b.id
        ) as b
        group by atYear");


     $yearlyReturnData = DB::select("select atYear, sum(sale) as returns, sum(amount) as return_amount
        from(
        select year(created_at) as atYear, b.id, count(bi.return_id) as sale, b.amount as amount
        from bill_returns as b
        inner join bill_return_items bi on b.id = bi.return_id
        where 
        year(created_at) >= year(CURRENT_DATE)-5
        group by b.id
        ) as b
        group by atYear");

     $intCurrentYearTransactions = 0;
     $arrCurrentYearTransactions = DB::select("select year(created_at) as atYear, count(b.id) as transactions
        from billings as b
        where 
        year(created_at) = year(CURRENT_DATE)
        group by year(created_at)");

     if(count($arrCurrentYearTransactions)!=0){
        $intCurrentYearTransactions = $arrCurrentYearTransactions[0]->transactions;
    }


    $arrSalesBreakdown = DB::select("select year(created_at) as atYear, count(b.id) as transactions, payment_mode
        from billings as b
        where 
        year(created_at) = year(CURRENT_DATE)
        group by year(created_at), payment_mode
        order by payment_mode");

    if(count($arrSalesBreakdown) !=0){
        foreach ($arrSalesBreakdown as $isb) {
            $intCashTransactions = ($isb->payment_mode == 1) ? $isb->transactions : $intCashTransactions;
            $intCashNCardTransactions = ($isb->payment_mode == 2) ? $isb->transactions : $intCashNCardTransactions;
            $intCreditTransactions = ($isb->payment_mode == 3) ? $isb->transactions : $intCreditTransactions;   
            $intCardTransactions = ($isb->payment_mode == 4) ? $isb->transactions : $intCardTransactions;
            $intNoonCreditTransactions = ($isb->payment_mode == 5) ? $isb->transactions : $intNoonCreditTransactions;
        }
    }

    $arrTopSalingItems = DB::select("SELECT i.*, count(bi.item_id) as total_sales FROM `items` as i
        inner JOIN billing_items bi on bi.item_id = i.id
        inner join billings b on b.id=bi.billing_id
        where  year(b.created_at) = year(CURRENT_DATE)
        group by bi.item_id
        order by total_sales DESC
        limit 5");

    $currentUserRole = \Auth::user()->roleId;

    $arrCashierSales = DB::select("SELECT u.email, CONCAT('".$this->curr." ',round(COALESCE(sum(b.bil_amt),0),$this->amt_fix)) as cashier_sales, sum(b.bil_amt) as co
        from users u 
        left outer join billings b on b.user_id = u.id and  year(b.created_at) = year(CURRENT_DATE)
        where (case when $currentUserRole = 1 then 1=1 else u.roleId <> 1 end)
        group by b.user_id, u.email
        order by co DESC
        ");

    $arrSalesmanPer = DB::select("SELECT s.name, CONCAT('".$this->curr." ',round(COALESCE(sum(b.bil_amt),0),$this->amt_fix)) as salesman_per, sum(b.bil_amt) as co
        from salesman s 
        left outer join billings b on b.salesman_id = s.id and year(b.created_at) = year(CURRENT_DATE)
        group by b.salesman_id, s.name
        order by co DESC");




    $yearlySalesCount=array();
    $yearlyReturnsCount=array();
    $yearlySalesAmount=array();
    $yearlyReturnsAmount=array();

    $years = array();

    $currentYear = (int)date("Y");
    $intCurrentSale =0;
    $floatCurrentSaleAmount =0;

    for( $h= ($currentYear-5); $h <= $currentYear; $h++){ 
        array_push($years, $h);

        $flg=0;

        for($i=0; $i < count($yearlyData); $i++){
            if($yearlyData[$i]->atYear == $h){
                array_push($yearlySalesCount, $yearlyData[$i]->sales);
                array_push($yearlySalesAmount, $yearlyData[$i]->sales_amount);

                $flg++;
            }
            if($currentYear == $yearlyData[$i]->atYear){
                $intCurrentSale = $yearlyData[$i]->sales;
            }
        }
        if($flg==0){
            array_push($yearlySalesCount, 0);
            array_push($yearlySalesAmount, 0);
        }


        $returnFlg=0;
        $intCurrentReturn =0;
        $floatCurrentReturnAmount =0;

        for($i=0; $i < count($yearlyReturnData); $i++){
            if($yearlyReturnData[$i]->atYear == $h){
                array_push($yearlyReturnsCount, $yearlyReturnData[$i]->returns);
                array_push($yearlyReturnsAmount, $yearlyReturnData[$i]->return_amount);

                $returnFlg++;
            }

            if($yearlyReturnData[$i]->atYear == $currentYear){
                $intCurrentReturn = $yearlyReturnData[$i]->returns;
            }
        }
        if($returnFlg==0){
            array_push($yearlyReturnsCount, 0);
            array_push($yearlyReturnsAmount, 0);
        }
    }

    $floatTotalSales =  DB::select("SELECT round(sum(b.bil_amt),$this->amt_fix) as totalSales
        from billings as b
        where 
        year(b.created_at) = year(CURRENT_DATE)");

    $floatTotalReturns =  DB::select("SELECT round(sum(b.amount),$this->amt_fix) as totalReturns
        from bill_returns as b
        where 
        year(b.created_at) = year(CURRENT_DATE)");

    $floatCurrentSaleAmount = (isset($floatTotalSales[0]) && $floatTotalSales[0]->totalSales!=null) ? $floatTotalSales[0]->totalSales : 0;
    $floatCurrentReturnAmount = (isset($floatTotalReturns[0]) && $floatTotalReturns[0]->totalReturns != null) ? $floatTotalReturns[0]->totalReturns : 0;

    return Response([
        "arrSalesYearlyCount" => $yearlySalesCount,
        "arrReturnYearlyCount" => $yearlyReturnsCount,
        "arrSalesYearlyAmount" => $yearlySalesAmount,
        "arrReturnYearlyAmount" => $yearlyReturnsAmount,

        "arrYears" => $years,

        "intCurrentSale" => $intCurrentSale,
        'intCurrentReturn' => $intCurrentReturn,
        'floatCurrentSaleAmount' => $floatCurrentSaleAmount,
        'floatCurrentReturnAmount' => $floatCurrentReturnAmount,

        'intCurrentTransactions' => $intCurrentYearTransactions,
        'intCashTransactions' => $intCashTransactions,
        'intCashNCardTransactions' => $intCashNCardTransactions,
        'intCreditTransactions' => $intCreditTransactions,
        'intCardTransactions' => $intCardTransactions,
        'intNoonCreditTransactions' => $intNoonCreditTransactions,
        'arrTopSalingItems' => $arrTopSalingItems,
        'arrCashierSales' => $arrCashierSales,
        'arrSalesmanPer' => $arrSalesmanPer,
    ]);
}
catch(Exception $e){
    Log::error("DashboardController@getSalesYearly ".$e);
    return View("errors.exception");
}

}




}
