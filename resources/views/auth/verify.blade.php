@extends('layout')

@section('top-pageinfo')
    @lang('auth.emailVerifyTitle')
@endsection

@section('main')
<div class='checkers_mainpage'>
    @lang('auth.emailNoticeContent')<br/>
</div>

<div class='checkers_centered'>
    @if (!$emailVerified)
        <div class='checkers_mainbutton'>
            <a href="{{ route('verification.resend') }}">@lang('auth.doVerify')</a>
        </div>
    @endif
</div>

    @include('components.validation-errors')
@endsection

