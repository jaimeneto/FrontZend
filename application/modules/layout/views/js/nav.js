$(document).ready(function(){
    initNavManagement();
});

function initNavManagement() {

    $('ul.nav-container').sortable({
        connectWith: 'ul.nav-container',
        placeholder: 'ui-state-highlight',
        axis: 'y',
        forcePlaceholderSize: true,
        receive: function(event, ui) {
            if (ui.item.hasClass('new-nav-item')) {
                Layout_Nav_AjaxAdd();
            }
        },
        update: function(event, ui) {
            if (!ui.sender) {
                var id = ui.item.attr('id').replace('layout_nav_', '');

                var parentLi = $(ui.item).parent().parent();
                var parent = parentLi.prop('tagName') == 'LI'
                    ? parentLi.attr('id').replace('layout_nav_', '')
                    : null;

                var order = $(ui.item).index() + 1;
                
                Layout_Nav_AjaxMove(id, parent, order);
            }
        }
    });

    $('.new-nav-item').draggable({
        connectToSortable: 'ul.nav-container',
        helper: 'clone',
        axis: 'y'
    });

    $('ul.nav-container, .new-nav-item').disableSelection();

    Layout_Nav_AjaxEdit();
    Layout_Nav_AjaxRemove();
    Layout_Nav_AjaxToggle();
    Layout_Nav_AjaxSave();
}

function Layout_Nav_AjaxAdd()
{
    var parentLi = $('ul.nav-container .new-nav-item').parent().parent();
    var id_parent = parentLi.prop('tagName') == 'LI'
        ? parentLi.attr('id').replace('layout_nav_', '')
        : null;
    var order = $('ul.nav-container .new-nav-item').index() + 1;

    $.ajax({
        type: 'POST',
        url: adminBaseUrl + '/layout/nav/ajax-add',
        data: {
            id_parent: id_parent,
            order: order
        },
        dataType: 'json',
        success: function(json){
            if (json.status == 1) {
                $('body').append(json.content);

                $('#form_nav_item').modal();
                $('#form_nav_item').on('hidden', function() {
                    $('ul.nav-container .new-nav-item').fadeOut('normal',
                        function(){$(this).remove();});
                    $('#form_nav_item').remove();
                })

                initNavManagement();
            } else {
                alert(json.msg);
                $('ul.nav-container .new-nav-item').fadeOut('normal',
                        function(){$(this).remove();});
            }
        },
        error: function(msg) {
            alert(msg);
            $('ul.nav-container .new-nav-item').fadeOut('normal',
                    function(){$(this).remove();});
        }
    });
}

function Layout_Nav_AjaxEdit()
{
    $('ul.nav-container a.edit').unbind('click');
    $('ul.nav-container a.edit').click(function(){
        var id_layout_nav = $(this).attr('href').replace('#', '');

        $.ajax({
            type: 'POST',
            url: adminBaseUrl + '/layout/nav/ajax-edit',
            data: {
                id: id_layout_nav
            },
            dataType: 'json',
            success: function(json){
                if (json.console) {
                    console.log(json.console);
                }
                if (json.status == 1) {
                    $('body').append(json.content);

                    $('#form_nav_item').modal();
                    $('#form_nav_item').on('hidden', function() {
                        $('#form_nav_item').remove();
                    })

                    Layout_Nav_AjaxSave();
                } else {
                    alert(json.msg);
                }
            },
            error: function(msg) {
                alert(msg);
            }
        });

        return false;
    });
}

function Layout_Nav_AjaxRemove()
{
    $('ul.nav-container a.remove').unbind('click');
    $('ul.nav-container a.remove').click(function(){

        if (confirm('Deseja excluir este item de menu e todos os seus subitens?')){
            var id_layout_nav = $(this).attr('href').replace('#', '');

            $.ajax({
                type: 'POST',
                url: adminBaseUrl + '/layout/nav/ajax-remove',
                data: {
                    id: id_layout_nav
                },
                dataType: 'json',
                success: function(json){
                    if (json.console) {
                        console.log(json.console);
                    }
                    if (json.status == 1) {
                        $('#layout_nav_' + id_layout_nav).fadeOut('normal',
                            function(){$(this).remove();});
                    } else {
                        alert(json.msg);
                    }
                },
                error: function(msg) {
                    alert(msg);
                }
            });
        }

        return false;
    });
}

