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
        
        
        //个人信息
        loed();
        function loed(){
            //用户体验
         var  dialog =  dialogUi.wait();
         
           
            
           //提交数据-个人信息
		   server.getinfo({}, function(resp) 
           {
						if (resp.code == 0) 
                        {
                             $('.org_name').html(resp.body.user.org.name);
                             $('#logo_img_url').attr('src',resp.body.user.org.logo_img_url);
                             $('#city_name').html(resp.body.user.city_name);
                             $('#progress_act_num').html(resp.body.user.progress_act_num);
                             $('#pho_num').html(resp.body.user.pho_num);
                             $('#account_balance').html(resp.body.user.account_balance);
 
						} 
                        else 
                        {
							tip(resp.msg);
						}
                        //关闭用户体验
                        //dialog.hide();
		    })  
           //提交数据-当前
		   server.currentActs({}, function(resp) 
           {
						if (resp.code == 0) 
                        {
                            $("#start").html('');
                                //alert(resp.body.acts.length);
                                var nowhtml='';
                                var types='';
                             for(var i=0; i<resp.body.acts.length; i++)   
                             {   
                                //alert(resp.body.acts[i].id);
                                       var  febxiang='';
                                        if(resp.body.acts[i].status=='-1')
                                          {
                                            types='已删除';
                                          }
                                          else if(resp.body.acts[i].status=='0')
                                          {
                                            types='未提交';
                                          }
                                          else if(resp.body.acts[i].status=='1')
                                          {
                                            types='待审核';
                                          }
                                          else if(resp.body.acts[i].status=='2')
                                          {
                                            types='审核中';
                                          }
                                          else if(resp.body.acts[i].status=='3')
                                          {
                                            types='未通过';
                                          }
                                          else if(resp.body.acts[i].status=='4')
                                          {
                                            types='未发布';
                                          }
                                          else if(resp.body.acts[i].status=='5')
                                          {
                                            types='已发布';
                                          }
                                          else
                                          {
                                            types='已下架';
                                          }
                                          if(resp.body.acts[i].status=='5')
                                          {
                                             febxiang=   '<div class="c3_15" data-id="'+resp.body.acts[i].share_url+'"></div>';
                                          }
                                
                                //生成HTML
                              nowhtml +=  '<div class="ok" >'+
                                          '<div class="c3_8">'+
                                          '<div class="c3_9"><img width="240" height="160" src="'+resp.body.acts[i].h_img_url+'"/></div>'+
                                          '<div class="c3_10">'+
                                          '<div class="c3_11">'+types+'</div>'+
                                          '<div class="c3_12">已发布至APP</div>'+
                                          '<div class="c3_13">'+
                                          '<span>报名人数: '+resp.body.acts[i].enroll_num+'</span>&nbsp;<span>签到人数: '+resp.body.acts[i].checkin_num+'</span></div>'+
                                          '</div>'+
                                          '<div class="newo">'+                  
                                          '<a class="c3_14" href="/org/default/qd?id='+resp.body.acts[i].id+'" ></a>'+
                                          '<br />'+febxiang+
                                          '</div>'+
                                          '<div class="clear"></div>'+
                                          '</div>'+
                                          '<div class="c3_16">'+
                                          '<div style="float: left;height: 50px;width: auto;">'+
                                          '<div class="c3_17">'+resp.body.acts[i].title+'</div>'+
                                          '<div class="c3_18">'+resp.body.acts[i].b_time.substr(0,10)+' 至 '+resp.body.acts[i].e_time.substr(0,10)+'</div>'+  
                                          '</div>'+
                                          '<div class="c3_21 shanchu"  aid="'+resp.body.acts[i].id+'"></div>'+
                                          '<a class="c3_22" href="/org/default/bm?id='+resp.body.acts[i].id+'"></a>'+
                                          '<a class="c3_23" href="/org/default/activity?id='+resp.body.acts[i].id+'"></a>'+ 
                                          '<input type="hidden" class="ACT_ID" value="'+resp.body.acts[i].id+'"/>'+               
                                          '</div>'+
                                          '</div>';
                                                  
                                          
                             } 
                             
                             $("#start").append(nowhtml); 
 
						} 
                        else 
                        {
							tip(resp.msg);
						}
                        //关闭用户体验
                        dialog.hide();
		    }) 
          //提交数据-以前
		   server.pastActs({}, function(resp) 
           {
						if (resp.code == 0) 
                        {
                             var oldhtml='';
                            // $("#stop").html('');
                             var types='';
                             for(var i=0; i<resp.body.acts.length; i++)   
                             {   
                                //alert(resp.body.acts[i].id);
                                        if(resp.body.acts[i].status=='-1')
                                          {
                                            types='已删除';
                                          }
                                          else if(resp.body.acts[i].status=='0')
                                          {
                                            types='未提交';
                                          }
                                          else if(resp.body.acts[i].status=='1')
                                          {
                                            types='待审核';
                                          }
                                          else if(resp.body.acts[i].status=='2')
                                          {
                                            types='审核中';
                                          }
                                          else if(resp.body.acts[i].status=='3')
                                          {
                                            types='未通过';
                                          }
                                          else if(resp.body.acts[i].status=='4')
                                          {
                                            types='未发布';
                                          }
                                          else if(resp.body.acts[i].status=='5')
                                          {
                                            types='已发布';
                                          }
                                          else
                                          {
                                            types='已下架';
                                          }
                                
                                //生成HTML
                              oldhtml +=  '<div class="ok" >'+
                                          '<div class="c3_8">'+
                                          '<div class="c3_9"><img width="240" height="160" src="'+resp.body.acts[i].h_img_url+'"/></div>'+
                                          '<div class="c3_10">'+
                                          '<div class="c3_11">'+types+'</div>'+
                                          '<div class="c3_12">已发布至APP</div>'+
                                          '<div class="c3_13">'+
                                          '<span>报名人数: '+resp.body.acts[i].enroll_num+'</span>&nbsp;<span>签到人数: '+resp.body.acts[i].checkin_num+'</span></div>'+
                                          '</div>'+
                                      
                                          '<div class="clear"></div>'+
                                          '</div>'+
                                          '<div class="c3_16">'+
                                          '<div style="float: left;height: 50px;width: auto;">'+
                                          '<div class="c3_17">'+resp.body.acts[i].title+'</div>'+
                                          '<div class="c3_18">'+resp.body.acts[i].b_time.substr(0,10)+' 至 '+resp.body.acts[i].e_time.substr(0,10)+'</div>'+  
                                          '</div>'+
                                          '<div class="c3_21 shanchu"  aid="'+resp.body.acts[i].id+'"></div>'+
                                          '<a class="c3_22" href="/org/default/bm?id='+resp.body.acts[i].id+'"></a>'+
                                          
                                          '<input type="hidden" class="ACT_ID" value="'+resp.body.acts[i].id+'"/>'+               
                                          '</div>'+
                                          '</div>';
                             } 
                             
                             $("#stop").append(oldhtml); 
 
						} 
                        else 
                        {
							tip(resp.msg);
						}
                        //关闭用户体验
                        //dialog.hide();
		    })
            //关闭用户体验
            //dialog.hide(); 
            
        }
        
  
	    //登出
		loginEl = $('.out');
		loginEl.click(function(){
                    //提交数据
					server.loginout({}, function(resp) 
                    {
						if (resp.code == 0) 
                        {
					    	location.href = '/org/default/login';
						} 
                        else 
                        {
							tip(resp.msg);
						}
					})
				})
                
        //活动查看切换        
        $('#now').click(function(){
            $('#start').css({'display':'block'});
            $('#stop').css({'display':'none'});
            $(this).css({"background":"url("+url+"/static/images/index/c3_2.png)"});
            $('#old').css({"background":"url('')"});
            
        }) 
        
            
            
       
        $('#old') .click(function(){
            $('#start').css({'display':'none'});
            $('#stop').css({'display':'block'});
            $(this).css({"background":"url("+url+"/static/images/index/c3_2.png)"});
            $('#now').css({"background":"url('')"});
        })  
        
       //删除活动
       $(".shanchu").live("click",function()
       {
           var aid='';
           var zhe=$(this);
           aid=zhe.attr('aid');
           if(aid=='')
           {
              tip('错误:系统错误！');
           }
           else
           {
               var  sc =  dialogUi.wait();
              //提交删除
				server.del({actId:aid}, function(resp) 
                {
						if(resp.code == 0 ) 
                        {
                            //隐藏
                            zhe.parent().parent().css({'display':'none'});
                          
						} 
                        else 
                        {
							tip(resp.msg);
						}
                        sc.hide();
				})
              
              
           }
           
           
           
           
       });
       //2微码
  		$(".c3_15").live("click",function(){ 
          if(makeCode){
              makeCode($(this).data('id'));
             _showQRCode();
          }
  		})    
		$(".qr-warp").click(_hideQRCode);
		function _showQRCode(){
		  
			$(".qr-warp").fadeIn(500);
		}
		function _hideQRCode(){
			$(".qr-warp").fadeOut(500);
		}
             
           
			
	})

})


