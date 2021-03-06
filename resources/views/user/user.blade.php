@extends('layout')

@section('script')
@can('giveOpinion', $data)
$(function() {
    $('#checkers_like_button').click(function() {
        checkersAxiosPost("{{ route('user.like', $data->id) }}",null,
            function(response) {
                $('#checkers_userlikes').text(response.data.likes);
            });
    });
});
@endcan

@include('components.comments_scripts')
@endsection

@section('top-pageinfo')
    @lang('main.userProfileTitle')
@endsection

@section('main')
<div class='checkers_data'>
    <table>
        <tr>
            <td>@lang('user.name'):</td>
            <td class='data'>{{ $data->getName() }}</td>
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
            <td class='data'><span id='checkers_userlikes'>{{ $data->likes }}</span>
                @can('giveOpinion', $data)
                <div class='checkers_button' id='checkers_like_button'>
                        @lang('main.doLike')</div>
                @endcan
            </td>
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
        <tr>
            <td>@lang('user.emailVerified'):</td>
            <td class='data'>
                    {{ $data->hasVerifiedEmail()?__('main.yes'):__('main.no') }}</td>
        </tr>
        @endcan
    </table>
</div>
<div class='checkers_centered'>
    @can('update', $data)
    <div class='checkers_buttons'>
        <div class='checkers_mainbutton'>
            <a href="{{ route('user.wcomments', $data->id) }}">
                    @lang('user.wcomments')</a>
        </div>
        <div class='checkers_mainbutton'>
            <a href="{{ route('user.edit', $data->id) }}">
                    @lang('user.edit')</a>
        </div>
    </div>
    @endcan
</div>

    @include('components.comments')
@endsection
