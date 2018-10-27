@extends('layout')

@section('top-pageinfo')
    @lang('auth.resetPasswordTitle')
@endsection

@section('main')
<div class='checkers_mainpage'>
    <form method='POST' action="{{ route('password.email') }}"
            class='checkers_form' name='resetPasswordForm'>
        @csrf
        <table>
            <tr>
                <td><label for='reset_name'>@lang('auth.resetEmail')</label></td>
                <td><input type='text' id='reset_email' name='email'/></td>
            </tr>
            <tr class="checkers_row_button">
                <td colspan='2'><button>@lang('auth.doSendReset')</button></td>
            </tr>
        </table>
    </form>
</div>

@include('components.validation-errors')
@endsection
