<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App;

use Dao;

use Utility;

class SystemInitControllers extends Controller
{
   //初回DB作成、システム情報登録処理
   public function databaseCreate(Request $request)
   {
      $reqdata = $request->all();

      //メッセージ
      $sacsses = 'データベースの作成が完了しました。<br>ログイン画面からログインしご利用頂けます。';
      $worning = 'トークンが有効期限切れです。';
      $errorTokenUpdate = 'トークンの更新処理に失敗しました。';
      $errorWorkSpaceAdd = 'ワークスペースの作成に失敗しました。';
      $errorLoginUpdate = 'ログイン情報の更新処理に失敗しました。';
      $errorDatabaseCreate = '専用データベースの作成に失敗しました。';
      $errorTable = '専用データベースの更新に失敗しました。';
      $errorInitDataAdd = '初回データ登録処理に失敗しました。';
      $errorMlDatabaseCreate = '専用機械学習データベースの作成に失敗しました。';
      $errorMlTable = '専用機械学習データベースの更新に失敗しました。';
      $errorDoneExe = '完了処理に失敗しました。';
      $requestReregistration = '<br>新規登録画面より再度登録処理をお願いします。';
      $supplement = '<br>本事象が頻発するようでしたら管理者までご連絡ください。';


      //各インスタンス生成
      $g_loginDao = new Dao\G_LoginDao;
      $g_companyDao = new Dao\G_CompanyDao;
      $dbConnect = new Dao\DbInfo;
      $createDao = new Dao\CreateDao;
      $dataFormat = new Utility\DataFormat;

      //トークン情報を元にDBのログイン情報を取得
      $loginInfo = $g_loginDao->getG_LoginTockenCheck($reqdata["token"]);
      if (empty($loginInfo)) {
         //トークンにマッチするレコードが存在しなければ終了
         $resdata = array('status' => 1, 'message' => $worning . $requestReregistration);
         return response()->json(['resdata' => $resdata]);
      }

      //トークン情報をクリア、フラグ変更
      $result = $g_loginDao->updateG_LoginToken(
         $loginInfo->email,
         $loginInfo->email_token
      );
      if ($result['code']) {
         //ログインテーブル更新失敗時、ロールバックし処理を終了
         $resdata = array('status' => 1, 'message' => $errorTokenUpdate . $requestReregistration . $supplement);
         $this->loginDelete($loginInfo->email);
         return response()->json(['resdata' => $resdata]);
      }

      //DBユーザとDBパスワードの文字列生成
      $userSetNameTmp = $dataFormat->randGet(8, "allAlphabet", "first");
      $userSetName = "U_" . $userSetNameTmp;
      $passwordTmp1 = $dataFormat->randGet(10, "all", "none");
      $passwordTmp2 = $dataFormat->randGet(1, "number", "none");
      $passwordTmp3 = $dataFormat->randGet(1, "symbol", "none");
      $userSetPassword = $passwordTmp1 . $passwordTmp2 . $passwordTmp3;

      //ワークスペース新規登録
      $result =  $g_companyDao->addG_Company($userSetName, $userSetPassword);
      //DBエラーチェック
      if ($result['code']) {
         //ワークスペースレコード登録失敗時、ロールバックし処理を終了
         $resdata = array('status' => 2, 'message' => $errorWorkSpaceAdd . $requestReregistration . $supplement);
         $this->loginDelete($loginInfo->email);
         return response()->json(['resdata' => $resdata]);
      }
      //登録したワークスペース情報を取得
      $companyId = $result["resultData"];
      $dbinfo = $g_companyDao->getG_CompanyBaseInfo($companyId);

      //登録したワークスペースの情報を基にログイン情報を更新
      $result = $g_loginDao->updateG_LoginCompanyId($loginInfo->email, $companyId);
      //DBエラーチェック
      if ($result['code']) {
         //ログイン情報更新失敗時、ロールバックし処理を終了
         $resdata = array('status' => 2, 'message' => $errorLoginUpdate . $requestReregistration . $supplement);
         $this->loginDelete($loginInfo->email);
         $this->companyDelete($companyId, $dbinfo->dbUser);
         return response()->json(['resdata' => $resdata]);
      }

      /*---------------------------<DB及びDBのユーザを作成>--------------------------------*/
      //rootユーザの接続コマンドを定義
      $masterDbInfo = \Config::get('database.connections.rootmysql');
      $connectCmd = "mysql -u " . $masterDbInfo['username'] . " -p'" . $masterDbInfo['password'] . "' -h " . $masterDbInfo['host'] . "";

      //実行するSQLコマンドを定義
      $passwordSet = "pass='" . $dbinfo->dbPassword . "';";
      $createDbCmd = "CREATE DATABASE " . $dbinfo->dbUser . " character set utf8 collate utf8_bin;";
      $createUserCmd = "CREATE USER " . $dbinfo->dbUser . "@'%' IDENTIFIED WITH mysql_native_password BY \"\\'\$pass\\'\";";
      $grantCmd = "GRANT ALL ON " . $dbinfo->dbUser . ".* TO " . $dbinfo->dbUser . "@'%';";
      $flushPrivilegesCmd = "FLUSH PRIVILEGES;";
      $sqlCmd = $createDbCmd . $createUserCmd . $grantCmd . $flushPrivilegesCmd;

      //シェルの実行コマンドを作成
      $exeCmd = $passwordSet . "echo \"" . $sqlCmd . "\"|" . $connectCmd;

      //コマンドを実行する
      exec($exeCmd, $output, $returnCode);

      //DBエラーチェック
      if ($returnCode) {
         //コマンドの実行失敗時、ロールバックし処理を終了
         $resdata = array('status' => 2, 'message' => $errorDatabaseCreate . $requestReregistration . $supplement);
         $this->loginDelete($loginInfo->email);
         $this->companyDelete($companyId, $dbinfo->dbUser);
         $this->dbDelete($connectCmd, $dbinfo->dbUser);
         return response()->json(['resdata' => $resdata]);
      }
      /*----------------------------------</>---------------------------------*/

      sleep(1);

      /*---------------------------<各種テーブルの作成処理>--------------------------------*/
      try {
         //各テーブルを作成する
         $dbConnect->dbConnectionSet($dbinfo->dbUser, $dbinfo->dbPassword, $dbinfo->dbHost);
         $createDao->createTable($dbinfo->dbUser);
      } catch (\Exception $e) {
         //テーブルの作成失敗時、ロールバックし処理を終了
         $resdata = array('status' => 2, 'message' => $errorTable . $requestReregistration . $supplement);
         $this->loginDelete($loginInfo->email);
         $this->companyDelete($companyId, $dbinfo->dbUser);
         $this->dbDelete($connectCmd, $dbinfo->dbUser);
         return response()->json(['resdata' => $resdata]);
      }
      /*---------------------------------</>----------------------------------*/

      sleep(1);

      //各テーブルに初回データを登録
      $result = $createDao->initDataInsert($dbinfo->dbUser);
      //DBエラーチェック
      if ($result['code']) {
         //テーブルの作成失敗時、ロールバックし処理を終了
         $resdata = array('status' => 2, 'message' => $errorInitDataAdd . $requestReregistration . $supplement);
         $this->loginDelete($loginInfo->email);
         $this->companyDelete($companyId, $dbinfo->dbUser);
         $this->dbDelete($connectCmd, $dbinfo->dbUser);
         return response()->json(['resdata' => $resdata]);
      }

      /*---------------------------<NOSQLDBを作成>--------------------------------*/
      //接続コマンドを定義
      $mlConnectCmd = "cqlsh " . $dbinfo->mlHost . "";

      //実行するSQLコマンドを定義
      $mlCreateDbCmd = "create keyspace " . $dbinfo->dbUser . " WITH REPLICATION = { 'class' : 'SimpleStrategy', 'replication_factor' : 1 };";

      //シェルの実行コマンドを作成
      $exeCmd = "echo \"" . $mlCreateDbCmd . "\"|" . $mlConnectCmd;

      //コマンドを実行する
      exec($exeCmd, $output, $returnCode);

      //DBエラーチェック
      if ($returnCode) {
         //コマンドの実行失敗時、ロールバックし処理を終了
         $resdata = array('status' => 2, 'message' => $errorMlDatabaseCreate . $requestReregistration . $supplement);
         $this->loginDelete($loginInfo->email);
         $this->companyDelete($companyId, $dbinfo->dbUser);
         $this->dbDelete($connectCmd, $dbinfo->dbUser);
         $this->mlDbDelete($mlConnectCmd, $dbinfo->dbUser);
         return response()->json(['resdata' => $resdata]);
      }
      /*----------------------------------</>---------------------------------*/

      /*---------------------------<NOSQLDBのテーブルを作成>--------------------------------*/
      //接続コマンドを定義
      $mlConnectCmd = "cqlsh " . $dbinfo->mlHost . "";

      //実行するSQLコマンドを定義
      $mlCreateDbCmd = $createDao->createMlTable($dbinfo->dbUser);

      //シェルの実行コマンドを作成
      $exeCmd = "echo \"" . $mlCreateDbCmd . "\"|" . $mlConnectCmd;

      //コマンドを実行する
      exec($exeCmd, $output, $returnCode);

      //DBエラーチェック
      if ($returnCode) {
         //コマンドの実行失敗時、ロールバックし処理を終了
         $resdata = array('status' => 2, 'message' => $errorMlTable . $requestReregistration . $supplement);
         $this->loginDelete($loginInfo->email);
         $this->companyDelete($companyId, $dbinfo->dbUser);
         $this->dbDelete($connectCmd, $dbinfo->dbUser);
         $this->mlDbDelete($mlConnectCmd, $dbinfo->dbUser);
         return response()->json(['resdata' => $resdata]);
      }
      /*----------------------------------</>---------------------------------*/


      //ログインとワークスペーステーブルのステータスを更新（完了処理）
      $result = $createDao->updateDoneCommit($loginInfo->email, $companyId, $dbinfo->dbUser);
      //DBエラーチェック
      if ($result['code']) {
         $resdata = array('status' => 2, 'message' => $errorDoneExe);
         $this->loginDelete($loginInfo->email);
         $this->companyDelete($companyId, $dbinfo->dbUser);
         $this->dbDelete($connectCmd, $dbinfo->dbUser);
         return response()->json(['resdata' => $resdata]);
      }

      $resdata = array('status' => 0, 'message' => $sacsses);
      return response()->json(['reqdata' => $reqdata, 'resdata' => $resdata]);
   }

