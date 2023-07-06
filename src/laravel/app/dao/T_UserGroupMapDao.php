<?php

namespace Dao;

class T_UserGroupMapDao extends BaseDao
{
    /*====================================================
    更新
    ====================================================*/
    //グループ所属ユーザ変更
    public function updateT_UserGroupMap($dbUser, $groupId, $userIdArray)
    {

        $result = ['code' => 0, 'resultData' => []];

        \DB::beginTransaction();
        try {
            //一旦対象グループのレコードを全て削除
            \DB::connection($dbUser)->table('T_UserGroupMap')
                ->where('groupId', $groupId)->delete();

            //対象グループに対し各ユーザを追加する
            foreach ($userIdArray as $userId) {
                \DB::connection($dbUser)->table('T_UserGroupMap')
                    ->insert(
                        [
                            'userId' => $userId,
                            'groupId' => $groupId,
                        ]
                    );
            }

            \DB::commit();
        } catch (\Exception $e) {
            $result['code'] = 1;
            \DB::rollback();
        }

        return $result;
    }
}
