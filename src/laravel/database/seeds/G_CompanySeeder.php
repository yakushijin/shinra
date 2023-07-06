<?php

use Illuminate\Database\Seeder;

class G_CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = \Carbon\Carbon::now();
        $data = [
            'companyName' => '株式会社テスト',
            'contractStatus' => 1,
            'dbUser' => 'dbUser1',
            'dbPassword' => 'dbPassword1!',
            'dbHost' => 'localhost',
            'mlHost' => 'localhost',
            'userMaxCount' => 100,
            'groupMaxCount' => 10,
            'tabMaxCount' => 10,
            'categoryMaxCount' => 100,
            'taskMaxCount' => 1000,
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
        DB::table('G_Company')->insert($data);
    }
}
