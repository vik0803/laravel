function placeholder()
{
    jQuery.support.placeholder = ('placeholder' in document.createElement('input'));
    if (!$.support.placeholder) {
        var active = document.activeElement;
        $(':text, input[type="date"], textarea').focus(function () {
            if ($(this).attr('placeholder') != '' && $(this).val() == $(this).attr('placeholder')) {
                $(this).val('').removeClass('js-ie-placeholder');
            }
        }).blur(function() {
            if ($(this).attr('placeholder') != '' && ($(this).val() == '' || $(this).val() == $(this).attr('placeholder'))) {
                $(this).val($(this).attr('placeholder')).addClass('js-ie-placeholder');
            }
        });
        $(':text, input[type="date"], textarea').blur();
        $(active).focus();
        $('form:eq(0)').submit(function() {
            $(':text.js-ie-placeholder, input[type="date"].js-ie-placeholder, textarea.js-ie-placeholder').val('');
        });
    }
}
