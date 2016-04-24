

//标签页
var step_tab = {
    inited: false,
    /**
     * 初始化
     */
    init: function () {
        var that = this;
        if (!that.inited) {
            that.inited = true;
            $(".step-item").click(function () {
                that.to($(this).data('to'));
            });
        }
    },
    to: function (i) {
        /*
         $(".step-slider-body").animate({'margin-left':'-'+((i-1)*1000)+'px'},250,null,function(){
         $(".step-item").removeClass('active');
         $(".step-item").eq(i-1).addClass('active');
         });
         */
        $(".step-slider-item").fadeOut(250).css('z-index', 1);
        $(".step-slider-item").eq(i - 1).fadeIn(300, function () {
            $(".step-item").removeClass('active');
            $(".step-item").eq(i - 1).addClass('active');
        }).css('z-index', 10);

    }
};

var pos_map = null;//活动地址
var poi_map = null;//活动线路
var pos_geoc = null;//地址解析
var pos_local = null;

jQuery(function () {
    aid = parseInt(0 + aid);
    //console.log('活动编号:' + aid);


    //步骤切换效果
    step_tab.init();
    pos_map = new BMap.Map("activePosMap");
    pos_map.centerAndZoom("成都", 12);
    pos_map.enableScrollWheelZoom();
    pos_local = new BMap.LocalSearch(pos_map, {
        renderOptions: {map: pos_map}
        //成功以后回调
        , onMarkersSet: function (pois) {
            for (var i = 0; i < pois.length; i++) {
                pois[i].marker.addEventListener('click', function (e) {
                    pos_getLocation(e.target);
                })
            }
        }
    });

    $("#search-pos").click(function () {
        if ($("#search").val()) {
            pos_map.clearOverlays();
            pos_local.search($("#search").val());
        }
    });


    poi_map = new BMap.Map("activePoiMap");
    poi_map.centerAndZoom("成都", 12);
    poi_map.enableScrollWheelZoom();
    poi_map.addEventListener('click', function (e) {
        //点击到覆盖物后不做处理
        if (!e.overlay) {
            hm_poi.addMarker(e.point);
        }
    });

    pos_geoc = new BMap.Geocoder();
    step2_init();


    check_customed_field("");

    if (aid > 0) {//传递了活动编号
        //表示有初始化数据
        init.call(this, run);
    } else {//没有传递活动编号
        run.call(this);
    }


    //自定义字段
    $(".customed_fields .custom_field i").live('click', function () {
        $(this).parent().fadeOut(250, function () {
            $(this).remove();
        })
    });
    $(".add_custom_field").click(function () {
        add_custom_field($("#custom_field").val());
        $("#custom_field").val('');
    });

});


/**
 * 程序初始化方法
 */
function init(callback) {
    console.log('程序开始初始化');
    window.HM.call(callback, this);
}
init.data = {};

/**
 * 运行程序
 */
function run() {
    //console.log('程序开始运行');
    step1_init();
    step4_init();
}


function step1_init() {
    if (!step1_init.inited) {
        step1_init.inited = true;
        //开始和结束日期选择器
        var start = {
            elem: '#b_time',
            format: 'YYYY/MM/DD hh:mm:ss',
            min: (new Date()).format('YYYY-MM-DD HH:II:SS'), //设定最小日期为当前日期
            istime: true,
            istoday: false,
            choose: function (datas) {
                end.min = datas; //开始日选好后，重置结束日的最小日期
                end.start = datas //将结束日的初始值设定为开始日
            }
        };
        var end = {
            elem: '#e_time',
            format: 'YYYY/MM/DD hh:mm:ss',
            min: (new Date()).format('YYYY-MM-DD HH:II:SS'),
            istime: true,
            istoday: false,
            choose: function (datas) {
                start.max = datas; //结束日选好后，重置开始日的最大日期
            }
        };
        laydate(start);
        laydate(end);


    }
}
step1_init.inited = false;


