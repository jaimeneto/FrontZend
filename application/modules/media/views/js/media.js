$(document).ready(function(){
    Media_Youtube_AjaxSearch();

    $('#image_filters input, #image_adjustments select, #image_resize input, #modify-write')
    .change(function(){
        Media_Image_AjaxModify();
    });
    
    $('#media_form_image #credits').change(function(){
        if ($('#media_form_image #modify-write').is(':checked')) {
            Media_Image_AjaxModify();
        }
    });
    
    $('#media_form_image #revert_original').click(function(event){
        event.preventDefault();
        $(this).closest('form').get(0).reset();
        $('#media_form_image .ui-slider').each(function(){
            var sliderObj = $(this);
            sliderObj.slider('value', 0);
        });
        Media_Image_AjaxModify();
    });

    $('#toggle_original').change(function(){
        console.log('toggle');
        $('#modified_image, #original_image').toggle();
    })

    initSliders();

});

function Media_Youtube_AjaxSearch() {
    initYoutubeRemoveClick();

    if ($('.btn-youtube-search').length == 0) return;

    $.ajax({
        type: 'POST',
        url: adminBaseUrl + '/media/youtube/ajax-search-modal',
        data: {
            title: $('.btn-youtube-search').val()
        },
        dataType: 'json',
        success: function(json){
            if (json.status == 1) {
                $('body').append(json.content);
                
                // Se apertar enter no campo de texto de busca, apertar o botão
                $('#youtube_search_term').bind("keypress", function(event) {
                    if(event.keyCode == 13) {
                        $('#youtube_search_btn').click();
                    }
                });
                
                var fieldId;

                $('#youtube_search').modal({
                    show:false
                });
                $('#youtube_search').on('shown', function(){
                    autosizeInputWithAddOn();

                    if (!$('#youtube_search_term').val() && $('#title').val()) {
                        $('#youtube_search_term').val($('#title').val());
                        $('#youtube_search_btn').click();
                    }
                })

                $('.btn-youtube-search').click(function(){
                    fieldId = $(this).attr('id').replace('select_', '');
                    $('#youtube_search').modal('show');
                });

                $('#youtube_search_btn').click(function(){
                    var searchString = $('#youtube_search_term').val();

                    $('#youtube_search_results').html('<div class="progress" ' +
                        'style="margin:100px auto 0; width: 80%">' +
                        '<div class="progress-bar progress-bar-striped active" ' + 
                        'role="progressbar" aria-valuenow="100" aria-valuemin="0" ' + 
                        'aria-valuemax="100" style="width: 100%">' +
                        '</div></div>');

                    $.ajax({
                        type: 'POST',
                        url: adminBaseUrl + '/media/youtube/ajax-search',
                        data: {
                            term: searchString
                        },
                        dataType: 'json',
                        success: function(json){
                            if (json.status == 1) {
                                if (json.vars.youtubeId) {
                                    $('#youtube_search_results').html('');
                                    $('#youtube_search').modal('hide');
                                    modalYoutubeVideo(fieldId, json.vars.youtubeId, $('.btn-youtube-search').val());
                                } else {
                                    $('#youtube_search_results').html(json.content);
                                    $('#youtube_search_results a').click(function(){
                                        $('#youtube_search').modal('hide');
                                        var idVideo = $(this).attr('href').replace('#', '');
                                        var videoTitle = $(this).parent().find('.title').html();

                                        if (!$('#youtube_video_'+idVideo).length) {
                                            modalYoutubeVideo(fieldId, idVideo, videoTitle);
                                        }
                                        return false;
                                    });
                                }
                            } else {
                                if (json.log) console.log(json.log);
                                $('#youtube_search_results').html('<div class="alert alert-error">'
                                    + '<a href="#" class="close" data-dismiss="alert">&times;</a>'
                                    + '<strong>Erro!</strong> ' + json.msg + '</div>');
                            }
                        },

                        error: function(msg) {
                            alert(msg);
                        }
                    });
                });
            }
        },

        error: function(msg) {
            alert(msg);
        }
    });
}

