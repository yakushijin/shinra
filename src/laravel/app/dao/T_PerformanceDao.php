<?php

namespace Dao;

class T_PerformanceDao extends BaseDao
{
    public function getT_PerformanceSearch($dbUser, $performanceDayFrom, $performanceDayTo, $target, $interval)
    {

        $data = [];

        //取得対象の日を設定
        $sqlSelect = "";
        $sqlFrom = " FROM T_Performance p ";
        $sqlJoin = "";
        $sqlWhere = " WHERE 1=1 ";
        $sqlWhereDayFrom = "";
        $sqlWhereDayTo = "";
        $sqlWhereInterval = "";
        $sqlGroupby = "";

        if ($performanceDayFrom != "") {
            $sqlWhereDayFrom = " AND p.performanceDay >= '" . $performanceDayFrom . "'";
        }
        if ($performanceDayTo != "") {
            $sqlWhereDayTo = " AND p.performanceDay <= '" . $performanceDayTo . "'";
        }


        switch ($interval) {
            case "intervalMonth":
                $sqlWhereInterval = " AND p.performanceDay = DATE_FORMAT(p.performanceDay, '%Y-%m-01') ";
                break;
            case "intervalDay":
                break;
        }

        //まず日付を取得するSQLを実行する
        $performanceDaySql = "SELECT DISTINCT p.performanceDay FROM T_Performance p "
            . $sqlWhere . $sqlWhereDayFrom . $sqlWhereDayTo . $sqlWhereInterval;

        $data["performanceDay"] = \DB::connection($dbUser)->select(\DB::raw($performanceDaySql));

        switch ($target) {
            case "targetAll":

                $sqlSelect = "SELECT p.performanceDay,SUM(p.notDoneCount) as notDoneCount,SUM(p.doneCount) as doneCount,AVG(p.percentage) as percentage ";
                $sqlGroupby = " GROUP BY p.performanceDay ";
                $sql = $sqlSelect . $sqlFrom . $sqlJoin . $sqlWhere . $sqlWhereDayFrom . $sqlWhereDayTo . $sqlWhereInterval . $sqlGroupby;

                $data["all"] = \DB::connection($dbUser)->select(\DB::raw($sql));

                break;
            case "targetTab":
                $data["tab"] = \DB::connection($dbUser)->table('T_Tab')
                    ->select(['T_Tab.tabId', 'T_Tab.tabName', 'T_Tab.color'])
                    ->where('T_Tab.groupFlg', 1)
                    ->where('T_Tab.archiveFlg', 0)
                    ->get();

                $sqlSelect = "SELECT DISTINCT p.performanceDay,t.tabId,ifnull(b.percentage,0) as percentage ";
                $sqlJoin = " LEFT JOIN T_Tab t ON (1=1) LEFT JOIN T_Performance b ON (p.performanceDay = b.performanceDay AND  t.tabId = b.tabId) ";


                foreach ($data["tab"] as $key => $tabData) {
                    $sqlWhere = "WHERE t.tabId = " . $tabData->tabId;

                    $sql = $sqlSelect . $sqlFrom . $sqlJoin . $sqlWhere . $sqlWhereDayFrom . $sqlWhereDayTo . $sqlWhereInterval . $sqlGroupby;

                    $data["tab"][$key]->target = \DB::connection($dbUser)->select(\DB::raw($sql));
                }

                break;
            case "targetGroup":

                $data["group"] = \DB::connection($dbUser)->table('M_Group')
                    ->select(['M_Group.groupId', 'M_Group.groupName', 'M_Group.color'])
                    ->where('M_Group.activeFlg', 0)
                    ->get();

                $sqlSelect = "SELECT t.userOrGroupId,p.performanceDay,SUM(p.notDoneCount) as notDoneCount,SUM(p.doneCount) as doneCount,AVG(p.percentage) as percentage  ";
                $sqlJoin = " INNER JOIN T_Tab t ON (p.tabId = t.tabId AND t.groupFlg = 1) ";

                $sqlGroupby = " GROUP BY t.userOrGroupId,p.performanceDay ";

                foreach ($data["group"] as $key => $tabData) {

                    $sqlWhere = " WHERE t.userOrGroupId = " . $tabData->groupId;

                    $sql = $sqlSelect . $sqlFrom . $sqlJoin . $sqlWhere . $sqlWhereDayFrom . $sqlWhereDayTo . $sqlWhereInterval . $sqlGroupby;
                    $data["group"][$key]->target = \DB::connection($dbUser)->select(\DB::raw($sql));
                }
        }

        return $data;
    }
}
