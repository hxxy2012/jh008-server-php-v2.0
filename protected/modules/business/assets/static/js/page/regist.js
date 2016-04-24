define(function(require, exports, module) {

	var dialogUi = require('dialogUi'),
		server = require('server'),
		$ = require('$');

	$(function(){

		function tip (text) {
			dialogUi.text(text);
		}
		$('#regist').click(function(){
			var user = $.trim($('#username').val()),
				pwd = $.trim($('#password').val()),
				repwd = $.trim($('#repassword').val());

			if (!user) {
				tip('用户名不能为空');
			} else if (user.length > 16) {
				tip('用户名长度不能超过16位');
			} else if (!pwd) {
				tip ('密码不能为空');
			} else if (pwd.length < 6 || pwd.length >16) {
				tip ('密码长度不正确');
			} else if(!repwd){
				tip ('重复密码不能为空');
			} else if(pwd != repwd){
				tip ('两次输入密码不相同');
			} else {
				server.regist({uName: user, uPass: pwd}, function(resp) {
					if (resp.code == 0) {
						location.href = "acts";
					} else {
						tip(resp.msg || '注册失败');
						$('#username').val('');
						$('#password').val('');
						$('#repassword').val('');
					}
				})
			}
		})

	})

})