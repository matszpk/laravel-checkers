@extends('layout')

@section('top-pageinfo')
    @if ($viewPurpose == 'index')
        @lang('main.gamesTitle')
    @elseif ($viewPurpose == 'toContinue')
        @lang('main.gamesToPlayTitle')
    @elseif ($viewPurpose == 'toJoin')
        @lang('main.gamesToJoinTitle')
    @else
        @lang('main.gamesToReplayTitle')
    @endif
@endsection

@section('main')
<div class='checkers_maintable'>
    @lang('game.gamesRange', ['start' => $pag->firstItem(), 'end' => $pag->lastItem()])
    <table>
        <thead>
            <tr>
                <th>@lang('main.nr')</th>
                <th>@lang('game.createdAt')</th>
                <th>@lang('game.endAt')</th>
                <th>@lang('game.comments')</th>
                <th>@lang('game.likes')</th>
                <th>@lang('game.player1')</th>
                <th>@lang('game.player2')</th>
                <th>@lang('game.result')</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pag->items() as $g)
            <tr>
                <td>{{ $pag->firstItem() + $loop->index }}</td>
                @can('play', $g)
                    <td><a href="{{ route(($g->result!==NULL || $viewPurpose=='toReplay') ?
                                'game.replay' : 'game.play', $g->id) }}">
                            {{ $g->created_at }}</a></td>
                @elsecan('join', $g)
                    <td><a href="{{ route('game.join', $g->id) }}">
                            {{ $g->created_at }}</a></td>
                @else
                    <td><a href="{{ route('game.replay', $g->id) }}">
                            {{ $g->created_at }}</a></td>
                @endcan
                <td>{{ $g->end_at }}</td>
                <td>{{ $g->comments_count }}</td>
                <td>{{ $g->likes }}</td>
                <td>{{ $g->player1!=NULL ? $g->player1->name : '' }}</td>
                <td>{{ $g->player2!=NULL ? $g->player2->name : '' }}</td>
                <td>@lang('game.result_'.$g->result)</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if ($pag->previousPageUrl() != NULL)
        <a href="{{ $pag->previousPageUrl() }}">@lang('pagination.previous')</a>
    @endif
    @if ($pag->nextPageUrl() != NULL)
        <a href="{{ $pag->nextPageUrl() }}">@lang('pagination.next')</a>
    @endif
</div>
@endsection
