$(function () {
  $("#logo").click(function () {
    window.location.href = WORKPATH;
  });
});

/*=================================
初期化処理（初回ページ遷移時に実行）
=================================*/
function init() {
  buttonDispChange();
  noticeDispChange();
  $("#modaldisp").load("html/modal.html");
  $("#canvasdisp").load("html/canvas.html");
};

//機種のサイズごとにボタン表示名を変更する
function buttonDispChange() {
  if (window.innerWidth <= MINSIZE) {
    notstartedText = "+";
    workingText = "+";
    waitingText = "+";
    doneText = "+";
    suspendedText = "+";
  }
  else if (window.innerWidth <= SMARTPHONE) {
    notstartedText = "未";
    workingText = "作";
    waitingText = "待";
    doneText = "完";
    suspendedText = "中";
  } else if (window.innerWidth <= TABLET) {
    notstartedText = "未着";
    workingText = "作業";
    waitingText = "待機";
    doneText = "完了";
    suspendedText = "中断";
  }
};

//機種のサイズごとにメッセージエリアのサイズを変更する
function noticeDispChange() {
  if (window.innerWidth <= MINSIZE) {
    noticeSize = "90%";
    noticeLeft = "10%";

  }
  else if (window.innerWidth <= SMARTPHONE) {
    noticeSize = "80%";
    noticeLeft = "20%";

  } else if (window.innerWidth <= TABLET) {
    noticeSize = "60%";
    noticeLeft = "40%";

  } else {
    noticeSize = "40%";
    noticeLeft = "60%";
  }
};

/*=================================
初期化処理（初回ページ遷移後最後にに実行）
=================================*/
function endInit() {
  $(function () {
    var tabTmpListSort = [];

    //ローカルストレージからタブの並び順を格納した配列を取得
    tabLocalListSort = JSON.parse(localStorage.getItem('tabListSort'));
    //ローカルストレージのデータがない場合、タブ生成時に格納した配列をローカルストレージに格納
    if ($.isEmptyObject(tabLocalListSort)) {
      localStorage.setItem('tabListSort', JSON.stringify(tabServerListSort));
    } else {

      //ローカルストレージのタブリストとserver側から取得したタブリストで両方にあるタブを抽出
      //（ローカル側の不要タブリストを排除する意図の処理）
      tabLocalListSort.forEach(function (local) {
        tabServerListSort.forEach(function (server) {
          if (local == server) {
            tabTmpListSort.push(local);
          }
        });
      });

      //上の処理で抽出したタブリストに、server側のタブリストを追加し重複削除
      tabListSort = tabTmpListSort.concat(tabServerListSort).filter(function (x, i, self) {
        return self.indexOf(x) === i;
      });
    }

    //画面上に並び順を適用する
    tabListSort.forEach(function (value) {
      $('#tabSortEndArea').before(
        $('#' + value),
      );
    });

  });
  //並び順変更用処理
  $('#tablistarea').sortable({
    cursor: "move",
    opacity: 0.7,
    placeholder: "ph1",
    handle: "[name='tabIcon']",
    forcePlaceholderSize: true,
    axis: 'x',
    update: function (event, ui) {
      //挿入対象レコードの情報
      var tabListDomId = ui.item[0].id;
      var tabListArrayNum = tabListSort.indexOf(tabListDomId);

      //挿入対象レコードの挿入後、前にあるレコードの情報（idと配列番号）
      var previousTabListDomId = $("#" + tabListDomId).prev();
      var previousTabListArrayNum = tabListSort.indexOf(previousTabListDomId[0].id);
      //挿入対象レコードの挿入後、後ろにあるレコードの情報（idと配列番号）
      var nextTabListDomId = $("#" + tabListDomId).next();
      var nextTabListArrayNum = tabListSort.indexOf(nextTabListDomId[0].id);

      //自分の要素を一旦削除（後で追加した際二重にリストに登録される為）
      tabListSort.splice(tabListArrayNum, 1);

      //移動先が最先頭の場合、配列の先頭に移動対象要素を格納する
      if (previousTabListDomId[0].id == 'tabSortStartArea') {
        tabListSort.splice(0, 0, tabListDomId);

        //移動先が最後尾の場合、配列の末尾に移動対象要素を格納する
      } else if (nextTabListDomId[0].id === 'tabSortEndArea') {
        tabListSort.splice(tabListSort.length, 0, tabListDomId);

        //それ以外（真ん中の場合）
      } else {
        //左へ移動の場合（自分の配列番号が移動先の配列番号より大きい）
        if (tabListArrayNum > previousTabListArrayNum) {
          //移動先の後ろの要素の前に自分の要素を入れる
          tabListSort.splice(nextTabListArrayNum, 0, tabListDomId);

          //右へ移動の場合（自分の配列番号が移動先の配列番号より小さい）
        } else if (tabListArrayNum < nextTabListArrayNum) {
          //移動先の前の要素の後ろに自分の要素を入れる
          tabListSort.splice(previousTabListArrayNum, 0, tabListDomId);

        } else {
          //何もしない※配列番号が被ることはありえないのでエラー制御入れるか要検討
        }

      }

      //ローカルストレージへ変更後リストを格納
      localStorage.setItem('tabListSort', JSON.stringify(tabListSort));

    }
  });

  //最後に選択していたタブをローカルストレージから抽出し、画面に設定
  var tabListSelectArray = JSON.parse(localStorage.getItem('tabListSelect'));
  if (!$.isEmptyObject(tabListSelectArray)) {
    tabSelect(tabListSelectArray[0], tabListSelectArray[1], tabListSelectArray[2], tabListSelectArray[3]);
  }
};

function adminPage() {
  window.location.href = ADMINPAGE;
};

/*=================================
イベント監視系処理
=================================*/
//カテゴリ追加用テキストエリアEnter押下処理
function categoryTextEnterPress(groupFlg, userOrGroupId, dbTabId, domCategoryAddTextId, color, textColor, borderColor) {
  $("#" + domCategoryAddTextId).on('keydown', function (event) {
    var text = $("#" + domCategoryAddTextId).val();
    if (event.which === 13 && text != "") {
      categoryCreate(groupFlg, userOrGroupId, dbTabId, domCategoryAddTextId, color, textColor, borderColor);
      categoryTextDelete(domCategoryAddTextId);
    }
  });
};

//タスク追加用テキストエリアEnter押下処理
function taskTextEnterPress(groupId, dbTabId, categoryId, domTaskAddTextId, color, textColor, borderColor) {
  $("#" + domTaskAddTextId).on('keydown', function (event) {
    var text = $("#" + domTaskAddTextId).val();
    if (event.which === 13 && text != "") {
      taskCreate(groupId, dbTabId, categoryId, domTaskAddTextId, color, textColor, borderColor);
    }
  });
};

