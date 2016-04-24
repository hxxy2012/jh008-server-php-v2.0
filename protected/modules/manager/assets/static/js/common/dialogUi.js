define(function(require, exports, module) {
	var Dialog = require('dialog');
	var $ = require('$');

	var DialogUi = {};

	DialogUi.text = function(text) {
		var dialog = new Dialog({	content: '<div class="text-wrap">'+text+'</div>', 
									height: 50});
		dialog.show();
		return dialog;
	}

	DialogUi.wait = function() {
		var dialog = new Dialog({content: '<span class="load-wrap"></span>', height: 50, width: 50, closeTpl: ''});
		dialog.show();
		return dialog;
	}

	DialogUi.content = function(content) {
		var dialog = new Dialog({content: content, height: 300, width: 300});
		return dialog;
	}

	DialogUi.textarea = function(options) {
		var El = $( '<div class="remark-wrap">' +
						'<p class="remark-title">'+ options.title +'：</p>' + 
						'<textarea class="remark-textarea" id="textarea"></textarea>' + 
						'<p id="remarkAlert" class="remark-alert"></p>' +
						'<div class="mt20">' + 
							'<a id="ok" href="javascript:;" class="fn-left ui-button ui-button-morange">'+ options.okText +'</a>' + 
							'<a id="cancel" href="javascript:;" class="fn-right ui-button ui-button-morange">'+ options.cancelText +'</a>' +
						'</div>' +
					'</div>');
		var dialog = new Dialog({
			content: El[0],
			closeTpl: false,
			height: 180,
			width: 300
		});
		El.find('#ok').click(function(){
			var textarea = $.trim(El.find('#textarea').val()),
				remarkAlert = El.find('#remarkAlert');
			if (!textarea) {
				remarkAlert.show();
				remarkAlert.text('内容不能为空');
			} else {
				remarkAlert.hide();
				remarkAlert.text('');
				options.okCallback && options.okCallback(textarea, dialog);
			}
		});
		El.find('#cancel').click(function(){
			dialog.destroy();
			options.cancelCallback && options.cancelCallback();
		})
		dialog.show();
		return dialog; 
	}

	DialogUi.remark = function(options) {
		return DialogUi.textarea({
			title: '备注',
			okText: '备注',
			cancelText: '取消',
			okCallback: options.callback,
			cancelCallback: ''
		})
	}

	DialogUi.createCity = function(callback) {
		return DialogUi.textarea({
			title: '创建城市',
			okText: '确定',
			cancelText: '取消',
			okCallback: callback,
			cancelCallback: ''
		})
	}

	DialogUi.resetPassDialog = function(callback) {
		var htmlString, El, dialog;
		htmlString = 	'<div class="p15">' + 
								'<div class="lc-ui-form-item">' + 
							        '<label for="" class="lc-ui-label">密码:</label>' +
							        '<div class="lc-ui-rs">' +
										'<input id="uPass" class="lc-ui-input" type="password" placeholder="">' +
							        '</div>' +
							    '</div>' +
							    '<div class="reset-tip" id="uPassError"></div>' + 
								'<div class="lc-ui-form-item">' + 
							        '<label for="" class="lc-ui-label">再次输入密码:</label>' +
							        '<div class="lc-ui-rs">' +
										'<input id="surePass" class="lc-ui-input" type="password" placeholder="">' +
							        '</div>' +
							    '</div>' +	
							    '<div class="reset-tip" id="surePassError"></div>' + 
							    '<div class="lc-ui-form-item">' +
									'<a id="reset" href="javascript:;" class="fn-left ui-button ui-button-morange">重置</a>' + 
									'<a id="cancel" href="javascript:;" class="fn-right ui-button ui-button-morange">取消</a>' +
							    '</div>' +				    
							'</div>';
		El = $('<div></div>');
		El.html(htmlString);
		dialog = new Dialog({
			content: El[0],
			closeTpl: false,
			width: 300,
			height: 160
		});
		dialog.show();
		var uPassError = $('#uPassError'),
			surePassError = $('#surePassError');
		El.find('#reset').click(function(){
			var uPass = $.trim(El.find('#uPass').val()),
				surePass = $.trim(El.find('#surePass').val());
			if (!uPass) {
				uPassError.text('密码不能为空').show();
			} else if (uPass != surePass) {
				uPassError.hide();
				surePassError.text('两次密码不相同').show();
			} else {
				uPassError.hide();
				surePassError.hide();
				callback && callback(uPass, dialog);
			}
		})
		El.find('#cancel').click(function(){
			dialog.destroy();
		})
		return dialog;
	}
	
	window.Dialog = Dialog;

	module.exports = DialogUi;
})