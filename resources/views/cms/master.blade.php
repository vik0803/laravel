<!doctype html>
<html class="no-js" lang="{{ \Locales::get() }}">
<head dir="{{ \Locales::getScript() }}">
	<meta charset="utf-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="apple-touch-icon" href="{{ \App\Helpers\autover('/apple-touch-icon.png') }}">
    <!-- Place favicon.ico in the root directory -->

    <link href="{{ \App\Helpers\autover('/css/cms/main.min.css') }}" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=Roboto:400,400italic,700,700italic&subset=latin,cyrillic' rel='stylesheet' type='text/css'>

    <script src="{{ \App\Helpers\autover('/js/cms/vendor/modernizr-2.8.3.min.js') }}"></script>

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
        <script src="{{ \App\Helpers\autover('/js/cms/vendor/ie8.min.js') }}"></script>
	<![endif]-->

    @yield('header')
</head>
<body>
    <header>
        <div id="header-wrapper">
            @include('cms/partials.fixed-header')
        </div>
    </header>
    <main>
        <div id="main-wrapper">
            <div id="sidebar-wrapper">
                @include('cms/partials.sidebar')
            </div>
            <div id="content-wrapper">
                @yield('content')
            </div>
        </div>
    </main>
    <footer>
        <div id="footer-wrapper">
            Footer
        </div>
    </footer>

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
                unikat.setJSVariables({
                    'ajaxErrorMessage': '{!! trans('cms/js.ajaxErrorMessage') !!}',
                    'loadingImageSrc': '{{ \App\Helpers\autover('/img/cms/loading.gif') }}',
                    'loadingImageAlt': '{{ trans('cms/js.loadingImageAlt') }}',
                    'loadingImageTitle': '{{ trans('cms/js.loadingImageTitle') }}',
                    'loadingText': '{{ trans('cms/js.loadingText') }}'
                });

                @yield('script');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: '{{ \App\Helpers\autover('/js/cms/google.min.js') }}',
                    dataType: "script",
                    cache: true
                });
            }
        }
    ]);
    </script>

    @yield('footer')

</body>
</html>
