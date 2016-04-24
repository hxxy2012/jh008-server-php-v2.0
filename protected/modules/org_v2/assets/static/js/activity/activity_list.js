$(document).ready(function()
{
        var now = new Date(); 
        var nowStr = now.format("yyyy-MM-dd");
        $('.laydate-icon').val(nowStr);
        
        
});   
(function(){    
//生成列表
	var util = K.util,
		DialogUi = K.dialogUi;

	var BlackList = (function() {

		function renderTablePag() {
			return util.PagTable({
				el: 'tableCon',
				columnNameList: [
					'name', 
					'act_start_time',
					'sign_up_end_time',
                    'total_number',
                    /*
                    'infro',
					function(data){
						return '<div class="infor-wrap"><input type="text" id="infor" class="form-control" value="'+ data.infro +'" /></div>';
					},*/
					function(data){
                        if(data['id'] == 0)
                        return  '<a id="recovery" href="/org_v2/default/activity_post" class="button button-pre"><i class="icon iconfont"></i>编辑</a>';
	                    else
                        return  '<a id="recovery" href="/org_v2/default/activity_share" class="button button-pre"><i class="icon iconfont"></i>管理</a>';
                    },
					function(data){
						return '<i class="icon iconfont jian" style="cursor: pointer;"></i><input  name="name" type="text" maxlength="4" class="jiajiantext"><i class="icon iconfont jia" style="cursor: pointer;"></i>';
					},
					function(data){
						return  '<a href="javascript:;" class="button button-pre"><i class="icon iconfont"></i>删除</a>';
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
								members: 
                                [
                                {name: '韦约尼斯当选拉脱维亚总统',act_start_time: '2012/12/12 12:00:00',sign_up_end_time: '2999/12/12 12:00:00',total_number:'3000/9999',id:'1'},
								{name: '韦约尼斯当选拉脱维亚总统',act_start_time: '2012/12/12 12:00:00',sign_up_end_time: '2999/12/12 12:00:00',total_number:'3000/9999',id:'0'},
								{name: '韦约尼斯当选拉脱维亚总统',act_start_time: '2012/12/12 12:00:00',sign_up_end_time: '2999/12/12 12:00:00',total_number:'3000/9999',id:'0'},
								{name: '韦约尼斯当选拉脱维亚总统',act_start_time: '2012/12/12 12:00:00',sign_up_end_time: '2999/12/12 12:00:00',total_number:'3000/9999',id:'1'},
								{name: '韦约尼斯当选拉脱维亚总统',act_start_time: '2012/12/12 12:00:00',sign_up_end_time: '2999/12/12 12:00:00',total_number:'3000/9999',id:'1'}
                                ],
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