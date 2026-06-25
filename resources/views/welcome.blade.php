@extends('layouts.start')

@section('content')

@if($type=="valid")
<style type="text/css">
   .flex-center {
       align-items: center;
       display: flex;
       justify-content: center;
   }

   .position-ref {
       position: relative;
   }

   .content {
       text-align: center;
   }

   .title {
       font-size: 84px;
   }

   .links > a {
       padding: 0 25px;
       font-size: 12px;
       font-weight: 600;
       letter-spacing: .1rem;
       text-decoration: none;
       text-transform: uppercase;
   }

   .m-b-md {
       margin-bottom: 30px;
   }
   .h-link{
    text-decoration:none !important;
    color:white;
}
.title-center{
    margin-top: 22%;

}
</style>

@endif

<div class="container license" name="csrf-token" content="{{ csrf_token() }}">

    <div class="row">

        @if($type=="valid")
        <div class="title m-b-md title-center" >
            <center>
                @if (Auth::check())
                <a href="/login"  style="text-decoration: none; color: white"> Vermont <span class="subtitle">Retail</span> </a>
                @else
                <a href="/login"  style="text-decoration: none; color: white"> Vermont <span class="subtitle">Retail</span> </a>
                @endif
            </center>    
            
        </div>
        @endif


        @if(($type=="new") || ($type=="expired"))
        <div class="col-md-5" style="margin-top: 22%">
            <div class="text-right header">
                <h1 class="title"><a href="/login"  style="text-decoration: none; color: white"> Vermont<span class="subtitle">Retail</span> </a></h1>
            </div>
        </div>

        {{-- New License --}}
        <div class="col-md-4" style="margin-top: 10%" id="new-license-panel">
            <div class="">
                <div class="panel-body" >
                   
                    <h3 class="subtitle exo">
                        @if($type=="expired")
                        <span style="color:whitesmoke">Your license</span><span> is expired</span><br/>
                        @endif
                        <span style="color:whitesmoke">Get a license </span> <span> to continue! </span>
                    </h3>
                    <form class="form" >

                       @if(($type=="new"))
                       <div class="form-group">
                        <label for="shop-name" class=" control-label">Shop Name</label>
                        <input id="shop-name" type="text" class="form-control" name="shop_name" value="" required autofocus>
                    </div>
                    @endif

                    @if(($type=="expired"))
                    <div class="form-group">
                        <label for="shop-name" class=" control-label">Shop Name</label>
                        <input id="shop-name" type="text" class="form-control" name="shop_name" value="{{$shop_name}}" disabled>
                    </div>
                    @endif

                    <div class="form-group">
                        <label for="license-type" class=" control-label">License Type</label>
                        <select name="license_type" id="license-type" class="form-control">
                            <option value="1">Limited License</option>
                            <option value="2">Permanent License</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="activation-date" class=" control-label">Activation Date</label>
                        <input id="activation-date" type="text" class="form-control input-sm datepicker-futureDate" name="activation_date" value="" required>
                    </div>

                    <div class="form-group">
                        <label id="expiry-date-label" for="expiry-date" class="control-label">Expiry Date</label>
                        <input id="expiry-date" type="text" class="form-control input-sm datepicker-futureDate" name="expiry_date" value="" required>

                    </div>

                    <div class="form-group">
                        <button id="requestLicence" class="btn btn-primary btn-block">
                            Request License
                        </button> 
                        <a class="btn btn-link btn-block panel-toggle" id="has-license">Already has license?</a>  
                    </div>
                </form>
            </div>
        </div>
    </div>




    {{-- Verify License --}}
    <div class="col-md-4" style="margin-top: 12%" id="verify-license-panel">
        <div class="">
            <div class="panel-body" >
                <h3 class="subtitle exo">
                    <span style="color:white">Verify </span> License
                </h3>
                <form class="form" >


                    <div class="form-group">
                        <label for="license-id" class=" control-label">License ID</label>
                        <input id="license-id" type="text" class="form-control" name="license_id" value="" required autofocus>
                    </div>

                    <div class="form-group">
                        <label for="license-key" class=" control-label">License Key</label>
                        <input id="license-key" type="text" class="form-control" name="license_key" value="" required>

                    </div>

                    <div class="form-group">
                        <button id="verifyLicence" class="btn btn-primary btn-block">
                            Verify License
                        </button> 
                        <a class="btn btn-link btn-block panel-toggle" id="dont-have-license">Dont have license?</a>  
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
</div>

<script src="{{ asset('js/start.js') }}"></script>
@endsection
