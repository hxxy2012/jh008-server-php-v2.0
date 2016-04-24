define(function(require, exports, module) {
	var $ = require('$');
	var dialogUi = require('dialogUi'),
		server = require('server'),
		common = require('common');

	var myValidate = common.myValidate,
		regexp = myValidate.regexp;
		

	$(function(){
		function tip (text) {
			dialogUi.text(text);
		}

		var flag = false,
		loginEl = $('#login');
		loginEl.click(function(){
			if(!flag) {
				flag = true;
				
				var user = $.trim($('#username').val()),
					pwd = $.trim($('#password').val()),
					rememberMe = $('#remember_me:checked');
				validate = myValidate();
				validate.check(regexp.checkEmpty(user), '用户名不能为空');
				validate.check(regexp.checkSize(user, 6, 16), '用户名长度保持6-16位');
				validate.check(regexp.checkEmpty(pwd), '密码不能为空');
				validate.check(regexp.checkSize(pwd, 6, 16), '密码长度保持6-16位');
				validate.run(function(){
					loginEl.text('登录中...');
					server.login({uName: user, uPass: pwd, rememberMe: rememberMe.length ? 1 : 0}, function(resp) {
						loginEl.text('登录');
						flag = false;
						if (resp.code == 0) {
							var managerRole = resp.body.manager.type;
							// 城市id， 没有城市id时候默认为1（成都） 
							var cityId = resp.body.manager.cityId || 1;
							if (managerRole == 1 || managerRole == 11) {
								location.href = 'actList?cityId=' + cityId;
							} else {
								location.href = 'figureMasters?cityId=' + cityId;
							}
						} else {
							tip(resp.msg || '登录失败');
							$('#username').val('');
							$('#password').val('');
						}
					})
				})
			}
		})

	})

})
