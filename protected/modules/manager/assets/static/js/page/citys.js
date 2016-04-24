define(function(require, exports, module){
	var $ = require('$'),
		common = require('common'),
		dialogUi = require('dialogUi'),
		main = require('main'),
		server = require('server'),
		static = require('static');

	var roleConfig = static.roleConfig;

	var tip = main.tip,
		wait = main.wait,
		PagTable = common.PagTable;

	// 城市列表视图
	var cityListView = (function() {
		function renderTable() {
			return table = PagTable({
				ThList: ['编号', '城市名','添加时间'],
				columnNameList: [
					'index', 'name', 'create_time'
				],
				source: function(o, pag, table) {
					dialog = wait();
					server.citys(function(resp){
						dialog.hide();
						if (resp.code == 0) {
							if (resp.body.cities.length) {
								pag({totalPage: Math.ceil(resp.body.total_num/static.managersPerNum)});
							}
							table(resp.body.cities);
						} else {
							tip(resp.msg || '查询数据列表出错');
						}
					})
				},
				perPageNums: static.managersPerNum
			});
		}

		function _render() {
			var table = renderTable();
			$('#cityCon').html(table.El);
		}

		return { render: _render };
	})()

	function setEvents() {
		$('#create').click(function(){
			dialogUi.createCity(function(cityName){
				alert(cityName);
			})
		})
	}

	// 入口.
	var page = {
		init: function() {
			cityListView.render();
			setEvents();
		}
	}
	
	page.init();

})