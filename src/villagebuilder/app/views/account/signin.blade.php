@extends('core.main')

@section('content')

    <form action="{{ URL::route('account-sign-in-post') }}" method="post">
        <div class="field">
            Email: <input type="text" name="email" value="{{ Input::old('email') ? e(Input::old('email')) : '' }}" />
            @if($errors->has('email'))
                {{ $errors->first('email') }}
            @endif
        </div>
        <div class="field">
            Password: <input type="password" name="password" />
            @if($errors->has('password'))
                {{ $errors->first('password') }}
            @endif
        </div>
        <div class="field">
            Remember me: <input type="checkbox" name="remember" id="remember" />
            @if($errors->has('password'))
                {{ $errors->first('password') }}
            @endif
        </div>
        <input type="submit" value="Sign In" />
        {{ Form::token() }}
    </form>


@stop
