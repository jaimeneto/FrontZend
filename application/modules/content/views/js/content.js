/****************************************************************************
    FrontZend
    Module: Content
    File: content.js
****************************************************************************/

$(document).ready(function(){
    initMetaFields();
    initRatingElements();
    Content_Content_AjaxSearch();
});

function initRatingElements()
{
    $('.radio.rating').each(function(){
        $(this).parents('.control-group').addClass('rating');
        $(this).parents('.controls').addClass('rating');
        $(this).attr('title', $(this).html().replace(/<\/?[^>]+>/gi, ''));
    });

    $('.controls.rating').each(function(){
        var half = $(this).find('input[type=radio]').size() == 10;
        var checked = $(this).find('input[type=radio]:checked');
        var checkedDescription = checked.parents('label').html();

        $(this).append('<div class="rating-description"></div>');

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
            // Shows de description of the selected rating
            checkedDescription = checkedDescription.replace(/<\/?[^>]+>/gi, ''); // stripTags
            var checkedDescription = checked.parents('label').html().replace(/<\/?[^>]+>/gi, '');
            $(this).find('.rating-description').html(checkedDescription);
        }

        // Replace the radio buttons with stars
        $(this).find('input[type=radio]').each(function(){
            var iconClass = (parseFloat($(this).val()) <= parseFloat(checked.val()))
                ? 'icon-large icon-star' : 'icon-large icon-star-empty';
            $(this).hide();
            $(this).parents('label').html($(this).clone().wrap('<p>').parent().html()
                + '<i class="' + iconClass + '"></i>');
        });
    });

    // Change the star icons and rating description accourding to the cursor
    $('.radio.rating').hover(function(){
            var pointed = $(this).find('input[type=radio]');
            var inputRating = $(this).parents('.controls.rating');
            var pointedDescription = $(this).attr('title');
            inputRating.find('.rating-description').html(pointedDescription);
            inputRating.find('input[type=radio]').each(function(){
                if(parseFloat($(this).val()) <= parseFloat(pointed.val())) {
                    $(this).parents('label').find('i').removeClass('icon-star-empty');
                    $(this).parents('label').find('i').addClass('icon-star');
                } else {
                    $(this).parents('label').find('i').removeClass('icon-star');
                    $(this).parents('label').find('i').addClass('icon-star-empty');
                }
            });
        },function(){
            var inputRating = $(this).parents('.controls.rating');
            var checked = inputRating.find('input[type=radio]:checked');
            var checkedDescription = checked.length
                ? checked.parents('label').attr('title') : '';
            inputRating.find('.rating-description').html(checkedDescription);
            inputRating.find('input[type=radio]').each(function(){
                if(parseFloat($(this).val()) <= parseFloat(checked.val())) {
                    $(this).parents('label').find('i').removeClass('icon-star-empty');
                    $(this).parents('label').find('i').addClass('icon-star');
                } else {
                    $(this).parents('label').find('i').removeClass('icon-star');
                    $(this).parents('label').find('i').addClass('icon-star-empty');
                }
            });
        }
    );
        
    // Change the star icons and rating description on changing the value
    $('.radio.rating input[type=radio]').change(function(){
        var inputRating = $(this).parents('.controls.rating');
        var checked = inputRating.find('input[type=radio]:checked');
        var checkedDescription = checked.parent().attr('title');
        inputRating.find('.rating-description').html(checkedDescription);
        inputRating.find('input[type=radio]').each(function(){
            if(parseFloat($(this).val()) <= parseFloat(checked.val())) {
                $(this).parents('label').find('i').removeClass('icon-star-empty');
                $(this).parents('label').find('i').addClass('icon-star');
            } else {
                $(this).parents('label').find('i').removeClass('icon-star');
                $(this).parents('label').find('i').addClass('icon-star-empty');
            }
        });
    });

}

function Content_Content_AjaxSearch()
{
    $('.content-search').each(function(){
        var elementId = $(this).attr('id').replace('_search','');
        var elementName = $(this).attr('data-name');
        var contentType = $(this).attr('data-type');

        // Evita o envio do formulário se o usuário apertar Enter
        $(this).parents('form').bind("keypress", function(event) {
            if(event.keyCode == 13) {
                return false;
            }
        });

        var id_content = $('#id_content').val();
        var source = adminBaseUrl + '/content/content/ajax-search/';
        if (contentType) source += 'type/'+ contentType;
        if (id_content) source += '/id/' + id_content;

        $(this).focus(function(){
            $(this).val('');
        }).autocomplete({
            source: source,
            minLength: 2,
            select: function(event, ui) {
                if ($('#'+elementId+'-' + ui.item.id).length == 0) {
                    if($(this).attr('data-multiple') != 1) {
                        $('#'+elementId+'_search').parents('.controls').find('label.checkbox').remove();
                    }
                    
                    $('#'+elementId+'_search').parents('.controls').append(
                        '<label for="'+elementId+'-' + ui.item.id + '" class="checkbox">'
                        + '<input type="checkbox" value="' + ui.item.id + '" '
                        + 'id="'+elementId+'-' + ui.item.id + '" '
                        + 'name="'+elementName+'" checked="checked">'

                        + '<span>'
                        + ui.item.filteredValue
                        + ' <a title="editar conteúdo" target="_blank" '
                        + 'href="/sites/blockscms/public/content/content/edit/id/'
                        + ui.item.id + '">'
                        + '<i class="icon-edit"></i></a> '
                        + '<a title="Acessar página do conteúdo" target="_blank" '
                        + 'href="/sites/blockscms/public/content/content/view/id/113">'
                        + '<i class="icon-globe"></i></a></span>'

                        + '</label>');

                        $('#'+elementId+'-' + ui.item.id).focus();
                }

                $('#'+elementId+'-' + ui.item.id).parent().pisca();
            }
        });
    })
}