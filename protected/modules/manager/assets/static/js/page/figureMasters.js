define(function(require, exports, module){
	// 达人页面
	var $ = require('$'),
		common = require('common'),
		dialogUi = require('dialogUi'),
		main = require('main'),
		server = require('server'),
		static = require('static'),
		InformationEdit = require('informationEdit');

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
						/*var resp = {
							code: 0,
							body: {
								acts: [{title: 3}],
								total_num: 1
							}
						};*/
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
						/*var resp = {
							code: 0,
							body: {
								acts: [{title: 3}],
								total_num: 1
							}
						};*/
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

		function _render(type, data) {
			var table;
			if (~$.inArray(type, ['share', 'collection', 'registration'])) {
				table = renderTable({cityId: page.cityId}, data.id, type);
			} else if (type == 'checkin') {
				table = renderCheckinTable({cityId: page.cityId}, data.id);
			}
			El.html(table.El);
			pageManager.render('secondPanel', El);
		}
		return {
			render: _render
		}
	})()

	// 动态照片列表视图
	var dynamicView = (function() {
		var El = $('<div></div>');
		function renderTable(parms) {
			return table = PagTable({
				ThList: ['编号','照片缩略图','动态内容', '回复用户数', '被回复数', '上传图片数', '发布时间'],
				columnNameList: [
					'index',
					function(data) {
						return '<a id="watch" href="javascript:;">查看</a>';
					},
					'content','at_user_num','at_num',function(data){
						return data.imgs.length;
					},'publish_time'
				],
				source: function(o, pag, table) {
					dialog = wait();
					parms.page = o.currentPage;
					parms.size = static.actsPerNum;
					server.dynamics(parms, function(resp){
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
			}, {
				'click #watch': 'watch'
			}, {
				watch: function(e, row) {
					var imgs = row.data.imgs;
					if (!imgs.length) {
						tip('照片为空');
					} else {
						imgsView.render(imgs);
					}
				}
			});
		}  

		function _render(data) {
			var table = renderTable({uid: data.id});
			pageManager.render('secondPanel', El.html(table.El));
		}
		return {
			render: _render
		}
	})()

	// 图片查看视图
	var imgsView = (function() {
		var El = $('<div></div>')
		function _render(imgs) {
			El.html('');
			$.each(imgs, function(index, photo){
				var photoEl = $('<div class="overflowauto"><img src="'+ photo.img_url +'" alt="" /></div>');
				El.append(photoEl);
			})
			pageManager.render('photoPanel', El);
		}

		return {
			render: _render
		}
	})()

	// 达人用户列表视图
	var figureMastersView = (function() {
		function renderTable(parms) {
			return table = PagTable({
				ThList: ['编号','头像', '用户昵称', '姓名', '性别', '电话',
				 '分享次数', '收藏次数', '报名次数', '签到次数', '动态上传数', '专访', '上/下架', '备注'],
				columnNameList: [
					'index',
					function(data) {
						return '<a id="detailPersonal" href="javascript:;"><img width="80" height="80" src="'+ data.head_img_url +'" /></a>';
					},
					'nick_name','real_name',
					function(data) {
						return data.sex == 1 ? '男' : data.sex == 2 ? '女' : '';
					},'contact_phone',
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
					function(data){
						return '<a href="javascript:;" id="interview">专访</a>';
					},
					function(data){
						if (data.status == -1) {
							return '<a class="red-color" href="javascript:;" id="upShelf">上架</a>';
						} else if (data.status == 0) {
							return '<a class="blue-color" href="javascript:;" id="downShelf">下架</a>';
						}
					},
					function(){
						return '<a class="" href="javascript:;" id="remark">备注</a>';
					}
				],
				source: function(o, pag, table) {
					dialog = wait();
					parms.page = o.currentPage;
					parms.size = static.actsPerNum;
					server.vips(parms, function(resp){
						dialog.hide();
						if (resp.code == 0) {
							if (resp.body.users.length) {
								pag({totalPage: Math.ceil(resp.body.total_num/static.actsPerNum)});
							}
							table(resp.body.users);
						} else {
							tip(resp.msg || '查询数据列表出错');
						}
					})
				},
				perPageNums: static.actsPerNum
			}, {
				'click #detailPersonal': 'detailPersonal',
				'click #shareNum': 'shareNum',
				'click #collectionNum': 'collectionNum',
				'click #registrationNum': 'registrationNum',
				'click #checkinNum': 'checkinNum',
				'click #dynamicsNum': 'dynamicNum',
				'click #upShelf': 'upShelf',
				'click #downShelf': 'downShelf',
				'click #remark': 'remark',
				'click #interview': 'interview'
			}, {
				detailPersonal: function(e, row) {
					var dialog = wait();
					server.user({cityId: page.cityId, uid: row.data.id}, function(resp){
						dialog.destroy();
						if (resp.code == 0) {
							figureDetailView.render(row.data);
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
				dynamicNum: function(e, row) {
					dynamicView.render(row.data);
				},
				upShelf: function(e, row) {
					var dialog = wait();
					server.setVip({cityId: page.cityId, vipId: row.data.id, status: 0}, function(resp){
						dialog.destroy();
						if (resp.code == 0) {
							row.set({status: 0});
							row.refresh();
						} else {
							tip('上架失败');
						}
					})
				},
				downShelf: function(e, row) {
					var dialog = wait();
					server.setVip({cityId: page.cityId, vipId: row.data.id, status: -1}, function(resp){
						dialog.destroy();
						if (resp.code == 0) {
							row.set({status: -1});
							row.refresh();
						} else {
							tip('上架成功');
						}
					})
				},
				interview: function(e, row) {
					if (!row.data.interview_id) {
						page.enterCreate(function(newsId, callback){
							var dialog = wait();
							server.setVipInterview({vipId: row.data.id, newsId: newsId}, function(resp){
								dialog.destroy();
								if (resp.code == 0) {
									callback && callback();
								} else {
									tip ('设置达人失败');
								}
							})
						});
					} else {
						var dialog = wait();
						server.newsInfo({newsId: row.data.interview_id}, function(resp){
							dialog.destroy();
							if (resp.code == 0) {
								page.enterEdit(resp.body);
							} else {
								tip ('获取资讯详情失败');
							}
						})
					}
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
		$('#create').click(function(){
			createFigureUserView.render();
		})
		$('#return').click(function(){
			var photoWrap = $('#photoWrap');
			if (photoWrap.css('display') == 'none') {
				pageManager.show('listPanel');
			} else {
				pageManager.show('secondPanel');
			}
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
			figureMastersView.render(parms);
		})
	}

	// 入口
	var page = {
		init: function() {
			var addInterviewHandle;
			page.cityId = main.getCityId();
			pageManager.add({name: 'listPanel', el: $('#adminListCon'), parent: $('#mainPanel')});
			pageManager.add({name: 'secondPanel', el: $('#operateWrap'), parent: $('#secondPanel')});
			pageManager.add({name: 'editPanel', el: $('#createWrap'), parent: $('#secondPanel')});
			pageManager.add({name: 'photoPanel', el: $('#photoWrap'), parent: $('#secondPanel')});
			pageManager.hide();
			figureMastersView.render({cityId: page.cityId});
			setEvents();

			this.enterCreate = function(fn) { // 添加专访
				addInterviewHandle = fn;
				pageManager.show('editPanel');
				activeEdit.load('create');		
			}
			this.enterEdit = function(data) { // 修改专访
				pageManager.show('editPanel');
				activeEdit.load('edit', data);
			}

			var activeEdit = InformationEdit({
				template: 'interviewCreateTemplate',
				typeId: 4,
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
					dialog.destroy();
					if (resp.code == 0) {
						addInterviewHandle && addInterviewHandle(newsId, function(){
							
							$('#searchBtn').click();
						});
					} else {
						tip('修改状态出错');
					}
				})
			})
		}
	}

	page.init();

})