define(function(require, exports, module) {
	var Dialog = require('dialog');

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
	
	window.Dialog = Dialog;

	module.exports = DialogUi;
})