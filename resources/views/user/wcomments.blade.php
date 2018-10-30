@extends('layout')

@section('top-pageinfo')
    @lang('main.userWCommentsTitle')
@endsection

@section('main')
<p>@lang('main.writtenComments', [ 'user' => $username ])</p>
<div id='checkers_comments'>
    @foreach ($data->writtenComments->all() as $comment)
        <div class='comment_info'>@lang('main.writtenTo')
            {{ $comment->commentable()->getResults()->name }},
            {{ $comment->created_at }}:</div>
        <div class='comment_content'>{{ $comment->content }}</div>
    @endforeach
</div>
@endsection
