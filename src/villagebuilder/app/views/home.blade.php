@extends('core.main')

@section('content')
    @if(Auth::check())
        Signed in as {{ Auth::user()->username }}.<br>
        using email {{ Auth::user()->email }}.
    @else
        You are not signed in.
    @endif
@stop