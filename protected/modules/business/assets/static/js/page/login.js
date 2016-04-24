define(function(require, exports, module) {
	var $ = require('$');
	var dialogUi = require('dialogUi'),
		server = require('server');

	$(function(){
		function tip (text) {
			dialogUi.text(text);
		}
		$('#login').click(function(){
			var user = $.trim($('#username').val()),
				pwd = $.trim($('#password').val()),
				rememberMe = $('#remember_me:checked');
			if (!user) {
				tip('用户名不能为空');
			} else if (user.length > 16) {
				tip('用户名长度不能超过16位');
			} else if (!pwd) {
				tip ('密码不能为空');
			} else if (pwd.length < 6 || pwd.length >16) {
				tip ('密码长度不正确');
			} else {
				var dialog = dialogUi.wait();
				server.login({uName: user, uPass: pwd, rememberMe: rememberMe.length ? 1 : 0}, function(resp) {
					dialog.hide();
					if (resp.code == 0) {
						location.href = 'acts';
					} else {
						tip(resp.msg || '登录失败');
						$('#username').val('');
						$('#password').val('');
					}
				})
			}
		})

	})

})