//タブのテキスト変更を行う為のテキスト長押し処理
function tabTextAreaLongPush(second, areaId, textId) {
  textAreaLongPushRun(second, areaId, textId);
};

$("div").scroll(function () {
  // div要素内でスクロールされた時に実行する処理
});

//カテゴリのテキスト変更を行う為のテキスト長押し処理
function categoryTextAreaLongPush(second, areaId, textId) {
  if (checkCategoryMyUserAdmin(areaId)) {
    textAreaLongPushRun(second, areaId, textId);
  }
};

function textAreaLongPushRun(second, areaId, textId) {
  var edit;
  $("#" + textId).on("mousedown touchstart", function (e) {
    edit = setTimeout(function () {
      $('#' + areaId).attr('data-edit', 1);
      $('#' + textId).attr('readonly', false);
      $('#' + textId).css('cursor', 'text');
      $('#' + textId).css('background', 'radial-gradient(#f0d39c, #dc9d16)');
      $('#' + textId).css('border-radius', '5px');
      $(e.target).trigger('longpress');
    }, second);
  }).on("mouseup mouseleave touchend", function () {
    clearTimeout(edit);
  });
};

//タブとカテゴリのテキスト変更を終了する為のブラー処理
function textAreaBlur(areaId, textId, color) {
  $('#' + textId).blur(function () {
    $('#' + areaId).attr('data-edit', 0);
    $('#' + textId).attr('readonly', true);
    $('#' + textId).css('cursor', 'pointer');
    $('#' + textId).css('background', color);
  });
};

//タブとカテゴリのテキスト変更を終了する為Enterキー押下処理
function textAreaEnter(areaId, textId, color) {
  $("#" + textId).on('keydown', function (event) {
    if (event.which === 13 && $("#" + textId).val() != "") {
      $('#' + areaId).attr('data-edit', 0);
      $('#' + textId).attr('readonly', true);
      $('#' + textId).css('cursor', 'pointer');
      $('#' + textId).css('background', color);
    }
  });
};

//タブとカテゴリのステータスセレクトボックス変更時の処理（スマホ用機能）
function statusSelectBoxChange(type, selectBoxId, areaId, dbId) {
  $("#" + selectBoxId).change(function () {
    status = $("#" + selectBoxId).val();
    if (type == "category") {
      categoryButtonClick(status, dbId, areaId);
    } else if (type == "task") {
      taskButtonClick(status, dbId, areaId);
    }
  });
};

//タブ追加用小ウィンドウテキストエリアEnter押下処理
function tabTextEnterPress() {
  $("#tabAddName").on('keydown', function (event) {
    if (event.which === 13) {
      tabAddRun();
      $("#dialog").remove();
    }
  });
};

/*=================================
タブ関連処理
=================================*/
//タブ切り替え時の処理
function tabSelect(domTabId, domMainAreaId, domTabTextId, color) {
  var edit = $('#' + domTabId).attr('data-edit');
  if (edit == 0) {
    $('div[name="tabback"]').hide();
    $('#' + domMainAreaId).show();

    $('[name=tabtext]').css("font-weight", "normal");

    $('#tabSelectLine').remove();
    $('[name=tab]').css("height", "26px");
    $('[name=tab]').css("border-bottom", "none");

    $('#' + domTabId).css("height", "30px");
    $('#' + domTabId).css("border-bottom", "solid 5px #" + color);

    $('#' + domTabTextId).css("font-weight", "bold");

    //選択したタブの情報をローカルストレージに格納
    var tabListSelectArray = [domTabId, domMainAreaId, domTabTextId, color];
    localStorage.setItem('tabListSelect', JSON.stringify(tabListSelectArray));
  }
};

//タブ表示（新規作成時、既存データ表示時共通）
function tabDisp(tabGroup) {

  var tabbottun;
  var userOrGroupId;
  if (tabGroup.groupFlg == "1") {
    tabbottun = '<span id="' + tabGroup.tabGroupIconId + '" class="tabGroup addDeleteBotton" name="tabIcon" onclick="tabinfogetUserDisp(' + tabGroup.dbTabId + ',\'' + tabGroup.domTabId + '\',\'' + tabGroup.domMainAreaId + '\',\'' + tabGroup.tabCategoryList + '\')">' + tabGroup.userOrGroupName.substring(0, 1) + '</span>';
    userOrGroupId = tabGroup.userOrGroupId;
  } else {
    tabbottun = '<span id="tabIcon" class="tabIcon" name="tabIcon" onclick="tabinfogetUserDisp(' + tabGroup.dbTabId + ',\'' + tabGroup.domTabId + '\',\'' + tabGroup.domMainAreaId + '\',\'' + tabGroup.tabCategoryList + '\')"></span>';
    userOrGroupId = 0;
  }

  //選択しているタブ以外は非表示にする
  $('div[name="tabback"]').hide();

  //タブの表示
  $("#tablistarea").append('<li id="tablist' + tabGroup.domTabId + '" style="display:inline;"><div id="' + tabGroup.domTabId + '" class="tab" name="tab" onclick="tabSelect(\'' + tabGroup.domTabId + '\',\'' + tabGroup.domMainAreaId + '\',\'' + tabGroup.domTabTextId + '\',\'' + tabGroup.color + '\')" data-edit=0>'
    // $("#tabarea").append('<ul id="tablist' + tabGroup.domTabId + '" style="display:inline;"><div id="' + tabGroup.domTabId + '" class="tab" name="tab" onclick="tabSelect(\'' + tabGroup.domTabId + '\',\'' + tabGroup.domMainAreaId + '\',\'' + tabGroup.domTabTextId + '\',\'' + tabGroup.color + '\')" data-edit=0>'
    + tabbottun
    + '<input type="text" maxlength=' + tabTextMaxLength + ' id="' + tabGroup.domTabTextId + '" class="tabtext" name="tabtext" value="' + tabGroup.tabName + '"  data-tabid=' + tabGroup.dbTabId + ' placeholder="タブ名を入力" readonly="readonly" autocomplete="off" >'
    + '</div> </li>'
  );

  //タブの順番をローカルストレージに格納
  tabServerListSort.push('tablist' + tabGroup.domTabId);

  //メインエリアの表示
  $("#mainarea").append('  <div id="' + tabGroup.domMainAreaId + '" name="tabback" class="tabback">'
    + '<div class="textback"><div><ul id="' + tabGroup.tabCategoryList + '"></div></ul>'
    + '<input type="text" maxlength=' + categoryTextMaxLength + ' id="' + tabGroup.domCategoryAddTextId + '" class="textarea" name="categorytext" placeholder="テキストを入力しEnterキー押下で追加" data-tabDeadline="' + tabGroup.tabDeadline + '" autocomplete="off">'
    + '</div>'
    + '</div>');

  //タブエリアの幅調整（横スクロール処理に必要）
  tabAreaSize += TABSIZE;
  $("#tabarea").css("width", tabAreaSize + "px");

  //各種色設定
  $("#" + tabGroup.tabGroupIconId).css({ "background": tabGroup.userOrGroupColor, "color": tabGroup.userOrGroupTextColor, "border": "solid 1px " + tabGroup.userOrGroupBorderColor });
  $("#" + tabGroup.domTabId).css("background-color", tabGroup.color);
  $("#" + tabGroup.domTabId).css("border", "solid 1px " + tabGroup.borderColor);
  $("#" + tabGroup.domMainAreaId).css("background-color", tabGroup.color);
  $("#" + tabGroup.domMainAreaId).css("border", "solid 1px " + tabGroup.borderColor);
  $("#" + tabGroup.domTabTextId).css("background-color", tabGroup.color);
  $("#" + tabGroup.domTabTextId).css("color", tabGroup.textColor);
  $("#" + tabGroup.domCategoryAddTextId).css("background-color", tabGroup.color);
  $("#" + tabGroup.domCategoryAddTextId).css("color", tabGroup.textColor);

  //各種メソッド定義
  tabNameChange(tabGroup.domTabTextId);
  tabTextAreaLongPush(1000, tabGroup.domTabId, tabGroup.domTabTextId);
  textAreaBlur(tabGroup.domTabId, tabGroup.domTabTextId, tabGroup.color);
  textAreaEnter(tabGroup.domTabId, tabGroup.domTabTextId, tabGroup.color);
  categoryTextEnterPress(tabGroup.groupFlg, userOrGroupId, tabGroup.dbTabId, tabGroup.domCategoryAddTextId,
    tabGroup.color, tabGroup.textColor, tabGroup.borderColor);

};

