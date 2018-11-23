@extends('layout')

@section('top-pageinfo')
    @lang('main.userWCommentsTitle')
@endsection

@section('script')
@include('components.comments_scripts')
@endsection

@section('main')
<div class='checkers_subtitle'>
@lang('main.writtenComments', [ 'user' => $data->getName() ])</div>
<div id='checkers_comments'>
    @foreach ($data->writtenComments->all() as $comment)
        <div class='comment_info'>@lang('main.writtenTo')
            @if ($comment->commentable_type == 'App\User')
            {{ $cusers[$comment->commentable_id]->getName() }}
            @else
            {{ $cgames[$comment->commentable_id]->getName() }}
            @endif
            ,
            {{ $comment->created_at }}:
            @lang('main.likes'):
            <span id="checkers_comment_likes_{{ $comment->id }}">
                {{ $comment->likes }}</span>
            @can('giveOpinion',$comment)
            <div class='checkers_comment_dolike'
                id="checkers_comment_dolike_{{ $comment->id }}">
                @lang('main.doLike')</div>
            </span>
            @endcan
        </div>
        <div class='comment_content'>{{ $comment->content }}</div>
    @endforeach
</div>
@endsection
