@extends('layout')

@section('main')
    <form method='POST' class='checkers_form' name='registerForm'>
        @csrf
        <table>
            <tr>
                <td><label for='register_name'>@lang('auth.registerName')</label></td>
                <td><input type='text' id='register_name' name='name'></input></td>
            </tr>
            <tr>
                <td><label for='register_email'>
                    @lang('auth.registerEmail')</label></td>
                <td>
                    <input type='text' id='register_email' name='email'></input>
                </td>
            </tr>
            <tr>
                <td><label for='register_password'>
                    @lang('auth.registerPassword')</label></td>
                <td>
                    <input type='password' id='register_password' name='password'></input>
                </td>
            </tr>
            <tr>
                <td><label for='register_password_confirm'>
                    @lang('auth.registerPasswordConfirm')</label></td>
                <td>
                    <input type='password' id='register_password_confirm'
                        name='password_confirmation'></input>
                </td>
            </tr>
            <tr class="checkers_row_button">
                <td colspan='2'><button>@lang('auth.doRegister')</button></td>
            </tr>
        </table>
    </form>

    @include('components.validation-errors')
@endsection

@section('top-pageinfo')
    @lang('auth.registerTitle')
@endsection

