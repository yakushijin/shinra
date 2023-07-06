<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Dao;

class AdminControllers extends BaseControllers
{
   /*====================================================
   取得
   ====================================================*/

   //初回管理者画面表示
   public function admin(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得

      $s_data =  parent::baseSession($request);

      if (!$s_data) {
         return view('auth.login');
      }

      $m_userDao = new Dao\M_UserDao;
      $resdata = $m_userDao->getM_UserDispInfo($s_data->dbUser, $s_data->myUserId);

      $systemUserId = $s_data->companyBaseInfo->systemUserId;

      $resdata->systemUser = 0;
      if ($s_data->myUserId == $systemUserId) {
         $resdata->systemUser = 1;
      }

      //無効化されているユーザの遷移
      if ($resdata->activeFlg) {
         return view('not')->with(['message' => 'ログイン中のユーザはワークスペース内のユーザにより無効化されています。']);
      }

      //管理者以外のユーザの場合（画面上からは導線はないがURL直叩きされた場合の対応）
      if (!$resdata->authority) {
         return view('not')->with(['message' => 'この画面の閲覧は管理者権限が必要です']);
      }

      return view('admin')->with(['resdata' => $resdata]);
   }

   //タブリスト検索
   public function tabSearch(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $t_tabDao = new Dao\T_TabDao;
      $resdata = $t_tabDao->getT_TabSearch($s_data->dbUser, $reqdata['tabName'], $reqdata['tabActive'], $reqdata['tabDeadline']);

      //エスケープ処理
      foreach ($resdata as $data) {
         $data->tabName = parent::textEscape($data->tabName);
      }

      return parent::baseAjaxResspons($s_data, $resdata);
   }

   //ユーザリスト検索
   public function userSearch(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $systemUserId = $s_data->companyBaseInfo->systemUserId;

      $m_userDao = new Dao\M_UserDao;
      $userList = $m_userDao->getM_UserSearch($s_data->dbUser, $reqdata['userName'], $reqdata['userActive'], $reqdata['userAuthority']);

      //エスケープ処理
      foreach ($userList as $data) {
         $data->userName = parent::textEscape($data->userName);
      }

      $resdata = array('systemUserId' => $systemUserId, 'userList' => $userList);

      return parent::baseAjaxResspons($s_data, $resdata);
   }

   //グループリスト検索
   public function groupSearch(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得

      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $m_groupDao = new Dao\M_GroupDao;
      $resdata = $m_groupDao->getM_GroupSearch($s_data->dbUser, $reqdata['groupName'], $reqdata['groupActive']);

      //エスケープ処理
      foreach ($resdata as $data) {
         $data->groupName = parent::textEscape($data->groupName);
      }

      return parent::baseAjaxResspons($s_data, $resdata);
   }

   //ログイン情報取得※ログインIDのみ
   public function loginInfoGet(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得

      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $g_loginDao = new Dao\G_LoginDao;
      $resdata = $g_loginDao->getG_LoginInfo($s_data->companyId, $reqdata['userId']);

      $g_companyDao = new Dao\G_CompanyDao;
      $systemUserId = $s_data->companyBaseInfo->systemUserId;

      $systemUserLoginId = $g_companyDao->getG_CompanySystemLoginId($s_data->companyId);
      $substrPosition  = strpos($systemUserLoginId->email, "@");
      $resdata->systemUserName = '@' . $s_data->companyBaseInfo->companyId . substr($systemUserLoginId->email, 0, $substrPosition);

      $resdata->systemUserFlg = 0;
      if ($reqdata['userId'] == $systemUserId) {
         $resdata->systemUserFlg = 1;
      } else {
         $resdata->email = str_replace($resdata->systemUserName, "", $resdata->email);
      }

      return parent::baseAjaxResspons($s_data, $resdata);
   }

   //実績情報検索
   public function performanceSearch(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得

      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $t_performanceDao = new Dao\T_PerformanceDao;
      $resdata = $t_performanceDao->getT_PerformanceSearch($s_data->dbUser, $reqdata['performanceDayFrom'], $reqdata['performanceDayTo'], $reqdata['target'], $reqdata['interval']);

      return parent::baseAjaxResspons($s_data, $resdata);
   }

   public function adminUserGet(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得

      $s_data =  parent::baseSession($request);

      $m_userDao = new Dao\M_UserDao;

      $resdata = $m_userDao->getM_UserAdminAll($s_data->dbUser);
      return parent::baseAjaxResspons($s_data, $resdata);
   }

   //ワークスペース情報取得
   public function companyGet(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得

      $s_data =  parent::baseSession($request);

      $m_userDao = new Dao\M_UserDao;
      $g_companyDao = new Dao\G_CompanyDao;


      $resdata = $g_companyDao->getG_CompanyDataInfo(
         $s_data->dbUser,
         $s_data->companyId
      );
      $resdata->systemUser = $m_userDao->getM_UserDispInfo(
         $s_data->dbUser,
         $resdata->systemUserId
      );

      //エスケープ処理
      $resdata->companyName = parent::textEscape($resdata->companyName);

      return parent::baseAjaxResspons($s_data, $resdata);
   }

