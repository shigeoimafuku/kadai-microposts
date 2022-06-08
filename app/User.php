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
        $this->loadCount(['favorites','microposts','followings','followers']);
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
    
    //このユーザがお気に入りの投稿
    public function favorites()
    {
        return $this->belongsToMany(Micropost::class,'favorites','user_id','micropost_id')->withTimestamps();
    }
    
     //$micropostIdで指定された投稿にお気に入りする
    public function favorite($micropostId)
    {
        //すでにお気に入りしているか
        $exist=$this->is_favoriting($micropostId);
        
        if($exist){
            //お気に入り済みの場合なにもしない
            return false;
        }
        else{    
            //お気に入りしていなければする
            $this->favorites()->attach($micropostId);
            return true;
        }
    }
    
    //$micropostIdで指定された投稿からお気に入りを外す
    public function unfavorite($micropostId)
    {
        //すでにお気に入りしてるかどうか
        $exist=$this->is_favoriting($micropostId);
        if($exist){
            //お気に入り済みの場合お気に入りを外す
            $this->favorites()->detach($micropostId);
            return true;
        }
        else{
            //上記以外はなにもしない
            return false;
        }
    }
    
    //指定された$micropostIdのユーザをこのユーザがお気に入りしてるか調べる。してたらtrueを返す。
    public function is_favoriting($micropostId)
    {
        //お気に入りしてる投稿の中に$micropostIdのものが存在するか
        return $this->favorites()->where('micropost_id',$micropostId)->exists();
    }
}
