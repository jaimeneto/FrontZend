$(function(){
    $('[rel="tooltip"]').tooltip({'placement': 'left'})
    .each(function(){
        var $this = $(this), data = $this.data('tooltip');
        $this.on('focus.tooltip', $.proxy(data.enter, data))
             .on('blur.tooltip', $.proxy(data.leave, data));
    });

    $("table.treetable").treetable({
        expandable: true
    });

    initRichText();
    initTreeview();
})

function initRichText()
{
    $('textarea#text, textarea#excerpt, textarea#description').each(function(){
        $(this).parents('.controls').addClass('richtext');
    })
    $('textarea#text').tinymce({
        // Location of TinyMCE script
        script_url : baseUrl + '/lib/tiny_mce/tiny_mce.js',

        // General options
        theme : "advanced",
        plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",

        // Theme options
        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,bullist,numlist,|,outdent,indent,blockquote,|,link,unlink,image,|,insertdate,inserttime,|,cleanup,code",
        theme_advanced_buttons2 : "cut,copy,paste,pastetext,|,undo,redo,|,tablecontrols,|,hr,removeformat,visualaid,|,template,|,print,preview,|,fullscreen",
        theme_advanced_buttons3 : "",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : false,
        language: "pt",

        // Example content CSS (should be your site CSS)
        content_css : baseUrl + "/lib/tiny_mce/css/frontzend.css?" + new Date().getTime(),

        relative_urls : false
    });

    $('textarea#excerpt, textarea#description').tinymce({
        script_url : baseUrl + '/lib/tiny_mce/tiny_mce.js',
        theme : "advanced",
        mode : "textareas",
        plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",
        language: "pt",
        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,sub,sup,|,charmap,|,undo,redo,|,code",
        theme_advanced_buttons2 : "",
        theme_advanced_buttons3 : "",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        content_css : baseUrl + "/common/css/default.css?" + new Date().getTime(),
        relative_urls : false
    });
}

function initTreeview()
{
    $('.treeview').treeview({
        animated: 'fast',
        collapsed: true,
        unique:true
    });

    // Workaround para abrir o caminho até a pasta ativa
    $('ul.treeview li.active, ul.treeview li.active > ul').each(function(){
        $(this).show();
    });
}