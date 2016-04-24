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
        
     //获取信息   
        loed();
        function loed(){
            //用户体验
            var  dialog =  dialogUi.wait();
           //提交数据
		   server.getinfo({}, function(resp) 
           {
						if (resp.code == 0) 
                        {    
                             var orgInfo= resp.body.user.org.id;
                                //提交数据
                    		   server.orgInfo({orgId:orgInfo}, function(resp) 
                               {
                    						if (resp.code == 0) 
                                            {
                                                $('#org_name').val(resp.body.org.name);
                                                //$('#p_detail').html(resp.body.org.intro);
                                                $('#p_detail').val(resp.body.org.intro);
                                                $('#logo_img_url').attr('src',resp.body.org.logo_img_url);
                                                $('#contact_way').val(resp.body.org.contact_way);
                                                $('#address').val(resp.body.org.address);
                    						} 
                                            else 
                                            {
                    							tip(resp.msg);
                    						}
                                            
                    		    }) 
     
						} 
                        else 
                        {
							tip(resp.msg);
						}
                       //关闭用户体验
                       dialog.hide();
                        
		    })
           //login上传  
           var  dialog11='';
         	var oBtn = document.getElementById("fileUpload");//按钮	
        	new AjaxUpload(oBtn,{
        		action:uploader_url,
        		name:"img", //设置文件上传的名称  也就是 $_FILES[uploa][]
        		onSubmit:function(file,ext)
                {
        			if(ext && /^(jpg|jpeg)$/.test(ext)){ //匹配上传格式
        				//ext是后缀名
        	            dialog11 =  dialogUi.wait();
        			}else
                    {	
        				tip("错误:上传图片格式为jpg!");
        				return false;
        			}
        		},
        		onComplete:function(file,response)
                {
                        //关闭用户体验
                        dialog11.hide();
                    try {
                            response = jQuery.parseJSON(response);
                        } 
                        catch (e) 
                        {
                            response = {"code": -128, "msg": "图片数据获取失败"};
                        }
  
                        if (response.code == '0') 
                        {
                           $('#logo_img_url').attr('src',response.body.img_url);
                           $('#logo_img_url').attr('img_id',response.body.id);
                           
                        } else 
                        {
                           	tip(response.msg);
                        }

        		}
                
        	}); 
            
           
            
        }
        

        
        
        
        
        
        
     //提交信息
     $('#save').click(function(){
        //获取值
        var modifyInfo_id=$('#logo_img_url').attr('img_id');
        var org_name=$('#org_name').val();
        var org_contact_way=$('#contact_way').val();
        var org_address= $('#address').val();
        //var org_intro= $('#p_detail').html();
        var org_intro= $('#p_detail').val();
        //用户体验
         var  dialog =  dialogUi.wait();
           //提交数据
		   server.modifyInfo({logoImgId:modifyInfo_id,name:org_name,intro:org_intro,contactWay:org_contact_way,address:org_address}, function(resp) 
           {
						if (resp.code == 0) 
                        {
                            tip('保存成功');
						} 
                        else 
                        {
							tip(resp.msg );
                            
						}
                        //关闭用户体验
                        dialog.hide();
		    })  
            
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


