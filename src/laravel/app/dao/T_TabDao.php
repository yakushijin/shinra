<?php

namespace Dao;

class T_TabDao extends BaseDao
{
    /*====================================================
   取得
   ====================================================*/
    //タブ件数取得
    public function getT_TabCount($dbUser)
    {
        $tabCount = \DB::connection($dbUser)->table('T_Tab')
            ->count();

        return $tabCount;
    }

    //タブ全件取得（リスト表示用）
    public function getT_TabAll($dbUser)
    {
        $tab = \DB::connection($dbUser)->table('T_Tab')
            ->select(['T_Tab.tabId', 'T_Tab.tabName', 'T_Tab.archiveFlg'])
            ->get();

        return $tab;
    }

    //タブ詳細情報取得
    public function getT_TabInfo($dbUser, $tabId)
    {
        $tab = \DB::connection($dbUser)->table('T_Tab')
            ->select([
                'T_Tab.tabId',
                'T_Tab.tabName',
                'T_Tab.tabRemarks',
                'T_Tab.color',
                'T_Tab.textColor',
                'T_Tab.borderColor',
                'T_Tab.tabDeadline',
                'T_Tab.userOrGroupId',
                'T_Tab.groupFlg',
                'T_Tab.archiveFlg',
                'T_Tab.createDay',
                'T_Tab.updateDay',
                'M_User_createUser.userName as createUserName',
                'M_User_createUser.color as createUserColor',
                'M_User_createUser.textColor as createUserTextColor',
                'M_User_createUser.borderColor as createUserBorderColor',
                'M_User_updateUser.userName as updateUserName',
                'M_User_updateUser.color as updateUserColor',
                'M_User_updateUser.textColor as updateUserTextColor',
                'M_User_updateUser.borderColor as updateUserBorderColor',
            ])
            ->where('T_Tab.tabId', '=', $tabId)
            ->join('M_User as M_User_createUser', 'M_User_createUser.userId', '=', 'T_Tab.createUser')
            ->join('M_User as M_User_updateUser', 'M_User_updateUser.userId', '=', 'T_Tab.updateUser')
            ->get();

        return $tab[0];
    }

    //タブの中にあるカテゴリのユーザ情報取得
    public function getT_TabUser($dbUser, $tabId)
    {
        $user = \DB::connection($dbUser)->table('T_Tab')
            ->select(['T_Category.userId'])
            ->distinct()
            ->join('T_Category', 'T_Category.tabId', '=', 'T_Tab.tabId')
            ->where('T_Tab.tabId', '=', $tabId)
            ->get();

        return $user;
    }

    //タブリスト検索
    public function getT_TabSearch($dbUser, $tabName, $tabActive, $tabDeadline)
    {

        $sql = "SELECT tabId,tabName,archiveFlg
                    FROM T_Tab
                    WHERE archiveFlg = " . $tabActive;
        if ($tabName != "") {
            $sql = $sql . " AND tabName like '%" . $tabName . "%'";
        }
        if ($tabDeadline != "") {
            $sql = $sql . " AND tabDeadline < " . $tabDeadline;
        }

        $tabdata = \DB::connection($dbUser)->select(\DB::raw($sql));

        return $tabdata;
    }

    //タブカラー情報取得
    public function getT_TabColor($dbUser, $tabId)
    {
        $data = \DB::connection($dbUser)->table('T_Tab')
            ->select([
                'T_Tab.color',
                'T_Tab.textColor',
                'T_Tab.borderColor'
            ])
            ->where('T_Tab.tabId', '=', $tabId)
            ->get();
        return $data[0];
    }

