
$(function () {
  $("#logo").click(function(){
    window.location.href = TOPPATH;
  });
});

function databaseCreate() {
  LoadOn();
  $('#messageInfo').append('<div id="databaseCreateRun" class="baseMessage">データベースを作成中・・・<br>10～20秒程かかります。ブラウザを閉じないでください。</div>');
  $('#resultArea').append('<progress id="progressbar" max="100" value="0"></progress>');
  $('#resultMassage').empty();
  $('#databaseCreateId').remove();
  $('#resultdisp').remove();

  var email = $('#email').val();
  var token = $('#token').val();
  var data = { 'email': email, 'token': token };

  ajaxPost(DATABASECREATE, data);
  progress();

};

function progress() {
  val = $('#progressbar').val();
  if (val < 100) {
    $('#progressbar').val(val + 1);
    setTimeout(progress, 100);
  }
};

function initDataAdd() {

};

//サーバメッセージトップ画面用
function systemCreateResultDisp(req, res) {
  $('#progressbar').val(100);
  $('#databaseCreateRun').remove();
  $('#progressbar').remove();
  $('#messageInfo').append('<div id="databaseCreateRun" class="baseMessage">' + res.message + '</div>');
  if (res.status == 0) {
    $('#resultArea').append('<div id="resultdisp" class="resultdisp">成功！</div>');
    $('#resultdisp').css('background', 'linear-gradient(to top, #3edd1e, #1f9b07)');
    $('#buttonarea').append('<button type="submit" id="loginDisp" class="topButton">ログイン画面へ</button>');
  } else if (res.status == 1) {
    $('#resultArea').append('<div id="resultdisp" class="resultdisp">中止</div>');
    $('#resultdisp').css('background', 'linear-gradient(to top, #c0dd1e, #919b07)');

  } else if (res.status == 2) {
    $('#resultArea').append('<div id="resultdisp" class="resultdisp">失敗</div>');
    $('#resultdisp').css('background', 'linear-gradient(to top, #dd541e, #9b2a07)');

  } else {
    $('#resultArea').append('<div id="resultdisp" class="resultdisp">失敗</div>');
    $('#resultdisp').css('background', 'linear-gradient(to top, #dd541e, #9b2a07)');

  }

};
