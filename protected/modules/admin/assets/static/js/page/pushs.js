define(function(require, exports, module){

	var server = require('server'),
		static = require('static'),
		calendar = require('calendar'),
		Uploader = require('upload'),
		K = require('K'),
		timePicker = require('timepicker'),
		main = require('main');

	var tip = main.tip,
		wait = main.wait;

	var pushCreateView = (function(){
		var pushTypeSel;
		function getPushTypes(callback) {
			server.getPushTypes(function(resp){
				if (resp.code == 0) {
					var sel;
					if (resp.body.types.length) {
						sel = $('<select name="" id="pushType"></select>');
						$.each(resp.body.types, function(i, type){
							sel.append('<option value="'+ type.id +'">'+ type.name +'</option>');
						})					
					} else {
						sel = '还没有push类型';
					}
					callback(sel);
				}
			})			
		}

		getPushTypes(function(sel){
			pushTypeSel = sel;
		})

		function render() {
			function init(sel) {
				var pushTypeFlag = 'normal';
				var El = $('<div></div>');
				El.html(template.render('push_create_template'));
				$('#otherWrap').html(El);
				El.find('#pushTypeSel').html(sel);
				new calendar({trigger: '#bTime'});
				El.find('#bhourTime').timePicker({});
				El.on('change', '#pushType', function(e){
					var target = $(e.currentTarget);
					if (target.val() == 2) {
						pushTypeFlag = 'url';
						El.find('#urlWrap').show();
					}else{
						pushTypeFlag = 'normal';
						El.find('#urlWrap').hide();
					}
				});
				El.on('click', '#addBtn', function(){
					var title = $.trim(El.find('#title').val()),
						text = $.trim(El.find('#text').val()),
						url = $.trim(El.find('#url').val()),
						filter = $.trim(El.find('#filter').val()),
						year = El.find('#bTime').val(),
						minute = El.find('#bhourTime').val(),
						typeId = El.find('#pushType')[0] && El.find('#pushType').val();

					if (!title) {
						tip ('push标题不能为空');
					} else if (!text) {
						tip ('文字内容不能为空');
					} else if(pushTypeFlag=='url' && !url) {
						tip ('外部链接不能为空');
					} else if (!filter) {
						tip ('跳转内容不能为空');
					} else if (!year) {
						tip ('年月日不能为空');
					} else if (!minute) {
						tip ('时分秒不能为空');
					} else {
						var dialog = wait();
						var parms = {
							sendType: 1,
							typeId: typeId,
							title: title,
							text: text,
							url: url,
							filter: filter,
							publishTime: year + ' ' + minute,
							isSendNow: 0
						};
						
						server.addPush(parms, function(resp) {
							dialog.hide();
							if (resp.code == 0) {
								page.statusMechine.mainUi();
								page.enterSearch();
							} else {
								tip (resp.msg || 'push创建失败');
							}
						})
					}
				});
			}
			if (pushTypeSel) {
				init(pushTypeSel);
			} else {
				getPushTypes(function(sel){
					init(sel);
				})
			}

		}

		return {
			render: render
		}
	})();

	var pushsView = (function() {
		function instanceTableList(parms) {
			var table = new K.PaginationTable({
				ThList: [
					'编号', '推送类型', '发送类型', '接收者', '标题', '内容',
					'外部链接', '跳转至', '创建时间', '发布时间', '状态', '失败次数', '最后失败时间'
				],
				columnNameList: [
					'index',
					function(data) {
						return data.type.name;
					},
					'send_type', 'recv', 'title', 'text', 'url',
					function(data) {
						return data.filter;
					},
					'create_time', 'publish_time', 	
					function(data) {
						if (data.status == 1){return '发送成功';} 
						else if(data.status == 0) {return '未发送';}
						else if(data.status == -1) {return '删除';}
					},
					'fail_num',
					'last_fail_time'
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
					parms.size = static.pushsPerNum;
					server.getPushs(parms, function(resp){
						dialog.hide();
						if (resp.code == 0) {
							//callback(resp.body.businesses);
							if (resp.body.pushs.length){
								pag({totalPage: Math.ceil(resp.body.total_num/static.pushsPerNum)});
							}
							table(resp.body.pushs);
						} else {
							tip(resp.msg || '查询push列表出错');
						}
					})
				},
			});

			table.setEvents({
				'click #detail': 'detail',
				'click #deletes': 'deletes',
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
					msgTypeDetailView.render(row.data, function(o){
						row.set(o);
						row.refresh();
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
			page.enterSearch();
		},
		setEvents: function() {
			page.enterSearch = function() {
				pushsView.render(function(table){
					$('#tableContainer').html(table.El);
				});
			}

			$('#addBtn').click(function(){
				page.statusMechine.otherUi();
				pushCreateView.render();
			})

			$('#addPage').on('click', '#return', function(){
				page.statusMechine.mainUi();
			})
		}
	};

	page.init();

})