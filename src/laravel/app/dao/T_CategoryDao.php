<?php

namespace Dao;

class T_CategoryDao extends BaseDao
{
    /*====================================================
   取得
   ====================================================*/
    //カテゴリ件数取得
    public function getT_CategoryCount($dbUser)
    {
        $categoryCount = \DB::connection($dbUser)->table('T_Category')
            ->count();

        return $categoryCount;
    }

    //カテゴリのタブID取得
    public function getT_TabId($dbUser, $categoryId)
    {
        $data = \DB::connection($dbUser)->table('T_Category')
            ->select([
                'T_Category.tabId'
            ])
            ->where('T_Category.categoryId', '=', $categoryId)
            ->get();
        return $data[0]->tabId;
    }

    //カテゴリ詳細情報取得
    public function getT_CategoryInfo($dbUser, $categoryId)
    {
        $data = \DB::connection($dbUser)->table('T_Category')
            ->select([
                'T_Category.categoryName',
                'T_Category.tabId',
                'T_Category.userId',
                'T_Category.groupId',
                'T_Category.categoryRemarks',
                'T_Category.categoryNotstarted',
                'T_Category.categoryWorking',
                'T_Category.categoryWaiting',
                'T_Category.categoryDone',
                'T_Category.categoryDeadline',
                'T_Category.learnedFlg',
                'T_Category.archiveFlg',
                'T_Category.notstartedDay',
                'T_Category.workingDay',
                'T_Category.waitingDay',
                'T_Category.doneDay',
                'T_Category.archiveDay',
                'T_Category.createUser',
                'T_Category.updateUser',
                'T_Category.createDay',
                'T_Category.updateDay',
                'M_User_user.userName as userName',
                'M_User_user.color as color',
                'M_User_user.textColor as textColor',
                'M_User_user.borderColor as borderColor',
                'M_User_createUser.userName as createUserName',
                'M_User_createUser.color as createUserColor',
                'M_User_createUser.textColor as createUserTextColor',
                'M_User_createUser.borderColor as createUserBorderColor',
                'M_User_updateUser.userName as updateUserName',
                'M_User_updateUser.color as updateUserColor',
                'M_User_updateUser.textColor as updateUserTextColor',
                'M_User_updateUser.borderColor as updateUserBorderColor',
            ])
            ->where('T_Category.categoryId', '=', $categoryId)
            ->join('M_User as M_User_user', 'M_User_user.userId', '=', 'T_Category.userId')
            ->join('M_User as M_User_createUser', 'M_User_createUser.userId', '=', 'T_Category.createUser')
            ->join('M_User as M_User_updateUser', 'M_User_updateUser.userId', '=', 'T_Category.updateUser')
            ->get();

        return $data[0];
    }

    //タブ内カテゴリを指定した条件で取得する
    public function getT_CategoryFilter($dbUser, $myUserId, $tabId, $categoryFilterSet)
    {
        /*----------------------SQL文字列定義----------------------*/
        //select関連
        $baseSelectSql = "
            SELECT 
            c.categoryId,
            c.categoryName,
            c.tabId,
            c.userId,
            c.groupId,
            c.categoryNotstarted,
            c.categoryWorking,
            c.categoryWaiting,
            c.categoryDone,
            c.categoryDeadline,
            c.archiveFlg,
            c.categorySort,
            u.userName,
            u.color,
            u.textColor,
            u.borderColor,
            g.groupName,
            ";
        $myUserSelectSql = " 1 as myUserflg ";
        $notMyUserSelectSql = " 0 as myUserflg ";

        //FROM、JOIN関連
        $baseFromSql = " FROM T_Category c ";
        $userJoinSql = " INNER JOIN M_User u ON (c.userId = u.userId) ";
        $groupJoinSql = " LEFT JOIN M_Group g ON (c.groupId = g.groupId) ";

        //WHERE関連
        $baseWhereSql = " WHERE archiveFlg = 0 AND tabId = " . $tabId;
        $myUserWhereSql = " AND c.userId = " . $myUserId;
        $notMyUserWhereSql = " AND c.userId <> " . $myUserId;

        //WHERE関連（フィルタ対象）
        $toDeadlineWhereSql = "";
        $baseStatusWhereSql = "";

        //orderby関連
        $baseOrderbySql = " ORDER BY categorySort,categoryDeadline IS NULL ASC,categoryDeadline ";

        //フィルタ対象のSQL組み換え（期限日指定、ステータス指定）
        switch ($categoryFilterSet["categoryFilterDeadline"]) {
            case 'categoryFilterAllDeadline':
                break;
            case 'categoryFilterSpecifyingDeadline':
                if($categoryFilterSet["categoryFilterToDeadline"] == null){
                    $toDeadlineWhereSql = " AND c.categoryDeadline is null ";
                }else{
                    $toDeadlineWhereSql = " AND c.categoryDeadline >= " . $categoryFilterSet["categoryFilterToDeadline"];
                }
                
                break;
        }
        switch ($categoryFilterSet["categoryFilterStatus"]) {
            case 'categoryFilterAllStatus':
                break;
            case 'categoryFilterSpecifyingStatus':

                switch ($categoryFilterSet["categoryFilterStatusValue"]) {
                    case 'categoryFilterNotstarted':
                        $baseStatusWhereSql = " AND c.categoryNotstarted = 1";
                        break;
                    case 'categoryFilterWorking':
                        $baseStatusWhereSql = " AND c.categoryWorking = 1";
                    break;
                    case 'categoryFilterSuspended':
                        $baseStatusWhereSql = " AND c.categoryWorking = 2";
                    break;
                    case 'categoryFilterWaiting':
                        $baseStatusWhereSql = " AND c.categoryWaiting = 1";
                    break;
                    case 'categoryFilterDone':
                        $baseStatusWhereSql = " AND c.categoryDone = 1";
                    break;
                }

                break;
        }

        /*----------------------SQL組み立て----------------------*/
        //自分の所有カテゴリを取得
        $myOwnCategoryGetSql =
            $baseSelectSql . $myUserSelectSql . $baseFromSql . $userJoinSql . $groupJoinSql .
            $baseWhereSql . $myUserWhereSql . $toDeadlineWhereSql . $baseStatusWhereSql;

        //自分の所有カテゴリ以外を取得
        $notMyOwnCategoryGetSql =
            $baseSelectSql . $notMyUserSelectSql . $baseFromSql . $userJoinSql . $groupJoinSql .
            $baseWhereSql . $notMyUserWhereSql . $toDeadlineWhereSql . $baseStatusWhereSql;

        //フィルタ対象のSQL組み換え（自ユーザか全てか）
        switch ($categoryFilterSet["categoryFilterOwner"]) {
            case 'categoryFilterAllOwner':
                $sql = $myOwnCategoryGetSql . " UNION ALL " . $notMyOwnCategoryGetSql . $baseOrderbySql;
                break;
            case 'categoryFilterMyOwner':
                $sql = $myOwnCategoryGetSql . $baseOrderbySql;
                break;
        }

        //SQL実行
        $data = \DB::connection($dbUser)->select(\DB::raw($sql));

        return $data;
    }

