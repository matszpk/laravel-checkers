@extends('layout')

@section('main')
@lang('auth.emailNoticeContent')<br/>

@if (!$emailVerified)
    <div class='checkers_mainbutton'>
        <a href="{{ route('verification.resend') }}">@lang('auth.doVerify')</a>
    </div>
@endif
@endsection

@section('top-pageinfo')
    @lang('auth.emailVerifyTitle')
@endsection
