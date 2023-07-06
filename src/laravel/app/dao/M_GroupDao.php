<?php

namespace Dao;

class M_GroupDao extends BaseDao
{
    /*====================================================
   取得
   ====================================================*/
    //グループ件数取得
    public function getM_GroupCount($dbUser)
    {
        $groupCount = \DB::connection($dbUser)->table('M_Group')
            ->count();

        return $groupCount;
    }

    //有効化されている全てのグループ取得
    public function getM_GroupAll($dbUser)
    {
        $group = \DB::connection($dbUser)->table('M_Group')
            ->select([
                'M_Group.groupId',
                'M_Group.groupName',
                'M_Group.activeFlg',
                'M_Group.color',
                'M_Group.textColor',
                'M_Group.borderColor'
            ])
            ->where('M_Group.activeFlg', '=', 0)
            ->get();

        return $group;
    }

    //グループ検索
    public function getM_GroupSearch($dbUser, $groupName, $groupActive)
    {
        $sql = "SELECT groupId,groupName,activeFlg
        FROM M_Group
        WHERE activeFlg = " . $groupActive;
        if ($groupName != "") {
            $sql = $sql . " AND groupName like '%" . $groupName . "%'";
        }

        $group = \DB::connection($dbUser)->select(\DB::raw($sql));

        return $group;
    }

    //グループ詳細情報取得
    public function getM_GroupInfo($dbUser, $groupId)
    {
        $group = \DB::connection($dbUser)->table('M_Group')
            ->select([
                'M_Group.groupId',
                'M_Group.groupName',
                'M_Group.color',
                'M_Group.textColor',
                'M_Group.borderColor',
                'M_Group.groupRemarks',
                'M_Group.activeFlg',
                'M_Group.archiveDay',
                'M_Group.createDay',
                'M_Group.updateDay',
                'M_User_createUser.userName as createUserName',
                'M_User_createUser.color as createUserColor',
                'M_User_createUser.textColor as createUserTextColor',
                'M_User_createUser.borderColor as createUserBorderColor',
                'M_User_updateUser.userName as updateUserName',
                'M_User_updateUser.color as updateUserColor',
                'M_User_updateUser.textColor as updateUserTextColor',
                'M_User_updateUser.borderColor as updateUserBorderColor',
            ])
            ->where('M_Group.groupId', '=', $groupId)
            ->join('M_User as M_User_createUser', 'M_User_createUser.userId', '=', 'M_Group.createUser')
            ->join('M_User as M_User_updateUser', 'M_User_updateUser.userId', '=', 'M_Group.updateUser')
            ->get();

        return $group[0];
    }

    //グループに所属するユーザを取得
    public function getM_GroupUser($dbUser, $groupId)
    {
        $data = \DB::connection($dbUser)->table('M_Group')
            ->select([
                'M_User.userId',
                'M_User.userName',
                'M_User.color',
                'M_User.textColor',
                'M_User.borderColor'
            ])
            ->join('T_UserGroupMap', 'T_UserGroupMap.groupId', '=', 'M_Group.groupId')
            ->join('M_User', 'M_User.userId', '=', 'T_UserGroupMap.userId')
            ->where('M_Group.groupId', '=', $groupId)
            ->where('M_User.activeFlg', '=', 0)
            ->get();

        return $data;
    }

    //グループに所属していないユーザを取得
    public function getM_GroupNotUser($dbUser, $groupId)
    {

        $data = collect(['groupUser' => '', 'groupNotUser' => '']);

        $data['groupUser'] = \DB::connection($dbUser)->table('M_Group')
            ->select([
                'M_User.userId',
                'M_User.userName',
                'M_User.color',
                'M_User.textColor',
                'M_User.borderColor'
            ])
            ->join('T_UserGroupMap', 'T_UserGroupMap.groupId', '=', 'M_Group.groupId')
            ->join('M_User', 'M_User.userId', '=', 'T_UserGroupMap.userId')
            ->where('M_Group.groupId', '=', $groupId)
            ->get();

        $array = [];
        foreach ($data['groupUser'] as $key => $value) {
            $array[$key] = $value->userId;
        }

        $data['groupNotUser']  = \DB::connection($dbUser)->table('M_User')
            ->select([
                'M_User.userId',
                'M_User.userName',
                'M_User.color',
                'M_User.textColor',
                'M_User.borderColor'
            ])
            ->whereNotIn('M_User.userId', $array)
            ->get();

        return $data;
    }

    /*====================================================
    登録
    ====================================================*/
    //グループ新規登録
    public function addM_GroupInfo($dbUser, $loginUserId, $groupName, $groupRemarks, $userIdArray)
    {
        $now = \Carbon\Carbon::now();
        $result = ['code' => 0, 'resultData' => []];

        \DB::beginTransaction();
        try {
            //グループの登録
            $groupId = \DB::connection($dbUser)->table('M_Group')
                ->insertGetId([
                    'groupName' => $groupName,
                    'color' => "#FFFFFF",
                    'textColor' => "#000000",
                    'borderColor' => "#FFFFFF",
                    'groupRemarks' => parent::nullChangeString($groupRemarks),
                    'activeFlg' => 0,
                    'archiveDay' => null,
                    'createUser' => $loginUserId,
                    'updateUser' => $loginUserId,
                    'createDay' => $now,
                    'updateDay' => $now
                ]);

            //グループカラーセット
            $initColor = parent::colorInitSet($groupId, "group");
            \DB::connection($dbUser)->table('M_Group')->where('groupId', $groupId)->update(
                [
                    'color' => $initColor["color"],
                    'textColor' => $initColor["textColor"],
                    'borderColor' => $initColor["borderColor"]
                ]
            );

            //所属するユーザも同時に登録する場合マッピングテーブルに登録する
            if (!empty($userIdArray)) {
                foreach ($userIdArray as $userId) {
                    \DB::connection($dbUser)->table('T_UserGroupMap')
                        ->insert(
                            [
                                'userId' => $userId,
                                'groupId' => $groupId,
                            ]
                        );
                }
            }

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
    //グループ基本情報更新
    public function updateM_GroupInfo($dbUser, $loginUserId, $groupId, $groupName, $color, $textColor, $borderColor, $groupRemarks, $activeFlg)
    {

        $now = \Carbon\Carbon::now();

        $result = ['code' => 0, 'resultData' => []];

        \DB::beginTransaction();
        try {
            \DB::connection($dbUser)->table('M_Group')->where('groupId', $groupId)
                ->update([
                    'groupName' => $groupName,
                    'color' => $color,
                    'textColor' => $textColor,
                    'borderColor' => $borderColor,
                    'groupRemarks' => parent::nullChangeString($groupRemarks),
                    'activeFlg' => $activeFlg,
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

}
