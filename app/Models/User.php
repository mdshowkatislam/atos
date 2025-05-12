<?php

namespace App\Models;

use App\Models\UserRole;
use App\Traits\CreatedUpdatedBy;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;


class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;
    use CreatedUpdatedBy;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // protected $fillable = [
    //     'name','username', 'email', 'password','mobile','image','status', 'bup_id', 'designation', 'department', 'address'
    // ];
    protected $fillable = [
        'name', 'email', 'password', 'mobile', 'image', 'status', 'bup_id', 'profile_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

  
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function sendPasswordResetNotification($token)
    {

        $data = [
            $this->email
        ];

        Mail::send('email.reset-password', [
            'fullname'      => $this->name,
            'reset_url'     => route('password.reset', ['token' => $token, 'email' => $this->email]),
        ], function ($message) use ($data) {
            // $message->from('no-reply@bup.edu.bd','BUP');
            $message->subject('Reset Password Request');
            $message->to($data[0]);
        });
    }
public function roles()
{
    return $this->belongsToMany(Role::class, 'user_roles');
}
public function hasRole($roleName)
{
    return $this->roles && $this->roles->contains('name', $roleName);
}
}









