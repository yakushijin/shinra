<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App;

use Dao;

class BaseControllers extends Controller
{
   public function baseSession($request)
   {
      //セッション情報チェック
      if (!Auth::check()) {
         return false;
      }

      //コントローラ側で使用するデータセット定義
      $baseSessionData = collect([]);

      //各セッション情報格納
      $baseSessionData->myUserId = Auth::user()->userId;
      $baseSessionData->companyId = Auth::user()->companyId;

      //リクエスト情報格納
      $baseSessionData->reqdata = $request->all();

      //データベース情報設定（契約者ごとにそれぞれ別のデータベースが存在）
      $g_companyDao = new Dao\G_CompanyDao;
      $dbConnect = new Dao\DbInfo;
      $companyBaseInfo = $g_companyDao->getG_CompanyBaseInfo($baseSessionData->companyId);
      $dbConnect->dbConnectionSet($companyBaseInfo->dbUser, $companyBaseInfo->dbPassword,$companyBaseInfo->dbHost);

      if($companyBaseInfo->contractStatus == 9){
         return false;
      }

      //データベース情報格納
      $baseSessionData->dbUser = $companyBaseInfo->dbUser;
      $baseSessionData->dbHost = $companyBaseInfo->dbHost;
      $baseSessionData->mlHost = $companyBaseInfo->mlHost;

      $baseSessionData->companyBaseInfo = $companyBaseInfo;

      return $baseSessionData;
   }

   public function baseAjaxResspons($baseSessionData,$resdata)
   {
      return response()->json(['reqdata' => $baseSessionData->reqdata, 'resdata' => $resdata]);
   }

   public function textEscape($str){
      $escapeStr = htmlspecialchars($str, ENT_QUOTES|ENT_HTML5, "UTF-8");
      return $escapeStr;
   }

}
