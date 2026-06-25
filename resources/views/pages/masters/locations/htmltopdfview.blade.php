@extends('layouts.app')

@section('page-title')@lang('glob.location')@endsection

@section('page-subtitle')@lang('glob.add_edit_location')@endsection 

@section ('breadcrumb')

<ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-home"></i> @lang('glob.home')</a></li>
    <li><a href="#"><i class="fa fa-user"></i> @lang('menu.masters')</a></li>
    <li class="active">@lang('glob.location')</li>
</ol>

@endsection

@section('content')
<div class="location"> 

<div class="row">
    <a href="{{ route('htmltopdfview',['download'=>'pdf']) }}">Download PDF</a>
    <table>
        <tr>
            <th>Name</th>
            <th>Details</th>
        </tr>
        @foreach ($locations as $location)
        <tr>
            <td>{{ $location->location }}</td>
            <td>{{ $location->name }}</td>
        </tr>
        @endforeach
    </table>
</div>

<!-- view table data end -->
</div>

@endsection