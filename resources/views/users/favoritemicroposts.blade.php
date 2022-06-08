@if (count($microposts)>0)
    <ul class="list-unstyled">
        @foreach ($microposts as $micropost)
            <li class="media">
                {{-- ユーザーのメールアドレスを元にGravatarを取得して表示 --}}
                <img class="mr-2 rounded" src="{{ Gravatar::get($user->email,['size'=>50]) }}" alt="">
                <div class="media-body">
                    <div>
                        {{-- 投稿者の所有者のユーザ詳細ページへのリンク --}}
                        {!! link_to_route('users.show',$micropost->user->name,['user'=>$micropost->user->id]) !!}
                        <span class="text-muted">posted at {{ $micropost->created_at }}</span>
                    </div>
                    <div>
                        {{ $micropost->content }}
                    </div>
                    @include('favorites.favorites_button')
                </div>
            </li>
            @endforeach
    </ul>
    {{-- ページネーションのリンク --}}
    {{ $microposts->links() }}
@endif    