function tabinfogetUserDisp(tabId, domTabId, domMainAreaId, tabCategoryList) {
  MiniLoadOn();
  var data = { 'tabId': tabId, 'domTabId': domTabId, 'domMainAreaId': domMainAreaId, 'tabCategoryList': tabCategoryList, 'disp': WORKDISP };
  ajaxPost(TABINFOGET, data);
};

//タブ名変更
function tabNameChange(domTabTextId) {
  $('#' + domTabTextId).change(function () {
    if (checkAdmin()) {
      var tabname = $('#' + domTabTextId).val();
      if (!checkDoNotUseSymbol(tabname)) {
        MiniLoadOn();
        var tabid = $('#' + domTabTextId).attr('data-tabid');
        var data = { 'tabId': tabid, 'tabName': tabname };
        ajaxPost(TABUPDATE, data);
      }
    } else {
      notAdminAlert();
    }
  });
};


/*=================================
カテゴリ関連処理
=================================*/

//カテゴリ追加作成
function categoryCreate(groupFlg, userOrGroupId, dbTabId, domCategoryAddTextId, color, textColor, borderColor) {
  MiniLoadOn();
  if (autoDeadlineCheck()) {
    var tabDeadline = $('#' + domCategoryAddTextId).attr('data-tabDeadline');
  } else {
    var tabDeadline = "";
  }
  var categoryName = $("#" + domCategoryAddTextId).val();
  var data = { 'groupFlg': groupFlg, 'groupId': userOrGroupId, 'tabId': dbTabId, 'categoryName': categoryName, 'categoryDeadline': tabDeadline, 'color': color, 'textColor': textColor, 'borderColor': borderColor };
  ajaxPost(CATEGORYADD, data);
};

function categoryGetInfo(dbCategoryId, groupFlg, dbTabId, tabCategoryList, color, textColor, borderColor) {

  var data = { 'categoryId': dbCategoryId, 'groupFlg': groupFlg, 'tabId': dbTabId, 'tabCategoryList': tabCategoryList, 'tabColor': color, 'tabTextColor': textColor, 'tabBorderColor': borderColor };
  MiniLoadOn();
  ajaxPost(CATEGORYINFOGET, data);
};

