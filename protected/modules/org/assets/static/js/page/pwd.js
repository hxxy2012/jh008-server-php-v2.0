define(function(require, exports, module) {
	var $ = require('$');
	var dialogUi = require('dialogUi'),
		server = require('server');
        //alert(dialogUi);
	//	common = require('common');
	

	$(function(){
		function tip (text) {
			dialogUi.text(text);
		}
  
        
     //提交信息
     $('#save').click(function()
     {
        //获取值
        var old_pwd=$('#old_pwd').val();
        var new_pwd=$('#new_pwd').val();
        var new_pwd2=$('#new_pwd2').val();
        if(old_pwd=='')
        {
            tip('请输入旧密码！');
        }
        else if(new_pwd!=new_pwd2 || new_pwd=='' )
        {
            tip('2次密码不相同！');
        }
        else if(new_pwd.length<6)
        {
            tip('密码不能小于6位！');
        }
        else
        {
           //alert(hex_md5(old_pwd))  ;
            //用户体验
            var  dialog =  dialogUi.wait();
           //提交数据
		   server.modifyPassword({oldPassword:hex_md5(old_pwd),newPassword:hex_md5(new_pwd)}, function(resp) 
           {
						if (resp.code == 0) 
                        {
                            tip('密码修改成功！');
						} 
                        else 
                        {
							tip(resp.msg );
						}
                        //关闭用户体验
                        dialog.hide();
		    })
        }
     })
        
  
	    //登出
		loginEl = $('.out');
		loginEl.click(function(){
                    //提交数据
					server.loginout({}, function(resp) 
                    {
						if (resp.code == 0) 
                        {
					    	location.href = 'login';
						} 
                        else 
                        {
							tip(resp.msg || '退出失败');
						}
					})
				})
			
		})

	})


