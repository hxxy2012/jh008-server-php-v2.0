define(function(require, exports, module){

	var $ = require('$'),
		server = require('server'),
		static = require('static'),
		Uploader = require('upload'),
		K = require('K'),
		main = require('main');

	var tip = main.tip,
		wait = main.wait;

	function setUploader(id, el, callback) {
		var uploading = false;
		var uploader = new Uploader({
		    trigger: '#' + id,
		    name: 'img',
		    action: '/admin/adminInfo/imgUp',
		    accept: 'image/*',
		    data: {'isReturnUrl': 1},
		    multiple: true,
		    change: function(files) {
		    	if (!uploading) {
		    		el.find('#changeLogo').text('上传中...');
					uploading = true;
					uploader.submit();
		    	}
		    },
		    error: function(file) {
		        tip('上传logo失败');
		        el.find('#changeLogo').text('更换logo');
		        uploading = false;
		    },
		    success: function(response) {
		    	var response = $.parseJSON(response);
		    	callback(response.body)
		        el.find('#changeLogo').text('更换logo');
		        uploading = false;
		        
		    }
		});
	}

	var businessesAddView = (function(){
		function render() {
			var El, html, logoImgId;
			El = $('<div></div>');
			html = $('#business_add_template').html();
			El.html(html);
			$('#otherWrap').html(El);

			setUploader('changeLogo', El, function(res){
				El.find('#logo').attr({'src': res.img_url});
				logoImgId = res.img_id;
			})

			El.find('#addSellerBtn').click(function(){
				var	name = $.trim(El.find('#name').val()),
					address = $.trim(El.find('#address').val()),
					contactPhone = $.trim(El.find('#contactPhone').val()),
					contactEmail = $.trim(El.find('#contactEmail').val()),
					contactDescri = $.trim(El.find('#contactDescri').val()),
					uName = $.trim(El.find('#uName').val()),
					uPass = $.trim(El.find('#uPass').val());
				if (!name) {
					tip('商家名称不能为空');
				} else if (!uName) {
					tip('登录用户名不能为空');
				} else if (uName.length > 16) {
					tip('登录用户名不能多于16位');
				} else if (!uPass) {
					tip('登录密码不能为空');
				} else if (uPass.length < 6 || uPass.length > 16) {
					tip('登录密码6-16位');
				} else if (!logoImgId) {
					tip('头像必须上传');
				} else {
					var dialog = wait();
					server.addBusiness({
						uName: uName,
						uPass: uPass,
						name: name,
						address: address,
						contactPhone: contactPhone,
						contactEmail: contactEmail,
						contactDescri: contactDescri,
						logoImgId: logoImgId
					}, function(resp){
						dialog.hide();
						if (resp.code == 0) {
							var d = tip('添加成功');
							setTimeout(function(){
								d.hide();
								page.statusMechine.mainUi();
								$('searchBtn').trigger('click');
							}, 2000);
						} else {
							tip(resp.msg || '添加商家失败');
						}
					})
				}
			})
		}
		return {
			render: render
		}
	})();

	var businessEditView = (function(){
		function render(data) {
			var logoImgId, El, html;
			page.statusMechine.otherUi();
			El = $('<div></div>');
			html = template.render('business_edit_template', data);
			El.html(html);
			$('#otherWrap').html(El);
			// bind-event
			El.find('#savePwdrBtn').click(function(){
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
					server.updateBusiness({newPass: newPass, oldPass: oldPass}, function(resp){
						/*var resp = {
							code: 0
						};*/
						dialog.hide();
						if (resp.code == 0) {
							tip('修改成功');
							$('#oldPwd').val('');
							$('#Pwd').val('');
							$('#surePwd').val('');
							renderShowUi();
						} else {
							tip(resp.msg || '保存有误');
						}
					})
				}
			});	

			El.find('#saveSellerBtn').click(function(){
				var	name = $.trim($('#name').val()),
					address = $.trim($('#address').val()),
					contactPhone = $.trim($('#contactPhone').val()),
					contactEmail = $.trim($('#contactEmail').val()),
					contactDescri = $.trim($('#contactDescri').val());
			
				if (!name) {
					tip('商家名称不能为空');
				} else if (!data.logo_img_url && !logoImgId) {
					tip('必须上传商家LOGO');
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
					server.updateBusiness(parms, function(resp){
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

			setUploader('changeLogo', El, function(res){
				El.find('#logo').attr({'src': res.img_url});
				logoImgId = res.img_id;
			})
			
		}

		return {
			render: render
		}
	})();

	var businessDetailView = (function() {
		var El;
		function render(data) {
			page.statusMechine.otherUi();
			var El = $('<div></div>');
			var html = template.render('business_detail_template', data);
			El.html(html);
			$('#otherWrap').html(El);
			// bind-event.
			El.find('#editBusinessBtn').click(function(){
				businessEditView.render(data);
			})
		}

		return {
			render: render
		}
	})();
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
		function render(bid) {
			page.statusMechine.otherUi();
			var parms = {};
			parms.bid = bid;
			parms.page = 1;
			parms.size = static.logsPerNum;
			var table = instanceTableList(parms);
			$('#otherWrap').html(table.El);
		}
		return {
			render: render
		}
	}();

	var businessView = (function() {
		/*function getData(parms, callback) {
			parms.page = 1;
			parms.size = 50;
			var dialog = wait();

		}*/

		function instanceTableList(parms) {
			var table = new K.PaginationTable({
				ThList: [
					'编号', '用户名', '当前状态', '详情', '操作'
				],
				columnNameList: [
					'index',
					'name',
					function(data) {
						return data.u_name == 0 ? '未完成' : '已完成';
					},
					function() {
						return '<a href="javascript:;" id="detail" class="">详情</a>';
					},
					function() {
						return  '<a href="javascript:;" id="delete" class="">删除</a>' +
								'<a href="javascript:;" id="watchLogs" class="ml10">查看日志</a>'
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
					parms.size = static.businessesPerNum;
					server.getBusinesses(parms, function(resp){
						dialog.hide();
						if (resp.code == 0) {
							//callback(resp.body.businesses);
							if (resp.body.businesses.length){
								pag({totalPage: Math.ceil(resp.body.total_num/static.businessesPerNum)});
							}
							table(resp.body.businesses);
						} else {
							tip(resp.msg || '查询商户列表出错');
						}
					})
				},
				perPageNums: static.businessesPerNum

			});

			table.setEvents({
				'click #detail': 'detail',
				'click #delete': 'deletes',
				'click #watchLogs': 'watchLogs'
			},
			{
				watchLogs: function(e, row) {
					renderLogsList.render(row.data.id);
				},
				deletes: function(e, row) {
					var dialog = wait();
					server.deleteBusiness({businessId: row.data.id}, function(resp) {
						dialog.hide();
						if (resp.code == 0) {
							tip(row.data.name + '删除成功');
							row.destory();
						} else {
							tip(resp.msg || '删除出错');
						}
					})
				},
				detail: function(e, row) {
					businessDetailView.render(row.data);
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
			//getData(parms, function(datas){
				var table = instanceTableList(parms);
				fn(table);
			//})			
		}


		return {
			render: render
		}
	})();

	var dustbinBusinessesView = (function() {
		/*function getData(parms, callback) {
			parms.page = 1;
			parms.size = 50;
			var dialog = wait();

		}*/

		function instanceTableList(parms) {
			var table = new K.PaginationTable({
				ThList: [
					'编号', '用户名', '当前状态', '详情', '操作'
				],
				columnNameList: [
					'index',
					'name',
					function(data) {
						return data.u_name == 0 ? '未完成' : '已完成';
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
					dialog = wait();
					parms.page = o.currentPage;
					parms.size = static.businessesPerNum;
					server.getDelBusinesses(parms, function(resp){
						dialog.hide();
						if (resp.code == 0) {
							//callback(resp.body.businesses);
							if (resp.body.businesses.length) {
								pag({totalPage: Math.ceil(resp.body.total_num/static.businessesPerNum)});
							}
							table(resp.body.businesses);
						} else {
							tip(resp.msg || '查询商户列表出错');
						}
					})
				},
				perPageNums: static.businessesPerNum
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
				businessView.render({keyWords: keyWords}, function(table){
					$('#tableContainer').html(table.El);
				});
			}

			function searchRabish() {
				var parms = {};
				var keyWords = $('#keyword').val();
				parms.keyWords = keyWords;
				dustbinBusinessesView.render(parms, function(table){
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

			$('#addBtn').click(function(){
				page.statusMechine.otherUi();
				businessesAddView.render();
			})
		}


	};
	page.init();
})