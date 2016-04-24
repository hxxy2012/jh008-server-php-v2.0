(function($){
	var util = K.util;

	var payment = (function() {

		function renderTablePag() {
			return util.PagTable({
				ThList: ['序号', '日期', '活动名称', '费用类型', '金额（元）', '付款人<i>（报名人姓名）</i>','电话','状态'],
				columnNameList: [
					'index', 
					'time',
					'eventName',
					'chargeType',
					'money',
					'payer',
					'phoneNum',
					'status'
				],
				source: function(o, pTable) {
					var parms = {};
					//parms.page = o.currentPage;
					//parms.size = static.actListPerNum;
					//server.acts(parms, function(resp){
						var resp = {
							code: 0,
							body: {
								members: [
									{id:1,time: '2012/12/12 12:00:00',eventName: '跑步',chargeType:'报名费',money:'1000.00',payer: '张三',phoneNum: '13211112222',status: '成功'},
									{id:2,time: '2012/12/12 12:00:00',eventName: '游泳',chargeType:'报名费',money:'1000.00',payer: '张三',phoneNum: '13211112222',status: '成功'},
									{id:3,time: '2012/12/12 12:00:00',eventName: '健身',chargeType:'报名费',money:'1000.00',payer: '张三',phoneNum: '13211112222',status: '成功'},
									{id:4,time: '2012/12/12 12:00:00',eventName: '吃火锅',chargeType:'报名费',money:'1000.00',payer: '张三',phoneNum: '13211112222',status: '成功'},
									{id:5,time: '2012/12/12 12:00:00',eventName: '遛弯儿',chargeType:'报名费',money:'1000.00',payer: '张三',phoneNum: '13211112222',status: '成功'}
								],
								total_num: 25
							}
						};
						if (resp.code == 0) {
							if (resp.body.members.length) {
								pTable({totalPage: Math.ceil(resp.body.total_num/5), datas: resp.body.members});
							}
						} else {
							alert(resp.msg || '查询数据列表出错');
						}
					//})
				},
				perPageNums: 5
			});
		}

		function _render() {
			var table = renderTablePag();
			$('.payment-table').html(table.El);
		}

		return {
			render: _render
		}
	})()

	var page = {
		initialize: function(){
			payment.render();
		}
	}
	page.initialize();

})(jQuery)