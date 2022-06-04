@if (Auth::id() != $user->id) {{-- Auth::id()とは閲覧者のid $userはshowアクションに定義されている詳細ページのユーザ --}}
    @if (Auth::user()->is_following($user->id))　{{--もし閲覧者がこのユーザをフォローしてたら--}}
        {{--アンフォローボタン--}}
        {!! Form::open(['route'=>['user.unfollow',$user->id],'method'=>'delete']) !!}
            {!! Form::submit('Unfollow',['class'=>"btn btn-danger btn-block"]) !!}
        {!! Form::close() !!}
    @else
        {{--フォローボタン--}}
        {!! Form::open(['route'=>['user.follow',$user->id]]) !!}
            {!! Form::submit('Follow',['class'=>"btn btn-primary btn-block"]) !!}
        {!! Form::close() !!}  
    @endif
@endif    
        