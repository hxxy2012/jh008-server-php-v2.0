define(function(require, exports, module){

	var 
		server = require('server'),
		static = require('static'),
		Uploader = require('upload'),
		calendar = require('calendar'),
		K = require('K'),
		timePicker = require('timepicker'),
		main = require('main');

	var tip = main.tip,
		wait = main.wait;

	var recommendsDetailView = (function(){
		function render(data) {
			var El = $('<div></div>');
			El.html(template.render('recommond_detail_template', data));
			$('#otherWrap').html(El);
		}

		return {
			render: render
		}
	})();

	var recommendsUsersView = (function(){
		function render(data) {
			var El = $('<div></div>');
			El.html(template.render('recommond_user_template', data));
			$('#otherWrap').html(El);
		}

		return {
			render: render
		}
	})();

	var recommendsView = (function() {
		function instanceTableList(parms) {
			var table = new K.PaginationTable({
				ThList: [
					'编号', '备注', '地址', '推荐时间', '状态', '详情', '用户信息', '删除'
				],
				columnNameList: [
					'index',
					'remark','address', 'create_time', 
					function(data) {
						if (data.status == -1){return '已删除';} else if(data.status==0){return '正常';}
					},
					function(data) {
						return  '<a href="javascript:;" id="detail" class="">详情</a>';
					},	
					function(data) {
						return  '<a href="javascript:;" id="watch" class="">查看</a>';
					},					
					function(data) {
						return  '<a href="javascript:;" id="delete" class="">删除</a>';
					}
				],
				//rowClass: 'abc',
				rowClass: function(index) {
					if (index%2 == 0) {
						return 'odd';
					} else {
						return 'even';
					}
				},
				source: function(o, pag, table) {
					dialog = wait();
					parms.page = o.currentPage;
					parms.size = static.recommendsPerNum;
					server.getRecommends(parms, function(resp){
						dialog.hide();
						if (resp.code == 0) {
							//callback(resp.body.businesses);
							if (resp.body.recommends.length){
								pag({totalPage: Math.ceil(resp.body.total_num/static.recommendsPerNum)});
							}
							table(resp.body.recommends);
						} else {
							tip(resp.msg || '查询推荐信息列表出错');
						}
					})
				},
			});

			table.setEvents({
				'click #detail': 'detail',
				'click #delete': 'deletes',
				'click #watch': 'watch'
			},
			{
				deletes: function(e, row) {
					var dialog = wait();
					server.delRecommend({recommendId: row.data.id}, function(resp) {
						dialog.hide();
						if (resp.code == 0) {
							tip('删除成功');
							row.destory();
						} else {
							tip('删除出错');
						}
					})
				},
				detail: function(e, row) {
					page.statusMechine.otherUi();
					recommendsDetailView.render(row.data);
				},
				watch: function(e, row) {
					page.statusMechine.otherUi();
					recommendsUsersView.render(row.data.user);
				}
			})

			table.on('errorSwitch', function(obj){
				//console.log(obj);
				if(obj.type == 'switch'){
					if(obj.page == 1){
						tip('已经是第一页')
					}else{
						tip('已经是最后一页')
					}
				}else if(obj.type == 'submit') {
					if(obj.page === ''){
						tip('不能为空')
					}else{
						tip('页码不正确');
					}
				}
			})

			table.run();
			return table;
		}

		function render(parms, fn) {
			var table = instanceTableList(parms);
			fn(table);		
		}

		return {
			render: render
		}
	})();

	var dustbinRecommendsView = (function() {
		function instanceTableList(parms) {
			var table = new K.PaginationTable({
				ThList: [
					'编号', '备注', '地址', '推荐时间', '状态', '详情', '用户信息', '删除'
				],
				columnNameList: [
					'index',
					'remark','address', 'create_time', 
					function(data) {
						if (data.status == -1){return '已删除';} else if(data.status==0){return '正常';}
					},
					function(data) {
						return  '-';
					},	
					function(data) {
						return  '-';
					},					
					function(data) {
						return  '-';
					}
				],
				//rowClass: 'abc',
				rowClass: function(index) {
					if (index%2 == 0) {
						return 'odd';
					} else {
						return 'even';
					}
				},
				source: function(o, pag, table) {
					dialog = wait();
					parms.page = o.currentPage;
					parms.size = static.recommendsPerNum;
					server.getDelRecommends(parms, function(resp){
						dialog.hide();
						if (resp.code == 0) {
							//callback(resp.body.businesses);
							if (resp.body.recommends.length){
								pag({totalPage: Math.ceil(resp.body.total_num/static.recommendsPerNum)});
							}
							table(resp.body.recommends);
						} else {
							tip(resp.msg || '查询推荐信息列表出错');
						}
					})
				},
			});

			table.on('errorSwitch', function(obj){
				//console.log(obj);
				if(obj.type == 'switch'){
					if(obj.page == 1){
						tip('已经是第一页')
					}else{
						tip('已经是最后一页')
					}
				}else if(obj.type == 'submit') {
					if(obj.page === ''){
						tip('不能为空')
					}else{
						tip('页码不正确');
					}
				}
			})

			table.run();
			return table;
		}

		function render(parms, fn) {
			var table = instanceTableList(parms);
			fn(table);		
		}

		return {
			render: render
		}
	})();
		
	var page = {

		statusMechine: {
			mainUi: function(e) {
				$('#mainPage').show();
				$('#addPage').hide();
			},
			otherUi: function(e) {
				$('#mainPage').hide();
				$('#addPage').show();
			}
		},
		init: function() {
			function getDate(date) {
				var result=[];
				var date = date || new Date();
				result.push(date.getFullYear());
				result.push(date.getMonth()+1);
				result.push(date.getDate());
				return result.join('-');
			}
			this.setEvents();
			page.statusMechine.mainUi();
			new calendar({
		        trigger: '#startTime',
		        range: [null, getDate(new Date((new Date()).getTime()-24*3600000))]
		        //focus: bTime
		    });
			new calendar({
		        trigger: '#endTime',
		        range: [null, getDate()]
		    });		    
			$('#normalList')[0].click();
		},
		setEvents: function() {
			function searchNormal() {
				var parms = {};
				var startTime = $('#startTime').val(),
					endTime = $('#endTime').val();
				startTime && (parms.startTime = startTime + ' 00:00:00');
				endTime && (parms.endTime = endTime + ' 23:59:59');
				recommendsView.render(parms, function(table){
					$('#tableContainer').html(table.El);
				});
			}

			function searchRabish() {
				var parms = {};
				var startTime = $('#startTime').val(),
					endTime = $('#endTime').val();
				startTime && (parms.startTime = startTime + ' 00:00:00');
				endTime && (parms.endTime = endTime + ' 23:59:59');
				dustbinRecommendsView.render(parms, function(table){
					$('#tableContainer').html(table.El);
				});
			}

			function changeTabClass(e) {
				$(e.target).parents('.ui-tab-items').find('li').removeClass('ui-tab-item-current');
				$(e.target).parent().addClass('ui-tab-item-current');				
			}

			$('#normalList').click(function(e){
				if (page.pageStatus != 'normal') {
					page.pageStatus = 'normal';
					changeTabClass(e);
					searchNormal();					
				}
			})

			$('#rabishList').click(function(e){
				if (page.pageStatus != 'rabish') {
					page.pageStatus = 'rabish';
					changeTabClass(e);
					searchRabish();					
				}
			})

			$('#searchBtn').click(function(){
				if (page.pageStatus == 'normal') {
					searchNormal();
				} else if (page.pageStatus == 'rabish') {
					searchRabish();
				}
			})

			$('#return').click(function(e){
				page.statusMechine.mainUi();
			})

		}
	};
	page.init();
})