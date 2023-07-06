
/*=================================
デバッグメニュー
=================================*/
//デモ用データ作成
function demo() {
  var result = confirm("デモ用のデータに差し替えます。");
  if (result) {
    LoadOn();
    ajaxPost("./demo", {});
  } else {
  }
};

//ユーザとグループ以外のデータ削除
function datadelete() {
  var result = confirm("ユーザとグループ以外のDB内の値をすべて消します。");
  if (result) {
    ajaxPost("./datadelete", {});
  } else {
  }
};

//全てのデータ削除
function alldelete() {
  var result = confirm("DB内の値をすべて消します。※デモ版は実行不可");
  if (result) {
  } else {
  }
};

//機械学習実行1
function machineLearning1() {
  var result = confirm("機械学習のバッチ処理を動かしてNoSQLを更新します。");
  if (result) {
    LoadOn();
    ajaxPost("./machinelearning1", {});
  } else {
  }
};

//機械学習実行2
function machineLearning2() {
  var result = confirm("機械学習のバッチ処理を動かしてNoSQLを更新します。");
  if (result) {
    LoadOn();
    ajaxPost("./machinelearning2", {});
  } else {
  }
};

//機械学習モデル削除
function machineLearningDelete() {
  var result = confirm("機械学習で使用するNoSQLデータを削除します");
  if (result) {
    LoadOn();
    ajaxPost("./machineLearningDelete", {});
  } else {
  }
};
