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
        $('#mag_fs').click(function()
        {
            var p_actId=$('#actId').val();
            var p_hasPush=$('.p_hasPush').is(':checked')?1:0;;
            var p_hasSms=$('.p_hasSms').is(':checked')?1:0;;
            var p_content=$('.p_content').val();
            if(p_hasPush==0 && p_hasSms==0)
            {
                tip ('错误：发送方式未选择！');
            }
            else if(p_content=='')
            {
                tip ('错误：发送内容未填写！');
            }
            else
            {
               //用户体验
               var  dialog =  dialogUi.wait();
                //发送
    		   server.sendMsg({actId:p_actId,hasPush:p_hasPush,hasSms:p_hasSms,content:p_content}, function(resp) 
               {
    						if (resp.code == 0) 
                            {
                                 tip('发送成功！');
    						} 
                            else 
                            {
    							tip(resp.msg);
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


