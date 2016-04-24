/**
 * 活动发布页面
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
    var geoc = null;
    var currentPoint = null;
    var map1 = null;
    var wait_dialog = dialogUi.wait();
    wait_dialog.show();
    var geolocation = null;
    //当前活动ID
    var act_id = 0;
    //活动名称
    var p_title = '';
    //活动开始时间
    var a_time = '';
    var map2 = null;
    var loader = require('loader').setConf({'base':basePath+'/'});  
             
    /*初始化报名时间为当前时间*/  
    var nowtime = new Date();
    var now_str = nowtime.getFullYear()+"-"+(nowtime.getMonth()+1)+"-"+nowtime.getDate();
      
         if(now_str!=null)
         {
             var now_str_array= now_str.split('-'); 
             if(now_str_array[0]<10)
             {
                now_str_array[0]='0'+now_str_array[0];
             }
             if(now_str_array[1]<10)
             {
                now_str_array[1]='0'+now_str_array[1];
             }
             if(now_str_array[2]<10)
             {
                now_str_array[2]='0'+now_str_array[2];
             }
              //报名开始时间
            $('#p_enrollBeginTime_l').children().each(function()
            {
                if($(this).val()==now_str_array[0])
                {
                     $(this).attr('selected','true');
                }
            })
            $('#p_enrollBeginTime_y').children().each(function()
            {
                if($(this).val()==now_str_array[1])
                {
                     $(this).attr('selected','true');
                }
            })
            $('#p_enrollBeginTime_r').children().each(function()
            {
                if($(this).val()==now_str_array[2])
                {
                     $(this).attr('selected','true');
                }
            })
            
            //报名结束
            $('#p_enrollEndTime_l').children().each(function()
            {
                if($(this).val()==now_str_array[0])
                {
                     $(this).attr('selected','true');
                }
            })
            $('#p_enrollEndTime_y').children().each(function()
            {
                if($(this).val()==now_str_array[1])
                {
                     $(this).attr('selected','true');
                }
            })
         }
                 

    
             
             
             
                  
    /* 初始化方法 */
    function init(callback){
        var that = this;
        //所有初始数据在这里获取...
        act_id=aid;
        //调用接口获取数据
        server.detail({actId:act_id},function(data)
        {
            if(data.code=='0')
            {
                //标题
                 p_title=data.body.act.title;
                 $('#title').val(p_title);
                //第一步初始化需要数据
                init.data.step1 = {
                     lon: data.body.act.lon//'104.068729'  
                     ,lat: data.body.act.lat//'30.681154'
                     ,addr_city: data.body.act.addr_city//'成都市' 
                     ,addr_area:data.body.act.addr_area//'青羊区'    
                     ,addr_road:data.body.act.addr_road//'江汉路'    
                     ,addr_num:data.body.act.addr_num//'228号'    
                     ,addr_name:data.body.act.addr_name//'青羊区政府'    
                };
                //时间
                //分割时间--活动开始
                 var ok_Time= data.body.act.b_time;
                 if(ok_Time!=null)
                 {
                     var ok_Time_arrays= ok_Time.split(' '); 
                     if(ok_Time_arrays[0]!='')
                     {
                        ok_Time_array=ok_Time_arrays[0].split('-');
                        ok_Time_array1=ok_Time_arrays[1].split(':');
                     }
                      //活动开始时间
                    $('#bTime_l').children().each(function()
                    {
                        if($(this).val()==ok_Time_array[0])
                        {
                             $(this).attr('selected','true');
                        }
                    })
                    $('#bTime_y').children().each(function()
                    {
                        if($(this).val()==ok_Time_array[1])
                        {
                             $(this).attr('selected','true');
                        }
                    })
                    $('#bTime_r').children().each(function()
                    {
                        if($(this).val()==ok_Time_array[2])
                        {
                             $(this).attr('selected','true');
                        }
                    })
                    //处理 时 分
                    $('#bTime_s').children().each(function()
                    {
                        if($(this).val()==ok_Time_array1[0])
                        {
                             $(this).attr('selected','true');
                        }
                    })
                    $('#bTime_f').children().each(function()
                    {
                        if($(this).val()==ok_Time_array1[1])
                        {
                             $(this).attr('selected','true');
                        }
                    })
                    
                    
                    
                 }

                 //分割时间--活动end
                 var end_time= data.body.act.e_time;
                 if(end_time!=null)
                 {
                     var end_time_arrays= end_time.split(' '); 
                     if(end_time_arrays[0]!='')
                     {
                        end_time_array=end_time_arrays[0].split('-');
                        end_time_array1=end_time_arrays[1].split(':');
                     }
    
                    //报名结束
                    $('#eTime_l').children().each(function()
                    {
                        if($(this).val()==end_time_array[0])
                        {
                             $(this).attr('selected','true');
                        }
                    })
                    $('#eTime_y').children().each(function()
                    {
                        if($(this).val()==end_time_array[1])
                        {
                             $(this).attr('selected','true');
                        }
                    })
                    $('#eTime_r').children().each(function()
                    {
                        if($(this).val()==end_time_array[2])
                        {
                             $(this).attr('selected','true');
                        }
                    })
                    //处理 时 分
                    $('#eTime_s').children().each(function()
                    {
                        if($(this).val()==end_time_array1[0])
                        {
                             $(this).attr('selected','true');
                        }
                    })
                    $('#eTime_f').children().each(function()
                    {
                        if($(this).val()==end_time_array1[1])
                        {
                             $(this).attr('selected','true');
                        }
                    })
                    
                    
                 }
                
               
                //第二步初始化数据
                //活动详情
                // $('#p_detail').html(data.body.act.detail);
                $('#p_detail').val(data.body.act.detail);
                /*ue.setContent(data.body.act.detail);*/
                init.data.step2 = {
                    zuobiao:data.body.act.route_maps[0]?data.body.act.route_maps[0].act_route_points:[]/*[
                        {lng:'104.000586',lat:'30.669444'} //第一个点
                        ,{lng:'104.008923',lat:'30.659752'} //第二个点
                        
                    ]*/
                    ,imgs:data.body.act.imgs
                    ,thumb:data.body.act.h_img_id
                };
                init.data.step2.imgs.unshift({id:data.body.act.h_img_id,img_url:data.body.act.h_img_url});

   
                //第三步初始化数据
                    //活动开始时间
                     a_time= data.body.act.b_time;
                    //分割时间--报名开始
                 var b_time= data.body.act.enroll_b_time;
                 if(b_time!=null)
                 {
                     var b_time_arrays= b_time.split(' '); 
                     if(b_time_arrays[0]!='')
                     {
                        b_time_array=b_time_arrays[0].split('-');
                        b_time_array1=b_time_arrays[1].split(':');
                     }
                      //报名开始时间
                    $('#p_enrollBeginTime_l').attr("disabled",'true');
                    $('#p_enrollBeginTime_l').children().each(function()
                    {
                        if($(this).val()==b_time_array[0])
                        {
                             $(this).attr('selected','true');
                        }
                    })
                    $('#p_enrollBeginTime_y').attr("disabled",'true');
                    $('#p_enrollBeginTime_y').children().each(function()
                    {
                        if($(this).val()==b_time_array[1])
                        {
                             $(this).attr('selected','true');
                        }
                    })
                    $('#p_enrollBeginTime_r').attr("disabled",'true');
                    $('#p_enrollBeginTime_r').children().each(function()
                    {
                        if($(this).val()==b_time_array[2])
                        {
                             $(this).attr('selected','true');
                        }
                    })
                    //处理 时 分
                    $('#p_enrollBeginTime_s').attr("disabled",'true');
                    $('#p_enrollBeginTime_s').children().each(function()
                    {
                        if($(this).val()==b_time_array1[0])
                        {
                             $(this).attr('selected','true');
                        }
                    })
                    $('#p_enrollBeginTime_f').attr("disabled",'true');
                    $('#p_enrollBeginTime_f').children().each(function()
                    {
                        if($(this).val()==b_time_array1[1])
                        {
                             $(this).attr('selected','true');
                        }
                    })
                    
                    
                    
                    
                 }

                 
                 
                 
                 //分割时间--报名end
                 var n_time= data.body.act.enroll_e_time;
                 if(n_time!=null)
                 {
                     var n_time_arrays= n_time.split(' '); 
                     if(n_time_arrays[0]!='')
                     {
                        n_time_array=n_time_arrays[0].split('-');
                        n_time_array1=n_time_arrays[1].split(':');
                     }
    
                    //报名结束
                    $('#p_enrollEndTime_l').attr("disabled",'true');
                    $('#p_enrollEndTime_l').children().each(function()
                    {
                        if($(this).val()==n_time_array[0])
                        {
                             $(this).attr('selected','true');
                        }
                    })
                    $('#p_enrollEndTime_y').attr("disabled",'true');
                    $('#p_enrollEndTime_y').children().each(function()
                    {
                        if($(this).val()==n_time_array[1])
                        {
                             $(this).attr('selected','true');
                        }
                    })
                    $('#p_enrollEndTime_r').attr("disabled",'true');
                    $('#p_enrollEndTime_r').children().each(function()
                    {
                        if($(this).val()==n_time_array[2])
                        {
                             $(this).attr('selected','true');
                        }
                    })
                    //处理 时 分
                    $('#p_enrollEndTime_s').attr("disabled",'true');
                    $('#p_enrollEndTime_s').children().each(function()
                    {
                        if($(this).val()==n_time_array1[0])
                        {
                             $(this).attr('selected','true');
                        }
                    })
                    $('#p_enrollEndTime_f').attr("disabled",'true');
                    $('#p_enrollEndTime_f').children().each(function()
                    {
                        if($(this).val()==n_time_array1[1])
                        {
                             $(this).attr('selected','true');
                        }
                    })
                 }
                 
                
                //是否报名人数限制：0否，1是
                if(data.body.act.enroll_limit!=-1)
                {
                    if(data.body.act.enroll_limit==0 )
                    {
                        $('.p_enrollLimit').attr("checked",'true');
                        $('.p_enrollLimit').attr("disabled",'true');
                        $('#p_enrollLimitNum').val(0);
                    }
                    else
                    {
                        $('.p_enrollLimit').removeAttr("checked");
                        $('.p_enrollLimit').attr("disabled",'true');
                        $('#p_enrollLimitNum').val(data.body.act.enroll_limit_num);
                        $('#p_enrollLimitNum').attr("disabled",'true');
                 
                    }
                }
                //是否需要支付费用：0否，1是(免费可以修改审核状态)
                if(data.body.act.show_pay!=-1)
                {
                    if(data.body.act.show_pay==0)
                    {
                        $('#showPay').attr("checked",'true');
                        //是否需要报名审核：0否，1是
                        if(data.body.act.show_verify==0)
                        {
                            $('#showVerify').attr("checked",'true');
                        }
                        else
                        {
                            $('#showVerify').removeAttr("checked");
                            $('#showVerify1').attr("checked",'true');
                        }
                    }
                    else
                    {
                        //收费不用审核
                        $('#showPay').removeAttr("checked");
                        $('#showPay1').attr("checked",'true');
                        $('#showPay1').attr("disabled",'true');
                        $('#showPay').attr("disabled",'true');
                        //费用
                        $('#p_totalFee').val(data.body.act.total_fee);
                        $('#p_totalFee').attr("disabled",'true');
                        //是否需要报名审核-不用审核
                        $('#showVerify').attr("checked",'true');
                        $('#showVerify').attr("disabled",'true');
                        $('#showVerify1').attr("disabled",'true');
                    }
                }

                
                //自定义
                if(data.body.act.custom_fields)
                {
                      if(data.body.act.custom_fields.length>0)
                      {
                        
                        /*var date='';
                        for(var aa=0; aa<data.body.act.custom_fields.length;aa++)
                        {
                            if(aa==0)
                            {
                                date=data.body.act.custom_fields[aa];
                            }
                            else
                            {
                                date+=','+data.body.act.custom_fields[aa];
                            }
                        }
                        alert(date);
                        */
                        init.data.step3 = { fields: data.body.act.custom_fields };
                      }
                      else
                      {
                        init.data.step3 = {fields:['手机','姓名']};
                      }
                }
                
              init.data.step3 = {fields:['手机','姓名']};
    
    
                //数据获取完成调用回调方法
                if($.isFunction(callback)){
                    callback.call(that);
                }
                //去到第二步
                 slider_step_to(1, function () {
                    //去到第二步
                    setp_log_to(1);
                    //初始化第二步
                    step2_init();
                    step3_init();
                });
             }
             else
             {
                dialogUi.text('获取数据失败！');   
             }
        }) ;   

        
    }
    init.status = aid?true:false;//true;//是否有初始化数据
    init.data = {step1:{},step2:{},step3:{}};


    /*程序开始运行*/
    function run(){
        //alert("ASDFSADF");
        // 百度地图API功能
        map1 = new BMap.Map("step1_map");
        map1.centerAndZoom("成都", 15);
        map1.enableScrollWheelZoom(true);
        map1.enableDragging();

        geoc = new BMap.Geocoder();


        //程序运行后开始定位
        geolocation = new BMap.Geolocation();
        if(!init.status){
            $("#step1_ret").html("开始定位");
            var lng = 104.06792346;
            var lat = 30.67994285;
            geolocation.getCurrentPosition(function (r) {
                //您的位置：104.06792346,30.67994285
                //成都的位置
                if (this.getStatus() == BMAP_STATUS_SUCCESS) {
                    /*
                     var mk = new BMap.Marker(r.point);
                     map1.addOverlay(mk);
                     map1.panTo(r.point);
                     alert('您的位置：' + r.point.lng + ',' + r.point.lat);
                     */
                    lng = r.point.lng;
                    lat = r.point.lat;
                }
                $("#step1_ret").html("定位完成");
                step1_map_init(lng, lat);
                currentPoint = new BMap.Point(lng, lat);
                //$(".c3_12_1").click();
            }, {enableHighAccuracy: true});
        }else{
            step1_map_init(init.data.step1.lon, init.data.step1.lat);
            currentPoint = new BMap.Point(init.data.step1.lon, init.data.step1.lat);
        }
        
        wait_dialog.hide();
        //step1_map_init(lng, lat);
    }




    $(function () {
        
        //编辑器
        /*
        loader.script('static/nicEditor/nicConfig.js',function(){
            loader.script('static/nicEditor/bkLib.js',function(){
                loader.script('static/nicEditor/nicEdit.js',function(){
                    new nicEditor({fullPanel : true}).panelInstance('p_detail',{hasPanel : true});
                    //获取到初始化数据
                    if (init.status) {
                        init.call(this,run); //初始化程序并运行
                    }else{
                        run.call(this); //运行程序
                        //alert("ASDFSFD");
                    }
                });
            });
        });
       */
                    //获取到初始化数据
                    if (init.status) {
                        init.call(this,run); //初始化程序并运行
                    }else{
                        run.call(this); //运行程序
                        //alert("ASDFSFD");
                    }

        


        $(".step_to").click(function(){
            var that = $(this);
            var to = that.data('to');
           /* if(to == 0){
                slider_step_to(to, function () { 
                    setp_log_to(to); 
                });
            }else */if(to == 1 && step2_init.status){
                slider_step_to(to, function () { 
                    setp_log_to(to); 
                    //step2_init();
                });
            }else if(to == 2 && step3_init.status){
                slider_step_to(to, function () { 
                    setp_log_to(to); 
                    //step3_init();
                });
            }
        });
        
    });

    


    /**
     * 当选择了某个点后回回调这个方法
     * @param state 选中状态
     * @param point {lng,lat}  选中元素坐标
     * @param title  选中元素标题
     * @param addr 选中元素地址
     * @param city 选中元素城市
     */
    function choice_poi(state, point, title, addr, city) {


        var lon = $("#lon");
        var lat = $("#lat");
        var addr_city = $("#addr_city");
        var addr_area = $("#addr_area");
        var addr_road = $("#addr_road");
        var addr_num = $("#addr_num");
        var addr_name = $("#addr_name");
        //清空以前的值
        lon.val('');
        lat.val('');
        addr_city.val('');
        addr_area.val('');
        addr_road.val('');
        addr_num.val('');
        addr_name.val('');

        map1.panTo(point);
        map1.clearOverlays();
        map1.addOverlay(new BMap.Marker(point));
        geoc.getLocation(point, function (rs) {
            /*
             var addCom = rs.addressComponents;
             setp1_add_ret({
             point: rs.point// 坐标点
             ,
             title: rs.address//标题
             ,
             address: addCom.province + addCom.city + addCom.district + addCom.street + addCom.streetNumber  //详细地址
             , province: addCom.province//省份
             , city: addCom.city//城市
             });*/

            var addCom = rs.addressComponents;
            lon.val(rs.point.lng);
            lat.val(rs.point.lat);
            addr_city.val(addCom.city);
            addr_area.val(addCom.district);
            addr_road.val(addCom.street);
            addr_num.val(addCom.streetNumber);
            addr_name.val(title);
        });
    }

    /**
     * 添加返回点列表
     * @param poi
     */
    function setp1_add_ret(poi) {
        if (poi == null) {
            setp1_add_ret.index = 1;
            $("#step1_ret").html("");
            map1.clearOverlays();
        } else if (poi.point) {
            //alert(checked);
            var poi_box = document.createElement('div');
            var poi_index = document.createElement('div');
            var poi_title = document.createElement('div')
            var poi_addr = document.createElement('div');
            var poi_check = document.createElement('input');

            poi_box.className = 'poi_box';
            poi_box.setAttribute('data-lng', poi.point.lng);
            poi_box.setAttribute('data-lat', poi.point.lat);
            poi_box.setAttribute('data-city', poi.city);
            poi_box.setAttribute('data-province', poi.province);


            poi_index.innerHTML = setp1_add_ret.index;
            poi_index.className = 'poi_index';

            poi_title.innerHTML = poi.title;
            poi_title.className = 'poi_title';


            poi_addr.innerHTML = poi.address;
            poi_addr.className = 'poi_addr';

            poi_check.className = 'poi_check';
            poi_check.type = "radio";
            poi_check.name = 'poi';
            poi_check.value = '1';
            jQuery(poi_check).change(function () {
                    
                    $(".poi_box").removeClass('checked');
                    jQuery(this).parents('.poi_box').addClass('checked');
                    if (jQuery.isFunction(choice_poi)) {
                        //alert();
                        choice_poi.call(this, this.checked, poi.point, poi.title, poi.address, poi.city);
                    }
                }
            ).click(function (e) {
                    //e.canelB
                    //e.cancelBubble = false;
                   // event.stopPropagation();
                });
            jQuery(poi_box).click(function (e) {
                poi_check.click(e);
            });
            ;

            poi_box.appendChild(poi_check);
            poi_box.appendChild(poi_index);
            poi_box.appendChild(poi_title);
            poi_box.appendChild(poi_addr);
            $("#step1_ret").append(poi_box);
            poi_box = null;
            poi_index = null;
            poi_addr = null;
            //poi_check = null;
            poi_title = null;
            if(setp1_add_ret.index==1){
                poi_check.click();
            }
            setp1_add_ret.index++;


        }
    }


    /**
     * 第一步地图初始化
     * @param lng
     * @param lat
     */
    function step1_map_init(lng, lat) {
        if(!step1_map_init.status){
            step1_map_init.status = true;
            //本地搜索
            var options = {
                renderOptions: {
                    //map: map1
                },
                onSearchComplete: function (results) {
                    // 判断状态是否正确
                    if (local.getStatus() == BMAP_STATUS_SUCCESS) {
                        setp1_add_ret(null);
                        for (var i = 0; i < results.getCurrentNumPois(); i++) {
                            setp1_add_ret(results.getPoi(i));
                        }
                    } else {
                        $("#step1_ret").html("没有数据");
                    }
                }
            };
            var local = new BMap.LocalSearch(map1, options);
            var timer = 0;
            //第一步 地图
            map1.panTo(new BMap.Point(lng, lat));
            /**
             * 显示周边信息
             * @param e
             */
            function showInfo(e) {
                /***/
                $("#step1_ret").html("正在加载数据...");
                clearTimeout(timer);
                timer = setTimeout(function () {
                    var poiRadius = 500;
                    var mOption = {
                        poiRadius: poiRadius,           //半径为1000米内的POI,默认100米
                        numPois: 50               //列举出50个POI,默认10个
                    };

                    map1.addOverlay(new BMap.Circle(e.point, poiRadius, {
                        strokeColor: 'rgba(0,0,0,0)',
                        fillColor: 'rgba(0,0,0,0)',
                        strokeWeight: 0,
                        strokeOpacity: 0,
                        fillOpacity: 0
                    }));        //添加一个圆形覆盖物
                    geoc.getLocation(e.point,
                        function (rs) {
                            // alert(rs);
                            //map1.clearOverlays();
                            setp1_add_ret(null);
                            var addCom = rs.addressComponents;

                            /**
                             * 
                             */    
                            if(init.status){
                                //判断程序是否有初始化数据.如果有 
                                setp1_add_ret({
                                    point:  currentPoint// 坐标点
                                    ,
                                    title: init.data.step1.addr_name//标题
                                    ,
                                    address: addCom.province + addCom.city + addCom.district + addCom.street + addCom.streetNumber  //详细地址
                                    , province: addCom.province//省份
                                    , city: addCom.city//城市
                                });       
                            }else{
                                setp1_add_ret({
                                    point: rs.point// 坐标点
                                    ,
                                    title: rs.address//标题
                                    ,
                                    address: addCom.province + addCom.city + addCom.district + addCom.street + addCom.streetNumber  //详细地址
                                    , province: addCom.province//省份
                                    , city: addCom.city//城市
                                });
                            }    
                            

                            map1.addOverlay(new BMap.Marker(e.point));
                            map1.panTo(e.point);
                            var allPois = rs.surroundingPois;       //获取全部POI（该点半径为100米内有6个POI点）
                            for (i = 0; i < allPois.length; ++i) {
                                setp1_add_ret(allPois[i]);
                            }
                            


                        }, mOption
                    );

                }, 250);
            }

            showInfo({point: map1.getCenter()});
            map1.addEventListener("click", showInfo);

            /**
             * 搜索的监听器
             * @returns {boolean}
             */
            var so_lisenter = function () {
                $("#step1_ret").html("正在加载数据...");
                clearTimeout(so_lisenter.timer);
                if (!$(this).val()) {
                    return true;
                }
                var _this = this;
                so_lisenter.timer = setTimeout(function () {
                    local.search($(_this).val());
                }, 250);
            };
            so_lisenter.timer = 0;
            $(".c3_9_1").keyup(so_lisenter).keydown(so_lisenter);
        }
    }
    step1_map_init.status = false;
    //地图初始化完成


    /**
     * 切换输入框
     * @param i
     * @param callback
     */
    function slider_step_to(i, callback) {
        $("#step_slider_box").animate({'margin-left': '-' + (i * 1049) + 'px'}, 500, null, function () {
            if (jQuery.isFunction(callback)) {
                callback.call(this);
            }
        });
    }

    /**
     * 切换指示图片
     * @param i
     */
    function setp_log_to(i) {
        var root = $("#step_img");
        root.data('now',i);
        var lv1 = $("#step_img>div");
        var lv2 = $("#step_img>div>div");
        if (i == 0) {
            root.attr('class', 'c2_1');
            lv1.attr('class', 'c2_2');
            lv2.attr('class', 'c2_3');
        } else if (i == 1) {
            root.attr('class', 'twoc2_1');
            lv1.attr('class', 'twoc2_2');
            lv2.attr('class', 'twoc2_3');
        } else if (i == 2) {
            root.attr('class', 'threec2_1');
            lv1.attr('class', 'threec2_2');
            lv2.attr('class', 'threec2_3');

        }
    }


    //-------------- 第二步 代码------------------------------------------------
    /**
     * 第二步初始化
     */
    function step2_init() {
        if(!step2_init.status){
            step2_init.status = true;

            var hm = {
                markers: []  //所有点的集合
                , polyline: null //绘制的路径
                , id: 1 //marker的自增编号
                , points: []  //markers的点列表
                , start_label: new BMap.Label('起点',{offset:new BMap.Size(-5,-20)}) //起点标志
                , end_label: new BMap.Label('终点',{offset:new BMap.Size(-5,-20)}) //终点标志
                ,start: null  //起点
                ,end: null //终点
                /**
                 * 添加一个marker
                 * @param point
                 */, addMarker: function (point) { //添加一个点
                    var marker = new BMap.Marker(point);
                    marker.enableDragging();//启用拖拽功能
                    map2.addOverlay(marker);
                    this.markers.push(marker);
                    this.draw();//绘制线路
                    var _this = this;
                    marker.addEventListener('dragging', function () {
                        _this.draggingMark();
                    });
                    marker.addEventListener('dragend', function () {
                        _this.draggingMark();
                    });
                }
                /**
                 * 删除最后一个点
                 */, removeMarker: function () {
                    if (this.markers.length > 0) {
                        var marker = this.markers.pop();
                        map2.removeOverlay(marker);
                        this.draw();//绘制线路
                    }
                }
                /**
                 * 拖拽点的时候调用
                 */, draggingMark: function () {
                    this.draw();
                }
                /**
                 * 绘制路径线条
                 */, draw: function () {
                    this.getPoints();
                    //alert(this.points);
                    if (!this.polyline) {
                        this.polyline = new BMap.Polyline(this.points);
                        map2.addOverlay(this.polyline);
                    } else {
                        this.polyline.setPath(this.points);
                    }
                    //console.log(BMapLib.GeoUtils.getPolylineDistance(this.polyline));//获取线的长度.起点和终点的距离
                    this.setIcons();
                }
                /**
                 * 获取到markers 的点
                 * @returns {*}
                 */, getPoints: function () {
                    this.points = [];
                    //生成html
                    var points_box = $("#points_box");
                    points_box.html('');
                    var point = null;
                    this.start = this.markers[0];//起点
                    for (var i in this.markers) {

                        if (this.markers[i] && this.markers[i].getPosition) {
                            point = this.markers[i].getPosition();
                            this.points.push(point);
                            points_box.append(
                                '<div class="zuobiao">' +
                                '<input class="lng" type="hidden" name="point[' + i + '][lng]"  value="' + point.lng + '"/>' +
                                '<input class="lat" type="hidden" name="point[' + i + '][lat]" value="' + point.lat + '"/>' +
                                '</div>'
                            );
                        }
                        this.end = this.markers[i];

                    }
                    return this.points;
                }
                /*设置起点和终点提示*/
                ,setIcons:function(){
                    if(this.start && this.start.setLabel){
                        this.start.setLabel(this.start_label);
                    }

                    if(this.markers.length>1 && this.end && this.end.setLabel){
                        this.end.setLabel(this.end_label);
                    }
                }

            };


            map2 = new BMap.Map('step2_map');
            map2.centerAndZoom(currentPoint, 15);
            map2.enableScrollWheelZoom(true);
            map2.enableDragging();
            map2.setDefaultCursor('crosshair');

            map2.addEventListener('click', function (e) {
                //点击到覆盖物后不做处理
                if (!e.overlay) {
                    hm.addMarker(e.point);
                }
            });




            map2.addEventListener('rightclick', function (e) {
                hm.removeMarker();
            });


            //alert(AjaxUpload);
            function setDefaultThumb(){
                $('.thumb-radio',$('.uploader:eq(0)')).click();
            }

            //#######实例化 上传空间############################
            $(".twoc3_6_1,.twoc3_6_2,.twoc3_6_3,.twoc3_6_4").each(function () {
                var box = $(this);
                var btn = $('<input class="uploader_btn" type="button" name="img[]" value="" />');
                var img = $('<img class="uploader_prev" />');
                var loading = $('<div class="uploader_loading"></div>');
                var mask = $('<div class="uploader_mask"><span class="closer">×</span></div>');
                var uploader = new AjaxUpload(btn.get(0), {
                    action: uploader_url,
                    name: "img", //设置文件上传的名称  也就是 $_FILES[uploa][]
                    onSubmit: function (file, ext) {
                        if (ext && /^(jpg|jpeg)$/.test(ext)) { //匹配上传格式
                            //格式匹配成功
                            uploader.disable();
                            loading.show();
                        } else {
                            dialogUi.text("错误:上传图片格式为jpg");
                            return false;
                        }
                    },
                    onComplete: function (file, response) {
                        try {
                            response = jQuery.parseJSON(response);
                        } catch (e) {
                            response = {"code": -128, "msg": "图片数据获取失败"};
                        }
                        //{"code":0,"msg":"SUCCESS : '\u56fe\u7247\u4e0a\u4f20\u6210\u529f'","body":{"id":"1465","img_url":"http:\/\/test.image.app.jhla.com.cn\/20150423\/201504231439340478.jpg"}}
                        //{"code":0,"msg":"SUCCESS : '\u56fe\u7247\u4e0a\u4f20\u6210\u529f'","body":{"img_id":"1463"}}

                        if (response.code == '0') {
                            box.addClass('uploader');
                            btn.val(response.body.id);
                            box.addClass('thumb-set');
                            img.attr('src', response.body.img_url + '@240w_160h_1e_0c_50Q_1x.jpg');
                            //获取数量
                            $("#img_num").html(parseInt($("#img_num").html()) + 1);
                             
                             if(!$('#thumb_hidden').val()){                                
                                setDefaultThumb();
                             }       
                        } else {
                            uploader.enable();
                            loading.hide();
                            dialogUi.text(response.msg);
                        }

                        //mask.fadeIn();
                    }
                });
                box.uploader = uploader;
                box.data('uploader',uploader);


                img.load(function () {
                    loading.fadeOut(1000);
                    $(this).fadeIn(1000);
                });
                box.append(btn);
                box.append(img);
                box.append(loading);
                box.append(mask);
                box.append('<span class="thumb-text">封面</span><span class="thumb-radio"><i>&nbsp;</i>设为封面</span>');


                //删除图片
                $('.closer', mask).click(function () {
                    //获取数量
                    $("#img_num").html(parseInt($("#img_num").html()) - 1);
                    img.hide();
                    box.removeClass('uploader');
                    box.removeClass('thumb-seted');
                    box.removeClass('thumb-set');
                    uploader.enable();
                    if (btn.val() == $('#thumb_hidden').val()) {
                        $('#thumb_hidden').val('');
                        setDefaultThumb();
                    }
                    btn.val('');


                });
                $(".thumb-radio", box).click(function () {
                    $('#thumb_hidden').val(btn.val());
                    $(".uploader").removeClass('thumb-seted').addClass('thumb-set');
                    box.addClass('thumb-seted').removeClass('thumb-set');
                });





            });
             if(init.status){
                //alert("ASDF");
                var init_points =  init.data.step2.zuobiao;
                if(init_points)
                {
                    for (var i =0;i< init_points.length ; i++) {
                        var init_point = init_points[i];
                        if(init_point.lng){
                            init_point = new BMap.Point(init_point.lng,init_point.lat);
                            hm.addMarker(init_point);
                            map2.panTo(init_point);
                        }
                    };
                }

                
                var init_imgs = init.data.step2.imgs;
                var init_thumb = init.data.step2.thumb;
                /*
                $(".twoc3_6_1,.twoc3_6_2,.twoc3_6_3,.twoc3_6_4").each(function (){
                    
                })
                */
               if(init_imgs)
               {
                    for(var i=0;i<init_imgs.length;i++)
                    {
                        var box = $(".twoc3_6_"+(i+1));
                        var init_img = init_imgs[i];
                        //console.log(init_img);
                        box.data('uploader').disable();
                        /*
                        "id": "1880",
                        "img_url": "http://test.image.app.jhla.com.cn/20150505/201505050859310570.jpg"
                        */
                        box.addClass('thumb_'+init_img.id);
                        var btn = $(".uploader_btn",box);
                        var img = $(".uploader_prev",box);
                        //btn.val(init_img.id);
                        //img.src= init.img_url+'@292w_160h_1e_0c_50Q_1x.jpg';
                        box.addClass('uploader');
                        btn.val(init_img.id);
                        box.addClass('thumb-set');
                        img.attr('src', init_img.img_url + '@292w_160h_1e_0c_50Q_1x.jpg');
                        //获取数量
                        $("#img_num").html(parseInt($("#img_num").html()) + 1);
                    }
                }
                $(".thumb_"+init_thumb+" .thumb-radio").click();
            }

        }
    }
    step2_init.status = false;



    // -------------第三步 代码------------------------------------------
    function step3_init(){
        if(!step3_init.status){
            step3_init.status = true;
            $(".ziliao_item").live('click',function(e){
                //获取父元素
                var id = $(this).parent().attr('id');        
                switch(id){
                    case 'ziliao_selected':
                    break;

                    case 'ziliao_selecter':
                        selecter_click.call(this,e);
                    break;
                }
            }); 
            //取消选中
            $(".ziliao_item .closer").live('click',function(){
                   var parent = $(this).parents('.ziliao_item');
                   var that = this;
                   parent.hide(300,function(){
                            $(that).remove();
                            $(".ziliao_cust").before( $(this) );
                            $(this).show(200,function(){                                 
                            });
                        });
                   
            }); 
            $(".ziliao_cust").click(function(){
                $(this).hide(0);
                $(this).before('<div class="ziliao_edit"><input type="text" id="ziliao_edit_inp" onblur="edit_event(event,this)" onkeyup="edit_event(event,this)" maxlength="6" /></div>'); 
                $("#ziliao_edit_inp").focus();
            });    
            window.edit_event = function(e,ele){
                e = window.event?window.event:e;
                //alert(e.type);
                if( (ele.value+"").trim() ){ 
                    var val = null;
                    if(e.type=='keyup'){
                        //alert(e.keyCode);
                        if(e.keyCode =='13'){
                           val = (ele.value+"").trim();     
                        }else{
                            val = false;
                        }    
                    }else if(e.type=='blur'){
                           val = (ele.value+"").trim();      
                    }     
                    if(val){
                        create_field(ele,val);
                    }   
                    val = null;
                } 
            }


            function create_field(ele,text){
                var status = false;
                var field = null;
                //判断值是否存在
                $(".ziliao_item").each(function(){
                    if(text == $(this).text() ){
                        status = true;
                        field = $(this);
                    }    
                })
                if(!status){
                    field = $(ele).parent();
                    field.html(text).attr('class','ziliao_item');
                    $(".ziliao_cust").show();                            
                }
                return {status:status,item:field};
            }


            //选中项目 
            function selecter_click(e){
                //判断事件处理状态
                if(!selecter_click.status && $("#ziliao_selected .ziliao_item").size() < 7){
                    //判断操作元素时候正确
                    if($(this).attr && $(this).attr('class') =='ziliao_item' ){
                        selecter_click.status = true;
                        $(this).hide(300,function(){
                            $(this).append('<span class="closer">×<input class="customFields" type="hidden" name="ziliao[]" value="'+$(this).text()+'" /></span>');
                            $("#ziliao_selected").append( $(this) );
                            $(this).show(200,function(){
                                selecter_click.status = false;
                            });
                        });
                        
                    }    
                }
            }
            selecter_click.status = false;

            if(init.status)
            {
                var init_fields = init.data.step3.fields;
                //alert(init_fields.length);
                if(init_fields)
                {
                    for (var i = init_fields.length - 1; i >= 0; i--) 
                    {
                        var init_field = init_fields[i];
                        if(
                            init_field =='手机' 
                            || init_field=='姓名'
                            || init_field=='年龄'
                            || init_field=='性别'
                            )
                            continue;
                        $("#ziliao_selected").append('<div class="ziliao_item" style="display: block;">'+init_field+'<span class="closer">×<input class="customFields" type="hidden" name="ziliao[]" value="'+init_field+'"></span></div>');
                        $("#ziliao_selecter .ziliao_item").each(function(){
                        if(init_field == $(this).text() ){
                            $(this).remove();
                        }    
                    })                  
                    };
                }
            }


        }
    }
    step3_init.status = false;



    /**
     *第一步数据提交
     */
    $(".c3_12_1").click(function () {
        /*
        //去到第二步
         slider_step_to(1, function () {
            //去到第二步
            setp_log_to(1);
            //初始化第二步
            step2_init();
        });
        return; 
        */
        //获取数据
        var p_lon = $('#lon').val();
        var p_lat = $('#lat').val();
        var p_addr_city = $('#addr_city').val();
        var p_addr_area = $('#addr_area').val();
        var p_addr_road = $('#addr_road').val();
        var p_addr_num = $('#addr_num').val();
        var p_addr_name = $('#addr_name').val();
        //结束时间
        var p_eTime_l = $('#eTime_l').val();
        var p_eTime_y = $('#eTime_y').val();
        var p_eTime_r = $('#eTime_r').val();
        var p_eTime_s = $('#eTime_s').val();
        var p_eTime_f = $('#eTime_f').val();
        
        //开始时间
        var p_bTime_l = $('#bTime_l').val();
        var p_bTime_y = $('#bTime_y').val();
        var p_bTime_r = $('#bTime_r').val();
        var p_bTime_s = $('#bTime_s').val();
        var p_bTime_f = $('#bTime_f').val();

        //活动标题
        p_title = $('#title').val();
        //判断数据
        if (p_title == '') {
            dialogUi.text("错误:活动名称未输入！");
        }
        else if (p_eTime_l == 0 || p_eTime_y == 0 || p_eTime_r == 0 || p_eTime_s == 0|| p_eTime_f == 0|| p_bTime_l == 0 || p_bTime_y == 0 || p_bTime_r == 0 || p_bTime_s == 0 || p_bTime_f == 0) {
            dialogUi.text("错误:活动时间选择不完整！");
        }
        else if (p_lon == '' || p_addr_name == '') {
            dialogUi.text("错误:活动地址未选择！");
        }
        else 
        {
            //通过判断
            var startime = p_bTime_l + '-' + p_bTime_y + '-' + p_bTime_r + ' '+p_bTime_s+':'+p_bTime_f+':00';
            var stoptime = p_eTime_l + '-' + p_eTime_y + '-' + p_eTime_r + ' '+p_eTime_s+':'+p_eTime_f+':00';
            a_time=startime;
            //活动日期
            var ok_time=  new Date(startime).getTime();
            //报名结束
            var end_time= new Date(stoptime).getTime();
            
            if(end_time<ok_time)
            {
                dialogUi.text("错误:活动结束日期不能小于活动开始日期！" );
            }
            else
            {
                //体验效果
                var dialog11 = dialogUi.wait();
                //提交数据
                server.create({
                    title: p_title,
                    bTime: startime,
                    eTime: stoptime,
                    lon: p_lon,
                    lat: p_lat,
                    addrCity: p_addr_city,
                    addrArea: p_addr_area,
                    addrRoad: p_addr_road,
                    addrNum: p_addr_num,
                    addrName: p_addr_name
                }, function (resp) {
                    if (resp.code == 0) {
                        act_id = resp.body.act_id;
                        //去到第二步
                        slider_step_to(1, function () {
                            //去到第二步
                            setp_log_to(1);
                            //初始化第二步
                            step2_init();
                        });
                    }
                    else {
                        dialogUi.text(resp.msg);
                    }
                    //关闭用户体验
                    dialog11.hide();
                })
            }

        }

    });
    /**
     *第二步数据提交
     */
    $(".twoc3_16").click(function () {
        /*
         slider_step_to(2, function () {
                        //去到第三部
                        setp_log_to(2);
                        step3_init();
                    });return; 
        */
        //获取数据
        //var p_callPhone =  $('#callPhone').val();
        //var p_callName  =  $('#callName').val();
        //var p_detail = $('#p_detail').html();
        var p_detail = $('#p_detail').val();
        var p_route_points = '';
        var img_id = new Array();
        var p_hImgId=$('#thumb_hidden').val();

        //获取图片
        var a1=0;
        $('.uploader').each(function (i) 
        {
            //获取图片id
            if(p_hImgId!=$(this).children($('.uploader_btn')).val())
            {
                img_id[a1] = $(this).children($('.uploader_btn')).val();
                a1++;
            }
            
        })
      
        //获取坐标
        $('.zuobiao').each(function (j) {
            if (j == 0)
                p_route_points = $(this).children($('.lng')).val() + ',' + $(this).children($('.lng')).next($('.lat')).val() + ',,';
            else
                p_route_points += '-' + $(this).children($('.lng')).val() + ',' + $(this).children($('.lng')).next($('.lat')).val() + ',,';
        })

        //判断数据
        if (act_id == 0 || p_title == '') {
            dialogUi.text("错误:系统忙，请重试！");
        }
        else if (p_detail == '') {
            dialogUi.text("错误:活动详情未输入！");
        }
       /* else if (p_route_points == '') {
            dialogUi.text("错误:活动路线位选择！");
        }*/
        else if (p_hImgId == '') {
            dialogUi.text("错误:活动封面图未上传！");
        }
        else if(img_id.length<1)
        {
            dialogUi.text("错误:活动图片至少2张！");
        }
        else {
            //体验效果
            var dialog2 = dialogUi.wait();
            //提交数据
            server.modify({detail:p_detail,actId: act_id, route_points: p_route_points, imgIds: img_id,hImgId:p_hImgId}, function (resp) {
                if (resp.code == 0) {
                    slider_step_to(2, function () {
                        //去到第三部
                        setp_log_to(2);
                        step3_init();
                    });

                }
                else {
                    dialogUi.text(resp.msg);
                }
                //关闭用户体验
                dialog2.hide();
            })

        }

    });

    /**
     *第三步数据提交
     */

 $("#fabu").click(function () {

        //报名开始时间
        var p_enrollBeginTime_l = $('#p_enrollBeginTime_l').val();
        var p_enrollBeginTime_y = $('#p_enrollBeginTime_y').val();
        var p_enrollBeginTime_r = $('#p_enrollBeginTime_r').val();
        var p_enrollBeginTime_s = $('#p_enrollBeginTime_s').val();
        var p_enrollBeginTime_f = $('#p_enrollBeginTime_f').val();        
        //报名结束时间
        var p_enrollEndTime_l = $('#p_enrollEndTime_l').val();
        var p_enrollEndTime_y = $('#p_enrollEndTime_y').val();
        var p_enrollEndTime_r = $('#p_enrollEndTime_r').val();
        var p_enrollEndTime_s = $('#p_enrollEndTime_s').val();
        var p_enrollEndTime_f = $('#p_enrollEndTime_f').val();
        //是否报名人数限制：0否，1是
        var p_enrollLimit=$('.p_enrollLimit').is(':checked')?0:1;
        //报名限制人数
        var p_enrollLimitNum=0;
        if(p_enrollLimit==1)
        {
            p_enrollLimitNum=$('#p_enrollLimitNum').val();
        }
        //是否需要支付费用：0否，1是
        var p_showPay=0;
        $('.p_showPay').each(function(){ 
            if($(this).is(':checked'))
            {
               p_showPay=$(this).val();
            }
        })
        //费用
        var p_totalFee=0;
        if(p_showPay==1)
        {
           p_totalFee=$('#p_totalFee').val();
        }
        //是否需要报名审核：0否，1是
        var p_showVerify=0;
        $('.p_showVerify').each(function(){ 
            if($(this).is(':checked'))
            {
               p_showVerify=$(this).val();
            }
        })
        
        //自定义字段数组
        var p_customFields=new Array();
        //获取报名需要提供资料
        $('.customFields').each(function (i) {
            //报名需要提供资料
            p_customFields[i] = $(this).val();
        })

        
        
        if (act_id == 0 && a_time=='') 
        {
            dialogUi.text("错误:系统忙，请重试！");
        }
        else if (p_enrollBeginTime_l == 0 || p_enrollBeginTime_y == 0 || p_enrollBeginTime_r == 0 || p_enrollBeginTime_s == 0 || p_enrollBeginTime_f == 0 || p_enrollEndTime_l == 0 || p_enrollEndTime_y == 0 || p_enrollEndTime_r == 0 || p_enrollEndTime_s == 0 || p_enrollEndTime_f == 0) 
        {
            dialogUi.text("错误:活动报名时间选择不完整！");
        }
        else if(p_enrollLimit==1 && p_enrollLimitNum==0)
        {
            dialogUi.text("错误:活动人数限制未输入！");
        }
        else if(p_showPay==1 && p_totalFee==0)
        {
            dialogUi.text("错误:活动费用未输入！");
        }
        else
        {
            //活动时间
            p_enrollBeginTime=p_enrollBeginTime_l+'-'+p_enrollBeginTime_y+'-'+p_enrollBeginTime_r+ ' '+p_enrollBeginTime_s+':'+p_enrollBeginTime_f+':00';      
            p_enrollEndTime= p_enrollEndTime_l+'-'+p_enrollEndTime_y+'-'+p_enrollEndTime_r+ ' '+p_enrollEndTime_s+':'+p_enrollEndTime_f+':00';;
         
            //活动日期
            var into_time=  new Date(a_time).getTime();
            //报名结束
            var now_time= new Date(p_enrollEndTime).getTime();
            //报名开始
            var now1_time= new Date(p_enrollBeginTime).getTime();
            
            if(into_time<now_time)
            {
                dialogUi.text("错误:报名结束日期不能大于活动开始日期！" );
            }
            else if(now_time<now1_time)
            {
                dialogUi.text("错误:报名结束日期不能小于报名开始日期！" );
            }
            else
            {
            
                //判断完成
                //用户体验
                var  dialog3 =  dialogUi.wait();
                //提交数据
    		   server.modify({enrollBeginTime:p_enrollBeginTime,enrollEndTime:p_enrollEndTime, actId: act_id,enrollLimit:p_enrollLimit,enrollLimitNum:p_enrollLimitNum,showPay:p_showPay,showVerify:p_showVerify,totalFee:p_totalFee,customFields:p_customFields}, function(resp) 
               {
    						if (resp.code == 0) 
                            {
                               dialogUi.text('发布成功!,3秒后返回!'); 
                               setTimeout(function(){window.location.href = 'index'},2000);  
    						} 
                            else 
                            {
    							dialogUi.text(resp.msg);
    						}
                            //关闭用户体验
                            dialog3.hide();
    		    })
             }
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
							dialogUi.text(resp.msg );
						}
					})
				})

    //选择特效
    $('.p_enrollLimit').click(function()
    {
        if($(this).is(':checked'))
        {
           $('#p_enrollLimitNum').attr("disabled",'true');
           $('#p_enrollLimitNum').val(0);
        }
        else
        {
            $('#p_enrollLimitNum').removeAttr("disabled");
            $('#p_enrollLimitNum').val('');
        }
    })
    //是否需要支付费用
    $('#showPay').click(function()
    {
        $('#showVerify').removeAttr("disabled");
        $('#showVerify1').removeAttr("disabled");
        $('#p_totalFee').attr("disabled",'true');
        $('#p_totalFee').val(0);
    })
    $('#showPay1').click(function()
    {
        $('#showVerify').attr("disabled",'true');
        $('#showVerify').attr("checked",'true');
        $('#showVerify1').removeAttr("checked");
        $('#showVerify1').attr("disabled",'true');
        $('#p_totalFee').removeAttr("disabled");
        $('#p_totalFee').val('');
        
    })
    
           


})
;