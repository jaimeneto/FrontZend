/****************************************************************************
    FrontZend
    Module: Content
    File: content-file.js
****************************************************************************/

$(document).ready(function(){
    initSelectImage();
    initUploader();
    initDisassociateImage();
    initSortingImages();
});

function initSortingImages()
{
    $("ul.thumbnails.sortable-list").sortable({
        handle: ".icon-move",
        placeholder: "ui-state-highlight"
    });
    $("ul.sortable-list").disableSelection();
}

function initSelectImage()
{
    $('.content-form-meta-file-images .btn-select-image').click(function(){
        if (!($('#file_list').length > 0)) {
            $('body').append('<div class="modal fade" tabindex="-1" ' + 
                'role="dialog" aria-labelledby="modal_label" id="file_list" ' + 
                    'aria-hidden="true"><div class="modal-dialog modal-lg">' +
                    '<div class="modal-content"></div></div></div>');
        }

        var images = $(this).parent().find('ul.thumbnails');
        var target = $(this).attr('id').replace('select_meta_', '');
        var path = 'images|' + $('#id_content_type').val() + '|'
                 + $('#slug').val().substr(0, 1) + '|' + $('#slug').val();

        $.ajax({
            type: 'POST',
            url: adminBaseUrl + '/media/image/ajax-manage',
            data: {
                path: path
            },
            dataType: 'json',
            success: function(json){
                if (json.status == 1) {
                    $('#file_list .modal-content').html(
                        '<div class="modal-header">' +
                        '<button type="button" class="close" ' + 
                        'data-dismiss="modal" aria-label="Close">' + 
                        '<span aria-hidden="true">&times;</span></button>' +
                        '<h4 class="modal-title" id="modal_label">' + 
                        'Selecione uma imagem</h4></div>' +
                        '<div class="modal-body">' + json.content + '</div>');

                    $('#file_list').modal('show');

                    $('#file_list .thumbnail').unbind('click');
                    $('#file_list .thumbnail').click(function(){
                        var fileId = $(this).attr('href').replace('#', '');
                        if ($('#files_meta-'+ target + '-' + fileId).length == 0) {
                            var newItem = '<li id="files_meta-'+ target + '-' + fileId + '">' +
                                '<input type="hidden" id="meta-'+ target + '-' + fileId + '" ' +
                                    'value="' + fileId + '" name="meta[' + target + '][]">' +
                                '<a title="Remover" href="#files_meta-'+ target + '-' + fileId + '" ' +
                                    'class="pull-right file-remove" ' +
                                    'id="remove_meta-'+ target + '-' + fileId + '">' +
                                '<span class="glyphicon glyphicon-remove"></span>' +
                                '</a>' +
                                '<div type="images" class="thumbnail file-preview" ' +
                                    'id="meta-'+ target + '-' + fileId + '_preview">' +
                                    '<img alt="" src="' + baseUrl + '/files/' +
                                        $(this).attr('rel') + '">' +
                                '</div>' +
                                '</li>';

                            if (images.hasClass('single')) {
                                $(images).find('li').each(function(){
                                    if (!$(this).hasClass('.image-uploader')) {
                                        $(this).remove();
                                    }
                                });
                            }

                            $(images).append(newItem);
                        }
                        initDisassociateImage();
                        return false;
                    });
                } else {
                    if (json.log) console.log(json.log);
                    alert(json.msg);
                }
            },

            error: function(msg) {
                alert(msg);
            }
        });

        $('#file_list').modal();
    });
}

function initUploader()
{
    if ($('.image-uploader').length > 0) {
        var contentType = $('#id_content_type').val();
        var slug = $('#slug').val();
        var path = 'images\\' + contentType + '\\'
                 + slug.substr(0, 1) + '\\' + slug;

        var uploader = new Array();
        var i = 0;
        $('.image-uploader').each(function(){
            var target = $(this).parent().parent().find('.btn-select-image').attr('id').replace('select_meta_', '');

            uploader[i] = new qq.FileUploader({
                element: $(this).get(0),
                action: adminBaseUrl + '/media/image/ajax-upload',
                params: {
                    path: path,
                    template: 'media-grid',
                    target: target
                },
                allowedExtensions: ['jpg', 'jpeg', 'gif', 'png'],
                uploadButtonText: 'Enviar imagem',
                cancelButtonText: 'Cancelar',
                dragText: '',
                listElement: $(this).parent().get(0),
//                extraDropzones: [qq.getByClass(document, 'file-upload-drop-area')[i]],
//                debug: true,
                 messages: {
                    typeError: "{file} tem um extensão inválida. Permitidos: {extensions}.",
                    sizeError: "{file} é grande demais, o tamanho máximo permitido é {sizeLimit}.",
                    minSizeError: "{file} é muito pequeno, o tamanho mínimo permitido é {minSizeLimit}.",
                    emptyError: "{file} está vazio, selecione os arquivos novamente sem este.",
                    noFilesError: "Não há arquivos para serem enviados.",
                    onLeave: "Os arquivos estão sendo enviados, se sair agora o envio será cancelado."
                },

                fileTemplate: '<li>'
                    + '<div class="file">'
                    + '<a href="#">'
                    + '<div class="qq-progress-bar"></div>'
                    + '<div class="qq-upload-spinner"></div>'
                    + '<div class="qq-upload-finished"></div>'
                    + '<div class="qq-upload-file"></div>'
                    + '<div class="qq-upload-size"></div>'
                    + '<div class="qq-upload-failed-text">{failUploadtext}</div>'
                    + '</a>'
                    + '<div title="cancelar" class="filename"><a href="#" class="qq-upload-cancel">{cancelButtonText}</a></div>'
                    + '</div>'
                    + '</li>',

                onComplete: function(id, filename, json) {
                    if (json.success){
                        $('.qq-upload-success')
                            .removeClass('qq-upload-success')
                            .attr('id', 'files_meta-' + target + '_' + json.id)
                            .html(json.content);

                        initDisassociateImage();
                    }

                    if(json.console) {
                        console.log(json.console);
                    }
                }
            });
            i++;
        });
    }
}

function initDisassociateImage()
{
    $('.content-form-meta-file-images .file-remove').unbind('click');
    $('.content-form-meta-file-images .file-remove').click(function(){
        var image = $(this).attr('href').replace('#', '');

        if (confirm('Deseja remover essa imagem da lista?\n(A imagem não será excluída)')) {
            $(this).parents('li').remove();
        }

        return false;
    });
}