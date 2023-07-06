<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Dao;

class DebugControllers extends BaseControllers
{
   //デバッグ用
   public function demo(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得

      $s_data =  parent::baseSession($request);

      $reqdata = $request->all();
      $debug = new Dao\Debug;
      $debug->demo($s_data->dbUser, $s_data->companyBaseInfo->dbPassword);
      return response(['reqdata' => $reqdata]);
   }

   public function machineLearning1(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      $reqdata = $request->all();

      $dbUser = $s_data->dbUser;
      $dbPassword = $s_data->companyBaseInfo->dbPassword;
      $dbHost = $s_data->dbHost;
      $mlUser = mb_strtolower($s_data->dbUser, 'UTF-8');

      $command = "/usr/bin/python3.6 /var/www/python/b_update_score.py '"
         . $dbUser . "' '"
         . $dbPassword . "' '"
         . $dbHost . "' '"
         . $mlUser . "'";
      exec($command, $output, $resultCode);

      return response(['reqdata' => $reqdata]);
   }

   public function machineLearning2(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      $reqdata = $request->all();

      $dbUser = $s_data->dbUser;
      $dbPassword = $s_data->companyBaseInfo->dbPassword;
      $dbHost = $s_data->dbHost;
      $mlUser = mb_strtolower($s_data->dbUser, 'UTF-8');

      $command = "/usr/bin/python3.6 /var/www/python/b_update_newSummaryWord.py '"
         . $dbUser . "' '"
         . $dbPassword . "' '"
         . $dbHost . "' '"
         . $mlUser . "'";
      exec($command, $output, $resultCode);

      return response(['reqdata' => $reqdata]);
   }

   public function machineLearningDelete(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得
      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $dbHost = $s_data->dbHost;
      $mlUser = mb_strtolower($s_data->dbUser, 'UTF-8');

      $command = "/usr/bin/python3.6 /var/www/python/d_delete_all.py '"
      . $dbHost . "' '"
      . $mlUser . "'";
      ;
      exec($command, $output, $resultCode);

      return response(['reqdata' => $reqdata]);
   }

   public function datadelete(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得

      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $debug = new Dao\Debug;
      $debug->datadelete();
      return response(['reqdata' => $reqdata]);
   }

   public function alldelete(Request $request)
   {
      //基底クラスよりリクエスト、セッション情報取得

      $s_data =  parent::baseSession($request);

      //リクエストデータ取得
      $reqdata = $s_data->reqdata;

      $debug = new Dao\Debug;
      $debug->alldelete();
      return response(['reqdata' => $reqdata]);
   }
}
