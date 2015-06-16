<!doctype html>
<html class="no-js" lang="{{ \Locales::get() }}">
<head>
	<meta charset="utf-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">

    <link rel="apple-touch-icon" href="{{ \App\Helpers\autover('/apple-touch-icon.png') }}">
    <!-- Place favicon.ico in the root directory -->

    <link href="{{ \App\Helpers\autover('/css/cms/main.min.css') }}" rel="stylesheet">

    <script src="{{ \App\Helpers\autover('/js/cms/vendor/modernizr-2.8.3.min.js') }}"></script>

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
        <script src="{{ \App\Helpers\autover('/js/cms/ie8.min.js') }}"></script>
	<![endif]-->

    @yield('header')
</head>
<body>
    <!--[if lt IE 8]>
        <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->
	<nav class="navbar navbar-default">
        <ul class="nav navbar-nav">
            <li><a href="{{ url(\Locales::getLocaleURL('bg')) }}">Български</a></li>
            <li><a href="{{ url(\Locales::getLocaleURL('en')) }}">English</a></li>
        </ul>

        <ul class="nav navbar-nav navbar-right">
        @if (Auth::guest())
            <li><a href="{{ url(\Locales::getLocalizedURL()) }}">Login</a></li>
            <li><a href="{{ url(\Locales::getLocalizedURL('register')) }}">Register</a></li>
        @else
            <li>{{ Auth::user()->name }}</li>
            <li><a href="{{ url(\Locales::getLocalizedURL('logout')) }}">Logout</a></li>
        @endif
        </ul>
    </nav>

	@yield('content')

	<script>
    Modernizr.load([
        {
            test: typeof isOldIe == 'undefined',
            yep: { 'v2' : '//ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js' },
            nope: { 'v1' : '//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js' },
            callback: function(url, result, key) {
                if (!window.jQuery) {
                    if (key === 'v1') {
                        Modernizr.load('{{ \App\Helpers\autover('/js/cms/vendor/jquery-1.11.3.min.js') }}');
                    } else {
                        Modernizr.load('{{ \App\Helpers\autover('/js/cms/vendor/jquery-2.1.4.min.js') }}');
                    }
                }
            }
        },
        {
            load: ['{{ \App\Helpers\autover('/js/cms/main.min.js') }}'],
            complete: function() {
                $.ajaxSetup({cache: true}); // cache script loaded with $.getScript
                $.getScript('{{ \App\Helpers\autover('/js/cms/google.min.js') }}');
            }
        }
    ]);
    </script>

    @yield('footer')

</body>
</html>