function modalYoutubeVideo(fieldId, idVideo, videoTitle)
{
    $('body').append('<div class="modal fade" id="youtube_video_' + idVideo + '" ' + 
        'tabindex="-1" role="dialog" aria-hidden="true"' + 
        ' aria-labelledby="youtube_video_' + idVideo + '_label">' +
      '<div class="modal-dialog">' +
        '<div class="modal-content">' +
          '<div class="modal-header">' +
            '<button type="button" class="close" data-dismiss="modal" ' + 
                'aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
            '<h4 class="modal-title" id="youtube_video_' + idVideo + '_label">' + videoTitle + '</h4>' +
          '</div>' +
          '<div class="modal-body text-center">' +
            youtubeVideo(idVideo, 450, 338) +
          '</div>' +
          '<div class="modal-footer">' +
            '<button class="btn btn-primary" id="select_youtube_video_' + idVideo + '">Selecionar</button>' +
            '<button class="btn btn-default" data-dismiss="modal" aria-hidden="true" id="cancel_youtube_video_' + idVideo + '">Cancelar</button>' +
          '</div>' +
        '</div>' +
      '</div>' +
    '</div>');

    $('#youtube_video_' + idVideo).modal();
    $('#youtube_video_' + idVideo).on('hidden', function(){
        $(this).remove();
    });
    $('#cancel_youtube_video_' + idVideo).click(function(){
        $('#youtube_video_' + idVideo).modal('hide');
        $('#youtube_search').modal('show');
    });

    $('#select_youtube_video_' + idVideo).click(function() {
        $('#youtube_video_'+idVideo).modal('hide');
        selectYoutubeVideo(fieldId, idVideo);
    });
}

function selectYoutubeVideo(fieldId, idVideo) {
    idVideo = getIdYoutube(idVideo);
    var fieldName = fieldId.replace('meta-', '');
    $('#' + fieldId + '_preview').html('<div '
        + 'id="files_' + fieldId + '-' + idVideo + '" class="col-xs-6">'
        + '<input type="hidden" name="meta[' + fieldName + ']"'
        + ' id="' + fieldId + '"'
        + ' value="' + idVideo + '" />'
        + '<div id="' + fieldId + '_preview" class="youtube-preview">'
        + '<a id="remove_' + fieldId + '" class="file-remove pull-right" '
        + 'href="#files_' + fieldId + '-' + idVideo + '" title="Remover">' 
        + '<span class="glyphicon glyphicon-remove"></span></a>'
        + youtubeVideo(idVideo, 360, 270)
        + '</div></div>');
    
    initYoutubeRemoveClick();
}

function initYoutubeRemoveClick() {
    $('.youtube-preview .file-remove').click(function(){
        if (confirm('Deseja desassociar esse vídeo?')) {
            $($(this).attr('href')).remove();
        }
        return false;
    });
}


/**
 * Extrai o id de um video do youtube de um link
 * 
 * @param string string Link do video
 */
function getIdYoutube(string) {
    var idYoutubeLength = 11;
    var idYoutube = null;
    if (string.length == idYoutubeLength) {
        idYoutube = string;
    } else {
        var idStarts = string.indexOf("?v=");
        if(idStarts === false) {
            idStarts = string.indexOf("&v=");
        }
        if(idStarts === false) {
            idStarts = string.indexOf("/v/");
        }
        if (idStarts) {
            idStarts += 3;
            idYoutube = string.substr(idStarts, idYoutubeLength);
        }
    }

    if (idYoutube === null || idYoutube.length != idYoutubeLength) {
        throw new Exception('Vídeo do youtube inválido');
    }

    return idYoutube;
}

/**
 * Exibe um iframe com um video do youtube
 * 
 * @param string  idVideo Id do video do youtube
 * @param integer width   Largura do video
 * @param integer height  Altura do video
 * @param string  style   Estilos CSS inline
 */
function youtubeVideo(idVideo, width, height, style) {
    var attrWidth = ' width="320"';
    if (width) attrWidth = ' width="' + width + '"';
    var attrHeight = ' height="240"';
    if (height) attrHeight = ' height="' + height + '"';
    var styles = '';
    if (style) styles = 'style="'+style+'"';

    var obj = '<iframe id="youtube_video_' + idVideo + '" '
    + 'class="youtube-video" src="http://www.youtube.com/embed/'
    + idVideo + '?showinfo=0&amp;rel=0&amp;wmode=transparent" '
    + styles + attrWidth + attrHeight
    + ' frameborder="0" allowfullscreen></iframe>'

    return obj;
}

function Media_Image_AjaxModify() {

    var data = $('#media_form_image').serialize();

    $.ajax({
        type: 'POST',
        url: adminBaseUrl + '/media/image/ajax-modify',
        data: data,
        dataType: 'json',
        success: function(json){
            if (json.status == 1) {
                d = new Date();
                var src = $('#modified_image img').attr('src');
                $('#modified_image img').attr('src', json.src + '?' + d.getTime());
                $('#modified_image .size .width').html(json.width);
                $('#modified_image .size .height').html(json.height);
            }
            else {
                if (json.log) console.log(json.log);
            }
        },

        error: function(msg) {
            alert(msg);
        }
    });
}


