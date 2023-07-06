@extends('layouts.app')

@section('content')
<div class="row">


    <div class="col-md-2 col-lg-3"></div>
    <div class="col-md-8 col-lg-6">
        <div class=" mainBlock">

            <form class="form-horizontal" method="POST" action="{{ route('login') }}">
                {{ csrf_field() }}

                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <label for="email" class="col-sm-4 col-md-4 control-label">ログインID</label>

                    <div class="col-sm-12 col-md-6">
                        <span id="emailarea">
                            <input id="email" type="email" class="form-control topText" name="email" value="{{ old('email') }}" required autofocus>
                        </span>

                        @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <label for="password" class="col-sm-4 col-md-4 control-label">パスワード</label>

                    <div class="col-sm-12 col-md-6">
                        <input id="password" type="password" class="form-control topText" name="password" required>

                        @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-4 col-md-4"></div>
                    <div class="col-sm-6 col-md-6">
                        <button type="submit" class="topButton">
                            ログイン
                        </button>
                        <div class="checkBox">
                            <label>
                                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> ログイン状態を保持
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-2 col-md-2"></div>

                </div>
            </form>
        </div>
    </div>
    <div class="col-md-2 col-lg-3"></div>
    @yield('addcontent')
</div>
@endsection