   public function initDataAdd(Request $request)
   {
   }

   //DB及びDBのユーザを削除する（各処理のエラー時のロールバック処理）
   private function dbDelete($connectCmd, $dbUser)
   {
      //削除用コマンド
      $dropDbCmd = "DROP DATABASE " . $dbUser . ";";
      $dropUserCmd = "DROP USER " . $dbUser . "@'%';";

      $sqlCmd = $dropDbCmd . $dropUserCmd;
      $exeCmd = "echo \"" . $sqlCmd . "\"|" . $connectCmd;
      exec($exeCmd, $output, $return_var);
   }

   //NOSQLDBを削除する（各処理のエラー時のロールバック処理）
   private function mlDbDelete($mlConnectCmd, $dbUser)
   {
      //削除用コマンド
      $dropDbCmd = "DROP KEYSPACE " . $dbUser . ";";

      $exeCmd = "echo \"" . $dropDbCmd . "\"|" . $mlConnectCmd;
      exec($exeCmd, $output, $return_var);
   }

   //ワークスペーステーブルのレコードを削除する（各処理のエラー時のロールバック処理）
   private function companyDelete($companyId, $dbUser)
   {
      $createDao = new Dao\CreateDao;
      $result = $createDao->deleteG_CompanySystemInit($companyId, $dbUser);
   }

   //ログインテーブルのレコードを削除する（各処理のエラー時のロールバック処理）
   private function loginDelete($email)
   {
      $createDao = new Dao\CreateDao;
      $result = $createDao->deleteG_LoginSystemInit($email);
   }

   public function loginInit(Request $request)
   {
      $reqdata = $request->all();
      return view('auth.loginInit')->with(['email' => $reqdata["email"]]);
   }
}
