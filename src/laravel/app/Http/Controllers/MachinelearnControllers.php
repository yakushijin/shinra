<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Dao;

class MachinelearnControllers extends BaseControllers
{
   //キーワードや文章から関連するユーザを取得する
   public function getWordToUser(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      //使用データ設定
      $reqdata = $s_data->reqdata;

      $socketNet = array('host' => $this->getPythonServerInfo(), 'port' => '8001', 'timeout' => 30, 'byteSize' => 8096);
      $socketData = array(
         'apiId' => '300',
         'dbUser' => mb_strtolower($s_data->dbUser, 'UTF-8'),
         'dbHost' => $s_data->dbHost,
         'mlHost' => $s_data->mlHost,
         'textData' => $reqdata['text'],
         'targetTable' => 'userWordSet',
         'targetColumn' => 'userId'
      );


      $socketResult = $this->socket($socketNet, $socketData);

      $wordToUserData = json_decode($socketResult);

      $target = $reqdata['target'];
      switch ($target) {
         case "wordToUserTab":
            //現在のタブ内にいるユーザを取得
            $t_tabDao = new Dao\T_TabDao;
            $userId = $t_tabDao->getT_TabUser($s_data->dbUser, $reqdata['tabId']);

            break;
         case "wordToUserGroup":
            //グループに所属してるユーザを取得
            $m_groupDao = new Dao\M_GroupDao;
            $userId = $m_groupDao->getM_GroupUser($s_data->dbUser, $reqdata['groupId']);

            break;
         case "wordToUserAll":
            $userId = [];
            break;
      }

      $v_machinelearnDao = new Dao\V_MachinelearnDao;
      $userOccupancyData = $v_machinelearnDao->getUserOccupancyRate($s_data->dbUser, $userId, $target);

      foreach ($userOccupancyData as $key => $o_value) {
         if (empty($wordToUserData)) {
            $userOccupancyData[$key]->score = 0;
         } else {
            foreach ($wordToUserData as $w_value) {

               if ($o_value->userId == $w_value->userId) {
                  $userOccupancyData[$key]->score = $w_value->score;
                  break;
               } else {
                  $userOccupancyData[$key]->score = 0;
               }
            }
         }
      }
      $resdata = $userOccupancyData;

      //エスケープ処理
      $reqdata['text'] = parent::textEscape($reqdata['text']);

      return response(['reqdata' => $reqdata, 'resdata' => $resdata]);
   }

   //ユーザから関連するキーワードを取得する
   public function getUserToWord(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      //使用データ設定
      $reqdata = $s_data->reqdata;

      switch ($reqdata['type']) {

         case "user":
            $targetTable = "userWordSet";
            $targetId = $reqdata['userId'];
            $targetColumn = 'userId';

            break;

         case "group":
            $targetTable = "groupWordSet";
            $targetId = $reqdata['groupId'];
            $targetColumn = 'groupId';
            break;

         case "tab":
            $targetTable = "tabWordSet";
            $targetId = $reqdata['tabId'];
            $targetColumn = 'tabId';
            break;
      }

      $socketNet = array('host' => $this->getPythonServerInfo(), 'port' => '8003', 'timeout' => 30, 'byteSize' => 8096);
      $socketData = array(
         'apiId' => '200',
         'dbUser' => mb_strtolower($s_data->dbUser, 'UTF-8'),
         'dbHost' => $s_data->dbHost,
         'mlHost' => $s_data->mlHost,
         'targetId' => $targetId,
         'targetTable' => $targetTable,
         'targetColumn' => $targetColumn
      );

      $socketResult = $this->socket($socketNet, $socketData);

      $userData = json_decode($socketResult);
      $resdata = $userData;

      return response(['reqdata' => $reqdata, 'resdata' => $resdata]);
   }


   //管理画面トップにて全てのワードの最新集計を出す
   public function getAllNewWord(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得

      $s_data =  parent::baseSession($request);

      //使用データ設定

      $reqdata = $s_data->reqdata;

      $socketNet = array('host' => $this->getPythonServerInfo(), 'port' => '8004', 'timeout' => 30, 'byteSize' => 8096);
      $socketData = array(
         'apiId' => '500',
         'dbUser' => mb_strtolower($s_data->dbUser, 'UTF-8'),
         'dbHost' => $s_data->dbHost,
         'mlHost' => $s_data->mlHost
      );


      $socketResult = $this->socket($socketNet, $socketData);

      $userData = json_decode($socketResult);
      $resdata = $userData;

      return response(['reqdata' => $reqdata, 'resdata' => $resdata]);
   }

