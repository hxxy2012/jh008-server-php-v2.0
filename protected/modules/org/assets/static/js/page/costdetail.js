define(function(require, exports, module) {
	var $ = require('$');
	var dialogUi = require('dialogUi'),
		server = require('server');
        //alert(dialogUi);
	//	common = require('common');
	        //生成div1行
        var htmls='';
        //页码
        var p_page=1;
        //每页显示调速
        var p_size=100;
	$(function(){
		function tip (text) {
			dialogUi.text(text);
		}
        //获取数据
        go_ready();
        function go_ready()
        {
               //获取流水账
			   zdtext(p_page,p_size);
               //收入记录
		       srtext(p_page,p_size);
               //支出记录
			   zctext(p_page,p_size);
                //充值记录
               cztext(p_page,p_size);
                //提现记录
               txtext(p_page,p_size);
        }
        
        //获取流水账
        function  zdtext(p_page,p_size)
        {
            //用户体验
                var  dialog =  dialogUi.wait();
				server.bills({filter:0,type:'all',page:p_page,size:p_size}, function(resp) 
                {
					if (resp.code == 0) 
                    {
				    	//获取流水账
                        if(resp.body.bills)
                          $('.zdtext').html(_div(resp.body.bills));
                        else
                           $('.zdtext').html('暂无数据！');  
					} 
                    else 
                    {
						tip(resp.msg);
					}                        
                    //关闭用户体验
                     dialog.hide();
				})
        } 

        
        //收入记录
        function  srtext(p_page,p_size)
        {
				server.bills({filter:1,type:'all',page:p_page,size:p_size}, function(resp) 
                {
					if (resp.code == 0) 
                    {
				    	 
                        if(resp.body.bills)
                          $('.srtext').html(_div(resp.body.bills));
                        else
                           $('.srtext').html('暂无数据！');  
					} 
                    else 
                    {
						tip(resp.msg);
					}                        
    
				}) 
        }
        //支出记录
        function  zctext(p_page,p_size)
        {
				server.bills({filter:2,type:'all',page:p_page,size:p_size}, function(resp) 
                {
					if (resp.code == 0) 
                    {
				    	 
                        if(resp.body.bills)
                          $('.zctext').html(_div(resp.body.bills));
                        else
                           $('.zctext').html('暂无数据！');  
					} 
                    else 
                    {
						tip(resp.msg);
					}                        
  
				}) 
        }
        
        //充值记录
        function  cztext(p_page,p_size)
        {
				server.bills({filter:0,type:'recharge',page:p_page,size:p_size}, function(resp) 
                {
					if (resp.code == 0) 
                    {
                        if(resp.body.bills)
                          $('.cztext').html(_div(resp.body.bills));
                        else
                           $('.cztext').html('暂无数据！');  
					} 
                    else 
                    {
						tip(resp.msg);
					}                        
         
				})
        }
        //提现记录
        function  txtext(p_page,p_size)
        {
				server.bills({filter:0,type:'withdraw_cash',page:p_page,size:p_size}, function(resp) 
                {
					if (resp.code == 0) 
                    {
                        if(resp.body.bills)
                          $('.txtext').html(_div(resp.body.bills));
                        else
                           $('.txtext').html('暂无数据！');  
					} 
                    else 
                    {
						tip(resp.msg);
					}                        
				})
        
        }
        
        
        
        
        
        
        //生成div1行
       
        function  _div(obj)
        { 
            var htmls='';
            if(obj)
            {

                for(var i=0;i<obj.length;i++)
                {
                
                    var aa='c2_2_4';
                    if(i%2==0)
                    {
                         aa='c2_2_4';
                    }
                    else
                    {
                         aa='c2_2_5';
                    }
                    //状态
                    var s_status='';
                    s_status=(obj[i].status==0?'<span style="color:#FF9A37;">处理中</span>':(obj[i].status==1?'<span style="color:red;">失败</span>':'<span style="color: #31B114;">成功</span>'));
                    //金额
                    var s_total_fee=0;
                    s_total_fee=(obj[i].total_fee==0?'<span>0</span>':(obj[i].total_fee<0?'<span style="color:red;">'+obj[i].total_fee+'</span>':'<span style="color: #31B114;">+'+obj[i].total_fee+'</span>'));
                    //手续费
                     var s_fee=0;
                    s_fee=(obj[i].fee==0?'<span>0</span>':(obj[i].fee<0?'<span style="color:red;">'+obj[i].fee+'</span>':'<span style="color: #31B114;">+'+obj[i].fee+'</span>'));
                   htmls+='<div class="'+aa+'">'+
                      '<div class="xunhuan1"></div>'+
                      '<div class="xunhuan">'+
                         '<div class="c2_2_4_1">&nbsp;&nbsp;'+obj[i].create_time.substr(0,10)+'</div>'+
                         '<div class="c2_2_4_2">'+obj[i].title+'</div>'+
                         '<div class="c2_2_4_3">'+s_total_fee+'</div>'+
                         '<div class="c2_2_4_4">'+s_fee+'</div>'+
                         '<div class="c2_2_4_5">'+s_status+'</div>'+
                         '<div class="clear"></div>'+
                      '</div></div>';
                }
            }
            return htmls;
        }
        
        
        //分页
        
        
        
        
        //切换
		$('.sxk').click(function()
        {    var sk=  $(this).attr('va');
            if(sk==0)
            {   
                $('.sxk').css({"background":"url('')"});
                $(this).css({"background":"url("+url+"/static/images/payment/mx1.png)"});
                $('.zdtext').css({'display':'block'});
                $('.srtext').css({'display':'none'});
                $('.zctext').css({'display':'none'});
                $('.cztext').css({'display':'none'});
                $('.txtext').css({'display':'none'});
            }
            if(sk==1)
            {
                $('.sxk').css({"background":"url('')"});
                $(this).css({"background":"url("+url+"/static/images/payment/mx1.png)"});
                $('.zdtext').css({'display':'none'});
                $('.srtext').css({'display':'block'});
                $('.zctext').css({'display':'none'});
                $('.cztext').css({'display':'none'});
                $('.txtext').css({'display':'none'});
            }
            if(sk==2)
            {
                $('.sxk').css({"background":"url('')"});
                $(this).css({"background":"url("+url+"/static/images/payment/mx1.png)"});
                $('.zdtext').css({'display':'none'});
                $('.srtext').css({'display':'none'});
                $('.zctext').css({'display':'block'});
                $('.cztext').css({'display':'none'});
                $('.txtext').css({'display':'none'});
            }
            if(sk==3)
            {
                $('.sxk').css({"background":"url('')"});
                $(this).css({"background":"url("+url+"/static/images/payment/mx1.png)"});
                $('.zdtext').css({'display':'none'});
                $('.srtext').css({'display':'none'});
                $('.zctext').css({'display':'none'});
                $('.cztext').css({'display':'block'});
                $('.txtext').css({'display':'none'}); 
            }
            if(sk==4)
            {
                $('.sxk').css({"background":"url('')"});
                $(this).css({"background":"url("+url+"/static/images/payment/mx1.png)"});
                $('.zdtext').css({'display':'none'});
                $('.srtext').css({'display':'none'});
                $('.zctext').css({'display':'none'});
                $('.cztext').css({'display':'none'});
                $('.txtext').css({'display':'block'});
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


