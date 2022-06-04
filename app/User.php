<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
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
    
    //このユーザが所有する投稿(Micropostモデルとの関係を定義)
    public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }
    
    //自分がフォローしているユーザを取得
    public function followings()
    {
        return $this->belongsToMany(User::class,'user_follow','user_id','follow_id')->withTimestamps();
    }
    
    //自分をフォローしているユーザを取得
    public function followers()
    {
        return $this->belongsToMany(User::class,'user_follow','follow_id','user_id')->withTimestamps();
    }
    
    public function loadRelationshipCounts()
    {
        $this->loadCount(['microposts','followings','followers']);
    }
    
    //$userIdで指定されたユーザをフォローする
    public function follow($userId)
    {
        //すでにフォローしているか
        $exist=$this->is_following($userId);
        //対象が自分自身かどうか
        $its_me=$this->id==$userId;
        
        if($exist||$its_me){// ||→「または」
            //なにもしない
            return false;
        }
        else{
            //上記以外はフォローする
            $this->followings()->attach($userId);
            return true;
        }
    }
    
    //$userIdで指定されたユーザをアンフォローする
    public function unfollow($userId)
    {
        //すでにフォローしているか
        $exist=$this->is_following($userId);
        //対象が自分自身かどうか
        $its_me=$this->id==$userId;
        
        if ($exist && !$its_me){ // &&「かつ」 !「ではない」
            //フォロー済み、かつ、自分自身ではない場合はフォローを外す
            $this->followings()->detach($userId);
            return true;
        }
        else{
            //上記以外はなにもしない
            return false;
        }
    }
    
    //指定された$userIdのユーザをこのユーザがフォロー中かどうか調べる
    public function is_following($userId)
    {
        //フォロー中のユーザの中に$userIdのものが存在するか
        return $this->followings()->where('follow_id',$userId)->exists();
    }
    
    // $thisとフォロー中ユーザの投稿に絞り込む
    public function feed_microposts()
    {
        //このユーザがフォロー中のユーザのidを取得して配列にする
        $userIds=$this->followings()->pluck('users.id')->toArray();
        //このユーザのidもその配列に追加
        $userIds[]=$this->id;
        //それらのユーザが所有する投稿に絞り込む
        return Micropost::whereIn('user_id',$userIds);
    }
}
