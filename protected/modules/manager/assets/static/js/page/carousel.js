define(function(require, exports, module){
	// 轮播列表
	var $ = require('$'),
		common = require('common'),
		dialogUi = require('dialogUi'),
		main = require('main'),
		server = require('server'),
		static = require('static'),
		Uploader = require('upload'),
		InformationEdit = require('informationEdit'); 

	var pageManager = common.pageManager,
		PagTable = common.PagTable,
		myValidate = common.myValidate,
		regexp = myValidate.regexp,
		util = common.util,
		roleConfig = static.roleConfig;

	var tip = main.tip,
		wait = main.wait,
		typeId = 5; 

	// 查看轮播图视图
	var carouselDetailView = (function() {
		function render(data, fn) {
			var El, html;
			El = $('<div></div>');
			html = template.render('carousel_show_Template', data);
			El.html(html);
			pageManager.render('cm', El);
			El.find('#myEditor').html(util.htmlDecode(data.detail));
		}
		return {
			render: render
		}
	})()

	// 轮播图列表视图
	var carouselListView = (function() {
		var carouselDatas;
		function renderTable(parms) {
			return table = PagTable({
				ThList: ['编号', '缩略图', '发布时间', roleConfig.changeText(), '上/下架',
				  '备注'],
				columnNameList: [
					'index', 'title', 'publish_time',
					function(){
						return static.watchText();
					},
					function(data){
						return static.statusText(data.status);
					},
					function(){
						return '<a class="" href="javascript:;" id="remark">备注</a>';
					}
				],
				source: function(o, pag, table) {
					dialog = wait();
					parms.page = o.currentPage;
					parms.size = static.carouselPerNum;
					server.homeAdverts(parms, function(resp){
						dialog.hide();
						if (resp.code == 0) {
							carouselDatas = resp.body.news; // 假设读取一页取得所有数据.
							if (resp.body.news.length) {
								pag({totalPage: Math.ceil(resp.body.total_num/static.perPageNums)});
							}
							table(resp.body.news);
						} else {
							tip(resp.msg || '查询数据列表出错');
						}
					})
				},
				perPageNums: static.perPageNums
			}, {
				'click #remark': 'remark',
				'click #update': 'update',
				'click #watch': 'watch',
				'click #upShelf': 'upShelves',
				'click #downShelf': 'downShelf',
				'click #remark': 'remark'
			}, {
				remark: function(e, row) {
					var remark = require('remark');
					remark(2, row.data.id, function(el){
						pageManager.render('secondPanel', el);
					});
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
							carouselDetailView.render(resp.body);
						} else {
							tip ('获取资讯详情失败');
						}
					})
				}
			});
		}	 
		function _render(parms) {
			var table = renderTable(parms);
			pageManager.render('listPanel', table.El);
		}
		function getCarousIds() {
			var result = [];
			$.each(carouselDatas, function(index, carous){
				if (carous.id) {
					result.push(carous.id);
				}
			})
			return result;
		}
		return { 
			render: _render,
			getCarousIds: getCarousIds
		};
	})()

	// 轮播图回收站列表视图
	var carouseRabishListView = (function() { // 
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
	}

	// 入口.
	var page = {
		init: function() {
			page.cityId = main.getCityId();
			pageManager.add({name: 'editPanel', el: $('#createWrap'), parent: $('#secondPanel')});
			pageManager.add({name: 'secondPanel', el: $('#operateWrap'), parent: $('#secondPanel')});
			pageManager.add({name: 'listPanel', el: $('#listCon'), parent: $('#mainPanel')});
			pageManager.hide();

			carouselListView.render({cityId: page.cityId});
			setEvents();

			this.enterCreate = function(o) { // 添加轮播
				pageManager.show('editPanel');
				activeEdit.load('create', o);		
			}
			this.enterEdit = function(data, fn) { // 修改轮播
				pageManager.show('editPanel');
				activeEdit.load('edit', data);
			}

			var activeEdit = InformationEdit({
				template: 'carouselCreateTemplate',
				typeId: typeId,
				cityId: page.cityId
			});
			
			activeEdit.init(function(el){
				$('#createWrap').html(el);
			})

			activeEdit.on('save', function(newsId) {
				pageManager.show('listPanel');
				var dialog = wait();
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
				pageManager.show('listPanel');
				var dialog = wait();
				server.updateStatusNews({newsId: newsId, status: 5}, function(resp){
					if (resp.code == 0) {
						var Id = newsId;
						var CarousIds = carouselListView.getCarousIds() || [];
						CarousIds.push(Id);
						server.updateHomeAdverts({
							cityId: page.cityId,
							newsIds: CarousIds
						}, function(resp) {
							dialog.destroy();
							if (resp.code == 0) {
								carouselListView.render({cityId: page.cityId});
							} else {
								tip('添加轮播图失败');
							}
						})						
					} else {
						tip('修改状态出错');
					}
				})
			})
		}
	}
	page.init();

})