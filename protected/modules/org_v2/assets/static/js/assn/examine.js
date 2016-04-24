(function(){
	var util = K.util,
		DialogUi = K.dialogUi;

	var Refusegroup = (function() {
		var group = ['身份不符合', '信息不真实', '成员已满'];

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
		    				'<div class="btns mt10"><a href="javascript:;" class="button button-m button-orange" id="sureBtn" >确定</a></div>' +
		    			'</div>', el[0]],
		        title: false,
		        skin: 'lc-layui-tip-white',
		    	success: function(layero, index) {
		    		var errorWrap = layero.find('.error-wrap');
		    		layero.find('.radioSels').lc_radioSel();
		    		layero.find('#sureBtn').click(function(){
		    			var selectVal = layero.find('input[type=radio]:checked').val();
		    			callback(selectVal, function(){
		    				layer.close(index);
		    			});
		    		})
		    	}
		    })
	    }

	    return {
	    	show: _show
	    }
	})()


	var ExamineList = (function() {

		function renderTablePag() {
			return util.PagTable({
				el: 'tableCon',
				columnNameList: [
					'index', 
					'time',
					'username',
					'infro',
					function(data){
						return '<div class="infor-wrap"><input type="text" id="infor" class="form-control" value="'+ data.infro +'" /></div>';
					},
					function(data){
						return  '<a href="javascript:;" class="button button-lg-pre" id="through"><i class="icon iconfont"></i>通过</a>' +
								'<a href="javascript:;" class="button button-lg-pre ml10" id="refuse"><i class="icon iconfont"></i>拒绝</a>' + 
								'<a href="javascript:;" class="button button-lg-pre ml10" id="defriend"><i class="icon iconfont"></i>黑名单</a>';
					}
				],
				source: function(o, pTable) {
					var parms = {};
					//parms.page = o.currentPage;
					//parms.size = static.actListPerNum;
					//server.acts(parms, function(resp){
						var resp = {
							code: 0,
							body: {
								members: [{id:1,	name: '12345678',time: '2012/12/12 12:00:00',username: '张三1',group: 'A',infro: '自定义信息'},
								{id:2,	name: '12345678',time: '2012/12/12 12:00:00',username: '张三2',group: 'A',infro: '自定义信息'},
								{id:3,	name: '12345678',time: '2012/12/12 12:00:00',username: '张三3',group: 'A',infro: '自定义信息'},
								{id:4,	name: '12345678',time: '2012/12/12 12:00:00',username: '张三4',group: 'A',infro: '自定义信息'},
								{id:5,	name: '12345678',time: '2012/12/12 12:00:00',username: '张三5',group: 'A',infro: '自定义信息'},
								{id:1,	name: '12345678',time: '2012/12/12 12:00:00',username: '张三6',group: 'A',infro: '自定义信息'},
								{id:2,	name: '12345678',time: '2012/12/12 12:00:00',username: '张三7',group: 'A',infro: '自定义信息'},
								{id:3,	name: '12345678',time: '2012/12/12 12:00:00',username: '张三8',group: 'A',infro: '自定义信息'},
								{id:4,	name: '12345678',time: '2012/12/12 12:00:00',username: '张三9',group: 'A',infro: '自定义信息'},
								{id:5,	name: '12345678',time: '2012/12/12 12:00:00',username: '张三',group: 'A',infro: '自定义信息'},
								{id:1,	name: '12345678',time: '2012/12/12 12:00:00',username: '张三',group: 'A',infro: '自定义信息'},
								{id:2,	name: '12345678',time: '2012/12/12 12:00:00',username: '张三',group: 'A',infro: '自定义信息'},
								{id:3,	name: '12345678',time: '2012/12/12 12:00:00',username: '张三',group: 'A',infro: '自定义信息'},
								{id:4,	name: '12345678',time: '2012/12/12 12:00:00',username: '张三',group: 'A',infro: '自定义信息'},
								{id:5,	name: '12345678',time: '2012/12/12 12:00:00',username: '张三',group: 'A',infro: '自定义信息'},
								{id:1,	name: '12345678',time: '2012/12/12 12:00:00',username: '张三',group: 'A',infro: '自定义信息'},
								{id:2,	name: '12345678',time: '2012/12/12 12:00:00',username: '张三',group: 'A',infro: '自定义信息'},
								{id:3,	name: '12345678',time: '2012/12/12 12:00:00',username: '张三',group: 'A',infro: '自定义信息'},
								{id:4,	name: '12345678',time: '2012/12/12 12:00:00',username: '张三',group: 'A',infro: '自定义信息'},
								{id:5,	name: '12345678',time: '2012/12/12 12:00:00',username: '张三',group: 'A',infro: '自定义信息'}],
								total_num: 20
							}
						};
						if (resp.code == 0) {
							if (resp.body.members.length) {
								setTimeout(function(){
									pTable({totalPage: Math.ceil(resp.body.total_num/5), datas: resp.body.members});
								}, 1000);
							}
						} else {
							alert(resp.msg || '查询数据列表出错');
						}
					//})
				},
				perNums: 20,
				events: {
					"focus #infor": "inforFocus",
					"blur #infor": "inforBlur",
					"click #through": "through",
					"click #refuse": "refuse",
					"click #defriend": "defriend"
				},
	 			eventsHandler: {
					inforFocus: function(e, row) {
						var target = $(e.target);
						target.addClass('form-control-long');
					},
					inforBlur: function(e, row) {
						var target = $(e.target);
						target.removeClass('form-control-long');
					},
					through: function(e, row) {
						var curDialog = DialogUi.msg('正在执行通过操作中...');
						setTimeout(function(){
							curDialog.close();
						}, 1000);
					},
					refuse: function(e, row) {
						Refusegroup.show($(e.target), function(value, close){
							var curDialog = DialogUi.msg('正在拒绝操作中...');
							setTimeout(function(){
								curDialog.close();
							}, 1000);
							close();
						});
						//layer.tips('Hi，我是tips', e.target);
					},
					defriend: function(e, row) {
						var curDialog = DialogUi.msg('正在拉入黑名单...');
						setTimeout(function(){
							curDialog.close();
							row.destory();
						}, 1000);
					}
				}
			});
		}

		function _render() {
			var table = renderTablePag();
		}

		return {
			render: _render
		}
	})()

	var page = {
		initialize: function(){
			ExamineList.render();
		}
	}

	page.initialize();

})()