/**
 * 活动数据分析
 * @author      吴华
 * @company     成都哈希曼普科技有限公司
 * @site        http://www.hashmap.cn
 * @email       wuhua@hashmap.cn
 * @date        2015-05-07
 */
define(function (require, exports, module){ 

	var $ = require('$');    
    var dialogUi = require('dialogUi');
    var server = require('server');      
    var loader = require('loader');      
    var wait_dialog = dialogUi.wait();
    loader.setConf({'base':basePath+'/'});  
    //document ready	
    $(function(){
        //加载 js
        loader.load('static/highcharts/js/highcharts.js',function(){
            //console.log(this);
            //wait_dialog.hide();
            init();
        });   		
    });	
    //document ready end



    //init start 	
 	function init( ){ 
 		//获取到活动数据
 		server.OrgDA({},function(ret){
 			if(ret.code=='0'){
 				//成功举办活动
 				$(".c2_2_2_1_1").text(ret.body.holded);
 				//incAnimate($(".c2_2_2_1_1").get(0),ret.body.holded);
 				//总报名人数
 				$(".c2_2_2_1_2").text(ret.body.enrolled);
 				//incAnimate($(".c2_2_2_1_2").get(0),ret.body.enrolled);
 				//总签到人数
 				$(".c2_2_2_1_3").text(ret.body.checked_in);
 				//incAnimate($(".c2_2_2_1_3").get(0),ret.body.checked_in);

 				showMemberForHoter(null);
 				var members = ret.body.active_members
 				for (var i = 0; i < members.length; i++) {
 					 var member = members[i];
 					 showMemberForHoter(member);
 				};
 			}



 			//获取到单个活动的统计数据
 			server.ActDA({},function(daRet){
                wait_dialog.hide();
 				if(daRet.code=='0'){
 					//活动
 					var acts = daRet.body.acts;
 					//清空列表数据
 					showActDaForCahrtList(null);
 					for (var i = 0; i < acts.length; i++) { 
 						showActDaForCahrtList(acts[i]);
 					};
 				}
 			});
 		});
 	}			
 	//init end



 	//显示成员到活跃成员
 	function showMemberForHoter(member){
 		if(member == null){
 			$(".hoter-body .hoter").remove();
 		}else if(member.id){
 			var hoter = $('<div class="hoter"></div>');
 			var head = $('<a href="###" class="head"><img src="'+member.head_img_url+'@100w_100h_1e_0c_50Q_1x.jpg"></a>');
 			var username = $('<a href="###" class="username">'+member.nick_name+'</a><br>');
 			var tips = $('<span class="tips">签到活动: <span class="tips-green">'+member.checked_in+'</span>个</span>');
 			hoter.append(head);
 			hoter.append(username);
 			hoter.append(tips);
			$(".hoter-body").append(hoter);
 		}
 	}
	//显示成员到活跃成员



	//将活动数据显示到据统计图
	function showActDaForCahrtList (act) {
		if(act==null){
			$(".act_da_list .actDA").remove();
		}else if(act.id){
			 				/*
total_num	int	总活动数
@acts	[]	活动数据信息
@id	int	活动id
@title	string	活动名称
@h_img_url	String	活动封面图
@b_time	string	开始时间
@e_time	string	结束时间
@enroll_num	int	报名数
@checkin_num	int	签到数
@male	Int	男性
@female	Int	女性
@@checked_info	[]	包含以下签到点信息
@@subject	string	签到点名称
@@checked_in	int	签到点签到人数
		签到点其他基本信息
 				*/
            var container = $('<div class="container actDA"><div class="container-title">活动数据</div><div class="container-body"></div></div>');
            var act_info = $('<div class="act-info"><a href="###" class="thumb"><img src="'+act.h_img_url+'@250w_140h_1e_0c_50Q_1x.jpg"></a><a href="###" class="title">'+act.title+'</a><span class="tips">结束时间: '+act.e_time+'</span></div> ');

            var act_da = $(
                '<div class="act-da">'+
                    '<div class="act_member_counters">'+
                        '<div class="act_member_counter inset_counter">'+
                            '<div class="counter-title">参与情况</div>'+
                            '<div class="counter-box"></div>'+
                            '<div class="baoming">'+
                                '<span class="num">'+act.enroll_num+'</span>'+
                                '<span class="dot">&nbsp;</span>'+
                            '报名人数</div>'+
                            '<div class="canyu">'+
                                '<span class="num">'+act.checkin_num+'</span>'+
                                '<span class="dot">&nbsp;</span>'+
                            '参与人数</div>'+
                        '</div>'+
                        '<div class="act_member_counter sex_counter">'+
                            '<div class="counter-title">性别比例</div>'+
                            '<div class="counter-box"></div>'+
                            '<div class="nan">'+
                                '<span class="num">'+act.male+'</span>'+
                                '<span class="dot">&nbsp;</span>'+
                            '男士人数</div>'+
                            '<div class="nv">'+
                                '<span class="num">'+act.female+'</span>'+
                                '<span class="dot">&nbsp;</span>'+
                            '女士人数</div>'+
                        '</div>'+
                    '</div>'+
                    '<div class="check-counters">'+
                        '<div class="counter-title">参与情况</div>'+
                        '<div class="counter-box"></div>'+
                    '</div>'+
                '</div>'
                );


            var check_count = $('');
            $(".container-body",container).append(act_info);
            $(".container-body",container).append(act_da);
            $(".container-body",container).append(check_count);
            $('.act_da_list').append(container);
            createSexCharts($(".sex_counter .counter-box",container).get(0),act.male,act.female);
            createInsertCharts($(".inset_counter .counter-box",container).get(0),act.enroll_num,act.checkin_num);
            /*act.checked_info = [
                {subject:'签到点1',checked_in:10}
                ,{subject:'签到点11',checked_in:11}
                ,{subject:'签到点111',checked_in:111}
            ];*/
            createCheckInCharts($(".check-counters .counter-box",container).get(0),act.checked_info);

		}
	}
	//将活动数据显示到据统计图






	//自动增长动画
 	function incAnimate(ele,to){ 		
 		var si = setInterval((function(){
 			var _ele = ele;
 			var _to = to;
 			var _si = si;
 			return function(){
 				var now = parseInt($(ele).text());
 				if(now==NaN){
 					now = 0;
 				}
 				if(now<_to){
 					now++;
 					$(ele).text(now)
 				}else{
 					clearInterval(_si);
 				}
 			};
 		})(),20);
 	}
 	//自动增长动画


    //create sex charts
    function createSexCharts(ele,male,female){
        var tilte = (male=='0' && male == female)?'无数据':'<h2 class="counter-val" style="font-weight: bold;font-size: 30px;">'+male+':'+female+'</h2><br><span class="counter-text">男:女</span>';
        $(ele).highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
                text: tilte,
                align: 'center',
                verticalAlign: 'middle',
                y: 0
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            colors:[
                '#FF9934','#60C247'
            ],
            credits:{
            enabled:false
            }
            ,plotOptions: {
                 
                pie: {
                    allowPointSelect: false,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                        style: {
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        }
                    }
                } 
            },
            series: [{
                type: 'pie',
                name: 'Browser share',
                innerSize: '80%',
                data: [
                    ['女', female],
                    ['男', male]
                ]
            }]
        });
    }
    //create sex charts


    //create insert charts
    function createInsertCharts(ele,enroll_num,checkin_num){
        var title = (enroll_num=='0')?'无数据':'<h2 class="counter-val" style="font-weight: bold;font-size: 30px;">'+(Math.ceil(checkin_num/enroll_num*100))+'</h2>%<br><span class="counter-text">参与率</span>';
        $(ele).highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
                text: title,
                align: 'center',
                verticalAlign: 'middle',
                y: 0
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            colors:[
                '#FF6600','#FF9934'
            ],
            credits:{
            enabled:false
            }
            ,plotOptions: {
                 
                pie: {
                    allowPointSelect: false,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                        style: {
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        }
                    }
                } 
            },
            series: [{
                type: 'pie',
                name: 'Browser share',
                innerSize: '80%',
                data: [
                    ['报名数', parseInt(enroll_num)],
                    ['签到数', parseInt(checkin_num)]
                ]
            }]
        });
    }
    //create insert charts



    function createCheckInCharts(ele,checked_info){
        var subjects = [];
        var checked_ins = [];
        for(var key in checked_info){
            var checked = checked_info[key];
            subjects.push(checked.subject);
            checked_ins.push(parseInt(checked.checked_in));
        }


        $(ele).highcharts({
            chart: {
                type: 'area'
            },
            title: {
                text: ''
            },legend: {
                enabled: false
            },

            colors: ['#FFAE5F']
            ,subtitle: {
                text: '人数 (单位: 人)'
                ,align:'left'
            },
            xAxis: {
                categories:subjects
            },
            yAxis: {
                title: {
                    text: ''
                },
                labels: {
                    formatter: function () {
                        return this.value;// / 1000 + 'k';
                    }
                }
            },
            tooltip: {
                pointFormat: '{series.name} produced <b>{point.y:,.0f}</b><br/>warheads in {point.x}'
            }
            /*,
            plotOptions: {
                area: {
                    pointStart: 1940,
                    marker: {
                        enabled: false,
                        symbol: 'circle',
                        radius: 2,
                        states: {
                            hover: {
                                enabled: true
                            }
                        }
                    }
                }
            }*/,
            series: [ {
                name: false,
                data: checked_ins
                ,marker:{enabled:false}
            }]
        });
    }



});




/*

$(function () {
    $('#container').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        title: {
            text: 'Browser<br>shares',
            align: 'center',
            verticalAlign: 'middle',
            y: 0
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        colors:[
            '#FF9934','#5FC349'
        ],
        credits:{
        enabled:false
        }
        ,plotOptions: {
             
            pie: {
                allowPointSelect: false,
                cursor: 'pointer',
                dataLabels: {
                    enabled: false,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            } 
        },
        series: [{
            type: 'pie',
            name: 'Browser share',
            innerSize: '70%',
            data: [
                ['女',       26.8],
                ['男',   45.0]
            ]
        }]
    });
});
*/