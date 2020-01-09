<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Foundation\Auth\User as Authenticatable; //  是授权相关功能的引用

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use Notifiable; // 是消息通知相关功能引用

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     * 过滤用户提交的字段
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     * 当我们需要对用户密码或其它敏感信息在用户实例通过数组或 JSON 显示时进行隐藏
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * $this->attributes['email'] 获取到用户的邮箱
     * 用 trim 方法剔除邮箱的前后空白内容
     * 用 strtolower 方法将邮箱转换为小写
     * 将小写的邮箱使用 md5 方法进行转码
     */
    public function gravatar($size = '100') {
        $hash = md5(strtolower(trim($this->attributes['email'])));

        return "https://www.gravatar.com/avatar/$hash?s=$size";
    }


    public static function boot() {
        parent::boot();
        static::creating(function ($user) {
            $user->activation_token = Str::random(10);
        });
    }


    public function statuses() {
        return $this->hasMany(Status::class);
    }

    public function feed() {
        return $this->statuses()
            ->orderBy('created_at', 'desc');
    }
}
