define(function(require, exports, module){
	var $ = require('$'),
		server = require('server'),
		static = require('static'),
		K = require('K'),
		Uploader = require('upload'),
		main = require('main');

	var wait = main.wait,
		tip = main.tip;

	var appsView = (function(){


		function instanceTableList(parms) {
			var table = new K.PaginationTable({
				ThList: [
					'版本id', '版本类型', '版本号', '版本名称', '版本描述', '下载', '状态', '删除',
					'修改版本', '版本创建时间'
				],
				columnNameList: [
					'id',
					function (data) {
						if (data.type == 1) {
							return '安卓';
						} else {
							return '';
						}
					},
					'code', 'name', 'descri', 
					function (data) {
						return '<a target="_blank" href="'+ data.app_url +'">下载</a>';
					},
					function (data) {
						if (data.status == -1) {
							return '删除';
						} else if (data.status == 0) {
							return '正常';
						}
					},
					function (data) {
						return '<a href="javascript:;" id="delete">删除</a>'
					},
					function (data) {
						return '<a href="javascript:;" id="update">修改</a>'
					},
					'create_time'
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
					server.getApps(parms, function(resp){
						dialog.hide();
						/*var resp = {
							code: 0,
							body: {
								total_num: 2,
								apps: [{
									id: 1,
									type: 1,
									code: 'skdfds',
									name: 'v1',
									descri: '234',
									app_url: 'http://www.baidu.com',
									status: 0,
									create_time: '1997-07-01 09:00:00'
								}]
							}
						};		*/	
						if (resp.code == 0) {
							if (resp.body.apps.length) {
								pag({totalPage: Math.ceil(resp.body.total_num/static.appsPerNum)});
							}
							table(resp.body.apps);
						} else {
							tip(resp.msg || '查询商户列表出错');
						}
					})
				}
			});

			table.setEvents({
				'click #delete': 'delete',
				'click #update': 'update'
			},
			{
				delete: function(e, row) {
					var dialog = wait();
					/*var resp = {
						code: 0
					}*/
					server.delApp({appId: row.data.id}, function(resp){
						dialog.hide();
						if (resp.code == 0) {
							tip('删除成功');
							row.destory();
						} else {
							tip(resp.msg || '删除出错');
						}
					})
				},
				update: function(e, row) {
					page.statusMechine.otherUi();
					editApkView.render(row.data,  function(o){
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

		function render(parms, fn) {
			//getData(parms, function(datas){
				parms.page = 1;
				parms.size = static.actsPerNum;
				var table = instanceTableList(parms);
				fn(table);
			//})			
		}


		return {
			render: render
		}
	})();

	var dustbinAppsView = (function(){


		function instanceTableList(parms) {
			var table = new K.PaginationTable({
				ThList: [
					'版本id', '版本类型', '版本号', '版本名称', '版本描述', '状态',
					'版本创建时间'
				],
				columnNameList: [
					'id',
					function (data) {
						if (data.type == 1) {
							return '安卓';
						} else {
							return '';
						}
					},
					'type', 'name', 'descri',
					function (data) {
						if (data.status == -1) {
							return '删除';
						} else if (data.status == 0) {
							return '正常';
						}
					},
					'create_time'
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
					server.getDelApps(parms, function(resp){
						dialog.hide();
						if (resp.code == 0) {
							if (resp.body.apps.length) {
								pag({totalPage: Math.ceil(resp.body.total_num/static.appsPerNum)});
							}
							table(resp.body.apps);
						} else {
							tip(resp.msg || '查询商户列表出错');
						}
					})
				}
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

		function render(parms, fn) {
			//getData(parms, function(datas){
				parms.page = 1;
				parms.size = static.actsPerNum;
				var table = instanceTableList(parms);
				fn(table);
			//})			
		}


		return {
			render: render
		}
	})();

	var addApkView = (function(){
		function render() {
			var upId;
			var El = $('<div></div>');
			El.html(template.render('apps_add_template', {}));
			$('#otherWrap').html(El);
			var flag = false;
			var uploader = new Uploader({ 
			    trigger: '#addApk',
			    name: 'file',
			    action: '/admin/appInfo/upload'
			}).change(function(files){
				if (!flag) {
					flag = true;
					El.find('#addApk').text('上传中...');
				    uploader.submit();					
				}
			}).success(function(response) {
			    var response = $.parseJSON(response);
			    if (response.code == 0) {
			    	upId = response.body.up_id;
			    } else {
			    	tip(response.msg);
			    }
			    El.find('#apkWrap').text('已上传');
			    El.find('#addApk').text('上传APK');
			    var flag = false;
			}).error(function(file){
				tip('上传APK失败');
				El.find('#addApk').text('上传APK');
				var flag = false;
			})

			El.on('click', '#saveBtn', function(){
				var code = $.trim(El.find('#code').val()),
					name = $.trim(El.find('#name').val()),
					descri = $.trim(El.find('#descri').val()),
					type = $('#typeWrap input[name="type"]:checked').val();
				if (!upId) {
					tip('必须上传apk');
				} else if(!type) {
					tip('必须选择类型');
				} else if(!code) {
					tip('必须填写版本号	');
				} else if(!name) {
					tip('必须填写版本名称	');
				} else if(!descri) {
					tip('必须填写版本描述');
				} else {
					server.addApp({
							type: type,
							code: code,
							name: name,
							descri: descri,
							upId: upId },
						function(resp){
							if (resp.code == 0) {
								tip('上传成功');
								$('#return')[0].click();
								$('#searchBtn')[0].click();
							} else {
								tip(resp.msg || '上传失败');
							}
						})					
				}

				
			});
		}
		return {
			render: render
		}
	})();

	var editApkView = (function(){
		function render(data, callback) {
			var upId, appId;
			if (data) appId = data.id;
			var El = $('<div></div>');
			El.html(template.render('apps_update_template', data));
			$('#otherWrap').html(El);
			var flag = false;
			var uploader = new Uploader({ 
			    trigger: '#updateApk',
			    name: 'file',
			    action: '/admin/appInfo/upload'
			}).change(function(files){
				if (!flag) {
					flag = true;
					El.find('#addApk').text('上传中...');
				    uploader.submit();					
				}
			}).success(function(response) {
			    var response = $.parseJSON(response);
			    if (response.code == 0) {
			    	upId = response.body.up_id;
			    } else {
			    	tip(response.msg);
			    }
			    El.find('#addApk').text('上传APK');
			    flag = false;
			}).error(function(file){
				tip('上传APK失败');
				El.find('#addApk').text('上传APK');
				flag = false;
			})

			El.on('click', '#updateBtn', function(){
				var code = $.trim(El.find('#code').val()),
					name = $.trim(El.find('#name').val()),
					descri = $.trim(El.find('#descri').val()),
					type = $('#typeWrap input[name="type"]:checked').val();
				if(!data.app_url && !upId) {
					tip('必须上传apk');
				} if(!type) {
					tip('必须选择类型');
				} else if(!code) {
					tip('必须填写版本号');
				} else if(isNaN(code)){
					tip('版本号必须为数字');
				} else if(!name) {
					tip('必须填写版本名称');
				} else if(!descri) {
					tip('必须填写版本描述');
				} else {
					server.updateApp({
							type: type,
							code: code,
							name: name,
							descri: descri,
							appId: appId,
							upId: upId },
						function(resp){
							if (resp.code == 0) {
								tip('上传成功');
								$('#return')[0].click();
								callback({
									type: type,
									code: code,
									name: name,
									descri: descri
								});
							} else {
								tip('修改失败');
							}
					})					
				}
			});
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
			var saveCallback;
			this.setEvents();
			page.statusMechine.mainUi();

			this.pageStatus = 'normal';
			$('#searchBtn').trigger('click');
		},
		setEvents: function() {

			function searchNormal() {
				var parms = {};
				var keyWords = $('#keyword').val();
				parms.keyWords = keyWords;
				appsView.render(parms, function(table){
					$('#tableContainer').html(table.El);
				});				
			}

			function searchRabish() {
				var parms = {};
				var keyWords = $('#keyword').val();
				parms.keyWords = keyWords;
				dustbinAppsView.render(parms, function(table){
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

			$('#addPage').on('click', '#return', function(){
				page.statusMechine.mainUi();
			})

			$('#createBtn').click(function() {
				page.statusMechine.otherUi();
				addApkView.render();
			})
		}


	};
	page.init();
})