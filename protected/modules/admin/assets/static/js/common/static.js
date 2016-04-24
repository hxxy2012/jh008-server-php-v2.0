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
		"4": "已结束"
	};

	config.adminListPerNum = 50;
	config.businessListPerNum = 50;
	config.tagsListPerNum = 50;

	// 分页请求
	config.actsPerNum = 50;
	config.usersPerNum = 50;
	config.businessesPerNum = 50;
	config.checkinsPerNum = 50;
	config.checinsUsersNum = 1024;
	config.appsPerNum = 50;
	config.msgsTypeNum = 50;
	config.msgsPerNum = 50;
	config.msgRevUserNum=50; // 获取某个消息的接收用户列表
	config.userMsgsPerNum = 50; // 获取某个用户的消息列表
	config.pushsPerNum = 50;  
	config.recommendsPerNum = 50; 
	config.logsPerNum = 50; // 每页日志数量
	return config;
});