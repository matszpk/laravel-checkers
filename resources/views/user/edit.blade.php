@extends('layout')

@section('top-pageinfo')
    @lang('main.userEditTitle')
@endsection

@section('main')
    <div class='checkers_subtitle'>@lang('user.userEdit', ['user' => $data->getName()])</div>
    <form method='POST' action="{{ route('user.update', $data->id) }}"
            class='checkers_form' name='userAccountForm'>
        @csrf
        <table>
            <tr>
                <td><label for='user_name'>@lang('user.name')</label></td>
                <td><input type='text' id='user_name' name='name'
                    value="{{ $data->getName() }}"/></td>
            </tr>
            @can('changeEmail', $data)
            <tr>
                <td><label for='user_email'>
                    @lang('auth.registerEmail')</label></td>
                <td>
                    <input type='text' id='user_email' name='email'
                        value="{{ $data->email }}"/>
                </td>
            </tr>
            @endcan
            <tr>
                <td><label for='user_password'>
                    @lang('auth.registerPassword')</label></td>
                <td>
                    <input type='password' id='user_password' name='password'/>
                </td>
            </tr>
            <tr>
                <td><label for='user_password_confirm'>
                    @lang('auth.registerPasswordConfirm')</label></td>
                <td>
                    <input type='password' id='user_password_confirm'
                        name='password_confirmation'/>
                </td>
            </tr>
            <tr class="checkers_row_button">
                <td colspan='2'><button>@lang('user.update')</button></td>
            </tr>
        </table>
    </form>

    @include('components.validation-errors')
@endsection
