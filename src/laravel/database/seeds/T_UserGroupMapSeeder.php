<?php

use Illuminate\Database\Seeder;

class T_UserGroupMapSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'userId' => 1,
            'groupId' => 1
        ];
        DB::connection('mysql_seed1')->table('T_UserGroupMap')->insert($data);
    }
}
