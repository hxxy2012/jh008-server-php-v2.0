define(function(require, exports, module){

	var $ = require('$'),
		server = require('server'),
		static = require('static'),
		Uploader = require('upload'),
		K = require('K'),
		main = require('main');

	var tip = main.tip,
		wait = main.wait;

	function getAdminList(callback) {
		var dialog = wait();
		server.getAdminList(function(resp){
			dialog.hide();
			if (resp.code == 0) {
				callback(resp.body.admins);
			} else {
				tip (resp.msg || '获取管理员列表失败');
			}
		})
	}

	// 日志列表
	var renderLogsList = function() {
		function instanceTableList(parms) {
			var table = new K.PaginationTable({
				ThList: [
					'编号', '日志类型', '日志内容', '状态', '操作时间'
				],
				columnNameList: [
					'index',
					'type',
					'operate',
					function(data) {
						if (data.status == -1) {
							return '已删除';
						} else {
							return '正常';
						}
					},
					'op_time'
				],
				//rowClass: 'abc',
				rowClass: function(index) {
					if (index%2 == 0) {
						return 'odd';
					} else {
						return 'even';
					}
				},
				//source: datas,
				source: function(o, pag, table) {console.log(o);
					dialog = wait();
					parms.page = o.currentPage;
					server.getLogs(parms, function(resp){
						dialog.hide();
						if (resp.code == 0) {
							//callback(resp.body.businesses);
							if (resp.body.logs.length) {
								pag({totalPage: Math.ceil(resp.body.total_num/static.logsPerNum)});
							}
							table(resp.body.logs);
						} else {
							tip(resp.msg || '查询商户列表出错');
						}
					})
				},
				perPageNums: static.logsPerNum
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
		function render(aid) {
			page.statusMechine.showEditUi();
			var parms = {};
			parms.aid = aid;
			parms.page = 1;
			parms.size = static.logsPerNum;
			var table = instanceTableList(parms);
			$('#operateWrap').html(table.El);
		}
		return {
			render: render
		}
	}()

	function getRabishAdminList(callback) {
		var dialog = wait();
		server.getDeladmins(function(resp){
			dialog.hide();
			if (resp.code == 0) {
				callback(resp.body.admins);
			} else {
				tip (resp.msg || '获取回收站管理员列表失败');
			}	
		})
	}

	function renderAdminList(datas) {
		var table = new K.PaginationTable({
			ThList: [
				'编号', '用户名', '昵称', '状态', '创建时间', '最后一次登录时间', '操作'
			],
			columnNameList: [
				'index',
				'u_name',
				'nick_name',
				function(data) {
					return data.status == 0 ? '正常' : '已删除';
				},
				'create_time',
				'last_login_time',
				function() {
					return  '<a href="javascript:;" id="delete" class="">删除</a>' +
							'<a href="javascript:;" id="detail" class="ml10">详细</a>' +
							'<a href="javascript:;" id="watchLogs" class="ml10">查看日志</a>';
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
			perPageNums: static.adminListPerNum
		});

		table.setEvents({
			'click #delete': 'delete',
			'click #detail': 'detail',
			'click #watchLogs': 'watchLogs'
		},
		{
			delete: function(e, row) {
				var dialog = wait();
				server.delAdmin({adminId: row.data.id}, function(resp) {
					dialog.hide();
					if (resp.code == 0) {
						tip('管理员' + row.data.u_name + '删除成功');
						row.destory();
					} else {
						tip(resp.msg || '删除出错');
					}
				})
			},
			detail: function(e, row) {
				renderDetailAdminUi(row.data, function(o){
					row.set(o);
					row.refresh();
				});
			},
			watchLogs: function(e, row) {
				renderLogsList.render(row.data.id);
			}
		})

		table.on('errorSwitch', function(obj){
			console.log(obj);
			if(obj.type == 'switch'){
				if(obj.page == 1){
					alert('已经是第一页')
				}else{
					alert('已经是最后一页')
				}
			}else if(obj.type == 'submit') {
				if(obj.page === ''){
					alert('不能为空')
				}else{
					alert('页码不正确');
				}
			}
		})

		table.run();
		return table;
	}


	function renderRabishAdminList(datas) {
		var table = new K.PaginationTable({
			ThList: [
				'编号', '用户名', '昵称', '状态', '创建时间', '最后一次登录时间'
			],
			columnNameList: [
				'index',
				'u_name',
				'nick_name',
				function(data) {
					return data.status == 0 ? '正常' : '已删除';
				},
				'create_time',
				'last_login_time'
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
			perPageNums: static.adminListPerNum
		});

		table.on('errorSwitch', function(obj){
			console.log(obj);
			if(obj.type == 'switch'){
				if(obj.page == 1){
					alert('已经是第一页')
				}else{
					alert('已经是最后一页')
				}
			}else if(obj.type == 'submit') {
				if(obj.page === ''){
					alert('不能为空')
				}else{
					alert('页码不正确');
				}
			}
		})

		table.run();
		return table;
	}	

	function renderAddAdminUi() { // create admin ui & events bind.
		var headImgId;
		page.statusMechine.showEditUi();
		var El = $($('#add_admin_template').html());
		$('#operateWrap').html(El);
		var uploading = false;
		var uploader = new Uploader({
		    trigger: '#changeLogo',
		    name: 'img',
		    action: '/admin/adminInfo/imgUp',
		    accept: 'image/*',
		    data: {'isReturnUrl': 1},
		    multiple: true,
		    change: function(files) {
		    	if (!uploading) {
		    		$('#changeLogo').text('上传中...');
					uploading = true;
					uploader.submit();
		    	}
		    },
		    error: function(file) {
		        tip('上传logo失败');
		        $('#changeLogo').text('更换logo');
		        uploading = false;
		    },
		    success: function(response) {
		    	var response = $.parseJSON(response);
		    	uploading = false;
		    	if (response.code == 0){
			        headImgId = response.body.img_id;
			        El.find('#changeLogo').text('更换logo');
			        El.find('#logo').attr({'src': response.body.img_url});		    		
		    	} else {
		    		tip(response.msg);
		    		El.find('#changeLogo').text('上传logo');
		    	}

		    }
		});
		El.find('#addAdminBtn').click(function(){
			var uName = $.trim($('#uName').val()),
				uPass = $.trim($('#uPass').val()),
				nickName = $.trim($('#nickName').val());
			if (!uName) { tip('用户名不能为空') } 
			else if (uName.length > 16 ) { tip('用户名长度不能多于16位') }
			else if (!uPass) { tip('密码不能为空') }
			else if (uPass.length < 6 || uPass.length > 16) { tip('密码长度6-16位')  }
			else if (!nickName) { tip('昵称不能为空') }
			else if (!headImgId){
				tip('必须上传头像图片');
			} else {
				var params = {
					uName: uName,
					uPass: uPass,
					nickName: nickName,
					headImgId: headImgId
				};
				var dialog = wait();
				server.addAdmin(params, function(resp) {
					dialog.hide();
					if (resp.code == 0) {
						$('#adminList').trigger('click');
					} else {
						tip(resp.msg || '添加管理员失败');
					}
				})
			}
		})
	}

	function renderDetailAdminUi(data, callback) { // 
		page.statusMechine.showEditUi();
		var El = $(template.render('adminShowTemplate', data));
		$('#operateWrap').html(El);
		El.find('#editAdminBtn').click(function(){
			renderEditAdminUi(data, callback);
		});
	}

	function renderEditAdminUi(data, callback) {
		var headImgId, headImgUrl;
		page.statusMechine.showEditUi();
		var El = $(template.render('adminEditTemplate', data));
		$('#operateWrap').html(El);

		var uploading = false;
		var uploader = new Uploader({
		    trigger: '#changeLogo',
		    name: 'img',
		    action: '/admin/adminInfo/imgUp',
		    accept: 'image/*',
		    data: {'isReturnUrl': 1},
		    multiple: true,
		    change: function(files) {
		    	if (!uploading) {
		    		El.find('#changeLogo').text('上传中...');
					uploading = true;
					uploader.submit();
		    	}
		    },
		    error: function(file) {
		        tip('上传logo失败');
		        El.find('#changeLogo').text('更换logo');
		        uploading = false;
		    }, 
		    success: function(response) {
		    	var response = $.parseJSON(response);
		    	uploading = false;
		    	if (response.code == 0){
			        data.logo_img_url = response.body.img_url;
			        headImgId = response.body.img_id;
			        headImgUrl = response.body.img_url;
			        El.find('#logo').attr({'src': response.body.img_url});
		    	} else {
		    		tip(response.msg);
		    	}
		    	El.find('#changeLogo').text('更换logo');
		    }
		});

		El.on('click', '#saveAdminBtn', function(){
			var	nickName = $.trim($('#nickName').val());
		
			if (!nickName) {
				tip('昵称不能为空');
			}else {
				var parms = {
					nickName: nickName,
					adminId: data.id
				};
				if (headImgId) parms.headImgId = headImgId;
				var dialog = wait();
				server.updateAdmin(parms, function(resp){
					dialog.hide();
					//var resp = {code: 0};
					if (resp.code == 0) {
						tip('保存成功');
						callback({nick_name: nickName, head_img_url: headImgUrl});
						page.statusMechine.showListUi();
					} else {
						tip(resp.msg || '保存有误');
					}
				})
			}
		})
		El.on('click', '#saveAdminPwdrBtn', function(){
			var oldPass = $.trim($('#oldPwd').val()),
				newPass = $.trim($('#Pwd').val()),
				reNewPass = $.trim($('#surePwd').val());
			if (!oldPass) {
				tip('旧密码不能为空');
				$('#oldPwd').focus();
			} else if (!newPass) {
				tip('新密码不能为空');
				$('#Pwd').focus();
			} else if (!reNewPass) {
				tip('确认密码不能为空');
				$('#surePwd').focus();
			} else if (oldPass.length<6 || oldPass.length>16) {
				tip('旧密码长度不正确');
				$('#oldPwd').focus();
			} else if (newPass.length<6 || newPass.length>16) {
				tip('新密码长度不正确');
				$('#Pwd').focus();
			} else if (reNewPass.length<6 || reNewPass.length>16) {
				tip('确认密码长度不正确');
				$('#surePwd').focus();
			} else if (newPass != reNewPass) {
				tip('两次密码不相同');
				$('#surePwd').focus();
			} else {
				var dialog = wait();
				server.updateAdmin({adminId: data.id, newPass: newPass, oldPass: oldPass}, function(resp){
					dialog.hide();
					if (resp.code == 0) {
						tip('修改成功');
						$('#oldPwd').val('');
						$('#Pwd').val('');
						$('#surePwd').val('');
					} else {
						tip(resp.msg || '保存有误');
					}
				})
			}
		})		
	}

	var page = {
		statusMechine: {
			adminList: function(e) {
				$('#adminListCon').show();
				$('#rabishListCon').hide();
				$('.ui-tab-items li').removeClass('ui-tab-item-current');
				$(e.target).parent('li').addClass('ui-tab-item-current');
			},
			rabishList: function(e) {
				$('#adminListCon').hide();
				$('#rabishListCon').show();
				$('.ui-tab-items li').removeClass('ui-tab-item-current');
				$(e.target).parent('li').addClass('ui-tab-item-current');
			},
			showListUi: function() {
				$('#operateAdmin').hide();
				$('#adminListWrap').show();
			},
			showEditUi: function() {
				$('#operateAdmin').show();
				$('#adminListWrap').hide();
			}
		},
		admins: '',
		init: function() {
			this.setEvents();
			$('#adminList').trigger('click');
		},
		setEvents: function() {
			$('#adminList').click(function(e){
				page.statusMechine.adminList(e);
				page.statusMechine.showListUi(e);
				getAdminList(function(admins){
					var table = renderAdminList(admins);
					$('#adminListPanel').html(table.El);
				})
			})

			$('#rabishList').click(function(e){
				page.statusMechine.rabishList(e);
				getRabishAdminList(function(admins){
					var table = renderRabishAdminList(admins);
					$('#rabishListCon').html(table.El);
				})
			})

			$('#createAdmin').click(function(){
				renderAddAdminUi();
			})

			$('#return').click(function(){
				page.statusMechine.showListUi();
			});
		}


	};
	page.init();
})