   public function systemLoginNameGet(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得

      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $g_companyDao = new Dao\G_CompanyDao;

      $resdata = $g_companyDao->getG_CompanySystemLoginId($s_data->companyId);
      $substrPosition  = strpos($resdata->email, "@");
      $resdata->systemUserName = '@' . $s_data->companyBaseInfo->companyId . substr($resdata->email, 0, $substrPosition);

      return parent::baseAjaxResspons($s_data, $resdata);
   }

   /*====================================================
   登録
   ====================================================*/
   public function groupAdd(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $m_groupDao = new Dao\M_GroupDao;

      $groupCount = $m_groupDao->getM_GroupCount($s_data->dbUser);

      if ($groupCount >= $s_data->companyBaseInfo->groupMaxCount) {
         $resdata = array('status' => 1, 'message' => "グループの件数が上限を超えています");
      } else {
         if (!empty($reqdata['userIdArray'])) {
            $userIdArray = $reqdata['userIdArray'];
         } else {
            $userIdArray = [];
         }

         $result = $m_groupDao->addM_GroupInfo(
            $s_data->dbUser,
            $s_data->myUserId,
            $reqdata['groupName'],
            $reqdata['groupRemarks'],
            $userIdArray
         );

         //DBエラーチェック
         if (!$result['code']) {
            $resdata = array('status' => 0, 'message' => $reqdata['groupName'] . "グループを作成");
         } else {
            $resdata = array('status' => 2, 'message' => 'サーバエラー[100]');
         }
      }

      return parent::baseAjaxResspons($s_data, $resdata);
   }

   public function userAdd(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得

      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $m_userDao = new Dao\M_UserDao;
      $g_loginDao = new Dao\G_LoginDao;

      $resdata = [];

      $userCount = $m_userDao->getM_UserCount($s_data->dbUser);

      if ($userCount >= $s_data->companyBaseInfo->userMaxCount) {
         $resdata = array('status' => 1, 'message' => "ユーザの件数が上限を超えています");
      } else {
         $loginIdExistence = $g_loginDao->getG_LoginLoginIdCheck(
            $reqdata['loginId']
         );

         if (!$loginIdExistence) {
            $result =  $m_userDao->addM_UserInfo(
               $s_data->dbUser,
               $s_data->myUserId,
               $reqdata['userName'],
               $reqdata['userRemarks'],
               $reqdata['loginId'],
               $reqdata['password'],
               $s_data->companyId
            );

            //DBエラーチェック
            if (!$result['code']) {
               $resdata = array('status' => 0, 'message' => $reqdata['userName'] . 'ユーザ作成');
            } else {
               $resdata = array('status' => 2, 'message' => 'サーバエラー[100]');
            }

            $resdata = array('status' => 0, 'message' => $reqdata['userName'] . "ユーザを作成");
         } else {
            $resdata = array('status' => 1, 'message' => $reqdata['loginId'] . "がすでに存在しています");
         }
      }

      return parent::baseAjaxResspons($s_data, $resdata);
   }

   /*====================================================
   更新
   ====================================================*/
   public function companyUpdate(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $g_companyDao = new Dao\G_CompanyDao;
      $systemUserId = $s_data->companyBaseInfo->systemUserId;

      if ($s_data->myUserId == $systemUserId) {
         $result =  $g_companyDao->updateG_CompanyInfo($s_data->companyId, $reqdata['companyName'], $reqdata['categorySaveDay']);

         //DBエラーチェック
         if (!$result['code']) {
            $resdata = array('status' => 0, 'message' => 'ワークスペース更新');
         } else {
            $resdata = array('status' => 2, 'message' => 'サーバエラー[100]');
         }
      }

      return parent::baseAjaxResspons($s_data, $resdata);
   }

   public function systemUserChange(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得

      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $resdata = [];

      $g_companyDao = new Dao\G_CompanyDao;
      $g_companyDao->updateG_CompanySystemuser($s_data->companyId, $reqdata['userId']);

      return parent::baseAjaxResspons($s_data, $resdata);
   }


   //ログインID更新
   public function loginIdUpdate(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $g_loginDao = new Dao\G_LoginDao;
      $result = $g_loginDao->updateG_LoginLoginId($s_data->companyId, $reqdata['userId'], $reqdata['loginId']);

      //DBエラーチェック
      if (!$result['code']) {
         $resdata = array('status' => 0, 'message' => 'ログインID更新');
      } else {
         $resdata = array('status' => 2, 'message' => 'サーバエラー[100]');
      }
      return parent::baseAjaxResspons($s_data, $resdata);
   }


   //ログインパスワード更新
   public function loginPasswordUpdate(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $g_loginDao = new Dao\G_LoginDao;
      $result = $g_loginDao->updateG_LoginPassword($s_data->companyId, $reqdata['userId'], $reqdata['password']);

      //DBエラーチェック
      if (!$result['code']) {
         $resdata = array('status' => 0, 'message' => 'パスワード更新');
      } else {
         $resdata = array('status' => 2, 'message' => 'サーバエラー[100]');
      }

      return parent::baseAjaxResspons($s_data, $resdata);
   }
}
