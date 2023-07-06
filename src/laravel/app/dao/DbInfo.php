<?php

namespace Dao;

class DbInfo 
{
    public function dbConnectionSet($dbUser,$dbPassword,$dbHost){
        config(['database.connections.'.$dbUser => [
           'driver' => 'mysql',
           'host' => $dbHost,
           'port' => '3306',
           'database' => $dbUser,
           'username' => $dbUser,
           'password' => $dbPassword,
           'unix_socket' => '',
           'charset' => 'utf8mb4',
           'collation' => 'utf8mb4_unicode_ci',
           'prefix' => '',
           'strict' => true,
           'engine' => null,
       ]]);
     }
}
