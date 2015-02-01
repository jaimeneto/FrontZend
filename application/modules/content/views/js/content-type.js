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
        handle: ".panel-heading",
        placeholder: "accordion-group sortable-placeholder"
    });

    Content_ContentType_RemoveMetafield();

    $('.meta-label').unbind('change');
    $('.meta-label').change(function(){
        var name = $(this).attr('id').replace('-label', '').replace('meta-', '');
        $('#heading_' + name + ' .panel-title span').html($(this).val());
    });

    $('.meta-field-rating-half-flag').unbind('change');
    $('.meta-field-rating-half-flag').change(function(){
        var metaConfigs = $(this).parents('.checkbox').parent();
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
    $('#add_meta_field_btn').unbind('click');
    $('#add_meta_field_btn').click(function(){
        var fieldtype = $('input[name="add_meta_field_type"]:checked').val();
        var datatype  = $('input[name="add_meta_field_type"]:checked').attr('rel');
        var name      = $('#add_meta_field_name').val();

        if ($('#meta_fields_' + name + '-element').length > 0) {
            alert('Esse nome de campo já está sendo usado: ' + name);
        } else {
            $.ajax({
                type: 'POST',
                url: adminBaseUrl + '/content/content-type/ajax-add-metafield',
                data: {
                    type: $('#id_content_type').val(),
                    fieldtype: fieldtype,
                    datatype: datatype,
                    name: name
                },
                dataType: 'json',
                success: function(json){
                    if (json.status == 1) {
                        $('#main_elements ul').append('<li class="panel panel-default">'
                                + json.content
                                + '</li>');

                        $('#add_meta_field_name').val('');
                        $('input[name=add_meta_field_type]').prop('checked', false);
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
    $('.remove-meta').unbind('click');
    $('.remove-meta').click(function(){
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