define(function(require, exports, module){
	// 用户列表
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

	// 个人详情页视图
	var figureDetailView = (function() {
		
		var El = $('<div></div>');
		function _render(data) {
			/*var data = {
				name: 111
			};*/
			var html = template.render('figure_detail_template', data);
			El.html(html);
			pageManager.render('secondPanel', El);
		}
		return { render: _render };
	})()

	// 基础数据展示视图
	var baseDataView = (function(){ 
		// 分享次数 | 收藏次数 | 报名次数 | 签到次数 | 动态发布数	
		var El = $('<div></div>');
		var serverConfig = {
			share: server.shareActs,
			collection: server.lovActs,
			registration: server.enrollActs,
			checkin: server.checkinActs,
			dynamics: server.dynamics
		};

		function renderTable(parms, uid, type) { // 分享次数 | 收藏次数 | 报名次数
			return table = PagTable({
				ThList: ['编号', '活动名称', '发布时间', '分享数', '收藏数'],
				columnNameList: [
					'index', 'title', 'publish_time', 'shared_num', 'loved_num'
				],
				source: function(o, pag, table) {
					dialog = wait();
					parms.page = o.currentPage;
					parms.size = static.actsPerNum;
					parms.uid = uid;
					serverConfig[type](parms, function(resp){
						dialog.destroy();
						if (resp.code == 0) {
							if (resp.body.acts.length) {
								pag({totalPage: Math.ceil(resp.body.total_num/static.actsPerNum)});
							}
							table(resp.body.acts);
						} else {
							tip(resp.msg || '查询数据列表出错');
						}
					})
				},
				perPageNums: static.actsPerNum
			});
		}	

		function renderCheckinTable(parms, uid) { // 签到次数
			return table = PagTable({
				ThList: ['编号', '活动名称', '发布时间', '分享数', '收藏数', '签到时间'],
				columnNameList: [
					'index', 'title', 'publish_time', 'shared_num', 'loved_num', 'checkin_time'
				],
				source: function(o, pag, table) {
					dialog = wait();
					parms.page = o.currentPage;
					parms.size = static.actsPerNum;
					parms.uid = uid;
					serverConfig.checkin(parms, function(resp){
						dialog.hide();
						if (resp.code == 0) {
							if (resp.body.acts.length) {
								pag({totalPage: Math.ceil(resp.body.total_num/static.actsPerNum)});
							}
							table(resp.body.acts);
						} else {
							tip(resp.msg || '查询数据列表出错');
						}
					})
				},
				perPageNums: static.actsPerNum
			});
		}

		function renderDynamicTable(parms, uid) { // 动态发布数
			return table = PagTable({
				ThList: ['编号', '活动名称', '发布时间', '分享数', '收藏数'],
				columnNameList: [
					'index', 'title', 'publish_time', 'shared_num', 'loved_num'
				],
				source: function(o, pag, table) {
					dialog = wait();
					parms.page = o.currentPage;
					parms.size = static.actsPerNum;
					parms.uid = uid;
					serverConfig.dynamics(parms, function(resp){
						dialog.hide();
						if (resp.code == 0) {
							if (resp.body.dynamics.length) {
								pag({totalPage: Math.ceil(resp.body.total_num/static.actsPerNum)});
							}
							table(resp.body.dynamics);
						} else {
							tip(resp.msg || '查询数据列表出错');
						}
					})
				},
				perPageNums: static.actsPerNum
			});
		}

		function _render(type, data) {
			var table;
			if (~$.inArray(type, ['share', 'collection', 'registration'])) {
				table = renderTable({cityId: page.cityId}, data.id, type);
			} else if (type == 'checkin') {
				table = renderCheckinTable({cityId: page.cityId}, data.id);
			} else if (type == 'dynamic') {
				table = renderDynamicTable({cityId: page.cityId}, data.id);
			}
			El.html(table.El);
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
				ThList: ['编号', '用户头像', '用户昵称', '是否达人', '姓名', '性别', '电话',
				 '分享次数', '收藏次数', '报名次数', '签到次数', '动态发布数', '对TA push', '上/下架', '备注'],
				columnNameList: [
					'index', 
					function(data) {
						return '<a id="detailPersonal" href="javascript:;"><img width="80" height="80" src="'+ data.head_img_url +'" /></a>';
					}, 'nick_name',
					function(data) {
						return data.is_vip == 1 ? '是' : data.is_vip == 0 ? '否' : '';
					},'real_name',
					function(data) {
						return data.sex == 1 ? '男' : data.sex == 2 ? '女' : '';
					}
					,'contact_phone',
					function(data){
						return '<a class="" href="javascript:;" id="shareNum">'+ data.shared_num +'</a>';
					},
					function(data){
						return '<a class="" href="javascript:;" id="collectionNum">'+ data.loved_num +'</a>';
					},
					function(data){
						return '<a class="" href="javascript:;" id="registrationNum">'+ data.enroll_num +'</a>';
					},
					function(data){
						return '<a class="" href="javascript:;" id="checkinNum">'+ data.checkin_num +'</a>';
					},
					function(data){
						return '<a class="" href="javascript:;" id="dynamicsNum">'+ data.dynamic_num +'</a>';
					},
					function(){
						return '<a class="" href="javascript:;" id="push">push</a>';
					},
					function(){
						return '<a class="" href="javascript:;" id="shelves">上架</a>';
					},
					function(){
						return '<a class="" href="javascript:;" id="remark">备注</a>';
					}
				],
				source: function(o, pag, table) {
					dialog = wait();
					parms.page = o.currentPage;
					parms.size = static.figureUsersPerNum;
					server.users(parms, function(resp){
						/*resp = {
							code: 0,
							body: {
								users: [{ name: 3, head_img_url: 'http://www.1124.cc/up_files/2009-12-14/1260789562Hidj.jpg'},
								{ name: 4, head_img_url: 'http://www.1124.cc/up_files/2009-12-14/1260789562Hidj.jpg'}],
								total_num: 1
							}
						};*/
						dialog.hide();
						if (resp.code == 0) {
							if (resp.body.users.length) {
								pag({totalPage: Math.ceil(resp.body.total_num/static.figureUsersPerNum)});
							}
							table(resp.body.users);
						} else {
							tip(resp.msg || '查询数据列表出错');
						}
					})
				},
				perPageNums: static.figureUsersPerNum
			}, {
				'click #detailPersonal': 'detailPersonal',
				'click #shareNum': 'shareNum',
				'click #collectionNum': 'collectionNum',
				'click #registrationNum': 'registrationNum',
				'click #checkinNum': 'checkinNum',
				'click #dynamicsNum': 'dynamicsNum',				
				'click #remark': 'remark'
			}, {
				detailPersonal: function(e, row) {
					var dialog = wait();
					server.user({cityId: page.cityId, uid: row.data.id}, function(resp){
						dialog.destroy();
						if (resp.code == 0) {
							figureDetailView.render(resp.body);
						} else {
							tip('获取用户详情失败');
						}
					})
				},
				shareNum: function(e, row) {
					baseDataView.render('share', row.data);
				},
				collectionNum: function(e, row) {
					baseDataView.render('collection', row.data);
				},
				registrationNum: function(e, row) {
					baseDataView.render('registration', row.data);
				},
				checkinNum: function(e, row) {
					baseDataView.render('checkin', row.data);
				},
				dynamicsNum: function(e, row) {
					baseDataView.render('dynamic', row.data);
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
			pageManager.show('listPanel');
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
			parms.cityId = page.cityId;
			figureUsersView.render(parms);
		})
	}

	// 入口
	var page = {
		init: function() {
			page.cityId = main.getCityId();
			pageManager.add({name: 'secondPanel', el: $('#operateWrap'), parent: $('#secondPanel')});
			pageManager.add({name: 'listPanel', el: $('#adminListCon'), parent: $('#mainPanel')});
			pageManager.hide();
			figureUsersView.render({cityId: page.cityId});
			setEvents();
		}
	}

	page.init();

})