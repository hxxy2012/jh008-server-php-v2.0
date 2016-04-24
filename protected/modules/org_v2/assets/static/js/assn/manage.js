(function(){

	var util = K.util,
		DialogUi = K.dialogUi;

	//var flag;
	/**
	 * 
	 */
	var group = (function() {
		var group = ['A', 'B', 'C'];


	    function addGroup(value) {
	    	group.push(value);
	    }

	    function createGroupItemUi(groupItem) {
	    	return  '<div class="radio-wrap">' +
						'<input type="radio" name="aaa" value="'+ groupItem +'">' +
						'<label><i class="icon iconfont"></i>'+ groupItem +'</label>' +
					'</div>';
	    }

	    function _show(el, callback) {
	    	var contentString = '';
	    	$.each(group, function(i, groupItem){
	    		contentString += createGroupItemUi(groupItem);
	    	})

		    layer.open({
		    	type: 4,
		    	tips: 3,
		    	content: ['<div class="group-dia">' + 
		    				'<div class="radioSels clearfix">'+ contentString +'</div>' + 
		    				'<div class="add-wrap">'+
		    					'<input name="email" id="groupInput" class="form-control" type="text">' + 
		    					'<a href="javascript:;" id="addGroup" class="button ml10">增加组</a>' +
		    				'</div>'+
		    				'<div class="error-wrap"></div>' +
		    				'<div class="mt10"><a href="javascript:;" id="sureBtn" class="button button-blue button-m">确定</a></div>' +
		    			'</div>', el[0]],
		    	//btn: ['确定'],
		        title: false,
		        skin: 'lc-layui-tip-white',
		        /*yes: function(index, layero) {
		    		var group = layero.find('input[type="radio"]:checked').val();
		    		alert(group);

		    	},*/
		    	success: function(layero, index) {
		    		var errorWrap = layero.find('.error-wrap'),
		    			input = layero.find('#groupInput');
		    		layero.find('.radioSels').lc_radioSel();
		    		layero.find('#addGroup').click(function(){
		    			var value = $.trim(input.val());
		    			if (!value) {
		    				errorWrap.html('<p class="error-tip">组不能为空</p>');
		    			} else {
		    				errorWrap.html('');
		    				layero.find('.radioSels').append(createGroupItemUi(value));
		    				addGroup(value);
		    				input.val('');
		    			}
		    		})

		    		layero.find('#sureBtn').click(function(e){
		    			var value = layero.find('input[type="radio"]:checked').val();
		    			callback(value, function() {
		    				layer.close(index);
		    			})
		    		})
		    	}
		    })
	    }

	    return {
	    	show: _show
	    }
	})()

	/*$('.filter-item-link').hover(function(e){
		var target = $(e.target);
		target.siblings('.subfilter-menu').show();
	}, function(e) {
		var target = $(e.target);
		target.siblings('.subfilter-menu').hide();
	})*/

	var memberList = (function(){
		var SelectManager = (function(){
			var list = [];
			function _add(row) {
				var flag = false;
				for (var i = 0; i < list.length; i++) {
					if (list[i] == row) {
						flag = true;
					}
				}
				if (!flag) {
					list.push(row);
				}
			}

			function _remove(row) {
				for (var i = list.length-1; i > 0; i--) {
					if (list[i] == row) {
						list.splice(i, 1);
					}
				}
			}

			function _getIds() {
				var result = [];
				$.each(list, function(i, row) {
					result.push(row.data.id);
				})
				return result;
			}

			function _clear() {
				list = [];
			}

			return {
				add: _add,
				remove: _remove,
				getIds: _getIds,
				clear: _clear
			}
		})();

		function renderTablePag() {
			return util.PagTable({
				el: 'tableCon',
				columnNameList: [
					'index', 
					function(){
						return '<input id="select" type="checkbox" name="members"/>';
					},
					'time',
					'name','username','group','infro',
					function(data){
						return  '<a href="javascript:;" class="button" id="adjust">调整分组</a>' +
								'<a href="javascript:;" class="button ml10" id="cancel">取消分组</a>' +
								'<a href="javascript:;" class="button ml10" id="del">权限</a>';
					}
				],
				/*source: [{id:1,	name: 3,time: '2012/12/12 12:00:00',username: '张三1',group: 'A',infro: '自定义信息'},
								{id:2,	name: 2,time: '2012/12/12 12:00:00',username: '张三2',group: 'A',infro: '自定义信息'},
								{id:3,	name: 4,time: '2012/12/12 12:00:00',username: '张三3',group: 'A',infro: '自定义信息'},
								{id:4,	name: 1,time: '2012/12/12 12:00:00',username: '张三4',group: 'A',infro: '自定义信息'},
								{id:5,	name: 5,time: '2012/12/12 12:00:00',username: '张三5',group: 'A',infro: '自定义信息'},
								{id:1,	name: 3,time: '2012/12/12 12:00:00',username: '张三6',group: 'A',infro: '自定义信息'},
								{id:2,	name: 2,time: '2012/12/12 12:00:00',username: '张三7',group: 'A',infro: '自定义信息'},
								{id:3,	name: 4,time: '2012/12/12 12:00:00',username: '张三8',group: 'A',infro: '自定义信息'},
								{id:4,	name: 1,time: '2012/12/12 12:00:00',username: '张三9',group: 'A',infro: '自定义信息'},
								{id:5,	name: 5,time: '2012/12/12 12:00:00',username: '张三10',group: 'A',infro: '自定义信息'}],*/
				source: function(o, ptable, filter) { 
					var parms = {};
					//parms.page = o.currentPage;
					//parms.size = static.actListPerNum;
					//server.acts(parms, function(resp){
						var resp = {
							code: 0,
							body: {
								members: [{id:1,	name: 3,time: '2012/12/12 12:00:00',username: '张三1',group: 'A',infro: '自定义信息'},
								{id:2,	name: 2,time: '2012/12/12 12:00:00',username: '张三2',group: 'A',infro: '自定义信息'},
								{id:3,	name: 4,time: '2012/12/12 12:00:00',username: '张三3',group: 'A',infro: '自定义信息'},
								{id:4,	name: 1,time: '2012/12/12 12:00:00',username: '张三4',group: 'A',infro: '自定义信息'},
								{id:5,	name: 5,time: '2012/12/12 12:00:00',username: '张三5',group: 'A',infro: '自定义信息'},
								{id:1,	name: 3,time: '2012/12/12 12:00:00',username: '张三1',group: 'A',infro: '自定义信息'},
								{id:2,	name: 2,time: '2012/12/12 12:00:00',username: '张三2',group: 'A',infro: '自定义信息'},
								{id:3,	name: 4,time: '2012/12/12 12:00:00',username: '张三3',group: 'A',infro: '自定义信息'},
								{id:4,	name: 1,time: '2012/12/12 12:00:00',username: '张三4',group: 'A',infro: '自定义信息'},
								{id:5,	name: 5,time: '2012/12/12 12:00:00',username: '张三5',group: 'A',infro: '自定义信息'}],
								total_num: 40
							}
						};
						if (filter.key == 'name') {
							var resp = {
								code: 0,
								body: {
									members: [{id:1,	name: 3,time: '2012/12/12 12:00:00',username: '2张三1',group: 'A',infro: '自定义信息'},
									{id:2,	name: 2,time: '2012/12/12 12:00:00',username: '2张三2',group: 'A',infro: '自定义信息'},
									{id:3,	name: 4,time: '2012/12/12 12:00:00',username: '2张三3',group: 'A',infro: '自定义信息'},
									{id:4,	name: 1,time: '2012/12/12 12:00:00',username: '2张三4',group: 'A',infro: '自定义信息'},
									{id:5,	name: 5,time: '2012/12/12 12:00:00',username: '2张三5',group: 'A',infro: '自定义信息'}],
									total_num: 12
								}
							};
						}
						if (resp.code == 0) {
							if (resp.body.members.length) {
								setTimeout(function(){
									ptable({totalPage: Math.ceil(resp.body.total_num/5), datas: resp.body.members});
								}, 1000);
							}
						} else {
							alert(resp.msg || '查询数据列表出错');
						}
						SelectManager.clear();
					//})
				},
				perNums: 8,
				events: {
					"click #adjust": "adjustGroup",
					"click #cancel": "cancelGroup",
					"click #select": "select",
					"click #del": "del"
				},
				eventsHandler: {
					adjustGroup: function(e, row) {
						group.show($(e.target), function(value, next) {
							console.log(value);
							next();
							var curDialog = DialogUi.msg('正在调整分组中...');
							setTimeout(function(){
								curDialog.close();
							}, 1000);
							close();
						});
					},
					cancelGroup: function(e, row) {
						alert('cancel');
					},
					select: function(e, row) {
						if (!row.select) {
							row.select = true;
							$(e.target).attr('checked', true);
							SelectManager.add(row);
						} else {
							row.select = false;
							$(e.target).attr('checked', false);
							SelectManager.remove(row);
						}
					},
					del: function(e, row) {
						row.destory();
					}
				}
			});
		}

		function _render() {
			// ajax datas
			table = renderTablePag();
		}

		return {
			render: _render,
			getIds: SelectManager.getIds
		}
	})()

	var page = {
		initialize: function() {
			memberList.render();
			$('#pitchDelete').click(function(){
				//var ids = memberList.getIds();
				//console.log(ids);
				//flag = 3;
			})
			$('#pitchAdjust').click(function(){
				var ids = memberList.getIds();
				group.show();
			})			
		}
	}

	page.initialize();

})()