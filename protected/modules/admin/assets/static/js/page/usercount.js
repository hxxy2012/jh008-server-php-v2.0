define(function(require, exports, module){

	var $ = jQuery,
		server = require('server'),
		dialogUi = require('dialogUi'),
		calendar = require('calendar'),
		main = require('main');


	var tip = main.tip,
		wait = main.wait;
		
	function baseTongjiPanel() {
		var dialog = wait();
		server.countInfo(function(resp){
			dialog.hide();
			if (resp.code == 0) {
				var html = template.render('count_template', resp.body);
				$('#countWrap').html(html);
			} else {
				tip (resp.msg || '获取统计数据失败');
			}
		})
	}

	var registCountPanel = function() {
		var initFlag = false;
		function setHighCharts(startDate, endDate, counts) {
			var xlist = [], ylist = [], pretitle='';
			$.each(counts, function(i, v){
				xlist.push(v.date);
				ylist.push(Number(v.count));
			})
			if (startDate && endDate) {
				pretitle = startDate + '到' + endDate;
			}
			window.$('#registWrap').highcharts({
				chart: {
					type: 'line'
				},
	            title: {
	                text: pretitle + '注册用户统计',
	                x: -20 //center
	            },
	           /* subtitle: {
	                text: 3,
	                x: -20
	            },*/
	            xAxis: {
	            	title:{
	            		text: '日期'
	            	},
	                categories: xlist,
	                labels:{
	                	rotation: 90 || 0
	                }
	            },
	            yAxis: {
	                title: {
	                    text: '注册数'
	                },
	                plotLines: [{
	                    value: 0,
	                    width: 1,
	                    color: '#808080'
	                }]
	            },
	            tooltip: {
	                valueSuffix: ''
	            },
	            legend: {
	                layout: 'vertical',
	                align: 'right',
	                verticalAlign: 'middle',
	                borderWidth: 0
	            },
	            series: [{data: ylist, name:'注册数量'}]
	        });
		}

		return function() {
			if (!initFlag) {
				function getDate(date) {
					var result=[];
					var date = date || new Date();
					result.push(date.getFullYear());
					result.push(date.getMonth()+1);
					result.push(date.getDate());
					return result.join('-');
				}
				initFlag = true;
				new calendar({
			        trigger: '#startDate',
			        range: [null, getDate(new Date((new Date()).getTime()-24*3600000))]
			    });
				new calendar({
			        trigger: '#endDate',
			         range: [null, getDate()]
			    });
				$('#dateSearch').click(function(){
					var startDate = $.trim($('#startDate').val()),
						endDate = $.trim($('#endDate').val());
					var dialog = wait();
					server.getRegistCount({startDate: startDate, endDate: endDate}, function(resp){
						dialog.hide();
						if (resp.code == 0) { console.log(resp.body);
							setHighCharts(startDate, endDate, resp.body.counts);
						} else {
							tip ('注册用户统计获取失败');
						}
					})
				})				
			}
		}
	}();

	var page = {
		init: function() {
			$('.ui-tab').on('click', function(e){
				var target = $(e.target);
				if (target.parent().hasClass('ui-tab-item') || target.hasClass('ui-tab-item')) {
					var lis = target.parents('.ui-tab-items').find('li');
					$.each(lis, function(i){
						lis.eq(i).removeClass('ui-tab-item-current');
						$('#' + lis.eq(i).attr('role')).hide();
					})
					target.closest('.ui-tab-item').addClass('ui-tab-item-current');
					$('#' + target.closest('.ui-tab-item').attr('role')).show();
				}
			})
			baseTongjiPanel();
			$('#registCount').click(function(){
				registCountPanel();
			})
		}
	}

	page.init();

})