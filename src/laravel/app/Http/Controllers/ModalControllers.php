<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Dao;

class ModalControllers extends BaseControllers
{
   /*====================================================
   取得
   ====================================================*/
   //ユーザ基本情報取得
   public function userInfoGet(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $userId = $s_data->reqdata['userId'];

      //ユーザ情報取得
      $m_userDao = new Dao\M_UserDao;
      $resdata = $m_userDao->getM_UserInfo($s_data->dbUser, $userId);

      //システムユーザチェック
      $systemUserId = $s_data->companyBaseInfo->systemUserId;


      if ($userId == $systemUserId) {
         $resdata->systemUserFlg = 1;
      } else {
         $resdata->systemUserFlg = 0;
      }

      //エスケープ処理
      $resdata->userName = parent::textEscape($resdata->userName);
      $resdata->userRemarks = parent::textEscape($resdata->userRemarks);

      return parent::baseAjaxResspons($s_data, $resdata);
   }

   //グループ基本情報取得
   public function groupInfoGet(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $groupId = $s_data->reqdata['groupId'];

      //グループ情報取得
      $m_groupDao = new Dao\M_GroupDao;
      $resdata = $m_groupDao->getM_GroupInfo($s_data->dbUser, $groupId);
      $resdata->groupUser = $m_groupDao->getM_GroupUser($s_data->dbUser, $groupId);

      //エスケープ処理
      $resdata->groupName = parent::textEscape($resdata->groupName);
      $resdata->groupRemarks = parent::textEscape($resdata->groupRemarks);

      return parent::baseAjaxResspons($s_data, $resdata);
   }

   //タブ基本情報取得
   public function tabInfoGet(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $tabId = $s_data->reqdata['tabId'];

      //タブ情報取得
      $t_tabDao = new Dao\T_TabDao;
      $resdata = $t_tabDao->getT_TabInfo($s_data->dbUser, $tabId);

      //タブの所持者（グループかユーザ）を判定しそれぞれの情報取得
      if ($resdata->groupFlg == 1) {
         $m_groupDao = new Dao\M_GroupDao;
         $resdata->groupInfo = $m_groupDao->getM_GroupInfo($s_data->dbUser, $resdata->userOrGroupId);
      } else if ($resdata->groupFlg == 0) {
         $m_userDao = new Dao\M_UserDao;
         $resdata->userInfo = $m_userDao->getM_UserInfo($s_data->dbUser, $resdata->userOrGroupId);
      } else {
      }

      //エスケープ処理
      $resdata->tabName = parent::textEscape($resdata->tabName);
      $resdata->tabRemarks = parent::textEscape($resdata->tabRemarks);

      return parent::baseAjaxResspons($s_data, $resdata);
   }

   //カテゴリ基本情報取得
   public function categoryInfoGet(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $categoryId = $s_data->reqdata['categoryId'];

      //カテゴリ情報取得
      $t_categoryDao = new Dao\T_CategoryDao;
      $resdata = $t_categoryDao->getT_CategoryInfo($s_data->dbUser, $categoryId);

      //エスケープ処理
      $resdata->categoryName = parent::textEscape($resdata->categoryName);
      $resdata->categoryRemarks = parent::textEscape($resdata->categoryRemarks);

      return parent::baseAjaxResspons($s_data, $resdata);
   }

   //タスク基本情報取得
   public function taskInfoGet(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $groupId = $s_data->reqdata['taskId'];

      //グループ情報取得
      $t_taskDao = new Dao\T_TaskDao;
      $resdata = $t_taskDao->getT_TaskInfo($s_data->dbUser, $groupId);

      //エスケープ処理
      $resdata->taskName = parent::textEscape($resdata->taskName);
      $resdata->taskRemarks = parent::textEscape($resdata->taskRemarks);

      return parent::baseAjaxResspons($s_data, $resdata);
   }

