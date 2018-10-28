@extends('layout')

@section('top-pageinfo')
    @lang('main.userProfile')
@endsection

@section('main')
<div class='checkers_data'>
    <table>
        <tr>
            <td>@lang('user.name'):</td>
            <td class='data'>{{ $data->name }}</td>
        </tr>
        <tr>
            <td>@lang('user.createdAt'):</td>
            <td class='data'>{{ $data->created_at }}</td>
        </tr>
        @if ($data->id == $userid)
        <tr>
            <td>@lang('user.updatedAt'):</td>
            <td class='data'>{{ $data->updated_at }}</td>
        </tr>
        @endif
        <tr>
            <td>@lang('user.likes'):</td>
            <td class='data'>{{ $data->likes }}</td>
        </tr>
        <tr>
            <td>@lang('user.role'):</td>
            <td class='data'>@lang('user.role' . $data->role)</td>
        </tr>
        @if ($data->id == $userid)
        <tr>
            <td>@lang('user.email'):</td>
            <td class='data'>{{ $data->email }}</td>
        </tr>
        @endif
    </table>
</div>
    @if ($data->id == $userid)
        <div class='checkers_mainbutton'>
            <a href="{{ url('/user/' . $userid . '/edit') }}">@lang('user.edit')</a>
        </div>
    @endif
@endsection
