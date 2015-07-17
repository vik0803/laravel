//@prepros-prepend plugins/*.js
//@prepros-prepend vendor/plugins/*.js

var unikat = function() {
    'use strict';

    var JSVariables = {};

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

    $(document).ready(function() {
        placeholder();

        $(document).on('click', alertMessagesClass + ' ' + buttonCloseClass, function() {
            $(this).closest(alertMessagesClass).remove();
        });
    });

    function setJSVariables(data) {
        JSVariables = data;

        htmlLoading = '<div tabindex="-1" class="ajax-locked"><div><div><img src="' + JSVariables.loadingImageSrc + '" alt="' + JSVariables.loadingImageAlt + '" title="' + JSVariables.loadingImageTitle + '">' + JSVariables.loadingText + '</div></div></div>';
        errorMessageHtmlStart = JSVariables.ajaxErrorMessage + errorMessageHtmlStart;
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

    function ajax_submit(form) {
        $('#' + form).submit(function(e) {
            e.preventDefault();

            var that = $(this);
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
    };

    return { ajax_submit: ajax_submit, setJSVariables: setJSVariables }
}();
