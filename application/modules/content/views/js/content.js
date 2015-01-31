/****************************************************************************
    FrontZend
    Module: Content
    File: content.js
****************************************************************************/

$(document).ready(function(){
    initMetaFields();
    initRatingElements();
    Content_Content_AjaxSearch();
    Content_Content_AjaxGenerateSlug();
});

function initRatingElements()
{
    $('.rating.radio-inline').each(function(){
        $(this).parents('.form-group').addClass('rating');
        $(this).attr('title', $(this).html().replace(/<\/?[^>]+>/gi, ''));
    });
   
    $('.form-group.rating').each(function(){
        var half = $(this).find('input[type=radio]').size() == 10;
        var checked = $(this).find('input[type=radio]:checked');
        var checkedDescription = checked.parents('label').html();

        $(this).find('div').append('<span class="rating-description"></span>');

        
        // If accepts half stars, add class to display the first or second half
        if (half) {
            $(this).find('input[type=radio]').each(function(){
                if(parseFloat($(this).val()) == parseInt($(this).val())) {
                    $(this).parents('label').addClass('half2');
                } else {
                    $(this).parents('label').addClass('half1');
                }
            });
        }

        if (checkedDescription) {
            checkedDescription = checkedDescription.replace(/<\/?[^>]+>/gi, ''); // stripTags
            $(this).find('.rating-description').html(checkedDescription);
        }

        // Replace the radio buttons with stars
        $(this).find('input[type=radio]').each(function(){
            var iconClass = (parseFloat($(this).val()) <= parseFloat(checked.val()))
                ? 'glyphicon-star' 
                : 'glyphicon-star-empty';
            $(this).hide();
            $(this).parents('label').html($(this).clone().wrap('<p>').parent().html()
                + '<span class="glyphicon ' + iconClass + '"></span>');
        });
    });
    
    // Change the star icons and rating description accourding to the cursor
    $('.radio-inline.rating').hover(function(){
            var pointed = $(this).find('input[type=radio]');
            var inputRating = $(this).parents('.form-group.rating');
            var pointedDescription = $(this).attr('title');
            inputRating.find('.rating-description').html(pointedDescription);
            inputRating.find('input[type=radio]').each(function(){
                var starIcon = $(this).parents('label').find('span.glyphicon');
                if(parseFloat($(this).val()) <= parseFloat(pointed.val())) {
                    starIcon.removeClass('glyphicon-star-empty');
                    starIcon.addClass('glyphicon-star');
                } else {
                    starIcon.removeClass('glyphicon-star');
                    starIcon.addClass('glyphicon-star-empty');
                }
            });
        },function(){
            var inputRating = $(this).parents('.form-group.rating');
            var checked = inputRating.find('input[type=radio]:checked');
            var checkedDescription = checked.length
                ? checked.parents('label').attr('title') : '';
            inputRating.find('.rating-description').html(checkedDescription);
            inputRating.find('input[type=radio]').each(function(){
                var starIcon = $(this).parents('label').find('span.glyphicon');
                if(parseFloat($(this).val()) <= parseFloat(checked.val())) {
                    starIcon.removeClass('glyphicon-star-empty');
                    starIcon.addClass('glyphicon-star');
                } else {
                    starIcon.removeClass('glyphicon-star');
                    starIcon.addClass('glyphicon-star-empty');
                }
            });
        }
    );

    // Change the star icons and rating description on changing the value
    $('.form-group.rating input[type=radio]').change(function(){
        var groupRating = $(this).parents('.form-group.rating');
        var checked = groupRating.find('input[type=radio]:checked');
        var checkedDescription = checked.parent().attr('title');
        groupRating.find('.rating-description').html(checkedDescription);
        groupRating.find('input[type=radio]').each(function(){
            var starIcon = $(this).parents('label').find('span.glyphicon');
            if(parseFloat($(this).val()) <= parseFloat(checked.val())) {
                starIcon.removeClass('glyphicon-star-empty');
                starIcon.addClass('glyphicon-star');
            } else {
                starIcon.removeClass('glyphicon-star');
                starIcon.addClass('glyphicon-star-empty');
            }
        });
        uncheckRatingElement();
    });
    
    uncheckRatingElement();
}

