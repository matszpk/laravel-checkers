@extends('layout')

@section('main')
    <form method='POST' id='checkers_loginForm' name='loginForm'>
        @csrf
        <label for='login_name'>@lang('auth.loginName')</label>
        <input type='text' id='login_name' name='name'></input><br>
        <label for='login_password'>@lang('auth.loginPassword')</label>
        <input type='text' id='login_password' name='password'></input><br/>
        <button>@lang('auth.doLogin')</button>
    </form>

    @if ($errors->any())
    <div class="checkers_form_errors">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@endsection

@section('top-pageinfo')
    @lang('auth.loginTitle')
@endsection
