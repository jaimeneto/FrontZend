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

    $(window).bind("resize", autosizeInputWithAddOn);
}