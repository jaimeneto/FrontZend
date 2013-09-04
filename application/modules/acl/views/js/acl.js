$(document).ready(function(){
    if ($('#acl_form_user').lengh) {
        $('#avatar_image').css('width', '80px')
                          .css('height', '80px')
                          .css('position', 'absolute')
                          .css('right', 0)
                          .css('margin-top', '5px');
        $('#avatar').parents('.controls').css('position','relative');
        $('#avatar').after($('#avatar_image'));
    }
});