<?php

namespace Dao;

class G_CompanyDao extends BaseDao
{
    /*====================================================
    取得
    ====================================================*/
    //ワークスペース基本情報取得
    public function getG_CompanyBaseInfo($companyId)
    {
        $data = \DB::table('G_Company')
            ->select([
                'G_Company.companyId',
                'G_Company.companyName',
                'G_Company.dbUser',
                'G_Company.dbPassword',
                'G_Company.dbHost',
                'G_Company.mlHost',
                'G_Company.contractStatus',
                'G_Company.userMaxCount',
                'G_Company.groupMaxCount',
                'G_Company.tabMaxCount',
                'G_Company.categoryMaxCount',
                'G_Company.taskMaxCount',
                'G_Company.systemUserId',
            ])
            ->where('G_Company.companyId', '=', $companyId)
            ->get();
        return $data[0];
    }

    //ワークスペース詳細情報及び現在の各データ仕様件数取得
    public function getG_CompanyDataInfo($dbUser, $companyId)
    {
        $data = \DB::table('G_Company')
            ->select([
                'G_Company.companyName',
                'G_Company.contractStatus',
                'G_Company.userMaxCount',
                'G_Company.groupMaxCount',
                'G_Company.tabMaxCount',
                'G_Company.categoryMaxCount',
                'G_Company.taskMaxCount',
                'G_Company.dbDataUse',
                'G_Company.dbDataMaxSize',
                'G_Company.mlDataUse',
                'G_Company.mlDataAllCount',
                'G_Company.mlDataMaxSize',
                'G_Company.systemUserId',
                'G_Company.categorySaveDay',
                'G_Company.createDay',
                'G_Company.updateDay',
            ])
            ->where('G_Company.companyId', '=', $companyId)
            ->get();

        $data[0]->userCount = \DB::connection($dbUser)->table('M_User')
            ->count();

        $data[0]->groupCount = \DB::connection($dbUser)->table('M_Group')
            ->count();

        $data[0]->tabCount = \DB::connection($dbUser)->table('T_Tab')
            ->count();

        $data[0]->categoryCount = \DB::connection($dbUser)->table('T_Category')
            ->count();

        $data[0]->taskCount = \DB::connection($dbUser)->table('T_Task')
            ->count();

        //ユーザテーブル1レコード当たりのmaxバイト数
        $userTableRecodeSize = 422;
        //グループテーブル1レコード当たりのmaxバイト数
        $groupTableRecodeSize = 418;
        //タブテーブル1レコード当たりのmaxバイト数
        $tabTableRecodeSize = 393;
        //カテゴリテーブル1レコード当たりのmaxバイト数
        $categoryTableRecodeSize = 483;
        //タスクテーブル1レコード当たりのmaxバイト数
        $taskTableRecodeSize = 457;

        //各概算容量を計算※マッピングと実績テーブルは1レコード当たりの容量が小さいため計算しない
        $userTablEstimateSize = $userTableRecodeSize * $data[0]->userCount;
        $groupTablEstimateSize = $groupTableRecodeSize * $data[0]->groupCount;
        $tabTablEstimateSize = $tabTableRecodeSize * $data[0]->tabCount;
        $categoryTablEstimateSize = $categoryTableRecodeSize * $data[0]->categoryCount;
        $taskTablEstimateSize = $taskTableRecodeSize * $data[0]->taskCount;

        $tableEstimateSize = $userTablEstimateSize + $groupTablEstimateSize
            + $tabTablEstimateSize + $categoryTablEstimateSize + $taskTablEstimateSize;

        //mysqlのシステムテーブルのデータを計算
        $sql = "
            SELECT sum(data_length) as dataSize
                FROM information_schema.tables
                WHERE table_schema = '" . $dbUser . "'  
                GROUP BY table_schema;
            ";
        $dataSize = \DB::connection($dbUser)->select(\DB::raw($sql));

        //画面表示用の使用容量を計算する（多めに算出）
        $totalSize = ($dataSize[0]->dataSize + $tableEstimateSize);
        $dataMbSize = $totalSize / 1024 / 1024;
        $data[0]->dbDataUse = round($dataMbSize, 4);

        return $data[0];
    }

    /*====================================================
    登録
    ====================================================*/
    //
    public function addG_Company($userSetName, $userSetPassword)
    {
        //ユーザ用DBのhost情報
        $usernowdbhost = \Config::get('database.connections.usernowdbhost');

        $now = \Carbon\Carbon::now();
        $result = ['code' => 0, 'resultData' => []];

        \DB::beginTransaction();
        try {
            $data = [
                'companyName' => 'デフォルトワークスペース',
                'contractStatus' => 9,
                'dbUser' => $userSetName,
                'dbPassword' => $userSetPassword,
                'dbHost' => $usernowdbhost['dbhost'],
                'mlHost' => $usernowdbhost['mlhost'],
                'userMaxCount' => 10,
                'groupMaxCount' => 5,
                'tabMaxCount' => 10,
                'categoryMaxCount' => 1000,
                'taskMaxCount' => 5000,
                'dbDataUse' => 0,
                'dbDataMaxSize' => 10,
                'mlDataUse' => 0,
                'mlDataAllCount' => 0,
                'mlDataMaxSize' => 10,
                'systemUserId' => 1,
                'categorySaveDay' => 30,
                'createDay' => $now,
                'updateDay' => $now
            ];
            $result["resultData"] = \DB::table('G_Company')->insertGetId($data, 'companyId');
            \DB::commit();
        } catch (\Exception $e) {
            $result['code'] = 1;
            \DB::rollback();
        }

        return $result;
    }


    /*====================================================
    更新
    ====================================================*/
    //ワークスペース基本情報更新
    public function updateG_CompanyInfo($companyId, $companyName, $categorySaveDay)
    {
        $now = \Carbon\Carbon::now();
        $result = ['code' => 0, 'resultData' => []];

        \DB::beginTransaction();
        try {
            \DB::table('G_Company')->where('companyId', $companyId)
                ->update([
                    'G_Company.companyName' => $companyName,
                    'G_Company.categorySaveDay' => $categorySaveDay,
                    'G_Company.createDay' => $now,
                    'G_Company.updateDay' => $now
                ]);

            \DB::commit();
        } catch (\Exception $e) {
            $result['code'] = 1;
            \DB::rollback();
        }

        return $result;
    }

    public function updateG_CompanySystemuser($companyId, $userId)
    {
        $now = \Carbon\Carbon::now();
        \DB::table('G_Company')->where('companyId', $companyId)
            ->update([
                'G_Company.systemUserId' => $userId
            ]);
    }

    public function getG_CompanySystemLoginId($companyId)
    {
        $data = \DB::table('G_Company')
            ->select([
                'G_Login.email'
            ])
            ->join('G_Login', 'G_Login.userId', '=', 'G_Company.systemUserId')
            ->where('G_Company.companyId', '=', $companyId)
            ->where('G_Login.companyId', '=', $companyId)
            ->get();

        return $data[0];
    }


    /*====================================================
    削除
    ====================================================*/
}
