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
	config.tStatus = {
		"1": "即将开始",
		"2": "进行中",
		"3": "筹备中",
		"4": "已结束",
	}

	return config;
});