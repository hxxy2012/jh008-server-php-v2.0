define(function(require, exports, module){

	var $ = require('$'),
		server = require('server'),
		static = require('static'),
		Uploader = require('upload'),
		K = require('K'),
		main = require('main');

	var tip = main.tip,
		wait = main.wait;

	var userEditView = (function(){
		var El;
		function render(data) {
			page.statusMechine.otherUi();
			var El = $('<div></div>');
			var html = template.render('user_edit_template', data);
			El.html(html);
			$('#otherWrap').html(El);
			// bind-event

			$('#sellerContainer').on('click', '#saveBtn', function(){
				var	name = $.trim($('#name').val()),
					address = $.trim($('#address').val()),
					contactPhone = $.trim($('#contactPhone').val()),
					contactEmail = $.trim($('#contactEmail').val()),
					contactDescri = $.trim($('#contactDescri').val());
			
				if (!name) {
					tip('商家名称不能为空');
				} else if (!address) {
					tip('地址不能为空');
				} else if (!contactPhone) {
					tip('联系电话不能为空');
				} else if (!contactEmail) {
					tip('联系邮箱不能为空');
				} else if (!contactDescri) {
					tip('其他联系方式不能为空');
				} else {
					var parms = {
						name: name,
						address: address,
						contactPhone: contactPhone,
						contactEmail: contactEmail,
						contactDescri: contactDescri
					};
					if (logoImgId) parms.logoImgId = logoImgId;
					var dialog = wait();
					server.updateSellerInfo(parms, function(resp){
						/*var resp = {
							code: 0
						};*/
						dialog.hide();
						if (resp.code == 0) {
							tip('保存成功');
							renderShowUi();
						} else {
							tip(resp.msg || '保存有误');
						}
					})
				}
			});
		}

		return {
			render: render
		}
	})();

	var userDetailView = (function() {
		function render(data) {
			page.statusMechine.otherUi();
			var El = $('<div></div>');
			var html = template.render('user_detail_template', data);
			El.html(html);
			$('#otherWrap').html(El);
			// bind-event.
			/*El.find('#editBtn').click(function(){
				userEditView.render(data);
			})*/
		}

		return {
			render: render
		}
	})();

	var userMsgsView = (function(){
		function instanceTableList(parms) {
			var table = new K.PaginationTable({
				ThList: [
					'编号', '消息类型', '消息内容', '跳转至', '消息状态','创建时间', '发布时间'
				],
				columnNameList: [
					'index',
					function(data) {
						return data.type.name;
					},
					'content', 'filter',
					function(data) {
						return data.status == 0 ? '未读' : data.status == 1 ? '已读' : '';
					},
					'create_time', 'publish_time'
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
				source: function(o, pag, table) {
					dialog = wait();console.log(o);
					parms.page = o.currentPage;
					parms.size = static.userMsgsPerNum;
					server.getUserMsgs(parms, function(resp){
						dialog.hide();
						if (resp.code == 0) {
							//callback(resp.body.businesses);
							if (resp.body.msgs.length) {
								pag({totalPage: Math.ceil(resp.body.total_num/static.userMsgsPerNum)});
							}
							table(resp.body.msgs);
						} else {
							tip(resp.msg || '查询数据列表出错');
						}
					})
				}
			});

			table.setEvents({
				'click #detail': 'detail',
				'click #delete': 'deletes',
				'click #watch': 'watch'
			},
			{
				deletes: function(e, row) {
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
					userDetailView.render(row.data);
				},
				watch: function(e, row) {

				}
			})

			table.on('errorSwitch', function(obj){
				//console.log(obj);
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

		function render(parms, fn) {
			var table = instanceTableList(parms);
			fn(table);	
		}
		return {
			render: render
		}
	})();

	var userListView = (function() {
		function instanceTableList(parms) {
			var table = new K.PaginationTable({
				ThList: [
					'编号', '昵称', '性别', '生日', '当前状态','消息列表', '详情', '操作'
				],
				columnNameList: [
					'index',
					'nick_name',
					function(data) {
						return data.sex == 1 ? '男' : '女';
					},
					'birth',
					function(data) {
						return data.status == 0 ? '正常' : '删除';
					},
					function() {
						return '<a href="javascript:;" id="watch">查看</a>'
					},
					function() {
						return '<a href="javascript:;" id="detail" class="">详情</a>';
					},
					function() {
						return  '<a href="javascript:;" id="delete" class="">删除</a>';
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
				//source: datas,
				source: function(o, pag, table) {
					dialog = wait();
					parms.page = o.currentPage;
					parms.size = static.usersPerNum;
					server.getUsers(parms, function(resp){
						dialog.hide();
						if (resp.code == 0) {
							//callback(resp.body.businesses);
							if (resp.body.users.length) {
								pag({totalPage: Math.ceil(resp.body.total_num/static.usersPerNum)});
							}
							table(resp.body.users);
						} else {
							tip(resp.msg || '查询商户列表出错');
						}
					})
				},
				perPageNums: static.usersPerNum
			});

			table.setEvents({
				'click #detail': 'detail',
				'click #delete': 'delete',
				'click #watch': 'watch'
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
					userDetailView.render(row.data);
				},
				watch: function(e, row) {
					page.statusMechine.otherUi();
					userMsgsView.render({uid: row.data.id}, function(table) {
						$('#otherWrap').html(table.El);
					})
				}
			})

			table.on('errorSwitch', function(obj){
				//console.log(obj);
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

		function render(parms, fn) {
			var table = instanceTableList(parms);
			fn(table);		
		}
		return {
			render: render
		}
	})();

	var dustbinUsersView = (function() {
		/*function getData(parms, callback) {
			parms.page = 1;
			parms.size = 50;
			var dialog = wait();

		}*/

		function instanceTableList(parms) {
			var table = new K.PaginationTable({
				ThList: [
					'编号', '昵称', '性别', '生日', '当前状态','消息列表', '详情', '操作'
				],
				columnNameList: [
					'index',
					'nick_name',
					function(data) {
						return data.sex == 1 ? '男' : '女';
					},
					'birth',
					function(data) {
						return data.status == 0 ? '正常' : '删除';
					},
					function() {
						return '-';
					},
					function() {
						return '-';
					},
					function() {
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
				//source: datas,
				source: function(o, pag, table) {
					dialog = wait();console.log(o);
					parms.page = o.currentPage;
					parms.size = static.usersPerNum;
					server.getDelUsers(parms, function(resp){
						dialog.hide();
						if (resp.code == 0) {
							pag({totalPage: Math.ceil(resp.body.total_num/static.usersPerNum)});
							table(resp.body.users);
						} else {
							tip(resp.msg || '查询商户列表出错');
						}
					})
				},
				perPageNums: static.usersPerNum
			});

			table.on('errorSwitch', function(obj){
				//console.log(obj);
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

		function render(parms, fn) {
			//getData(parms, function(datas){
				var table = instanceTableList(parms);
				fn(table);
			//})			
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
				var keyWords = $('#keyword').val();
				userListView.render({keyWords: keyWords}, function(table){
					$('#tableContainer').html(table.El);
				});			
			}

			function searchRabish() {
				var parms = {};
				var keyWords = $('#keyword').val();
				parms.keyWords = keyWords;
				dustbinUsersView.render(parms, function(table){
					$('#tableContainer').html(table.El);
				});
			}

			function changeTabClass(e) {
				$(e.target).parents('.ui-tab-items').find('li').removeClass('ui-tab-item-current');
				$(e.target).parent().addClass('ui-tab-item-current');				
			}

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

			$('#return').click(function(){
				page.statusMechine.mainUi();
			})
		}


	};
	page.init();
})