<?php

namespace Dao;

class M_UserDao extends BaseDao
{
    /*====================================================
    取得
    ====================================================*/
    //ユーザ件数取得
    public function getM_UserCount($dbUser)
    {
        $userCount = \DB::connection($dbUser)->table('M_User')
            ->count();

        return $userCount;
    }

    //ユーザ詳細情報取得
    public function getM_UserInfo($dbUser, $userId)
    {
        $user = \DB::connection($dbUser)->table('M_User')
            ->select([
                'M_User.userId',
                'M_User.userName',
                'M_User.authority',
                'M_User.activeFlg',
                'M_User.color',
                'M_User.textColor',
                'M_User.borderColor',
                'M_User.userRemarks',
                'M_User.archiveDay',
                'M_User.defaultDeadlineFlg',
                'M_User.deleteMessageFlg',
                'M_User.doneAutoActiveFlg',
                'M_User.createDay',
                'M_User.updateDay',
                'M_User_createUser.userName as createUserName',
                'M_User_createUser.color as createUserColor',
                'M_User_createUser.textColor as createUserTextColor',
                'M_User_createUser.borderColor as createUserBorderColor',
                'M_User_updateUser.userName as updateUserName',
                'M_User_updateUser.color as updateUserColor',
                'M_User_updateUser.textColor as updateUserTextColor',
                'M_User_updateUser.borderColor as updateUserBorderColor',
            ])
            ->where('M_User.userId', '=', $userId)
            ->join('M_User as M_User_createUser', 'M_User_createUser.userId', '=', 'M_User.createUser')
            ->join('M_User as M_User_updateUser', 'M_User_updateUser.userId', '=', 'M_User.updateUser')
            ->get();

        return $user[0];
    }

    //有効化されている全てのユーザ取得
    public function getM_UserAll($dbUser)
    {
        $user = \DB::connection($dbUser)->table('M_User')
            ->select([
                'M_User.userId',
                'M_User.userName',
                'M_User.activeFlg',
                'M_User.color',
                'M_User.textColor',
                'M_User.borderColor',
            ])
            ->where('activeFlg', 0)
            ->get();

        return $user;
    }

    //有効化されている全ての管理者ユーザ取得
    public function getM_UserAdminAll($dbUser)
    {
        $user = \DB::connection($dbUser)->table('M_User')
            ->select([
                'M_User.userId',
                'M_User.userName',
                'M_User.activeFlg',
                'M_User.color',
                'M_User.textColor',
                'M_User.borderColor',
            ])
            ->where('authority', 1)
            ->where('activeFlg', 0)
            ->get();

        return $user;
    }

    //ユーザ検索
    public function getM_UserSearch($dbUser, $userName, $userActive, $userAuthority)
    {

        $sql = "SELECT userId,userName,activeFlg,authority
        FROM M_User
        WHERE activeFlg = " . $userActive .
            " AND authority = " . $userAuthority;
        if ($userName != "") {
            $sql = $sql . " AND userName like '%" . $userName . "%'";
        }

        $user = \DB::connection($dbUser)->select(\DB::raw($sql));

        return $user;
    }

    //ユーザの画面制御に関連する情報取得
    public function getM_UserDispInfo($dbUser, $userId)
    {

        $user = \DB::connection($dbUser)->table('M_User')
            ->select([
                'M_User.userId',
                'M_User.userName',
                'M_User.defaultDeadlineFlg',
                'M_User.deleteMessageFlg',
                'M_User.doneAutoActiveFlg',
                'M_User.authority',
                'M_User.activeFlg',
                'M_User.color',
                'M_User.textColor',
                'M_User.borderColor'
            ])
            ->where('M_User.userId', '=', $userId)
            ->get();

        return $user[0];
    }

    //ユーザに紐づくグループ情報取得
    public function getM_UserGroup($dbUser, $userId)
    {
        $data = \DB::connection($dbUser)->table('M_User')
            ->select([
                'M_Group.groupId',
                'M_Group.groupName',
                'M_Group.color',
                'M_Group.textColor',
                'M_Group.borderColor'
            ])
            ->join('T_UserGroupMap', 'T_UserGroupMap.userId', '=', 'M_User.userId')
            ->join('M_Group', 'M_Group.groupId', '=', 'T_UserGroupMap.groupId')
            ->where('M_User.userId', '=', $userId)
            ->get();

        return $data;
    }

    /*====================================================
    登録
    ====================================================*/
    //ユーザ新規登録※システムユーザ以外
    public function addM_UserInfo($dbUser, $loginUserId, $userName, $userRemarks, $loginId, $password, $companyId)
    {
        $now = \Carbon\Carbon::now();
        $result = ['code' => 0, 'resultData' => []];

        \DB::beginTransaction();
        try {
            //ユーザ情報を登録
            $userId = \DB::connection($dbUser)->table('M_User')
                ->insertGetId([
                    'userName' => $userName,
                    'authority' => 0,
                    'activeFlg' => 0,
                    'archiveDay' => null,
                    'color' => "#FFFFFF",
                    'textColor' => "#000000",
                    'borderColor' => "#FFFFFF",
                    'userRemarks' => parent::nullChangeString($userRemarks),
                    'defaultDeadlineFlg' => 1,
                    'deleteMessageFlg' => 1,
                    'doneAutoActiveFlg' => 1,
                    'createUser' => $loginUserId,
                    'updateUser' => $loginUserId,
                    'createDay' => $now,
                    'updateDay' => $now
                ]);

            //ユーザカラーをセット
            $initColor = parent::colorInitSet($userId, "user");
            \DB::connection($dbUser)->table('M_User')->where('userId', $userId)->update(
                [
                    'color' => $initColor["color"],
                    'textColor' => $initColor["textColor"],
                    'borderColor' => $initColor["borderColor"]
                ]
            );

            //ログイン情報を登録
            \DB::table('G_Login')->insert([
                'email' => $loginId,
                'password' => bcrypt($password),
                'accountStatus' => 1,
                'email_token' => "",
                'userId' => $userId,
                'companyId' => $companyId,
                'createDay' => $now,
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
    更新
    ====================================================*/
    //ユーザ更新
    public function updateM_UserInfo($dbUser, $loginUserId, $userId, $userName, $authority, $userRemarks, $activeFlg, $defaultDeadlineFlg, $deleteMessageFlg, $doneAutoActiveFlg, $color, $textColor, $borderColor)
    {
        $now = \Carbon\Carbon::now();
        $result = ['code' => 0, 'resultData' => []];

        \DB::beginTransaction();
        try {
            \DB::connection($dbUser)->table('M_User')->where('userId', $userId)
                ->update([
                    'userName' => $userName,
                    'authority' => $authority,
                    'activeFlg' => $activeFlg,
                    'color' => $color,
                    'textColor' => $textColor,
                    'borderColor' => $borderColor,
                    'userRemarks' => parent::nullChangeString($userRemarks),
                    'defaultDeadlineFlg' => $defaultDeadlineFlg,
                    'deleteMessageFlg' => $deleteMessageFlg,
                    'doneAutoActiveFlg' => $doneAutoActiveFlg,
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
