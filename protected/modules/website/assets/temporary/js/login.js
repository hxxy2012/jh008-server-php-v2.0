$(function(){
	var myValidate = K.util.myValidate,
		regexp = K.util.myValidate.regexp,
		server = K.server;
	//alert(fullpage);
	$('#hm_fullpage').fullpage({
		anchors: ['home', 'product', 'about' ],
		menu: '#menu',
		navigation: true,
		scrollingSpeed: 200,
		afterLoad: function(anchorLink, index) {
			var items = $('.header_menu a');
			items.removeClass('menu-item-active');
			items.eq(index-1).addClass('menu-item-active');
		}
	});

	$(".loading-mask").fadeOut(350,function(){
		$(this).remove();
	});

	$(".anchor-login").click(function(){
		$(".container").fadeIn(450);
	});

	$('.anchor-reg').click(function(){
		layer.open({
		   	type: 1,
		    title: false,
		    closeBtn: false,
		    shadeClose: true,
		    area: ['430px', '380px'],
			content: $('.qr-warp'),
			success: function(layero, index) {
				layero.find('.qr-close').click(function() {
					layer.close(index);
				})
			}
		});
	})

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
				pwd = $.trim($('#password').val());
              
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
				    	location.href = 'main';
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

 