   //グループ内のユーザ取得
   public function groupUserget(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $m_groupdao = new Dao\M_GroupDao;
      $resdata = $m_groupdao->getM_GroupUser(
         $s_data->dbUser,
         $reqdata['groupId']
      );

      //エスケープ処理
      foreach ($resdata as $data) {
         $data->userName = parent::textEscape($data->userName);
      }

      return parent::baseAjaxResspons($s_data, $resdata);
   }

   //グループ内のユーザとそれ以外のユーザ取得
   public function groupNotUserget(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      switch ($reqdata['type']) {
         case 'add':
            $m_userdao = new Dao\M_UserDao;
            $resdata = collect(['groupNotUser' => '']);
            $resdata['groupNotUser'] = $m_userdao->getM_UserAll(
               $s_data->dbUser
            );

            //エスケープ処理
            foreach ($resdata['groupNotUser'] as $data) {
               $data->userName = parent::textEscape($data->userName);
            }
            break;
         case 'update':
            $m_groupdao = new Dao\M_GroupDao;
            $resdata = $m_groupdao->getM_GroupNotUser(
               $s_data->dbUser,
               $reqdata['groupId']
            );
            //エスケープ処理
            foreach ($resdata["groupUser"] as $data) {
               $data->userName = parent::textEscape($data->userName);
            }
            foreach ($resdata["groupNotUser"] as $data) {
               $data->userName = parent::textEscape($data->userName);
            }
            break;
      }



      return parent::baseAjaxResspons($s_data, $resdata);
   }

   //ユーザに紐づくグループ取得
   public function userGroupGet(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $m_userdao = new Dao\M_UserDao;
      $resdata = $m_userdao->getM_UserGroup(
         $s_data->dbUser,
         $reqdata['userId']
      );

      //エスケープ処理
      foreach ($resdata as $data) {
         $data->groupName = parent::textEscape($data->groupName);
      }

      return parent::baseAjaxResspons($s_data, $resdata);
   }

   //全てのグループ取得
   public function groupAllGet(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      $m_groupdao = new Dao\M_GroupDao;
      $resdata = $m_groupdao->getM_GroupAll(
         $s_data->dbUser
      );

      //エスケープ処理
      foreach ($resdata as $data) {
         $data->groupName = parent::textEscape($data->groupName);
      }
      return parent::baseAjaxResspons($s_data, $resdata);
   }

   //タブ内のカテゴリ表示フィルタ
   public function categoryFilter(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $t_categoryDao = new Dao\T_CategoryDao;
      $resdata = $t_categoryDao->getT_CategoryFilter($s_data->dbUser, $s_data->myUserId, $reqdata["tabId"], $reqdata["categoryFilterSet"]);

      //エスケープ処理
      foreach ($resdata as $data) {
         $data->categoryName = parent::textEscape($data->categoryName);
      }
      return parent::baseAjaxResspons($s_data, $resdata);
   }

   /*====================================================
   登録
   ====================================================*/

   //タブ新規登録
   public function tabAdd(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $resdata = [];
      $t_tabDao = new Dao\T_TabDao;

      $tabCount = $t_tabDao->getT_TabCount($s_data->dbUser);

      if ($tabCount >= $s_data->companyBaseInfo->tabMaxCount) {
         $resdata = array('status' => 1, 'message' => "タブの件数が上限を超えています");
      } else {
         $tabName = $reqdata['tabName'] ?? "";

         $result =  $t_tabDao->addT_Tab($s_data->dbUser, $s_data->myUserId, $tabName);
         $colorSet = $t_tabDao->getT_TabColor($s_data->dbUser, $result['resultData']);

         //DBエラーチェック
         if (!$result['code']) {
            $resdata = array('status' => 0, 'message' => '期日更新');
            $resdata = array(
               'color' => $colorSet->color,
               'textColor' => $colorSet->textColor,
               'borderColor' => $colorSet->borderColor,
               'tabId' => $result['resultData'],
               'tabName' => $tabName,
               'status' => 0,
               'message' => $tabName . "を作成"
            );
         } else {
            $resdata = array('status' => 2, 'message' => 'サーバエラー[100]');
         }
      }

      return parent::baseAjaxResspons($s_data, $resdata);
   }

