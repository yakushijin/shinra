@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-2 col-lg-3"></div>
    <div class="col-md-8 col-lg-6">
        <div class="mainBlock">
            <form class="form-horizontal" method="POST" action="{{ route('login') }}">
                {{ csrf_field() }}
                <div class="form-group">
                    <div class="panel-body">
                        @if (session('status'))
                        <div id="resultMassage" class="baseMessage">
                            {{ session('status') }}
                        </div>
                        @endif
                    </div>
                    <div id="buttonarea" class="topButtonArea">
                        <button type="submit" id="loginDisp" class="topButton">ログイン画面へ</button>
                    </div>
                </div>
            </form>


        </div>
    </div>
</div>
@endsection