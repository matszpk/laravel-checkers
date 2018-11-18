@extends('layout')

@section('top-pageinfo')
    @lang('main.pageErrorTitle')
@endsection

@section('main')
    <div class='checkers_mainpage checkers_error'>
    @lang($errorTrans)
    </div>
@endsection