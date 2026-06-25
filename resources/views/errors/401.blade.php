@extends('layouts.app')
@section('page-title')
Access Denied
@endsection

@section('content')        
<link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

<style>
    html, body {
        height: 100%;
    }

    .main {
        margin: 0;
        margin-top: 10%;
        padding: 0;
        width: 100% !important;
        
        display: table;
        font-weight: 200;
        font-family: 'Lato', sans-serif;
    }

    .container {
        text-align: center;
        display: table-cell;
        vertical-align: middle;
    }

    .content {
        text-align: center;
        display: inline-block;
        width:100%;
    }

    .title {
        font-size: 40px;
        margin-bottom: 40px !important;
    }
</style>
<div class="main">
    <div class="title">You don't have privilege to access {{ $page }} page. <br>Please contact your Administrator to get access.</div>
</div>


@endsection

