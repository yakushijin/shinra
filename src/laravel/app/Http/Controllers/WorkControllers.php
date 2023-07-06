<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Dao;

class WorkControllers extends BaseControllers
{
   /*====================================================
   取得
   ====================================================*/

   //ユーザ画面初期表示
   public function allGet(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      if (!$s_data) {
         return view('auth.login');
      }

      //ユーザの所属するグループID取得
      $m_userDao = new Dao\M_UserDao;
      $groupId = $m_userDao->getM_UserGroup($s_data->dbUser, $s_data->myUserId);

      //ユーザ及びユーザの所属するグループのタブ及び紐づくカテゴリを取得
      $t_tabDao = new Dao\T_TabDao;
      $resdata = $t_tabDao->getT_TabCategoryJoin($s_data->dbUser,  $s_data->myUserId, $groupId);

      //ユーザ情報取得
      $resdata->userData = $m_userDao->getM_UserDispInfo($s_data->dbUser,  $s_data->myUserId);

      //無効化されているユーザの遷移
      if ($resdata->userData->activeFlg) {
         return view('not')->with(['message' => 'ログイン中のユーザはワークスペース内のユーザにより無効化されています。']);
      }

      return view('work')->with(['resdata' => $resdata]);
   }

   //カテゴリに紐づいたタスク取得
   public function taskGet(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      if (!$s_data) {
         return view('auth.login');
      }

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $t_tabDao = new Dao\T_TabDao;
      $colorSet = $t_tabDao->getT_TabColor($s_data->dbUser, $reqdata['tabId']);

      $t_taskDao = new Dao\T_TaskDao;
      $taskData = $t_taskDao->getT_Task($s_data->dbUser, $reqdata['categoryId']);

      //エスケープ処理
      foreach ($taskData as $data) {
         $data->taskName = parent::textEscape($data->taskName);
      }

      $resdata = array('color' => $colorSet->color, 'textColor' => $colorSet->textColor, 'borderColor' => $colorSet->borderColor, 'taskData' => $taskData);

      return parent::baseAjaxResspons($s_data, $resdata);
   }

   /*====================================================
   登録
   ====================================================*/

   //カテゴリ追加
   public function categoryAdd(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      //インスタンス生成
      $t_categoryDao = new Dao\T_CategoryDao;

      //現在のカテゴリ件数取得し上限のチェック
      $categoryCount = $t_categoryDao->getT_CategoryCount($s_data->dbUser);
      if ($categoryCount >= $s_data->companyBaseInfo->categoryMaxCount) {
         $resdata = array('status' => 1, 'message' => "カテゴリの件数が上限を超えています");
      } else {
         //上限チェックに問題なければカテゴリを新規登録
         $now = \Carbon\Carbon::now();
         $result = $t_categoryDao->addT_Category(
            $s_data->dbUser,
            $s_data->myUserId,
            $reqdata['groupId'],
            $reqdata['tabId'],
            $reqdata['categoryName'],
            $reqdata['categoryDeadline'],
            $now
         );
         //DBエラーチェック
         if (!$result['code']) {
            //エスケープ処理
            $s_data->reqdata['categoryName'] = parent::textEscape($reqdata['categoryName']);
            $resdata = array('userId' => $s_data->myUserId, 'categoryId' => $result['resultData'], 'now' => $now->format('Y-m-d H:i:s'), 'status' => 0, 'message' => $s_data->reqdata['categoryName'] . "を作成");
         } else {
            $resdata = array('status' => 2, 'message' => 'サーバエラー[100]');
         }
      }

      //フロントに結果を返却
      return parent::baseAjaxResspons($s_data, $resdata);
   }

   //タスク追加
   public function taskAdd(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得

      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $resdata = [];
      $t_taskDao = new Dao\T_TaskDao;

      $taskCount = $t_taskDao->getT_TaskCount($s_data->dbUser);

      if ($taskCount >= $s_data->companyBaseInfo->taskMaxCount) {
         $resdata = array('status' => 1, 'message' => "タスクの件数が上限を超えています");
      } else {
         //タスクを新規登録
         $now = \Carbon\Carbon::now();
         $result =  $t_taskDao->addT_Task(
            $s_data->dbUser,
            $s_data->myUserId,
            $reqdata['groupId'],
            $reqdata['tabId'],
            $reqdata['categoryId'],
            $reqdata['taskName'],
            $reqdata['taskDeadline'],
            $now
         );
         //DBエラーチェック
         if (!$result['code']) {
            $s_data->reqdata['taskName'] = parent::textEscape($reqdata['taskName']);
            $resdata = array('taskId' => $result['resultData'], 'now' => $now->format('Y-m-d H:i:s'), 'status' => 0, 'message' => $s_data->reqdata['taskName'] . "を作成");
         } else {
            $resdata = array('status' => 2, 'message' => 'サーバエラー[100]');
         }
      }

      return parent::baseAjaxResspons($s_data, $resdata);
   }