   //
   public function getNewWordToId(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得

      $s_data =  parent::baseSession($request);

      //使用データ設定

      $reqdata = $s_data->reqdata;

      $socketNet = array('host' => $this->getPythonServerInfo(), 'port' => '8004', 'timeout' => 30, 'byteSize' => 8096);
      $socketData = array('apiId' => '500');


      $socketResult = $this->socket($socketNet, $socketData);

      $userData = json_decode($socketResult);
      $resdata = $userData;

      return response(['reqdata' => $reqdata, 'resdata' => $resdata]);
   }

   //
   public function getFreeWordToId(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      //使用データ設定
      $reqdata = $s_data->reqdata;

      switch ($reqdata['wordTarget']) {
         case "wordTargetUser":
            $targetTable = "userWordSet";
            $targetColumn = 'userId';
            break;

         case "wordTargetGroup":
            $targetTable = "groupWordSet";
            $targetColumn = 'groupId';
            break;

         case "wordTargetTab":
            $targetTable = "tabWordSet";
            $targetColumn = 'tabId';
            break;
      }

      $socketNet = array('host' => $this->getPythonServerInfo(), 'port' => '8001', 'timeout' => 30, 'byteSize' => 8096);
      $socketData = array(
         'apiId' => '300',
         'textData' => $reqdata['freeWord'],
         'targetTable' => $targetTable,
         'targetColumn' => $targetColumn,
         'dbUser' => mb_strtolower($s_data->dbUser, 'UTF-8'),
         'dbHost' => $s_data->dbHost,
         'mlHost' => $s_data->mlHost
      );

      $socketResult = $this->socket($socketNet, $socketData);

      $wordToIdData = json_decode($socketResult);

      $v_machinelearnDao = new Dao\V_MachinelearnDao;

      switch ($reqdata['wordTarget']) {
         case "wordTargetUser":
            $occupancyData = $v_machinelearnDao->getUserOccupancyRate($s_data->dbUser, [], "wordToUserAll");

            foreach ($occupancyData as $key => $o_value) {
               if (empty($wordToIdData)) {
                  $occupancyData[$key]->score = 0;
               } else {
                  foreach ($wordToIdData as $w_value) {

                     if ($o_value->userId == $w_value->userId) {
                        $occupancyData[$key]->score = $w_value->score;
                        break;
                     } else {
                        $occupancyData[$key]->score = 0;
                     }
                  }
               }
            }
            break;

         case "wordTargetGroup":
            $occupancyData = $v_machinelearnDao->getGroupOccupancyRate($s_data->dbUser);

            foreach ($occupancyData as $key => $o_value) {
               if (empty($wordToIdData)) {
                  $occupancyData[$key]->score = 0;
               } else {
                  foreach ($wordToIdData as $w_value) {

                     if ($o_value->groupId == $w_value->groupId) {
                        $occupancyData[$key]->score = $w_value->score;
                        break;
                     } else {
                        $occupancyData[$key]->score = 0;
                     }
                  }
               }
            }
            break;

         case "wordTargetTab":
            $occupancyData = $v_machinelearnDao->getTabOccupancyRate($s_data->dbUser);

            foreach ($occupancyData as $key => $o_value) {
               if (empty($wordToIdData)) {
                  $occupancyData[$key]->score = 0;
               } else {
                  foreach ($wordToIdData as $w_value) {

                     if ($o_value->tabId == $w_value->tabId) {
                        $occupancyData[$key]->score = $w_value->score;
                        break;
                     } else {
                        $occupancyData[$key]->score = 0;
                     }
                  }
               }
            }
            break;
      }


      $resdata = $occupancyData;

      //エスケープ処理
      $reqdata['freeWord'] = parent::textEscape($reqdata['freeWord']);

      return response(['reqdata' => $reqdata, 'resdata' => $resdata]);
   }


   private function socket($socketNet, $socketData)
   {

      $socket = null;
      //ソケット作成
      $socket = fsockopen($socketNet["host"], $socketNet["port"], $errno, $errstr, $socketNet["timeout"]);

      //ソケット接続結果
      if (!$socket) {
         //失敗
      } else {
         //成功
      }

      $json = json_encode($socketData, JSON_UNESCAPED_UNICODE);

      //メッセージ送信
      $fwrite = fwrite($socket, $json);

      //ソケット書き込み結果
      if (!$fwrite) {
         //失敗
      } else {
         //成功
      }

      //受信待機
      $time = 0;
      while (true) {
         $resultdata = fread($socket, $socketNet["byteSize"]);
         if (strlen($resultdata) != "") {
            break;
         }
         sleep(1);
         $time++;
         if ($time > 20) {
            break;
         }
      }

      //ソケットクローズ
      fclose($socket);

      return $resultdata;
   }

   private function getPythonServerInfo()
   {
      //機械学習処理を行うpythonサーバの情報を取得
      $pythonServerInfo = \Config::get('database.connections.pythonserver');
      return $pythonServerInfo['host'];
   }
}