    /*====================================================
   登録
   ====================================================*/
    //カテゴリ新規登録
    public function addT_Category($dbUser, $userId, $groupId, $tabId, $categoryName, $categoryDeadline, $now)
    {
        $result = ['code' => 0, 'resultData' => []];
        $data = [
            'categoryName' => $categoryName,
            'tabId' => $tabId,
            'userId' => $userId,
            'groupId' => $groupId,
            'categoryRemarks' => '',
            'categoryNotstarted' => 1,
            'categoryWorking' =>  0,
            'categoryWaiting' => 0,
            'categoryDone' =>  0,
            'categoryDeadline' => $categoryDeadline,
            'learnedFlg' =>  0,
            'archiveFlg' => 0,
            'notstartedDay' => $now,
            'workingDay' => null,
            'waitingDay' => null,
            'doneDay' => null,
            'archiveDay' => null,
            'categorySort' => 0,
            'createUser' => $userId,
            'updateUser' => $userId,
            'createDay' => $now,
            'updateDay' => $now

        ];

        \DB::beginTransaction();
        try {
            $categoryId = \DB::connection($dbUser)->table('T_Category')->insertGetId($data, 'categoryId');

            \DB::connection($dbUser)->table('T_Category')->where('categoryId', $categoryId)->update(
                [
                    'categorySort' => $categoryId
                ]
            );

            $result['resultData'] = $categoryId;
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
    //カテゴリ名更新
    public function updateT_Category($dbUser, $userId, $categoryId, $categoryName)
    {
        $now = \Carbon\Carbon::now();
        $result = ['code' => 0, 'resultData' => []];

        \DB::beginTransaction();
        try {
            \DB::connection($dbUser)->table('T_Category')->where('categoryid', $categoryId)
                ->update([
                    'categoryname' => $categoryName,
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

    //カテゴリ期日更新
    public function updateT_CategoryDeadline($dbUser, $userId, $categoryId, $deadline)
    {
        $now = \Carbon\Carbon::now();
        $result = ['code' => 0, 'resultData' => []];

        \DB::beginTransaction();
        try {
            \DB::connection($dbUser)->table('T_Category')->where('categoryid', $categoryId)
                ->update([
                    'categoryDeadline' => $deadline,
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

    //カテゴリ詳細画面からの各情報更新
    public function updateDetailT_Category($dbUser, $userId, $categoryId, $categoryName, $categoryRemarks, $categoryDeadline)
    {
        $now = \Carbon\Carbon::now();
        $result = ['code' => 0, 'resultData' => []];

        \DB::beginTransaction();
        try {
            \DB::connection($dbUser)->table('T_Category')->where('categoryid', $categoryId)
                ->update([
                    'categoryName' => $categoryName,
                    'categoryRemarks' => parent::nullChangeString($categoryRemarks),
                    'categoryDeadline' => $categoryDeadline,
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

    //カテゴリ所有ユーザ変更
    public function userChangeT_Category($dbUser, $myUserId, $categoryId, $userId)
    {
        $now = \Carbon\Carbon::now();
        $result = ['code' => 0, 'resultData' => []];

        \DB::beginTransaction();
        try {
            \DB::connection($dbUser)->table('T_Category')->where('categoryId', $categoryId)
                ->update([
                    'userId' => $userId,
                    'updateUser' => $myUserId,
                    'updateDay' => $now
                ]);
            \DB::commit();
        } catch (\Exception $e) {
            $result['code'] = 1;
            \DB::rollback();
        }

        return $result;
    }

    //カテゴリ並び順変更
    public function updateT_CategorySort($dbUser, $myUserId, $categoryId, $categorySort)
    {
        $now = \Carbon\Carbon::now();
        $result = ['code' => 0, 'resultData' => []];

        \DB::beginTransaction();
        try {
            \DB::connection($dbUser)->table('T_Category')->where('categoryId', $categoryId)
                ->update([
                    'categorySort' => $categorySort,
                    'updateUser' => $myUserId,
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
    //カテゴリ削除
    public function deleteT_Category($dbUser, $categoryId)
    {
        $result = ['code' => 0, 'resultData' => []];

        \DB::beginTransaction();
        try {
            \DB::connection($dbUser)->table('T_Category')->where('categoryid', $categoryId)->delete();
            \DB::connection($dbUser)->table('T_Task')->where('categoryid', $categoryId)->delete();
            \DB::commit();
        } catch (\Exception $e) {
            $result['code'] = 1;
            \DB::rollback();
        }

        return $result;
    }

    /*====================================================
   更新_特殊
   ====================================================*/
    public function statusUpdateT_Category($dbUser, $categoryId, $notstarted, $working, $waiting, $done, $archiveFlg)
    {
        $result = ['code' => 0, 'resultData' => []];
        $now = \Carbon\Carbon::now();

        \DB::beginTransaction();
        try {
            \DB::connection($dbUser)->table('T_Category')->where('categoryid', $categoryId)
                ->update([
                    'categoryNotstarted' => $notstarted,
                    'categoryWorking' => $working,
                    'categoryWaiting' => $waiting,
                    'categoryDone' => $done,
                    'archiveFlg' => $archiveFlg,
                ]);

            if ($working) {
                \DB::connection($dbUser)->table('T_Category')
                    ->where('categoryid', $categoryId)
                    ->where('categoryWorking', 1)
                    ->where('workingDay', null)
                    ->update([
                        'workingDay' => $now
                    ]);
            } else if ($waiting) {
                \DB::connection($dbUser)->table('T_Category')
                    ->where('categoryid', $categoryId)
                    ->where('categoryWaiting', 1)
                    ->where('waitingDay', null)
                    ->update([
                        'waitingDay' => $now
                    ]);
            } else if ($done) {
                \DB::connection($dbUser)->table('T_Category')
                    ->where('categoryid', $categoryId)
                    ->where('categoryDone', 1)
                    ->update([
                        'doneDay' => $now
                    ]);
            }
            \DB::commit();
        } catch (\Exception $e) {
            $result['code'] = 1;
            \DB::rollback();
        }

        return $result;
    }

    public function suspendedT_Category($dbUser, $categoryId, $userId)
    {
        $result = ['code' => 0, 'resultData' => []];

        \DB::beginTransaction();
        try {
            \DB::connection($dbUser)->table('T_Category')
                ->where('categoryWorking', 1)
                ->where('categoryId', '<>', $categoryId)
                ->where('userId', $userId)
                ->update([
                    'categoryWorking' => 2
                ]);

            $result['resultData'] = \DB::connection($dbUser, $dbUser)->table('T_Category')
                ->select([
                    'T_Category.categoryId',
                    'T_Category.categoryWorking'
                ])
                ->where('T_Category.categoryWorking', '=', 2)
                ->where('userId', $userId)
                ->get();
            \DB::commit();
        } catch (\Exception $e) {
            $result['code'] = 1;
            \DB::rollback();
        }

        return $result;
    }


    public function updateT_CategoryDoneArchive($dbUser, $myUserId, $tabId,$categoryArchiveTarget)
    {
        $now = \Carbon\Carbon::now();
        $result = ['code' => 0, 'resultData' => []];

        //自ユーザか全ユーザかの判定
        switch ($categoryArchiveTarget) {
            case 'categoryArchiveMyUser':
                $userId = $myUserId;
                break;
            case 'categoryArchiveAll':
                $userId = 'userId';
                break;
        }

        \DB::beginTransaction();
        try {
            \DB::connection($dbUser)->table('T_Category')
                ->where('tabId', $tabId)
                ->where('categoryDone', 1)
                ->where('archiveFlg', 0)
                ->whereRaw('userId = '. $userId)
                ->update([
                    'archiveFlg' => 1,
                    'updateUser' => $myUserId,
                    'updateDay' => $now
                ]);
            \DB::commit();
        } catch (\Exception $e) {
            $result['code'] = 1;
            \DB::rollback();
        }

        return $result;
    }
}
