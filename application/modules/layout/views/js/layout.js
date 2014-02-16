$(document).ready(function() {
    layoutInitBlocks();
    Layout_Block_AjaxToggle();
    Layout_Block_AjaxSetup();
    initRemoveBlock();
    layoutToggleBlockData();
    layoutToggleArea();
});

/**
 * Permite alterar a ordem dos blocos e/ou mover parar outras áreas e
 * adicionar um novo bloco a uma das áreas, dentre as opções da lista de blocos
 */
function layoutInitBlocks() {
    $('#layout_body > ul').sortable({
        connectWith: '#layout_body > ul',
        forceHelperSize: true,
        start: function(event, ui) {
            ui.placeholder.height(ui.helper.outerHeight());
        },
        update: function(event, ui) {
            if (ui.item.find('.layout-area-id').length == 0) {
                var area = generateSerial(8);
                ui.item.prepend('<input class="layout-area-id" type="hidden" '
                        + 'name="areas[' + area + '][id]" value="layout-area" />');

                ui.item.find('.columns > .column').each(function() {
                    var column = generateSerial(8);
                    $(this).prepend('<input type="hidden"'
                            + ' class="layout-column-id" value="layout-column"'
                            + ' name="areas[' + area + '][columns][' + column + '][id]" />'
                            + '<input type="hidden" class="layout-column-class" '
                            + ' name="areas[' + area + '][columns][' + column + '][class]"'
                            + ' value="' + $(this).attr('class').replace('column', '').trim() + '" />');
                });
                layoutInitBlocks();
            }
        }
    }).disableSelection();

    $('#areas_list li.area').draggable({
        helper: 'clone',
        connectToSortable: '#layout_body > ul'
    });


    /* Permite alterar a ordem dos blocos e/ou mover parar outras áreas */
    $('#layout_body > ul > li.area > .columns > .column > ul').sortable({
        connectWith: '#layout_body > ul > li.area > .columns > .column > ul',
        forceHelperSize: true,
        update: function(event, ui) {
            if ($(ui.item).find('.toggle').length == 0 || $(ui.item).find('.toggle').hasClass('disabled')) {
                $(ui.item).addClass('disabled');
            }

            var name = ui.item.parents('.column').find('.layout-column-id').attr('name').replace('[id]', '[blocks][]');
            ui.item.find('input[type=hidden]').attr('name', name);

            initRemoveBlock();
//            alignColumnsHeights();
        }
    }).disableSelection();

    /* Permite adicionar um novo bloco a uma das áreas, dentre as opções da lista de blocos */
    $('#blocks_list li.block').draggable({
        helper: 'clone',
        connectToSortable: '#layout_body > ul > li.area > .columns > .column > ul',
        start: function(event, ui) {
            $(event.target).parents('.accordion-body').css('overflow', 'visible');
        },
        stop: function(event, ui) {
            $(event.target).parents('.accordion-body').css('overflow', 'hidden');
        }
    });

    $('#layout_body > ul > li.area > .columns > .column').each(function() {
        var col = $(this);
        if (col.find('.resizer').length == 0 && col.index() != 0) {
            col.prepend('<div class="resizer" title="Redimensionar colunas">'
                    + '<i class="icon-arrow-left"></i>'
                    + '<i class="icon-arrow-right"></i>'
                    + '</div>');

            col.find('.resizer i').click(function() {
                var colClass = col.attr('class').replace('column', '').trim();
                var newClass = $(this).hasClass('icon-arrow-left')
                        ? 'span' + (parseInt(colClass.replace('span', '')) + 1).toString()
                        : 'span' + (parseInt(colClass.replace('span', '')) - 1).toString();
                var prevCol = $(this).parents('.columns').find('.column').get(col.index() - 1);
                var prevColClass = $(prevCol).attr('class').replace('column', '').trim();
                var newPrevClass = $(this).hasClass('icon-arrow-left')
                        ? 'span' + (parseInt(prevColClass.replace('span', '')) - 1).toString()
                        : 'span' + (parseInt(prevColClass.replace('span', '')) + 1).toString();

                if (newClass != 'span0' && newPrevClass != 'span0') {
                    col.removeClass(colClass).addClass(newClass);
                    col.find('> .layout-column-class').val(newClass);
                    $(prevCol).removeClass(prevColClass).addClass(newPrevClass);
                    $(prevCol).find('> .layout-column-class').val(newPrevClass);
                }
            });
        }

        col.find('.splitter span').unbind('click');
        col.find('.splitter span').click(function() {
            var colClass = col.attr('class').replace('column', '').trim();
            var newClass = 'span' + Math.ceil((parseInt(colClass.replace('span', '')) / 2)).toString();
            var newColClass = 'span' + Math.floor((parseInt(colClass.replace('span', '')) / 2)).toString();

            var areaId = $(this).parents('.area').find('.layout-area-id').val();
            var newColumnId = generateSerial(8);

            col.removeClass(colClass).addClass(newClass);
            col.after('<div class="column ' + newColClass + '">'
                    + '<ul class="ui-sortable"></ul>'
                    + '<input type="hidden" name="areas[' + areaId + '][columns][' + newColumnId + '][id]" value="layout-column" class="layout-column-id" />'
                    + '<input type="hidden" value="' + newColClass + '" name="areas[' + areaId + '][columns][' + newColumnId + '][class]" class="layout-column-class" />'
                    + '<div class="splitter" title="Dividir coluna"><span>|</span></div>'
                    + '</div>');

            layoutInitBlocks();
        });
    })
}

