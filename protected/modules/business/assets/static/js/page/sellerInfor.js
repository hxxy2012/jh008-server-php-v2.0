define(function(require, exports, module) {
	var $ = require('$'),
		server = require('server'),
		Uploader = require('upload'),
		main = require('main'),
		data,  // 商家信息数据
		logoImgId; // 上传之后的图片id

	var tip = main.tip,
		wait = main.wait;

	function renderShowUi() {
		var htm = template.render('sellerShowTemplate', data);
		$('#sellerContainer').html(htm);
	}

	function renderEditUi() {
		var htm = template.render('sellerEditTemplate', data);
		$('#sellerContainer').html(htm);
		var uploading = false;
		var uploader = new Uploader({
		    trigger: '#changeLogo',
		    name: 'img',
		    action: '/business/businessInfo/imgUp',
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
		        logoImgId = response.body.img_id;
		        $('#changeLogo').text('更换logo');
		        uploading = false;
		        console.log(data);
		        $('#logo').attr({'src': response.body.img_url});
		    }
		});
	}

	function getSellerInfo() {
		var dialog = wait();
		server.getSellerInfo(function(resp) {
			/*var resp = {
				code: 0,
				msg: '状态信息描述',
				body: {
					business: {
						id:	886,
						u_name:	 1, // int	是否已完成注册：0未完成，1已完成
						name: '家乐福',	// String	商家名称
						address: '甘肃省民乐县', //	String	地址
						contact_phone: '12508238092', // String	联系电话
						contact_email: '412365@l63.com', // String	联系邮箱
						contact_descri: '4411428', // String  其他联系方式
						logo_img_url: 'http://localhost/ling/static/images/a.jpg'  // String	logo图片url						
					}
				}

			};*/
			dialog.hide();
			if (resp.code == 0) {
				data = resp.body.business;
				renderShowUi();
			} else {
				tip(resp.msg || '请求有误.');
			}
		})		
	}

	$('#sellerContainer').on('click', '#editSellerBtn', function(){
		renderEditUi();
	})

	/*$('#sellerContainer').on('click', '#saveSellerBtn', function(){
		var htm = template.render('sellerShowTemplate', data);
		$('#sellerContainer').html(htm);
	})*/

	$('#sellerContainer').on('click', '#return', function(){
		renderShowUi();
	})

	$('#sellerContainer').on('click', '#savePwdrBtn', function(){
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
			server.updateSellerInfo({newPass: newPass, oldPass: oldPass}, function(resp){
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

	$('#sellerContainer').on('click', '#saveSellerBtn', function(){
		var	name = $.trim($('#name').val()),
			address = $.trim($('#address').val()),
			contactPhone = $.trim($('#contactPhone').val()),
			contactEmail = $.trim($('#contactEmail').val()),
			contactDescri = $.trim($('#contactDescri').val());
	
		if (!name) {
			tip('商家名称不能为空');
		} else if (!data.logo_img_url && !logoImgId) {
			tip('必须上传logo');
		} else {
			var parms = {
				name: name,
				address: address,
				contactPhone: contactPhone,
				contactEmail: contactEmail,
				contactDescri: contactDescri
			};
			if (logoImgId) parms.logoImgId = logoImgId;
			var dialog = wait();
			server.updateSellerInfo(parms, function(resp){
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
			getSellerInfo();
		}
	}
	page.init();

})