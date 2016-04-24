/**
 * 活动分组页面
 * @author      吴华
 * @company     成都哈希曼普科技有限公司
 * @site        http://www.hashmap.cn
 * @email       wuhua@hashmap.cn
 * @date        2015-04-30
 */
define(function (require, exports, module){ 
	var $ = require('$');
    var dialogUi = require('dialogUi');
    var server = require('server');
    var actid = 0;
    exports.inited = false;
    exports.members = [];
    exports.groups = [];
    exports.groups_num = 0;
    var groupIds = [];

    var wait_dialog = dialogUi.wait();
    
    var move_ren = {
        down: false//鼠标按下状态
        ,ele: null //要移动的元素
        ,ele_offset: {top:0,left:0} //移动元素的位置
        ,off:{x:0,y:0}  //元素与鼠标的偏移位置
        ,ele_clone: null //移动元素的拷贝
    };
 



    $(function(){
        $(".c3_4_5_3").click(function(){
            if(exports.inited && exports.members.length){
                var size = parseInt($(".c3_4_5_2 input").val());
                if(size>0){
                    var mem_num = $(".c3_4_3 .c3_4_3_1").size()
                    exports.groups_num = Math.ceil( mem_num / size );
                    //alert(exports.groups_num);
                    var j = 0;
                    var member = {};
                    var member_ele = {};
                    $(".c3_4_7>.c3_4_7_1").remove();
                    exports.groups = [];

                    for (var i = 0; i < exports.groups_num; i++) {
                        for (var k = 0 ; k  < size; k ++) {
                            member = {};
                            member_ele = $(".c3_4_3 .c3_4_3_1").eq(j);
                            // console.log(member_ele.size());
                            if(member_ele.size() && member_ele.data){
                                member = member_ele.data();
                                console.log(member);
                                console.log(member && member.id);
                                if(member && member.id){
                                    member.group_id = (i+1);
                                    member.group_name = '第'+member.group_id+'组';
                                    add_member_group(member);
                                }  
                            }  
                            j++;
                        };
                    };
                    console.log(exports.groups);
                    save();
                }else{
                    dialogUi.text('请填写一个数字');
                }
            }else{
                dialogUi.text('程序初始化未完成.请稍候');
            }
        }); 

        $(".zu_post").live('click',function(){
            var zu_mane = $(".zu_mane" , $(this).parent());
            zu_mane.html('<input type="text" value="'+zu_mane.text()+'">');
            $(this).hide(0);
            $(".zu_edit", $(this).parent()).show(0);
        }); 

        $(".zu_edit").live('click',function(){
            var id = $(this).parents('.c3_4_7_1').data('groupid');
            var zu_mane = $(".zu_mane" , $(this).parent());
            zu_mane.html( $("input",zu_mane).val() );
            if(exports.groups && exports.groups[id] ){
                exports.groups[id].groupName = zu_mane.html();
            }
            $(this).hide(0);
            $(".zu_post", $(this).parent()).show(0);
            save();
        });  

        $(document).mousemove(function(e){
            if(move_ren.down){
                console.log(move_ren);
                var p = getMousePosition(e);
                var loc = {left:0,top:0};
                loc.left = p.x - move_ren.off.x;
                loc.top = p.y - move_ren.off.y;
                move_ren.ele_clone.css(loc);
                loc= null;
                p = null;
                var drop_box = get_drop_box();
                $(".c3_4_7_1").not(drop_box).removeClass('drop_box');
                if(drop_box){
                    drop_box.addClass('drop_box');
                }

            }
        }).mouseup(_mouse_blur) ;
        $(".zu_ren_move_mask").live('mouseup',_mouse_blur);
        function _mouse_blur(e){
            //console.log(e);
            if(move_ren.down){
                move_ren.down = false;
                move_ren.ele_clone.remove();
                $(".zu_ren_move_mask").hide();
                var src_box = move_ren.ele.parents('.c3_4_7_1');
                var src_id = src_box.data('groupid');
                var now_box = $(".drop_box");
                var now_id = now_box.data('groupid');
                var userid = move_ren.ele.data('id');
                if(src_id != now_id  && now_id){
                    $(".drop_box .c3_4_7_3 .clear").before(move_ren.ele); 
                    $(".c3_4_7_1 ").removeClass('drop_box'); 
                    console.log(exports.groups);
                    console.log(src_id);
                    //清除原组编号
                    for(var i in exports.groups[src_id].userIds){
                        if(exports.groups[src_id].userIds[i] == userid)
                            exports.groups[src_id].userIds[i] = null;
                    }
                    //添加到新分组
                    exports.groups[now_id].userIds.push(userid);
                    save();
                }

                
            }
        }

        $(".zu_ren").live('mousedown',function(e){
            if(!move_ren.down && exports.inited){
                move_ren.down = true;
                move_ren.ele = $(this);
                move_ren.ele_offset = move_ren.ele.offset();
                var p = getMousePosition(e);
                move_ren.off.x = p.x - move_ren.ele_offset.left; 
                move_ren.off.y = p.y - move_ren.ele_offset.top; 
                move_ren.ele_clone = move_ren.ele.clone();
                $("body").append(move_ren.ele_clone);
                move_ren.ele_clone.addClass('zu_ren_clone');
                move_ren.ele_clone.css(move_ren.ele_offset);
                $(".zu_ren_move_mask").show();
            }   
        }).mouseup(_mouse_blur);

        function get_drop_box(){
            get_drop_box.box = null;
            if(move_ren.down){ 
                var offset = move_ren.ele_clone.offset();
                $(".c3_4_7_1").each(function(e){
                    var box_off = $(this).offset();
                    var box_size = {w:$(this).width(),h:$(this).height()};
                    var area = {
                        lt:{x:box_off.left,y:box_off.top}
                        ,rd:{x:box_off.left+box_size.w,y:box_off.top+box_size.h}
                    }
                    //console.log(offset);
                    if(
                        offset.left>area.lt.x && offset.left<area.rd.x
                        && offset.top>area.lt.y && offset.top<area.rd.y
                        ){
                        get_drop_box.box = $(this);
                    }
                });
            }
            return get_drop_box.box;
        }
        get_drop_box.box = null;
 
    });


    function save(){

        wait_dialog.show();

        exports.groups.actId = actid;
        //;
        //生成表单
        var groups_form = $("#groups_form");
        groups_form.html('');
        var group_id_exists = false;
        for(var key in exports.groups){
            //alert(key);
            if(key == 'actId'){
                groups_form.append( save.create_from_item('actId',exports.groups.actId) );        
            }else if((/^[1-9][0-9]*$/).test(key+"")){
                var group = exports.groups[key];
                for(var key_g in group){
                    if (key_g == 'userIds') {
                        for(var id_i in group.userIds){
                            //if(group.userIds[id_i]!=null)
                            groups_form.append( save.create_from_item('groups['+key+']['+key_g+'][]',group.userIds[id_i]) );
                        }    
                    }else if(key_g =='groupId'){
                        //判断当前id时候存在
                        group_id_exists = $.inArray(group[key_g],groupIds);
                        //alert(group_id_exists);
                        //console.log(groupIds);
                        if(group_id_exists >= 0){
                            groups_form.append( save.create_from_item('groups['+key+']['+key_g+']',group[key_g]) );
                        }else{
                            groups_form.append( save.create_from_item('groups['+key+']['+key_g+']',''));
                        }
                        group_id_exists = false;

                    }else{
                         groups_form.append( save.create_from_item('groups['+key+']['+key_g+']',group[key_g]) );
                    }
                }        
            }
        }
        //return false;
        //alert(groups_form.serialize());

        server.modifyGroup(groups_form.serialize(),function(ret){

            wait_dialog.hide(); 
            //alert('modify');
            if(ret.code =='0'){
                //alert('modify');
                //dialogUi.text(ret.msg);
                exports.inited  = false;
                exports.init(actid);
            }else{
               dialogUi.text(ret.msg); 
            }

            
        });
    }

    save.create_from_item = function(name,value){
        return $('<input type="hidden" name="'+name+'" value="'+value+'">');
    }

    function getMousePosition(e){
        var point = {x:0,y:0,sx:0,sy:0};
        if(e.pageX)
            point.x = e.pageX;
        if(e.pageY)
            point.y = e.pageY;
        if(e.screenX)
            point.sx = e.screenX;
        if(e.screenY)
            point.sy = e.screenY;
        return point;
    }

    /**
     * 程序初始化
     */
    exports.init = function(id){
        wait_dialog.show();

    	actid = parseInt(id?id:0);
    	//获取到活动会员信息
    	//alert(actid);

        //alert('init');
    	if(actid && !exports.inited){
            
    		//获取到用户信息
    		server.members({actId:actid},function(ret){
                wait_dialog.hide();
                //修改初始化状态
                exports.inited = true;
                groupIds = [];
                $(".c3_4_7>.c3_4_7_1,.c3_4_3>.c3_4_3_1").remove();
                $(".c3_4_7>.c3_4_7_1").remove();
    			if(ret.code == '0'  && ret.body.users){
    				//alert(ret.body.users);
                    $(".c3_4_1 span").text(ret.body.total_num);
    				exports.members = ret.body.users;
                    for (var i = exports.members.length - 1; i >= 0; i--) {
                        if(exports.members[i]  &&  exports.members[i].id){
                            add_member_chengyuan(exports.members[i]);
                            add_member_group(exports.members[i]);

                            if(exports.members[i].group_id){
                                groupIds.push(exports.members[i].group_id);
                            }
                        }
                    };
    			}else{
    				dialogUi.text(ret.msg);
    			}
    		});		
 
    	}

    };




    

    function add_member_group(member){
        if(member.id && member.group_id){
            //根据分组编号创建对象
            var group_box = create_group_box(member.group_id,member.group_name?member.group_name:'第'+member.group_id+'组');
            //alert(group_box);
            //创建元素
            if(group_box){
                var member_ele = $('<div class="zu_ren"><div class="zu_ren_heard" style="background-image: url(\''+member.head_img_url+'@83w_83h_1e_0c_50Q_1x.jpg\')"></div><div class="zu_ren_name">'+member.nick_name+'</div></div>');
                    var group = exports.groups[member.group_id];
                    if(!group){
                        group = {};
                        group.groupId = member.group_id;
                        group.groupName = member.group_name?member.group_name:'第'+member.group_id+'组';
                        group.userIds= [member.id];
                    }else{
                        group.userIds.push(member.id);
                    }
                    exports.groups[member.group_id] = group;

                    $(".c3_4_7_3 .clear",group_box).before(member_ele);
                    for(var key in member){
                        member_ele.data(key,member[key]);
                    }
            }
            return true;
        }else{
            return false;
        }
    }


    /**
     * 创建分组元素框
     */
    function create_group_box(id,group_name){
        var _id = 'group_box_'+id;
        var group_box = $('#'+_id);
        if(!group_box.size()){
            group_box = $('<div id="'+_id+'" data-groupid="'+id+'" class="c3_4_7_1"><div class="c3_4_7_2"><div class="zu_mane">'+group_name+'</div><div class="zu_edit"></div><div class="zu_post"></div></div><div class="c3_4_7_3"><div class="clear"></div></div></div>');
            $(".c3_4_7>.clear").before(group_box);
            group_box = $('#'+_id);
        }
        return group_box;
    }



    /**
     * 添加用户到成员列表
     */
    function add_member_chengyuan(member){
        if(member.id){
            var member_ele = $('<div class="c3_4_3_1" style="background-image: url(\''+member.head_img_url+'@83w_83h_1e_0c_50Q_1x.jpg\')"></div>');

            $(".c3_4_3 .clear").before(member_ele);
            for(var key in member){
                member_ele.data(key,member[key]);
            }
        }else{
            return false;
        }
        
    
    }
});