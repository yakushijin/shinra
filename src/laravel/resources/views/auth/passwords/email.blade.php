@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-2 col-lg-3"></div>
    <div class="col-md-8 col-lg-6">
        <div class=" mainBlock">
            @if (session('status'))
            <div class="control-label">
                <div id="resultMassage" class="baseMessage"> {{ session('status') }}</div>
            </div>
            @endif

            <form class="form-horizontal" method="POST" action="{{ route('password.email') }}">
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

                <div class="form-group">
                    <div class="col-sm-4 col-md-4"></div>
                    <div class="col-sm-6 col-md-6">
                        <button type="submit" class="topButton">
                            パスワードリセット
                        </button>
                    </div>

                </div>
            </form>
            <div id="registerMassage" class="baseMessage">
                ※本メニューはシステムユーザ専用のメニューとなり、<br>
                通常のユーザのパスワードをリセットするものではありません。<br>
                通常のユーザのパスワード変更については、<br>
                <span class="linkText topText" onclick="manual('#sousa')">操作説明の【3】[3-3-2]ユーザのログイン情報を更新する</span>を参照ください。
            </div>
        </div>
    </div>
</div>
</div>
@endsection