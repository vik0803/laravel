//@prepros-prepend plugins/*.js
//@prepros-prepend vendor/plugins/*.js

var lock_timer;
var lock_time = 100;
var jqxhr;

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
}

function ajax_lock(that, is_form) {
    if (is_form) {
        $('[type=submit]', that).prop('disabled', true);
    }

    lock_timer = window.setTimeout(function() {
        var lock_wrapper = (is_form ? $('#' + that.attr('id')) : that.closest('.ajax-lock'));
        $('<div tabindex="-1" id="ui-lock" class="locked"></div>').html('<div><span><img src="/img/cms/loading.gif" alt="Loading..." title="Loading...">Loading...<a title="Abort" id="ui-lock-cancel"><img src="/img/cms/stop.png" alt="Abort" title="Abort" /></a></span></div>').appendTo(lock_wrapper);
        window.setTimeout(function() {
            var ui = $('#ui-lock');
            ui.focus();

            $('#ui-lock-cancel').click(function() {
                ajax_unlock(that, is_form);
                return false;
            });

            ui.on("keydown", function(e) {
                e.preventDefault();
                e.stopPropagation();
                var code = e.keyCode ? e.keyCode : e.which;
                if (code == 27 || code == 13) { // ESC or ENTER
                    ajax_unlock(that, is_form);
                }
                return false;
            });

            ui.on("blur", function(e) {
                e.preventDefault();
                e.stopPropagation();
                window.setTimeout(function() {
                    ui.focus();
                }, 50);
                return false;
            });
        }, 50);
    }, lock_time);
    return true;
};

function ajax_unlock(that, is_form) {
    jqxhr.abort();
    $('#ajax-messages').empty();
    window.clearTimeout(lock_timer);
    if (is_form) {
        $('[type=submit]', that).prop('disabled', false);
    }
    $('#ui-lock').remove();
    return true;
};

function ajax_message(that, msg) {
    $('#ajax-messages').length > 0 ? $('#ajax-messages').append(msg) : $('<div id="ajax-messages" class="messages">' + msg + '</div>').prependTo(that);

    if (!isElementInViewport($('#ajax-messages')[0])) {
        $.scrollTo($('#ajax-messages'));
    }
}

function ajax_clear(that) {
    $('span.glyphicon-remove', that).remove();
    $('.form-group', that).removeClass('has-error has-feedback has-addon-feedback');
}

function ajax_error_validation(that, data) {
    ajax_clear(that);

    var msg = '';

    if (typeof data == 'object') {
        for (key in data) {
            if ($('#input-' + key).next().hasClass('input-group-addon')) {
                $('#input-' + key).after('<span class="glyphicon glyphicon-remove form-control-feedback"></span>').closest('.form-group').addClass('has-error has-feedback has-addon-feedback');
            } else {
                $('#input-' + key).closest('.form-group').addClass('has-error has-feedback').append('<span class="glyphicon glyphicon-remove form-control-feedback"></span>');
            }
            msg += '<p class="bg-danger"><span class="glyphicon glyphicon-warning-sign"></span>' + data[key] + '</p>';
        }
    }

    ajax_message(that, msg);
}

function ajax_error(that, data) {
    ajax_clear(that);

    var msg = '';
    var excluded = [];
    var included = [];

    if (typeof data == 'object') {

        if (data.errors) {
            for (var i = 0; i < data.errors.length; i++) {
                msg += '<p class="bg-danger"><span class="glyphicon glyphicon-warning-sign"></span>' + data.errors[i] + '</p>';
            }
        }

        if (data.ids) {
            for (var i = 0; i < data.ids.length; i++) {
                if ($('#input-' + data.ids[i]).next().hasClass('input-group-addon')) {
                    $('#input-' + data.ids[i]).after('<span class="glyphicon glyphicon-remove form-control-feedback"></span>').closest('.form-group').addClass('has-error has-feedback has-addon-feedback');
                } else {
                    $('#input-' + data.ids[i]).closest('.form-group').addClass('has-error has-feedback').append('<span class="glyphicon glyphicon-remove form-control-feedback"></span>');
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
}

function ajax_error_text(that, msg) {
    ajax_clear(that);
    msg = '<p class="bg-danger"><span class="glyphicon glyphicon-warning-sign"></span>' + msg + '</p>';
    ajax_message(that, msg);
}

function ajax_success_text(that, msg) {
    ajax_clear(that);
    msg = '<p class="bg-success"><span class="glyphicon glyphicon-ok"></span>' + msg + '</p>';
    ajax_message(that, msg);
}

function ajax_submit(el, is_form)
{
    $('#' + el).submit(function(event)
    {
        event.preventDefault();

        var that = $(this);
        ajax_lock(that, is_form);

        jqxhr = $.post(that.attr('action'), that.serialize());

        jqxhr.done(function(data, status, xhr)
        {
            ajax_unlock(that, is_form);
            if (data.redirect) {
                window.location.href = data.redirect;
            } else if (data.success) {
                ajax_success_text(that, data.html);
                ajax_reset_form(that);
            } else {
                ajax_error(that, data);
            }
        });

        jqxhr.fail(function(xhr, textStatus, errorThrown)
        {
            ajax_unlock(that, is_form);
            if (xhr.status == 422) { // laravel response for validation errors
                ajax_error_validation(that, xhr.responseJSON);
            } else {
                ajax_error_text(that, textStatus + ': ' + errorThrown);
            }
        });

        return false;
    });
}

$(document).ready(function() {
    placeholder();
});