/**
 * Exibe/oculta uma tabela com os dados do bloco
 */
function layoutToggleBlockData() {
    $('#layout_body .block .options .data').click(function(event) {
        event.preventDefault();
        var table = $(this).parents('.block').find('table');
        var icon = $(this).find('i');
        table.slideToggle('normal'/*, function(){
         alignColumnsHeights();
         }*/);
        icon.toggleClass('icon-chevron-down').toggleClass('icon-chevron-up');
    });
}

function layoutToggleArea() {
    $('#options_areas input').change(function() {
        var area = $(this).attr('id').replace('_toggle', '');
        $('#' + area).toggle();

        if ($('#left_column').is(':visible')) {
            $('#middle_column').removeAttr('style');
        } else {
            $('#middle_column').css('margin-left', 0);
        }

        if (!$('#left_column').is(':visible') && !$('#right_column').is(':visible')) {
            $('#middle_column').removeClass('span6').removeClass('span9').addClass('span12');
        }
        if ((!$('#left_column').is(':visible') && $('#right_column').is(':visible')) ||
                ($('#left_column').is(':visible') && !$('#right_column').is(':visible'))) {
            $('#middle_column').removeClass('span6').removeClass('span12').addClass('span9');
        }
        if ($('#left_column').is(':visible') && $('#right_column').is(':visible')) {
            $('#middle_column').removeClass('span9').removeClass('span12').addClass('span6');
        }
    });
}

/**
 * Oculta um bloco, ou torna-o visÃ­vel
 */
function Layout_Block_AjaxToggle() {
    $('#layout_body .options .toggle').click(function(event) {
        event.preventDefault();
        var toggleIcon = $(this).find('i');
        toggleIcon.blur();
        var block = $(this).parent().parent();
        var currentIcon = toggleIcon.attr('class');

        toggleIcon.attr('class', 'icon-loading');
        block.removeClass('disabled');
        $.ajax({
            type: 'POST',
            url: adminBaseUrl + '/layout/block/ajax-toggle',
            data: {
                id: block.attr('id').replace('block_', '')
            },
            dataType: 'json',
            success: function(json) {
                if (json.status == 1) {
                    if (json.vars.visible == 1) {
                        toggleIcon.attr('class', 'icon-eye-open');
                    } else {
                        toggleIcon.attr('class', 'icon-eye-close');
                        block.addClass('disabled');
                    }
                } else {
                    alert(json.msg);
                    toggleIcon.attr('class', currentIcon);
                }
            },
            error: function(msg) {
                alert(msg);
                toggleIcon.attr('class', currentIcon);
            }
        })
    });
}

