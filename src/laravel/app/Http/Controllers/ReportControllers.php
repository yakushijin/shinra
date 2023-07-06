<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Dao;

class ReportControllers extends BaseControllers
{

   //実績レポート出力
   public function userReport(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      //使用データ設定
      $reqdata = $s_data->reqdata;

      $v_reportDao = new Dao\V_ReportDao;
      $aggregationUnit = $reqdata["aggregationUnit"];
      $resdata = $v_reportDao->getUserReport(
         $s_data->dbUser,
         $reqdata["userId"],
         $reqdata["dayFrom"],
         $reqdata["dayTo"],
         $aggregationUnit
      );

      return response(['reqdata' => $reqdata, 'resdata' => $resdata]);
   }

   public function groupReport(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      //使用データ設定
      $reqdata = $s_data->reqdata;

      $v_reportDao = new Dao\V_ReportDao;
      $resdata = $v_reportDao->getGroupReport(
         $s_data->dbUser,
         $reqdata["groupId"],
         $reqdata["dayFrom"],
         $reqdata["dayTo"],
         $reqdata["aggregationUnit"]
      );

      return response(['reqdata' => $reqdata, 'resdata' => $resdata]);
   }

   public function tabReport(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      //使用データ設定
      $reqdata = $s_data->reqdata;

      $v_reportDao = new Dao\V_ReportDao;
      $resdata = $v_reportDao->getTabReport(
         $s_data->dbUser,
         $reqdata["tabId"],
         $reqdata["dayFrom"],
         $reqdata["dayTo"],
         $reqdata["aggregationUnit"]
      );

      return response(['reqdata' => $reqdata, 'resdata' => $resdata]);
   }
}
