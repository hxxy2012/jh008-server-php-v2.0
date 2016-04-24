define(function(require, exports, module) {
	var $ = require('$');  
	var dialogUi = require('dialogUi'),
		server = require('server'),
		common = require('common'),
		main = require('main');

	var myValidate = common.myValidate,
		regexp = myValidate.regexp;
		
		require( 'fullpage' );

	$(function(){
		//alert(fullpage);
		$('#hm_fullpage').fullpage({
			anchors: ['page1', 'page2' ],
			menu: '#menu'
			,loopBottom:true
			,loopTop:true
			,'navigation': true
		});

		$(".loading-mask").fadeOut(350,function(){
			$(this).remove();
		});

		$(".anchor-login").click(function(){
			$(".container").fadeIn(450);
		});

		$(".login .closer").click(function(){
			$(".container").fadeOut(450);
		});

		$(".qr-code").click(_showQRCode);//.mouseover(_showQRCode);
		$(".qr-warp").click(_hideQRCode);
		function _showQRCode(){
			$(".qr-warp").fadeIn(500);
		}
		function _hideQRCode(){
			$(".qr-warp").fadeOut(500);
		}

 
		function tip (text) {
			clearTimeout(tip.timer);
			tip.timer = setTimeout(function(){
			 $("#error-tip").fadeOut(200,function(){
			 	$(this).html('');
			 });
			},3000);
			 $("#error-tip").fadeOut(0).html(text).fadeIn(1500);
		}
		tip.timer = 0;
		var flag = false,
		loginEl = $('#login');
		
		loginEl.click(function(){
			if(!flag) {
				flag = true;
				
				var user = $.trim($('#username').val()),
					pwd = $.trim($('#password').val()),
					rememberMe = $('#remember_me:checked');
				validate = myValidate(tip);
  				//main.tip = tip;
				validate.check(regexp.checkEmpty(user), '用户名不能为空');
				validate.check(regexp.checkSize(user, 6, 16), '用户名长度保持6-16位');
				validate.check(regexp.checkEmpty(pwd), '密码不能为空');
				validate.check(regexp.checkSize(pwd, 6, 16), '密码长度保持6-16位');
				validate.run(function(){
					loginEl.text('登录中...');
                    //提交数据
					server.login({loginType: 1, loginName: user, userPass: pwd}, function(resp) 
                    {
						
						flag = false;
						if (resp.code == 0 && resp.body.uid!='') 
                        {
					    	location.href = 'index';
						} 
                        else 
                        {
							tip(resp.msg);  
						}
                        loginEl.text('登录');
					})
				})
				flag = false;
			}
		})

	})

})
