@extends('core.main')

@section('content')
    
    <form action="{{ URL::route('account-create-post') }}" method="post">
        <div class="field">
            Email: <input type="text" name="email" value="{{ Input::old('email') ? e(Input::old('email')) : '' }}" />
            @if($errors->has('email'))
                {{ $errors->first('email') }}
            @endif
        </div>
        <div class="field">
            username: <input type="text" name="username" value="{{ Input::old('username') ? e(Input::old('usrname')) : '' }}" />
            @if($errors->has('username'))
                {{ $errors->first('username') }}
            @endif
        </div>
        <div class="field">
            password: <input type="password" name="password" />
            @if($errors->has('password'))
                {{ $errors->first('password') }}
            @endif
        </div>
        <div class="field">
            password again: <input type="password" name="password_again" />
            @if($errors->has('password_again'))
                {{ $errors->first('password_again') }}
            @endif
        </div>
        <input type="submit" value="Create account" />
        {{ Form::token() }}
    </form>
@stop