//カテゴリ表示（新規作成時、既存データ表示時共通）
function categoryDisp(categoryGroup) {
  //カテゴリが所属しているタブのデータセット作成
  var categoryTabDataSet = {
    'groupFlg': categoryGroup.groupFlg, 'dbTabId': categoryGroup.dbTabId, 'tabCategoryList': categoryGroup.tabCategoryList,
    'color': categoryGroup.color, 'textColor': categoryGroup.textColor, 'borderColor': categoryGroup.borderColor
  };

  //カテゴリのアイコンをグループ用か個人用か判定
  if (categoryGroup.groupFlg == 1) {
    var groupCategory = '<span id="' + categoryGroup.userNameIconId + '" class="userNameIcon" name="icon" onclick="categoryGetInfo('
      + categoryGroup.dbCategoryId + ',' + categoryGroup.groupFlg + ',\'' + categoryGroup.dbTabId + '\',\'' + categoryGroup.tabCategoryList + '\',\'' + categoryGroup.color + '\',\'' + categoryGroup.textColor + '\',\'' + categoryGroup.borderColor + '\')">'
      + categoryGroup.userName.substring(0, 2) + '</span>';
  } else {
    var groupCategory = '<div class="categoryIcon" name="icon" onclick="categoryGetInfo('
      + categoryGroup.dbCategoryId + ',' + categoryGroup.groupFlg + ',\'' + categoryGroup.dbTabId + '\',\'' + categoryGroup.tabCategoryList + '\',\'' + categoryGroup.color + '\',\'' + categoryGroup.textColor + '\',\'' + categoryGroup.borderColor + '\')"></div>';
  }

  //ステータスセレクトボックス定義（スマホ用機能）
  var categoryStatusSelectBox = statusSelectBoxInit(categoryGroup.domCategoryStatusSelectBoxId);

  //カテゴリ表示
  $("#" + categoryGroup.tabCategoryList).append('<ul id="' + categoryGroup.domCategoryAreaId + '" class="accordion2 category" data-task=0 data-edit=0 data-myUserflg=' + categoryGroup.myUserflg + ' data-categoryid=' + categoryGroup.dbCategoryId + ' data-sort=' + categoryGroup.categorySort + '><li>'
    + '<div class="row"><div class="col-9 col-sm-7 col-md-5 col-lg-6"><span>'
    + groupCategory
    + '<input type="text" maxlength=' + categoryTextMaxLength + ' id="' + categoryGroup.domNameId + '" name="categoryname" class="categoryname" value="' + categoryGroup.categoryName + '" data-categoryid=' + categoryGroup.dbCategoryId + ' readonly="readonly" onclick="taskGet(' + categoryGroup.dbTabId + ',' + categoryGroup.dbCategoryId + ')" autocomplete="off"></span>'
    + '</div><div class="d-none d-sm-block col-sm-4 col-md-4 col-lg-3 categorystatusarea">'
    + '<div id="' + categoryGroup.domCategoryNotstartedId + '" name="category_notstarted" class="statusbutton category_st button_notstarted" data-val=' + categoryGroup.categoryNotstarted + ' onclick="categoryButtonClick(\'notstarted\',\'' + categoryGroup.dbCategoryId + '\',\'' + categoryGroup.domCategoryAreaId + '\')">' + notstartedText + '</div>'
    + '<div id="' + categoryGroup.domCategoryWorkingId + '" name="category_working" class="statusbutton category_st button_working" data-val=' + categoryGroup.categoryWorking + ' onclick="categoryButtonClick(\'working\',\'' + categoryGroup.dbCategoryId + '\',\'' + categoryGroup.domCategoryAreaId + '\')">' + workingText + '</div>'
    + '<div id="' + categoryGroup.domCategoryWaitingId + '" name="category_waiting" class="statusbutton category_st button_waiting" data-val=' + categoryGroup.categoryWaiting + ' onclick="categoryButtonClick(\'waiting\',\'' + categoryGroup.dbCategoryId + '\',\'' + categoryGroup.domCategoryAreaId + '\')">' + waitingText + '</div>'
    + '<div id="' + categoryGroup.domCategoryDoneId + '" name="category_done" class="statusbutton category_st button_done" data-val=' + categoryGroup.categoryDone + ' onclick="categoryButtonClick(\'done\',\'' + categoryGroup.dbCategoryId + '\',\'' + categoryGroup.domCategoryAreaId + '\')">' + doneText + '</div></div>'
    + '<div class="d-none d-md-block col-md-2 col-lg-2"><input type="text" maxlength=' + dayTextMaxLength + ' id="' + categoryGroup.domCategoryDeadlineId + '" class="dispDayTextBox" value="' + categoryGroup.categoryDeadline + '" placeholder="期限なし" autocomplete="off"></div>'
    + '<div class="d-block d-sm-none col-2">' + categoryStatusSelectBox + '</div>'
    + '<div class="col-1"><div class="row"><div class="d-none d-md-block col-md-4 col-lg-6"></div><div class="col-sm-12 col-md-8 col-lg-6">'
    + ' <div class="categoryDelete addDeleteBotton" onclick="categoryDelete(' + categoryGroup.dbCategoryId + ',\'' + categoryGroup.domCategoryAreaId + '\',\'' + categoryGroup.domNameId + '\')">×</div></div></div></div></div>'
    + '<ul id="' + categoryGroup.domCategoryMainAreaId + '"class="inner"><div><ul id="' + categoryGroup.categoryTaskList + '"></ul></div>'
    + '<input type="text" maxlength=' + taskTextMaxLength + ' id="' + categoryGroup.domTaskAddTextId + '" class="taskTextarea" placeholder="テキストを入力しEnterキー押下で追加" data-categoryDeadline="' + categoryGroup.categoryDeadline + '" autocomplete="off"></ul></li></ul>');

  //各種色設定
  buttonColorSet("category");
  $("#" + categoryGroup.userNameIconId).css({ 'background': categoryGroup.userColor, 'color': categoryGroup.userTextColor, 'border': 'solid 1px ' + categoryGroup.userBorderColor });
  $("#" + categoryGroup.userNameIconId).css('background', categoryGroup.userColor);
  $("#" + categoryGroup.domNameId).css("background-color", categoryGroup.color);
  $("#" + categoryGroup.domNameId).css("color", categoryGroup.textColor);
  $("#" + categoryGroup.domRemarksId).css("background-color", categoryGroup.color);
  $("#" + categoryGroup.domTaskAddTextId).css("background-color", categoryGroup.color);
  $("#" + categoryGroup.domCategoryDeadlineId).css("color", categoryGroup.textColor);
  dateLimitColorChange(categoryGroup.domCategoryDeadlineId, categoryGroup.categoryDeadline, categoryGroup.color, categoryGroup.textColor);

  if (categoryGroup.categoryDeadline != "") {
    deadlineWordAdd(categoryGroup.domCategoryDeadlineId, categoryGroup.textColor);
  }

  //各種メソッド定義
  $("#" + categoryGroup.domCategoryDeadlineId).datepicker();
  deadlineTextChange("category", categoryGroup.dbCategoryId, categoryGroup.domCategoryDeadlineId, categoryGroup.color, categoryGroup.textColor);
  categoryNameChange(categoryGroup.domNameId);
  categoryTextAreaLongPush(800, categoryGroup.domCategoryAreaId, categoryGroup.domNameId);
  textAreaBlur(categoryGroup.domCategoryAreaId, categoryGroup.domNameId, categoryGroup.color);
  textAreaEnter(categoryGroup.domCategoryAreaId, categoryGroup.domNameId, categoryGroup.color);
  statusSelectBoxSet(categoryGroup.domCategoryStatusSelectBoxId, categoryGroup.categoryNotstarted, categoryGroup.categoryWorking, categoryGroup.categoryWaiting, categoryGroup.categoryDone);
  statusSelectBoxChange("category", categoryGroup.domCategoryStatusSelectBoxId, categoryGroup.domCategoryAreaId, categoryGroup.dbCategoryId);
  taskTextEnterPress(categoryGroup.groupId, categoryGroup.dbTabId, categoryGroup.dbCategoryId, categoryGroup.domTaskAddTextId,
    categoryGroup.color, categoryGroup.textColor, categoryGroup.borderColor);

  //並び順変更用処理
  $('#' + categoryGroup.tabCategoryList).sortable({
    cursor: "move",
    opacity: 0.7,
    placeholder: "ph1",
    handle: "[name='icon']",
    update: function (event, ui) {
      //挿入対象レコードの情報
      var currentCategoryDomId = ui.item[0].id;
      var currentCategoryDbId = $("#" + currentCategoryDomId).attr("data-categoryid");
      //挿入対象レコードの挿入後、前にあるレコードの情報
      var previousCategoryDomId = $("#" + currentCategoryDomId).prev();
      //挿入対象レコードの挿入後、後ろにあるレコードの情報
      var nextCategoryDomId = $("#" + currentCategoryDomId).next();

      //挿入対象レコードに設定するソート番号（初期化処理）
      var currentCategorySort = 0;

      //最先頭の場合
      if (typeof previousCategoryDomId[0] === 'undefined') {
        //最先頭のソート番号を取得し-1000する
        currentCategorySort = Number($("#" + nextCategoryDomId[0].id).attr("data-sort")) - 1000;

        //最後尾の場合
      } else if (typeof nextCategoryDomId[0] === 'undefined') {
        //最後尾のソート番号を取得し+1000する
        currentCategorySort = Number($("#" + previousCategoryDomId[0].id).attr("data-sort")) + 1000;

        //それ以外（真ん中の場合）
      } else {
        //挿入先の前と後ろのソート番号を取得
        var nextCategoryDomSort = $("#" + nextCategoryDomId[0].id).attr("data-sort");
        var previousCategorySort = $("#" + previousCategoryDomId[0].id).attr("data-sort");

        //挿入先の前と後ろのソート番号が同じだった場合、前の方のソート番号に+1する
        if (Number(nextCategoryDomSort) == Number(previousCategorySort)) {
          currentCategorySort = Number(previousCategorySort) + 1;
          //違う場合以下の処理を行う
        } else {
          //挿入先の前と後ろのソート番号の差分を求める
          var sortDiff = Number(nextCategoryDomSort) - Number(previousCategorySort);
          //求めた値を2で割る
          var middle = Math.floor(sortDiff / 2);
          //その値を、前のソート番号と足し算し、挿入するレコードのソート番号に設定する
          currentCategorySort = Number(previousCategorySort) + middle;
        }
      }

      //画面に反映
      $("#" + currentCategoryDomId).attr("data-sort", currentCategorySort);

      //サーバ側に反映
      var data = { 'categoryId': currentCategoryDbId, 'categorySort': currentCategorySort };
      ajaxPost(CATEGORYSORT, data);
    }
  });
};

