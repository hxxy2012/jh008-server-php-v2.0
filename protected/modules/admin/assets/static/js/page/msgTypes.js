define(function(require, exports, module){

	var $ = require('$'),
		server = require('server'),
		static = require('static'),
		Uploader = require('upload'),
		K = require('K'),
		main = require('main');

	var tip = main.tip,
		wait = main.wait;

	var msgTypeCreateView = (function(){

		function render() {
			var El = $('<div></div>');
			El.html(template.render('msg_create_template'));
			$('#otherWrap').html(El);
			El.on('click', '#addBtn', function(){
				var name = $.trim(El.find('#name').val());
				if (!name) {
					tip ('消息类型不能为空');
				} else {
					var dialog = wait();
					server.addMsgType({name: name}, function(resp) {
						dialog.hide();
						if (resp.code == 0) {
							page.statusMechine.mainUi();
							$('#searchBtn').trigger('click');
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

	var msgTypeDetailView = (function(){

		function render(data, callback) {
			var El = $('<div></div>');
			El.html(template.render('msg_detail_template', data));
			$('#otherWrap').html(El);
			El.find('#editBtn').bind('click', function(e){
				msgTypeEditView.render(data, callback);
			});
		}

		return {
			render: render
		}
	})();

	var msgTypeEditView = (function(){
		function render(data, callback) {
			page.statusMechine.otherUi();
			var El = $('<div></div>');
			var html = template.render('msg_edit_template', data);
			El.html(html);
			$('#otherWrap').html(El);
			// bind-event
			El.find('#saveBtn').click(function(){
				var name = $.trim(El.find('#name').val());
				if (!name) {
					tip ('内容不能为空');
				} else {
					var dialog = wait();
					server.updateMsgType({name: name, msgTypeId: data.id}, function(resp) {
						dialog.hide();
						if (resp.code == 0) {
							page.statusMechine.mainUi();
							callback({name: name});
						} else {
							tip (resp.msg || '消息类型修改失败');
						}
					})
				}
			})
		}

		return {
			render: render
		}
	})();

	var tagsView = (function() {
		function getData(parms, callback) {
			var dialog = wait();
			server.getMsgTypes(parms, function(resp){
				dialog.hide();
				if (resp.code == 0) {
					callback(resp.body.types);
				} else {
					tip(resp.msg || '查询商户列表出错');
				}
			})
		}

		function instanceTableList(datas) {
			var table = new K.PaginationTable({
				ThList: [
					'编号', '消息类型名称', '当前状态', '详情', '删除'
				],
				columnNameList: [
					'index',
					'name',
					function(data) {
						return data.status == -1 ? '删除' : data.status == 0 ? '正常' : '';
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
					server.delMsgType({msgTypeId: row.data.id}, function(resp) {
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
					msgTypeDetailView.render(row.data, function(o){
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

	var dustbinTagsView = (function() {
		function getData(parms, callback) {
			var dialog = wait();
			server.getDelMsgTypes(parms, function(resp){
				dialog.hide();
				if (resp.code == 0) {
					callback(resp.body.tags);
				} else {
					tip(resp.msg || '查询商户列表出错');
				}
			})
		}

		function instanceTableList(datas) {
			var table = new K.PaginationTable({
				ThList: [
					'编号', '消息类型名称', '当前状态', '详情', '删除'
				],
				columnNameList: [
					'index',
					'name',
					function(data) {
						return data.status == -1 ? '删除' : data.status == 0 ? '正常' : '';
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
			this.setEvents();
			page.statusMechine.mainUi();

			this.pageStatus = 'normal';
			$('#searchBtn').trigger('click');
		},
		setEvents: function() {
			function searchNormal() {
				var parms = {};
				var keyWords = $('#keyword').val();
				parms.keyWords = keyWords;
				tagsView.render(parms, function(table){
					$('#tableContainer').html(table.El);
				});
			}

			function searchRabish() {
				var parms = {};
				var keyWords = $('#keyword').val();
				parms.keyWords = keyWords;
				dustbinTagsView.render(parms, function(table){
					$('#tableContainer').html(table.El);
				});
			}

			function changeTabClass(e) {
				$(e.target).parents('.ui-tab-items').find('li').removeClass('ui-tab-item-current');
				$(e.target).parent().addClass('ui-tab-item-current');				
			}
			
			$('#addBtn').click(function() {
				page.statusMechine.otherUi();
				msgTypeCreateView.render();
			})

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

			$('#addPage').on('click', '#return', function(){
				page.statusMechine.mainUi();
			})


		}


	};
	page.init();
})