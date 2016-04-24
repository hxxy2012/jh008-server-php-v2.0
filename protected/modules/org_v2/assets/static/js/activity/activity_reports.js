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
					'name',
                    /*
                    'infro',
					function(data){
						return '<div class="infor-wrap"><input type="text" id="infor" class="form-control" value="'+ data.infro +'" /></div>';
					},*/
					function(data){
						return  '<a href="javascript:;" class="button button-pre"><i class="icon iconfont"></i>查看</a>&nbsp;&nbsp;'+
                        '<a id="recovery" href="javascript:;" class="button button-pre"><i class="icon iconfont"></i>编辑</a>&nbsp;&nbsp;'+
                        '<a href="javascript:;" class="button button-pre"><i class="icon iconfont"></i>删除</a>';
					}
				],
				source:[],
				source: function(o, PagTable, option) {
					var parms = {};
					//parms.page = o.currentPage;
					//server.acts(parms, function(resp){
						var resp = {
							code: 0,
							body: {
								members: [{id:1,	name: '习近平电贺韦约尼斯当选拉脱维亚总统',time: '2012/12/12 12:00:00'},
								{id:2,	name: '习近平电贺韦约尼斯当选拉脱维亚总统',time: '2012/12/12 12:00:00'},
								{id:3,	name: '习近平电贺韦约尼斯当选拉脱维亚总统',time: '2012/12/12 12:00:00'},
								{id:4,	name: '习近平电贺韦约尼斯当选拉脱维亚总统',time: '2012/12/12 12:00:00'},
								{id:5,	name: '习近平电贺韦约尼斯当选拉脱维亚总统',time: '2012/12/12 12:00:00'}],
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
					recovery: function(e, row) {row.setData();row.refresh();
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


