@extends('layout')

@section('top-pageinfo')
    @lang('main.usersTitle')
@endsection

@section('main')
<div class='checkers_maintable'>
    @lang('user.usersRange', ['start' => $pag->firstItem(), 'end' => $pag->lastItem()])
    <table>
        <thead>
            <tr>
                <th>@lang('main.nr')</th>
                <th>@lang('user.name')</th>
                <th>@lang('user.createdAt')</th>
                <th>@lang('user.comments')</th>
                <th>@lang('user.likes')</th>
                <th>@lang('user.role')</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pag->items() as $u)
            <tr>
                <td>{{ $pag->firstItem() + $loop->index }}</td>
                <td><a href="{{ url('/user/' . $u->id) }}">{{ $u->name }}</a></td>
                <td>{{ $u->created_at }}</td>
                <td>{{ $u->comments_count }}</td>
                <td>{{ $u->likes }}</td>
                <td>@lang('user.role' .$u->role)</td>
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
