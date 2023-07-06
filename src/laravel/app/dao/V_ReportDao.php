<?php

namespace Dao;

class V_ReportDao extends BaseDao
{
    //ユーザ起点のレポート実績取得
    public function getUserReport($dbUser, $userId, $dayFrom, $dayTo, $aggregationUnit)
    {
        //共通SQL組み立て
        $sqlSet =  $this->sqlCommon($userId, $dayFrom, $dayTo, "userReport");

        //各集計単位ごとにSQLを組み立て実行する
        switch ($aggregationUnit) {
                /*
            対象ユーザの全体進捗率を集計
            ・集計方法
            ①ユーザが所有しているカテゴリを、完了済みとそれ以外を集計した状態ですべて抽出する。
            ②取得したデータを基に、進捗率を計算する
            */
            case "aggregationUser";
                $sql = "
                    SELECT a.userId,a.userName,
                    " . $sqlSet["categoryStatusAggregate"] . "
                    FROM M_User a
                    " . $sqlSet["tabProgressJoinUser"] . "
                    WHERE a.userId = " . $userId . "
                    GROUP BY a.userId,a.userName
                ";
                $selectData = \DB::connection($dbUser)->select(\DB::raw($sql));
                break;

                /*
            対象ユーザが所属してるグループ単位で対象ユーザの進捗率を集計
            ・集計方法
            ①ユーザが所有しているカテゴリを、完了済みとそれ以外を集計した状態ですべて抽出する。
            ②取得したデータでタブの所有グループがユーザの所属するグループのものに絞り込む
            ③取得したデータを基に、グループ単位で進捗率を計算する
            */
            case "aggregationGroup";
                $sql = "
                    SELECT a.groupId,a.groupName,
                    " . $sqlSet["categoryStatusAggregate"] . "
                    FROM M_Group a
                    " . $sqlSet["tabProgressJoinGroup"] . "
                    GROUP BY a.groupId,a.groupName
                ";
                $selectData = \DB::connection($dbUser)->select(\DB::raw($sql));
                break;

                /*
            対象ユーザが所有しているカテゴリのタブ単位で対象ユーザの進捗率を集計
            ・集計方法
            ①ユーザが所有しているカテゴリを、完了済みとそれ以外を集計した状態ですべて抽出する。
            ②取得したデータでタブの所有グループがユーザの所属するグループのものに絞り込む
            ③取得したデータを基に、タブ単位で進捗率を計算する
            ④取得したデータを基に、タブ内のカテゴリのステータスを取得。作業中と中断ステータスの場合は紐づいているタスクの進捗率を取得する
            */
            case "aggregationTab";
                $sql = "
                    SELECT a.tabId,a.tabName,a.userOrGroupId,b.groupName,
                    " . $sqlSet["categoryStatusAggregate"] . "
                    FROM T_Tab a
                    INNER JOIN M_Group b ON (a.userOrGroupId = b.groupId)
                    " . $sqlSet["tabProgressJoinTab"] . "
                    WHERE groupFlg = 1
                    GROUP BY a.tabId,a.tabName,a.userOrGroupId,b.groupName
                ";
                $selectData = \DB::connection($dbUser)->select(\DB::raw($sql));

                //タブ内のカテゴリの詳細を取得
                foreach ($selectData as $key => $data) {
                    $selectData[$key]->category = \DB::connection($dbUser)->select(\DB::raw("
                        SELECT x.categoryId,x.categoryName,x.tabId,'仕掛中' as status,IFNULL(y.percentage,0) as percentage FROM T_Category x
                        " . $sqlSet["categoryProgressJoin"] . "
                        WHERE tabId = " . $data->tabId . "
                        AND userId = " . $userId . "
                        AND categoryWorking <> 0
                        AND 
                        (
                            workingDay between cast('" . $dayFrom . "' as DATETIME) and cast('" . $dayTo . " 23:59:59' as DATETIME) 
                            OR y.percentage <> 100
                        )
                        UNION ALL 
                        SELECT x.categoryId,x.categoryName,x.tabId,'完了' as status,'' FROM T_Category x
                        WHERE  tabId = " . $data->tabId . "
                        AND userId = " . $userId . "
                        AND doneDay between cast('" . $dayFrom . "' as DATETIME) and cast('" . $dayTo . " 23:59:59' as DATETIME)
                        AND categoryDone = 1
                    "));

                    //エスケープ処理
                    foreach ($selectData[$key]->category as $data) {
                        $data->categoryName = htmlspecialchars($data->categoryName, ENT_QUOTES | ENT_HTML5, "UTF-8");
                    }
                }
                break;
        }

        return $selectData;
    }

    //グループ起点のレポート実績取得
    public function getGroupReport($dbUser,  $groupId, $dayFrom, $dayTo, $aggregationUnit)
    {
        //共通SQL組み立て
        $sqlSet =  $this->sqlCommon($groupId,  $dayFrom, $dayTo, "groupReport");

        //各集計単位ごとにSQLを組み立て実行する
        switch ($aggregationUnit) {
                /*
            対象グループに所属しているユーザ単位で対象グループ内の実績を集計
            ・集計方法
            ①グループが所有しているカテゴリを、完了済みとそれ以外を集計した状態ですべて抽出する。
            ②取得したデータを基に、進捗率を計算する
            */
            case "aggregationUser";
                $selectData = \DB::connection($dbUser)->select(\DB::raw("
                SELECT a.groupId,a.groupName,c.userId,u.userName,
                " . $sqlSet["categoryStatusAggregate"] . "
                FROM M_Group a
                " . $sqlSet["tabProgressJoinGroup"] . "
                INNER JOIN M_User u ON (c.userId = u.userId)
                WHERE a.groupId = " . $groupId . "
                GROUP BY a.groupId,a.groupName,c.userId,u.userName
            "));
                break;

                /*
            対象グループの全体進捗率を集計
            ・集計方法
            ①グループが所有しているカテゴリを、完了済みとそれ以外を集計した状態ですべて抽出する。
            ②取得したデータを基に、進捗率を計算する
            */
            case "aggregationGroup";
                $selectData = \DB::connection($dbUser)->select(\DB::raw("
                SELECT a.groupId,a.groupName,
                " . $sqlSet["categoryStatusAggregate"] . "
                FROM M_Group a
                " . $sqlSet["tabProgressJoinGroup"] . "
                WHERE a.groupId = " . $groupId . "
                GROUP BY a.groupId,a.groupName
            "));
                break;

                /*
            対象グループが所有しているタブ単位で対象グループ内の実績を集計
            ・集計方法
            ①グループが所有しているカテゴリを、完了済みとそれ以外を集計した状態ですべて抽出する。
            ②取得したデータを基に、進捗率を計算する
            */
            case "aggregationTab";
                $selectData = \DB::connection($dbUser)->select(\DB::raw("
                SELECT a.groupId,a.groupName,c.tabId,t.tabName,
                " . $sqlSet["categoryStatusAggregate"] . "
                FROM M_Group a
                " . $sqlSet["tabProgressJoinGroup"] . "
                INNER JOIN T_Tab t ON (c.tabId = t.tabId)
                WHERE a.groupId = " . $groupId . "
                GROUP BY a.groupId,a.groupName,c.tabId,t.tabName
            "));
                break;
        }

        return $selectData;
    }

    //タブ起点のレポート実績取得
    public function getTabReport($dbUser,  $tabId, $dayFrom, $dayTo, $aggregationUnit)
    {
        //共通SQL組み立て
        $sqlSet =  $this->sqlCommon($tabId,  $dayFrom, $dayTo, "tabReport");

        //各集計単位ごとにSQLを組み立て実行する
        switch ($aggregationUnit) {
                /*
            対象タブ内にカテゴリがあるユーザ単位で対象タブ内の実績を集計
            ・集計方法
            ①タブが所有しているカテゴリを、完了済みとそれ以外を集計した状態ですべて抽出する。
            ②取得したデータを基に、進捗率を計算する
            */
            case "aggregationUser";
                $sql = "
                    SELECT a.tabId,c.userId,u.userName,
                    " . $sqlSet["categoryStatusAggregate"] . "
                    FROM T_Tab a
                    " . $sqlSet["tabProgressJoinTab"] . "
                    INNER JOIN M_User u ON (c.userId = u.userId)
                    WHERE a.tabId = " . $tabId . "
                    AND groupFlg = 1
                    GROUP BY a.tabId,c.userId,u.userName
                ";
                $selectData = \DB::connection($dbUser)->select(\DB::raw($sql));
                break;

                /*
            対象タブの全体進捗率を集計
            ・集計方法
            ①タブが所有しているカテゴリを、完了済みとそれ以外を集計した状態ですべて抽出する。
            ②取得したデータを基に、進捗率を計算する
            */
            case "aggregationTab";
                $sql = "
                    SELECT a.userOrGroupId,
                    " . $sqlSet["categoryStatusAggregate"] . "
                    FROM T_Tab a
                    " . $sqlSet["tabProgressJoinTab"] . "
                    WHERE a.tabId = " . $tabId . "
                    AND groupFlg = 1
                    GROUP BY a.tabId
                    ";
                $selectData = \DB::connection($dbUser)->select(\DB::raw($sql));
                break;
        }

        return $selectData;
    }


    function sqlCommon($id,  $dayFrom, $dayTo, $reportType)
    {
        //それぞれの起点別ごとにフィルタをかけるカラムを指定する（後述のSQLで使用）
        $whereId = " ";
        switch ($reportType) {
            case "userReport":
                $whereId = " AND userId = " . $id . " ";
                break;
            case "groupReport":
                $whereId = " AND groupId = " . $id . " ";
                break;
            case "tabReport":
                $whereId = " AND tabId = " . $id . " ";
                break;
        }
        //カテゴリの完了、未完了をユーザ、グループ、タブごとに集計するSQL
        $tabInsideCategory  = "
            SELECT ct.notDoneCount,ct.doneCount,ct.tabId,ct.userId,ct.groupId
            FROM T_Tab t
            inner join (
                SELECT 
                    COUNT(*) AS notDoneCount,
                    0 AS doneCount,
                    tabId,
                    userId,
                    groupId
                FROM T_Category
                WHERE categoryDone = 0
                AND notstartedDay <= cast('" . $dayTo . " 23:59:59' as DATETIME)
                " . $whereId . "
                GROUP BY tabId,userId,groupId
                UNION all
                SELECT 
                    0 AS notDoneCount,
                    COUNT(*) AS doneCount,
                    tabId,
                    userId,
                    groupId 
                FROM T_Category
                WHERE categoryDone = 1
                AND notstartedDay <= cast('" . $dayTo . " 23:59:59' as DATETIME)
                " . $whereId . "
                GROUP BY tabId,userId,groupId
                ) 
                ct on (t.tabId = ct.tabId)
            where t.groupFlg = 1
            ";

        //SQL用格納コレクション
        $sqlSet = collect([]);

        //全レポート共通で使用するカテゴリステータス集計SQL
        $sqlSet["categoryStatusAggregate"] = "
            case IFNULL(sum(c.notDoneCount),0) + IFNULL(sum(c.doneCount),0) when 0 then 0
                ELSE ROUND(IFNULL(sum(c.doneCount),0) / (IFNULL(sum(c.notDoneCount),0) + IFNULL(sum(c.doneCount),0)) * 100) 
                END AS percentage,
            sum(c.notDoneCount),
            sum(c.doneCount)
            ";

        //集計したカテゴリ完了未完了結果を、指定したユーザで取得
        $sqlSet["tabProgressJoinUser"] = "
            INNER JOIN (
                " . $tabInsideCategory . "
            ) c ON (a.userId = c.userId)
            ";

        //集計したカテゴリ完了未完了結果を、指定したタブで取得
        $sqlSet["tabProgressJoinTab"] = "
            INNER JOIN (
                " . $tabInsideCategory . "
            ) c ON (a.tabId = c.tabId)
            ";

        //集計したカテゴリ完了未完了結果を、指定したグループで取得
        $sqlSet["tabProgressJoinGroup"] = "
            INNER JOIN (
                    " . $tabInsideCategory . "
                ) c ON (a.groupId = c.groupId)
            ";

        //指定したカテゴリのタスクの進捗率を計算
        $sqlSet["categoryProgressJoin"] = "
            LEFT JOIN 
            (
                SELECT a.categoryId,b.notDoneCount,c.doneCount,
                case IFNULL(b.notDoneCount,0) + IFNULL(c.doneCount,0) when 0 then 0
                ELSE ROUND(IFNULL(c.doneCount,0) / (IFNULL(b.notDoneCount,0) + IFNULL(c.doneCount,0)) * 100) 
                    END AS percentage
                FROM T_Category a
                LEFT JOIN 
                (
                    SELECT
                        COUNT(*) AS notDoneCount,
                        taskDone,
                        categoryId
                    FROM T_Task
                    WHERE taskDone = 0
                    AND notstartedDay <= cast('" . $dayTo . " 23:59:59' as DATETIME)
                    GROUP BY categoryId
                )
                b ON (a.categoryId = b.categoryId)
                LEFT JOIN 
                (
                    SELECT 
                        COUNT(*) AS doneCount,
                        taskDone,
                        categoryId 
                    FROM T_Task
                    WHERE taskDone = 1
                    AND notstartedDay <= cast('" . $dayTo . " 23:59:59' as DATETIME)
                    GROUP BY categoryId
                )
                c ON (a.categoryId = c.categoryId)
            ) 
            y ON (x.categoryId = y.categoryId)
            ";

        return $sqlSet;
    }
}
