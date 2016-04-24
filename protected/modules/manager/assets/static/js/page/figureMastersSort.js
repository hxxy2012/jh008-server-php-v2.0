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

	// 达人用户列表视图
	var figureMastersView = (function() {
		var table;
		function renderTable(parms) {
			return table = PagTable({
				ThList: ['编号','头像', '用户昵称', '姓名', '性别', '电话',
				 '置顶'],
				columnNameList: [
					'index',
					function(data) {
						return '<a id="detailPersonal" href="javascript:;"><img width="80" height="80" src="'+ data.head_img_url +'" /></a>';
					},
					'nick_name','real_name',
					function(data) {
						return data.sex == 1 ? '男' : data.sex == 2 ? '女' : '';
					},'contact_phone',
					function(){
						return '<a class="" href="javascript:;" id="resetTop">置顶</a>';
					}
				],
				source: function(o, pag, table) {
					dialog = wait();
					parms.page = o.currentPage;
					parms.size = static.actsPerNum;
					server.topVips(parms, function(resp){
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
				'click #resetTop': 'resetTop'
			}, {
				resetTop: function(e, row) {
					//console.log(row);
					if (row.index) {
						row.setLoc();
						var vipIds = getVipIds();
						var tagId = $('#masgerTagSelect #spanText').attr('value');
						var dialog = wait();
						server.setTopVips({
							cityId: page.cityId,
							tagId: tagId,
							vipIds: vipIds
						}, function(resp){
							dialog.destroy();
							if (resp.code == 0) {
								tip ('置顶成功');
							} else {
								tip ('置顶失败');
								$('#searchBtn').trigger('click');
							}
						})					
					}
				}
			});
		}	

		function _render(parms) {
			table = renderTable(parms);
			pageManager.render('listPanel', table.El);
		}

		function getVipIds() {
			var datas = table.table.getModel(), result = [];
			$.each(datas, function(i, data){
				result.push(data.id);
			})
			return result;			
		}

		return { 
			render: _render,
			getVipIds: getVipIds
		}
	})()

	var addMasterView = (function() {
		var hotDialog, hotTable;
			El = $(	'<div class="p10">' +
						'<div>' +
							'<input type="text" class="ui-input" id="searchHotInput" />' + 
							'<a href="javascript:;" id="searchHotBtn" class="ui-button ui-button-ldarkblue ml20">搜索</a>' +
							'<div id="loadWrap" class="mt20 tc"></div>' +
						'</div>'+
						'<div class="mt10" id="tablecontainer"></div>' + 
					'</div>');
			El.find('#searchHotBtn').click(function(){
				var keyWords = El.find('#searchHotInput').val();
				render({cityId: page.cityId, keyWords: keyWords});
			})

		function showLoad() {
			El.find('#loadWrap').html('<a href="javascript:;" class="load-wrap"></a>');
		}

		function closeLoad() {
			El.find('#loadWrap').html('');
		}

		function renderTable(parms) {
			return table = PagTable({
				ThList: ['编号', '达人姓名', '置顶'],
				columnNameList: [
					'index', 
					'real_name',
					function(data){
						return '<a id="setHot" href="javascript:;" >置顶</a>';
					}
				],
				source: function(o, pag, table) {
					showLoad();
					parms.page = o.currentPage;
					parms.size = 20;
					server.vips(parms, function(resp){
						/*resp = {
							code: 0,
							body: {
								users: [{title: 3, id: 76},{title: 4, id: 77},{title: 5, id: 78}],
								total_num: 1
							}
						};*/
						closeLoad();
						if (resp.code == 0) {
							if (resp.body.users.length) {
								pag({totalPage: Math.ceil(resp.body.total_num/20)});
							}
							table(resp.body.users);
						} else {
							tip(resp.msg || '查询数据列表出错');
						}
					})
				},
				perPageNums: 20
			}, {
				'click #setHot': 'setHot'
			}, {
				setHot: function(e, row) {
					//actHotListView.add(row.data);
					//sureHotBtnEl.show();
					var vipIds = figureMastersView.getVipIds();
					if (~$.inArray(row.data.id, vipIds)) {
						tip('此达人已经在排序列表中');
					} else {
						vipIds.unshift(row.data.id);
						var tagId = $('#masgerTagSelect #spanText').attr('value');
						var dialog = wait();
						server.setTopVips({
							cityId: page.cityId,
							tagId: tagId,
							vipIds: vipIds
						}, function(resp){
							dialog.destroy();
							hotDialog.hide();
							$('#searchBtn').trigger('click');
							if (resp.code == 0) {
								row.destory();
								tip ('置顶成功');
							} else {
								tip ('置顶失败');
							}
						})						
					}

				}
			})
		}

		function render(parms) {
			hotTable = renderTable(parms);
			El.find('#tablecontainer').html(hotTable.El);			
		}

		function init() {
			if (!hotDialog) {
				//server.acts({cityId: page.cityId, page: })
				hotDialog = new Dialog({
					content: El[0]
					//closeTpl: false
					//height: 180,
					//width: 300
				});
			}
			render({cityId: page.cityId});
			hotDialog.show();
		}

		return {
			init: init
		}
	})()

	function setEvents() {
		$('#addMaster').click(function(){
			addMasterView.init();
		})

		$('#searchBtn').click(function(){
			var parms={};
			tagId = $('#masgerTagSelect #spanText').attr('value');
			if (tagId) {
				parms.tagId = tagId;
			}
			parms.cityId = page.cityId;	
			figureMastersView.render(parms);
		})
	}

	// 入口
	var page = {
		init: function() {
			page.cityId = main.getCityId();
			pageManager.add({name: 'listPanel', el: $('#listCon'), parent: $('#mainPanel')});
			pageManager.hide();
			setEvents();
			main.initUserTagsSelect(function(){
				$('#mastersTagsCon').find('li:eq(0)').find('a').trigger('click');
				$('#searchBtn').trigger('click');
			});

		}
	}

	page.init();

})