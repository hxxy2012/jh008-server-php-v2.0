define(function(require, exports, module){

	var $ = require('$'),
		server = require('server'),
		static = require('static'),
		Uploader = require('upload'),
		K = require('K'),
		main = require('main');

	var tip = main.tip,
		wait = main.wait;

	var pushTypeCreateView = (function(){

		function render() {
			var El = $('<div></div>');
			El.html(template.render('pushType_create_template'));
			$('#otherWrap').html(El);
			El.on('click', '#addBtn', function(){
				var name = $.trim(El.find('#name').val());
				if (!name) {
					tip ('push类型不能为空');
				} else {
					var dialog = wait();
					server.addPushType({name: name}, function(resp) {
						dialog.hide();
						if (resp.code == 0) {
							page.statusMechine.mainUi();
							page.enterSearch();
						} else {
							tip (resp.msg || '消息类型创建失败');
						}
					})
				}
			});
		}

		return {
			render: render
		}
	})();

	var pushTypeDetailView = (function(){

		function render(data, callback) {
			var El = $('<div></div>');
			El.html(template.render('pushType_detail_template', data));
			$('#otherWrap').html(El);
			El.find('#editBtn').bind('click', function(e){
				pushTypeEditView.render(data, callback);
			});
		}

		return {
			render: render
		}
	})();

	var pushTypeEditView = (function(){
		function render(data, callback) {
			page.statusMechine.otherUi();
			var El = $('<div></div>');
			var html = template.render('pushType_edit_template', data);
			El.html(html);
			$('#otherWrap').html(El);
			// bind-event
			El.find('#saveBtn').click(function(){
				var name = $.trim(El.find('#name').val());
				if (!name) {
					tip ('push类型不能为空');
				} else {
					var dialog = wait();
					server.updatePushType({name: name, pushTypeId: data.id}, function(resp) {
						dialog.hide();
						if (resp.code == 0) {
							page.statusMechine.mainUi();
							callback({name: name});
						} else {
							tip (resp.msg || 'push类型修改失败');
						}
					})
				}
			})
		}

		return {
			render: render
		}
	})();

	var pushTypesView = (function() {
		function getData(callback) {
			var dialog = wait();
			server.getPushTypes(function(resp){
				dialog.hide();
				if (resp.code == 0) {
					callback(resp.body.types);
				} else {
					tip(resp.msg || '查询push列表出错');
				}
			})
		}

		function instanceTableList(datas) {
			var table = new K.PaginationTable({
				ThList: [
					'推送名称', '推送状态', '推送详情', '删除'
				],
				columnNameList: [
					'name',
					function(data) {
						return data.status == -1 ? '已删除' : data.status == 0 ? '正常' : '';
					},
					function(data) {
						return  '<a href="javascript:;" id="detail" class="">详情</a>';
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
				source: datas,
				perPageNums: static.msgsTypeNum
			});

			table.setEvents({
				'click #detail': 'detail',
				'click #delete': 'deletes',
			},
			{
				deletes: function(e, row) {
					var dialog = wait();
					server.delPushType({pushTypeId: row.data.id}, function(resp) {
						dialog.hide();
						if (resp.code == 0) {
							tip(row.data.name + ' 删除成功');
							row.destory();
						} else {
							tip(resp.msg || '删除出错');
						}
					})
				},
				detail: function(e, row) {
					page.statusMechine.otherUi();
					pushTypeDetailView.render(row.data, function(o){
						row.set(o);
						row.refresh();
					});
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

		function render(fn) {
			getData(function(datas){
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
			this.setEvents();
			page.statusMechine.mainUi();
			page.enterSearch();
		},
		setEvents: function() {
			page.enterSearch = function() {
				pushTypesView.render(function(table){
					$('#tableContainer').html(table.El);
				});
			}

			$('#addBtn').click(function(){
				page.statusMechine.otherUi();
				pushTypeCreateView.render();
			})

			$('#addPage').on('click', '#return', function(){
				page.statusMechine.mainUi();
			})


		}


	};
	page.init();
})