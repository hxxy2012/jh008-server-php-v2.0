define(function(require, exports, module) {
	var $ = require('$');
	var server = require('server'),
		dialogUi = require('dialogUi');

	$(function(){
		// leftSide
		$('.ui-box-head').on('click', function(e){
			var target = $(e.target),
				toggleTarget = target.siblings('.ui-box-container'),
				triggleTarget = target.find('i');
			if (triggleTarget.hasClass('rotate')) {
				triggleTarget.removeClass('rotate slide-up');
				toggleTarget.slideUp(380);			
			} else {
				triggleTarget.addClass('rotate slide-up');
				toggleTarget.slideDown(380);
			}
		});

		// ui-select
		$('.ui-select-trigger').on('click', function(e) {
			var target = $(e.target).closest('.ui-select-trigger'),
				toggleTarget = target.siblings('.ui-select-content');
			toggleTarget.toggle();
		})

		$('.ui-select-content').on('click', '.ui-select-item', function(e) {
			var target = $(e.currentTarget).find('a'),
				parentTarget = target.closest('.ui-select');
			parentTarget.find('.ui-select-trigger span').text(target.text()).attr({'value': target.attr('data-val')});
			parentTarget.find('.ui-select-content').hide();
		})

		$(document).on('click', function(e) {
			if (!$(e.target).parents('.ui-select').length) {
				$('.ui-select-content').hide();
			}
		})

		// logout

		$('#logout').click(function(){
			server.logout(function(resp){
				if (resp.code == 0) {
					location.href = 'login'
				} else {
					dialogUi.tip(resp.msg || '退出登录失败');
				}
			})
		})

	})
	
	exports.tip = function(text) {
		var dialog = dialogUi.text(text);
		setTimeout(function(){
			if (dialog.attrs.visible.value === true) dialog.hide();
		}, 5000);
		return dialog;
	}

	exports.wait = function() {
		return dialogUi.wait();
	}	



})