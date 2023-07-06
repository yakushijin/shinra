<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name', 'Laravel') }}</title>

  <link rel="stylesheet" href="lib/css/jquery-ui.min.css" type="text/css">
  <script type="text/javascript" src="lib/js/jquery-3.3.1.min.js"></script>

  <script type="text/javascript" src="js/global.js"></script>

  <link rel="stylesheet" href="lib/css/bootstrap.min.css" type="text/css">
  <script type="text/javascript" src="lib/js/bootstrap.min.js"></script>

  <link rel="stylesheet" href="css/header.css" type="text/css">
  <link rel="stylesheet" href="css/not.css" type="text/css">
  <link rel="shortcut icon" href="images/favicon.ico">

  @extends('layouts.ga')
</head>

<body>

  <div class="row headerArea">
    <div class="col-md-4 col-lg-3 header">
      <div id="headertext" class="headertext">
        <img id="logo" class="headerlogo" src="images/logo.png" alt="headerlogo" title="headerlogo" width="200" height="40">
      </div>
    </div>
    <div class="col-md-8 col-lg-9 header">
      <div class="headerLogout">
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">ログアウト</a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST">{{ csrf_field() }}</form>
      </div>

      <div class="headerUserName" onclick="manual('#sousa')">ヘルプ </div>
    </div>
  </div>

  <div class="messageArea">
    <div class="message">{{$message}} </div>
  </div>

</body>

</html>