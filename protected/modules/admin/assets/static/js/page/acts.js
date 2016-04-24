define(function(require, exports, module){

	var $ = require('$'),
		server = require('server'),
		static = require('static'),
		Uploader = require('upload'),
		dialogUi = require('dialogUi'),
		K = require('K'),
		main = require('main');

	var tip = main.tip,
		wait = main.wait;

	var actsDetailView = (function() {

		function HTMLDecode(text)
		{
			var temp = document.createElement("div");
			temp.innerHTML = text;
			var output = temp.innerText || temp.textContent;
			temp = null;
			return output;
		}

		function render(data, fn) {
			var El, html;
			page.statusMechine.otherUi();
			$('#editWrap').hide();
			$('#addWrap').show();
			El = $('<div></div>');
			html = template.render('active_add_Template', data);
			El.html(html);
			El.find('#myEditor').html(HTMLDecode(data.detail_all));
			El.find('#edit').click(function(){
				if (~$.inArray(Number(data.status), [0, 3, 4, 6])) {
					page.enterEdit(data, fn);
				} else {
					tip('该活动无法修改');
				}
			});
			$('#addWrap').html(El);
		}

		return {
			render: render
		}
	})();

	var actsView = (function() {
		/*function getData(parms, callback) {
			parms.page = 1;
			parms.size = 50;
			var dialog = wait();

		}*/

		function instanceTableList(parms) {
			var table = new K.PaginationTable({
				ThList: [
					'编号', '活动名称', '发布时间', '当前状态', '分享数', '兴趣数', '修改', '状态',
					'操作', '二维'
				],
				columnNameList: [
					'index',
					'title',
					function(data) {
						return data.publish_time || '';
					},
					function(data) {
						return static.tStatus[data.t_status];
					},
					'shared_num',
					'loved_num',
					function(data) {
						//if (~$.inArray(Number(data.status), [0, 3, 4, 6])) {
							return '<a href="javascript:;" id="change" class="">详情</a>';
						//} else {
						//	return '-';
						//}
					},
					function(data) {
						var result = '';
						result += '<span style="display:block">' + static.aStatus[data.status] + '</span>';
						if (data.status == 1) {
							result += '<a href="javascript:;" class="ui-button ui-button-sdarkblue table-button" id="pass">通过</a>' +
							'<a href="javascript:;" class="ui-button ui-button-sdarkblue table-button ml10" id="refuse">拒绝</a>'
						}
						return result;
					},
					function(data) {
						var result = '';
						if (data.status == 4) {
							result += '<a href="javascript:;" id="publish" class="mr10">发布</a>';
						} 
						if (data.status == 5) {
							result += '<a href="javascript:;" id="offPublish" class="mr10">下架</a>';
						}
						if (data.status == 0) {
							result += '<a href="javascript:;" id="commit" class="mr10">提交</a>';
						}
						if (data.status == 0 || data.status == 3 || data.status == 4 ||data.status==6){
							result += '<a href="javascript:;" id="delete" class="">删除</a>'
						}
						return  result;
					},
					'qr_code_str'
				],
				//rowClass: 'abc',
				rowClass: function(index) {
					if (index%2 == 0) {
						return 'odd';
					} else {
						return 'even';
					}
				},
				//source: datas,
				source: function(o, pag, table) {console.log(o);
					dialog = wait();
					parms.page = o.currentPage;
					server.getActs(parms, function(resp){
						dialog.hide();
						if (resp.code == 0) {
							//callback(resp.body.businesses);
							if (resp.body.acts.length) {
								pag({totalPage: Math.ceil(resp.body.total_num/static.actsPerNum)});
							}
							table(resp.body.acts);
						} else {
							tip(resp.msg || '查询商户列表出错');
						}
					})
				},
				perPageNums: static.actsPerNum
			});

			table.setEvents({
				'click #change': 'change',
				'click #publish': 'publish',
				'click #offPublish': 'offPublish',
				'click #commit': 'commit',
				'click #delete': 'deletes',
				'click #pass': 'pass',
				'click #refuse': 'refuse'
			},
			{
				change: function(e, row) {
					var dialog = wait();
					server.getActInfo({actId: row.data.id}, function(resp) {
						dialog.hide();
						if (resp.code == 0) {
							//resp.body.act.week_rules=[1,2,5,0];
							actsDetailView.render(resp.body.act, function(o) {
								row.set({status: 1});
								row.refresh();
							});
							/*page.enterEdit(resp.body.act, function(o) {
								row.set({status: 1});
								row.refresh();
							});*/
						} else {
							tip(resp.msg || '查询失败');
						}
					})
				},
				publish: function(e, row) {
					var dialog = wait();
					server.updateActStatus({actId: row.data.id, actStatus: 5}, function(resp) {
						dialog.hide();
						if (resp.code == 0) {
							tip('该活动发布成功');
							row.set({status: 5});
							row.refresh();
						} else {
							tip(resp.msg || '修改状态出错');
						}
					})
				},
				offPublish: function(e, row) {
					var dialog = wait();
					server.updateActStatus({actId: row.data.id, actStatus: 6}, function(resp) {
						dialog.hide();
						if (resp.code == 0) {
							tip('该活动下架成功');
							row.set({status: 6});
							row.refresh();
						} else {
							tip(resp.msg || '修改状态出错');
						}
					})
				},
				commit: function(e, row) {
					var dialog = wait();
					server.updateActStatus({actId: row.data.id, actStatus: 1}, function(resp) {
						dialog.hide();
						if (resp.code == 0) {
							tip('该活动提交成功');
							row.set({status: 1});
							row.refresh();
						} else {
							tip(resp.msg || '修改状态出错');
						}
					})
				},
				deletes: function(e, row) {
					var dialog = wait();
					server.delAct({actId: row.data.id}, function(resp) {
						dialog.hide();
						if (resp.code == 0) {
							tip('该活动删除成功');
							row.destory();
						} else {
							tip(resp.msg || '删除出错');
						}
					})
				},
				pass: function(e, row) {
					var dialog = wait();
					server.updateActStatus({actId: row.data.id, actStatus: 4}, function(resp) {
						dialog.hide();
						if (resp.code == 0) {
							tip('该活动已经通过审核');
							row.set({status: 4});
							row.refresh();
						} else {
							tip(resp.msg || '修改状态出错');
						}
					})
				},
				refuse: function(e, row) {
					var dialog = wait();
					server.updateActStatus({actId: row.data.id, actStatus: 3}, function(resp) {
						dialog.hide();
						if (resp.code == 0) {
							tip('该活动未通过审核');
							row.set({status: 3});
							row.refresh();
						} else {
							tip(resp.msg || '修改状态出错');
						}
					})
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
			//getData(parms, function(datas){
				parms.page = 1;
				parms.size = static.actsPerNum;
				var table = instanceTableList(parms);
				fn(table);
			//})			
		}


		return {
			render: render
		}
	})();

	var dustbinActsView = (function() {

		function instanceTableList(parms) {
			var table = new K.PaginationTable({
				ThList: [
					'编号', '活动名称', '发布时间', '当前状态', '分享数', '兴趣数', '修改', '状态',
					'操作', '二维'
				],
				columnNameList: [
					'index',
					'title',
					function(data) {
						return data.publish_time || '';
					},
					function(data) {
						return static.tStatus[data.t_status];
					},
					'shared_num',
					'loved_num',
					function(data) {
						return '-';
					},
					function(data) {
						return static.aStatus[data.status];
					},
					function(data) {
						var result = '-';
						return  result;
					},
					'qr_code_str'
				],
				//rowClass: 'abc',
				rowClass: function(index) {
					if (index%2 == 0) {
						return 'odd';
					} else {
						return 'even';
					}
				},
				//source: datas,
				source: function(o, pag, table) {
					dialog = wait();
					parms.page = o.currentPage;
					server.getDelActs(parms, function(resp){
						dialog.hide();
						if (resp.code == 0) {
							//callback(resp.body.businesses);
							if (resp.body.acts.length) {
								pag({totalPage: Math.ceil(resp.body.total_num/static.adminListPerNum)});
							}
							table(resp.body.acts);
						} else {
							tip(resp.msg || '查询商户列表出错');
						}
					})
				},
				perPageNums: static.adminListPerNum
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
				parms.page = 1;
				parms.size = static.actsPerNum;;
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
			var saveCallback;
			this.setEvents();
			page.statusMechine.mainUi();

			var activeEdit = require('activeEdit')();
			K.Observe.make(activeEdit);
			activeEdit.init(function(el){
				$('#editWrap').html(el);
			})
			activeEdit.on('save', function() {
				saveCallback && saveCallback();
			})

			activeEdit.on('add', function() {
				$('#searchBtn').trigger('click');
			})

			this.enterCreate = function(o) {
				page.statusMechine.otherUi();
				$('#addWrap').hide();
				$('#editWrap').show();
				activeEdit.load('create', o);		
			}
			this.enterEdit = function(data, fn) {
				saveCallback = fn;
				page.statusMechine.otherUi();
				$('#addWrap').hide();
				$('#editWrap').show();
				activeEdit.load('edit', data);
			}

			this.pageStatus = 'normal';
			$('#searchBtn').trigger('click');

			// 百度地图API功能


		},
		setEvents: function() {
			$('#addBtn').click(function() {
				var element =   '<div class="busi-tip-w"><label for="" class="">请先选择一个商家:</label>' +
								'<div class="busi-area" id="businessArea"><span class="load-wrap"></span></div></div>';
				var El = $(element);	
				var dia = dialogUi.content(El[0]);
				dia.show();
				//$('#businessWrap').html(El[0]);
				server.getBusinesses({page:1, size: 10000, keyWords: ''}, function(resp){
					if (resp.code == 0) {
						var businesses = resp.body.businesses;
						var outer = $('<ul class="ui-list ui-list-graylink busi-outer"></ul>');
						$.each(businesses, function(i, busi){
							var li = ' <li id='+ busi.id +' class="ui-list-item"><a href="#">'+ busi.name +'</a></li>';
							outer.append(li);
						});
						El.find('#businessArea').html(outer);
						El.find('ul').on('click', 'li',function(e){
							dia.hide();
							page.enterCreate({businessId: $(e.target).closest('li').attr('id')});
						});
					} else {
						tip(resp.msg || '商家列表失败');
					}
				})
			})

			function searchNormal() {
				var parms = {};
				var keyWords = $('#keyword').val();
				var tStatus = $('#activeSelect #spanText').attr('value'),
					actStatus = $('#statusSelect #spanText').attr('value');
				parms.keyWords = keyWords;
				if (tStatus!='all') parms.tStatus = tStatus;
				if (actStatus!='all') parms.actStatus = actStatus;
				actsView.render(parms, function(table){
					$('#tableContainer').html(table.El);
				});				
			}

			function searchRabish() {
				var parms = {};
				var keyWords = $('#keyword').val();
				var tStatus = $('#activeSelect #spanText').attr('value'),
					actStatus = $('#statusSelect #spanText').attr('value');
				parms.keyWords = keyWords;
				if (tStatus!='all') parms.tStatus = tStatus;
				if (actStatus!='all') parms.actStatus = actStatus;
				dustbinActsView.render(parms, function(table){
					$('#tableContainer').html(table.El);
				});
			}

			function changeTabClass(e) {
				$(e.target).parents('.ui-tab-items').find('li').removeClass('ui-tab-item-current');
				$(e.target).parent().addClass('ui-tab-item-current');				
			}

			$('#searchBtn').click(function(){
				if (page.pageStatus == 'normal') {
					searchNormal();
				} else if (page.pageStatus == 'rabish') {
					searchRabish();
				}
			})

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

			/*$('#dustbinBtn').click(function(){
				var parms = {};
				var keyWords = $('#keyword').val();
				var tStatus = $('#activeSelect #spanText').attr('value'),
					actStatus = $('#statusSelect #spanText').attr('value');
				parms.keyWords = keyWords;
				if (tStatus!='all') parms.tStatus = tStatus;
				if (actStatus!='all') parms.actStatus = actStatus;
				dustbinActsView.render(parms, function(table){
					$('#tableContainer').html(table.El);
				});
			})*/

			$('#addPage').on('click', '#return', function(){
				page.statusMechine.mainUi();
			})
		}


	};
	page.init();
})