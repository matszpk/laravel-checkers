@can('giveOpinion', $data)
<form id='checkers_commentform' method='POST'>
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
                    {{ $comment->writtenBy()->getResults()->getName() }}</a>,
            {{ $comment->created_at }}:</div>
        <div class='comment_content'>{{ $comment->content }}</div>
    @endforeach
</div>
