define(function(require, exports, module){

	var 
		server = require('server'),
		static = require('static'),
		Uploader = require('upload'),
		calendar = require('calendar'),
		K = require('K'),
		timePicker = require('timepicker'),
		main = require('main');

	var tip = main.tip,
		wait = main.wait;

	var msgCreateView = (function(){
		var msgTypeSel;
		function getMsgTypes(callback) {
			server.getMsgTypes({}, function(resp){
				if (resp.code == 0) {
					var sel;
					if (resp.body.types.length) {
						sel = $('<select name="" id="msgsType"></select>');
						$.each(resp.body.types, function(i, type){
							sel.append('<option value="'+ type.id +'">'+ type.name +'</option>');
						})					
					} else {
						sel = '还没有消息类型';
					}
					callback(sel);
				}
			})			
		}

		getMsgTypes(function(sel){
			msgTypeSel = sel;
		});

		function render() {
			function init(sel) {
				var El = $('<div></div>');
				El.html(template.render('msg_create_template'));
				$('#otherWrap').html(El);
				El.find('#msgTypeSel').html(sel);
				new calendar({trigger: '#bTime'});
				El.find('#bhourTime').timePicker({});
				El.on('click', '#addBtn', function(){
					var year = El.find('#bTime').val(),
						minute = El.find('#bhourTime').val(),
					typeId = El.find('#msgsType')[0] && El.find('#msgsType').val(),
					title = El.find('#title').val(),
					content = El.find('#content').val(),
					filter = El.find('#filter').val(),
					isPublishNow = El.find('input[type=radio][name="sendsure"]:checked').val();
					//publishTime = 
					if (!typeId) {
						tip ('消息类型不能为空');
					} else if (!title) {
						tip ('标题不能为空');
					} else if(!content) {
						tip ('内容不能为空');
					} else if(!filter) {
						tip ('跳转不能为空');
					} else if(!year) {
						tip ('年月日不能为空');
					} else if(!minute) {
						tip ('时分秒不能为空');
					} else {
						var dialog = wait();
						var parms = {
							typeId: typeId,
							title: title,
							content: content,
							filter: filter,
							isPublishNow: isPublishNow,
							publishTime: year + ' ' + minute
						};
						server.addMsg(parms, function(resp) {
							dialog.hide();
							if (resp.code == 0) {
								page.statusMechine.mainUi();
								$('#searchBtn').trigger('click');
							} else {
								tip (resp.msg || '消息类型创建失败');
							}
						})
					}
				});				
			}

			if (!msgTypeSel) {
				getMsgTypes(function(sel){
					init(sel);
				})
			} else {
				init(msgTypeSel);
			}

		}

		return {
			render: render
		}
	})();

	var msgsDetailView = (function(){

		function render(data, callback) {
			var El = $('<div></div>');
			El.html(template.render('msg_detail_template', data));
			$('#otherWrap').html(El);
			El.find('#editBtn').bind('click', function(e){
				msgEditView.render(data, callback);
			});
		}

		return {
			render: render
		}
	})();

	var msgEditView = (function(){
		function render(data, callback) {
			page.statusMechine.otherUi();
			var El = $('<div></div>');
			var html = template.render('msg_edit_template', data);
			El.html(html);
			$('#otherWrap').html(El);
			// bind-event
			El.find('#saveBtn').click(function(){
				var title = $.trim(El.find('#title').val()),
					content = $.trim(El.find('#content').val()),
					filter = $.trim(El.find('#filter').val());
				if (!title) {
					tip ('消息标题不能为空');
				} else if (!content) {
					tip ('消息内容不能为空');
				} else if(!filter) {
					tip ('消息跳转不能为空')
				} else {
					var dialog = wait();
					server.updateMsg({title: title, content: content, filter: filter, msgId: data.id}, function(resp) {
						dialog.hide();
						if (resp.code == 0) {
							page.statusMechine.mainUi();
							callback({title: title, content: content, filter: filter});
						} else {
							tip (resp.msg || '消息修改失败');
						}
					})
				}
			})
		}

		return {
			render: render
		}
	})();

	var msgRevUsersView = (function(){

		function instanceTableList(parms) {
			var table = new K.PaginationTable({
				ThList: [
					'头像', '用户昵称', '用户性别', '用户生日', '地址', '邮箱',
					'真实姓名', '联系QQ', '联系电话', '状态'
				],
				columnNameList: [
					function(data){
						return '<img src="'+ data.head_img_url +'" alt="" />';
					},
					'nick_name','sex','birth','address','email','real_name','contact_qq','contact_phone',

					function(data) {
						return data.type.name;
					},
					'content', 'filter', 
					function(data) {
						if (data.status == -1){return '删除';} else {return '正常';}
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
				source: function(o, pag, table) {
					dialog = wait();
					parms.page = o.currentPage;
					parms.size = static.msgRevUserNum;
					server.getMsgRevUsers(parms, function(resp){
						dialog.hide();
						if (resp.code == 0) {
							//callback(resp.body.businesses);
							if (resp.body.msgs.length){
								pag({totalPage: Math.ceil(resp.body.total_num/static.msgRevUserNum)});
							}
							table(resp.body.msgs);
						} else {
							tip(resp.msg || '查询消息列表出错');
						}
					})
				},
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

		function render(msgId, fn) {
			var parms = {msgId: msgId};
			var table = instanceTableList(parms);
			fn(table);
		}

		return {
			render: render
		}
	})();

	var msgsView = (function() {
		function instanceTableList(parms) {
			var table = new K.PaginationTable({
				ThList: [
					'编号', '消息类型', '消息内容', '消息跳转', '消息状态', '详情',
					'查看收到消息的用户', '创建时间', '发布时间', '删除'
				],
				columnNameList: [
					'index',
					function(data) {
						return data.type.name;
					},
					'content', 'filter', 
					function(data) {
						if (data.status == 1){return '已读';} else {return '未读';}
					},
					function(data) {
						return  '<a href="javascript:;" id="detail" class="">详情</a>';
					},
					function(data) {
						return  '<a href="javascript:;" id="watch" class="">查看</a>';
					},
					'create_time', 'publish_time', 	
					function(data) {
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
				source: function(o, pag, table) {
					dialog = wait();
					parms.page = o.currentPage;
					parms.size = static.msgsPerNum;
					server.getMsgs(parms, function(resp){
						dialog.hide();
						if (resp.code == 0) {
							//callback(resp.body.businesses);
							if (resp.body.msgs.length){
								pag({totalPage: Math.ceil(resp.body.total_num/static.msgsPerNum)});
							}
							table(resp.body.msgs);
						} else {
							tip(resp.msg || '查询消息列表出错');
						}
					})
				},
			});

			table.setEvents({
				'click #detail': 'detail',
				'click #delete': 'deletes',
				'click #watch': 'watch'
			},
			{
				deletes: function(e, row) {
					var dialog = wait();
					server.delMsg({msgId: row.data.id}, function(resp) {
						dialog.hide();
						if (resp.code == 0) {
							tip(row.data.name + ' 删除成功');
							row.destory();
						} else {
							tip(resp.msg || '删除出错');
						}
					})
				},
				detail: function(e, row) {
					page.statusMechine.otherUi();
					msgsDetailView.render(row.data, function(o){
						row.set(o);
						row.refresh();
					});
				},
				watch: function(e, row) {
					page.statusMechine.otherUi();
					msgRevUsersView.render(row.data.id, function(table){
						$('#otherWrap').html(table.El);
					});
				}
			})

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

		function render(fn) {
			var parms = {};
			var table = instanceTableList(parms);
			fn(table);		
		}


		return {
			render: render
		}
	})();

	var dustbinMsgsView = (function() {
		function instanceTableList(parms) {
			var table = new K.PaginationTable({
				ThList: [
					'编号', '消息类型', '消息内容', '消息跳转', '消息状态', '详情',
					'查看收到消息的用户', '创建时间', '发布时间', '删除'
				],
				columnNameList: [
					'index',
					function(data) {
						return data.type.name;
					},
					'content', 'filter', 
					function(data) {
						if (data.status == 1){return '已读';} else {return '未读';}
					},
					function(data) {
						return  '-';
					},
					function(data) {
						return  '-';
					},
					'create_time', 'publish_time', 	
					function(data) {
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
				source: function(o, pag, table) {
					dialog = wait();
					parms.page = o.currentPage;
					parms.size = static.msgsPerNum;
					server.getDelMsgs(parms, function(resp){
						dialog.hide();
						if (resp.code == 0) {
							//callback(resp.body.businesses);
							if (resp.body.msgs.length){
								pag({totalPage: Math.ceil(resp.body.total_num/static.msgsPerNum)});
							}
							table(resp.body.msgs);
						} else {
							tip(resp.msg || '查询消息列表出错');
						}
					})
				},
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

		function render(fn) {
			var parms = {};
			var table = instanceTableList(parms);
			fn(table);		
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

			$('#normalList')[0].click();
		},
		setEvents: function() {
			function searchNormal() {
				msgsView.render(function(table){
					$('#tableContainer').html(table.El);
				});
			}

			function searchRabish() {
				dustbinMsgsView.render(function(table){
					$('#tableContainer').html(table.El);
				});
			}

			function changeTabClass(e) {
				$(e.target).parents('.ui-tab-items').find('li').removeClass('ui-tab-item-current');
				$(e.target).parent().addClass('ui-tab-item-current');				
			}
			
			$('#addBtn').click(function() {
				page.statusMechine.otherUi();
				msgCreateView.render();
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

			$('#addPage').on('click', '#return', function(){
				page.statusMechine.mainUi();
			})


		}


	};
	page.init();
})