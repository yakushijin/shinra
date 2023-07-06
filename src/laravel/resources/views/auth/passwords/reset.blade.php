@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-2 col-lg-3"></div>
    <div class="col-md-8 col-lg-6">
        <div class=" mainBlock">
            <form class="form-horizontal" method="POST" action="{{ route('password.request') }}">
                {{ csrf_field() }}

                <div class="control-label">
                    <div id="resultMassage" class="baseMessage">ご登録いただいているメールアドレスと、新しいパスワードを入力し、パスワード再登録ボタンを押してください。</div>

                </div>

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <label for="email" class="col-sm-4 col-md-4 control-label">メールアドレス</label>

                    <div class="col-sm-12 col-md-6">
                        <input id="email" type="email" class="form-control topText" name="email" value="{{ old('email') }}" required>

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
                    <label for="password-confirm" class="col-sm-4 col-md-4 control-label">パスワード（確認）</label>

                    <div class="col-sm-12 col-md-6">
                        <input id="password-confirm" type="password" class="form-control topText" name="password_confirmation" required>

                        @if ($errors->has('password_confirmation'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password_confirmation') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-4 col-md-4"></div>
                    <div class="col-sm-6 col-md-6">
                        <button type="submit" class="topButton">
                            パスワード再登録
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
</div>
</div>
@endsection