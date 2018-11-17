@extends('layout')

@section('top-pageinfo')
    @lang('main.pageErrorTitle')
@endsection

@section('main')
    <div class='checkers_mainpage checkers_error'>
    @lang('error.errorCode', ['code' => '403'])<br/>
    @lang('error.forbidden')
    </div>
@endsection