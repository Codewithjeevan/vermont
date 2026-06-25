<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    /**
     * show the application master screen for customer
     * 
     * @return pages/billing/billing.blade.php
     */
    public function billing(){

        return view('pages.billing.billing');
    }

    public function billing2(){

        return view('pages.billing.billing2');
    }

   
    /**
     * show the supplier under masters tab...
     * 
     * @return /pages/masters/suppliers/supplier.blade.php
     */
    public function supplier(){

        return view('pages.masters.suppliers.supplier');

    }
}
