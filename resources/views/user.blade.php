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
        @can('viewUpdateAt', $data)
        <tr>
            <td>@lang('user.updatedAt'):</td>
            <td class='data'>{{ $data->updated_at }}</td>
        </tr>
        @endcan
        <tr>
            <td>@lang('user.likes'):</td>
            <td class='data'>{{ $data->likes }}</td>
        </tr>
        <tr>
            <td>@lang('user.role'):</td>
            <td class='data'>@lang('user.role' . $data->role)</td>
        </tr>
        @can('viewEmail', $data)
        <tr>
            <td>@lang('user.email'):</td>
            <td class='data'>{{ $data->email }}</td>
        </tr>
        @endcan
    </table>
</div>
    @can('update', $data)
        <div class='checkers_mainbutton'>
            <a href="{{ url('/user/' . $userid . '/edit') }}">@lang('user.edit')</a>
        </div>
    @endcan
@endsection