define(function(require, exports, module){
	// 爆料
	var $ = require('$'),
		common = require('common'),
		dialogUi = require('dialogUi'),
		Dialog = require('dialog'),
		main = require('main'),
		server = require('server'),
		static = require('static');

	var pageManager = common.pageManager,
		PagTable = common.PagTable,
		myValidate = common.myValidate,
		regexp = myValidate.regexp;

	var tip = main.tip,
		wait = main.wait;


	// 图片查看视图
	var imgsView = (function() {
		var El = $('<div></div>')
		function _render(imgs) {
			El.html('');
			$.each(imgs, function(index, photo){
				var photoEl = $('<div class="overflowauto"><img src="'+ photo.img_url +'" alt="" /></div>');
				El.append(photoEl);
			})
			pageManager.render('secondPanel', El);
		}

		return {
			render: _render
		}
	})()

	// 爆料列表视图
	var tipOffListView = (function() {
		function renderTable(parms) {
			return table = PagTable({
				ThList: ['编号', '照片', '心里话', '电话', '通信地址', '备注'],
				columnNameList: [
					'index', function(data){
						return '<a class="" href="javascript:;" id="watch">查看</a>';
					}, 
					'intro', 
					'contact_phone',
					'contact_address',
					function(){ 
						return '<a class="" href="javascript:;" id="remark">备注</a>';
					}
				],
				source: function(o, pag, table) {
					dialog = wait();
					parms.page = o.currentPage;
					parms.size = static.tipoffPerNum;
					server.brokenews(parms, function(resp){
						dialog.hide();
						if (resp.code == 0) {
							if (resp.body.broke_news.length) {
								pag({totalPage: Math.ceil(resp.body.total_num/static.tipoffPerNum)});
							}
							table(resp.body.broke_news);
						} else {
							tip(resp.msg || '查询数据列表出错');
						}
					})
				},
				perPageNums: static.tipoffPerNum
			}, {
				'click #remark': 'remark',
				'click #watch': 'watch'
			}, {
				watch: function(e, row) {
					var imgs = row.data.imgs;
					if (!imgs.length) {
						tip('照片为空');
					} else {
						imgsView.render(imgs);
					}
				},
				remark: function(e, row) {
					dialogUi.remark({
						callback: function(val, dialog) {
							dialog.hide();
						}
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

	// 爆料列表回收站视图
	var tipOffRabishListView = (function() {
		function _render() {}
		return { render: _render };
	})()

	function setEvents() {
		$('#return').click(function(){
			pageManager.show('listPanel');
		})
	}

	// 入口
	var page = {
		init: function() {
			page.cityId = 1;
			pageManager.add({name: 'listPanel', el: $('#listCon'), parent: $('#mainPanel')});
			pageManager.add({name: 'secondPanel', el: $('#operateWrap'), parent: $('#secondPanel')});
			pageManager.hide();
			tipOffListView.render({cityId: page.cityId});
			setEvents();
		}
	}

	page.init();

})