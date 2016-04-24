define(function(require, exports, module) {
	var config = {};

	config.aStatus = { // 活动状态.  修改1  下架2  发布3  删除4  提交5
		"-1": "已删除",    
		"0": "未提交",       //1 4 5
		"1": "待审核",
		"2": "审核中",
		"3": "未通过",      // 1 4
		"4": "未发布",    //3  1 4
		"5": "已发布", // 2
		"6": "已下架"       // 1 4
	};
	
	config.roleType = {
		1: '管理员',
		11: '数据管理员',
		12: '运营专员',
		101: '城市管理员',
		102: '城市操作员'
	};

	config.tStatus = {
		1: '即将开始',
		2: '进行中',
		3: '筹备中',
		4: '已结束'
	};

	// 根据活动状态做上架或者下架.
	config.statusText = function(status) {
		if (status == -1) {
			return '-';
		} else if (status == 5) {
			return '<a class="blue-color" href="javascript:;" id="downShelf">下架</a>';
		} else {
			return '<a class="red-color" href="javascript:;" id="upShelf">上架</a>';
		}
	}

	// 根据角色权限，查看或者修改

	config.watchText = function() {
		if (this.roleConfig.baseDefault() == 1) {
			return '<a class="" href="javascript:;" id="update">修改</a>';	
		} else if  (this.roleConfig.baseDefault() == 2) {
			return '<a class="" href="javascript:;" id="watch">查看</a>';	
		}
	}

	config.roleConfig = {
		baseDefault: function() {
			if (roleType == 1 || roleType == 102) return 1;
			if (roleType == 11 || roleType == 101) return 2;
		},
		changeText: function() {
			if (!roleType) return false;
			if (roleType == 1 || roleType == 102) return '修改';
			if (roleType == 11 || roleType == 101) return '查看';
		}
	}

	// config.adminListPerNum = 50;

	// 分页请求
	config.actsPerNum = 50;
	config.managersPerNum = 50;
	config.actTagsPerNum = 50;
	config.actListPerNum = 50;
	config.informationListPerNum = 50;
	config.figureUsersPerNum = 50;
	config.figureMastersPerNum = 50;
	config.cityPerNum = 50;
	config.carouselPerNum = 50;
	config.tipoffPerNum = 50; // 爆料列表
	config.remarkPerNum = 50;
	return config;
});