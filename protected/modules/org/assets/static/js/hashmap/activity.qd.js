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
    var cc = require('cc');
    //console.log(cc);
    var actid = 0;
    
    var wait_dialog = dialogUi.wait();
    wait_dialog.show();

    $(function(){

        $(".c3_4_1_3_1").live('click',function(){
            var parent = $(this).parent();
            var box = $(this).parents('.c3_4_1');
            var subject_ele = $(".c3_4_1_2",box);
            subject_ele.html('<input  maxlength="10" type="text" value="'+subject_ele.text()+'"/>');
            $(this).hide(0);
            $(".c3_4_1_3_1_eidt",parent).show(0);
        });

        $(".c3_4_1_3_1_eidt").live('click',function(){
            wait_dialog.show();
            var parent = $(this).parent();
            var box = $(this).parents('.c3_4_1');
            var subject_ele = $(".c3_4_1_2",box);
            subject_ele.html( $("input",subject_ele).val() );
            box.data('subject',subject_ele.text());
            if(box.data('id') =='new'){
                server.addCode({actId:actid,subject:box.data('subject')},function(ret){
                    wait_dialog.hide();
                    if(ret.code =='0'){
                        box.remove();
                        var checkin = ret.body.checkin_codes[0];
                        checkin.act_info = ret.body.act_info;
                        addCheckIn(checkin,true);
                    }
                    dialogUi.text(ret.msg);
                    $(".c3_4_3_1").show();
                });     
            }else{
                server.modifyCode({codeId:box.data('id'),subject:box.data('subject')},function(ret){
                    wait_dialog.hide();
                    dialogUi.text(ret.msg);
                }); 
            }
            
            $(this).hide(0);
            $(".c3_4_1_3_1",parent).show(0);
        });



        //添加签到点
        $(".c3_4_3_1").click(function(){
            if($(".c3_4_1").size()>=10){
                return false;
            }
            var checkin_ele = $('<div class="c3_4_1"><a name="add"></>'+
                   '<div class="c3_4_1_1"></div>'+
                   '<div class="c3_4_1_2"><input maxlength="10" type="text" value=""/></div>'+
                   '<div class="c3_4_1_3">'+
                       '<div class="c3_4_1_3_1" style="display:none;"></div>'+
                       '<div class="c3_4_1_3_1_eidt" style="display:block;"></div>'+
                   '</div>'+
                   '<div class="c3_4_1_4">'+
                      '<div class="c3_4_1_4_1"></div>'+
                      '<div class="c3_4_1_4_2"></div>'+
                   '</div>'+
                   '<div class="c3_4_1_5"></div>'+
                '</div>');
                           
            $(".c3_4 > .clear").before(checkin_ele);
             
            checkin_ele.data('id','new');
            $("input",checkin_ele).focus();
            $(this).hide();
        });

        $("input[name='checkin_down_radio']").live('change',function(){
            var box = $(this).parents('.c3_4_1') ;
            $(".c3_4_1").removeClass('checkin_down');
            box.addClass('checkin_down');
            $(".c3_4_3_2").click();
        });

        var down_runing = false;
        //批量下载
        $(".c3_4_3_2").click(function(){
            wait_dialog.show();
            var box = $("input[name='checkin_down_radio']:checked").parents('.c3_4_1');
            if(box.size()){
                console.log(box.data());
                var checkin = box.data();
                if(checkin.id){
                    console.log(checkin.id);
                    cc.createCheckinDownImage(checkin.act_info.title,getDateByDateStr(checkin.act_info.b_time)+'-'+getDateByDateStr(checkin.act_info.e_time),'签到点'+checkin.i,checkin.subject,$(".c3_4_1_4_2 img",box).get(0),'act_'+ checkin.act_info.id+'_checkin_'+checkin.i,function(){ 
                        wait_dialog.hide();
                        $("input[name='checkin_down_radio']:checked") .attr('checked',false);
                   });
                }

            }else{
                wait_dialog.hide();
                dialogUi.text('选择要下载的签到点');
            }
            
        });


        //删除签到点
        $(".c3_4_1_5").live('click',function(){
            var box = $(this).parents('.c3_4_1') ;
            if(server.delCode && box.data('id')){
                wait_dialog.show();
                server.delCode({codeId:box.data('id')},function(ret){
                    wait_dialog.hide();
                    if(ret.code=='0'){
                        box.remove();
                        updateCheckInTitle();
                    }else{
                        dialogUi.show(ret.msg);
                    }
                });
            }
        });
         
    });



    //程序初始化
    exports.init = function(id){
        actid = id;
        if(actid && !exports.init.status){
            exports.init.status = true;
            //获取签到列表
            server.checkinCodes({actId:actid},function(ret){
                if(ret.code =='0'){
                    addCheckIn(null);
                    wait_dialog.hide();
                    var checkins = ret.body.checkin_codes;//签到点表
                    /*for (var i = checkins.length - 1; i >= 0; i--) {
                        var checkin = checkins[i];
                        checkin.act_info = ret.body.act_info;
                        addCheckIn(checkin);
                    };*/
                    for (var i = 0; i < checkins.length; i++) {
                        var checkin = checkins[i];
                        checkin.act_info = ret.body.act_info;
                        addCheckIn(checkin);
                    };
                }
                else
                {
                    dialogUi.text(ret.msg);
                }
            })
        }
    }
    exports.init.status = false;

    function addCheckIn(checkin,add){
        if(checkin == null){
            $(".c3_4 > .c3_4_1").remove();
            addCheckIn.i = 1;
        }else{
            checkin.i = addCheckIn.i;
            if(!checkin.url){
                checkin.url = checkin.qrcode_info;//签到地址
                //checkin.area = '天府广场';//签到地区名称
            }
            var checkin_ele = $('<div class="c3_4_1">'+
                   '<div class="c3_4_1_1"><span class="checkin-title">签到点'+checkin.i+'</span><input type="radio" name="checkin_down_radio" id="check_in_radio_'+checkin.id+'" value="1" /><label class="checkin_choice_label" for="check_in_radio_'+checkin.id+'">√</label></div>'+
                   '<div class="c3_4_1_2">'+checkin.subject +'</div>'+
                   '<div class="c3_4_1_3">'+
                       '<div class="c3_4_1_3_1"></div>'+
                       '<div class="c3_4_1_3_1_eidt"></div>'+
                   '</div>'+
                   '<div class="c3_4_1_4">'+
                      '<div class="c3_4_1_4_1"></div>'+
                      '<div class="c3_4_1_4_2"></div>'+
                   '</div>'+
                   '<div class="c3_4_1_5"></div>'+
                '</div>'); 
            $(".c3_4 > .clear").before(checkin_ele);
            for(var key in checkin){
                checkin_ele.data(key,checkin[key]);
            }
            if($(".c3_4_1_4_2",checkin_ele).size())
            //生成二维码
            makeQrCode($(".c3_4_1_4_2",checkin_ele).get(0),checkin.url,{width:995,height:995});
            addCheckIn.i++;

        }
        
    }
    addCheckIn.i = 1;



    /**
     * 二维码生成的方法
     */
    function makeQrCode(ele,content,options){
          var s = $.extend({},{width:280,height:280},options||{});
          var qrcode = new QRCode(ele,s);
          qrcode.makeCode(content);
          return ;
    }
    function getDateByDateStr(date_str){
        var date = new Date(date_str);
        /*console.log(date);*/
        return date.getFullYear()+'.'+(date.getMonth()+1)+'.'+date.getDate();

    }
    function updateCheckInTitle(){
        addCheckIn.i = 1;
        $(".c3_4_1").each(function(){
            $(this).data('i',addCheckIn.i );
            $(".checkin-title",this).text('签到点'+(addCheckIn.i ));
            addCheckIn.i ++;
        });
    }

    exports.makeQrCode = makeQrCode;
});