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
  <script type="text/javascript" src="js/modaladmin.js"></script>
  <script type="text/javascript" src="js/machinelearning.js"></script>
  <script type="text/javascript" src="js/admin.js"></script>
  <link rel="stylesheet" href="css/work.css" type="text/css">
  <link rel="stylesheet" href="css/admin.css" type="text/css">
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
          <div class="headerUserName" onclick="userinfoget('{{$resdata->userId}}')">{{$resdata->userName}}さん </div>
          <input type="hidden" id="systemUser" value="{{$resdata->systemUser}}">
          <input type="hidden" id="authority" value="{{$resdata->authority}}">
          <div class="headerUserName" onclick="userPage()">ワーク</div>
          <div class="headerUserName" onclick="manual('#sousa')">ヘルプ</div>
        </div>
      </div>
    </div>

    <div id="adminMainArea" class="adminMainArea">
      <div class="row">
        <div class="col-sm-12 col-md-4">
          <div id="operationTitel" class="subTitelArea">・操作メニュー</div>
          <div id="searcharea" class=" searcharea">

            <input id="acd-check1" class="acd-check" name="menu" type="radio">
            <label class="acd-label" for="acd-check1">ユーザ管理</label>
            <div class="acd-content">
              <div class="searchGroup">
                <div class="searchUnit">
                  <label class="baseLabel">ユーザ名
                    <input type="text" id="userName" class="baseTextBox textGroup" maxlength="20" autocomplete="off">
                  </label>
                </div>
                <div class="searchUnit">
                  <input type="checkbox" id="userActive" class="checkBoxGroup baseCheckBox"><label for="userActive" class="baseCheckBox-label" onclick="">無効化</label>
                </div>
                <div class="searchUnit">
                  <input type="checkbox" id="userAuthority" class="checkBoxGroup baseCheckBox"><label for="userAuthority" class="baseCheckBox-label" onclick="">管理者権限</label>
                </div>
              </div>
              <div class="centerArea">
                <button type="button" id="userSearch" class="modalSubButton subButtonSelect" onclick=userSearch()>検索</button>
                <button type="button" id="userCreate" class="modalSubButton subButtonUpdate" onclick=userCreate()>新規作成</button>
              </div>
            </div>

            <input id="acd-check2" class="acd-check" name="menu" type="radio">
            <label class="acd-label" for="acd-check2">グループ管理</label>
            <div class="acd-content">
              <div class="searchGroup">
                <div class="searchUnit">
                  <label class="baseLabel">グループ名
                    <input type="text" id="groupName" class="baseTextBox textGroup" maxlength="20" autocomplete="off">
                  </label>
                </div>
                <div class="searchUnit">
                  <input type="checkbox" id="groupActive" class="checkBoxGroup baseCheckBox"><label for="groupActive" class="baseCheckBox-label" onclick="">無効化</label>
                </div>
              </div>
              <div class="centerArea">
                <button type="button" id="groupSearch" class="modalSubButton subButtonSelect" onclick=groupSearch()>検索</button>
                <button type="button" id="groupCreate" class="modalSubButton subButtonUpdate" onclick=groupCreate()>新規作成</button>
              </div>
            </div>

            <input id="acd-check3" class="acd-check" name="menu" type="radio">
            <label class="acd-label" for="acd-check3">タブ管理</label>
            <div class="acd-content">
              <div class="searchGroup">
                <div class="searchUnit">
                  <label class="baseLabel">タブ名
                    <input type="text" id="tabName" class="baseTextBox textGroup" maxlength="20" autocomplete="off">
                  </label>
                </div>
                <div class="searchUnit">
                  <input type="checkbox" id="tabActive" class="checkBoxGroup baseCheckBox"><label for="tabActive" class="baseCheckBox-label" onclick="">無効化</label>
                </div>
                <div class="searchUnit">
                  <label class="baseLabel">期限日(まで)
                    <input type="text" id="tabDeadline" class="baseDayTextBox" name="searchDay" autocomplete="off">
                  </label>
                </div>
              </div>
              <div class="centerArea"><button type="button" id="tabSearch" class="modalSubButton subButtonSelect" onclick=tabSearch()>検索</button></div>
            </div>

            <input id="acd-check4" class="acd-check" name="menu" type="radio" checked="checked">
            <label class="acd-label" for="acd-check4">実績管理</label>
            <div class="acd-content">
              <div class="searchGroup">
                <div id="graphvalidationMessage" class="validationMessage"></div>
                <div class="searchUnit">
                  <label class="baseLabel">日付
                    <input type="text" id="performanceDayFrom" class="baseDayTextBox" name="searchDay" autocomplete="off">～
                    <input type="text" id="performanceDayTo" class="baseDayTextBox" name="searchDay" autocomplete="off">
                  </label>
                </div>
                <div class="searchUnit">
                  <label class="baseLabel">対象
                    <span class="baseRadioButtonBack">
                      <input type="radio" id="targetAll" class="radioGroup baseRadioButton" name="target" value="targetAll" checked="checked"><label for="targetAll" class="baseRadioButton-label" onclick="">全体</label>
                      <input type="radio" id="targetTab" class="radioGroup baseRadioButton" name="target" value="targetTab"><label for="targetTab" class="baseRadioButton-label" onclick="">タブ</label>
                      <input type="radio" id="targetGroup" class="radioGroup baseRadioButton" name="target" value="targetGroup"><label for="targetGroup" class="baseRadioButton-label" onclick="">グループ</label>
                    </span>
                  </label>
                </div>
                <div class="searchUnit">
                  <label class="baseLabel">間隔
                    <span class="baseRadioButtonBack">
                      <input type="radio" id="intervalMonth" class="radioGroup baseRadioButton" name="interval" value="intervalMonth"><label for="intervalMonth" class="baseRadioButton-label" onclick="">月</label>
                      <input type="radio" id="intervalDay" class="radioGroup baseRadioButton" name="interval" value="intervalDay" checked="checked"><label for="intervalDay" class="baseRadioButton-label" onclick="">日</label>
                    </span>
                  </label>
                </div>
              </div>
              <div class="centerArea"><button type="button" id="performanceSearch" class="modalSubButton subButtonSelect" onclick=performanceSearch()>検索</button></div>
            </div>

            <input id="acd-check5" class="acd-check" name="menu" type="radio">
            <label class="acd-label" for="acd-check5">フリーワード検索</label>
            <div class="acd-content">
              <div class="searchGroup">
                <div id="freeWordValidationMessage" class="validationMessage"></div>
                <div class="searchUnit">
                  <label class="baseLabel">ワード
                    <input type="text" id="freeWord" class="baseTextBox textGroup" maxlength="30" autocomplete="off">
                  </label>
                </div>
                <div class="searchUnit">
                  <label class="baseLabel">対象
                    <span class="baseRadioButtonBack">
                      <input type="radio" id="wordTargetUser" class="radioGroup baseRadioButton" name="wordTarget" value="wordTargetUser" checked="checked"><label for="wordTargetUser" class="baseRadioButton-label" onclick="">ユーザ</label>
                      <input type="radio" id="wordTargetTab" class="radioGroup baseRadioButton" name="wordTarget" value="wordTargetTab"><label for="wordTargetTab" class="baseRadioButton-label" onclick="">タブ</label>
                      <input type="radio" id="wordTargetGroup" class="radioGroup baseRadioButton" name="wordTarget" value="wordTargetGroup"><label for="wordTargetGroup" class="baseRadioButton-label" onclick="">グループ</label>
                    </span>
                  </label>
                </div>
              </div>
              <div class="centerArea"><button type="button" id="freeWordSearch" class="modalSubButton subButtonSelect" onclick=freeWordSearch()>検索</button></div>
            </div>

            <input id="acd-check6" class="acd-check" name="menu" type="radio">
            <label class="acd-label" for="acd-check6">システム管理</label>
            <div class="acd-content">
              <div class="searchGroup">

              </div>
              <div class="centerArea"><button type="button" id="freeWordSearch" class="modalSubButton subButtonSelect" onclick=companyget()>情報表示</button></div>
            </div>
          </div>

          <div id="graphTitel" class="subTitelArea">・検索結果リスト</div>
          <div id="resultdisparea" class=" searcharea">
            <div id="resultitle" class="resultitle"></div>
            <div id="searchResult" class="searchResult"></div>
          </div>

        </div>

        <div class="col-sm-12 col-md-8">
          <div id="graphTitel" class="subTitelArea">・検索結果グラフ</div>

          <div id="graphdisparea" class="resultdisparea">
            <div id="graphtitle" class="resultitle"></div>
            <div id="graphResult" class="graphResult"></div>
          </div>

          <div id="wordcloudarea" data-disp=0>

          </div>

        </div>

        <div class="col-lg-12">
          <div id="wordcloudInitarea">
          </div>
        </div>




      </div>
    </div>

    <div id="resultdisp"></div>

    <script type="text/javascript">
      endInit();
    </script>
  </div>



</body>

</html>