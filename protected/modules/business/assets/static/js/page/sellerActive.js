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
		function getData(parms, callback) {
			var dialog = wait();
			server.getActivity(parms, function(resp){
				dialog.hide();
				if (resp.code == 0) {
					callback(resp.body.acts);
				} else {
					tip(resp.msg || '查询商户列表出错');
				}
			})
		}

		function instanceTableList(datas) {
			var table = new K.PaginationTable({
				ThList: [
					'编号', '活动名称', '发布时间', '当前状态', {text:'分享数', isOrderBy: true}, {text: '兴趣数', isOrderBy: true}, '详情', '状态',
					'操作', '二维'
				],
				columnNameList: [
					'index',
					'title',
					'publish_time',
					function(data) {
						return static.tStatus[data.t_status];
					},
					'shared_num',
					'loved_num',
					function(data) {
						return '<a href="javascript:;" id="change" class="">详情</a>';
					},
					function(data) {
						return static.aStatus[data.status];
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
				rowClass: function(index) {
					if (index%2 == 0) {
						return 'odd';
					} else {
						return 'even';
					}
				},
				source: datas,
				perPageNums: 20,
			});

			table.setEvents(
				{
					'click #change': 'change',
					'click #publish': 'publish',
					'click #offPublish': 'offPublish',
					'click #delete': 'delete',
					'click #commit': 'commit'
				},

				{
					commit: function(e, row) {
						var dialog = wait();
						server.commitActivity({actId: row.data.id}, function(resp) {
							dialog.hide();
							if (resp.code == 0) {
								tip('活动提交成功');
								row.set({status: 1});
								row.refresh();
							} else {
								tip(resp.msg || '提交活动出错');
							}
						})
					},
					publish: function(e, row) {
						var dialog = wait();
						server.publishActivity({actId: row.data.id}, function(resp) {
							dialog.hide();
							if (resp.code == 0) {
								tip('活动发布成功');
								row.set({status: 5});
								row.refresh();
							} else {
								tip(resp.msg || '发布出错');
							}
						})
					},
					offPublish: function(e, row) {
						var dialog = wait();
						server.offpublishActivity({actId: row.data.id}, function(resp){
							dialog.hide();
							if (resp.code == 0) {
								tip('活动下架成功');
								row.set({status: 6});
								row.refresh();
							} else {
								tip(resp.msg || '下架出错');
							}
						})
					},
					delete: function(e, row) {
						var dialog = wait();
						server.delActivity({actId: row.data.id}, function(resp) {
							dialog.hide();
							if (resp.code == 0) {
								tip('活动删除成功');
								row.set({status: -1});
								row.refresh();
								row.destory();
							} else {
								tip(resp.msg || '删除出错');
							}
						})
					},
					change: function(e, row) {
						var dialog = wait();
						server.getActivityDetail({actId: row.data.id}, function(resp) {
							dialog.hide();
							if (resp.code == 0) {
								actsDetailView.render(resp.body.act, function(o) {
									row.set({status: 1});
									row.refresh();
								});
							} else {
								tip(resp.msg || '查询失败');
							}
						})
						
					},
					edit: function(e, row) {
						row.set({orderId: 'liubei'});
						row.render();
					},
					show: function(e, row) {
						alert(row.data.orderId);
					}
				}
			);

			table.on('errorSwitch', function(obj){
					console.log(obj);
					if(obj.type == 'switch'){
						if(obj.page == 1){
							alert('已经是第一页')
						}else{
							alert('已经是最后一页')
						}
					}else if(obj.type == 'submit') {
						if(obj.page === ''){
							alert('不能为空')
						}else{
							alert('页码不正确');
						}
					}
			})

			table.run();
			return table;
		}

		function render(parms, fn) {
			getData(parms, function(datas){
				var table = instanceTableList(datas);
				fn(table);
			})			
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

			$('#searchBtn')[0].click();

			// 百度地图API功能
		},
		setEvents: function() {
			$('#addBtn').click(function() {
				page.enterCreate();
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

			$('#searchBtn').click(function(){
				searchNormal();
			})

			$('#addPage').on('click', '#return', function(){
				page.statusMechine.mainUi();
			})
		}


	};
	page.init();
})