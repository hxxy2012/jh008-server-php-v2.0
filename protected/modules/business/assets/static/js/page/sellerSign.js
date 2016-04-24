define(function(require, exports, module){
	var static = require('static'),
		K = require('K'),
		server = require('server'),
		main = require('main');

	var tip = main.tip,
		wait = main.wait;


	$(function(){

		var checkinsList,
			userSelected;
			
		var pageStatusMechine = (function(){
			var homeEl = $('.homePage'),
				detailEl = $('.detailPage');

			var statusMechine = {
				homePage: function() {
					homeEl.show();
					detailEl.hide();
				},
				detailPage: function() {
					homeEl.hide();
					detailEl.show();
				}
			}

			return {
				// type {String} main | add
				switch: function(type, fn){
					(type == 'home') && (statusMechine.homePage());
					(type == 'detail') && (statusMechine.detailPage());
				}	
			};
		})();

		// 查询结果、渲染视图.
		function search(datas) {
			var table = new K.PaginationTable({
				ThList: [
					'编号', '活动名称', '发布时间', '当前状态', , {text: '签到数量', isOrderBy: true},
					'详情', '删除'
				],
				columnNameList: [
					'index',
					'title',
					'publish_time',
					function(data) {
						return static.tStatus[data.t_status];
					},
					'checkin_num',
					function() {
						return '<a href="javascript:;" id="detail" class="">详情</a>';
					},
					function(data) {
						var result = '';
						if (data.status == 0 || data.status == 3 || data.status == 4 ||data.status==6){
							result += '<a href="javascript:;" id="delete" class="">删除</a>'
						}
						return  result;
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
				perPageNums: 50
			});

			table.setEvents(
				{
					"click #detail": "detail",
					"click #delete": "delete"
				},
				{
					detail: function(e, row) {
						pageStatusMechine.switch('detail');
						// autocomplete(row.data.id);
						enterDetailPage(row.data.id);
					},
					delete: function(e, row) {
						server.delActivity({actId: row.data.id}, function(resp){
							if (resp.code == 0) {
								tip('活动删除成功');
								row.set({status: -1});
								row.refresh();
								row.destory();
							} else {
								tip(resp.msg || '删除出错');
							}
						})
					}
				}
			);

			table.run();
			$('#tableContainer').html(table.El);
		}

		// 进入详情页面.
		function enterDetailPage(actId) {
			var dialog = wait();
	        server.checkinActivityUsers({actId: actId}, function(resp){
	        	/*var resp = {
	        		code: 0,
					msg: 'ssdf',
					body: {
						checkins: [
							{
								id:	11111,
								lon: 22,
								lat: 22,
								address: "china",
								status: 1,	   //int	状态：-1删除，0正常，1标注
								create_time: '1997-07-01 09:00:00',
								descri: 'ss',	//String	备注
								user: {
									id: 001, //	int	用户id
									nick_name: 'zhang11', //	String	昵称（1-9位汉字或英文）
									sex: 1,	// int	性别
									birth: '1997-07-01',
									address: '地址',
									email: '邮箱',
									real_name: 'pete',
									contact_qq: 1136,
									contact_phone: 15208256358,
									head_img_url: '/ling/static/images/a.jpg',
									status: '0'	  //int	状态：-1删除，0正常												
								}
							},
							{
								id:	22222,
								lon: 22,
								lat: 22,
								address: "china",
								status: 0,	   //int	状态：-1删除，0正常，1标注
								create_time: '1997-07-01 09:00:00',
								descri: 'ss',	//String	备注
								user: {
									id: 001, //	int	用户id
									nick_name: 'zhang13', //	String	昵称（1-9位汉字或英文）
									sex: 0,	// int	性别
									birth: '1997-07-01',
									address: '地址',
									email: '邮箱',
									real_name: '皮特',
									contact_qq: 1136,
									contact_phone: 15208256358,
									head_img_url: '/ling/static/images/a.jpg',
									status: '0'	  //int	状态：-1删除，0正常												
								}
							},
							{
								id:	33333,
								lon: 22,
								lat: 22,
								address: "china",
								status: 0,	   //int	状态：-1删除，0正常，1标注
								create_time: '1998-07-01 09:00:00',
								descri: 'ss',	//String	备注
								user: {
									id: 001, //	int	用户id
									nick_name: 'zhang13', //	String	昵称（1-9位汉字或英文）
									sex: 0,	// int	性别
									birth: '1997-07-01',
									address: '地址',
									email: '邮箱',
									real_name: '张三',
									contact_qq: 1136,
									contact_phone: 15208256358,
									head_img_url: '/ling/static/images/a.jpg',
									status: '0'	  //int	状态：-1删除，0正常												
								}
							},
							{
								id:	44444,
								lon: 22,
								lat: 22,
								address: "china",
								status: 1,	   //int	状态：-1删除，0正常，1标注
								create_time: '1998-07-01 09:00:00',
								descri: 'ss',	//String	备注
								user: {
									id: 001, //	int	用户id
									nick_name: 'zhang13', //	String	昵称（1-9位汉字或英文）
									sex: 0,	// int	性别
									birth: '1997-07-01',
									address: '地址',
									email: '邮箱',
									real_name: 'zhangsan',
									contact_qq: 1136,
									contact_phone: 15208256358,
									head_img_url: '/ling/static/images/a.jpg',
									status: '0'	  //int	状态：-1删除，0正常												
								}
							}
						]	
					}
	        	};*/
	        	checkinsList = resp.body.checkins;
	        	dialog.hide();
	        	/*if (resp.code == 0) {
	        		for(var i=1; i<8; i++) {
	        			resp.body.checkins[i*4] = resp.body.checkins[0];
	        			resp.body.checkins[i*4+1] = resp.body.checkins[1];
	        			resp.body.checkins[i*4+2] = resp.body.checkins[2];
	        			resp.body.checkins[i*4+3] = resp.body.checkins[3];
	        		}*/
	        		createUserListUi(filterChekins(resp.body.checkins));
	        	//}
	        })	
		}

		// 渲染右侧详情.
		function renderDetail() {
			var htm = template.render('sign_user_infor_template', userSelected);
			$('#rsideContainer').html(htm);
			//userSelected = datas;
			
		}

		function filterChekins(checkins) {
			var result = [];
			$.each(checkins, function(i, checkin){
				result.push({
					label: checkin.user.real_name,
					value: checkin.id,
					time: checkin.create_time
				})
			});
			return result;
		}

		// 用户列表渲染.
		function createUserListUi(userlist) {
			var content = '';
			$.each(userlist, function(i, user){
				content += '<li data-id='+ user.value +'>'+ user.label + ' - ' +
							user.time +
							'</li>';
			})
			$('#userList').html(content);
		}

		/**
		 * 同步修改的数据到本地
		 * obj {Object} checkins 签到用户数据
		 */
		function setLocalData(obj) {
			if (userSelected) {
				for (var key in obj) {
					userSelected[key] = obj[key];
				}			
			}
		}

		(function() { // 详情搜索
	        $('#keyInput').autocomplete({
		        autoFocus: true,
		        delay: 80,
		        //source: ['a', 'ab', 'abc', 'd']
		        source: function(request, response) {
		          	var value = request.term,
		          		checkins = checkinsList,
		            	arrlist = [];
		            if (!value) {
		            	$.each(checkins, function(i, sign){
		        			arrlist.push({
		        				label: sign.user.real_name,
		        				value: sign.id,
		        				time: sign.create_time
		        			})
		            	})

		            } else {
		        		$.each(checkins, function(i, sign){
		        			if (new RegExp("^"+ value +"\\S*$", "gi").test(sign.user.real_name)) {
			        			arrlist.push({
			        				label: sign.user.real_name,
			        				value: sign.id,
			        				time: sign.create_time
			        			});	        				
		        			}
		        		});	
		        		$('#cancelSearch').show();	            	
		            }

		          	response(arrlist);
		        },
		        response: function(event, ui) {
		            $('.ui-autocomplete').hide();
		            createUserListUi(ui.content);
		        },
		        open: function(){
		          $('.ui-autocomplete').hide();
		        },
		        change: function() {
		        	console.log(8)
		        },
		        search: function() {
		        	console.log('search')
		        },
		        select: function() {
		        	console.log('select')
		        }
	        })
	    })();

	    var page = {
	    	init: function() {
	    		pageStatusMechine.switch('home');
	    		this.bindEvents();
	    		$('#search').click();
	    	},
	    	bindEvents: function() {
				$('#search').click(function(){
					var keyWord = $.trim($('#keyWord').val());
					var dialog = wait();
					server.getActivityBySign({keyWords: keyWord}, function(resp){
						/*var resp = {
							code: 0, //	int	响应状态（0成功，其他见参考状态码）
							msg: '2', //	String	状态信息描述
							acts: [ // jsonArray	包含以下非加粗字段
								{
									id: 11111,
									title: '第一次活动',
									publish_time: '1997-07-01 09:00:00',
									checkin_num: 98,
									t_status: 1,
									status: 3
								},
								{
									id: 222,
									title: '第2次活动',
									publish_time: '1997-07-01 09:00:00',
									checkin_num: 198,
									t_status: 2,
									status: 3
								},
								{
									id: 333,
									title: '第3次活动',
									publish_time: '1997-07-01 09:00:00',
									checkin_num: 498,
									t_status: 3,
									status: 3
								},
								{
									id: 444,
									title: '第4次活动',
									publish_time: '1997-07-01 09:00:00',
									checkin_num: 8,
									t_status: 4,
									status: 3
								},						
							]
						};*/
						dialog.hide();
						if (resp.code == 0) {
							// dialogUi
							search(resp.body.acts);
						} else {
							dialogUi.text(resp.msg);
						}
					})
				});
				
				$('#return').click(function(){
					pageStatusMechine.switch('home');
					$('#userList').html('');
					$('#rsideContainer').html('');
				});

				// 点击用户，渲染用户详情.
				$('#userList').on('click', 'li', function(e){
					var target = $(e.target),
						dataId = target.attr('data-id');
					if (checkinsList.length) {
						for (var i=0,l=checkinsList.length; i < l; i++) {
							if (checkinsList[i].id == dataId) {
								userSelected = checkinsList[i];
								renderDetail();
								break;
							}
						}				
					}
					e.stopPropagation();
				});

				// 保存备注
				$('.detailPage').on('click', '#updateDescri', function(e){
					var target = $(e.target),
						value = $.trim($('#descriText').val());
					if (!value) {
						dialogUi.text('备注说明不能为空');
					} else {
						var dialog = wait();
						server.updateCheckinDescri({checkinId: userSelected.id, descri: value}, function(resp){
							dialog.hide();
							if (resp.code == 0) {
								tip(resp.msg || '保存成功');
								setLocalData({descri: value});
							} else {
								tip(resp.msg || '保存失败');
							}
						})
					}

				});

				// 标注
				$('.detailPage').on('click', '#mark', function(e){console.log('d3')
					server.markUserCheckin({checkinId: userSelected.id}, function(resp){
						var resp = {code: 0};
						if (resp.code == 0) {
							userSelected.status = 1;
							renderDetail();
						} else {
							dialogUi.text(resp.msg || '标注失败');
						}
					})
				});

				// 取消标注
				$('.detailPage').on('click', '#unmark', function(e){ console.log('d')
					server.unmarkUserCheckin({checkinId: userSelected.id}, function(resp){
						var resp = {code: 0};
						if (resp.code == 0) {
							userSelected.status = 0;
							renderDetail();
						} else {
							dialogUi.text(resp.msg || '取消标注失败');
						}
					})
				});

				// 
				$('.detailPage').on('click', '#cancelSearch', function(e){ 
					$('#keyInput').val('');
	        		createUserListUi(filterChekins(checkinsList));
	        		$('#cancelSearch').hide();
				});

	    	}
	    };

	    page.init();

	})

})