   /*====================================================
   更新_基本
   ====================================================*/

   //タブ名更新
   public function tabUpdate(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $t_tabDao = new Dao\T_TabDao;
      $result =  $t_tabDao->updateT_Tab($s_data->dbUser, $s_data->myUserId, $reqdata['tabId'], $reqdata['tabName']);

      //DBエラーチェック
      if (!$result['code']) {
         $resdata = array('status' => 0, 'message' => $reqdata['tabName'] . '更新');
      } else {
         $resdata = array('status' => 2, 'message' => 'サーバエラー[100]');
      }

      return parent::baseAjaxResspons($s_data, $resdata);
   }

   //カテゴリ名更新
   public function categoryUpdate(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得

      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $t_categoryDao = new Dao\T_CategoryDao;
      $result = $t_categoryDao->updateT_Category($s_data->dbUser, $s_data->myUserId, $reqdata['categoryId'], $reqdata['categoryName']);

      //DBエラーチェック
      if (!$result['code']) {
         $resdata = array('status' => 0, 'message' => $reqdata['categoryName'] . '更新');
      } else {
         $resdata = array('status' => 2, 'message' => 'サーバエラー[100]');
      }

      return parent::baseAjaxResspons($s_data, $resdata);
   }

   //カテゴリ期日更新
   public function categoryDeadlineUpdate(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得

      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $t_categoryDao = new Dao\T_CategoryDao;
      $result = $t_categoryDao->updateT_CategoryDeadline($s_data->dbUser, $s_data->myUserId, $reqdata['categoryId'], $reqdata['deadline']);

      //DBエラーチェック
      if (!$result['code']) {
         $resdata = array('status' => 0, 'message' => '期日更新');
      } else {
         $resdata = array('status' => 2, 'message' => 'サーバエラー[100]');
      }

      return parent::baseAjaxResspons($s_data, $resdata);
   }

   //タスク名更新
   public function taskUpdate(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得

      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $t_taskDao = new Dao\T_TaskDao;
      $result = $t_taskDao->updateT_Task(
         $s_data->dbUser,
         $s_data->myUserId,
         $reqdata['taskId'],
         $reqdata['taskName']
      );

      //DBエラーチェック
      if (!$result['code']) {
         $resdata = array('status' => 0, 'message' => $reqdata['taskName'] . '更新');
      } else {
         $resdata = array('status' => 2, 'message' => 'サーバエラー[100]');
      }

      return parent::baseAjaxResspons($s_data, $resdata);
   }

   //タスク期日更新
   public function taskDeadlineUpdate(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $t_taskDao = new Dao\T_TaskDao;
      $reqdata = $request->all();
      $result = $t_taskDao->updateT_TaskDeadline(
         $s_data->dbUser,
         $s_data->myUserId,
         $reqdata['taskId'],
         $reqdata['deadline']
      );

      //DBエラーチェック
      if (!$result['code']) {
         $resdata = array('status' => 0, 'message' => '期日更新');
      } else {
         $resdata = array('status' => 2, 'message' => 'サーバエラー[100]');
      }
      return parent::baseAjaxResspons($s_data, $resdata);
   }

   /*====================================================
   更新_ステータス変更
   ====================================================*/
   public function categoryStatusUpdate(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $t_categoryDao = new Dao\T_CategoryDao;
      $result =  $t_categoryDao->statusUpdateT_Category(
         $s_data->dbUser,
         $reqdata['categoryId'],
         $reqdata['notstarted'],
         $reqdata['working'],
         $reqdata['waiting'],
         $reqdata['done'],
         $reqdata['archiveFlg']
      );
      //DBエラーチェック
      if (!$result['code']) {
         $resdata = array('status' => 0, 'message' => 'ステータス更新');
      } else {
         $resdata = array('status' => 2, 'message' => 'サーバエラー[100]');
      }

      return parent::baseAjaxResspons($s_data, $resdata);
   }

