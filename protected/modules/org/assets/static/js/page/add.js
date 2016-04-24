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
        
        
        //充值提交
		$('#tj_post').click(function()
        { tip ('功能暂未开放！') ;
            /*
            //获取数据
            var p_way='alipay';
            var p_totalFee=0;
            $('.zf').each(function(){
                if($(this).is(":checked"))
                p_way=$(this).val();
            })
            //判断金额
            var r = /^\d{1,12}(?:\.\d{1,2})?$/;
           if(r.test($('.jetesxt').val()) && $('.jetesxt').val()!='0' &&  $('.jetesxt').val()!='0.0' &&  $('.jetesxt').val()!='0.00')
           {    
                p_totalFee=$('.jetesxt').val();
                //用户体验
                var  dialog =  dialogUi.wait();
               //提交数据
				server.rechargePayUrl({totalFee:p_totalFee,way:p_way}, function(resp) 
                {
					if (resp.code == 0) 
                    {
				    	
					} 
                    else 
                    {
						tip(resp.msg);
					}                        
                    //关闭用户体验
                    dialog.hide();
				})
           }
           else
           {
               tip ('请输入正确的金额！') ;
           }
                   */    
        });
        
  
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
							tip(resp.msg);
						}
					})
				})
    
	})

})