   /*====================================================
   更新_基本
   ====================================================*/

   //ユーザ基本情報更新
   public function userInfoUpdate(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $m_userDao = new Dao\M_UserDao;
      $result =  $m_userDao->updateM_UserInfo(
         $s_data->dbUser,
         $s_data->myUserId,
         $reqdata['userId'],
         $reqdata['userName'],
         (int) $reqdata['authority'],
         $reqdata['userRemarks'],
         (int) $reqdata['activeFlg'],
         (int) $reqdata['defaultDeadlineFlg'],
         (int) $reqdata['deleteMessageFlg'],
         (int) $reqdata['doneAutoActiveFlg'],
         $reqdata['color'],
         $reqdata['textColor'],
         $reqdata['borderColor']
      );

      //DBエラーチェック
      if (!$result['code']) {
         $s_data->reqdata['userName'] = parent::textEscape($reqdata['userName']);
         $resdata = array('status' => 0, 'message' => $s_data->reqdata['userName'] . '更新');
      } else {
         $resdata = array('status' => 2, 'message' => 'サーバエラー[100]');
      }

      return parent::baseAjaxResspons($s_data, $resdata);
   }

   //グループ基本情報更新
   public function groupInfoUpdate(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $m_groupDao = new Dao\M_GroupDao;
      $result = $m_groupDao->updateM_GroupInfo(
         $s_data->dbUser,
         $s_data->myUserId,
         $reqdata['groupId'],
         $reqdata['groupName'],
         $reqdata['color'],
         $reqdata['textColor'],
         $reqdata['borderColor'],
         $reqdata['groupRemarks'],
         (int) $reqdata['activeFlg']
      );

      //DBエラーチェック
      if (!$result['code']) {
         $s_data->reqdata['groupName'] = parent::textEscape($reqdata['groupName']);
         $resdata = array('status' => 0, 'message' => $s_data->reqdata['groupName'] . '更新');
      } else {
         $resdata = array('status' => 2, 'message' => 'サーバエラー[100]');
      }

      return parent::baseAjaxResspons($s_data, $resdata);
   }

   //タブ基本情報更新
   public function tabInfoUpdate(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得

      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $t_tabDao = new Dao\T_TabDao;
      $result = $t_tabDao->updateT_TabInfo(
         $s_data->dbUser,
         $s_data->myUserId,
         $reqdata['tabId'],
         $reqdata['tabName'],
         $reqdata['tabRemarks'],
         $reqdata['tabDeadline'],
         $reqdata['color'],
         $reqdata['textColor'],
         $reqdata['borderColor'],
         (int) $reqdata['archiveFlg']
      );

      //DBエラーチェック
      if (!$result['code']) {
         $resdata = array('status' => 0, 'message' => $reqdata['tabName'] . '更新');
      } else {
         $resdata = array('status' => 2, 'message' => 'サーバエラー[100]');
      }

      return parent::baseAjaxResspons($s_data, $resdata);
   }

   //カテゴリ更新
   public function categoryDetailUpdate(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $t_categoryDao = new Dao\T_CategoryDao;
      $result = $t_categoryDao->updateDetailT_Category(
         $s_data->dbUser,
         $s_data->myUserId,
         $reqdata['categoryId'],
         $reqdata['categoryName'],
         $reqdata['categoryRemarks'],
         $reqdata['categoryDeadline']
      );

      //DBエラーチェック
      if (!$result['code']) {
         $resdata = array('status' => 0, 'message' => $reqdata['categoryName'] . '情報更新');
      } else {
         $resdata = array('status' => 2, 'message' => 'サーバエラー[100]');
      }

      return parent::baseAjaxResspons($s_data, $resdata);
   }

