@can('giveOpinion', $data)
<form  id='checkers_commentform' method='POST'>
    @csrf
    <label for='checkers_comment'>@lang('main.comment'):</label>
    <textarea id='checkers_comment' name='content'></textarea>
    <button>@lang('main.doComment')</button>
</form>
@endcan

<div id='checkers_comments'>
    @if (!$comments->isEmpty())
    <div class='checkers_centered'>
        @lang('main.commentsRange', ['start' => $comments->firstItem(),
                'end' => $comments->lastItem()])
    </div>
    @endif
    @foreach ($comments->items() as $comment)
        <div class='comment_info'>
            <a href="{{ route('user.user', $comment->writer_id) }}">
                    {{ $writers[$comment->writer_id]->getName() }}</a>,
            {{ $comment->created_at }}:
            @lang('main.likes'):
            <span id="checkers_comment_likes_{{ $comment->id }}">
                {{ $comment->likes }}</span>
            @can('giveOpinion',$comment)
            <div class='checkers_comment_dolike'
                id="checkers_comment_dolike_{{ $comment->id }}">
                @lang('main.doLike')</div>
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
