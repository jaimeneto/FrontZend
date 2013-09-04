$(document).ready(function(){
    initCodeHighlight();
});

function initCodeHighlight()
{
    if ($('#layout_form_code').length > 0) {

        var syntax = 'php';
        var filetype = $('label[for=code]').text().substring(
            $('label[for=code]').text().lastIndexOf('.')+1
        );
        
        if (filetype == 'css') syntax = 'css';
        if (filetype == 'js') syntax = 'js';
        if (filetype == 'sql') syntax = 'sql';
        if (filetype == 'html' || filetype == 'xhtml' || filetype == 'htm') syntax = 'html';
        if (filetype == 'xml') syntax = 'xml';

        editAreaLoader.init({
			id: "code"	// id of the textarea to transform
			,start_highlight: true	// if start with highlight
			,allow_resize: false
			,allow_toggle: false
			,word_wrap: false
			,language: "pt"
			,syntax: syntax
            ,syntax_selection_allow: "css,html,js,php,xml,sql"
            ,toolbar: "charmap, |, search, go_to_line, |, undo, redo, |, select_font, |, syntax_selection, |, change_smooth_selection, highlight, reset_highlight, word_wrap, |, help"
            ,plugins: "charmap"
			,charmap_default: "arrows"
		});
    }
}