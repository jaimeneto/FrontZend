$(function(){
    initForms();

    $('.nav-tabs > li > a').click(function(){
        $(this).blur();
    });
});

function initForms() {
    $('input[type=file]').each(function(){
        var attrId = $(this).attr('id');

        if (attrId && $('#' + attrId + '_beauty').length == 0) {
            $(this).hide();
            $(this).after(
                '<div class="input-append" id="' + attrId + '_beauty">' +
                    '<input id="' + attrId + '_value" type="text" ' +
                    'value="' + $(this).val() + '" ' +
                    'class="' + $(this).attr('class') +'" />' +
                    '<a id="' + attrId + '_btn" href="#" class="add-on btn">Selecionar</a>' +
                '</div>'
            );
            $('#' + attrId + '_btn, #' + attrId + '_value').click(function(){
                $('#' + attrId).click();
                return false;
            });
            $(this).change(function(){
                $('#' + attrId + '_value').val($(this).val());
            });
        }
    });

    autosizeInputWithAddOn();

    $(window).bind("resize", autosizeInputWithAddOn);
}

function autosizeInputWithAddOn() {
    $('.input-prepend, .input-append').each(function(){
        var widthTotal = $(this).parent().width();
        var input = $(this).find('.input-block-level');
        if (input) {
            var addonWidth = 0;
            $(this).find('.add-on').each(function(){
                addonWidth += getTotalWidth($(this));
            });
            input.css('width', widthTotal - addonWidth);
        }
    });
}

function getTotalWidth(elementObj) {
    return elementObj.width()
        + parseInt(elementObj.css("padding-left"), 10)
        + parseInt(elementObj.css("padding-right"), 10)
        + parseInt(elementObj.css("margin-left"), 10)
        + parseInt(elementObj.css("margin-right"), 10)
        + parseInt(elementObj.css("borderLeftWidth"), 10)
        + parseInt(elementObj.css("borderRightWidth"), 10);
}
