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


