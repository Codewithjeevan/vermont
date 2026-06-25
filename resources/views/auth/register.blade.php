@extends('layouts.start')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-5" style="margin-top: 22%">
            <div class="text-right header">
                <h1 class="title">Retail<span class="subtitle">Sales</span></h1>
            </div>
        </div>
        <div class="col-md-4" style="margin-top: 12%">
            <div class="">
                <div class="panel-body">
                    <form class="form" role="form" method="POST" action="{{ url('/register') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class=" control-label">Name</label>

                            
                            <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>

                            @if ($errors->has('name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                            @endif
                            
                        </div>

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="username" class=" control-label">Username</label>

                            
                            <input id="username" type="text" class="form-control" name="email" value="{{ old('email') }}" required>

                            @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                            @endif
                            
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class=" control-label">Password</label>

                            
                            <input id="password" type="password" class="form-control" name="password" required>

                            @if ($errors->has('password'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                            @endif
                            
                        </div>

                        <div class="form-group">
                            <label for="password-confirm" class="control-label">Confirm Password</label>

                            
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block">
                                Register
                            </button>   
                            <a href="{{ url('/login') }}" class="btn register  btn-block">Login</a> 
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