//カテゴリクリック時のタスク表示操作（アコーディオン）
function accordion(id) {
  $("#" + id).slideToggle();
};

//カテゴリ追加テキストエリアのテキストクリア
function categoryTextDelete(categoryid) {
  $("#" + categoryid).val("");
};

//カテゴリ名変更
function categoryNameChange(domNameId) {
  $('#' + domNameId).change(function () {
    var categoryname = $('#' + domNameId).val();
    if (requiredCheck(categoryname)) {
      MiniLoadOn();
      var categoryid = $('#' + domNameId).attr('data-categoryid');
      var data = { 'categoryId': categoryid, 'categoryName': categoryname };
      ajaxPost(CATEGORYUPDATE, data);
    } else {
      MessageDisp(WARNING, "カテゴリ名が無しのデータは登録できません", 5000);
    }
  });
};

//カテゴリ削除ボタン押下時の処理
function categoryDelete(categoryId, domCategoryAreaId, domNameId) {
  if (checkCategoryMyUserAdmin(domCategoryAreaId)) {
    var categoryName = $('#' + domNameId).val();
    var dispDataSet = { categoryId: categoryId, domCategoryAreaId: domCategoryAreaId, domNameId: domNameId, categoryName: categoryName };
    if (deleteCheck()) {
      var popupPosition = { my: 'right top', at: 'right bottom', of: '#' + domCategoryAreaId };
      deleteCheckDialog("削除", xssEscapeEncode(categoryName) + "を削除します", "category", dispDataSet, popupPosition, 'subButtonDelete');
    } else {
      categoryDeleteRun(dispDataSet);
    }
  }
  else {
    categoryNotMyUserAlert();
  }
};

function categoryDeleteRun(dispDataSet) {
  MiniLoadOn();
  var data = { 'categoryId': dispDataSet["categoryId"], 'categoryName': dispDataSet["categoryName"] };
  ajaxPost(CATEGORYDELETE, data);
  $("#" + dispDataSet["domCategoryAreaId"]).remove();
};

//期限日変更
function deadlineTextChange(type, dbId, domId, color, textColor) {
  $('#' + domId).change(function () {
    MiniLoadOn();
    $('#word' + domId).remove();
    var deadline = $('#' + domId).val();
    dateLimitColorChange(domId, deadline, color, textColor);
    switch (type) {
      case "category":
        var data = { 'categoryId': dbId, 'deadline': deadline };
        ajaxPost(CATEGORYDEADLINEUPDATE, data);
        break;
      case "task":
        var data = { 'taskId': dbId, 'deadline': deadline };
        ajaxPost(TASKDEADLINEUPDATE, data);
        break;
      default:
    }
    if (deadline != "") {
      deadlineWordAdd(domId, textColor);
    }
  });
};

//期限が入っている場合テキスト追加
function deadlineWordAdd(domCategoryDeadlineId, textColor) {
  $('#' + domCategoryDeadlineId).after('<span id="word' + domCategoryDeadlineId + '" style="color:' + textColor + ';">まで</span>')
};

/*=================================
タスク関連処理
=================================*/
//タスク追加作成
function taskCreate(groupId, dbTabId, categoryId, domTaskAddTextId, color, textColor, borderColor) {
  MiniLoadOn();
  if (autoDeadlineCheck()) {
    var taskDeadline = $('#' + domTaskAddTextId).attr('data-categoryDeadline');
  } else {
    var taskDeadline = "";
  }
  var tackName = $("#" + domTaskAddTextId).val();
  var data = { 'groupId': groupId, 'tabId': dbTabId, 'categoryId': categoryId, 'taskName': tackName, 'taskDeadline': taskDeadline, 'color': color, 'textColor': textColor, 'borderColor': borderColor };
  ajaxPost(TASKADD, data);
  taskTextDelete(domTaskAddTextId);
};

function taskGetInfo(dbTaskId) {
  var data = { 'taskId': dbTaskId };
  MiniLoadOn();
  ajaxPost(TASKINFOGET, data);
};