function uncheckRatingElement()
{
    // If clicks the checked option, uncheck it
    $('.form-group.rating input[type=radio]:checked').click(function(){
        var groupRating = $(this).parents('.form-group.rating');
        if (groupRating.find('.control-label').hasClass('required')) {
            return;
        }
        $(this).prop('checked', false);
        groupRating.find('.rating-description').html('');
        groupRating.find('input[type=radio]').each(function(){
            var starIcon = $(this).parents('label').find('span.glyphicon');
            starIcon.removeClass('glyphicon-star');
            starIcon.addClass('glyphicon-star-empty');
            $(this).unbind('click');
        });
        uncheckRatingElement();
    });
}

function Content_Content_AjaxSearch()
{
    $('.content-search').each(function(){
        var elementId = $(this).attr('id').replace('_search','');
        var elementName = $(this).attr('data-name');
        var contentType = $(this).attr('data-type');
        var contentRelated = $(this).attr('data-related');

        // Evita o envio do formulário se o usuário apertar Enter
        $(this).parents('form').bind("keypress", function(event) {
            if(event.keyCode == 13) {
                return false;
            }
        });

        var id_content = $('#id_content').val();
        var source = contentType == 'contents'
            ? adminBaseUrl + '/content/content/ajax-search/'
            : adminBaseUrl + '/acl/user/ajax-search/';
        if (contentRelated) source += 'type/'+ contentRelated;
        if (id_content) source += '/id/' + id_content;

        $(this).focus(function(){
            $(this).val('');
        }).autocomplete({
            source: source,
            minLength: 2,
            select: function(event, ui) {
                if ($('#'+elementId+'-' + ui.item.id).length == 0) {
                    if($(this).attr('data-multiple') != 1) {
                        $('#'+elementId+'_search').parents('.input-group').parent().find('div.checkbox').remove();
                    }
                    
                    var listItem = '<div class="checkbox">'
                        + '<label for="'+elementId+'-' + ui.item.id + '">'
                        + '<input type="checkbox" value="' + ui.item.id + '" '
                        + 'id="'+elementId+'-' + ui.item.id + '" '
                        + 'name="'+elementName+'" checked="checked">'
                        + ui.item.filteredValue;

                    if (contentType == 'contents') {
                        listItem += ' <a title="editar conteúdo" target="_blank" '
                        + 'href="' + adminBaseUrl + '/content/content/edit/id/'
                        + ui.item.id + '">'
                        + '<span class="glyphicon glyphicon-edit"></span></a> '
                        + '<a title="Acessar página do conteúdo" target="_blank" '
                        + 'href="' + baseUrl + '/' + ui.item.slug + '">'
                        + '<span class="glyphicon glyphicon-globe"></span></a>'
                    }

                    listItem += '</label></div>';
                    
                    $('#'+elementId+'_search').parents('.input-group').parent().append(listItem);

                    $('#'+elementId+'-' + ui.item.id).focus();
                }

                $('#'+elementId+'-' + ui.item.id).parent().pisca();
            }
        });
    })
}

function Content_Content_AjaxGenerateSlug()
{
    $('#content_form_content #title').change(function(){
        var title = $(this).val();
        
        if ($('#content_form_content #id_content').length > 0 
                && $('#content_form_content #slug').val()) {
            return;
        }
        
        $.ajax({
            type: 'POST',
            url: adminBaseUrl + '/content/content/ajax-generate-slug',
            data: {
                title: title
            },
            dataType: 'json',
            success: function(json){
                if (json.status == 1) {
                    $('#content_form_content #slug').val(json.slug);
                } else {
                    alert(json.msg);
                }
            },

            error: function(msg) {
                alert(msg);
            }
        })
    });
}
