<?php

namespace Dao;

class V_MachinelearnDao extends BaseDao
{
    //ユーザの完了以外カテゴリ件数取得
    public function getUserOccupancyRate($dbUser, $userId, $target)
    {
        //カテゴリおすすめユーザ検索の時の追加処理
        if ($target != "wordToUserAll") {
            $userIdArray = [];
            foreach ($userId as $data) {
                $userIdArray[] = $data->userId;
            }
            $userId = implode(",", $userIdArray);
            $sqlWhere = " AND main.userId IN (" . $userId . ") ";
        } else {
            $sqlWhere = "";
        }

        //共通SQL組み立て
        $sqlSet =  $this->sqlCommon("userId", "userName");

        //SQL組み立て
        $sql = $sqlSet["selectSql"] . "
            FROM M_User main"
            . $sqlSet["joinSql"] . "
            WHERE main.activeFlg = 0
            " . $sqlWhere . "
        ";

        $selectData = \DB::connection($dbUser)->select(\DB::raw($sql));

        return $selectData;
    }

    //グループの完了以外カテゴリ件数取得
    public function getGroupOccupancyRate($dbUser)
    {
        //共通SQL組み立て
        $sqlSet =  $this->sqlCommon("groupId", "groupName");

        //SQL組み立て
        $sql = $sqlSet["selectSql"] . "
            FROM M_Group main"
            . $sqlSet["joinSql"] . "
            WHERE main.activeFlg = 0
        ";

        $selectData = \DB::connection($dbUser)->select(\DB::raw($sql));

        return $selectData;
    }

    //タブの完了以外カテゴリ件数取得
    public function getTabOccupancyRate($dbUser)
    {
        //共通SQL組み立て
        $sqlSet =  $this->sqlCommon("tabId", "tabName");

        //SQL組み立て
        $sql = $sqlSet["selectSql"] . "
            FROM T_Tab main"
            . $sqlSet["joinSql"] . "
            WHERE main.archiveFlg = 0
            AND main.groupFlg = 1
            ";

        $selectData = \DB::connection($dbUser)->select(\DB::raw($sql));

        return $selectData;
    }

    //共通SQL
    private function sqlCommon($idColumn, $nameColumn)
    {

        $sqlSet = collect([]);

        $sqlSet["selectSql"] = "
            SELECT (IFNULL(c.notDoneCount,0)*2)+IFNULL(t.notDoneCount,0) as notDoneCount,
            main." . $idColumn . ",
            main." . $nameColumn . " as name,
            main.color
            ";

        $sqlSet["joinSql"] = "
            LEFT JOIN  
                (
                SELECT 
                COUNT(*) AS notDoneCount," . $idColumn . "
                FROM T_Category
                WHERE categoryDone = 0
                GROUP BY " . $idColumn . "
                )
            c ON (main." . $idColumn . " = c." . $idColumn . ")
            LEFT JOIN  
                (
                SELECT 
                COUNT(*) AS notDoneCount," . $idColumn . "
                FROM T_Task
                WHERE taskDone = 0
                GROUP BY " . $idColumn . "
                )
            t ON (main." . $idColumn . " = t." . $idColumn . ")
            ";

        return $sqlSet;
    }
}
