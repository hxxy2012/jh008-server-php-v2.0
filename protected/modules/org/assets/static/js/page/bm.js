define(function(require, exports, module) {
	var $ = require('$');
	var dialogUi = require('dialogUi'),
		server = require('server');

        //alert(dialogUi);
	//	common = require('common');
	
 //用户体验
 var  dialog =  dialogUi.wait();
	$(function(){
      //报名结束时间
      var endtime='';
      //添加用户
      var adduser='';
		function tip (text) {
			dialogUi.text(text);
		}
         
        
        //获取信息
        loed();
        function loed()
        {
            //用户体验
            //var  dialog =  dialogUi.wait();
           //查活动报名列表3已通过
		   server.enrolls({actId:aid,type:3}, function(resp) 
           {
						if (resp.code == 0) 
                        {     
                            //用户
                            $('#bm1').html('');
                            //已报名
                             var shu=0;
                             if(resp.body.enrolls!=null)
                             shu= resp.body.enrolls.length;
                            //报名结束时间
                             endtime= resp.body.enroll_e_time;
                             //报名结束时间 倒计时             
                            if(endtime!='')
                            {
                                //活动时间的秒
                                var into_time=  new Date(endtime).getTime();
                                //当前秒
                                var now_time= new Date().getTime();
                                //计算时间差
                                var diff_time = Math.ceil((into_time  -  now_time)/1000 );
                                
                                //计时器
                                var tick_time = 0;
                                //定时器对象
                                var tick_timer = 0;
                             
                                function TickTimer(){
                                    if(diff_time <tick_time ){
                                       // document.getElementById('sign_button').removeAttribute('disabled');
                                        clearInterval(tick_timer);
                                    }else{
                                        //document.getElementById('sign_button').setAttribute('disabled','true');//按钮失效
                                        var diff = diff_time - tick_time;
                                        var h = Math.floor(diff/3600).toString();
                                        var m = Math.floor( (diff-3600*h)/60 );
                                        var s = diff-3600*h-60*m;
                                        //int转成string
                                         h = h.toString();
                                         m = m.toString();
                                         s = s.toString();
                                        // alert(h.length);
                                        if(h.length>=3)
                                        {
                                             document.getElementById('h1').innerHTML =h.substr(0, 1);
                                        }
                                        else
                                        {
                                            document.getElementById('h1').innerHTML =0;
                                        }
                                        if(h.length>=3)
                                        {
                                             document.getElementById('h2').innerHTML =h.substr(1, 1);
                                        }
                                        else if(h.length>=2)
                                        {
                                            document.getElementById('h2').innerHTML =h.substr(0, 1);
                                        }
                                        else
                                        {
                                            document.getElementById('h2').innerHTML =0;
                                        }
                                        if(h.length>=3)
                                        {
                                             document.getElementById('h3').innerHTML =h.substr(2, 1);
                                        }
                                        else if(h.length>=2)
                                        {
                                            document.getElementById('h3').innerHTML =h.substr(1, 1);
                                        }
                                        else
                                        {
                                            document.getElementById('h3').innerHTML =h;
                                        }
                                        //分钟
                                        if(m.length>=2)
                                        {
                                            document.getElementById('m1').innerHTML =m.substr(0, 1);
                                        }
                                        else
                                        {
                                            document.getElementById('m1').innerHTML =0;
                                        }
                                        //分钟
                                        if(m.length>=2)
                                        {
                                            document.getElementById('m2').innerHTML =m.substr(1, 1);
                                        }
                                        else
                                        {
                                            document.getElementById('m2').innerHTML =m;
                                        }
                                        //秒
                                        if(s.length>=2)
                                        {
                                            document.getElementById('s1').innerHTML =s.substr(0, 1);
                                        }
                                        else
                                        {
                                            document.getElementById('s1').innerHTML =0;
                                        }
                                        //秒
                                        if(s.length>=2)
                                        {
                                            document.getElementById('s2').innerHTML =s.substr(1, 1);
                                        }
                                        else
                                        {
                                            document.getElementById('s2').innerHTML =s;
                                        }
                                        
                                        
                                        
                                    }
                                    tick_time++;
                                } 
                                    tick_timer = setInterval(TickTimer,1000);
                             
                                
                            }
                            else
                            {
                              // $('.button').css({'background':'url(__PUBLIC__/image/client/sy_qiandao3.png)'});
                            }

                           
                                       
                          
                            if(shu>0)
                            {
                               //custom_keys
                               //添加用户生成
                               if(resp.body.custom_keys)
                               {
                                   for(var jj=0;jj<resp.body.custom_keys.length;jj++)
                                   {
                                          
                                          adduser+='<div class="adduser" >'+resp.body.custom_keys[jj]+' :<input class="srk scd"  type="text"  n="'+resp.body.custom_keys[jj]+'" maxlength="10"/></div>';
                                   }
                               }
                                //平均分table
                               var hh=4; 
                               $('#ybm').html(shu);
                                //赋值
                               var   bmhtml=  '<tr><th class="xm">姓名</th><th>电话</th><th>性别</th><th>年龄</th>';
                                for (x in resp.body.enrolls[0])
                                {
                                    if(x!='u_id' && x!='real_name' && x!='contact_phone'&& x!='sex'&& x!='status'&& x!='group_id' && x!='enroll_id' && x!='age' && x!='birth')
                                    {
                                        //生成头
                                        bmhtml+=  '<th>'+x+'</th>';
                                        hh++;
                                    }
                                   
                                }
                                bmhtml=bmhtml+'</tr>';
                                 //平均分table
                                hh=Math.floor((100/hh));
                                //生成数据
                                for(var i=0; i<shu; i++)   
                                {
                                
                                     bmhtml+='<tr class='+(i%2==0?'aa':'ss')+'>';
                                    for (j in resp.body.enrolls[i])
                                    {
                                        if(j!='status'&& j!='group_id' && j!='u_id' && j!='enroll_id'  && j!='birth')
                                        {   
                                            var sex='';
                                            if(j=='sex')
                                            {
                                                //1男 2女
                                                if(resp.body.enrolls[i][j]=='0')
                                                sex='/';
                                                else if(resp.body.enrolls[i][j]=='1')
                                                sex='男';
                                                else
                                                sex='女';
                                            }
                                            else
                                            {
                                                sex=resp.body.enrolls[i][j]==''?'/':resp.body.enrolls[i][j];
                                            }
                                            //生成头
                                            bmhtml+=  '<td width="'+hh+'%">'+sex+'</td>';
                                        }
                                    }
                                    bmhtml+='</tr>';
                                }
                               $('#bm1').append(bmhtml);
                           }
                           else
                           {
                              //平均分table
                               var hh=4; 
                               $('#ybm').html(shu);
                                //赋值
                               var   bmhtml=  '<tr><th class="xm">姓名</th><th>电话</th><th>性别</th><th>年龄</th>';
                               //添加用户生成
                               if(resp.body.custom_keys)
                               {
                                   for(var jj=0;jj<resp.body.custom_keys.length;jj++)
                                   {
                                         //生成头
                                         bmhtml+=  '<th>'+resp.body.custom_keys[jj]+'</th>';hh++;
                                         adduser+='<div class="adduser" >'+resp.body.custom_keys[jj]+' :<input class="srk scd"  type="text"  n="'+resp.body.custom_keys[jj]+'" maxlength="10"/></div>';
                                   }
                               }
                               bmhtml=bmhtml+'</tr>';
                               $('#bm1').append(bmhtml);
                               
                               
                           }
						} 
                        else 
                        {
							tip(resp.msg);
						}
                        //关闭用户体验
                        dialog.hide();
		    })
            
             server.enrolls({actId:aid,type:1}, function(resp) 
            {
						if (resp.code == 0) 
                        {
                            //用户
                            $('#sh1').html('');
                            //已报名
                             var shu=0;
                             if(resp.body.enrolls!=null)
                             shu= resp.body.enrolls.length;
                            if(shu>0)
                            {
                              //平均分table
                              var hh=5; 
                                
                               $('#wsh').html(shu);
                                //赋值
                               var   shhtml=  '<tr  ><th class="xm">姓名</th><th>电话</th><th>性别</th><th>年龄</th>';
                                for (x in resp.body.enrolls[0])
                                { 
                                    if(x!='u_id' && x!='real_name' && x!='contact_phone'&& x!='sex'&& x!='status'&& x!='group_id' && x!='enroll_id'  && x!='age' && x!='birth')
                                    {
                                        //生成头
                                        shhtml+=  '<th>'+x+'</th>';
                                        hh++;
                                    }
                                   
                                }
                                shhtml=shhtml+'<th>操作</th></tr>';
                                //平均分table
                                hh=Math.floor((100/hh)); 
                               //alert(hh);
                                //生成数据
                                for(var i=0; i<shu; i++)   
                                {
                                
                                     shhtml+='<tr class='+(i%2==0?'aa':'ss')+'>';
                                    for (j in resp.body.enrolls[i])
                                    {
                                        if(j!='status'&& j!='group_id' && j!='u_id' && j!='enroll_id' && j!='birth')
                                        {   
                                            var sex='';
                                            if(j=='sex')
                                            {
                                                if(resp.body.enrolls[i][j]=='0')
                                                sex='男';
                                                else if(resp.body.enrolls[i][j]=='1')
                                                sex='女';
                                                else
                                                sex='保密';
                                            }
                                            else
                                            {
                                                sex=resp.body.enrolls[i][j]==''?'/':resp.body.enrolls[i][j];
                                            }
                                            //生成头
                                            shhtml+=  '<td width="'+hh+'%">'+sex+'</td>';
                                        }
                                    }
                                    shhtml+='<td>'+
                                            '<div style="margin: auto;width:180px;height: auto;">'+
                                            '<div  class="ck" uid="'+resp.body.enrolls[i].enroll_id+'"></div>'+
                                            '<div  class="sc" uid="'+resp.body.enrolls[i].enroll_id+'"></div>'+
                                            '<div class="clear"></div>'+
                                            '</div></td></tr>';
                                }
                               $('#sh1').append(shhtml);
                           }
                           else
                           {
                             //$('#sh1').append('没有审核数据！');
                           }
 
						} 
                        else 
                        {
							tip(resp.msg);
						}
            })
          

        }
        
  
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
                
        //切换        
        $('#bm_ok').click(function()
        {   $('#bm1').html('');
            $(".c3_4_2_4_1").show();
            $("#aa").hide();
            $('#bm').css({'display':'block'});
            $('#sh').css({'display':'none'});
            $(this).css({"background":"url("+url+"/static/images/index/c3_2.png)"});
            $('#bm_sh').css({"background":"url('')"});
             //用户体验
            var  dialog =  dialogUi.wait();
           //查活动报名列表3已通过
		   server.enrolls({actId:aid,type:3}, function(resp) 
           {
            
            
						if (resp.code == 0) 
                        {     
                            //用户
                            $('#bm1').html('');
                            //已报名
                             var shu=0;
                             if(resp.body.enrolls!=null)
                             shu= resp.body.enrolls.length;
                          
                            if(shu>0)
                            {
                                //平均分table
                               var hh=4; 
                               $('#ybm').html(shu);
                                //赋值
                               var   bmhtml=  '<tr  ><th class="xm">姓名</th><th>电话</th><th>性别</th><th>年龄</th>';
                                for (x in resp.body.enrolls[0])
                                {
                                    if(x!='u_id' && x!='real_name' && x!='contact_phone'&& x!='sex'&& x!='status'&& x!='group_id' && x!='enroll_id'   && x!='age' && x!='birth')
                                    {
                                        //生成头
                                        bmhtml+=  '<th>'+x+'</th>';
                                        hh++;
                                    }
                                   
                                }
                                bmhtml=bmhtml+'</tr>';
                                 //平均分table
                                hh=Math.floor((100/hh));
                                //生成数据
                                for(var i=0; i<shu; i++)   
                                {
                                
                                     bmhtml+='<tr class='+(i%2==0?'aa':'ss')+'>';
                                    for (j in resp.body.enrolls[i])
                                    {
                                        if(j!='status'&& j!='group_id' && j!='u_id' && j!='enroll_id' && j!='birth')
                                        {   
                                            var sex='';
                                            if(j=='sex')
                                            {
                                                   //1男 2女
                                                if(resp.body.enrolls[i][j]=='0')
                                                sex='/';
                                                else if(resp.body.enrolls[i][j]=='1')
                                                sex='男';
                                                else
                                                sex='女';
                                            }
                                            else
                                            {
                                                sex=resp.body.enrolls[i][j]==''?'/':resp.body.enrolls[i][j];
                                            }
                                            //生成头
                                            bmhtml+=  '<td width="'+hh+'%">'+sex+'</td>';
                                        }
                                    }
                                    bmhtml+='</tr>';
                                }
                               $('#bm1').html(bmhtml);

                           //--------------------

                              $(".c3_4_2_4_1").show();
                              $("button",$(".c3_4_2_4_1").parent()).remove();
                           }
                           else
                           {
                             //平均分table
                               var hh=4; 
                               $('#ybm').html(shu);
                                //赋值
                               var   bmhtml=  '<tr><th class="xm">姓名</th><th>电话</th><th>性别</th><th>年龄</th>';
                               //添加用户生成
                               if(resp.body.custom_keys)
                               {
                                   for(var jj=0;jj<resp.body.custom_keys.length;jj++)
                                   {
                                         //生成头
                                         bmhtml+=  '<th>'+resp.body.custom_keys[jj]+'</th>';hh++;
                                         adduser+='<div class="adduser" >'+resp.body.custom_keys[jj]+' :<input class="srk scd"  type="text"  n="'+resp.body.custom_keys[jj]+'" maxlength="10"/></div>';
                                   }
                               }
                               bmhtml=bmhtml+'</tr>';
                               $('#bm1').append(bmhtml);
                           }
						} 
                        else 
                        {
							tip(resp.msg);
						}
                        //关闭用户体验
                        dialog.hide();
		    })
            
        }) 
        
        $('#bm_sh') .click(function()
        {
            $('#sh1').html('');
            //隐藏添加
            $(".c3_4_2_4_1").hide();
             $("#aa").hide();
            $('#bm').css({'display':'none'});
            $('#sh').css({'display':'block'});
            $(this).css({"background":"url("+url+"/static/images/index/c3_2.png)"});
            $('#bm_ok').css({"background":"url('')"});
            //用户体验
            var  dialog =  dialogUi.wait();
             //查活动报名列表1待审核
		   server.enrolls({actId:aid,type:1}, function(resp) 
           {
						if (resp.code == 0) 
                        {
                            //用户
                            $('#sh1').html('');
                            //已报名
                             var shu=0;
                             if(resp.body.enrolls!=null)
                             shu= resp.body.enrolls.length;
                            if(shu>0)
                            {
                              //平均分table
                              var hh=5; 
                                
                               $('#wsh').html(shu);
                                //赋值
                               var   shhtml=  '<tr ><th class="xm">姓名</th><th>电话</th><th>性别</th><th>年龄</th>';
                                for (x in resp.body.enrolls[0])
                                { 
                                    if(x!='u_id' && x!='real_name' && x!='contact_phone'&& x!='sex'&& x!='status'&& x!='group_id' && x!='enroll_id'   && x!='age' && x!='birth')
                                    {
                                        //生成头
                                        shhtml+=  '<th>'+x+'</th>';
                                        hh++;
                                    }
                                   
                                }
                                shhtml=shhtml+'<th>操作</th></tr>';
                                //平均分table
                                hh=Math.floor((100/hh)); 
                               //alert(hh);
                                //生成数据
                                for(var i=0; i<shu; i++)   
                                {
                                
                                     shhtml+='<tr class='+(i%2==0?'aa':'ss')+'>';
                                    for (j in resp.body.enrolls[i])
                                    {
                                        if(j!='status'&& j!='group_id' && j!='u_id' && j!='enroll_id' && j!='birth')
                                        {   
                                            var sex='';
                                            if(j=='sex')
                                            {
                                                if(resp.body.enrolls[i][j]=='0')
                                                sex='/';
                                                else if(resp.body.enrolls[i][j]=='1')
                                                sex='男';
                                                else
                                                sex='女';
                                            }
                                            else
                                            {
                                                sex=resp.body.enrolls[i][j]==''?'/':resp.body.enrolls[i][j];
                                            }
                                            //生成头
                                            shhtml+=  '<td width="'+hh+'%">'+sex+'</td>';
                                        }
                                    }
                                    shhtml+='<td>'+
                                            '<div style="margin: auto;width:180px;height: auto;">'+
                                            '<div  class="ck" uid="'+resp.body.enrolls[i].enroll_id+'"></div>'+
                                            '<div  class="sc" uid="'+resp.body.enrolls[i].enroll_id+'"></div>'+
                                            '<div class="clear"></div>'+
                                            '</div></td></tr>';
                                }
                               $('#sh1').html(shhtml);
                           }
                           else
                           {
         
                           }
 
						} 
                        else 
                        {
							tip(resp.msg);
						}
                        //关闭用户体验
                        dialog.hide();         
		    })  
        })  
        

      /////////////审核----拒绝/////////////////////
       $(".ck").live("click",function()
       {
          var zhe=$(this);
          var uid= zhe.attr('uid');
          if(uid!='')
          {
               //用户体验
               var  dialog =  dialogUi.wait();
               //提交数据
				server.verify({enrollId:uid,type:1}, function(resp) 
                {
					if (resp.code == 0) 
                    {
				    	tip('操作成功！');
                        //隐藏
                        zhe.parent().parent().parent().css({'display':'none'});
                        $("#wsh").html( parseInt($("#wsh").html)-1);
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
            tip('系统错误！');
          }
        
        
       });       
           
       $(".sc").live("click",function()
       {
          var zhe=$(this);
          var uid= zhe.attr('uid');
          if(uid!='')
          {
               //用户体验
               var  dialog =  dialogUi.wait();
                //提交数据
				server.verify({enrollId:uid,type:2}, function(resp) 
                {
					if (resp.code == 0) 
                    {
				    	tip('操作成功！');
                         //隐藏
                        zhe.parent().parent().parent().css({'display':'none'});
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
            tip('系统错误！');
          }
       });
     var closeed='';
       //手动添加用户
       $(".c3_4_2_4_1").click(function()
       {
          if($("#bm1 .add_form").size()<1 )
          {
            if($("td").size()>0)
            {
                var bm1 = $("#bm1");
                var add_form = $( '<div class="adduser" >姓名 :<input class="srk"  id="xm" type="text"  n="姓名" maxlength="10"/></div>'+
                  '<div class="adduser" >电话 :<input class="srk"  id="dh" type="text"  n="电话" maxlength="11"/></div>'+
                  '<div class="adduser" >性别 :<select class="srk" id="xb"><option value="1">男</option><option value="2">女</option></select></div>'+
                  '<div class="adduser" >年龄 :<input class="srk"  id="ln" type="text"  n="年龄" maxlength="10"/></div>'+adduser+
                  '<div class="adduser" ><button    id="aa">添加</button></div><div class="adduser" id="error"></div>' );
              
                  var tr_clone = $("tr:last",bm1).clone() ; 
                  $("td",tr_clone).html("");
                  tr_clone.addClass('add_form');
                  bm1.append(tr_clone);
                  //closeed=  dialogUi.text();
                  $('input,select',add_form).each(function(i){
                     $("td",tr_clone).eq(i).append(this); 
                  });
                   //$(".c3_4_2_4_1").;
                  $(".c3_4_2_4_1").hide().before($("button",add_form) );
            }
            else
            {
                var bm1 = $("#bm1");
                var add_form = $( '<div class="adduser" >姓名 :<input class="srk"  id="xm" type="text"  n="姓名" maxlength="10"/></div>'+
                  '<div class="adduser" >电话 :<input class="srk"  id="dh" type="text"  n="电话" maxlength="11"/></div>'+
                  '<div class="adduser" >性别 :<select class="srk" id="xb"><option value="1">男</option><option value="2">女</option></select></div>'+
                  '<div class="adduser" >年龄 :<input class="srk"  id="ln" type="text"  n="年龄" maxlength="10"/></div>'+adduser+
                  '<div class="adduser" ><button    id="aa">添加</button></div><div class="adduser" id="error"></div>' );
              
                  var tr_clone = $("tr:last",bm1).clone() ; 
                  $("th",tr_clone).html("");
                  tr_clone.addClass('add_form');
                  bm1.append(tr_clone);
                  //closeed=  dialogUi.text();
                  $('input,select',add_form).each(function(i){
                     $("th",tr_clone).eq(i).append(this); 
                  });
                   //$(".c3_4_2_4_1").;
                  $(".c3_4_2_4_1").hide().before($("button",add_form) );
            }
        }

        
        
       })
       
       
       //添加用户提交
        $("#aa").live("click",function()
       {
         dialog.show();
        if(aid!='')
        {
             //自定义字段数组
             var p_zdy=new Array();
            //处理数据
              var p_realName =$("#xm").val();
              var p_contactPhone=$("#dh").val();
              var p_sex=$("#xb").val();
              var p_age=$("#ln").val();
              var jj=0;
            $(".scd").each(function()
            {
                
              var key =$(this).attr('n');
              var vlue=$(this).val();
               p_zdy[jj]=key+','+vlue;
               jj++;
            })
            //alert(p_realName+'---'+p_contactPhone+'---'+p_sex+'---'+p_age);
            if(p_realName!='' && p_contactPhone!='' && p_sex!='' && p_age!='')
            {
               // alert(aid);
                //提交数据   contactPhone
    			server.manualEnroll({actId:aid,realName:p_realName,contactPhone:p_contactPhone,sex:p_sex,age:p_age,customFields:p_zdy}, function(resp) 
                {
    				if (resp.code == 0) 
                    {

    			    	tip('<span style="color:lime;">添加成功！</span>');
                        //清空
                        $(".scd").each(function()
                        {
                           $(this).val('');
                        })
                        $("#xm").val('');
                        $("#dh").val('');
                        $("#xb").val('');
                        $("#ln").val('');
                        window.location.reload();
    				} 
                    else 
                    {
          dialog.hide(); 
    				    tip('<span style="color:red;">'+resp.msg+'</span>');
    				}
    			})
              }
              else
              {
          dialog.hide(); 
                tip('填写不完整！');
              }

        }
        else
        {   
          dialog.hide(); 
            //closeed.hide();
            tip('系统错误！');
        }
         
       });
       
        	
	})

})


