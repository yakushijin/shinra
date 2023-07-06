$(function () {
  $("#logo").click(function(){
    window.location.href = ADMINPATH;
  });
});

/*=================================
初期化処理（初回ページ遷移時に実行）
=================================*/
function init() {
  noticeDispChange();
  $("#modaldisp").load("html/modal.html");
  $("#canvasdisp").load("html/canvas.html");
};

function endInit() {
  $('[name="searchDay"]').datepicker();
  graphInit();
  wordcloudInit();

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

function userPage() {
  window.location.href = USERPAGE;
};


function managementDisp(modalTitel, tableColName, dataType, res) {

  $("#resultitle").empty();
  $("#searchResult").empty();

  $("#resultitle").append('<div id="userInfoDispTitel" >' + modalTitel + '</div>');

  $("#searchResult").append('<div id="modalTableArea" class="modalTable">');

  var columnDefs;

  switch (dataType) {
    case 'user':
      statusColumn = 2;

      $("#modalTableArea").append('<table id="resultTable" class="table table-bordered modalTableText">'
        + '<thead><tr><th>' + tableColName + '</th><th>権限</th><th>状態</th></tr></thead>'
        + '<tbody></tbody></table >');

      columnDefs =
        [
          { targets: [1], width: "40px" },
          { targets: [2], width: "30px" }
        ];

      res["userList"].forEach(function (value) {
        var userActive = "有効";
        if (value["activeFlg"] == "1") {
          userActive = "無効";
        }
        var userAuthority = "ユーザ";
        if (value["authority"] == "1") {
          userAuthority = "管理者";
        }
        if (value["userId"] == res["systemUserId"]) {
          userAuthority = "system";
        }

        $("#resultTable").append('<tr><td ><div id="' + value["userId"] + '" class="searchList" onclick="userinfoget(' + value["userId"] + ');">' + value["userName"] + '</div></td>'
          + '<td ><div>' + userAuthority + '</div></td>'
          + '<td ><div>' + userActive + '</div></td></tr>');

      });
      break;

    case 'group':

      $("#modalTableArea").append('<table id="resultTable" class="table table-bordered modalTableText">'
        + '<thead><tr><th>' + tableColName + '</th><th>状態</th></tr></thead>'
        + '<tbody></tbody></table >');

      columnDefs =
        [
          { targets: [1], width: "30px" }
        ];

      res.forEach(function (value) {
        var groupActive = "有効";
        if (value["activeFlg"] == "1") {
          groupActive = "無効";
        }
        $("#resultTable").append('<tr><td ><div id="' + value["groupId"] + '"  class="searchList" onclick="groupinfoget(' + value["groupId"] + ');">' + value["groupName"] + '</div></td>'
          + '<td ><div>' + groupActive + '</div></td></tr>');

      });
      break;

    case 'tab':
      statusColumn = 1;

      $("#modalTableArea").append('<table id="resultTable" class="table table-bordered modalTableText">'
        + '<thead><tr><th>' + tableColName + '</th><th>状態</th></tr></thead>'
        + '<tbody></tbody></table >');

      columnDefs =
        [
          { targets: [1], width: "30px" }
        ];

      res.forEach(function (value) {
        var archiveFlg = "有効";
        if (value["archiveFlg"] == "1") {
          archiveFlg = "無効";
        }
        if(value["tabName"] == ""){
          value["tabName"] = "（タブ名無し）"
        }
        $("#resultTable").append('<tr><td ><div id="' + value["tabId"] + '"  class="searchList" onclick="tabinfoget(' + value["tabId"] + ');">' + value["tabName"] + '</div></td>'
          + '<td ><div>' + archiveFlg + '</div></td></tr>');


      });
      break;
  }


  $.extend($.fn.dataTable.defaults, {
    language: { url: 'lib/res/Japanese.json' }
  });

  $("#resultTable").DataTable({
    columnDefs: columnDefs,
    lengthMenu: [ [10, 20, 30], [10, 20, 30]]
  });

  var wordCloudAreaDisp = $("#wordcloudarea").attr("data-disp");
  if (wordCloudAreaDisp == 0) {
    $('#graphdisparea').css('height','auto');
    
    $('#searchResult').css('height','400px');
    $('#wordCloud').insertAfter('#wordcloudarea');
    $('#wordcloudTitel').insertAfter('#wordcloudarea');
    $("#wordcloudarea").attr("data-disp", 1);
  }



};

//フリーワード検索
function freeWordSearch() {
  var freeWord = $("#freeWord").val();
	var postDataCheck = 1;
	if (!requiredCheck(freeWord)) {
		var freeWordCheck = 1;
		postDataCheck = 0;
		$('#freeWordValidationMessage').append("<div>テキストを入力してください</div>");
  }
  
  if (postDataCheck) {
    var wordTarget = $('input[name="wordTarget"]:checked').val();
    var data = { 'freeWord': freeWord, 'wordTarget': wordTarget };
    LoadOn();
    ajaxPost(GETFREEWORDTOID, data);
	} else {
		textBoxColorChange('freeWord', freeWordCheck);
	}



};



function graphInit() {


  var performanceDayTo = NowDate();

  var performanceDayFromValue = DayCalculationDate(performanceDayTo, -10);

  $("#performanceDayFrom").val(performanceDayFromValue);
  $("#performanceDayTo").val(performanceDayTo);
  performanceSearch();
};

function graphDisp(req, res) {
  $("#graphtitle").empty();
  $("#graphResult").empty();

  var titelTarget;
  switch (req["target"]) {
    case "targetTab":
      titelTarget = "タブ";
      break;
    case "targetGroup":
      titelTarget = "グループ";
      break;

    case "targetAll":
      titelTarget = "全体";
      break;
  }

  var titelName = titelTarget + ':' + req["performanceDayFrom"] + '～' + req["performanceDayTo"];


  $("#graphtitle").append('<div id="userInfoDispTitel" >' + titelName + '</div>');
  $("#graphResult").append('<div id="graphArea" class="graphArea"></div>');
  $("#graphArea").append('<canvas id="graph"></canvas>');
  $("#graphArea").css('background', 'linear-gradient(to bottom, rgb(66, 68, 92), rgb(57, 58, 119))');

  //グラフに表示する日付（横軸）
  var performanceDayArray = [];
  res["performanceDay"].forEach(function (value) {
    performanceDayArray.push(value.performanceDay);
  });


  var barmem = false;

  //グラフに表示するデータ
  var datasetsArray = [];
  var datasetsTemp = {};
  switch (req["target"]) {
    case "targetTab":
      var type = "line";
      res["tab"].forEach(function (value) {
        datasetsTemp = {};
        var percentageArray = [];
        value.target.forEach(function (targetvalue) {
          percentageArray.push(targetvalue.percentage);
        });
        datasetsTemp["yAxisID"] = 'percentage';
        datasetsTemp["label"] = value.tabName;
        datasetsTemp["borderColor"] = value.color;
        datasetsTemp["fill"] = false;
        datasetsTemp["data"] = percentageArray;
        datasetsTemp["pointRadius"] = 0;
        datasetsArray.push(datasetsTemp);
      });
      break;

    case "targetGroup":
      var type = "line";
      res["group"].forEach(function (value) {
        datasetsTemp = {};
        var percentageArray = [];
        value.target.forEach(function (targetvalue) {
          percentageArray.push(targetvalue.percentage);
        });
        datasetsTemp["yAxisID"] = 'percentage';
        datasetsTemp["label"] = value.groupName;
        datasetsTemp["borderColor"] = value.color;
        datasetsTemp["fill"] = false;
        datasetsTemp["data"] = percentageArray;
        datasetsTemp["pointRadius"] = 0;
        datasetsArray.push(datasetsTemp);
      });
      break;
    case "targetAll":
      var type = "bar";
      var barmem = true;

      datasetsTemp = {};
      datasetsTemp2 = {};
      datasetsTemp3 = {};
      var percentageArray = [];
      var notDoneCountArray = [];
      var doneCountArray = [];
      res["all"].forEach(function (value) {
        percentageArray.push(value.percentage);
      });
      datasetsTemp["type"] = 'line';
      datasetsTemp["yAxisID"] = 'percentage';
      datasetsTemp["label"] = "進捗率";
      datasetsTemp["borderColor"] = "#6379c2";
      datasetsTemp["fill"] = false;
      datasetsTemp["data"] = percentageArray;
      datasetsTemp["pointRadius"] = 0;
      datasetsArray.push(datasetsTemp);

      res["all"].forEach(function (value) {
        notDoneCountArray.push(value.notDoneCount);
      });
      datasetsTemp2["type"] = 'bar';
      datasetsTemp2["label"] = "未完了数";
      datasetsTemp2["backgroundColor"] = "#bacce8";
      datasetsTemp2["fill"] = false;
      datasetsTemp2["data"] = notDoneCountArray;
      datasetsArray.push(datasetsTemp2);

      res["all"].forEach(function (value) {
        doneCountArray.push(value.doneCount);
      });
      datasetsTemp3["type"] = 'bar';
      datasetsTemp3["label"] = "完了数";
      datasetsTemp3["backgroundColor"] = "#00004d";
      datasetsTemp3["fill"] = false;
      datasetsTemp3["data"] = doneCountArray;
      datasetsArray.push(datasetsTemp3);
      break;
  }

  //壁画処理
  var ctx = document.getElementById("graph");
  var chart = new Chart(ctx, {
    type: type,

    //成形したデータを設定
    data: {
      labels: performanceDayArray,
      datasets: datasetsArray
    },

    // 設定はoptionsに記述
    options: {
      //タイトル

      scales: {
        xAxes: [{
					ticks: {
						fontColor:"#FFFFFF"
					}
				}],
        yAxes: [{
          scaleLabel: {
            fontColor: "#FFFFFF"
          },
          gridLines: {
            color: "rgba(126, 126, 126, 0.4)",
            zeroLineColor: "#FFFFFF"
          },
          ticks: {
            fontColor: "#FFFFFF",
            beginAtZero: true,
            display: barmem
          }
        },
        {
          id: "percentage",
          position: "right",
          autoSkip: true,
          gridLines: {
            display: false
          },
          ticks: {
            fontColor: "#FFFFFF",
            beginAtZero: true,
            max: 100,
            stepSize: 20,
            callback: function (val) {
              return val + '%';
            }
          }
        }]
      }
    }

  });

};



function userSearch() {
  var userName = $("#userName").val();
  var userActive = Number($('#userActive').prop('checked'));
  var userAuthority = Number($('#userAuthority').prop('checked'));

  var data = { 'userName': userName, 'userActive': userActive, 'userAuthority': userAuthority };
  LoadOn();
  ajaxPost(USERSEARCH, data);
};

function groupSearch() {
  var groupName = $("#groupName").val();
  var groupActive = Number($('#groupActive').prop('checked'));

  var data = { 'groupName': groupName, 'groupActive': groupActive };
  LoadOn();
  ajaxPost(GROUPSEARCH, data);
};

function tabSearch() {
  var tabName = $("#tabName").val();
  var tabActive = Number($('#tabActive').prop('checked'));
  var tabDeadline = $("#tabDeadline").val();

  var data = { 'tabName': tabName, 'tabActive': tabActive, 'tabDeadline': tabDeadline };
  LoadOn();
  ajaxPost(TABSEARCH, data);
};

function performanceSearch() {
  var interval = $('input[name="interval"]:checked').val();
  if (interval == "intervalDay") {
    var postDataCheck = dateCheckWrap('graphvalidationMessage', 'performanceDayTo', 'performanceDayFrom', graphDayRange, "day");

  } else if (interval == "intervalMonth") {
    var postDataCheck = dateCheckWrap('graphvalidationMessage', 'performanceDayTo', 'performanceDayFrom', graphMonthRange, "month");

  }

  if (postDataCheck) {
    var performanceDayFrom = $("#performanceDayFrom").val();
    var performanceDayTo = $("#performanceDayTo").val();
    var target = $('input[name="target"]:checked').val();
    var interval = $('input[name="interval"]:checked').val();

    var data = { 'performanceDayFrom': performanceDayFrom, 'performanceDayTo': performanceDayTo, 'target': target, 'interval': interval };
    LoadOn();
    ajaxPost(PERFORMANCESEARCH, data);
  }
};


function wordcloudInit() {
  var data = {};
  ajaxPost(GETALLNEWWORD, data);
};

function wordCloudDisp(req, res) {
  $("#wordcloudInitarea").append('<div id="wordcloudTitel" class="subTitelArea">・最近のキーワード</div>');
  $("#wordcloudInitarea").append('<div id="wordCloud" class="wordCloud"><div id="wordCloudDisp" class="wordCloudDisp"></div></div>');
  $("#wordCloudDisp").append('<div id="wordCloudInner1" class="wordCloudInner1"></div>');
  $("#wordCloudDisp").append('<div id="wordCloudInner2" class="wordCloudInner2"></div>');
  $("#wordCloudDisp").append('<div id="wordCloudInner3" class="wordCloudInner3"></div>');

  var wordCount = Object.keys(res).length;

  var wordArea_2_3 = wordCount / 3 * 2;
  var wordArea_1_3 = wordCount / 3;

  var wordAreaWidth1 = 0;
  var wordAreaWidth2 = 0;
  var wordAreaWidth3 = 0;

  res.forEach(function (value, key) {

    if (key > wordArea_2_3) {
      $('#wordCloudInner1').append("<span id='word" + key + "' class='wordCloudText'>" + value.word + "</span>");
      wordSizeSet(key, value.score);
      wordAreaWidth1 = wordAreaWidth1 + $('#word' + key).width() + 30;
    } else if (key > wordArea_1_3) {
      $('#wordCloudInner2').append("<span id='word" + key + "' class='wordCloudText'>" + value.word + "</span>");
      wordSizeSet(key, value.score);
      wordAreaWidth2 = wordAreaWidth2 + $('#word' + key).width() + 30;
    } else {
      $('#wordCloudInner3').append("<span id='word" + key + "' class='wordCloudText'>" + value.word + "</span>");
      wordSizeSet(key, value.score);
      wordAreaWidth3 = wordAreaWidth3 + $('#word' + key).width() + 30;
    }

  });
  $('#wordCloudInner1').css('width', wordAreaWidth1 + 'px');
  $('#wordCloudInner2').css('width', wordAreaWidth2 + 'px');
  $('#wordCloudInner3').css('width', wordAreaWidth3 + 'px');

};

function wordSizeSet(key, score) {

  if (score > 2 && score <= 4) {
    $('#word' + key).css('color', 'rgb(202, 224, 209))');
    $('#word' + key).css('background', 'linear-gradient(to bottom, rgb(132, 173, 132), rgb(41, 153, 41))');
    wordSize = 15;
  } else if (score > 4 && score <= 6) {
    $('#word' + key).css('color', 'rgb(221, 224, 202)');
    $('#word' + key).css('background', 'linear-gradient(to bottom, rgb(162, 173, 132), rgb(151, 153, 41))');
    wordSize = 20;
  } else if (score >= 7) {
    $('#word' + key).css('font-weight', 'bold');
    $('#word' + key).css('color', 'gb(27, 12, 4)');
    $('#word' + key).css('background', 'linear-gradient(to bottom, rgb(173, 140, 132), rgb(153, 63, 41))');
    wordSize = 25;
  }else{
    wordSize = 10;
  }

  $('#word' + key).css('font-size', wordSize + 'px');
};
