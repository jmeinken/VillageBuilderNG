@extends('core.main')

@section('content')
    <form action="{{ URL::route('account-forgot-password-post') }}" method="post">
        <div id="field">
            Email <input type="text" name="email" value="{{ Input::old('email') ? e(Input::old('email')) : '' }}" />
            @if($errors->has('email'))
                {{ $errors->first('email') }}
            @endif
        </div>
        <input type="submit" value="Recover" />
        {{ Form::token() }}
    </form>
@stop
