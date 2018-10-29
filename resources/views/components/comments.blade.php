@can('giveOpinion', $data)

<form method='POST'>
    @csrf
    <label for='checkers_comment'>@lang('main.comment')</label>
    <input type='text' id='checkers_comment' name='content'/>
</form>
@endcan

<div>
    @foreach ($data->comments->all() as $comment)
        <span>Written by {{ $comment->writtenBy()->getResults()->name }} at
            {{ $comment->created_at }}:<br/>
        {{ $comment->content }}<br/></span>
    @endforeach
</div>
