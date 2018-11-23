@extends('layout')

@section('top-pageinfo')
    @lang('main.gameCommentsTitle')
@endsection

@section('script')
    @include('components.comments_scripts')
@endsection

@section('main')
    <div class='checkers_subtitle'>@lang('game.commentsToGame',
        [ 'name' => $data->getName() ])</div>
    
    @include('components.comments') 
@endsection