    /*====================================================
    取得_特殊
    ====================================================*/
    //ユーザ及びユーザの所属するグループの全てのタブ紐づくカテゴリを取得する
    public function getT_TabCategoryJoin($dbUser, $userId, $groupId)
    {
        $groupIdArray = [];
        foreach ($groupId as $data) {
            $groupIdArray[] = $data->groupId;
        }

        $tabgroupdata = \DB::connection($dbUser)->table('T_Tab')
            ->select([
                'T_Tab.tabId',
                'T_Tab.tabName',
                'T_Tab.color',
                'T_Tab.textColor',
                'T_Tab.borderColor',
                'T_Tab.tabDeadline',
                'T_Tab.userOrGroupId',
                'T_Tab.groupFlg',
                'T_Tab.archiveFlg',
                'T_Tab.createDay',
                'M_Group.groupName as userOrGroupName',
                'M_Group.color as userOrGroupColor',
                'M_Group.textColor as userOrGroupTextColor',
                'M_Group.borderColor as userOrGroupBorderColor',
            ])
            ->where('T_Tab.archiveFlg', '=', 0)
            ->where('T_Tab.groupFlg', '=', 1)
            ->where('M_Group.activeFlg', '=', 0)
            ->whereIn('T_Tab.userOrGroupId', $groupIdArray)
            ->join('M_Group', 'M_Group.groupId', '=', 'T_Tab.userOrGroupId');

        $tabdata = \DB::connection($dbUser)->table('T_Tab')
            ->select([
                'T_Tab.tabId',
                'T_Tab.tabName',
                'T_Tab.color',
                'T_Tab.textColor',
                'T_Tab.borderColor',
                'T_Tab.tabDeadline',
                'T_Tab.userOrGroupId',
                'T_Tab.groupFlg',
                'T_Tab.archiveFlg',
                'T_Tab.createDay',
                'M_User.userName as userOrGroupName',
                \DB::raw('"" as userOrGroupColor'),
                \DB::raw('"" as userOrGroupTextColor'),
                \DB::raw('"" as userOrGroupBorderColor'),
            ])
            ->where('T_Tab.archiveFlg', '=', 0)
            ->where('T_Tab.groupFlg', '=', 0)
            ->where('T_Tab.userOrGroupId', '=', $userId)
            ->join('M_User', 'M_User.userId', '=', 'T_Tab.userOrGroupId')
            ->unionAll($tabgroupdata)
            ->orderByRaw('tabDeadline IS NULL ASC')
            ->orderBy('tabDeadline')
            ->get();

        foreach ($tabdata as $key => $data) {
            $myUserCategory = \DB::connection($dbUser)->table('T_Category')
                ->select([
                    'T_Category.categoryId',
                    'T_Category.categoryName',
                    'T_Category.tabId',
                    'T_Category.userId',
                    'T_Category.groupId',
                    'T_Category.categoryNotstarted',
                    'T_Category.categoryWorking',
                    'T_Category.categoryWaiting',
                    'T_Category.categoryDone',
                    'T_Category.categoryDeadline',
                    'T_Category.archiveFlg',
                    'T_Category.categorySort',
                    'M_User.userName',
                    'M_User.color',
                    'M_User.textColor',
                    'M_User.borderColor',
                    'M_Group.groupName',
                    \DB::raw('1 as myUserflg')
                ])
                ->where('T_Category.archiveFlg', '=', 0)
                ->where('T_Category.tabId', '=', $data->tabId)
                ->where('T_Category.userId', '=', $userId)
                ->leftJoin('M_Group', 'M_Group.groupId', '=', 'T_Category.groupId')
                ->join('M_User', 'M_User.userId', '=', 'T_Category.userId');

            $tabdata[$key]->category = \DB::connection($dbUser)->table('T_Category')
                ->select([
                    'T_Category.categoryId',
                    'T_Category.categoryName',
                    'T_Category.tabId',
                    'T_Category.userId',
                    'T_Category.groupId',
                    'T_Category.categoryNotstarted',
                    'T_Category.categoryWorking',
                    'T_Category.categoryWaiting',
                    'T_Category.categoryDone',
                    'T_Category.categoryDeadline',
                    'T_Category.archiveFlg',
                    'T_Category.categorySort',
                    'M_User.userName',
                    'M_User.color',
                    'M_User.textColor',
                    'M_User.borderColor',
                    'M_Group.groupName',
                    \DB::raw('0 as myUserflg')
                ])
                ->where('T_Category.archiveFlg', '=', 0)
                ->where('T_Category.tabId', '=', $data->tabId)
                ->where('T_Category.userId', '<>', $userId)
                ->leftJoin('M_Group', 'M_Group.groupId', '=', 'T_Category.groupId')
                ->join('M_User', 'M_User.userId', '=', 'T_Category.userId')
                ->unionAll($myUserCategory)
                ->orderBy('categorySort')
                ->orderByRaw('categoryDeadline IS NULL ASC')
                ->orderBy('categoryDeadline')
                ->get();
        }
        return $tabdata;
    }

