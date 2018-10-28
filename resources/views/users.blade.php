@extends('layout')

@section('top-pageinfo')
    @lang('main.users')
@endsection

@section('main')
<div class='checkers_maintable'>
    @lang('user.usersRange', ['start' => $data->firstItem(), 'end' => $data->lastItem()])
    <table>
        <thead>
            <tr>
                <th>@lang('main.nr')</th>
                <th>@lang('user.name')</th>
                <th>@lang('user.createdAt')</th>
                <th>@lang('user.likes')</th>
                <th>@lang('user.role')</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data->items() as $u)
            <tr>
                <td>{{ $data->firstItem() + $loop->index }}</td>
                <td><a href="{{ url('/user/' . $u->id) }}">{{ $u->name }}</a></td>
                <td>{{ $u->created_at }}</td>
                <td>{{ $u->likes }}</td>
                <td>@lang('user.role' .$u->role)</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if ($data->previousPageUrl() != NULL)
        <a href="{{ $data->previousPageUrl() }}">@lang('pagination.previous')</a>
    @endif
    @if ($data->nextPageUrl() != NULL)
        <a href="{{ $data->nextPageUrl() }}">@lang('pagination.next')</a>
    @endif
</div>

@endsection
