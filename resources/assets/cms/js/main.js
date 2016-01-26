//@prepros-prepend plugins/*.js
//@prepros-prepend vendor/plugins/combine/*.js
//@prepros-prepend vendor/headroom.min.js
//@prepros-prepend vendor/js.cookie.js
//@prepros-prepend vendor/history.min.js

var unikat = function() {
    'use strict';

    var variables = {
        tables: [],
        rows_selected: {},
        lock_time: 0,
        jsCreateHook: '.js-create',
        jsEditHook: '.js-edit',
        jsDestroyHook: '.js-destroy',
        jsUploadHook: '.js-upload',
        filterClass: '.dataTables_filter',
        datatablePrefix: 'datatable',
    };

    var htmlLoading;

    var progressBarHtml = '<div id="upload-progress-bar-container" class="qq-total-progress-bar-container-selector progress"><div id="upload-progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-total-progress-bar-selector progress-bar progress-bar-success progress-bar-striped active"></div></div>';

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
    var alertClass = '.alert';
    var inputGroupAddonClass = 'input-group-addon';
    var buttonCloseClass = 'button.close';

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

            $('.popup-gallery').magnificPopup({
                delegate: 'a',
                type: 'image',
                tClose: variables.magnificPopup.close,
                tLoading: variables.magnificPopup.loading,
                gallery: {
                    enabled: true,
                    preload: [0, 2],
                    tPrev: variables.magnificPopup.prev,
                    tNext: variables.magnificPopup.next,
                },
                preloader: true,
                mainClass: 'mfp-zoom-in',
                zoom: {
                    enabled: true,
                },
            });

            var magnificPopupOptions = {
                type: 'ajax',
                key: 'popup-form',
                focus: 'input:visible',
                closeOnBgClick: false,
                alignTop: true,
                tClose: variables.magnificPopup.close,
                tLoading: variables.magnificPopup.loading,
                ajax: {
                    tError: variables.magnificPopup.ajaxError
                },
                preloader: true,
                removalDelay: 500,
                mainClass: 'mfp-zoom-in',
            };

            $(document).on('click', variables.jsCreateHook, function(e) {
                e.preventDefault();

                var table = $(this).data('table');
                var separator = $(this).attr('href').indexOf('?') == -1 ? '?' : '&';
                var src = $(this).attr('href') + (table ? separator + 'table=' + table : '');

                $.magnificPopup.open($.extend(magnificPopupOptions, {
                    items: {
                        src: src,
                    },
                    callbacks: {
                        ajaxContentAdded: function() {
                            if (typeof CKEDITOR == 'object') {
                                ckeditorSetup(unikat.ckeditorConfig);
                            }

                            if (typeof $.multiselect == 'object') {
                                multiselectSetup(unikat.multiselectConfig);
                            }

                            if (typeof unikat.magnificPopupCreateCallback == 'function') {
                                unikat.magnificPopupCreateCallback();
                            }
                        }
                    },
                }));
            });

            $(document).on('click', variables.jsDestroyHook, function(e) {
                e.preventDefault();

                var table = $(this).data('table');
                var separator = $(this).attr('href').indexOf('?') == -1 ? '?' : '&';
                var src = $(this).attr('href') + (table ? separator + 'table=' + table : '');

                $.magnificPopup.open($.extend(magnificPopupOptions, {
                    items: {
                        src: src,
                    },
                }));
            });

            $(document).on('click', variables.jsEditHook, function(e) {
                e.preventDefault();

                var table = $(this).data('table');
                var separator = $(this).attr('href').indexOf('?') == -1 ? '?' : '&';

                var param = null;
                var tableId = variables.datatablePrefix + table;
                if (variables.rows_selected[tableId]) {
                    param = '/' + variables.rows_selected[tableId][0];
                }

                var src = $(this).attr('href') + param + (table ? separator + 'table=' + table : '');

                $.magnificPopup.open($.extend(magnificPopupOptions, {
                    items: {
                        src: src,
                    },
                    callbacks: {
                        ajaxContentAdded: function() {
                            if (typeof CKEDITOR == 'object') {
                                ckeditorSetup(unikat.ckeditorConfig);
                            }

                            if (typeof unikat.magnificPopupEditCallback == 'function') {
                                unikat.magnificPopupEditCallback();
                            }
                        }
                    },
                }));

                ajax_unlock($(this).closest(ajaxLockClass));
            });

            $(variables.jsUploadHook).each(function() {
                if (typeof qq !== 'undefined') {
                    var params = {
                        id: $(this).attr('id'),
                        table: $(this).data('table'),
                        url: $(this).data('url'),
                    };

                    fineUploaderSetup(params, unikat.fineUploaderConfig);
                }
            });

            if (variables.datatables) {
                // Register an API method that will empty the pipelined data, forcing an Ajax
                // fetch on the next draw (i.e. `table.clearPipeline().draw()`)
                $.fn.dataTable.Api.register('clearPipeline()', function() {
                    return this.iterator('table', function(settings) {
                        settings.clearCache = true;
                    });
                });

                $(document).on('preInit.dt', function(e, settings) {
                    var api = new $.fn.dataTable.Api(settings);
                    var $table = api.table().node();
                    var $wrapper = $($table).closest('.dataTableWrapper');
                    var $filter = $($wrapper).find(variables.filterClass + ' input');
                    $filter.off('keyup.DT input.DT'); // disable global search events except: search.DT paste.DT cut.DT
                    $filter.on('keyup.DT input.DT', $.debounce(settings.searchDelay, function(e) {
                        api.search(this.value).draw();
                    }));
                });

                // Handle click on "Select all" checkbox
                $(document).on('click', '.table-checkbox thead input[type="checkbox"]', function(e) {
                    var tableId = $(this).closest('table').attr('id');

                    if (this.checked) {
                        $('#' + tableId + ' tbody input[type="checkbox"]:not(:checked)').trigger('click');
                    } else {
                        $('#' + tableId + ' tbody input[type="checkbox"]:checked').trigger('click');
                    }

                    e.stopPropagation(); // Prevent click event from propagating to parent
                });

                // Handle click on table cells with checkboxes
                $(document).on('click', '.table-checkbox tbody td, .table-checkbox thead th:first-child', function(e) {
                    if (e.target.tagName.toLowerCase() !== 'a' && e.target.tagName.toLowerCase() !== 'img') {
                        $(this).parent().find('input[type="checkbox"]').trigger('click');
                    }
                });

                // Handle click on checkbox
                $(document).on('click', '.table-checkbox tbody input[type="checkbox"]', function(e) {
                    var tableId = $(this).closest('table').attr('id');
                    var $row = $(this).closest('tr');
                    var rowId = $row.attr('id');
                    var index = $.inArray(rowId, variables.rows_selected[tableId]);

                    if (this.checked && index === -1) { // If checkbox is checked and row ID is not in list of selected row IDs
                        variables.rows_selected[tableId].push(rowId);
                    } else if (!this.checked && index !== -1) { // Otherwise, if checkbox is not checked and row ID is in list of selected row IDs
                        variables.rows_selected[tableId].splice(index, 1);
                    }

                    if (this.checked) {
                        $row.addClass('selected');
                    } else {
                        $row.removeClass('selected');
                    }

                    datatablesUpdateCheckbox(tableId);

                    e.stopPropagation(); // Prevent click event from propagating to parent
                });
            }
        }

        $(document).on('click', function(e) {
            if (!$(e.target).closest('.submenu').length) {
                $('.dropdown-menu').each(function() {
                    if (!$(this).hasClass('menu-static')) {
                        $(this).removeClass('active');
                    }
                });

                $('.slidedown-menu').each(function() {
                    if (!$(this).hasClass('menu-static')) {
                        $(this).slideUp();
                    }
                });
            }
        });

        $(document).on('click', '.dropdown-toggle', function(e) {
            e.preventDefault();

            $(this).toggleClass('active');

            var that = $(this).next();

            $('.dropdown-menu.active').not(that[0]).removeClass('active');

            that.toggleClass('active');
        });

        $(document).on('click', '.slidedown-toggle', function(e) {
            e.preventDefault();

            if (!$(e.target).closest('.dropdown-menu').length) {
                $('.dropdown-menu.active').removeClass('active');
            }

            $(this).toggleClass('active');
            $(this).next().slideToggle();
        });

        $(document).on('click', alertMessagesClass + ' ' + buttonCloseClass, function() {
            $(this).closest(alertClass).remove();
        });

        if (typeof this.callback == 'function') {
            this.callback();
        }

        $(document).on('submit', 'form', function(e) {
            e.preventDefault();

            var extra = [];
            var table = $('#input-table').val();
            if (typeof table != 'undefined') {
                var tableId = variables.datatablePrefix + table;
                if (variables.rows_selected[tableId]) {
                    $.each(variables.rows_selected[tableId], function(key, value) {
                        extra.push({ name: 'id[]', value: value });
                    });
                }
            }

            var data = $(this).serialize();
            extra = $.param(extra);
            if (extra) {
                data += '&' + extra;
            }

            ajaxify({
                that: $(this),
                method: 'post',
                queue: $(this).data('ajaxQueue'),
                action: $(this).attr('action'),
                data: data,
            });

            return false;
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
        });

        $.ajax({
            url: variables.urlGoogleAnalytics,
            dataType: "script",
            cache: true,
        });
    }

    function ckeditorSetup(config) {
        var config = config || {};
        CKFinder.basePath = CKEDITOR.basePath.replace('ckeditor', 'ckfinder');

        CKFinder.config({
            configPath: '', // don't load config.js
            language: variables.language,
            defaultLanguage: variables.defaultLanguage, // used if language is not available
            defaultDisplayDate: false,
            defaultDisplayFileName: false,
            defaultDisplayFileSize: false,
            defaultSortBy: 'date',
            defaultSortByOrder: 'desc',
            editImageAdjustments: [
                'brightness', 'clip', 'contrast', 'exposure', 'gamma', 'hue', 'noise', 'saturation', 'sepia',
                'sharpen', 'stackBlur', 'vibrance'
            ],
            editImagePresets: [
                'clarity', 'concentrate', 'crossProcess', 'glowingSun', 'grungy', 'hazyDays', 'hemingway', 'herMajesty',
                'jarques', 'lomo', 'love', 'nostalgia', 'oldBoot', 'orangePeel', 'pinhole', 'sinCity', 'sunrise', 'vintage'
            ],
            jquery: '//ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js',
            // connectorInfo: 'token=7901a26e4bc422aef54eb45', // Additional (GET) parameters to send to the server with each request.
        });

        CKFinder.setupCKEditor();

        var defaultConfig = {
            customConfig: '', // don't load config.js
            language: variables.language,
            disableNativeSpellChecker: false,
            removePlugins: '',
            extraPlugins: '',
            autosave_delay: 30, // seconds
            autoGrow_maxHeight: 1000,
            autoGrow_minHeight: 150,
            autoGrow_bottomSpace: 20,
            disallowedContent: 'img{width,height,border*}', // Disallow setting inline borders, width & height for images.
            /*filebrowserBrowseUrl: '/js/cms/vendor/ckfinder/ckfinder.html',
            filebrowserUploadUrl: '/js/cms/vendor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',*/
            uploadUrl: '/js/cms/vendor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images&responseType=json',
            colorButton_colors: '5ca4a1,db6969', // set brand colors
            contentsCss: 'css/cms/main.min.css', // replace with css/www/main.min.css
            contentsLangDirection: variables.languageScript,
            defaultLanguage: variables.defaultLanguage, // used if language is not available
            fontSize_sizes: '12/.75em;14/.875em;16/1em;20/1.25em',
            font_names: 'Helvetica Neue/Helvetica Neue, Helvetica, Arial, sans-serif; Roboto/Roboto, Helvetica Neue, Helvetica, Arial, sans-serif; Oswald/Oswald, Helvetica Neue, Helvetica, Arial, sans-serif',
            format_tags: 'p;h1;h2;h3;h4;h5;h6;div;address',
            justifyClasses: ['text-left', 'text-center', 'text-right', 'text-justify'],
            removeButtons: '', // maybe needed
            removeDialogTabs: '', // maybe needed
            stylesSet: [ // see the styles.js
                { name: 'Strong Emphasis', element: 'strong' },
                { name: 'Emphasis', element: 'em' },
            ],
            // templates_files: '', // maybe usefull
        };

        $.extend(defaultConfig, config);

        CKEDITOR.replaceAll(function(textarea, config) {
            if ($(textarea).hasClass('ckeditor')) {
                $.extend(config, defaultConfig);
            } else {
                return false;
            }
        });
    }

    function multiselectSetup(config) {
        var config = config || {};
        $.extend($.multiselect.multiselect.prototype.options, {
            checkAllText: variables.multiselect.checkAll,
            uncheckAllText: variables.multiselect.uncheckAll,
            noneSelectedText: variables.multiselect.noneSelected,
            noneSelectedSingleText: variables.multiselect.noneSelectedSingle,
            selectedText: variables.multiselect.selected,
            filterLabel: variables.multiselect.filterLabel,
            filterPlaceholder: variables.multiselect.filterPlaceholder,
        }, config);
    }

    function fineUploaderSetup(params, config) {
        var config = config || {};
        params.buttonGroupWrapper = $('#' + params.id).closest('.btn-group-wrapper');

        var defaultConfig = {
            // debug: true,
            button: document.getElementById(params.id),
            chunking: {
                enabled: true,
                concurrent: {
                    enabled: true,
                },
                success: {
                    endpoint: params.url + '/done',
                },
            },
            paste: {
                targetElement: document,
            },
            request: {
                endpoint: params.url,
                customHeaders: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
            },
            resume: {
                enabled: true,
            },
            retry: {
               enableAuto: true,
            },
            validation: {
                allowedExtensions: ['jpg', 'png', 'gif', 'jpeg'],
                stopOnFirstInvalidFile: false,
            },
            text: {
                defaultResponseError: variables.fineUploader.defaultResponseError,
            },
            messages: {
                emptyError: variables.fineUploader.emptyError,
                maxHeightImageError: variables.fineUploader.maxHeightImageError,
                maxWidthImageError: variables.fineUploader.maxWidthImageError,
                minHeightImageError: variables.fineUploader.minHeightImageError,
                minWidthImageError: variables.fineUploader.minWidthImageError,
                minSizeError: variables.fineUploader.minSizeError,
                noFilesError: variables.fineUploader.noFilesError,
                onLeave: variables.fineUploader.onLeave,
                retryFailTooManyItemsError: variables.fineUploader.retryFailTooManyItemsError,
                sizeError: variables.fineUploader.sizeError,
                tooManyItemsError: variables.fineUploader.tooManyItemsError,
                typeError: variables.fineUploader.typeError,
                unsupportedBrowserIos8Safari: variables.fineUploader.unsupportedBrowserIos8Safari,
            },
            callbacks: {
                onTotalProgress: function(totalUploadedBytes, totalBytes) {
                    var progress;
                    var speed = '';
                    var minSamples = 6;
                    var maxSamples = 20;
                    var uploadSpeeds = [];
                    var progressPercent = (totalUploadedBytes / totalBytes).toFixed(2);
                    var totalSize = formatSize(totalBytes, this._options.text.sizeSymbols);
                    var totalUploadedSize = formatSize(totalUploadedBytes, this._options.text.sizeSymbols);

                    uploadSpeeds.push({
                        totalUploadedBytes: totalUploadedBytes,
                        currentTime: new Date().getTime(),
                    });

                    if (uploadSpeeds.length > maxSamples) {
                        uploadSpeeds.shift(); // remove first element
                    }

                    if (uploadSpeeds.length >= minSamples) {
                        var firstSample = uploadSpeeds[0];
                        var lastSample = uploadSpeeds[uploadSpeeds.length - 1];
                        var progressBytes = lastSample.totalUploadedBytes - firstSample.totalUploadedBytes;
                        var progressTimeMS = lastSample.currentTime - firstSample.currentTime;
                        var Mbps = ((progressBytes * 8) / (progressTimeMS / 1000) / (1000 * 1000)).toFixed(2); // megabits per second
                        // var MBps = (progressBytes / (progressTimeMS / 1000) / (1024 * 1024)).toFixed(2); // megabytes per second

                        if (Mbps > 0) {
                            speed = ' / ' + Mbps + ' Mbps';
                        }
                    }

                    if (isNaN(progressPercent)) {
                        progress = '0%';
                    } else {
                        progress = (progressPercent * 100).toFixed() + '%';
                    }

                    $('#upload-progress-bar').css('width', progress).text(progress + ' (' + totalUploadedSize + ' of ' + totalSize + speed + ')');
                },
                onAllComplete: function(succeeded, failed) {
                    $('#upload-progress-bar-container').remove();

                    if (failed.length > 0) {
                        var that = this;
                        var msg = errorWrapperHtmlStart + errorMessageHtmlStart;
                        $.each(failed, function(key) {
                            msg += errorListHtmlStart + glyphiconWarning + that.getName(key) + errorListHtmlEnd;
                        });
                        msg += errorMessageHtmlEnd + errorWrapperHtmlEnd;
                        ajax_message(params.buttonGroupWrapper, msg, 'insertAfter');
                    }

                    if (succeeded.length > 0) {
                        ajax_message(params.buttonGroupWrapper, successWrapperHtmlStart + variables.fineUploader.uploadComplete + successWrapperHtmlEnd, 'insertAfter');
                    }
                },
                onComplete: function(id, name, responseJSON, xhr) {
                    if (responseJSON.id) {
                        var table = variables.tables[variables.datatablePrefix + params.table]; // get the DataTables api
                        if (typeof table.ajax.url() == 'function') {
                            table.clearPipeline().draw(false); // update clearPipeline to reset only current table // no pipeline: table.ajax.reload();
                        } else {
                            table.row.add({
                                id: responseJSON.id,
                                name: '',
                                size: formatSize(responseJSON.fileSize, this._options.text.sizeSymbols),
                                file: responseJSON.file,
                            }).draw(false);
                        }
                    }
                },
                onSubmit: function(id, name) {
                    this.setParams({
                        id: $('#' + params.id).data('pageId'),
                    });
                },
                onSubmitted: function(id, name) {
                    ajax_clear_messages(params.buttonGroupWrapper);
                    if (!$('#upload-progress-bar-container').length) {
                        params.buttonGroupWrapper.append(progressBarHtml);
                    }
                },
                onError: function(id, name, errorReason, xhr) {
                    if (xhr && xhr.responseText) {
                        var response = $.parseJSON(xhr.responseText);
                        if (response.refresh) { // VerifyCsrfToken exception
                            this.cancelAll();
                            window.location.reload(true);
                        }
                    }

                    var msg = errorWrapperHtmlStart + glyphiconWarning + errorReason + ' [<strong>' + name + '</strong>]' + errorWrapperHtmlEnd;
                    ajax_message(params.buttonGroupWrapper, msg, 'insertAfter');
                },
            },
        };

        $.extend(defaultConfig, config);

        var uploader = new qq.FineUploaderBasic(defaultConfig);
    }

    function formatSize(bytes, sizeSymbols) {
        var i = -1;
        do {
            bytes = bytes / 1024;
            i++;
        } while (bytes > 1023);

        return Math.max(bytes, 0.1).toFixed(1) + sizeSymbols[i];
    }

    function ajaxify(params) {
        var deferred = $.Deferred();

        ajax_abort(params.that);
        ajax_lock(params.that);

        if (params.method == 'get') {
            var jqxhr = $.getq(params.queue, params.action, params.data);
        } else {
            var jqxhr = $.postq(params.queue, params.action, params.data);
        }

        jqxhr.done(function(data, status, xhr) {
            var that = params.that;

            $.each(data, function(key) {
                if (typeof data[key] == 'object' && (data[key].updateTable || data[key].reloadTable)) {
                    var tableId = variables.datatablePrefix + key;
                    if (data[key].updateTable) {
                        that = $('#' + tableId).closest(ajaxLockClass);
                        var table = variables.tables[tableId]; // get the api
                        if (data[key].ajax && typeof table.ajax.url() == 'function') {
                            table.clearPipeline().draw(false); // update clearPipeline to reset only current table // no pipeline: table.ajax.reload();
                        } else if (data[key].data) {
                            table.clear().rows.add(data[key].data).draw();
                        } else {
                            // datatable initialized with DOM data so I can't use ajax to reload the data: table.ajax.url(data[key].url).load();
                        }
                    } else if (data[key].reloadTable) {
                        deferred.resolve(data[key]);
                    }

                    $('#' + tableId + ' tbody input[type="checkbox"]:checked').trigger('click');
                    variables.rows_selected[tableId] = [];
                }
            });

            if (data.redirect) {
                window.location.href = data.redirect;
            } else if (data.refresh) {
                window.location.reload(true);
            } else {
                ajax_unlock(params.that);
                ajax_reset(params.that, data);
                if (data.success) {
                    if (data.closePopup) {
                        params.that = that;
                        $.magnificPopup.close();
                        ajax_unlock(params.that);
                    }

                    ajax_success_text(params.that, data.success);
                } else if (data.errors) {
                    ajax_error(params.that, data);
                }
            }
        });

        jqxhr.fail(function(xhr, textStatus, errorThrown) {
            if (textStatus != 'abort') {
                ajax_unlock(params.that);
                if (xhr.status == 422) { // laravel response for validation errors
                    ajax_error_validation(params.that, xhr.responseJSON);
                } else {
                    ajax_error_text(params.that, textStatus + ': ' + errorThrown);
                }
            }
        });

        return deferred.promise();
    };

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

        that.lock_timer = window.setTimeout(function() {
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
        }, variables.lock_time);
        return true;
    };

    function ajax_unlock(that) {
        window.clearTimeout(that.lock_timer);
        ajax_clear_messages(that);
        $('[type=submit]', that).prop('disabled', false);
        $(ajaxLockedClass, that).remove();
        return true;
    };

    function ajax_abort(that) {
        if (/^sync-/.test(that.data('ajaxQueue')) && $.ajaxq.isRunning(that.data('ajaxQueue'))) {
            $.ajaxq.abort(that.data('ajaxQueue'));
        }
    };

    function ajax_message(that, msg, method) {
        var method = method || 'insertBefore';
        var $messages;

        $messages = that.prev(alertMessagesClass);
        if ($messages.length > 0) {
            $messages.append(msg)
        } else {
            $messages = that.next(alertMessagesClass);
            if ($messages.length > 0) {
                $messages.append(msg)
            } else {
                $messages = $(alertMessagesHtmlStart + msg + alertMessagesHtmlEnd);
                $messages[method](that);
            }
        }

        if (!isElementInViewport($messages[0])) {
            $.scrollTo($messages[0]);
        }
    };

    function ajax_clear_messages(that) {
        that.prev(alertMessagesClass).empty();
        that.next(alertMessagesClass).empty();
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

    function ajax_reset(that, data) {
        var excluded = [];
        var included = [];
        var i;

        if (data.resetOnly) {
            for (i = 0; i < data.resetOnly.length; i++) {
                included.push('#input-' + data.resetOnly[i]);
            }
            if (included.length) {
                ajax_reset_form(that, null, included.join());
            }
        } else if (data.resetExcept) {
            for (i = 0; i < data.resetExcept.length; i++) {
                excluded.push('#input-' + data.resetExcept[i]);
            }
            if (excluded.length) {
                ajax_reset_form(that, excluded.join());
            }
        } else if (data.reset) {
            ajax_reset_form(that);
        }

        if (data.resetMultiselect) {
            $.each(data.resetMultiselect, function(key, value) {
                var $multiselect = $('#' + key);

                if ($.inArray('empty', value) !== -1) {
                    $multiselect.empty();
                }

                if ($.inArray('disable', value) !== -1) {
                    $multiselect.multiselect('disable');
                }

                if ($.inArray('refresh', value) !== -1) {
                    $multiselect.multiselect('refresh');
                }
            });
        }
    }

    function ajax_error(that, data) {
        ajax_clear(that);

        var msg = '';
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

    function datatablesColumns(id, data) {
        var columns = [];

        $.each(data, function(key, value) {
            columns.push({
                data: value.id,
                title: (value.checkbox ? '<input type="checkbox" value="1" name="check-' + id + '" id="input-check-' + id + '">' : value.name),
                searchable: (value.search ? true : false),
                orderable: (typeof value.order !== 'undefined' ? false : true),
                className: (value.class ? value.class : ''),
            });
        });

        return columns;
    }

    function datatablesColumnDefs(data) {
        var columnDefs = [];

        $.each(data, function(key, value) {
            if (value.checkbox) {
                columnDefs.push({
                    targets: key,
                    width: '1.25em',
                    searchable: (value.search ? true : false),
                    orderable: (typeof value.order !== 'undefined' ? false : true),
                    className: (value.class ? value.class : ''),
                    render: function (data, type, full, meta) {
                        return '<input type="checkbox">';
                    }
                });
            }
        });

        return columnDefs;
    }

    // Updates "Select all" checkbox in a datatable
    function datatablesUpdateCheckbox(tableId) {
        var table = $('#' + tableId);
        var $checkbox_all = $('tbody input[type="checkbox"]', table);
        var $checkbox_checked = $('tbody input[type="checkbox"]:checked', table);
        var checkbox_select_all = $('thead input[type="checkbox"]', table).get(0);
        var $tableWrapper = table.closest('.dataTableWrapper');

        if ($checkbox_checked.length === 0) { // If none of the checkboxes are checked
            $tableWrapper.find('a' + variables.jsDestroyHook).addClass('disabled');
            $tableWrapper.find('a' + variables.jsEditHook).addClass('disabled');

            checkbox_select_all.checked = false;
            if ('indeterminate' in checkbox_select_all) {
                checkbox_select_all.indeterminate = false;
            }
        } else {
            $tableWrapper.find('a' + variables.jsDestroyHook).removeClass('disabled');
            if ($checkbox_checked.length == 1) {
                $tableWrapper.find('a' + variables.jsEditHook).removeClass('disabled');
            } else {
                $tableWrapper.find('a' + variables.jsEditHook).addClass('disabled');
            }

            if ($checkbox_checked.length === $checkbox_all.length) { // If all of the checkboxes are checked
                checkbox_select_all.checked = true;
                if ('indeterminate' in checkbox_select_all) {
                    checkbox_select_all.indeterminate = false;
                }
            } else { // If some of the checkboxes are checked
                checkbox_select_all.checked = true;
                if ('indeterminate' in checkbox_select_all) {
                    checkbox_select_all.indeterminate = true;
                }
            }
        }
    }

    function datatables(params) {
        $.each(params, function(id, param) {
            var tableId = variables.datatablePrefix + id;
            variables.rows_selected[tableId] = [];

            var checkbox = false;
            $.each(param.columns, function(key, value) {
                if (value.checkbox) {
                    checkbox = true;
                    return false;
                }
            });

            variables.tables[tableId] = $('#' + tableId).DataTable({
                dom: "<'clearfix'<'dataTableL'l><'dataTableF'f>>tr<'clearfix'<'dataTableI'i><'dataTableP'p>>",
                stateSave: true,
                stateDuration: -1,
                deferRender: true,
                retrieve: true,
                rowId: 'id',
                defaultContent: '',
                language: {
                    url: variables.datatablesLanguage
                },
                paging: param.count > variables.datatablesPaging ? true : false,
                searchDelay: param.ajax ? variables.datatablesSearchDelay : 100,
                serverSide: param.ajax ? true : false,
                pagingType: variables.datatablesPagingType[param.size],
                pageLength: variables.datatablesPageLength[param.size],
                lengthMenu: variables.datatablesLengthMenu[param.size],
                order: isNaN(param.orderByColumn) ? [] : [[param.orderByColumn, param.order]],
                ajax: param.ajax ? ajaxifyDatatables({ url: param.url }) : null,
                data: param.data ? param.data : null,
                columns: datatablesColumns(id, param.columns),
                columnDefs: datatablesColumnDefs(param.columns),
                createdRow: checkbox ? function(row, data, dataIndex) {
                    if ($.inArray(row.id, variables.rows_selected[tableId]) !== -1) {
                        $(row).find('input[type="checkbox"]').prop('checked', true);
                        $(row).addClass('selected');
                    }
                } : null,
                drawCallback: checkbox ? function(settings) {
                    // Update state of "Select all" checkbox
                    datatablesUpdateCheckbox(tableId);
                } : null,
            });
        });
    }

    function ajaxifyDatatables(params) {
        var params = $.extend({
            pipeline: variables.datatablesPipeline, // number of pages to cache/pipeline
            url: '',  // script url
            data: null, // function or object with parameters to send to the server matching how `ajax.data` works in DataTables
            method: 'get' // Ajax HTTP method
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
            if (requestLength < 0) { // all
                requestLength = 0;
            }
            var requestEnd = requestStart + requestLength;

            if (settings.clearCache) { // API requested that the cache be cleared
                ajax = true;
                settings.clearCache = false;
            } else if (requestLength == 0 || cacheLower < 0 || requestStart < cacheLower || requestEnd > cacheUpper) { // outside cached data - need to make a request
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

                var result = ajaxify({
                    that: that,
                    method: params.method,
                    queue: that.data('ajaxQueue'),
                    action: params.url,
                    data: request
                });

                result.done(function(data) {
                    cacheLastJson = $.extend(true, {}, data);
                    if (cacheLower != drawStart) {
                        data.data.splice(0, drawStart - cacheLower);
                    }

                    if (requestLength > 0) {
                        data.data.splice(requestLength, data.data.length);
                    }

                    if (data.search) {
                        var api = new $.fn.dataTable.Api(settings);
                        var $table = api.table().node();
                        var $wrapper = $($table).closest('.dataTableWrapper');
                        var $filter = $($wrapper).find(variables.filterClass + ' input');
                        $filter.focus();
                    }
                    callback(data);
                });
            } else {
                var json = $.extend(true, {}, cacheLastJson);
                json.draw = request.draw; // Update the echo for each response
                json.data.splice(0, requestStart - cacheLower);
                if (requestLength > 0) {
                    json.data.splice(requestLength, json.data.length);
                }
                callback(json);
            }
        }
    };

    return {run: run, variables: variables, datatables: datatables}
}();
