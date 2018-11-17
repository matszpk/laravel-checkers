@extends('layout')

@section('top-pageinfo')
    @lang('main.pageErrorTitle')
@endsection

@section('main')
    <div class='checkers_mainpage checkers_error'>
    @lang('error.errorCode', ['code' => '503'])<br/>
    @lang('error.serviceUnavailable')
    </div>
@endsection