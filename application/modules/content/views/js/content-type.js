/****************************************************************************
    FrontZend
    Module: Content
    File: content-type.js
****************************************************************************/

$(document).ready(function(){
    initMetaFields();
});

function initMetaFields()
{
   initMetaConfigMultiOptions();

    $('ul.sortable').sortable({
        axis: "y",
        forcePlaceholderSize: true,
        handle: ".accordion-heading",
        placeholder: "accordion-group sortable-placeholder"
    });

    Content_ContentType_RemoveMetafield();

    $('.meta-fields-label').unbind('change');
    $('.meta-fields-label').change(function(){
        var name = $(this).attr('id').replace('-label', '')
            .replace('meta-field-', '')
            .replace('meta-relationship-', '')
            .replace('meta-file-', '');
        $('a[href="#meta_fields_' + name + '-element"]').html($(this).val());
    });

    $('.meta-field-rating-half-flag').unbind('change');
    $('.meta-field-rating-half-flag').change(function(){
        var metaConfigs = $(this).parents('.controls');
        if ($(this).is(':checked')) {
            metaConfigs.find('.meta-field-rating-multioptions-half-off').slideUp();
            metaConfigs.find('.meta-field-rating-multioptions-half-on').slideDown();
        } else {
            metaConfigs.find('.meta-field-rating-multioptions-half-off').slideDown();
            metaConfigs.find('.meta-field-rating-multioptions-half-on').slideUp();
        }
    });

    Content_ContentType_AjaxAddMetafield();
}

function Content_ContentType_AjaxAddMetafield()
{
    $('#add_meta_field_btn, #add_meta_relationship_btn, #add_meta_file_btn').unbind('click');
    $('#add_meta_field_btn, #add_meta_relationship_btn, #add_meta_file_btn').click(function(){
        var metaElementsId = $(this).parents('.meta-elements').attr('id');
        var datatype       = $(this).attr('id').replace('add_meta_', '').replace('_btn', '');
        var fieldtype      = $('input[name="add_meta_' + datatype + '_type"]:checked').val();
        var name           = $('#add_meta_' + datatype + '_name').val();

        if ($('#meta_fields_' + name + '-element').length > 0) {
            alert('Esse nome de campo já está sendo usado: ' + name);
        } else {
            $.ajax({
                type: 'POST',
                url: adminBaseUrl + '/content/content-type/ajax-add-metafield',
                data: {
                    type: $('#id_content_type').val(),
                    datatype: datatype,
                    fieldtype: fieldtype,
                    name: name
                },
                dataType: 'json',
                success: function(json){
                    if (json.status == 1) {
                        $('#' + metaElementsId + ' ul').append('<li class="accordion-group">'
                                + json.content
                                + '</li>');

                        $('#' + metaElementsId + ' #add_meta_' + datatype + '_name').val('');
                        $('#' + metaElementsId + ' input[name=add_meta_field_type]').attr('checked', false);
                        $('#' + metaElementsId + ' input[name=add_meta_file_type]').attr('checked', false);
                        $('#' + metaElementsId + ' input[name=add_meta_relationship_type]').attr('checked', false);
                        initMetaFields();
                    } else {
                        alert(json.msg);
                    }
                },

                error: function(msg) {
                    alert(msg);
                }
            })
        }
    })
}

function initMetaConfigMultiOptions()
{
    $('textarea.multiOptions').unbind('change');
    $('textarea.multiOptions').change(function(){
       var id = $(this).attr('id').replace('-multiOptions','');
       var options = '\n<option value=""></option>';
       $.each($(this).val().split("\n"), function(key, val){
           var keyVal = val.split(':');
           options += '\n<option value="' + keyVal[0] + '">' + keyVal[1].trim() + '</option>';
       })

       var objValue = $('#' + id + '-value');
       var currentValue = objValue.val();
       objValue.html(options);
       objValue.val(currentValue);
   })

}


function Content_ContentType_RemoveMetafield()
{
    $('.remove-meta-field').unbind('click');
    $('.remove-meta-field').click(function(){
        if (confirm("Tem certeza de que quer excluir este campo?\n(Só será excluído realmente ao salvar o formulário)")) {
            $(this).parents('li').slideUp('slow', function(){
                $(this).remove();
            });

//            var metafield = $(this).attr('id').replace('meta_fields-', '').replace('-remove', '');
//            $.ajax({
//                type: 'POST',
//                url: adminBaseUrl + '/content/content-type/remove-metafield',
//                data: {
//                    field: metafield,
//                    type: $('#type').val()
//                },
//                dataType: 'json',
//                success: function(json){
//                    if (json.status == 1) {
//                        $('#meta_fields_' + metafield + '-element').parents('li').remove();
//                    } else {
//                        alert(json.msg);
//                    }
//                },
//
//                error: function(msg) {
//                    alert(msg);
//                }
//            })
        }
    })
}