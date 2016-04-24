(function(){

var dialog = K.ns('dialogUi');
var base = K.base;
/**-----------------------------------------------------------------------------------------
 * laod层操作
 * @type {{index: number, show: Function, hide: Function}}
 */
dialog.load = function (icon, options) {
    return dialog.load.show(icon, options);
};
dialog.load.index = 0; //layer box index
/**
 * 显示 加载界面
 */
dialog.load.show = function (icon, options) {
    options = base.extend({shade: [0.1, '#000000']}, options);
    dialog.load.hide();
    dialog.load.index = layer.load(icon, options);
    return dialog.load.index;
};
/**
 * 隐藏加载界面
 */
dialog.load.hide = function () {
    return layer.close(dialog.load.index);
};

//export load for window , trend product
window.load = dialog.load;


/**
 * 简洁的提示框
 * @param content   提示内容
 * @param options   配置
 * @param end       结束的回调方法
 * @returns {Window.layer.index|*|index|Number|jQuery.index|number}
 */
dialog.msg = function (content, options, end) {
    return dialog.msg.show(content, options, end);
};
/**
 * 弹出框编号
 * @type {number}
 */
dialog.msg.index = 0;
/**
 * 显示提示框方法
 * @param content   提示内容
 * @param options   配置
 * @param end       结束的回调方法
 * @returns {Window.layer.index|*|index|Number|jQuery.index|number}
 */
dialog.msg.show = function (content, options, end) {
    dialog.msg.index = layer.msg(content, options, end);
    return dialog.msg.index;
}
/**
 * 隐藏提示框
 * @returns {*}
 */
dialog.msg.hide = function () {
    return layer.close(dialog.msg.index);
}
//export msg for window , trend product
window.msg = dialog.msg;
/**--------------------------------------------------------------------------------------------------------------*/


/**
 * text 
 */
dialog.alert = function(text, callback) {
    return layer.open({
        time: 0,
        content: '<p class="alert-con">'+ text +'</p>',
        closeBtn: false,
        title: false,
        area: ['360px', '200px'],
        skin: 'lc-layui-layer',
        btn: ['确定'],
        yes: function(index, layero) {
            callback && callback();
            layer.close(index);
        }
    })
}

/**
 * @parms {Object}
        - text
        - okText
        - cancelText
        - okCallback
        - cancelCallback
 */
dialog.confirm = function(options) {
    return layer.open({
        time: 0,
        content: '<p class="alert-con">'+ options.text || '' +'</p>',
        closeBtn: false,
        title: false,
        area: ['360px', '200px'],
        skin: 'lc-layui-layer',
        btn: [options.okText || '确定', options.cancelText || '取消'],
        yes: function(index, layero) {
            options.okCallback && options.okCallback();
            layer.close(index);
        },
        cancel: function(index) {
            options.cancelCallback && options.cancelCallback();

        }
    }) 
}

/**
 * wait 
 */
dialog.wait = function() {
    var dia =  layer.open({
        type: 3
    });

    return {
        close: function() {
            layer.close(dia);
        }
    }
}

/**
 * tip
 */
dialog.tip = function(ele, content, options) {
    var settings = $.extend({
        type: 4,
        tips: 3,
        closeBtn: false,
        content: [content, ele],
        time: 2000 
    }, options || {});

    var dia = layer.open(settings);
    return {
        close: function() {
            layer.close(dia);
        }
    }
}

/**
 *
 */
dialog.open = function(options) {
    var dia = layer.open(options);
    return {
        close: function() {
            layer.close(dia);
        }
    }
}

/**
 *
 */
dialog.msg = function(text) {
    var dia = layer.msg(text || '', {time:false, icon:16, offset: '200px'});
    return {
        close: function() {
            layer.close(dia);
        }
    }
}


})()