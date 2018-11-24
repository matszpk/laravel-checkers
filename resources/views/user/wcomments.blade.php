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
    <div class='checkers_centered'>
        @lang('main.commentsRange', ['start' => $comments->firstItem(),
                'end' => $comments->lastItem()])
    </div>
    @foreach ($comments->all() as $comment)
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
    <div class='checkers_centered'>
        @if ($comments->previousPageUrl() != NULL)
            <a href="{{ $comments->previousPageUrl() }}">@lang('pagination.previous')</a>
        @endif
        @if ($comments->nextPageUrl() != NULL)
            <a href="{{ $comments->nextPageUrl() }}">@lang('pagination.next')</a>
        @endif
    </div>
</div>
@endsection