   public function categorySuspended(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得

      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $t_categoryDao = new Dao\T_CategoryDao;
      $result = $t_categoryDao->suspendedT_Category(
         $s_data->dbUser,
         $reqdata['categoryId'],
         $s_data->myUserId
      );
      //DBエラーチェック
      if (!$result['code']) {
         $resdata = array('status' => 0, 'message' => 'ステータス更新', 'data' => $result['resultData']);
      } else {
         $resdata = array('status' => 1, 'message' => '中断ステータス変更失敗[100]');
      }

      return parent::baseAjaxResspons($s_data, $resdata);
   }


   public function taskStatusUpdate(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $t_taskDao = new Dao\T_TaskDao;
      $result = $t_taskDao->statusUpdateT_Task(
         $s_data->dbUser,
         $reqdata['taskId'],
         $reqdata['notstarted'],
         $reqdata['working'],
         $reqdata['waiting'],
         $reqdata['done'],
         $reqdata['archiveFlg']
      );
      //DBエラーチェック
      if (!$result['code']) {
         $resdata = array('status' => 0, 'message' => 'ステータス更新');
      } else {
         $resdata = array('status' => 2, 'message' => 'サーバエラー[100]');
      }

      return parent::baseAjaxResspons($s_data, $resdata);
   }

   public function taskSuspended(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $t_taskDao = new Dao\T_TaskDao;
      $result = $t_taskDao->suspendedT_Task(
         $s_data->dbUser,
         $reqdata['taskId'],
         $s_data->myUserId
      );
      //DBエラーチェック
      if (!$result['code']) {
         $resdata = array('status' => 0, 'message' => 'ステータス更新', 'data' => $result['resultData']);
      } else {
         $resdata = array('status' => 1, 'message' => '中断ステータス変更失敗[100]');
      }

      return parent::baseAjaxResspons($s_data, $resdata);
   }

   /*====================================================
   更新_並び順変更
   ====================================================*/
   public function categorySort(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $t_categoryDao = new Dao\T_CategoryDao;
      $result = $t_categoryDao->updateT_CategorySort(
         $s_data->dbUser,
         $s_data->myUserId,
         $reqdata['categoryId'],
         $reqdata['categorySort']
      );
      //DBエラーチェック
      if (!$result['code']) {
         $resdata = array('status' => 0, 'message' => 'カテゴリ表示順変更', 'data' => $result['resultData']);
      } else {
         $resdata = array('status' => 1, 'message' => 'カテゴリ表示順変更失敗[100]');
      }

      return parent::baseAjaxResspons($s_data, $resdata);
   }

   public function taskSort(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $t_taskDao = new Dao\T_TaskDao;
      $result = $t_taskDao->updateT_TaskSort(
         $s_data->dbUser,
         $s_data->myUserId,
         $reqdata['taskId'],
         $reqdata['taskSort']
      );
      //DBエラーチェック
      if (!$result['code']) {
         $resdata = array('status' => 0, 'message' => 'タスク表示順変更', 'data' => $result['resultData']);
      } else {
         $resdata = array('status' => 1, 'message' => 'タスク表示順変更失敗[100]');
      }

      return parent::baseAjaxResspons($s_data, $resdata);
   }

   /*====================================================
   削除
   ====================================================*/

   //カテゴリ削除
   public function categoryDelete(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得

      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $t_categoryDao = new Dao\T_CategoryDao;
      $result = $t_categoryDao->deleteT_Category($s_data->dbUser, $reqdata['categoryId']);

      //DBエラーチェック
      if (!$result['code']) {
         $s_data->reqdata['categoryName'] = parent::textEscape($reqdata['categoryName']);
         $resdata = array('status' => 0, 'message' => $s_data->reqdata['categoryName'].'削除');
      } else {
         $resdata = array('status' => 2, 'message' => 'サーバエラー[100]');
      }
      return parent::baseAjaxResspons($s_data, $resdata);
   }

   //タスク削除
   public function taskDelete(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $t_taskDao = new Dao\T_TaskDao;
      $result = $t_taskDao->deleteT_Task($s_data->dbUser, $reqdata['taskId']);

      //DBエラーチェック
      if (!$result['code']) {
         $s_data->reqdata['taskName'] = parent::textEscape($reqdata['taskName']);
         $resdata = array('status' => 0, 'message' => $s_data->reqdata['taskName'].'削除');
      } else {
         $resdata = array('status' => 2, 'message' => 'サーバエラー[100]');
      }
      return parent::baseAjaxResspons($s_data, $resdata);
   }
}
