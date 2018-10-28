@extends('layout')

@section('top-pageinfo')
    @lang('main.userEditTitle')
@endsection

@section('main')
    <p>@lang('user.userEdit', ['user' => $data->name])</p>
    <form method='POST' action="{{ route('userUpdate', [$data->id]) }}"
            class='checkers_form' name='userAccountForm'>
        @csrf
        <table>
            <tr>
                <td><label for='user_name'>@lang('user.name')</label></td>
                <td><input type='text' id='user_name' name='name'
                    value="{{ $data->name }}"/></td>
            </tr>
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