//タスク表示（新規作成時、既存データ表示時共通）
function taskDisp(taskGroup) {

  //カテゴリ押下時に判定する、タスクをすでにサーバから取得しているかのフラグを立てる
  $("#" + taskGroup.domCategoryAreaId).attr("data-task", taskGroup.categoryTaskFlg);

  //ステータスセレクトボックス定義（スマホ用機能）
  var taskStatusSelectBox = statusSelectBoxInit(taskGroup.domTaskStatusSelectBoxId);

  //タスク表示
  $("#" + taskGroup.categoryTaskList).append('<ul id="' + taskGroup.domTaskAreaId + '" class="category" data-taskid=' + taskGroup.dbTaskId + ' data-sort=' + taskGroup.taskSort + '><li>'
    + '<div class="row"><div class="col-9 col-sm-7 col-md-5 col-lg-6">'
    + '<div class="taskIcon" name="icon" onclick="taskGetInfo(' + taskGroup.dbTaskId + ')"></div>'
    + '<input type="text" maxlength=' + taskTextMaxLength + ' id="' + taskGroup.domTaskNameId + '" name="taskname" class="taskname" value="' + taskGroup.taskName + '" data-taskid=' + taskGroup.dbTaskId + ' autocomplete="off">'
    + '</div><div class="d-none d-sm-block col-sm-4 col-md-4 col-lg-3 categorystatusarea">'
    + '<div id="' + taskGroup.domTaskNotstartedId + '" name="task_notstarted" class="statusbutton task_st button_notstarted" data-val=' + taskGroup.taskNotstarted + ' onclick="taskButtonClick(\'notstarted\',\'' + taskGroup.dbTaskId + '\',\'' + taskGroup.domTaskAreaId + '\')">' + notstartedText + '</div>'
    + '<div id="' + taskGroup.domTaskWorkingId + '" name="task_working" class="statusbutton task_st button_working" data-val=' + taskGroup.taskWorking + ' onclick="taskButtonClick(\'working\',\'' + taskGroup.dbTaskId + '\',\'' + taskGroup.domTaskAreaId + '\')">' + workingText + '</div>'
    + '<div id="' + taskGroup.domTaskWaitingId + '" name="task_waiting" class="statusbutton task_st button_waiting" data-val=' + taskGroup.taskWaiting + ' onclick="taskButtonClick(\'waiting\',\'' + taskGroup.dbTaskId + '\',\'' + taskGroup.domTaskAreaId + '\')">' + waitingText + '</div>'
    + '<div id="' + taskGroup.domTaskDoneId + '" name="task_done" class="statusbutton task_st button_done" data-val=' + taskGroup.taskDone + ' onclick="taskButtonClick(\'done\',\'' + taskGroup.dbTaskId + '\',\'' + taskGroup.domTaskAreaId + '\')">' + doneText + '</div>'
    + '</div>'
    + '<div class="d-none d-md-block col-md-2 col-lg-2"><input type="text" maxlength=' + dayTextMaxLength + ' id="' + taskGroup.domtaskDeadlineId + '" class="dispDayTextBox" value="' + taskGroup.taskDeadline + '" placeholder="期限なし" autocomplete="off"></div>'
    + '<div class="d-block d-sm-none col-2">' + taskStatusSelectBox + '</div>'
    + '<div class="col-1"><div class="row"><div class="d-none d-md-block col-md-4 col-lg-6"></div><div class="col-sm-12 col-md-8 col-lg-6">'
    + '<div class="taskDelete addDeleteBotton" onclick="taskDelete(' + taskGroup.dbTaskId + ',\'' + taskGroup.domTaskAreaId + '\',\'' + taskGroup.domTaskNameId + '\')">×</div> '
    + '</div></div></div></div>'
    + '</li></ul></ul>');

  if (taskGroup.taskDeadline != "") {
    deadlineWordAdd(taskGroup.domtaskDeadlineId, taskGroup.textColor);
  }

  //各種色設定
  buttonColorSet("task");
  $("#" + taskGroup.domTaskNameId).css("background-color", taskGroup.color);
  $("#" + taskGroup.domTaskNameId).css("color", taskGroup.textColor);
  $("#" + taskGroup.domTaskRemarksId).css("background-color", taskGroup.color);
  // $("#" + taskGroup.domtaskDeadlineId).css("background-color", taskGroup.color);
  $("#" + taskGroup.domtaskDeadlineId).css("color", taskGroup.textColor);
  dateLimitColorChange(taskGroup.domtaskDeadlineId, taskGroup.taskDeadline, taskGroup.color, taskGroup.textColor);

  //各種メソッド定義
  taskNameChange(taskGroup.domTaskNameId);
  $("#" + taskGroup.domtaskDeadlineId).datepicker();
  deadlineTextChange("task", taskGroup.dbTaskId, taskGroup.domtaskDeadlineId, taskGroup.color, taskGroup.textColor);
  statusSelectBoxSet(taskGroup.domTaskStatusSelectBoxId, taskGroup.taskNotstarted, taskGroup.taskWorking, taskGroup.taskWaiting, taskGroup.taskDone);
  statusSelectBoxChange("task", taskGroup.domTaskStatusSelectBoxId, taskGroup.domTaskAreaId, taskGroup.dbTaskId);

  //並び変え用処理
  $('#' + taskGroup.categoryTaskList).sortable({
    cursor: "move",
    opacity: 0.7,
    placeholder: "ph1",
    handle: "[name='icon']",
    update: function (event, ui) {
      //挿入対象レコードの情報
      var currentTaskDomId = ui.item[0].id;
      var currentTaskDbId = $("#" + currentTaskDomId).attr("data-taskid");
      //挿入対象レコードの挿入後、前にあるレコードの情報
      var previousTaskDomId = $("#" + currentTaskDomId).prev();
      //挿入対象レコードの挿入後、後ろにあるレコードの情報
      var nextTaskDomId = $("#" + currentTaskDomId).next();

      //挿入対象レコードに設定するソート番号（初期化処理）
      var currentTaskSort = 0;

      //最先頭の場合
      if (typeof previousTaskDomId[0] === 'undefined') {
        //最先頭のソート番号を取得し-1000する
        currentTaskSort = Number($("#" + nextTaskDomId[0].id).attr("data-sort")) - 1000;

        //最後尾の場合
      } else if (typeof nextTaskDomId[0] === 'undefined') {
        //最後尾のソート番号を取得し+1000する
        currentTaskSort = Number($("#" + previousTaskDomId[0].id).attr("data-sort")) + 1000;

        //それ以外（真ん中の場合）
      } else {
        //挿入先の前と後ろのソート番号を取得
        var nextTaskDomSort = $("#" + nextTaskDomId[0].id).attr("data-sort");
        var previousTaskSort = $("#" + previousTaskDomId[0].id).attr("data-sort");

        //挿入先の前と後ろのソート番号が同じだった場合、前の方のソート番号に+1する
        if (Number(nextTaskDomSort) == Number(previousTaskSort)) {
          currentTaskSort = Number(previousTaskSort) + 1;
          //違う場合以下の処理を行う
        } else {
          //挿入先の前と後ろのソート番号の差分を求める
          var sortDiff = Number(nextTaskDomSort) - Number(previousTaskSort);
          //求めた値を2で割る
          var middle = Math.floor(sortDiff / 2);
          //その値を、前のソート番号と足し算し、挿入するレコードのソート番号に設定する
          currentTaskSort = Number(previousTaskSort) + middle;
        }
      }

      //画面に反映
      $("#" + currentTaskDomId).attr("data-sort", currentTaskSort);

      //サーバ側に反映
      var data = { 'taskId': currentTaskDbId, 'taskSort': currentTaskSort };
      ajaxPost(TASKSORT, data);
    }
  });

};

