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
<body id="body-auth">
    <script>(function() { var pad = '000'; var random = Math.floor((Math.random() * 298) + 1); var img = pad.substring(0, pad.length - random.toString().length) + random.toString(); document.getElementById('body-auth').style.backgroundImage = "url('/img/cms/auth/" + img + ".jpg')"; })();</script>
    <div class="auth-quote">{{ Inspiring::quote() }}</div>
    <div class="auth-wrapper">
        <a href="{{ url(\Locales::getLocalizedURL()) }}">{!! HTML::image(\App\Helpers\autover('/img/cms/logo.png'), trans('cms/messages.altLogo')) !!}</a>

        <div class="auth-box">@yield('content')</div>

        <div class="languages-wrapper">
            <div class="dropdown">
                <a href="#" class="btn btn-default dropdown-toggle dropdown-toggle-left">
                    {!! HTML::image(\App\Helpers\autover('/img/cms/languages.png'), trans('cms/messages.changeLanguage')) !!}
                    {{ trans('cms/messages.changeLanguage') }}
                    <span class="caret caret-right"></span>
                </a>
                <ul class="dropdown-menu">
                    <li class="active">
                        <a href="{{ url(\Locales::getLocaleURL(\Locales::get())) }}">
                        {{ \Locales::getNativeName() }}
                        @if (\Locales::getName() != \Locales::getNativeName())
                            <span class="sub-text">({{ \Locales::getName() }})</span>
                        @endif
                        </a>
                    </li>
                    @foreach (\Locales::getSupportedLocales() as $key => $value)
                        @if ($key != \Locales::get())
                            <li>
                                <a href="{{ url(\Locales::getLocaleURL($key)) }}">
                                {{ $value['native'] }}
                                @if ($value['name'] != $value['native'])
                                    <span class="sub-text">({{ $value['name'] }})</span>
                                @endif
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

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
                $.extend(unikat.variables, {
                    is_auth: true,
                    ajaxErrorMessage: '{!! trans('cms/js.ajaxErrorMessage') !!}',
                    loadingImageSrc: '{{ \App\Helpers\autover('/img/cms/loading.gif') }}',
                    loadingImageAlt: '{{ trans('cms/js.loadingImageAlt') }}',
                    loadingImageTitle: '{{ trans('cms/js.loadingImageTitle') }}',
                    loadingText: '{{ trans('cms/js.loadingText') }}',
                    urlGoogleAnalytics: '{{ \App\Helpers\autover('/js/cms/google.min.js') }}'
                });

                @yield('script')

                unikat.run();
            }
        }
    ]);
    </script>

    @yield('footer')

</body>
</html>
