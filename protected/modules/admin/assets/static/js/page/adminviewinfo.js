define(function(require, exports, module) {
	var $ = require('$'),
		server = require('server'),
		Uploader = require('upload'),
		main = require('main'),
		data,  // 商家信息数据
		headImgId; // 上传之后的图片id

	var tip = main.tip,
		wait = main.wait;

	function renderShowUi() {
		var htm = template.render('sellerShowTemplate', data);
		$('#adminContainer').html(htm);
	}

	function renderEditUi() {
		var htm = template.render('sellerEditTemplate', data);
		$('#adminContainer').html(htm);
		var uploading = false;
		var uploader = new Uploader({
		    trigger: '#changeLogo',
		    name: 'img',
		    action: '/admin/adminInfo/imgUp',
		    accept: 'image/*',
		    data: {'isReturnUrl': 1},
		    multiple: true,
		    change: function(files) {
		    	if (!uploading) {
		    		$('#changeLogo').text('上传中...');
					uploading = true;
					uploader.submit();
		    	}
		    },
		    error: function(file) {
		        tip('上传logo失败');
		        $('#changeLogo').text('更换logo');
		        uploading = false;
		    },
		    success: function(response) {
		    	var response = $.parseJSON(response);
		        data.logo_img_url = response.body.img_url;
		        headImgId = response.body.img_id;
		        $('#changeLogo').text('更换logo');
		        uploading = false;
		        console.log(data);
		        $('#logo').attr({'src': response.body.img_url});
		    }
		});
	}

	function getAdminInfo() {
		var dialog = wait();
		server.getMyInfo(function(resp) {
			dialog.hide();
			if (resp.code == 0) {
				data = resp.body.admin;
				renderShowUi();
			} else {
				tip(resp.msg || '请求有误.');
			}
		})		
	}

	$('#adminContainer').on('click', '#editSellerBtn', function(){
		renderEditUi();
	})

	$('#adminContainer').on('click', '#return', function(){
		renderShowUi();
	})

	$('#adminContainer').on('click', '#savePwdrBtn', function(){
		var oldPass = $.trim($('#oldPwd').val()),
			newPass = $.trim($('#Pwd').val()),
			reNewPass = $.trim($('#surePwd').val());
		if (!oldPass) {
			tip('旧密码不能为空');
			$('#oldPwd').focus();
		} else if (!newPass) {
			tip('新密码不能为空');
			$('#Pwd').focus();
		} else if (!reNewPass) {
			tip('确认密码不能为空');
			$('#surePwd').focus();
		} else if (oldPass.length<6 || oldPass.length>16) {
			tip('旧密码长度不正确');
			$('#oldPwd').focus();
		} else if (newPass.length<6 || newPass.length>16) {
			tip('新密码长度不正确');
			$('#Pwd').focus();
		} else if (reNewPass.length<6 || reNewPass.length>16) {
			tip('确认密码长度不正确');
			$('#surePwd').focus();
		} else if (newPass != reNewPass) {
			tip('两次密码不相同');
			$('#surePwd').focus();
		} else {
			var dialog = wait();
			server.updateInfo({newPass: newPass, oldPass: oldPass}, function(resp){
				/*var resp = {
					code: 0
				};*/
				dialog.hide();
				if (resp.code == 0) {
					tip('修改成功');
					$('#oldPwd').val('');
					$('#Pwd').val('');
					$('#surePwd').val('');
					renderShowUi();
				} else {
					tip(resp.msg || '保存有误');
				}
			})
		}
	});	

	$('#adminContainer').on('click', '#saveSellerBtn', function(){
		var	nickName = $.trim($('#nickName').val());
	
		if (!nickName) {
			tip('昵称不能为空');
		}else {
			var parms = {
				nickName: nickName
			};
			if (headImgId) parms.headImgId = headImgId;
			var dialog = wait();
			server.updateInfo(parms, function(resp){
				dialog.hide();
				if (resp.code == 0) {
					tip('保存成功');
					renderShowUi();
				} else {
					tip(resp.msg || '保存有误');
				}
			})
		}
	});

	page = {
		init: function() {
			getAdminInfo();
		}
	}
	page.init();

})