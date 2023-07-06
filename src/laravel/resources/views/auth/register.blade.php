@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-2 col-lg-3"></div>
    <div class="col-md-8 col-lg-6">
        <div class=" mainBlock">
            <form class="form-horizontal" method="POST" action="{{ route('register') }}">
                {{ csrf_field() }}

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
                            新規ユーザ登録
                        </button>
                    </div>
                </div>
            </form>
            <div id="registerMassage" class="baseMessage">
                ※「新規ユーザ登録ボタン」をクリックすることで、<br>
                <span class="linkText" onclick="kiyaku()">利用規約</span>と
                <span class="linkText" onclick="privacy()">プライバシーポリシー</span>
                に同意頂いたものとみなします。
            </div>

        </div>
    </div>
</div>
</div>

@endsection