/**
 * 管理员设置页面
 * @author      吴华
 * @company     成都哈希曼普科技有限公司
 * @site        http://www.hashmap.cn
 * @email       wuhua@hashmap.cn
 * @date        2015-04-21
 */
define(function (require, exports, module) {
    var $ = require('$');
    var dialogUi = require('dialogUi');
    var server = require('server');
    var loading = dialogUi.wait();//等待css
    //所有的管理员个数
    var manager_total = 0;
    var manager_page_size = 5;
    var manager_pages = 1;
    var manager_page = 1;


    //程序入口开始
    $(function () {
        load_managers();
   

        //程序入口结束
    });

    /**
     * 加载管理员方法
     * @returns {boolean}
     */
    function load_managers() {
        if (manager_page > manager_pages) 
        {
            return false;
        } else {
            //获取关注人列表
            server.lovs({}, function (mangers) 
            {
                //{"code":0,"msg":"SUCCESS : 'user friends success'","body":{"total_num":"6","users":[{"id":"5","head_img_url":"http:\/\/test.image.app.jhla.com.cn\/20150128\/201501281807316407.jpg","is_vip":1,"nick_name":"\u5468","sex":"1","birth":"1990-10-10 00:00:00","intro":null,"hobby":null,"last_login_platform":"-1","baidu_user_id":null,"baidu_channel_id":null,"is_focus":1}]}}
                
                if (mangers.code) {
                    dialogUi.text(mangers.msg);
                } 
                else 
                {
                    if (mangers.body) {
                        manager_total = mangers.body.total_num;
                        manager_pages = Math.ceil(manager_total / manager_page_size);
                        //{"id":"5","u_name":"zhouyao","name":"\u5468","type":"11","status":"0","create_time":"2015-01-20 19:52:41","last_login_time":"2015-01-31 16:58:33"}
                        if (mangers.body.users)
                            show_manager(null);//清除所有数据
                        for (var i in mangers.body.users) {
                            if (mangers.body.users[i]) {
                                show_manager(mangers.body.users[i]);
                            }
                        }
                        
                        
                        //获取到管理员列表
                        server.orgmanagers({}, function(resp) 
                        {
            				if (resp.code == 0) 
                            {
               	                if (resp.body) {  
                                    
                                    add_manager.fast = true;                  
                                    for (var i in resp.body.users) {
                                        if (resp.body.users[i]) {
                                           //showMangersForSetList(resp.body.users[i]);
                                           show_manager(resp.body.users[i]).click();
                                        }
                                    }
                                    add_manager.fast = false;
                                }
            				} 
                            else 
                            {
            			     	dialogUi.text(mangers.msg);
            				}
                            loading.hide();
                       })
                    }

                }
                
            });
            
            
            
            
        }
    }
    //添加
    $('.c3_3_4_2').live("click",function()
    {
        var loading1 = dialogUi.wait();
        var p_uid= $(this).attr('uid');
        if(p_uid!='')
        {
            //添加管理员列表
            server.addManager({uid:p_uid}, function(resp) 
            {
    			if (resp.code == 0) 
                {
                    
    			} 
                else 
                {
    			   dialogUi.text(resp.msg);
    			}
                 loading1.hide();
           })
                       
        }
        else
        {
           dialogUi.text('系统错误！'); 
        }
      
    });
    
    //删除
    $('.c2_3_3_2_3').live("click",function()
    {
         loading.show();
        var p_uid= $(this).attr('uid');
        if(p_uid!='')
        {
            //删除管理员列表
            server.delManager({uid:p_uid}, function(resp) 
            {
    			if (resp.code == 0) 
                {

    			} 
                else 
                {
    			   dialogUi.text(resp.msg);
    			}
                 loading.hide();
           })
                       
        }
        else
        {
           dialogUi.text('系统错误！'); 
        }
    });
    //搜索
    $('.c3_3_2_3').click(function()
    {

      var p_text=  $('.c3_3_2_2_1').val();
      if(p_text!='')
      {
         loading.show();
            //提交数据
            server.searchUser({keyWords:p_text,page:1,size:10}, function(resp) 
            {
    			if (resp.code == 0) 
                {
                    show_manager(null);
                    for (var i in resp.body.users) {
                        if (resp.body.users[i]) {
                           //showMangersForSetList(resp.body.users[i]);
                           show_manager(resp.body.users[i]) ;
                        }
                    }
    			} 
                else 
                {
    			   dialogUi.text(resp.msg);
    			}
                 loading.hide();
           })
      }
      else
      {
        load_managers();
      }
        
      
        
    });
     
    
    
    
    

    /**
     * 显示管理员到列表
     * @param manager
     */
    function show_manager(manager) {
        if (manager == null) {
            $(".c3_3_4_1").children().remove();
        } else if (manager.id) {
            var eleid = 'manager_'+manager.id;
            var meleid = 'clone_'+eleid;
            if($("#"+eleid).size() ){
                return $("#"+eleid);
            }
            if($("#"+meleid).size() ){
                return $("#"+meleid);
            }
            var manager_box = $('<div class="c3_3_4_2" id="manager_' + manager.id + '" uid="'+ manager.id +'" ><div style="background: url(\'' + manager.head_img_url + '@86w_86h_1e_0c_50Q_1x.jpg\') no-repeat center" class="c3_3_4_3"></div><div class="c3_3_4_4" title="' + manager.nick_name + '">' + manager.nick_name + '</div></div>');
            manager_box.click(add_manager);
            $('.c3_3_4_1').append(manager_box);
            for(var key in manager){
                manager_box.data(key,manager[key]);
            }
            return manager_box;
        }
    }

    /**
     * 将管理员添加到用户列表
     * @param e
     */
    function add_manager(e) {
        var that = $(this);
        if (!add_manager.ing && that.size() /*&& $(".set_box").children('.c2_3_3_2').size() < 3*/) {
            //$(this).size();
            add_manager.ing = true;
            var off = that.offset();
            var clone = $(this).clone();
            clone.data('list_id', clone.attr('id'));
            clone.attr('id', 'clone_' + clone.attr('id'));
            clone.css(off);
            clone.addClass('manager_clone');
            /*<div class="c2_3_3_2">
             <div class="c2_3_3_2_1"></div>
             <div class="c2_3_3_2_3"></div>
             <div class="c2_3_3_2_2">多好多好</div>
             <div class="clear"></div>
             </div>*/
            //alert(.html());
            var last = $(".set_box").children().last();
            var to = last.offset();
            to.left = to.left + last.width();
            $('body').append(clone);
            var member_info = that.data();
            for(var key in member_info){
                clone.data(key,member_info[key]);
            }
            clone.animate(to, add_manager.fast?0:500, 'linear', function () {
                $(this).fadeOut(0).removeClass('manager_clone').removeClass('c3_3_4_2').addClass('c2_3_3_2');
                $(".set_box").append(this);
                $(this).fadeIn(add_manager.fast?0:250);
                //.append();

                $(this).children().first().before($('<div class="c2_3_3_2_3"  uid="'+ clone.attr('uid') +'"></div>').click(remove_manager));
                $(".manager_num").html($(".set_box").children('.c2_3_3_2').size());
                add_manager.ing = false;
            });
            that.fadeOut(add_manager.fast?0:500);
        }

    }

    //是否在添加过程中
    add_manager.ing = false;
    add_manager.fast = false;

    /**
     * 删除管理员
     */
    function remove_manager() {
        var that = $(this);
        if (!remove_manager.ing && that.parent && that.parent('.c2_3_3_2').size()) {
            remove_manager.ing = true;
            //获取到包裹元素
            var parent = that.parent('.c2_3_3_2');
            //克隆包裹元素
            var parent_clone = parent.clone();
            var off = parent.offset();
            parent_clone.css(off);
            parent_clone.addClass('manager_clone');
            parent_clone.children('.c2_3_3_2_3').remove();
            $('body').append(parent_clone);

            // 获取添加前的编号
            var src_id = parent.data('list_id');
            //alert(src_id);
            var dest = $("#" + src_id);
            if (dest.size()) {
                dest.fadeTo(0, 0.1);
                var to = dest.offset();
                parent_clone.animate(to, 500);
                dest.fadeTo(500, 1, function () {
                    parent.remove();
                    parent_clone.remove();
                    remove_manager.ing = false;
                    $(".manager_num").html($(".set_box").children('.c2_3_3_2').size());
                });

            } else {
                var last = $(".c3_3_4_1").children().last();
                var to = last.offset();
                to.left = to.left + last.width();
                parent_clone.attr('id','');
                parent.attr('id','');
                parent_clone.animate(to, add_manager.fast?0:500,'linear',function(){
                    show_manager(parent.data());
                    parent.remove();
                    parent_clone.remove();
                    remove_manager.ing = false;
                    $(".manager_num").html($(".set_box").children('.c2_3_3_2').size());
                });
                //parent_clone.data();

            }


        }
    }

    remove_manager.ing = false;


});