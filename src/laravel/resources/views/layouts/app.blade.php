<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="{{ asset('lib/js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('js/global.js') }}"></script>
    <script src="{{ asset('js/utility.js') }}"></script>
    <script src="{{ asset('js/serverinterface.js') }}"></script>
    <script src="{{ asset('js/systemInit.js') }}"></script>

    <script src="{{ asset('lib/js/bootstrap.min.js') }}"></script>
    <link href="{{ asset('lib/css/bootstrap.min.css') }}" rel="stylesheet">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/home.css') }}" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">

    @extends('layouts.ga')
</head>

<body>
    <div id="app">
        <div id="page" class="page">
            <div class="row">
                <div class="col-md-2 col-lg-3"></div>
                <div class="col-md-8 col-lg-6">
                    <div class="logoBlock">
                        <div> <img id="logo" class="headerlogo" src="{{ asset('images/logo.png') }}" alt="headerlogo" title="headerlogo" width="90%" height="30%"></div>
                        <div class="versionArea topText">
                            ver1.2.4
                        </div>
                    </div>
                    <div class="menuArea">
                        <span class="topMenu"><a href="{{ route('register') }}">新規登録</a> </span>
                        <span class="topMenu"><a href="{{ route('password.request') }}"> システムパスワード再発行 </a></span>
                        <span class="topMenu" onclick="help()">ヘルプ</span>
                    </div>
                </div>
            </div>
            @yield('content')
        </div>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
</body>

</html>