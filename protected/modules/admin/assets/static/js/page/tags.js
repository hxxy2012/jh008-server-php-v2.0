define(function(require, exports, module){

	var $ = require('$'),
		server = require('server'),
		static = require('static'),
		Uploader = require('upload'),
		K = require('K'),
		main = require('main');

	var tip = main.tip,
		wait = main.wait;

	var tagsCreateView = (function(){

		function render() {
			var El = $('<div></div>');
			El.html(template.render('tags_add_template', {}));
			$('#otherWrap').html(El);
			El.on('click', '#saveBtn', function(){
				var name = $.trim(El.find('#name').val());
				if (!name) {
					tip ('标签不能为空');
				} else {
					var dialog = wait();
					server.addTag({name: name}, function(resp) {
						dialog.hide();
						if (resp.code == 0) {
							page.statusMechine.mainUi();
							$('#searchBtn').trigger('click');
						} else {
							tip (resp.msg || '标签创建失败');
						}
					})
				}
			});
		}

		return {
			render: render
		}
	})();

	var tagsEditView = (function(){
		function render(data, callback) {
			page.statusMechine.otherUi();
			var El = $('<div></div>');
			var html = template.render('tags_edit_template', data);
			El.html(html);
			$('#otherWrap').html(El);
			// bind-event
			El.find('#editSaveBtn').click(function(){
				var name = $.trim(El.find('#name').val());
				if (!name) {
					tip ('标签内容不能为空');
				} else {
					var dialog = wait();
					server.updateTag({name: name, tagId: data.id}, function(resp) {
						dialog.hide();
						if (resp.code == 0) {
							page.statusMechine.mainUi();
							callback({name: name});
						} else {
							tip (resp.msg || '标签修改失败');
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
			server.getTags(parms, function(resp){
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
					'编号', '标签名称', '状态', '修改时间', '未结束活动数量', '使用次数','操作'
				],
				columnNameList: [
					'index',
					'name',
					function(data) {
						return data.status == -1 ? '删除' : data.status == 0 ? '正常' : '';
					},
					'update_time',
					'count',
					function() {
						return 0;
					},
					function(data) {
						return  '<a href="javascript:;" id="edit" class="mr10">编辑</a>' +
								'<a href="javascript:;" id="delete" class="">删除</a>';
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
				perPageNums: static.tagsListPerNum
			});

			table.setEvents({
				'click #edit': 'edit',
				'click #delete': 'delete',
			},
			{
				delete: function(e, row) {
					var dialog = wait();
					server.delTag({tagId: row.data.id}, function(resp) {
						dialog.hide();
						if (resp.code == 0) {
							tip('标签 ' + row.data.name + ' 删除成功');
							row.destory();
						} else {
							tip(resp.msg || '删除出错');
						}
					})
				},
				edit: function(e, row) {
					tagsEditView.render(row.data, function(o){
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
			server.getDelTags(parms, function(resp){
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
					'编号', '标签名称', '状态', '修改时间', '未结束活动数量', '使用次数','操作'
				],
				columnNameList: [
					'index',
					'name',
					function(data) {
						return data.status == -1 ? '删除' : data.status == 0 ? '正常' : '';
					},
					'update_time',
					'count',
					function() {
						return 0;
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
				source: datas,
				perPageNums: static.tagsListPerNum
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
				tagsCreateView.render();
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