//タスク追加テキストエリアのテキストクリア
function taskTextDelete(taskid) {
  $("#" + taskid).val("");
};

//タスク取得
function taskGet(tabId, categoryId) {
  var domCategoryAreaId = "categoryarea" + categoryId;
  var edit = $('#' + domCategoryAreaId).attr('data-edit');

  if (checkCategoryMyUserAdmin(domCategoryAreaId)) {
    if (edit == 0) {
      var categoryTaskFlg = $('#' + domCategoryAreaId).attr('data-task');
      if (categoryTaskFlg == 0) {
        MiniLoadOn();
        var data = { 'tabId': tabId, 'categoryId': categoryId };
        ajaxPost(TASKGET, data);
      } else {
        accordion("categorymainarea" + categoryId);
      }
    }
  }
  else {
    categoryNotMyUserAlert();
  }

};

//タスク名変更
function taskNameChange(domNameId) {
  $('#' + domNameId).change(function () {
    var taskname = $('#' + domNameId).val();
    if (requiredCheck(taskname)) {
      MiniLoadOn();
      var taskid = $('#' + domNameId).attr('data-taskid');
      var data = { 'taskId': taskid, 'taskName': taskname };
      ajaxPost(TASKUPDATE, data);
    } else {
      MessageDisp(WARNING, "タスク名が無しのデータは登録できません", 5000);
    }
  });
};


//タスク削除ボタン押下時の処理
function taskDelete(taskId, domTaskAreaId, domTaskNameId) {
  var taskName = $("#" + domTaskNameId).val();
  var dispDataSet = { taskId: taskId, domTaskAreaId: domTaskAreaId, domTaskNameId: domTaskNameId, taskName: taskName };
  if (deleteCheck()) {
    var popupPosition = { my: 'right top', at: 'right bottom', of: '#' + domTaskAreaId };
    deleteCheckDialog("削除", xssEscapeEncode(taskName) + "を削除します", "task", dispDataSet, popupPosition, 'subButtonDelete');
  } else {
    taskDeleteRun(dispDataSet)
  }
};

function taskDeleteRun(dispDataSet) {
  MiniLoadOn();
  var data = { 'taskId': dispDataSet["taskId"], 'taskName': dispDataSet["taskName"] };
  ajaxPost(TASKDELETE, data);
  $("#" + dispDataSet["domTaskAreaId"]).remove();
};


/*=================================
ステータスボタン制御関連処理
=================================*/
//カテゴリステータスボタンクリック時の処理（ステータスセレクトボックス変更時の処理にも使用）
function categoryButtonClick(status, dbCategoryId, domCategoryAreaId) {
  if (checkCategoryMyUserNotAdmin(domCategoryAreaId)) {
    MiniLoadOn();
    //基本データ定義
    var data = {
      'categoryId': dbCategoryId,
      'domCategoryAreaId': domCategoryAreaId,
      'notstarted': 0,
      'working': 0,
      'waiting': 0,
      'done': 0,
      'archiveFlg': 0
    };

    //ステータスごとにデータ変更し、サーバへ更新
    switch (status) {
      case "notstarted":
        data['notstarted'] = 1;
        ajaxPost(CATEGORYSTATUSUPDATE, data);
        break;
      case "working":
        data['working'] = 1;
        ajaxPost(CATEGORYSTATUSUPDATE, data);
        ajaxPost(CATEGORYSUSPENDED, data);
        break;
      case "waiting":
        data['waiting'] = 1;
        ajaxPost(CATEGORYSTATUSUPDATE, data);
        break;
      case "done":
        data['done'] = 1;
        data['archiveFlg'] = autoActiveCheck();
        ajaxPost(CATEGORYSTATUSUPDATE, data);
        break;
    }
  }
  else {
    categoryNotMyUserAlert();
  }

};

//タスクステータスボタンクリック時の処理
function taskButtonClick(status, dbTaskId, domTaskAreaId) {
  MiniLoadOn();
  //基本データ定義
  var data = {
    'taskId': dbTaskId,
    'domTaskAreaId': domTaskAreaId,
    'notstarted': 0,
    'working': 0,
    'waiting': 0,
    'done': 0,
    'archiveFlg': 0
  };

  //ステータスごとにデータ変更し、サーバへ更新
  switch (status) {
    case "notstarted":
      data['notstarted'] = 1;
      ajaxPost(TASKSTATUSUPDATE, data);
      break;
    case "working":
      data['working'] = 1;
      ajaxPost(TASKSTATUSUPDATE, data);
      ajaxPost(TASKSUSPENDED, data);
      break;
    case "waiting":
      data['waiting'] = 1;
      ajaxPost(TASKSTATUSUPDATE, data);
      break;
    case "done":
      data['done'] = 1;
      data['archiveFlg'] = autoActiveCheck();
      ajaxPost(TASKSTATUSUPDATE, data);
      break;
  }

};

//サーバから受け取ったカテゴリのステータスをHTMLに適用する
function categoryStatusButtonUpdate(categoryId, domCategoryAreaId, notstarted, working, waiting, done, archiveFlg) {
  if (archiveFlg == 1) {
    $("#" + domCategoryAreaId).remove();
  } else {
    $("#categorynotstarted" + categoryId).attr("data-val", notstarted);
    $("#categoryworking" + categoryId).attr("data-val", working);
    $("#categorywaiting" + categoryId).attr("data-val", waiting);
    $("#categorydone" + categoryId).attr("data-val", done);

    buttonColorSet("category");

    var categoryStatusSelectBoxId = "categoryStatusSelectBox" + categoryId;
    statusSelectBoxSet(categoryStatusSelectBoxId, notstarted, working, waiting, done);
  }
};

