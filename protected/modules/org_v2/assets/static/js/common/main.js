/*!
 * 主框架页面js
 * v2.1     2015-05-19
 * http://www.hashmap.cn
 */
var browser_iframe = null
    ;
window.open_url = function () {
    load.show();
    browser_iframe.attr('src', K.base.randomUrl($(this).data('href')));
};
jQuery(function () {
    _randAtag();
    browser_iframe = $("iframe");
    browser_iframe.load(function () {
        var hash = (window.location.hash + "").substring(1);
        if (hash) {
            $(".menu li").removeClass('active');
            $(".menu a[url='" + hash + "']").parents('li').addClass('active');
        }
        load.hide();
    });

    $(".menu-warp a").click(open_url/*function () {
     load.show();
     //alert(layer.load);
     //layer.load('asdf');
     $(".menu li").removeClass('active');
     $(this).parent().addClass('active');
     browser_iframe.attr('src', window.HM.randomUrl($(this).data('href')));

     }*/);


    //窗口大小发生改变以后
    //$(window).resize(_resize);
    //_resize();

    if (window.location.hash) {
        var hash = (window.location.hash + "").substring(1);
        var a = $(".menu a[url='" + hash + "']");
        if (a.size())
            $(".menu a[url='" + hash + "']").click();
        else {
            load.show();
            browser_iframe.attr('src', hash);
        }

    } else {
        $(".menu li.active a").click()
    }


});


function _randAtag() {
    $(".menu a").each(function () {
        var that = $(this);
        that.data('href', that.attr('href'));
        that.attr('href', '#' + that.data('href'));
        that.attr('url', that.data('href'));
        that = null;
    });
}


/**
 * 窗口大小发生改变
 * @private
 */
function _resize() {
    var wh = $(window).height();
    var bh = $('html,body').height();
    if (wh < bh) {
        $('body').css('min-height', (wh - $('.foot-warp').height()) + 'px');
    }
}