var click_marker = null;
var hm_poi = {
    markers: []  //所有点的集合
    , polyline: null //绘制的路径
    , id: 1 //marker的自增编号
    , points: []  //markers的点列表
    , start_label: new BMap.Label('起点', {offset: new BMap.Size(-5, -20)}) //起点标志
    , end_label: new BMap.Label('终点', {offset: new BMap.Size(-5, -20)}) //终点标志
    , start: null  //起点
    , end: null //终点
    /**
     * 添加一个marker
     * @param point
     */, addMarker: function (point) { //添加一个点
        var marker = new BMap.Marker(point);
        marker.enableDragging();//启用拖拽功能
        poi_map.addOverlay(marker);
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
            poi_map.removeOverlay(marker);
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
            poi_map.addOverlay(this.polyline);
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
    /*设置起点和终点提示*/, setIcons: function () {
        if (this.start && this.start.setLabel) {
            this.start.setLabel(this.start_label);
        }

        if (this.markers.length > 1 && this.end && this.end.setLabel) {
            this.end.setLabel(this.end_label);
        }
    }

};

function pos_getLocation(e) {
    pos_geoc.getLocation(e.point, function (rs) {
        var addComp = rs.addressComponents;
        //alert(addComp.province + ", " + addComp.city + ", " + addComp.district + ", " + addComp.street + ", " + addComp.streetNumber);
        var poi = rs.surroundingPois && rs.surroundingPois[0] ? rs.surroundingPois[0] : {};
        if (!poi.title) {
            poi.title = rs.address;
        }

        set_pos(e.point.lng, e.point.lat, addComp.city, addComp.district, addComp.street, addComp.streetNumber, rs.address, poi.title ? poi.title : '');
    });
}

function step2_init() {
    if (!step2_init.inited) {
        step2_init.inited = true;
        if (!click_marker) {
            click_marker = new BMap.Marker(new BMap.Point(116.404, 39.915));
        }
        pos_map.addEventListener("click", function (e) {
            //点击到覆盖物后不做处理
            if (!e.overlay) {
                click_marker.setPosition(e.point);
                pos_map.addOverlay(click_marker);
                pos_getLocation(e);
            }

        });
    }
}
step2_init.inited = false;

function set_pos(lng, lat, addr_city, addr_area, addr_road, addr_num, address, addr_name) {
    $("#lng").val(lng);
    $("#lat").val(lat);
    $("#addr_city").val(addr_city);
    $("#addr_area").val(addr_area);
    $("#addr_road").val(addr_road);
    $("#addr_num").val(addr_num);
    $("#address").val(address);
    $("#addr_name").val(addr_name);

    if (pos_map.panTo) {
        pos_map.panTo(new BMap.Point(lng, lat));
    }
}


function add_custom_field(value) {
    value = ("" + value).trim();
    if (value && !check_customed_field(value)) {
        $(".customed_fields").append(' <a href="javascript:;" class="button button-m custom_field">' + value + '<i class="icon iconfont">&#xe615;</i><input type="hidden" name="custom_field_hidden[]" class="custom_field_hidden" value="' + value + '"/></a> ');
    }
}
function check_customed_field(value) {
    value = ("" + value).trim();
    var has = false;
    $(".system_field,.customed_fields .custom_field").each(function () {
        var clone = $(this).clone();
        $("i", clone).remove();
        var text = (clone.text() + "").trim();
        if (text == value) {
            has = true;
        }
    });
    return has;
}


function step4_init() {
    if (!step4_init.inited) {
        step4_init.inited = true;
        //开始和结束日期选择器
        var start = {
            elem: '#enroll_b_time',
            format: 'YYYY/MM/DD hh:mm:ss',
            min: (new Date()).format('YYYY-MM-DD HH:II:SS'), //设定最小日期为当前日期
            istime: true,
            istoday: false,
            choose: function (datas) {
                end.min = datas; //开始日选好后，重置结束日的最小日期
                end.start = datas //将结束日的初始值设定为开始日
            }
        };
        var end = {
            elem: '#enroll_e_time',
            format: 'YYYY/MM/DD hh:mm:ss',
            min: (new Date()).format('YYYY-MM-DD HH:II:SS'),
            istime: true,
            istoday: false,
            choose: function (datas) {
                start.max = datas; //结束日选好后，重置开始日的最大日期
            }
        };
        laydate(start);
        laydate(end);

    }
}
step4_init.inited = false;




