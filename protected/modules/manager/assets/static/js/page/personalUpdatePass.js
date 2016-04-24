define(function(require, exports, module){
	var $ = require('$');
	var dialogUi = require('dialogUi'),
		main = require('main'),
		server = require('server'),
		common = require('common');

	var myValidate = common.myValidate,
		regexp = myValidate.regexp,
		tip = main.tip;

	var updateMethod;
	if (roleType == 1 || roleType == 11 || roleType == 12) {
		updateMethod = server.updateMSelf;
	} else if (roleType == 101 || roleType == 102) {
		updateMethod = server.updateCMSelf;
	}

	$('#update').click(function(){
		var pwd = $.trim($('#uPass').val()),
			repwd = $.trim($('#surePass').val());
		validate = myValidate();
		validate.check(regexp.checkEmpty(pwd), '密码不能为空');
		validate.check(regexp.checkSize(pwd, 6, 16), '密码长度保持6-16位');
		validate.check(regexp.checkEmpty(repwd), '新密码不能为空');
		validate.check(regexp.checkSize(pwd, 6, 16), '新密码长度保持6-16位');
		validate.run(function(){
			// 修改密码
			updateMethod({
				oldPass: pwd,
				newPass: repwd
			}, function(resp){
				if (resp.code == 0) {
					tip('修改成功');
					$('#uPass').val('');
					$('#surePass').val('');
				} else {
					tip('修改密码出错');
				}
			})
		})
	})
})