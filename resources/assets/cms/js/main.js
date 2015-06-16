//@prepros-prepend plugins/*.js
//@prepros-prepend vendor/plugins/*.js

var lock_timer;
var lock_time = 100;
var is_form = false;
var jqxhr;

function ajax_reset_form(form) {
    form.find('input:not(:button, :submit, :reset, :radio, :checkbox, [type=hidden]), select, textarea').val('');
    form.find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');
}

function ajax_lock(that) {
    if (is_form) {
        $('[type=submit]', that).prop('disabled', true);
    }

    lock_timer = window.setTimeout(function() {
        var lock_wrapper = (is_form ? $('#' + that.attr('id')) : that.closest('.ajax-lock'));
        $('<div tabindex="-1" id="ui-lock" class="locked"></div>').html('<div><span><img src="/img/loading.gif" alt="Loading..." title="Loading...">Loading...<a title="Abort" id="ui-lock-cancel"><img src="/img/stop.png" alt="Abort" title="Abort" /></a></span></div>').appendTo(lock_wrapper);
        window.setTimeout(function() {
            var ui = $('#ui-lock');
            ui.focus();

            $('#ui-lock-cancel').click(function() {
                ajax_unlock(that);
                return false;
            });

            ui.on("keydown", function(e) {
                e.preventDefault();
                e.stopPropagation();
                var code = e.keyCode ? e.keyCode : e.which;
                if (code == 27 || code == 13) { // ESC or ENTER
                    ajax_unlock(that);
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

function ajax_unlock(that) {
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

function ajax_error(that, data) {
    ajax_clear(that);

    var msg = '';

    if (typeof data == 'object') {
        for (var i = 0; i < data.ids.length; i++) {
            if ($('#input-' + data.ids[i]).next().hasClass('input-group-addon')) {
                $('#input-' + data.ids[i]).after('<span class="glyphicon glyphicon-remove form-control-feedback"></span>').closest('.form-group').addClass('has-error has-feedback has-addon-feedback');
            } else {
                $('#input-' + data.ids[i]).closest('.form-group').addClass('has-error has-feedback').append('<span class="glyphicon glyphicon-remove form-control-feedback"></span>');
            }
        }

        for (var i = 0; i < data.errors.length; i++) {
            msg += '<p class="bg-danger"><span class="glyphicon glyphicon-warning-sign"></span>' + data.errors[i] + '</p>';
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

$(document).ready(function() {
    placeholder();
});
