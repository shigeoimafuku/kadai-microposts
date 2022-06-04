<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserFollowController extends Controller
{
    //ユーザフォローアクション
    public function store($id)
    {
        //認証済みユーザがidのユーザをフォローする
        \Auth::user()->follow($id);
        //前のURLへリダイレクト
        return back();
    }
    
    //ユーザアンフォローアクション
    public function destroy($id)
    {
        //認証済みユーザ(閲覧者)がidのユーザをアンフォローする
        \Auth::user()->unfollow($id);
        //前のURLへリダイレクト
        return back();
    }
    
   
}

