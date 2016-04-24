define(function(require, exports, module){
	// 管理员列表
	var $ = require('$'),
		common = require('common'),
		Dialog = require('dialog'),
		dialogUi = require('dialogUi'),
		main = require('main'),
		server = require('server'),
		static = require('static'),
		Authority = roleType; // 1|11|12|101|102

	var pageManager = common.pageManager,
		PagTable = common.PagTable,
		myValidate = common.myValidate,
		regexp = myValidate.regexp;

	var tip = main.tip,
		wait = main.wait,
		resetPassDialog = dialogUi.resetPassDialog; // 重置密码弹出框

	var citySelectEl;

	// 初始化城市列表 用于创建角色
	(function initCity() {
		var cityEle = $('<div id="cityCon" class="lc-ui-form-item"><label for="" class="lc-ui-label">城市列表:</label>' + 
	        				'<div class="lc-ui-rs">' + 
								'<select name="" id="citySelect">' +
								'</select>' +
					        '</div>' +
					    '</div>');
		server.citys(function(resp){
			/*var resp = {
				code: 0,
				body: {
					cities: [{status: 0, id:11, name: '成都'},{status: 0, id:22, name: '北京'}]
				}
			}*/
			if (resp.code == 0) {
				$.each(resp.body.cities, function(index, city){
					if (city.status == 0) {
						cityEle.find('#citySelect').append('<option value="'+ city.id +'">'+ city.name +'</option>');
					}
				});
			}
		})
		citySelectEl = cityEle;			
	})()

	// 创建管理员视图
	var createManagerView = (function() {
		pageManager.add({name: 'cm', el: $('#operateWrap'), parent: $('#secondPanel')});
		var addCM = server.addCM,
			addM = server.addM;

		var El = $('<div></div>');

		function _render() {
			var html = template.render('create_manager_template');
			El.html(html);
			pageManager.render('cm', El);
			if ($('#authoritySelect')[0]) {
				$('#authoritySelect').bind('change', function() {
					if ($(this).val() == '101') {
						El.find('#authorityCon').after(citySelectEl);
					} else {
						El.find('#cityCon').remove();
					}
				})
			}
			El.find('#add').click(function(){
				var	uName = $.trim(El.find('#uName').val())
					name = $.trim(El.find('#name').val()),
					uPass = $.trim(El.find('#uPass').val());
					//authority,city;
					surePass = $.trim(El.find('#surePass').val());

				var validate = myValidate();
				validate.check(regexp.checkEmpty(uName), '登录名不能为空');
				validate.check(regexp.checkSize(uName, 6, 16), '用户名长度保持6-16位');
				validate.check(regexp.checkEmpty(name), '姓名不能为空');
				validate.check(regexp.checkEmpty(uPass), '密码不能为空');
				validate.check(regexp.checkSize(uName, 6, 16), '密码长度保持6-16位');
				validate.check(regexp.checkSame(uPass, surePass), '两次密码不一样');
				validate.run(function(){
					var parms = {};
					parms.uName = uName;
					parms.uPass = uPass;
					parms.name = name;
					// parms.type = ;
					function callback(resp) {
						//var resp = {code: 0};
						if (resp.code == 0) {
							tip('管理员添加成功');
							managerListView.render('all');
						} else {
							tip(resp.msg || '管理员添加失败');
						}
					}
					function addmanager(type, parms) {
						var dialog = wait();
						if (type == 1) {
							addCM(parms, function(resp){
								dialog.hide();
								callback(resp);
							});
						} else if (type == 2) {
							addM(parms, function(resp){
								dialog.hide();
								callback(resp);
							});
						}
					}
					if (Authority == 1 && $('#authoritySelect')[0]) {
						parms.type = $('#authoritySelect').val();
						if ($('#authoritySelect').val() == '101') {
							parms.cityId = $('#citySelect').val();
							addmanager(1, parms);
							//callback();
						} else {
							addmanager(2, parms);
						}
					} else if (Authority == 101) {
						parms.type = 102;
						addmanager(1, parms);
					}
				})
			})
		}
		return { render: _render };
	})()

	// 更新管理员视图
	var updateManagerView = (function() {
		//pageManager.add({name: 'cm', el: $('#operateWrap'), parent: $('#secondPanel')});
		var updateM = server.updateM,
			updateCM = server.updateCM, curData;
		var El = $('<div></div>');
		function _render(data) {
			curData = data;
			El.html(template.render('update_manager_template', data));
			pageManager.render('cm', El);
			El.find('#update').click(function(){
				var	name = $.trim(El.find('#name').val()),
					parms = {};

				if (!name) {
					tip('姓名不能为空');
				} else {
					parms.name = name;
					function callback(resp) {
						//var resp = {code: 0};
						if (resp.code == 0) {
							tip('管理员修改成功');
							managerListView.render('all');
						} else {
							tip(resp.msg || '管理员修改失败');
						}
					}
					function updatemanager(type, parms) {
						var dialog = wait();
						if (type == 1) {
							updateCM(parms, function(resp){
								dialog.hide();
								callback(resp);
							});
						} else if (type == 2) {
							updateM(parms, function(resp){
								dialog.hide();
								callback(resp);
							});
						}
					}
					if (Authority == 1) {
						if (curData.type == 101) {
							parms.cityManagerId = curData.id;
							updatemanager(1, parms);
						} else if (curData.type == 11 || curData.type == 22) {
							parms.managerId = curData.id;
							updatemanager(2, parms);
						}
					} else if (Authority == 101) {
						parms.cityManagerId = curData.id;
						updatemanager(1, parms);
					}
				}

			})

		}
		return { render: _render };
	})()

 	// 管理员列表视图
	var managerListView = (function() {
		var managersAjax = server.managers,
			cityManagers = server.cityManagers,
			cityOperators = server.cityOperators;
		pageManager.add({name: 'mlist', el: $('#adminListCon'), parent: $('#mainPanel')});
		function renderTable() {
			// 其他管理员列表
			return table = PagTable({
				ThList: ['编号', '登录名', '权限', '姓名', '状态', '创建时间', '最后一次登录时间', '修改', '暂停|恢复', '重置密码', '查看日志'],
				columnNameList: [
					'index', 'u_name', 
					function(data){
						return static.roleType[data.type];
					},'name','status','create_time','last_login_time',
					function(){
						return '<a class="ui-button ui-button-sdarkblue" href="javascript:;" id="update">修改</a>';
					},
					'name',
					function(){
						return '<a class="ui-button ui-button-sdarkblue" href="javascript:;" id="resetPass">重置密码</a>'
					},
					function(){
						return '<a class="ui-button ui-button-sdarkblue" href="javascript:;" id="watch">查看</a>';
					}
				],
				source: function(o, pag, table) {
					dialog = wait();
					var parms = {};
					parms.page = o.currentPage;
					parms.size = static.managersPerNum;
					managersAjax(parms, function(resp){
						/*resp = {
							code: 0,
							body: {
								managers: [{id:111, name: 3, u_name: '管理员', type: 12}],
								total_num: 1
							}
						};*/
						dialog.hide();
						if (resp.code == 0) {
							//callback(resp.body.businesses);
							if (resp.body.managers.length) {
								pag({totalPage: Math.ceil(resp.body.total_num/static.managersPerNum)});
							}
							table(resp.body.managers);
						} else {
							tip(resp.msg || '查询数据列表出错');
						}
					})
				},
				perPageNums: static.managersPerNum
			}, {
				'click #watch': 'watch',
				'click #update': 'update',
				'click #resetPass': 'resetPass'
			}, {
				watch: function() {
					alert('watch');
				},
				update: function(e, row) {
					updateManagerView.render(row.data);
				},
				resetPass: function(e, row) {
					resetPassDialog(function(uPass, dialog){
						var dia = wait();
						server.updateM({managerId: row.data.id, uPass: uPass}, function(resp){
							dia.destroy();
							dialog.destroy();
							if (resp.code == 0) {
								tip('密码重置成功');
							} else {
								tip('密码重置失败');
							}
						})
					})
				}
			});
		}
		function renderTableSecond() {
			// 城市管理员列表
			return table = PagTable({
				ThList: ['编号', '登录名', '权限', '姓名', '对应城市', '状态', '创建时间', '最后一次登录时间', '修改', '暂停|恢复', '重置密码', '查看日志'],
				columnNameList: [
					'index', 'u_name', 
					function(data){
						return static.roleType[data.type];
					},'name',
					function(data){
						return '<a id="city" href="javascript:;">'+ data.city +'</a>';
					},'status','create_time','last_login_time',
					function(){
						return '<a class="ui-button ui-button-sdarkblue" href="javascript:;" id="update">修改</a>';
					},
					'name',
					function() {
						return '<a class="ui-button ui-button-sdarkblue" href="javascript:;" id="resetPass">重置密码</a>'
					},
					function(){
						return '<a class="ui-button ui-button-sdarkblue" href="javascript:;" id="watch">查看</a>';
					}
				],
				source: function(o, pag, table) {
					dialog = wait();
					var parms = {};
					parms.page = o.currentPage;
					parms.size = static.managersPerNum;
					cityManagers(parms, function(resp){
						dialog.hide();
						if (resp.code == 0) {
							//callback(resp.body.businesses);
							if (resp.body.city_managers.length) {
								pag({totalPage: Math.ceil(resp.body.total_num/static.managersPerNum)});
							}
							table(resp.body.city_managers);
						} else {
							tip(resp.msg || '查询数据列表出错');
						}
					})
				},
				perPageNums: static.managersPerNum
			}, {
				'click #watch': 'watch',
				'click #update': 'update',
				'click #city': 'city',
				'click #resetPass': 'resetPass'
			}, {
				watch: function() {
					alert('watch');
				},
				update: function(e, row) {
					updateManagerView.render(row.data);
				},
				city: function(e, row) {
					_render('cityop', row.data);
				},
				resetPass: function(e, row) {
					resetPassDialog(function(uPass, dialog){
						var dia = wait();
						server.updateCM({cityManagerId: row.data.id, uPass: uPass}, function(resp){
							dia.destroy();
							dialog.destroy();
							if (resp.code == 0) {
								tip('密码重置成功');
							} else {
								tip('密码重置失败');
							}
						})
					})
				}
			});
		}
		function renderTableThree(data) {
			// 城市操作员列表
			return table = PagTable({
				ThList: ['编号', '登录名', '权限', '姓名', '状态', '创建时间', '最后一次登录时间', '修改', '暂停|恢复', '重置密码', '查看日志'],
				columnNameList: [
					'index', 'u_name', 
					function(data){
						return static.roleType[data.type];
					},
					'name','status','create_time','last_login_time',
					function(){
						return '<a class="ui-button ui-button-sdarkblue" href="javascript:;" id="update">修改</a>';
					},
					'name',
					function() {
						return '<a class="ui-button ui-button-sdarkblue" href="javascript:;" id="resetPass">重置密码</a>';
					},
					function(){
						return '<a class="ui-button ui-button-sdarkblue" href="javascript:;" id="watch">查看</a>';
					}
				],
				source: function(o, pag, table) {
					dialog = wait();
					var parms = {};
					parms.page = o.currentPage;
					parms.size = static.managersPerNum;
					if (data) {
						parms.cityId = page.cityId;
					}
					cityOperators(parms, function(resp){
						/*resp = {
							code: 0,
							body: {
								city_managers: [{id: 113, name: 3, u_name: '城市操作员'}],
								total_num: 1
							}
						};*/
						dialog.hide();
						if (resp.code == 0) {
							//callback(resp.body.businesses);
							if (resp.body.city_managers.length) {
								pag({totalPage: Math.ceil(resp.body.city_managers/static.managersPerNum)});
							}
							table(resp.body.city_managers);
						} else {
							tip(resp.msg || '查询数据列表出错');
						}
					})
				},
				perPageNums: static.managersPerNum
			}, {
				'click #watch': 'watch',
				'click #update': 'update',
				'click #resetPass': 'resetPass'
			}, {
				watch: function() {
					alert('watch');
				},
				update: function(e, row) {
					updateManagerView.render(row.data);
				},
				resetPass: function(e, row) {
					resetPassDialog(function(uPass, dialog){
						var dia = wait();
						server.updateCM({cityManagerId: row.data.id, uPass: uPass}, function(resp){
							dia.destroy();
							dialog.destroy();
							if (resp.code == 0) {
								tip('密码重置成功');
							} else {
								tip('密码重置失败');
							}
						})
					})
				}
			});
		}
		function _render(type, data) {
			var table;
			if (Authority == 101 || type == 'cityop') {
				table = renderTableThree(data);
			} else if (Authority == 1 && (type == 'all' || type == 'yyzy' || type=='jdy' )) {
				table = renderTable();
			} else if (Authority == 1 && type == 'citym') {
				table = renderTableSecond();
			}
			table &&
			pageManager.render('mlist', table.El);
		}
		return { render: _render };
	})();

	// 管理员回收站列表视图
	var managerRabishListView = (function() {
		function _render() {}
		return { render: _render };
	})

	function setEvents() {
		$('#create').click(function(){
			createManagerView.render();
		})
		$('#return').click(function(){
			pageManager.show('mlist');
		})
		$('#managerSelect ul').on('click', 'a', function(e){
			var target = $(e.target),
				val = target.attr('data-val');
			managerListView.render(val);
		})
	}

	// 入口
	var page = {
		init: function() {
			page.cityId = main.getCityId();
			pageManager.hide();
			managerListView.render('all');
			setEvents();
		}
	}

	page.init();

})