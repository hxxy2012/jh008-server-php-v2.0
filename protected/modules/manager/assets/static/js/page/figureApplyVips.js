define(function(require, exports, module){
	// 申请达人用户列表
	var $ = require('$'),
		common = require('common'),
		dialogUi = require('dialogUi'),
		main = require('main'),
		server = require('server'),
		static = require('static');

	var pageManager = common.pageManager,
		PagTable = common.PagTable,
		myValidate = common.myValidate,
		regexp = myValidate.regexp;

	var tip = main.tip,
		wait = main.wait;

	// 个人详情页视图
	var figureDetailView = (function() {
		
		var El = $('<div></div>');
		function _render(data) {
			var html = template.render('figure_detail_template', data);
			El.html(html);
			pageManager.render('secondPanel', El);
		}
		return { render: _render };
	})()

	// 照片查看视图
	var photosView = (function() {
		var El = $('<div></div>')
		function _render(photos) {
			El.html('');
			$.each(photos, function(index, photo){
				var photoEl = $('<div><img src="'+ photo.img_url +'" alt="" /></div>');
				El.append(photoEl);
			})
			pageManager.render('secondPanel', El);
		}

		return {
			render: _render
		}
	})()


	// 用户列表视图
	var figureUsersView = (function() {
		function renderTable(parms) {
			return table = PagTable({
				ThList: ['编号', '用户详情', '真实姓名', '联系电话', '常用邮箱', '活动标签', '达人标签',
				 '个人简介', '照片', '提交时间', '设置达人', '备注'],
				columnNameList: [
					'index', 
					function(data) {
						return '<a id="detailPersonal" href="javascript:;">用户详情</a>';
					}, 'real_name', 'contact_phone', 'email', 
					function(data) {
						var result = [];
						if (data.act_tags && data.act_tags.length) {
							$.each(data.act_tags, function(i, tag) {
								result.push(tag.name);
							})							
						}
						return result.join('、');
					},
					function(data) {
						var result = [];
						if (data.user_tags && data.user_tags.length) {
							$.each(data.user_tags, function(i, tag) {
								result.push(tag.name);
							})							
						}
						return result.join('、');
					}, 'intro',
					function(data) {
						return '<a class="" href="javascript:;" id="watch">查看</a>';
					},
					 'create_time',
					function() {
						return '<a class="" href="javascript:;" id="setVip">设置达人</a>';
					},
					function(){
						return '<a class="" href="javascript:;" id="remark">备注</a>';
					}
				],
				source: function(o, pag, table) {
					dialog = wait();
					parms.page = o.currentPage;
					parms.size = static.figureUsersPerNum;
					parms.cityId = page.cityId;
					server.vipApplys(parms, function(resp){
						dialog.hide();
						if (resp.code == 0) {
							if (resp.body.applys.length) {
								pag({totalPage: Math.ceil(resp.body.total_num/static.figureUsersPerNum)});
							}
							table(resp.body.applys);
						} else {
							tip(resp.msg || '查询数据列表出错');
						}
					})
				},
				perPageNums: static.figureUsersPerNum
			}, {
				'click #detailPersonal': 'detailPersonal',
				'click #watch': 'watch',
				'click #setVip': 'setVip',				
				'click #remark': 'remark'
			}, {
				detailPersonal: function(e, row) {
					var dialog = wait();
					server.user({cityId: page.cityId, uid: row.data.author_id}, function(resp){
						dialog.destroy();
						if (resp.code == 0) {
							figureDetailView.render(resp.body);
						} else {
							tip('获取用户详情失败');
						}
					})
					//figureDetailView.render();
				},
				watch: function(e, row) {
					var photos = row.data.photos;
					if (!photos.length) {
						tip('照片为空');
					} else {
						photosView.render(photos);
					}
				},
				setVip: function(e, row) {
					var dialog = wait(); 
					server.dealVipApply({cityId: page.cityId, applyId: row.data.id}, function(resp){
						dialog.destroy();
						if (resp.code == 0) {
							tip('设置达人成功');
							row.destory();
						} else {
							tip('处理达人申请失败');
						}
					})
				},
				remark: function(e, row) {
					var remark = require('remark');
					remark(3, row.data.id, function(el){
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

	function setEvents() {		

		$('#return').click(function(){
			pageManager.show('listPanel');
		})
		$('#searchBtn').click(function(){
			var keyWords, sex, parms={};
			keyWords = $.trim($('#keyword').val());
			sex = $('#sexSelect #spanText').attr('value');
			if (sex != 'all') {
				parms.sex = sex;
			}
			parms.keyWords = keyWords;
			figureUsersView.render(parms);
		})}

	
	// 入口
	var page = {
		init: function() {
			page.cityId = main.getCityId();
			pageManager.add({name: 'secondPanel', el: $('#operateWrap'), parent: $('#secondPanel')});
			pageManager.add({name: 'listPanel', el: $('#adminListCon'), parent: $('#mainPanel')});
			pageManager.hide();
			figureUsersView.render({});
			setEvents();
		}
	}

	page.init();

})