function initSliders() {

    var selectBrightness = $('#modify-filter-brightness');
    $('<div id="slider-brightness"></div>').insertAfter(selectBrightness).slider({
        min: -255,
        max: 255,
        range: 'min',
        value: 0,
        create: function() {
            selectBrightness.hide();
        },
        slide: function( event, ui ) {
            selectBrightness.val(ui.value);
        },
        change: function() {
            Media_Image_AjaxModify();
        }
    });

    var selectContrast = $('#modify-filter-contrast');
    $('<div id="slider-contrast"></div>').insertAfter(selectContrast).slider({
        min: -255,
        max: 255,
        range: 'min',
        value: 0,
        create: function() {
            selectContrast.hide();
        },
        slide: function( event, ui ) {
            selectContrast.val(ui.value);
        },
        change: function() {
            Media_Image_AjaxModify();
        }
    });

    var selectGaussianBlur = $('#modify-filter-gaussian-blur');
    $('<div id="slider-gaussian-blur"></div>').insertAfter(selectGaussianBlur).slider({
        min: 0,
        max: 100,
        range: 'min',
        value: 0,
        create: function() {
            selectGaussianBlur.hide();
        },
        slide: function( event, ui ) {
            selectGaussianBlur.val(ui.value);
        },
        change: function() {
            Media_Image_AjaxModify();
        }
    });

    var selectSelectiveBlur = $('#modify-filter-selective-blur');
    $('<div id="slider-selective-blur"></div>').insertAfter(selectSelectiveBlur).slider({
        min: 0,
        max: 100,
        range: 'min',
        value: 0,
        create: function() {
            selectSelectiveBlur.hide();
        },
        slide: function( event, ui ) {
            selectSelectiveBlur.val(ui.value);
        },
        change: function() {
            Media_Image_AjaxModify();
        }
    });

    var selectMeanRemoval = $('#modify-filter-mean-removal');
    $('<div id="slider-mean-removal"></div>').insertAfter(selectMeanRemoval).slider({
        min: 0,
        max: 10,
        range: 'min',
        value: 0,
        create: function() {
            selectMeanRemoval.hide();
        },
        slide: function( event, ui ) {
            selectMeanRemoval.val(ui.value);
        },
        change: function() {
            Media_Image_AjaxModify();
        }
    });

    var selectPixelate = $('#modify-filter-pixelate');
    $('<div id="slider-pixelate"></div>').insertAfter(selectPixelate).slider({
        min: 0,
        max: 10,
        range: 'min',
        value: 0,
        create: function() {
            selectPixelate.hide();
        },
        slide: function( event, ui ) {
            selectPixelate.val(ui.value);
        },
        change: function() {
            Media_Image_AjaxModify();
        }
    });

    var selectColorizeRed = $('#modify-filter-colorize-red');
    $('<div id="slider-colorize-red"></div>').insertAfter(selectColorizeRed).slider({
        min: -255,
        max: 255,
        range: 'min',
        value: 0,
        create: function() {
            selectColorizeRed.hide();
        },
        slide: function( event, ui ) {
            selectColorizeRed.val(ui.value);
        },
        change: function() {
            Media_Image_AjaxModify();
        }
    });

    var selectColorizeGreen = $('#modify-filter-colorize-green');
    $('<div id="slider-colorize-green"></div>').insertAfter(selectColorizeGreen).slider({
        min: -255,
        max: 255,
        range: 'min',
        value: 0,
        create: function() {
            selectColorizeGreen.hide();
        },
        slide: function( event, ui ) {
            selectColorizeGreen.val(ui.value);
        },
        change: function() {
            Media_Image_AjaxModify();
        }
    });

    var selectColorizeBlue = $('#modify-filter-colorize-blue');
    $('<div id="slider-colorize-blue"></div>').insertAfter(selectColorizeBlue).slider({
        min: -255,
        max: 255,
        range: 'min',
        value: 0,
        create: function() {
            selectColorizeBlue.hide();
        },
        slide: function( event, ui ) {
            selectColorizeBlue.val(ui.value);
        },
        change: function() {
            Media_Image_AjaxModify();
        }
    });

    var selectColorizeAlpha = $('#modify-filter-colorize-alpha');
    $('<div id="slider-colorize-alpha"></div>').insertAfter(selectColorizeAlpha).slider({
        min: 0,
        max: 127,
        range: 'min',
        value: 0,
        create: function() {
            selectColorizeAlpha.hide();
        },
        slide: function( event, ui ) {
            selectColorizeAlpha.val(ui.value);
        },
        change: function() {
            Media_Image_AjaxModify();
        }
    })

}