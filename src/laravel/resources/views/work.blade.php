<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name', 'Laravel') }}</title>

  <link rel="stylesheet" href="lib/css/jquery-ui.min.css" type="text/css">

  <script type="text/javascript" src="lib/js/jquery-3.3.1.min.js"></script>
  <script type="text/javascript" src="lib/js/jquery-ui.min.js"></script>
  <script type="text/javascript" src="lib/js/jquery.ui.touch-punch.min.js"></script>
  <script type="text/javascript" src="lib/js/datepicker-ja.js"></script>

  <link rel="stylesheet" href="lib/css/bootstrap.min.css" type="text/css">
  <script type="text/javascript" src="lib/js/bootstrap.min.js"></script>

  <link rel="stylesheet" href="lib/css/datatables.min.css" type="text/css">
  <script type="text/javascript" src="lib/js/datatables.min.js"></script>

  <script type="text/javascript" src="lib/js/Chart.min.js"></script>

  <script type="text/javascript" src="js/global.js"></script>
  <script type="text/javascript" src="js/object.js"></script>
  <script type="text/javascript" src="js/utility.js"></script>
  <script type="text/javascript" src="js/validation.js"></script>
  <script type="text/javascript" src="js/serverinterface.js"></script>
  <script type="text/javascript" src="js/machinelearning.js"></script>
  <script type="text/javascript" src="js/work.js"></script>
  <script type="text/javascript" src="js/debug.js"></script>
  <link rel="stylesheet" href="css/work.css" type="text/css">
  <link rel="stylesheet" href="css/header.css" type="text/css">
  <link rel="shortcut icon" href="images/favicon.ico">

  @extends('layouts.ga')
</head>

<body>
  <div id="modaldisp"></div>
  <div id="canvasdisp"></div>
  <div id="page" class="page">

    <script type="text/javascript">
      init();
    </script>

    <div class="row headerArea">
      <div class="col-md-4 col-lg-3 header">
        <div id="headertext" class="headertext">
          <img id="logo" class="headerlogo" src="images/logo.png" alt="headerlogo" title="headerlogo" width="200" height="40">
        </div>
      </div>
      <div class="col-md-8 col-lg-9 header">
        <div class="headerButtonArea">
          <div class="headerUserName">
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">ログアウト</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST">{{ csrf_field() }}</form>
          </div>
          <div class="headerUserName" onclick="userinfoget('{{$resdata->userData->userId}}')">{{$resdata->userData->userName}}さん </div>
          <input type="hidden" id="userName" value="{{$resdata->userData->userName}}">
          <input type="hidden" id="authority" value="{{$resdata->userData->authority}}">
          <input type="hidden" id="doneAutoActiveFlg" value="{{$resdata->userData->doneAutoActiveFlg}}">
          <input type="hidden" id="deleteMessageFlg" value="{{$resdata->userData->deleteMessageFlg}}">
          <input type="hidden" id="defaultDeadlineFlg" value="{{$resdata->userData->defaultDeadlineFlg}}">
          <input type="hidden" id="color" value="{{$resdata->userData->color}}">
          <input type="hidden" id="textColor" value="{{$resdata->userData->textColor}}">
          <input type="hidden" id="borderColor" value="{{$resdata->userData->borderColor}}">

          @if ($resdata->userData->authority)
          <div class="headerUserName" onclick="adminPage()">管理</div>
          @else
          @endif

          <div class="headerUserName" onclick="manual('#sousa')">ヘルプ </div>
        </div>
      </div>
    </div>

    <div id="tabscroll" class="tabscroll">
      <div id="tabarea" class="tabarea">
        <div id="tabadd" class="tabAdd addDeleteBotton" onclick="tabAddDisp()">＋</div>
        <ul id="tablistarea">
          <span id="tabSortStartArea"></span>
          <span id="tabSortEndArea"></span>
        </ul>

      </div>
    </div>

    <div id="mainarea">
    </div>

    @if (count($resdata) > 0)

    @foreach($resdata as $key=>$tabdata)
    <script type="text/javascript">
      var tabGroup = new TabGroup(
        "{{$tabdata->tabId}}",
        "{{$tabdata->tabName}}",
        "{{$tabdata->color}}",
        "{{$tabdata->textColor}}",
        "{{$tabdata->borderColor}}",
        "{{$tabdata->tabDeadline}}",
        "{{$tabdata->groupFlg}}",
        "{{$tabdata->userOrGroupName}}",
        "{{$tabdata->userOrGroupId}}",
        "{{$tabdata->userOrGroupColor}}",
        "{{$tabdata->userOrGroupTextColor}}",
        "{{$tabdata->userOrGroupBorderColor}}"
      );
      tabDisp(tabGroup);
    </script>
    @foreach($resdata[$key]->category as $categorydata)
    <script type="text/javascript">
      var categoryGroup = new CategoryGroup(
        "{{$categorydata->tabId}}",
        "{{$categorydata->categoryId}}",
        "{{$categorydata->categoryName}}",
        "",
        "{{$categorydata->categoryNotstarted}}",
        "{{$categorydata->categoryWorking}}",
        "{{$categorydata->categoryWaiting}}",
        "{{$categorydata->categoryDone}}",
        "{{$categorydata->categoryDeadline}}",
        "{{$tabdata->color}}",
        "{{$tabdata->textColor}}",
        "{{$tabdata->borderColor}}",
        "{{$tabdata->groupFlg}}",
        "{{$categorydata->userId}}",
        "{{$categorydata->userName}}",
        "{{$categorydata->groupId}}",
        "{{$categorydata->myUserflg}}",
        "{{$categorydata->color}}",
        "{{$categorydata->textColor}}",
        "{{$categorydata->borderColor}}",
        "{{$categorydata->categorySort}}",
      );
      categoryDisp(categoryGroup);
    </script>
    @endforeach

    @endforeach


    @else

    @endif

    @extends('layouts.debugmode')

    <div id="resultdisp"></div>
  </div>

  <script type="text/javascript">
    endInit();
  </script>

</body>

</html>