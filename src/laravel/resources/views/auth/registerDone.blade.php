@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-2 col-lg-3"></div>
    <div class="col-md-8 col-lg-6">
        <div class=" mainBlock">
            <form class="form-horizontal" method="POST" action="{{ route('logininit') }}">
                {{ csrf_field() }}
                <div class="form-group">
                    <div id="messageInfo">
                        <div id="resultArea" class="baseResult">
                            <div id="resultdisp" class="resultdisp">アカウント登録完了</div>
                        </div>
                        <div id="resultMassage" class="baseMessage"> アカウントの本登録が完了しました。
                            <br>初回データベースの作成を行います。次へをクリックしてください。</div>

                    </div>
                    <input type="hidden" id="email" name="email" value="{{$email}}">
                    <input type="hidden" id="token" name="token" value="{{$token}}">
                    <div id="buttonarea" class="topButtonArea">
                        <button type="button" id="databaseCreateId" class="topButton" onclick=databaseCreate()>次へ</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
</div>

@endsection