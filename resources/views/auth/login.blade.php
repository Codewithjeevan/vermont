@extends('layouts.start')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-5" style="margin-top: 22%">
            <div class="text-right header">
                <h1 class="title">Vermont<span class="subtitle">Retail</span></h1>
            </div>
        </div>
        <div class="col-md-4 " style="margin-top: 15%">
            <div class="">
                <div class=""></div>
                <div class="panel-body">
                    <form id="loginForm" class="form" role="form" method="POST" action="{{ url('/login') }}">
                        {{ csrf_field() }}

                        <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}" >
                            <label for="email" class="control-label">Username</label>
                            <select id="email"  name="email"  class="form-control text-center select2">
                                <option value="" style="height: 25px;""></option>
                                @foreach($users as $u)
                                <option value="{{ $u->email }}" style="height: 25px;">{{ $u->email }}</option>
                                @endforeach
                            </select>

                            
                           

                            @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                            @endif
                            
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="control-label">Password</label>
                            <input  id="loginPass" type="password" class="form-control keyboard text-center" name="password" required>

                            @if ($errors->has('password'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                            @endif
                        </div>

                      

                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary btn-block">
                                Login
                            </button>
                            
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
