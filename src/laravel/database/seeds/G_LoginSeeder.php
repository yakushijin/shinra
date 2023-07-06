<?php

use Illuminate\Database\Seeder;

class G_LoginSeeder extends Seeder
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
            'email' => 'user@test.com',
            'password' => bcrypt('user'),
            'userId' => 1,
            'companyId' => 1,
            'accountStatus' => 0,
            'email_token' => '',
            'lastLogin' => null,
            'createDay' => $now,
            'updateDay' => $now
        ];
        DB::table('G_Login')->insert($data);
    }
}
