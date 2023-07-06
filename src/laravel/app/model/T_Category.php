<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class T_Category extends Model
{
    protected $table = 'T_Category';

    // protected $dates = ['categoryDeadline'];

    protected $dateFormat = 'YYYY/MM/DD';
    
}