    /*====================================================
    登録
    ====================================================*/
    //タブ新規登録
    public function addT_Tab($dbUser, $userId, $tabName)
    {
        $now = \Carbon\Carbon::now();
        $result = ['code' => 0, 'resultData' => []];

        \DB::beginTransaction();
        try {
            $data = [
                'tabName' => parent::nullChangeString($tabName),
                'type' => 1,
                'color' => '#000000',
                'textColor' => "#000000",
                'borderColor' => "#000000",
                'tabRemarks' => "",
                'userOrGroupId' => $userId,
                'groupFlg' => 0,
                'tabDeadline' => null,
                'archiveFlg' => 0,
                'archiveDay' => null,
                'createUser' => $userId,
                'updateUser' => $userId,
                'createDay' => $now,
                'updateDay' => $now
            ];
            $tabId = \DB::connection($dbUser)->table('T_Tab')->insertGetId($data, 'tabId');

            $initColor = parent::colorInitSet($tabId, "tab");

            \DB::connection($dbUser)->table('T_Tab')->where('tabId', $tabId)->update(
                [
                    'color' => $initColor["color"],
                    'textColor' => $initColor["textColor"],
                    'borderColor' => $initColor["borderColor"]
                ]
            );
            $result['resultData'] = $tabId;

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
    //タブ更新
    public function updateT_TabInfo($dbUser, $loginUserId, $tabId, $tabName, $tabRemarks, $tabDeadline, $color, $textColor, $borderColor, $archiveFlg)
    {

        $now = \Carbon\Carbon::now();
        $result = ['code' => 0, 'resultData' => []];

        \DB::beginTransaction();
        try {
            \DB::connection($dbUser)->table('T_Tab')->where('tabId', $tabId)
                ->update([
                    'tabName' => $tabName,
                    'archiveFlg' => $archiveFlg,
                    'tabDeadline' => $tabDeadline,
                    'color' => $color,
                    'textColor' => $textColor,
                    'borderColor' => $borderColor,
                    'tabRemarks' => parent::nullChangeString($tabRemarks),
                    'updateUser' => $loginUserId,
                    'updateDay' => $now
                ]);
            \DB::commit();
        } catch (\Exception $e) {
            $result['code'] = 1;
            \DB::rollback();
        }

        return $result;
    }

    //ユーザタブをグループタブに変更
    public function userGroupChangeT_Tab($dbUser, $userId, $tabId, $groupId)
    {
        $now = \Carbon\Carbon::now();
        $result = ['code' => 0, 'resultData' => []];

        \DB::beginTransaction();
        try {
            \DB::connection($dbUser)->table('T_Tab')
                ->where('tabId', $tabId)
                ->where('groupFlg', 0)
                ->update([
                    'userOrGroupId' => $groupId,
                    'groupFlg' => 1,
                    'updateUser' => $userId,
                    'updateDay' => $now
                ]);

            \DB::connection($dbUser)->table('T_Category')
                ->where('tabId', $tabId)
                ->where('archiveFlg', 0)
                ->update([
                    'groupId' => $groupId,
                    'updateUser' => $userId,
                    'updateDay' => $now
                ]);

            \DB::connection($dbUser)->table('T_Task')
                ->where('tabId', $tabId)
                ->where('archiveFlg', 0)
                ->update([
                    'groupId' => $groupId,
                    'updateUser' => $userId,
                    'updateDay' => $now
                ]);

            \DB::commit();
        } catch (\Exception $e) {
            $result['code'] = 1;
            \DB::rollback();
        }

        return $result;
    }

    //タブの所有グループを変更
    public function updateT_Tabgroup($dbUser, $userId, $tabId, $groupId)
    {
        $now = \Carbon\Carbon::now();
        $result = ['code' => 0, 'resultData' => []];

        \DB::beginTransaction();
        try {
            \DB::connection($dbUser)->table('T_Tab')
                ->where('tabId', $tabId)
                ->where('groupFlg', 1)
                ->update([
                    'userOrGroupId' => $groupId,
                    'updateUser' => $userId,
                    'updateDay' => $now
                ]);
            \DB::connection($dbUser)->table('T_Category')
                ->where('tabId', $tabId)
                ->where('archiveFlg', 0)
                ->update([
                    'userId' => $userId,
                    'groupId' => $groupId,
                    'updateUser' => $userId,
                    'updateDay' => $now
                ]);
            \DB::connection($dbUser)->table('T_Task')
                ->where('tabId', $tabId)
                ->where('archiveFlg', 0)
                ->update([
                    'userId' => $userId,
                    'groupId' => $groupId,
                    'updateUser' => $userId,
                    'updateDay' => $now
                ]);
            \DB::commit();
        } catch (\Exception $e) {
            $result['code'] = 1;
            \DB::rollback();
        }

        return $result;
    }

    //タブ名更新
    public function updateT_Tab($dbUser, $userId, $tabId, $tabName)
    {
        $now = \Carbon\Carbon::now();
        $result = ['code' => 0, 'resultData' => []];

        \DB::beginTransaction();
        try {
            \DB::connection($dbUser)->table('T_Tab')->where('tabId', $tabId)
                ->update([
                    'tabName' => $tabName,
                    'updateUser' => $userId,
                    'updateDay' => $now
                ]);
            \DB::commit();
        } catch (\Exception $e) {
            $result['code'] = 1;
            \DB::rollback();
        }

        return $result;
    }

    /*====================================================
    削除
    ====================================================*/
    //タブ削除
    public function deleteT_tab($dbUser, $tabId)
    {
        $result = ['code' => 0, 'resultData' => []];

        \DB::beginTransaction();
        try {
            \DB::connection($dbUser)->table('T_Tab')->where('tabId', $tabId)->delete();
            \DB::connection($dbUser)->table('T_Category')->where('tabId', $tabId)->delete();
            \DB::connection($dbUser)->table('T_Task')->where('tabId', $tabId)->delete();
            \DB::commit();
        } catch (\Exception $e) {
            $result['code'] = 1;
            \DB::rollback();
        }

        return $result;
    }

    /*====================================================
    取得_レポート
    ====================================================*/
    public function getReportT_Tab($dbUser, $userId, $groupId)
    {
        $now = \Carbon\Carbon::now();
        $yesterday = \Carbon\Carbon::yesterday();

        $array = array();
        foreach ($groupId as $data) {
            $array[] = $data->groupId;
        }

        $groupIdArray = implode(",", $array) . "";

        $tabdata = \DB::connection($dbUser)->select(\DB::raw("
            SELECT a.tabId,a.tabName,a.userOrGroupId,a.groupFlg,b.groupName as userOrGroupName,IFNULL(c.percentage,0) as percentage
            FROM T_Tab a
            INNER JOIN M_Group b ON (a.userOrGroupId = b.groupId)
            LEFT JOIN (
                SELECT a.tabId,b.userId,b.notDoneCount,c.doneCount,
                case IFNULL(b.notDoneCount,0) + IFNULL(c.doneCount,0) when 0 then 0
                ELSE ROUND(IFNULL(c.doneCount,0) / (IFNULL(b.notDoneCount,0) + IFNULL(c.doneCount,0)) * 100) 
					 END AS percentage
                FROM T_Tab a
                    LEFT JOIN (
                    SELECT COUNT(*) AS notDoneCount,categoryDone,tabId,userId FROM T_Category
                    WHERE categoryDone = 0
                    GROUP BY tabId,userId
                    ) b ON (a.tabId = b.tabId)
                LEFT JOIN (
                    SELECT COUNT(*) AS doneCount,categoryDone,tabId,userId FROM T_Category
                    WHERE categoryDone = 1
                    GROUP BY tabId,userId
                    ) c ON (a.tabId = c.tabId)
                WHERE b.userId = 1
            ) c ON (a.tabId = c.tabId)
            WHERE a.userOrGroupId IN (" . $groupIdArray . ")
            AND archiveFlg = 0
            AND groupFlg = 1
            UNION all
            SELECT a.tabId,a.tabName,a.userOrGroupId,a.groupFlg,b.userName as userOrGroupName,IFNULL(c.percentage,0) as percentage
            FROM T_Tab a
            INNER JOIN M_User b ON (a.userOrGroupId = b.userId)
            LEFT JOIN (
                SELECT a.tabId,b.userId,b.notDoneCount,c.doneCount,
                case IFNULL(b.notDoneCount,0) + IFNULL(c.doneCount,0) when 0 then 0
                ELSE ROUND(IFNULL(c.doneCount,0) / (IFNULL(b.notDoneCount,0) + IFNULL(c.doneCount,0)) * 100) 
					 END AS percentage
                FROM T_Tab a
                    LEFT JOIN (
                    SELECT COUNT(*) AS notDoneCount,categoryDone,tabId,userId FROM T_Category
                    WHERE categoryDone = 0
                    GROUP BY tabId,userId
                    ) b ON (a.tabId = b.tabId)
                LEFT JOIN (
                    SELECT COUNT(*) AS doneCount,categoryDone,tabId,userId FROM T_Category
                    WHERE categoryDone = 1
                    GROUP BY tabId,userId
                    ) c ON (a.tabId = c.tabId)
                WHERE b.userId = " . $userId . "
            ) c ON (a.tabId = c.tabId)
            WHERE a.userOrGroupId = " . $userId . "
            AND archiveFlg = 0
            AND groupFlg = 0
            "));

        foreach ($tabdata as $key => $data) {
            $tabdata[$key]->category = \DB::connection($dbUser)->select(\DB::raw("
            SELECT x.categoryId,x.categoryName,x.tabId,'仕掛中' as status,IFNULL(y.percentage,0) as percentage FROM T_Category x
            LEFT JOIN 
            (
                SELECT a.categoryId,b.notDoneCount,c.doneCount,
                case IFNULL(b.notDoneCount,0) + IFNULL(c.doneCount,0) when 0 then 0
                ELSE ROUND(IFNULL(c.doneCount,0) / (IFNULL(b.notDoneCount,0) + IFNULL(c.doneCount,0)) * 100) 
					 END AS percentage
                FROM T_Category a
                LEFT JOIN 
                (
                SELECT COUNT(*) AS notDoneCount,taskDone,categoryId FROM T_Task
                WHERE taskDone = 0
                GROUP BY categoryId
                ) b ON (a.categoryId = b.categoryId)
                LEFT JOIN 
                (
                SELECT COUNT(*) AS doneCount,taskDone,categoryId FROM T_Task
                WHERE taskDone = 1
                GROUP BY categoryId
                ) c ON (a.categoryId = c.categoryId)
            ) y ON (x.categoryId = y.categoryId)
            WHERE archiveFlg = 0
            AND tabId = " . $data->tabId . "
            AND userId = " . $userId . "
            AND categoryWorking <> 0
            UNION ALL 
            SELECT x.categoryId,x.categoryName,x.tabId,'完了' as status,'' FROM T_Category x
            WHERE archiveFlg = 0
            AND tabId = " . $data->tabId . "
            AND userId = " . $userId . "
            AND categoryDone = 1
            "));
        }
        return $tabdata;
    }

    public function getT_TabProgress($dbUser)
    {
        $tab = \DB::connection($dbUser)->select(\DB::raw("
        SELECT a.tabId,b.notDone,c.done FROM T_Tab a
        LEFT JOIN (
            SELECT COUNT(*) AS notDone,categoryDone,tabId FROM T_Category
            WHERE categoryDone = 0
            GROUP BY tabId
            ) b ON (a.tabId = b.tabId)
        LEFT JOIN (
            SELECT COUNT(*) AS done,categoryDone,tabId FROM T_Category
            WHERE categoryDone = 1
            GROUP BY tabId
            ) c ON (a.tabId = c.tabId)
        "));

        return $tab;
    }

    public function getT_TabUserProgress($dbUser)
    {
        $tab = \DB::connection($dbUser)->select(\DB::raw("
        SELECT a.tabId,b.userId,b.notDone,c.done FROM T_Tab a
        LEFT JOIN (
            SELECT COUNT(*) AS notDone,categoryDone,tabId,userId FROM T_Category
            WHERE categoryDone = 0
            GROUP BY tabId,userId
            ) b ON (a.tabId = b.tabId)
        LEFT JOIN (
            SELECT COUNT(*) AS done,categoryDone,tabId,userId FROM T_Category
            WHERE categoryDone = 1
            GROUP BY tabId,userId
            ) c ON (a.tabId = c.tabId)
        WHERE b.userId = 1
        "));

        return $tab;
    }
}
