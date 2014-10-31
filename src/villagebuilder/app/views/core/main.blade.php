<!DOCTYPE html>
<html>
    <head>
        <title>Village Builder</title> 
    </head>
    <body>
        @if(Session::has('global'))
            <p>{{ Session::get('global') }}</p>
        @endif
        @include('core.navigation')
        @yield('content')
    </body>
</html>
