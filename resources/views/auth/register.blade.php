@extends('layout')

@section('top-pageinfo')
    @lang('auth.registerTitle')
@endsection

@section('extra-scripts')
    {!! NoCaptcha::renderJs() !!}
@endsection

@section('main')
    <form method='POST' action="{{ route('register') }}"
            class='checkers_form' name='registerForm'>
        @csrf
        <table>
            <tr>
                <td><label for='register_name'>@lang('auth.registerName')</label></td>
                <td><input type='text' id='register_name' name='name'/></td>
            </tr>
            <tr>
                <td><label for='register_email'>
                    @lang('auth.registerEmail')</label></td>
                <td>
                    <input type='text' id='register_email' name='email'/>
                </td>
            </tr>
            <tr>
                <td><label for='register_password'>
                    @lang('auth.registerPassword')</label></td>
                <td>
                    <input type='password' id='register_password' name='password'/>
                </td>
            </tr>
            <tr>
                <td><label for='register_password_confirm'>
                    @lang('auth.registerPasswordConfirm')</label></td>
                <td>
                    <input type='password' id='register_password_confirm'
                        name='password_confirmation'/>
                </td>
            </tr>
            <tr class="checkers_row_button">
                <td colspan='2'><button>@lang('auth.doRegister')</button></td>
            </tr>
        </table>
        
        {!! NoCaptcha::display() !!}
    </form>

    @include('components.validation-errors')
@endsection

