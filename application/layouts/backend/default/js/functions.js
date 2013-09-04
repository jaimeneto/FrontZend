/**
 * Retorna a largura total de um elemento levando em conta bordas, padding 
 * e margin
 * 
 * @param object elementObj Objeto jquery
 */
function getTotalWidth(elementObj) {
    return elementObj.width()
        + parseInt(elementObj.css("padding-left"), 10)
        + parseInt(elementObj.css("padding-right"), 10)
        + parseInt(elementObj.css("margin-left"), 10)
        + parseInt(elementObj.css("margin-right"), 10)
        + parseInt(elementObj.css("borderLeftWidth"), 10)
        + parseInt(elementObj.css("borderRightWidth"), 10);
}

/**
 * Faz um elemento piscar
 * 
 * @param integer times Quantidade de vezes a piscar
 */
jQuery.fn.pisca = function(times) {
    if (!times) times = 3;
    for(var i=0;i<times;i++) {
        jQuery(this).fadeOut(100).fadeIn(100);
    };
}

/**
 * Abre uma janela popup
 *
 * @param string url    Link da página
 * @param string width  Largura da janela
 * @param string height Altura da janela
 */
function popup(url, width, height) {
    window.open(url,'page','toolbar=no,location=no,status=no,menubar=no'
        + ',scrollbars=no,resizable=no,width=' + width + ',height=' + height);
}


function generateSerial(len) {
    var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
    var string_length = 10;
    var randomstring = '';

    for (var x=0;x<string_length;x++) {

        var letterOrNumber = Math.floor(Math.random() * 2);
        if (letterOrNumber == 0) {
            var newNum = Math.floor(Math.random() * 9);
            randomstring += newNum;
        } else {
            var rnum = Math.floor(Math.random() * chars.length);
            randomstring += chars.substring(rnum,rnum+1);
        }

    }
    return randomstring;
}