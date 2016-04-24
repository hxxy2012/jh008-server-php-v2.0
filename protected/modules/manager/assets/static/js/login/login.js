define(function(require, exports, module) {

	var dialogUi = require('dialogUi'),
		server = require('server'),
		placeholder = require('placeholder');
		c = placeholder;

	$(function(){
		placeholder.enable($('#username')[0]);
		function tip (text) {
			dialogUi.text(text);
		}
		$('#login').click(function(){
			var user = $.trim($('#username').val()),
				pwd = $.trim($('#password').val()),
				rememberMe = $('#rememberMe');
			if (!user) {
				tip('用户名不能为空');
			} else if (user.length > 16) {
				tip('用户名长度不能超过16位');
			} else if (!pwd) {
				tip ('密码不能为空');
			} else if (pwd < 6 || pwd >16) {
				tip ('密码长度不正确');
			} else {
				server.login({uName: user, uPass: pwd}, function(resp) {
					
				})
			}
		})

	})

})
