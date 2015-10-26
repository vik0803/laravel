<!doctype html>
<html class="no-js" lang="{{ \Locales::getCurrent() }}">
<head dir="{{ \Locales::getScript() }}">
	<meta charset="utf-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{{ \Locales::getMetaTitle() }}</title>
    <meta name="description" content="{{ \Locales::getMetaDescription() }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="apple-touch-icon" href="{{ \App\Helpers\autover('/apple-touch-icon.png') }}">
    <!-- Place favicon.ico in the root directory -->

    <link href="{{ \App\Helpers\autover('/css/cms/main.min.css') }}" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=Oswald|Roboto:400,400italic,700,700italic&subset=latin,cyrillic' rel='stylesheet' type='text/css'>

    <script src="{{ \App\Helpers\autover('/js/cms/vendor/modernizr-2.8.3.min.js') }}"></script>

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
        <script src="{{ \App\Helpers\autover('/js/cms/vendor/ie8.min.js') }}"></script>
	<![endif]-->
</head>
<body>
    <header>
        <div id="header-wrapper">
            @include('cms/partials.fixed-header')
        </div>
    </header>
    <main>
        <div id="wrapper"{!! isset($jsCookies['navState']) ? ' class="collapsed"' : '' !!}>
            <div id="sidebar-wrapper">
                @include('cms/partials.sidebar')
            </div>
            <div id="main-wrapper">
                @include('cms/partials.breadcrumbs')

                <div id="content-wrapper">
                    @yield('content')
                </div>
            </div>
        </div>
    </main>
    <footer>
        <div id="footer-wrapper">
            @include('cms/partials.footer')
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
                $.extend(unikat.variables, {
                    ajaxErrorMessage: '{!! trans('cms/js.ajaxErrorMessage') !!}',
                    loadingImageSrc: '{{ \App\Helpers\autover('/img/cms/loading.gif') }}',
                    loadingImageAlt: '{{ trans('cms/js.loadingImageAlt') }}',
                    loadingImageTitle: '{{ trans('cms/js.loadingImageTitle') }}',
                    loadingText: '{{ trans('cms/js.loadingText') }}',
                    magnificPopupClose: '{{ trans('cms/js.magnificPopupClose') }}',
                    magnificPopupLoading: '{{ trans('cms/js.magnificPopupLoading') }}',
                    magnificPopupAjaxError: '{!! trans('cms/js.magnificPopupAjaxError') !!}',
                    urlGoogleAnalytics: '{{ \App\Helpers\autover('/js/cms/google.min.js') }}',
                    headroomOffset: 300,
                });

                @if (isset($datatables))
                <?php $size = ($datatables['count'] <= 100 ? 'Small' : ($datatables['count'] <= 1000 ? 'Medium' : 'Large')); ?>
                $.extend(unikat.variables, {
                    datatables: true,
                    datatablesAjax: {!! isset($datatables['ajax']) ? "'" . $datatables['ajax'] . "'" : 'false' !!},
                    datatablesCount: {{ $datatables['count'] }},
                    datatablesPipeline: {{ \Config::get('datatables.pipeline') }},
                    datatablesPagingType: '{{ \Config::get('datatables.pagingType' . $size) }}',
                    datatablesPageLength: {{ \Config::get('datatables.pageLength' . $size) }},
                    datatablesLengthMenu: {!! str_replace('all', trans('cms/messages.all'), \Config::get('datatables.lengthMenu' . $size)) !!},
                    datatablesLanguage: '{{ \App\Helpers\autover('/lng/datatables/' . \Locales::getCurrent() . '.json') }}'
                });
                @endif

                @yield('script')

                unikat.run();
            }
        }
    ]);
    </script>

</body>
</html>
