
/* run Highlighting plugin */
hljs.initHighlightingOnLoad();
hljs.configure({useBR: true});
//$('code').each(function(i, e) {hljs.highlightBlock(e)});


/* BASE URL */
function url(type){
    var url = null;
    if(type == 'base'){
        url = $('meta[data-url]').attr('data-url');
    }else if(type == 'theme'){
        url = $('meta[data-url]').attr('data-url-theme');
    }
    return url;
}


/** WORK WITH COOKIES **/
function cookieSet(index) {
    $.cookie('submenuMark-' + index, 'opened', {expires: null, path: '/'}); // Set mark to cookie (submenu is shown):
}
function cookieDel(index) {
    $.cookie('submenuMark-' + index, null, {expires: null, path: '/'}); // Delete mark from cookie (submenu is hidden):
}


$(document).ready(function () {

	/* MENU SNIPPETS */
    $('ul#left_ul ul').each(function (i) { // Check each submenu:
        if ($.cookie('submenuMark-' + i)) {  // If index of submenu is marked in cookies:
            $(this).show().prev().removeClass('collapsed').addClass('expanded'); // Show it (add apropriate classes)
        } else {
            $(this).hide().prev().removeClass('expanded').addClass('collapsed'); // Hide it
        }
        $(this).prev().addClass('collapsible').click(function () { // Attach an event listener
            var this_i = $('ul#left_ul ul').index($(this).next()); // The index of the submenu of the clicked link
            if ($(this).next().css('display') == 'none') {
                $(this).next().slideDown(200, function () { // Show submenu:
                    $(this).prev().removeClass('collapsed').addClass('expanded');
                    cookieSet(this_i);
                });
            } else {
                $(this).next().slideUp(200, function () { // Hide submenu:
                    $(this).prev().removeClass('expanded').addClass('collapsed');
                    cookieDel(this_i);
                    $(this).find('ul').each(function () {
                        $(this).hide(0, cookieDel($('ul#left_ul ul').index($(this)))).prev().removeClass('expanded').addClass('collapsed');
                    });
                });
            }
            return false; // Prohibit the browser to follow the link address
        });
    });
    
    
    
    
    
    
});

