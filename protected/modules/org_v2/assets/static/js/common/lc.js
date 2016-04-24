/*!
 @Name：零创后台ui组件，配合lc_ui.css使用
 @Author：pheart  
 */
$(function(){
if (!window.K) return false;

// 元素外点击执行 
var outsiteClick = K.util.outsiteClick;

/**
 *  register keyup events to global.
 */
/*var registerKeyup = (function(){
    var list = []; 
    $('body').keyup(function(e){
        var target = e.target;
        $.each(list, function(i, item){
            var context = null || item.context;
            if (item.handle) {
                item.handle.call(context, $(target));
            }
        })
    })

    function _regist(handleCallback) {
        list.push({handle: handleCallback});
    }

    return {
        regist: _regist
    }
})()

function limitTextRegistKeyup() {
    var callback = function(el) {
        var target = el, parentEl, limit, textShowEl;
        if (target[0].nodeName == 'TEXTAREA' && target.hasClass('limitText-ta')) {
            parentEl = target.parents('.limitText');
            if (!parentEl.length) return;
            textShowEl = target.siblings('.text-tip');
            limit = Number($.trim(textShowEl.text()).split('/')[1]);
            var value = target.val();
            var textLength = value.length;
            if(textLength > limit) {
                target.val(value.slice(0, limit));
                textShowEl.text(limit + '/' + limit);
            } else {
                textShowEl.text(textLength + '/' + limit);
            }
        }

        if (target.parents('')) {

        }
    }
    registerKeyup.regist(callback);
}*/


/**
 * placeholder 属性兼容 
 */
$.fn.lc_placeholder = function() {
    var target = $(this);
    var placeholderText = target.attr('placeholder');
    if (!('placeholder' in document.createElement('input'))) {
        target.val(placeholderText);
        target.focus(function(){
            target.val('');
        }).blur(function(){
            var value = $.trim(target.val());
            if (!value) {
                target.val(placeholderText);
            }
        })
    }
}

/**
 * limittext 限制输入次数的textarea
 */
$.fn.lc_limitText = function() {
    var target = $(this),
        textarea, textTip;
    textarea = target.find('textarea');
    if (!textarea.length) return;
    textarea.keyup(function(){
        textShowEl = target.find('.text-tip');
        limit = Number($.trim(textShowEl.text()).split('/')[1]);
        var value = textarea.val();
        var textLength = value.length;
        if(textLength > limit) {
            textarea.val(value.slice(0, limit));
            textShowEl.text(limit + '/' + limit);
        } else {
            textShowEl.text(textLength + '/' + limit);
        }        
    })
}

/**
 * limittext 下拉框
 */
$.fn.lc_uiSelect = function() {
    var target = $(this),
        dropMenu = target.find('.dropdown-menu'),
        showText = target.find('.ui-select-text');
    target.on('click', '.dropdown-menu li a', function(e){
        dropMenu.hide();
        showText.text($(e.target).text());
        e.stopPropagation();
    }).on('click', function(){
        dropMenu.toggle();
    })
    outsiteClick(target, function(srcElement){
        if (!$(srcElement).parents('.ui-select').length && srcElement != target[0]) {
            dropMenu.hide();
        }
    });
}


/**
 * 单选按钮
 */
$.fn.lc_radioSel = function() {
    var target = $(this);
    if (!target.length) return;
    target.on('click', 'label', function(e) {console.log(e);
        var curTarget = $(e.currentTarget),
            curParent;
        target.find('.radio-wrap').removeClass('sel');
        curParent = curTarget.parent();
        curParent.addClass('sel');
        curParent.find('input[type="radio"]').attr('checked', true);
        e.stopPropagation();
    })
}

/**
 * 复选按钮
 */
$.fn.lc_checkboxSel = function() {
    var target = $(this), checkboxWrap;
    if (!target.length) return;
    checkboxWrap = target.find('.checkbox-wrap')
    if (!checkboxWrap.length) return;
    $.each(checkboxWrap, function(i){
        if (checkboxWrap.eq(i).attr('d-select') == 'on') {
            checkboxWrap.eq(i).addClass('sel');
            checkboxWrap.eq(i).find('input[type=checkbox]').attr('checked', true);
        }
    })
    target.on('click', 'label', function(e) {
        var curTarget = $(e.currentTarget),
            curParent;
        curParent = curTarget.parent();
        if (curParent.attr('d-select') != 'on') {
            if (curParent.hasClass('sel')) {
                curParent.removeClass('sel');
                curParent.find('input[type="checkbox"]').attr('checked', false);  
            } else {
                curParent.addClass('sel');
                curParent.find('input[type="checkbox"]').attr('checked', true);            
            }
        }
        e.stopPropagation();
    })
}


function initPlaceholder() {
    var els = $('input[type="text"], input[type="password"], textarea');
    if (els.length) {
        $.each(els, function(i){
            els.eq(i).lc_placeholder();
        })
    }
}
 
function initLimitText() {
    var els = $('body').find('.limitText');
    if (els.length) {
        $.each(els, function(i){
            els.eq(i).lc_limitText();
        })
    }
}

function initUiSelect() {
    var els = $('.ui-select');
    if (els.length) {
        $.each(els, function(i){
            els.eq(i).lc_uiSelect();
        })
    }
}

function initRadioSel() {
    var els = $('.radioSels');
    if (els.length) {
        $.each(els, function(i){
            els.eq(i).lc_radioSel();
        })
    }
}

function initCheckboxSel() {
    var els = $('.checkboxSels');
    if (els.length) {
        $.each(els, function(i){
            els.eq(i).lc_checkboxSel();
        })
    }
}

initPlaceholder();
initLimitText();
initUiSelect();
initRadioSel();
initCheckboxSel();

})