define(function(require, exports, module){
	var static = require('static'),
		$ = require('$'),
		server = require('server'),
		common = require('common'),
		dialogUi = require('dialogUi'),
		main = require('main');

	var wait = main.wait,
		tip = main.tip,
		PagTable = common.PagTable;
	// 备注
	module.exports = function(typeId, targetId, callback) {
		var remarkListAjax, remakeAddAjax, remarkDeleteAjax;
		if (roleType == 1 || roleType == 11 || roleType == 12) {
			remarkListAjax = server.managerRemarks;
			remakeAddAjax = server.addRemarkM; 
			remarkDeleteAjax = server.delRemarkM;
		} else if (roleType == 101 || roleType == 102) {
			remarkListAjax = server.cityManagerRemarks;
			remakeAddAjax = server.addRemarkCM; 
			remarkDeleteAjax = server.delRemarkCM;
		}

		var El = $(	'<div>' +
						'<div>' +
							'<a href="javascript:;" id="addRemark" class="ui-button ui-button-ldarkblue">添加备注</a>' +
						'</div>'+
						'<div class="mt10" id="tableCon"></div>' + 
					'</div>');
		// 列表
		function renderTable(parms) {
			return table = PagTable({
				ThList: ['编号', '备注内容', '创建时间', '修改时间', '状态', '删除'],
				columnNameList: [
					'index', 
					'remark', 'create_time', 'modify_time',
					function(data){
						return data.status == -1 ? '删除' : data.status == 0 ? '正常' : '';
					},
					function(data){
						return '<a class="" href="javascript:;" id="delete">删除</a>';
					}
				],
				source: function(o, pag, table) {
					dialog = wait();
					parms.targetId = targetId;
					parms.typeId = typeId;					
					parms.page = o.currentPage;
					parms.size = static.remarkPerNum;
					remarkListAjax(parms, function(resp){
						dialog.destroy();
						if (resp.code == 0) {
							if (resp.body.remarks.length) {
								pag({totalPage: Math.ceil(resp.body.total_num/static.remarkPerNum)});
							}
							table(resp.body.remarks);
						} else {
							tip(resp.msg || '查询数据列表出错');
						}
					})
				},
				perPageNums: static.remarkPerNum
			}, {
				'click #delete': 'deletes',
			}, {
				deletes: function(e, row) {
					var dialog = wait();
					remarkDeleteAjax({remarkId: row.data.id, typeId: typeId}, function(resp){
						dialog.destroy();
						if (resp.code == 0) {
							row.destory();
							tip('删除成功');
						} else {
							tip('删除失败');
						}
					});
				}
			});
		}

		El.find('#addRemark').click(function(){
			dialogUi.remark({
				callback: function(text, dia) {
					var dialog = wait();
					remakeAddAjax({
						targetId: targetId,
						typeId: typeId,
						remark: text
					}, function(resp){
						dialog.destroy();
						if (resp.code == 0) {
							dia.destroy();
							render();
						} else {
							tip ('备注添加失败');
						}
					})
				}
			})
		})

		function render() {
			var table = renderTable({});
			El.find('#tableCon').html(table.El);
		}

		render();
		callback(El);

	}

})