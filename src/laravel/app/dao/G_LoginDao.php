<?php

namespace Dao;

class G_LoginDao extends BaseDao
{
    /*====================================================
    取得
    ====================================================*/
    //メール認証でのユーザ登録時のログイン情報取得
    public function getG_LoginTockenCheck($token)
    {
        $data = \DB::table('G_Login')
            ->select([
                'email',
                'password',
                'accountStatus',
                'email_token',
                'createDay',
                'updateDay'
            ])
            ->where('email_token', $token)
            ->where('companyId', 0)
            ->where('userId', 0)
            ->where('accountStatus', 0)
            ->get();

        return $data[0];
    }

    //ログインID取得
    public function getG_LoginInfo($companyId, $userId)
    {
        $data = \DB::table('G_Login')
            ->select([
                'email'
            ])
            ->where('companyId', $companyId)
            ->where('userId', $userId)
            ->get();

        return $data[0];
    }

    //ユーザ作成時にログインIDの重複チェック用
    public function getG_LoginLoginIdCheck($loginId)
    {
        $data = \DB::table('G_Login')
            ->select([
                'email'
            ])
            ->where('email', $loginId)
            ->exists();

        return $data;
    }

    /*====================================================
    更新
    ====================================================*/

    //初回DB登録時のデータ更新処理
    public function updateG_LoginCompanyId($email,$companyId)
    {
        $now = \Carbon\Carbon::now();
        $result = ['code' => 0, 'resultData' => []];

        \DB::beginTransaction();
        try {
            \DB::table('G_Login')
                ->where('email', $email)
                ->where('accountStatus', 9)
                ->update([
                    'companyId' => $companyId,
                    'userId' => 1,
                    'updateDay' => $now
                ]);

            \DB::commit();
        } catch (\Exception $e) {
            $result['code'] = 1;
            \DB::rollback();
        }

        return $result;
    }

    //初回DB登録時のトークンクリアとフラグ変更処理
    public function updateG_LoginToken($email, $token)
    {
        $now = \Carbon\Carbon::now();
        $result = ['code' => 0, 'resultData' => []];

        \DB::beginTransaction();
        try {
            \DB::table('G_Login')
                ->where('email', $email)
                ->where('email_token', $token)
                ->where('accountStatus', 0)
                ->where('companyId', 0)
                ->where('userId', 0)
                ->update([
                    'accountStatus' => 9,
                    'email_token' => null,
                    'updateDay' => $now
                ]);

            \DB::commit();
        } catch (\Exception $e) {
            $result['code'] = 1;
            \DB::rollback();
        }

        return $result;
    }

    //ログインID更新※システムユーザ以外
    public function updateG_LoginLoginId($companyId, $userId, $loginId)
    {
        $now = \Carbon\Carbon::now();
        $result = ['code' => 0, 'resultData' => []];

        \DB::beginTransaction();
        try {
            \DB::table('G_Login')
                ->where('userId', $userId)
                ->where('companyId', $companyId)
                ->update([
                    'email' => $loginId,
                    'updateDay' => $now
                ]);
            \DB::commit();
        } catch (\Exception $e) {
            $result['code'] = 1;
            \DB::rollback();
        }

        return $result;
    }

    //パスワード更新※システムユーザ以外
    public function updateG_LoginPassword($companyId, $userId, $password)
    {
        $now = \Carbon\Carbon::now();
        $result = ['code' => 0, 'resultData' => []];

        \DB::beginTransaction();
        try {
            \DB::table('G_Login')
                ->where('userId', $userId)
                ->where('companyId', $companyId)
                ->update([
                    'password' =>  bcrypt($password),
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

}
