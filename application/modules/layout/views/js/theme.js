$(document).ready(function(){

    Layout_Theme_AjaxActivate();
    Layout_Theme_AjaxTest();

});

function Layout_Theme_AjaxActivate()
{
    $('#layout_themes button[name=activate]').click(function(){
        var id_theme = $(this).parents('.thumbnail').attr('id').replace('theme_', '');
        var env = $(this).parents('.tab-pane').attr('id');

        $.ajax({
            type: 'POST',
            url: adminBaseUrl + '/layout/theme/ajax-activate',
            data: {
                id: id_theme
            },
            dataType: 'json',
            success: function(json){
                if (json.status == 1) {
                    $('#layout_themes #' + env + ' .thumbnail.active').removeClass('active');
                    $('#theme_' + id_theme).addClass('active');
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


        return false;
    })
}

function Layout_Theme_AjaxTest()
{
    $('#layout_themes button[name=test]').click(function(){
        var id_theme = $(this).parents('.thumbnail').attr('id').replace('theme_', '');
        var button = $(this);

        $.ajax({
            type: 'POST',
            url: adminBaseUrl + '/layout/theme/ajax-test',
            data: {
                id: id_theme
            },
            dataType: 'json',
            success: function(json){
                if (json.status == 1) {
                    if (json.active == 1) {
                        button.addClass('active');
                    } else {
                        button.removeClass('active');
                    }
                } else {
                    alert(json.msg);
                }
            },
            error: function(msg) {
                alert(msg);
            }
        });


        return false;
    })
}