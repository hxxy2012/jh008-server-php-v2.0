define(function(require, exports, module){
	// 用户标签管理 --待接口完善，修改相应接口.
	var $ = require('$'),
		common = require('common'),
		dialogUi = require('dialogUi'),
		main = require('main'),
		server = require('server'),
		static = require('static'),
		Authority = 'admin'; // cityAdmin | citycaozuoyuan | 

	var pageManager = common.pageManager,
		PagTable = common.PagTable,
		myValidate = common.myValidate,
		regexp = myValidate.regexp;

	var tip = main.tip,
		wait = main.wait;

	// 用户标签添加视图
	var createTagsView = (function() {
		
		var El = $('<div></div>');
		function _render(fn) {
			var html = template.render('tags_add_template');
			El.html(html);
			pageManager.render('secondPanel', El);
			El.find('#add').click(function(){
				var name = $.trim(El.find('#name').val());
				if (!name) {
					tip ('标签名不能为空');
				} else {
					server.addTagUser({name: name}, function(resp){
						if (resp.code == 0) {
							pageManager.show('listPanel');
							fn && fn();
						} else {
							tip('标签添加失败');
						}
					})
				}
			})
		}
		return { render: _render };
	})()

	// 用户标签更新视图
	var updateTagsView = (function() {
		var El = $('<div></div>');
		function _render(data, fn) {
			var html = template.render('tags_edit_template', data);
			El.html(html);
			pageManager.render('secondPanel', El);
			El.find('#update').click(function(){
				var name = $.trim(El.find('#name').val());
				if (!name) {
					tip ('标签名不能为空');
				} else {
					var dialog = wait();
					server.updateTagUser({tagId: data.id, name: name}, function(resp){
						dialog.destroy();
						if (resp.code == 0) {
							pageManager.show('listPanel');
							fn && fn({name: name});
						} else {
							tip('标签修改失败');
						}
					})
				}
			})			
		}
		return { render: _render };
	})()

	// 用户标签列表视图
	var tagListView = (function() {
		function renderTable(parms) {
			return table = PagTable({
				ThList: ['编号', '标签名称', '修改时间', '当前状态', '修改','备注'],
				columnNameList: [
					'index', 'name', 'modify_time',
					function(data) {
						return data.status == -1 ? '已删除' : data.status == 0 ? '正常' : 
								data.status == 1 ? '已下架' : '';
					},
					function(){
						return '<a class="" href="javascript:;" id="update">修改</a>';
					},
					function(){
						return '<a class="" href="javascript:;" id="remark">备注</a>';
					}
				],
				source: function(o, pag, table) {
					dialog = wait();
					parms.page = o.currentPage;
					parms.size = static.actTagsPerNum;
					server.tagsUser(parms, function(resp){
						dialog.hide();
						if (resp.code == 0) {
							if (resp.body.tags.length) {
								pag({totalPage: Math.ceil(resp.body.total_num/static.actTagsPerNum)});
							}
							table(resp.body.tags);
						} else {
							tip(resp.msg || '查询数据列表出错');
						}
					})
				},
				perPageNums: static.actTagsPerNum
			}, {
				'click #update': 'update',
				'click #remark': 'remark'
			}, {
				remark: function(e, row) {
					var remark = require('remark');
					remark(5, row.data.id, function(el){
						pageManager.render('secondPanel', el);
					});
				},
				update: function(e, row) {
					updateTagsView.render(row.data, function(o){
						row.set(o);
						row.refresh();
					});
				}
			});
		}	 
		function _render(parms) {
			var table = renderTable(parms || {});
			pageManager.render('listPanel', table.El);
		}
		return { render: _render };
	})()

	// 用户标签回收站列表视图
	var tagRabishListView = (function() {
		function _render() {}
		return { render: _render };
	})

	// 页面事件绑定
	function setEvents() {
		$('#create').click(function(){
			createTagsView.render(function(){
				$('#searchBtn').click();
			});
		})
		$('#return').click(function(){
			pageManager.show('listPanel');
		})
		$('#searchBtn').click(function(){
			var keyWords = $.trim($('#keyword').val());
			tagListView.render({keyWords: keyWords});
		})
	}

	// 页面入口
	var page = {
		init: function() {
			pageManager.add({name: 'secondPanel', el: $('#operateWrap'), parent: $('#secondPanel')});
			pageManager.add({name: 'listPanel', el: $('#adminListCon'), parent: $('#mainPanel')});
			pageManager.hide();
			tagListView.render({});
			setEvents();
		}
	}
	page.init();

})