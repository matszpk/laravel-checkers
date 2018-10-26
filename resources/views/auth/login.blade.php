@extends('layout')

@section('main')
    <form method='POST' class='checkers_form' name='loginForm'>
        @csrf
        <table>
            <tr>
                <td><label for='login_name'>@lang('auth.loginName')</label></td>
                <td><input type='text' id='login_name' name='name'></input></td>
            </tr>
            <tr>
                <td><label for='login_password'>@lang('auth.loginPassword')</label></td>
                <td><input type='password' id='login_password' name='password'></input></td>
            </tr>
            <tr class="checkers_row_button">
                <td colspan='2'><button>@lang('auth.doLogin')</button></td>
            </tr>
        </table>
    </form>

    @include('components.validation-errors')
@endsection

@section('top-pageinfo')
    @lang('auth.loginTitle')
@endsection
