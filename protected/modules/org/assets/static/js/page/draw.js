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
        
        //申请提现情况
        lord();
        function lord()
        {
            var  dialog =  dialogUi.wait();
	       server.withdrawCashAllow({}, function(resp) 
            {
				if (resp.code == 0) 
                {
                    //赋值
                    $('#allow_fee').html(resp.body.withdraw_cash_info.allow_fee);
                    $('#real_name').val(resp.body.withdraw_cash_info.real_name);
                    $('#allow_num').html(resp.body.withdraw_cash_info.allow_num);
			    	
				} 
                else 
                {
					tip(resp.msg);
				}                        
                //关闭用户体验
                dialog.hide();
			})
            
            
            
        }
        
        
        
        
        
        
        
        
        
        //提交
		$('.c2_8_1').click(function()
        { 
            //获取数据
            var p_way='alipay';
            var p_totalFee=0;
            var p_realName=$('#real_name').val();
            var P_outAccount=$('.zhanghao').val();
            $('.zf').each(function(){
                if($(this).is(":checked"))
                p_way=$(this).val();
            })
            //判断金额
            var r = /^\d{1,12}(?:\.\d{1,2})?$/;
           if(r.test($('.txmoney').val()) && $('.txmoney').val()!='0' &&  $('.txmoney').val()!='0.0' &&  $('.txmoney').val()!='0.00')
           {    
                p_totalFee=$('.txmoney').val();
                if(p_realName!='')
                {
                   if(P_outAccount!='')
                   {
                        //用户体验
                        var  dialog =  dialogUi.wait();
                       //提交数据
        				server.withdrawCashApply({totalFee:p_totalFee ,realName:p_realName,way:p_way,outAccount:P_outAccount,}, function(resp) 
                        {p_way
        					if (resp.code == 0) 
                            {
        				    	tip('申请提现成功！');
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
                       tip ('未输入提现账号！') ;
                    }
                } 
                else
                {
                   tip ('未输入收款人姓名！') ;
                }
           }
           else
           {
               tip ('请输入正确的金额！') ;
           }
                       
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


