@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-2 col-lg-3"></div>
    <div class="col-md-8 col-lg-6">
        <div class=" mainBlock">
            {{ csrf_field() }}
            <div class="baseResult">
                <div id="resultdisp" class="resultdisp">仮登録完了</div>
            </div>
            <div id="resultMassage" class="baseMessage">
                {{session('email')}}へメールを送信しました。<br>メール本文記載のURLを押すことで登録が完了となります。
            </div>
        </div>
    </div>
</div>
</div>

@endsection