//サーバから受け取ったタスクのステータスをHTMLに適用する
function taskStatusButtonUpdate(taskId, domTaskAreaId, notstarted, working, waiting, done, archiveFlg) {
  if (archiveFlg == 1) {
    $("#" + domTaskAreaId).remove();
  } else {
    $("#tasknotstarted" + taskId).attr("data-val", notstarted);
    $("#taskworking" + taskId).attr("data-val", working);
    $("#taskwaiting" + taskId).attr("data-val", waiting);
    $("#taskdone" + taskId).attr("data-val", done);

    buttonColorSet("task");

    var taskStatusSelectBoxId = "taskStatusSelectBox" + taskId;
    statusSelectBoxSet(taskStatusSelectBoxId, notstarted, working, waiting, done);
  }
};

//サーバから受け取ったカテゴリの中断中ステータスをHTMLに適用する
function categoryStatusButtonSuspended(categoryId, working) {
  $("#categoryworking" + categoryId).attr("data-val", working);
  buttonSuspendedSet("category");

  var selectBoxId = "categoryStatusSelectBox" + categoryId;
  statusSelectBoxSuspended(selectBoxId);
};

//サーバから受け取ったタスクの中断中ステータスをHTMLに適用する
function taskStatusButtonSuspended(taskId, working) {
  $("#taskworking" + taskId).attr("data-val", working);
  buttonSuspendedSet("task");

  var selectBoxId = "taskStatusSelectBox" + taskId;
  statusSelectBoxSuspended(selectBoxId);
};

//ステータスセレクトボックスの中断処理（スマホ用機能）
function statusSelectBoxSuspended(selectBoxId) {
  $("#" + selectBoxId).children('option[value=suspended]').remove();
  $("#" + selectBoxId).append('<option style="background:linear-gradient(#ff0505, #ec2a10)" value="suspended">中断</option>');
  $("#" + selectBoxId + ' option[value="suspended"]').prop('selected', true);
  $("#" + selectBoxId).css('background', "linear-gradient(#ff0505, #ec2a10)");
};

//カテゴリ、タスク共通ステータスボタンの色設定
function buttonColorSet(type) {
  $('div[data-val=0]').css("background", "#ccc");

  $('div[name="' + type + '_notstarted"]' + 'div[data-val=1]').css("background", "linear-gradient(#05FBFF, #78bcd3)");
  $('div[name="' + type + '_working"]' + 'div[data-val=1]').css("background", "linear-gradient(#ff9305, #dfaa79)");
  $('div[name="' + type + '_waiting"]' + 'div[data-val=1]').css("background", "linear-gradient(#05ff05, #5cda50)");
  $('div[name="' + type + '_done"]' + 'div[data-val=1]').css("background", "linear-gradient(#7105ff, #605385)");

  $('div[data-val=0]').css("color", "#FFF");
  $('div[data-val=0]').css("font-weight", "normal");

  $('div[data-val=1]').css("color", "#000");
  $('div[data-val=1]').css("font-weight", "bold");

  buttonSuspendedSet(type);
};

//カテゴリ、タスク共通作業中と中断中ステータスの設定
function buttonSuspendedSet(type) {
  $('div[name="' + type + '_working"]' + 'div[data-val=1]').text(workingText);
  $('div[name="' + type + '_working"]' + 'div[data-val=0]').text(workingText);

  $('div[name="' + type + '_working"]' + 'div[data-val=2]').css("background", "linear-gradient(#ff0505, #ec2a10)");
  $('div[name="' + type + '_working"]' + 'div[data-val=2]').text(suspendedText);

  $('div[data-val=2]').css("color", "#000");
  $('div[data-val=2]').css("font-weight", "bold");

};

//ステータスセレクトボックス定義（スマホ用機能）
function statusSelectBoxInit(selectBoxId) {
  var selectBox = '<select id="' + selectBoxId + '" class="statusSelectBox">'
    + '<option style="background:linear-gradient(#05FBFF, #78bcd3)" value="notstarted">未着</option>'
    + '<option style="background:linear-gradient(#ff9305, #dfaa79)" value="working">作業</option>'
    + '<option style="background:linear-gradient(#05ff05, #5cda50)" value="waiting">待機</option>'
    + '<option style="background:linear-gradient(#7105ff, #605385)" value="done">完了</option>'
    + '</select>';
  return selectBox;
};

//ステータスセレクトボックスの表示設定（スマホ用機能）
function statusSelectBoxSet(selectBoxId, notstarted, working, waiting, done) {
  var selectStatus;
  var color;

  $("#" + selectBoxId + ' option[value="suspended"]').remove();

  if (notstarted == 1) {
    selectStatus = "notstarted";
    color = "linear-gradient(#05FBFF, #78bcd3)";
  } else if (working == 1) {
    selectStatus = "working";
    color = "linear-gradient(#ff9305, #dfaa79)";
  } else if (working == 2) {
    selectStatus = "suspended";
    $("#" + selectBoxId).append('<option style="background:linear-gradient(#ff0505, #ec2a10)" value="suspended">中断</option>');
    color = "linear-gradient(#ff0505, #ec2a10)";
  } else if (waiting == 1) {
    selectStatus = "waiting";
    color = "linear-gradient(#05ff05, #5cda50)";
  } else if (done == 1) {
    selectStatus = "done";
    color = "linear-gradient(#7105ff, #605385)";
  } else {
    selectStatus = "notstarted";
    color = "linear-gradient(#05FBFF, #78bcd3)";
  }
  $('#' + selectBoxId + ' option[value="' + selectStatus + '"]').prop('selected', true);
  $("#" + selectBoxId).css('background', color);

};

//カテゴリとタスクの期限日が当日か、過ぎているか、明日以降かの色分け
function dateLimitColorChange(id, date, defaltColor, defaltTextColor) {
  if (date == "") {
    $("#" + id).css("background", defaltColor);
    $("#" + id).css("color", defaltTextColor);
    return;
  }

  var dayJudgment = dayComparisonCheck(date, NowDate());
  switch (dayJudgment) {
    case "rightDateWin":
      $("#" + id).css("background", 'linear-gradient(to top, #dd541e, #9b2a07)');
      $("#" + id).css("color", '#6d0000');
      break;
    case "same":
      $("#" + id).css("background", 'linear-gradient(to top, #c0dd1e, #919b07)');
      $("#" + id).css("color", '#626d00');
      break;
    case "leftDateWin":
      var tomorrowDay = DayCalculationDate(NowDate(), +1);
      var dayJudgment = dayComparisonCheck(date, tomorrowDay);
      if (dayJudgment == "same") {
        $("#" + id).css("background", 'linear-gradient(to top, #3edd1e, #1f9b07)');
        $("#" + id).css("color", '#006d3a');
      } else {
        console.log(defaltColor);
        console.log(id);
        $("#" + id).css("background", defaltColor);
        $("#" + id).css("color", defaltTextColor);
      }
      break;
  }

};
