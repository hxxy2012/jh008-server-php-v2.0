(function(){
	var util = K.util,
		DialogUi = K.dialogUi;

	var BlackList = (function() {

		function renderTablePag() {
			return util.PagTable({
				el: 'tableCon',
				columnNameList: [
					'index', 
					'time',
					'name','infro',
					function(data){
						return '<div class="infor-wrap"><input type="text" id="infor" class="form-control" value="'+ data.infro +'" /></div>';
					},
					function(data){
						return  '<a id="recovery" href="javascript:;" class="button button-lg-pre"><i class="icon iconfont"></i>恢复</a>';
					}
				],
				source: function(o, PagTable, option) {
					var parms = {};
					//parms.page = o.currentPage;
					//parms.size = static.actListPerNum;
					//server.acts(parms, function(resp){
						var resp = {
							code: 0,
							body: {
								members: [{id:1,	name: '12345678',time: '2012/12/12 12:00:00',username: '张三',group: 'A',infro: '自定义信息'},
								{id:2,	name: '12345678',time: '2012/12/12 12:00:00',username: '张三',group: 'A',infro: '自定义信息'},
								{id:3,	name: '12345678',time: '2012/12/12 12:00:00',username: '张三',group: 'A',infro: '自定义信息'},
								{id:4,	name: '12345678',time: '2012/12/12 12:00:00',username: '张三',group: 'A',infro: '自定义信息'},
								{id:5,	name: '12345678',time: '2012/12/12 12:00:00',username: '张三',group: 'A',infro: '自定义信息'}],
								total_num: 20
							}
						};
						if (resp.code == 0) {
							if (resp.body.members.length) {
								PagTable({totalPage: Math.ceil(resp.body.total_num/5), datas: resp.body.members});
							}
						} else {
							alert(resp.msg || '查询数据列表出错');
						}
					//})
				},
				perNums: 5,
				events: {
					"click #recovery": "recovery",
					"focus #infor": "inforFocus",
					"blur #infor": "inforBlur"
				},
				eventsHandler: {
					recovery: function(e, row) {
						var curDialog = DialogUi.msg('正在恢复中...');
						setTimeout(function(){
							curDialog.close();
						}, 1000);
					},
					inforFocus: function(e, row) {
						var target = $(e.target);
						target.addClass('form-control-long');
					},
					inforBlur: function(e, row) {
						var target = $(e.target);
						target.removeClass('form-control-long');
					}
				}
			})
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
			BlackList.render();
		}
	}

	page.initialize();

})()