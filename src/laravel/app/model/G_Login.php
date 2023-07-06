<?php

namespace App\model;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\PasswordResetEmail;

class G_Login extends Authenticatable
{
    protected $table = 'G_Login';

    protected $primaryKey = 'generalId';

    // protected $name = 'userName'; 

    const CREATED_AT = 'createDay';
    const UPDATED_AT = 'updateDay';

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'userName','email', 'password','authority','activeFlg','accountStatus','email_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordResetEmail($token));
    }
}
