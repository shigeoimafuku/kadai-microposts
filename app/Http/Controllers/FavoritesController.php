<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FavoritesController extends Controller
{
    //お気に入りの投稿に登録するアクション
    public function store($id)
    {
        
        //認証済み(閲覧者)がidの投稿をお気に入り登録する
        \Auth::user()->favorite($id);
        //前のURLへリダイレクト
        return back();
    }
    
    //お気に入り投稿のお気に入りを外す
    public function destroy($id)
    {
        //認証済み(閲覧者)がお気に入りを外す
        \Auth::user()->unfavorite($id);
         return back();
    }
}
