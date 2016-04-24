define(function(require, exports, module){
	// 回忆
	var $ = require('$'),
		common = require('common'),
		dialogUi = require('dialogUi'),
		main = require('main'),
		server = require('server'),
		static = require('static'),
		K = require('K'),
		Uploader = require('upload'),
		InformationEdit = require('informationEdit');
		
	var pageManager = common.pageManager,
		PagTable = common.PagTable,
		myValidate = common.myValidate,
		regexp = myValidate.regexp;

	var tip = main.tip,
		wait = main.wait,
		roleConfig = static.roleConfig;

	var typeId = 2; // 资讯相关接口所传参数， 回忆：2

	// 初始化标签选择列表
	main.initActTagsSelect();

	// 基础数据视图
	var baseDataView = (function(){ // 单个活动收藏数 | 单个活动分享数 | 评论
		var El = $('<div></div>');
		var serverConfig = {
			share: server.sharesNews,
			collection: server.lovsNews,
			comment: server.commentsNews
		};

		function renderCommonTable(parms, type) {
			return table = PagTable({
				ThList: ['编号', '是否达人', '昵称', '姓名', '电话', '通信地址'],
				columnNameList: [
					'index', 
					function(data) {
						return data.is_vip == 1 ? '是' : '否';
					}
					, 'nick_name', 'real_name', 'contact_phone', 'address'
				],
				source: function(o, pag, table) {
					dialog = wait();
					parms.page = o.currentPage;
					parms.size = static.actsPerNum;
					serverConfig[type](parms, function(resp){
						/*resp = {
							code: 0,
							body: {
								s_users: [{nick_name: 3}],
								total_num: 1
							}
						};*/
						dialog.hide();
						if (resp.code == 0) {
							if (resp.body.s_users.length) {
								pag({totalPage: Math.ceil(resp.body.total_num/static.actsPerNum)});
							}
							table(resp.body.s_users);
						} else {
							tip(resp.msg || '查询数据列表出错');
						}
					})
				},
				perPageNums: static.actsPerNum
			});
		} 
		function renderCommentTable(parms) {
			return table = PagTable({
				ThList: ['编号', '是否达人', '昵称', '姓名', '电话', '通信地址', '签到时间'],
				columnNameList: [
					'index', 
					function(data) {
						return data.is_vip == 1 ? '是' : '否';
					}
					, 'nick_name', 'real_name', 'contact_phone', 'address', 'checkin_time'
				],
				source: function(o, pag, table) {
					dialog = wait();
					parms.page = o.currentPage;
					parms.size = static.actsPerNum;
					//serverConfig.comment(parms, function(resp){
						resp = {
							code: 0,
							body: {
								comments: [{nick_name: 3}],
								total_num: 1
							}
						};
						dialog.hide();
						if (resp.code == 0) {
							if (resp.body.comments.length) {
								pag({totalPage: Math.ceil(resp.body.total_num/static.actsPerNum)});
							}
							table(resp.body.comments);
						} else {
							tip(resp.msg || '查询数据列表出错');
						}
					//})
				},
				perPageNums: static.actsPerNum
			});
		} 		
		function _render(parms, type) {
			var table;
			if (~$.inArray(type, ['share', 'collection'])) {
				table = renderCommonTable(parms, type);
			} else if(type == 'comment') {
				table = renderCommentTable(parms);
			}
			El.html(table.El);
			pageManager.render('secondPanel', El);
		}
		return {
			render: _render
		}
	})()

	// 查看回忆详情视图
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
			El = $('<div></div>');
			html = template.render('memory_show_Template', data);
			El.html(html);
			pageManager.render('secondPanel', El);
			El.find('#myEditor').html(HTMLDecode(data.detail));
		}

		return {
			render: render
		}
	})()

	// 回忆列表视图
	var informationListView = (function() {
		
		function renderTable(parms) {
			return table = PagTable({
				ThList: ['编号', '资讯名称', '发布时间', '分享数','收藏数','评论数', roleConfig.changeText(), '上/下架',
				 '删除', '备注'],
				columnNameList: [
					'index', 
					'title', 'publish_time',
					function(data){
						return '<a class="" href="javascript:;" id="shareNum">'+ data.shared_num +'</a>';
					},
					function(data){
						return '<a class="" href="javascript:;" id="collectionNum">'+ data.loved_num +'</a>';
					},
					function(data){
						return '<a class="" href="javascript:;" id="commentNum">'+ data.comment_num +'</a>';
					},
					function(data){
						return static.watchText();
					},
					function(data){
						return static.statusText(data.status);
					},
					function(data){
						return '<a class="" href="javascript:;" id="delete">删除</a>';
					},
					function(data){
						return '<a class="" href="javascript:;" id="remark">备注</a>';
					}
				],
				source: function(o, pag, table) {
					dialog = wait();
					parms.page = o.currentPage;
					parms.size = static.informationListPerNum;
					server.news(parms, function(resp){
						dialog.hide();
						if (resp.code == 0) {
							if (resp.body.news.length) {
								pag({totalPage: Math.ceil(resp.body.total_num/static.informationListPerNum)});
							}
							table(resp.body.news);
						} else {
							tip(resp.msg || '查询数据列表出错');
						}
					})
				},
				perPageNums: static.informationListPerNum
			}, {
				'click #shareNum': 'shareNum',
				'click #collectionNum': 'collectionNum',
				'click #commentNum': 'commentNum',
				'click #update': 'update',
				'click #watch': 'watch',
				'click #upShelf': 'upShelves',
				'click #downShelf': 'downShelf',
				'click #remark': 'remark'
			}, {
				shareNum: function(e, row) {
					baseDataView.render({cityId: page.cityId, newsId: row.data.id}, 'share');
				},
				collectionNum: function(e, row) {
					baseDataView.render({cityId: page.cityId, newsId: row.data.id}, 'collection');
				},
				commentNum: function(e, row) {
					baseDataView.render({cityId: page.cityId, newsId: row.data.id}, 'comment');
				},
				update: function(e, row) {
					var dialog = wait();
					server.newsInfo({newsId: row.data.id}, function(resp){
						dialog.destroy();
						if (resp.code == 0) {
							page.enterEdit(resp.body);
						} else {
							tip ('获取资讯详情失败');
						}
					})
				},
				watch: function(e, row) {
					var dialog = wait();
					server.newsInfo({newsId: row.data.id}, function(resp){
						dialog.destroy();
						if (resp.code == 0) {
							actsDetailView.render(resp.body);
						} else {
							tip ('获取资讯详情失败');
						}
					})
				},
				upShelves: function(e, row) {
					var dialog = wait();
					server.updateStatusNews({newsId: row.data.id, status: 5}, function(resp){
						dialog.destroy();
						if (resp.code == 0) {
							tip('上架成功');
							row.set({status: 5});
							row.refresh();
						} else {
							tip('上架失败');
						}
					})
				},
				downShelf: function(e, row) {
					var dialog = wait();
					server.updateStatusNews({newsId: row.data.id, status: 6}, function(resp){
						dialog.destroy();
						if (resp.code == 0) {
							tip('下架成功');
							row.set({status: 6});
							row.refresh();
						} else {
							tip('下架失败');
						}
					})
				},
				remark: function(e, row) {
					var remark = require('remark');
					remark(2, row.data.id, function(el){
						pageManager.render('secondPanel', el);
					});
				}
			});
		}	 
		function _render(parms) {
			var table = renderTable(parms);
			pageManager.render('listPanel', table.El);
		}
		return { render: _render };
	})()

	// 回忆回收站列表视图
	var informationRabishListView = (function() {
		function _render() {}
		return { render: _render };
	})

	function setEvents() {
		$('#create').click(function(){
			page.enterCreate();
		})
		$('#return').click(function(){
			pageManager.show('listPanel');
		})
		$('#searchBtn').click(function(){
			var parms = {};
			var keyWords = $.trim($('#keyword').val()),
				tagId = $('#tagSelect #spanText').attr('value');
			if (tagId == 'all') {
				parms.tagId = '';
			} else {
				parms.tagId = tagId;
			}
			parms.keyWords = keyWords;
			parms.cityId = page.cityId;
			parms.typeId = typeId;
			informationListView.render(parms);
		})
	}

	// 入口
	var page = {
		init: function() {
			page.cityId = main.getCityId();
			setEvents();
			pageManager.hide();
			pageManager.add({name: 'listPanel', el: $('#listCon'), parent: $('#mainPanel')});
			pageManager.add({name: 'secondPanel', el: $('#operateWrap'), parent: $('#secondPanel')});
			pageManager.add({name: 'editPanel', el: $('#createWrap'), parent: $('#secondPanel')});
			//informationListView.render({cityId: page.cityId, typeId: typeId});
			$('#searchBtn').trigger('click');

			this.enterCreate = function(o) {
				pageManager.show('editPanel');
				activeEdit.load('create', o);		
			}
			this.enterEdit = function(data, fn) {
				pageManager.show('editPanel');
				activeEdit.load('edit', data);
			}

			var activeEdit = InformationEdit({
				template: 'memoryCreateTemplate',
				typeId: typeId,
				cityId: page.cityId
			});
			
			activeEdit.init(function(el){
				$('#createWrap').html(el);
			})
			activeEdit.on('save', function(newsId) {
				var dialog = wait();
				pageManager.show('listPanel');
				server.updateStatusNews({newsId: newsId, status: 5}, function(resp){
					dialog.destroy();
					if (resp.code == 0) {
						$('#searchBtn').click();
					} else {
						tip('修改状态出错');
					}
				})
			})
			activeEdit.on('add', function(newsId) {
				var dialog = wait();
				pageManager.show('listPanel');
				server.updateStatusNews({newsId: newsId, status: 5}, function(resp){
					dialog.destroy();
					if (resp.code == 0) {
						$('#searchBtn').click();
					} else {
						tip('修改状态出错');
					}
				})
			})
		}
	}

	page.init();

})