   //タスク更新
   public function taskDetailUpdate(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $resdata = [];

      $t_taskDao = new Dao\T_TaskDao;
      $result = $t_taskDao->updatedetailT_Task(
         $s_data->dbUser,
         $s_data->myUserId,
         $reqdata['taskId'],
         $reqdata['taskName'],
         $reqdata['taskRemarks'],
         $reqdata['taskDeadline']
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
   更新_特殊
   ====================================================*/

   //タブの所持グループ変更
   public function tabGroupChange(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得

      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $t_tabdao = new Dao\T_TabDao;
      $result = $t_tabdao->updateT_Tabgroup(
         $s_data->dbUser,
         $s_data->myUserId,
         $reqdata['tabId'],
         $reqdata['groupId']
      );

      //DBエラーチェック
      if (!$result['code']) {
         $resdata = array('status' => 0, 'message' => 'タブの所有グループ更新');
      } else {
         $resdata = array('status' => 2, 'message' => 'サーバエラー[100]');
      }
      return parent::baseAjaxResspons($s_data, $resdata);
   }

   //グループの所持ユーザ変更
   public function groupUserChange(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得

      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $t_usergroupmapdao = new Dao\T_UserGroupMapDao;
      $result = $t_usergroupmapdao->updateT_UserGroupMap(
         $s_data->dbUser,
         $reqdata['groupId'],
         $reqdata['userIdArray']
      );

      //DBエラーチェック
      if (!$result['code']) {
         $resdata = array('status' => 0, 'message' => 'グループ所属ユーザ変更');
      } else {
         $resdata = array('status' => 2, 'message' => 'サーバエラー[100]');
      }
      return parent::baseAjaxResspons($s_data, $resdata);
   }

   //カテゴリの所持ユーザ変更
   public function categoryUserChange(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $t_categoryDao = new Dao\T_CategoryDao;
      $result = $t_categoryDao->userChangeT_Category(
         $s_data->dbUser,
         $s_data->myUserId,
         $reqdata['categoryId'],
         $reqdata['userId']
      );

      //DBエラーチェック
      if (!$result['code']) {
         $resdata = array('status' => 0, 'message' => '所有ユーザ変更');
      } else {
         $resdata = array('status' => 2, 'message' => 'サーバエラー[100]');
      }

      return parent::baseAjaxResspons($s_data, $resdata);
   }

   //ユーザタブをグループタブに変更
   public function groupTabChange(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;
      $resdata = [];

      $t_tabDao = new Dao\T_TabDao;

      //タブにグループフラグつけてグループID付与
      $result =  $t_tabDao->userGroupChangeT_Tab(
         $s_data->dbUser,
         $s_data->myUserId,
         $reqdata['tabId'],
         $reqdata['groupId']
      );

      //DBエラーチェック
      if (!$result['code']) {
         $resdata = array('status' => 0, 'message' => 'グループタブ化');
      } else {
         $resdata = array('status' => 2, 'message' => 'サーバエラー[100]');
      }

      return parent::baseAjaxResspons($s_data, $resdata);
   }

   //完了済みカテゴリのアーカイブ
   public function categoryDoneArchive(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $t_categoryDao = new Dao\T_CategoryDao;
      $result = $t_categoryDao->updateT_CategoryDoneArchive(
         $s_data->dbUser,
         $s_data->myUserId,
         $reqdata['tabId'],
         $reqdata['categoryArchiveTarget']
      );

      //DBエラーチェック
      if (!$result['code']) {
         $resdata = array('status' => 0, 'message' => '完了済カテゴリアーカイブ完了');
      } else {
         $resdata = array('status' => 2, 'message' => 'サーバエラー[100]');
      }

      return parent::baseAjaxResspons($s_data, $resdata);
   }



   /*====================================================
   削除
   ====================================================*/

   //タブ削除
   public function tabDelete(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $t_tabDao = new Dao\T_TabDao;
      $result = $t_tabDao->deleteT_Tab($s_data->dbUser, $reqdata['tabId']);

      //DBエラーチェック
      if (!$result['code']) {
         $resdata = array('status' => 0, 'message' => 'タブを削除');
      } else {
         $resdata = array('status' => 2, 'message' => 'サーバエラー[100]');
      }
      return parent::baseAjaxResspons($s_data, $resdata);
   }
}
