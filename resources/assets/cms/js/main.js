//@prepros-prepend plugins/*.js
//@prepros-prepend vendor/plugins/*.js
//@prepros-prepend vendor/headroom.min.js
//@prepros-prepend vendor/js.cookie.js
//@prepros-prepend vendor/history.min.js

var unikat = function() {
    'use strict';

    var variables = {};

    var jsCreateHook = '.js-create';

    var htmlLoading;

    var errorWrapperHtmlStart = '<div class="alert alert-danger alert-dismissible"><button type="button" class="close"><span aria-hidden="true">&times;</span></button>';
    var errorMessageHtmlStart =  '<ul>';
    var errorListHtmlStart =      '<li>';
    var errorListHtmlEnd =        '</li>';
    var errorMessageHtmlEnd =    '</ul>';
    var errorWrapperHtmlEnd =   '</div>';

    var successWrapperHtmlStart = '<div class="alert alert-success alert-dismissible"><button type="button" class="close"><span aria-hidden="true">&times;</span></button><span class="glyphicon glyphicon-ok"></span>';
    var successWrapperHtmlEnd =   '</div>';

    var alertMessagesHtmlStart = '<div class="alert-messages">';
    var alertMessagesHtmlEnd =   '</div>';

    var glyphiconWarning = '<span class="glyphicon glyphicon-warning-sign"></span>';
    var glyphiconRemove = '<span class="glyphicon glyphicon-remove form-control-feedback"></span>';
    var glyphiconRemoveSpan = 'span.glyphicon-remove';

    var formGroupClass = '.form-group';
    var hasErrorClass = 'has-error has-feedback';
    var hasErrorClasses = hasErrorClass + ' has-addon-feedback';
    var ajaxLockClass = '.ajax-lock';
    var ajaxLockedClass = '.ajax-locked';
    var alertMessagesClass = '.alert-messages';
    var inputGroupAddonClass = 'input-group-addon';
    var buttonCloseClass = 'button.close';

    var lock_timer;
    var lock_time = 0;
    var jqxhr;

    function run() {
        htmlLoading = '<div tabindex="-1" class="ajax-locked"><div><div><img src="' + variables.loadingImageSrc + '" alt="' + variables.loadingImageAlt + '" title="' + variables.loadingImageTitle + '">' + variables.loadingText + '</div></div></div>';
        errorMessageHtmlStart = variables.ajaxErrorMessage + errorMessageHtmlStart;

        placeholder();

        if (!variables.is_auth) {
            var location = window.history.location || window.location;

            $(window).on('popstate', function(e) {
                var $sidebars = $('.sidebar');
                var $tabs = $('.sidebar-tabs li');
                var hash = window.location.hash; //.substring(1);
                var $index = (hash ? $sidebars.index($(hash)) : 0);
                var jsCookies = Cookies.getJSON('jsCookies');

                $sidebars.removeClass('sidebar-active');
                $tabs.removeClass('sidebar-tab-active');
                $sidebars.eq($index).addClass('sidebar-active').css('opacity', 0).animate({opacity: 1}, 'fast');
                $tabs.eq($index).addClass('sidebar-tab-active');

                Cookies.set('jsCookies', { sidebar: $index, navState: (jsCookies ? jsCookies.navState : null) }, { expires: 365 });
            });

            $('#fixed-header').headroom({
                offset: variables.headroomOffset
            });

            $('.nav-toggle').on('click', 'a', function(e) {
                e.preventDefault();

                $(this).parent().toggleClass('collapsed');
                $('#wrapper').toggleClass('collapsed');

                var navState;
                if ($('#wrapper').hasClass('collapsed')) {
                    navState = 'collapsed';
                } else {
                    navState = null;
                }

                var jsCookies = Cookies.getJSON('jsCookies');
                Cookies.set('jsCookies', { navState: navState, sidebar: (jsCookies ? jsCookies.sidebar : null) }, { expires: 365 });
            });

            $('.sidebar-tabs').on('click', 'a', function(e) {
                e.preventDefault();

                var $parent = $(this).parent();
                var $tabs = $('.sidebar-tabs li');
                var $sidebars = $('.sidebar');
                var $index = $tabs.index($parent);

                $tabs.removeClass('sidebar-tab-active');
                $parent.addClass('sidebar-tab-active');

                $sidebars.removeClass('sidebar-active');
                $sidebars.eq($index).addClass('sidebar-active').css('opacity', 0).animate({opacity: 1}, 'fast');

                history.pushState(null, document.title, $(this).attr('href'));

                var jsCookies = Cookies.getJSON('jsCookies');
                Cookies.set('jsCookies', { sidebar: $index, navState: (jsCookies ? jsCookies.navState : null) }, { expires: 365 });
            });

            $('body').on('click', jsCreateHook, function(e) {
                e.preventDefault();

                var src = $(this).attr('href');

                $.magnificPopup.open({
                    type: 'ajax',
                    key: 'popup-form',
                    focus: 'input',
                    closeOnBgClick: false,
                    // alignTop: true,
                    tClose: variables.magnificPopupClose,
                    tLoading: variables.magnificPopupLoading,
                    ajax: {
                        tError: variables.magnificPopupAjaxError
                    },
                    preloader: true,
                    removalDelay: 500,
                    mainClass: 'mfp-zoom-in',
                    items: {
                        src: src
                    }
                });
            })

            if (variables.datatables) {
                $.extend($.fn.dataTable.defaults, {
                    dom: "<'clearfix'<'dataTableL'l><'dataTableF'f>>tr<'clearfix'<'dataTableI'i><'dataTableP'p>>",
                    paging: (variables.datatablesCount <= 10 ? false : true),
                    stateSave: true,
                    deferRender: true,
                    retrieve: true,
                    stateDuration: 0,
                    defaultContent: '',
                    searchDelay: (variables.datatablesAjax ? 400 : 0),
                    serverSide: (variables.datatablesAjax ? true : false),
                    // deferLoading: variables.datatablesCount, // State can't be saved with pipelining and deferLoading enabled
                    pagingType: variables.datatablesPagingType,
                    pageLength: variables.datatablesPageLength,
                    lengthMenu: variables.datatablesLengthMenu,
                    language: {
                        url: variables.datatablesLanguage
                    },
                    initComplete: function(settings, json) {
                        var $searchInput = $('input', settings.aanFeatures.f);
                        $searchInput.off('keyup.DT input.DT'); // disable global search events except: search.DT paste.DT cut.DT
                        var table = $('.dataTable').DataTable(); // get the api

                        $(document).on('input keyup', $searchInput.selector, $.debounce(settings.searchDelay, function(e) {
                            table.search(this.value).draw();
                        }));
                    }
                });

                // Register an API method that will empty the pipelined data, forcing an Ajax
                // fetch on the next draw (i.e. `table.clearPipeline().draw()`)
                $.fn.dataTable.Api.register('clearPipeline()', function() {
                    return this.iterator('table', function(settings) {
                        settings.clearCache = true;
                    });
                });
            }
        }

        $(document).on('click', function(e) {
            if (!$(e.target).closest('.dropdown').length) {
                $('.dropdown-menu').each(function() {
                    if (!$(this).hasClass('dropdown-menu-static')) {
                        if ($(this).hasClass('dropdown-menu-slide')) {
                            $(this).slideUp();
                        } else {
                            $(this).removeClass('active');
                        }
                    }
                });
            }
        });

        $(document).on('click', '.dropdown-toggle', function(e) {
            e.preventDefault();

            var that = $(this).next();

            $('.dropdown-menu.active').not(['.dropdown-menu-static', that[0]]).removeClass('active');

            if (that.hasClass('dropdown-menu-slide')) {
                that.slideToggle();
            } else {
                that.toggleClass('active');
            }
        });

        $(document).on('click', alertMessagesClass + ' ' + buttonCloseClass, function() {
            $(this).closest(alertMessagesClass).remove();
        });

        if (typeof this.callback == 'function') {
            this.callback();
        }

        $(document).on('submit', 'form', function(e) {
            e.preventDefault();

            var that = $(this);
            if (typeof jqxhr != 'undefined') {
                ajax_unlock(that);
            }
            ajax_lock(that);

            jqxhr = $.post(that.attr('action'), that.serialize());

            jqxhr.done(function(data, status, xhr) {
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    ajax_unlock(that);
                    if (data.success) {
                        ajax_success_text(that, data.success);
                        ajax_reset_form(that);
                    } else {
                        ajax_error(that, data);
                    }
                }
            });

            jqxhr.fail(function(xhr, textStatus, errorThrown) {
                ajax_unlock(that);
                if (xhr.status == 422) { // laravel response for validation errors
                    ajax_error_validation(that, xhr.responseJSON);
                } else {
                    ajax_error_text(that, textStatus + ': ' + errorThrown);
                }
            });

            return false;
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: variables.urlGoogleAnalytics,
            dataType: "script",
            cache: true
        });
    }

    function ajax_reset_form(form, excluded, included) {
        var inputs = form.find('input:not(:button, :submit, :reset, :radio, :checkbox, [type=hidden]), select, textarea');
        var options = form.find('input:radio, input:checkbox');

        if (included) {
            inputs.each(function() {
                if ($(this).is(included)) {
                    $(this).val('');
                }
            });
            options.each(function() {
                if ($(this).is(included)) {
                    $(this).removeAttr('checked').removeAttr('selected');
                }
            });
        } else if (excluded) {
            inputs.not(excluded).val('');
            options.not(excluded).removeAttr('checked').removeAttr('selected');
        } else {
            inputs.val('');
            options.removeAttr('checked').removeAttr('selected');
        }
    };

    function ajax_lock(that) {
        $('[type=submit]', that).prop('disabled', true);

        lock_timer = window.setTimeout(function() {
            $(htmlLoading).prependTo(that.closest(ajaxLockClass)).focus();
            $(ajaxLockedClass, that).on('keydown', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var code = e.keyCode ? e.keyCode : e.which;
                if (code == 27) { // ESC
                    ajax_unlock(that);
                }
                return false;
            });
        }, lock_time);
        return true;
    };

    function ajax_unlock(that) {
        jqxhr.abort();
        window.clearTimeout(lock_timer);
        that.prev(alertMessagesClass).empty();
        $('[type=submit]', that).prop('disabled', false);
        $(ajaxLockedClass, that).remove();
        return true;
    };

    function ajax_message(that, msg) {
        var $messages = that.prev(alertMessagesClass);
        if ($messages.length > 0) {
            $messages.append(msg)
        } else {
            $(alertMessagesHtmlStart + msg + alertMessagesHtmlEnd).insertBefore(that);
            $messages = that.prev(alertMessagesClass);
        }

        if (!isElementInViewport($messages[0])) {
            $.scrollTo($messages[0]);
        }
    };

    function ajax_clear(that) {
        $(glyphiconRemoveSpan, that).remove();
        $(formGroupClass, that).removeClass(hasErrorClasses);
    };

    function ajax_error_validation(that, data) {
        ajax_clear(that);

        var msg = '';

        if (typeof data == 'object') {
            msg += errorWrapperHtmlStart + errorMessageHtmlStart;
            for (var key in data) {
                if ($('#input-' + key).next().hasClass(inputGroupAddonClass)) {
                    $('#input-' + key).after(glyphiconRemove).closest(formGroupClass).addClass(hasErrorClasses);
                } else {
                    $('#input-' + key).closest(formGroupClass).addClass(hasErrorClass).append(glyphiconRemove);
                }

                for (var i = 0; i < data[key].length; i++) {
                    msg += errorListHtmlStart + glyphiconWarning + data[key][i] + errorListHtmlEnd;
                }
            }
            msg += errorMessageHtmlEnd + errorWrapperHtmlEnd;
        }

        ajax_message(that, msg);
    };

    function ajax_error(that, data) {
        ajax_clear(that);

        var msg = '';
        var excluded = [];
        var included = [];

        if (typeof data == 'object') {
            if (data.errors) {
                msg += errorWrapperHtmlStart + errorMessageHtmlStart;
                for (var i = 0; i < data.errors.length; i++) {
                    msg += errorListHtmlStart + glyphiconWarning + data.errors[i] + errorListHtmlEnd;
                }
                msg += errorMessageHtmlEnd + errorWrapperHtmlEnd;
            }

            if (data.ids) {
                for (var i = 0; i < data.ids.length; i++) {
                    if ($('#input-' + data.ids[i]).next().hasClass(inputGroupAddonClass)) {
                        $('#input-' + data.ids[i]).after(glyphiconRemove).closest(formGroupClass).addClass(hasErrorClasses);
                    } else {
                        $('#input-' + data.ids[i]).closest(formGroupClass).addClass(hasErrorClass).append(glyphiconRemove);
                    }
                }
            }

            if (data.resetOnly) {
                for (var i = 0; i < data.resetOnly.length; i++) {
                    included.push('#input-' + data.resetOnly[i]);
                }
                if (included.length) {
                    ajax_reset_form(that, null, included.join());
                }
            } else if (data.resetExcept) {
                for (var i = 0; i < data.resetExcept.length; i++) {
                    excluded.push('#input-' + data.resetExcept[i]);
                }
                if (excluded.length) {
                    ajax_reset_form(that, excluded.join());
                }
            } else if (data.reset) {
                ajax_reset_form(that);
            }
        }

        ajax_message(that, msg);
    };

    function ajax_error_text(that, msg) {
        ajax_clear(that);
        msg = errorWrapperHtmlStart + glyphiconWarning + msg + errorWrapperHtmlEnd;
        ajax_message(that, msg);
    };

    function ajax_success_text(that, msg) {
        ajax_clear(that);
        msg = successWrapperHtmlStart + msg + successWrapperHtmlEnd;
        ajax_message(that, msg);
    };

    function datatables(params) {
        var params = $.extend({
            pipeline: variables.datatablesPipeline, // number of pages to cache/pipeline
            url: '',  // script url
            data: null, // function or object with parameters to send to the server matching how `ajax.data` works in DataTables
            method: 'GET' // Ajax HTTP method
        }, params);

        // Private variables for storing the cache
        var cacheLower = -1;
        var cacheUpper = null;
        var cacheLastRequest = null;
        var cacheLastJson = null;

        return function (request, callback, settings) {
            var ajax = false;
            var requestStart = request.start;
            var drawStart = request.start;
            var requestLength = request.length;
            var requestEnd = requestStart + requestLength;

            if (settings.clearCache) { // API requested that the cache be cleared
                ajax = true;
                settings.clearCache = false;
            } else if (cacheLower < 0 || requestStart < cacheLower || requestEnd > cacheUpper) { // outside cached data - need to make a request
                ajax = true;
            } else if (JSON.stringify(request.order) !== JSON.stringify(cacheLastRequest.order) || JSON.stringify(request.columns) !== JSON.stringify(cacheLastRequest.columns) || JSON.stringify(request.search) !== JSON.stringify(cacheLastRequest.search)) { // properties changed (ordering, columns, searching)
                ajax = true;
            }

            // Store the request for checking next time around
            cacheLastRequest = $.extend(true, {}, request);

            if (ajax) { // Need data from the server
                if (requestStart < cacheLower) {
                    requestStart = requestStart - (requestLength * (params.pipeline - 1));
                    if (requestStart < 0) {
                        requestStart = 0;
                    }
                }

                cacheLower = requestStart;
                cacheUpper = requestStart + (requestLength * params.pipeline);

                request.start = requestStart;
                request.length = requestLength * params.pipeline;

                // Provide the same `data` options as DataTables.
                if ($.isFunction(params.data)) {
                    // As a function it is executed with the data object as an arg
                    // for manipulation. If an object is returned, it is used as the
                    // data object to submit
                    var d = params.data(request);
                    if (d) {
                        $.extend(request, d);
                    }
                } else if ($.isPlainObject(params.data)) { // As an object, the data given extends the default
                    $.extend(request, params.data);
                }

                var that = $(this).closest(ajaxLockClass);

                if (typeof jqxhr != 'undefined') {
                    ajax_unlock(that);
                }
                ajax_lock(that);

                if (params.method == 'GET') {
                    jqxhr = $.get(params.url, request);
                } else {
                    jqxhr = $.post(params.url, request);
                }

                jqxhr.done(function(data, status, xhr) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        ajax_unlock(that);
                        if (data.success) {
                            ajax_success_text(that, data.success);
                            ajax_reset_form(that);
                        } else if (data.errors) {
                            ajax_error(that, data);
                        } else {
                            cacheLastJson = $.extend(true, {}, data);
                            if (cacheLower != drawStart) {
                                data.data.splice(0, drawStart - cacheLower);
                            }
                            data.data.splice(requestLength, data.data.length);
                            callback(data);
                        }
                    }
                });

                jqxhr.fail(function(xhr, textStatus, errorThrown) {
                    ajax_unlock(that);
                    if (xhr.status == 422) { // laravel response for validation errors
                        ajax_error_validation(that, xhr.responseJSON);
                    } else {
                        ajax_error_text(that, textStatus + ': ' + errorThrown);
                    }
                });
            } else {
                var json = $.extend(true, {}, cacheLastJson);
                json.draw = request.draw; // Update the echo for each response
                json.data.splice(0, requestStart - cacheLower);
                json.data.splice(requestLength, json.data.length);
                callback(json);
            }
        }
    };

    return { datatables: datatables, run: run, variables: variables }
}();
