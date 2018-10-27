@extends('layout')

@section('top-pageinfo')
    @lang('auth.resetPasswordTitle')
@endsection

@section('main')
<div class='checkers_mainpage'>
    <form method='POST' action="{{ route('password.update') }}"
            class='checkers_form' name='resetPasswordForm'>
        @csrf
        <table>
            <input type='hidden' name='token' value="{{ $token }}"/>
            <tr>
                <td><label for='reset_email'>@lang('auth.resetEmail')</label></td>
                <td><input type='text' id='reset_email' name='email'/></td>
            </tr>
            <tr>
                <td><label for='register_password'>
                    @lang('auth.resetPassword')</label></td>
                <td>
                    <input type='password' id='register_password' name='password'/>
                </td>
            </tr>
            <tr>
                <td><label for='register_password_confirm'>
                    @lang('auth.resetPasswordConfirm')</label></td>
                <td>
                    <input type='password' id='register_password_confirm'
                        name='password_confirmation'/>
                </td>
            </tr>
            <tr class="checkers_row_button">
                <td colspan='2'><button>@lang('auth.doReset')</button></td>
            </tr>
        </table>
    </form>
</div>

@include('components.validation-errors')
@endsection