function Layout_Nav_AjaxToggle()
{
    $('ul.nav-container a.toggle').unbind('click');
    $('ul.nav-container a.toggle').click(function(){
        var id_layout_nav = $(this).attr('href').replace('#', '');

        $('#layout_nav_' + id_layout_nav + ' .toggle i').addClass('icon-loading');

        $.ajax({
            type: 'POST',
            url: adminBaseUrl + '/layout/nav/ajax-toggle',
            data: {
                id: id_layout_nav
            },
            dataType: 'json',
            success: function(json){
                if (json.console) {
                    console.log(json.console);
                }
                if (json.status == 1) {
                    if (json.data.visible == 1) {
                        $('#layout_nav_' + id_layout_nav + ' .toggle i').addClass('icon-eye-open');
                        $('#layout_nav_' + id_layout_nav + ' .toggle i').removeClass('icon-eye-close');
                        $('#layout_nav_' + id_layout_nav).removeClass('not-visible');
                    } else {
                        $('#layout_nav_' + id_layout_nav + ' .toggle i').addClass('icon-eye-close');
                        $('#layout_nav_' + id_layout_nav + ' .toggle i').removeClass('icon-eye-open');
                        $('#layout_nav_' + id_layout_nav).addClass('not-visible');
                    }
                    $('#layout_nav_' + id_layout_nav + ' .toggle i').removeClass('icon-loading');
                } else {
                    alert(json.msg);
                }
            },
            error: function(msg) {
                alert(msg);
            }
        });

        return false;
    });
}

function Layout_Nav_AjaxSave()
{
    $('#form_nav_item #save').click(function(){
        var values = $('#form_nav_item form').serialize();

        $.ajax({
            type: 'POST',
            url: adminBaseUrl + '/layout/nav/ajax-save',
            data: values,
            dataType: 'json',
            success: function(json){
                if (json.console) {
                    console.log(json.console);
                }
                if (json.status == 1) {
                    if ($('ul.nav-container .new-nav-item').length > 0) {
                        $('ul.nav-container .new-nav-item').before(json.content);
                        $('ul.nav-container .new-nav-item').remove();
                    } else {
                        $('#layout_nav_' + json.data.id_layout_nav + ' .nav-label').html(json.data.label);

                        if (json.data.visible == 0) {
                            $('#layout_nav_' + json.data.id_layout_nav).addClass('not-visible');
                            $('#layout_nav_' + json.data.id_layout_nav + ' .icon-eye-open').addClass('icon-eye-close').removeClass('icon-eye-open');
                        } else {
                            $('#layout_nav_' + json.data.id_layout_nav).removeClass('not-visible');
                            $('#layout_nav_' + json.data.id_layout_nav + ' .icon-eye-close').addClass('icon-eye-open').removeClass('icon-eye-close');
                        }
                    }
                    $('#form_nav_item').modal('hide');
                    
                    initNavManagement();
                } else {
                    alert(json.msg);
                }
            },
            error: function(msg) {
                alert(msg);
                $('ul.nav-container .form-nav-item').fadeOut('normal',
                        function(){$(this).remove();});
            }
        });

    });
}

function Layout_Nav_AjaxMove(id, parent, order)
{
    $.ajax({
        type: 'POST',
        url: adminBaseUrl + '/layout/nav/ajax-move',
        data: {
            id_layout_nav: id,
            id_parent: parent,
            order: order
        },
        dataType: 'json',
        success: function(json){
            if (json.status == 1) {
                console.log(json.msg);
            } else {
                if (json.console) {
                    console.log(json.console);
                }
                alert(json.msg);
                window.open(location.href, '_self');
            }
        },
        error: function(msg) {
            alert(msg);
        }
    });
}