/**
 * Exibe um modal com um formulário para configurar o bloco
 */
function Layout_Block_AjaxSetup() {
    $('#layout_body .options .setup').click(function(event) {
        event.preventDefault();
        var setupIcon = $(this).find('i');
        var block = $(this).parent().parent();
        var id = block.attr('id').replace('block_', '');

        if ($('#block_setup').length > 0) {
            $('block_setup').remove();
        }

        setupIcon.attr('class', 'icon-loading');
        $.ajax({
            type: 'GET',
            url: adminBaseUrl + '/layout/block/ajax-setup',
            data: {
                id: id
            },
            dataType: 'json',
            success: function(json) {
                if (json.status == 1) {
                    $('body').append(json.content);

                    $('#block_setup').modal();
                    $('#block_setup').on('shown', function() {
                        autosizeInputWithAddOn();
                        $('a[rel="tooltip"]').tooltip().each(function() {
                            var $this = $(this), data = $this.data('tooltip');
                            $this.on('focus.tooltip', $.proxy(data.enter, data))
                                    .on('blur.tooltip', $.proxy(data.leave, data));
                        });

                        $('#block_setup form').submit(function(event) {
                            event.preventDefault()
                            var data = $(this).serialize();
                            $.ajax({
                                type: 'POST',
                                url: adminBaseUrl + '/layout/block/ajax-setup',
                                data: data,
                                dataType: 'json',
                                success: function(json) {
                                    if (json.status == 1) {
                                        if (json.msg) {
                                            alert(json.msg);
                                        }
                                        $('#block_setup').modal('hide');
                                    } else {
                                        alert(json.msg);
                                        if (json.console) {
                                            console.log(json.console);
                                        }
                                    }
                                },
                                error: function(msg) {
                                    alert(msg);
                                }
                            });
                        });

                    });
                    $('#block_setup').on('hidden', function() {
                        $('#block_setup').remove();
                    })

                    setupIcon.attr('class', 'icon-wrench');
                } else {
                    alert(json.msg);
                }
            },
            error: function(msg) {
                setupIcon.attr('class', 'icon-wrench');
                alert(msg);
            }
        });
    });
}

/**
 * Exclui um bloco
 */
function initRemoveBlock() {
    $('#layout_body .options .remove').unbind('click');
    $('#layout_body .options .remove').click(function(event) {
        event.preventDefault();
        var setupIcon = $(this).find('i');
        var block = $(this).parent().parent();
        var removeBlocks = $('#remove_blocks').val();
        var isColumn = block.hasClass('column');

        if (confirm('Deseja excluir esse bloco?')) {

            if (isColumn) {
                var span = block.attr('class').replace('column', '').replace('span', '').trim();
                var columns = block.parent();
            }

            // Se for um bloco recem adicionado (ainda nao salvo) remove
            if (block.attr('id')) {
                var id = block.attr('id').replace('block_', '');

                if (removeBlocks)
                    removeBlocks += ',';
                removeBlocks += id;
                $('#remove_blocks').val(removeBlocks);
            }
            block.remove();

            if (isColumn) {
                var other = columns.find('.column').first();
                if (other) {
                    var otherSpan = other.attr('class').replace('column', '').replace('span', '').trim();
                    var columnClass = 'span' + (parseInt(otherSpan) + parseInt(span));
                    other.removeClass('span' + otherSpan).addClass(columnClass);
                    other.find('.layout-column-class').val(columnClass);
                }
            }
        }
    });
}
