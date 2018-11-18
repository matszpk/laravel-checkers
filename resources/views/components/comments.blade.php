@can('giveOpinion', $data)
<form  id='checkers_commentform' method='POST'>
    @csrf
    <label for='checkers_comment'>@lang('main.comment'):</label>
    <textarea id='checkers_comment' name='content'></textarea>
    <button>@lang('main.doComment')</button>
</form>
@endcan

<div id='checkers_comments'>
    @foreach ($data->comments->all() as $comment)
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
            </span>
            @endcan
        </div>
        <div class='comment_content'>{{ $comment->content }}</div>
    @endforeach
</div>
