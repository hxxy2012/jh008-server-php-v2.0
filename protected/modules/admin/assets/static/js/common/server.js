define(function(require, exports, module) {
var $ = require('$');
var contextPath = '/'	
//var Dialog = ika.dialogUi;	

var send = function (type, api, parameters, success,  async) {
	var request = $.ajax({
		url: api + "?r=" + Math.random(),
		data: parameters,
		type: type,
		dataType: 'json',
		async: true,
		cache: false,
		headers: {"Cache-Control": "no-cache", "Accept": "application/json"},
		timeout: 300000,
		success: function (data, textStatus, jqXHR) {
            success(data);
        },
		error: function (jqXHR, textStatus, errorThrown) {
			if(jqXHR.status == 401){
				location.href = contextPath;
			}else{
                if (!errorThrown) {
                    return false;
                }

				var errors = {
						101: "网络不稳定或不畅通，请检查网络设置",
                        403: "服务器禁止此操作！",
						500: "服务器遭遇异常阻止了当前请求的执行<br/><br/><br/>"
				};

				var msg = null;
				switch (textStatus) {
				case "timeout":
					msg = "网络连接超时，请检查网络是否畅通！";
					break;
				case "error":
					if (errors[jqXHR.status]) {
                        var data = null;
                        try {
                            data = jQuery.parseJSON(jqXHR.responseText);
                        } catch (e) {
                        }
                        if (data && data.message) {
                            msg = data.message;
                        } else {
                            msg = errors[jqXHR.status];
                        }
					} else {
						msg = "服务器响应异常<br/><br/>" + (jqXHR.status == 0 ? "" : jqXHR.status) + "&nbsp;" + errorThrown;
					}
					break;
				case "abort":
					msg = null;//"数据连接已被取消！";
					break;
				case "parsererror":
					msg = "数据解析错误！";
					break;
				default:
					msg = "出现错误:" + textStatus + "！";
				}
				if (errorThrown.code != null && errorThrown.message != null && !errors[errorThrown.code]) {
					msg += "</br>[code:" + errorThrown.code + "][message:" + errorThrown.message + "]" + (null == errorThrown.stack ? "" : errorThrown.stack);
				}
				if(msg == null) {
					msg = '';
				}
				success({code: jqXHR.status, msg: msg});
			}
		}
	});
}	

var server = {};

// ==== 管理员
// 登录
server.adminLogin = function(data, callback){
	return send('post', contextPath + 'admin/adminInfo/login', data, callback);
}

// 查看自己的资料
server.getMyInfo = function(callback){
	return send('get', contextPath + 'admin/adminInfo/getMyInfo', {}, callback);
}

// 修改管理员资料
server.updateInfo = function(data, callback){
	return send('post', contextPath + 'admin/adminInfo/upInfo', {}, callback);
}

// 获取管理员列表
server.getAdminList = function(callback){
	return send('get', contextPath + 'admin/adminInfo/getUsers', {}, callback);
}

// 获取管理员列表（回收站）
server.getDeladmins = function(callback){
	return send('get', contextPath + 'admin/adminInfo/getDelUsers', {}, callback);
}

// 查看某个管理员的资料
server.getAdminInfo = function(data, callback){
	return send('get', contextPath + 'admin/adminInfo/getUserInfo', data, callback);
}

// 添加管理员
server.addAdmin = function(data, callback){
	return send('post', contextPath + 'admin/adminInfo/addUser', data, callback);
}

// 修改某个管理员的资料
server.updateAdmin = function(data, callback){
	return send('post', contextPath + 'admin/adminInfo/updateUser', data, callback);
}

// 删除某个管理员
server.delAdmin = function(data, callback){
	return send('post', contextPath + 'admin/adminInfo/delUser', data, callback);
}

// 退出登录
server.logout = function(data, callback){
	return send('get', contextPath + 'admin/adminInfo/logout', data, callback);
}

// 获取商家列表 
server.getBusinesses = function(data, callback){
	return send('get', contextPath + 'admin/businessInfo/getBusinesses', data, callback);
}
// 获取某个商家的资料
server.getInfo = function(data, callback){
	return send('get', contextPath + 'admin/businessInfo/getInfo', data, callback);
}
// 获取商家列表 回收站
server.getDelBusinesses = function(data, callback){
	return send('get', contextPath + 'admin/businessInfo/getDelBusinesses', data, callback);
}
// 删除商家
server.deleteBusiness = function(data, callback){
	return send('post', contextPath + 'admin/businessInfo/del', data, callback);
}
// 修改商家资料
server.updateBusiness = function(data, callback){
	return send('post', contextPath + 'admin/businessInfo/update', data, callback);
}
// 添加商家
server.addBusiness = function(data, callback){
	return send('post', contextPath + 'admin/businessInfo/add', data, callback);
}

// 获取用户列表
server.getUsers = function(data, callback){
	return send('get', contextPath + 'admin/userInfo/getUsers', data, callback);
}
// 获取用户列表（回收站）
server.getDelUsers = function(data, callback){
	return send('get', contextPath + 'admin/userInfo/getDelUsers', data, callback);
}
// 删除用户
server.delUser = function(data, callback){
	return send('get', contextPath + 'admin/userInfo/delUser', data, callback);
}
// 获取某个用户的资料
server.getUserInfo = function(data, callback){
	return send('get', contextPath + 'admin/userInfo/getUserInfo', data, callback);
}


// 获取活动列表
server.getActs = function(data, callback){
	return send('get', contextPath + 'admin/actInfo/getActs', data, callback);
}
// 获取活动列表（回收站）
server.getDelActs = function(data, callback){
	return send('get', contextPath + 'admin/actInfo/getDelActs', data, callback);
}
// 获取活动详情
server.getActInfo = function(data, callback){
	return send('get', contextPath + 'admin/actInfo/getActInfo', data, callback);
}
// 删除活动
server.delAct = function(data, callback){
	return send('post', contextPath + 'admin/actInfo/delAct', data, callback);
}
// 修改活动状态
server.updateActStatus = function(data, callback){
	return send('post', contextPath + 'admin/actInfo/updateStatus', data, callback);
}
// 修改活动资料
server.updateAct = function(data, callback){
	return send('post', contextPath + 'admin/actInfo/updateAct', data, callback);
}
// 添加活动
server.addAct = function(data, callback){
	return send('post', contextPath + 'admin/actInfo/addAct', data, callback);
}

// 获取标签列表
server.getTags = function(data, callback){
	return send('get', contextPath + 'admin/tagInfo/getTags', data, callback);
}
// 获取标签列表（回收站）
server.getDelTags = function(data, callback){
	return send('get', contextPath + 'admin/tagInfo/getDelTags', data, callback);
}
// 删除标签
server.delTag = function(data, callback){
	return send('post', contextPath + 'admin/tagInfo/delTag', data, callback);
}
// 修改标签
server.updateTag = function(data, callback){
	return send('post', contextPath + 'admin/tagInfo/updateTag', data, callback);
}
// 添加标签
server.addTag = function(data, callback){
	return send('post', contextPath + 'admin/tagInfo/addTag', data, callback);
}

// 获取用户统计信息
server.countInfo = function(callback){
	return send('get', contextPath + 'admin/userInfo/countInfo', {}, callback);
}
// 获取时间段内每天注册用户统计数
server.getRegistCount = function(data, callback){
	return send('get', contextPath + 'admin/userInfo/getRegistCount', data, callback);
}


// == 签到
// 获取签到的活动
server.getCheckinActs = function(data, callback){
	return send('get', contextPath + 'admin/actInfo/getCheckinActs', data, callback);
}
// 活动的签到信息及用户信息
server.getCheckinUsers = function(data, callback){
	return send('get', contextPath + 'admin/actInfo/getCheckinUsers', data, callback);
}

// == 消息类型
// 获取消息类型列表
server.getMsgTypes = function(data, callback){
	return send('get', contextPath + 'admin/msgInfo/getMsgTypes', data, callback);
}
// 获取消息类型列表（回收站）
server.getDelMsgTypes = function(data, callback){
	return send('post', contextPath + 'admin/msgInfo/getDelMsgTypes', data, callback);
}
// 获取消息类型
server.getMsgType = function(data, callback){
	return send('get', contextPath + 'admin/msgInfo/getMsgType', data, callback);
}
// 删除消息类型
server.delMsgType = function(data, callback){
	return send('post', contextPath + 'admin/msgInfo/delMsgType', data, callback);
}
// 修改消息类型
server.updateMsgType = function(data, callback){
	return send('post', contextPath + 'admin/msgInfo/updateMsgType', data, callback);
}
// 创建消息类型
server.addMsgType = function(data, callback){
	return send('post', contextPath + 'admin/msgInfo/addMsgType', data, callback);
}

// == 消息
// 获取消息列表
server.getMsgs = function(data, callback){
	return send('get', contextPath + 'admin/msgInfo/getMsgs', data, callback);
}
// 获取消息列表（回收站）
server.getDelMsgs = function(data, callback){
	return send('get', contextPath + 'admin/msgInfo/getDelMsgs', data, callback);
}
// 删除消息
server.delMsg = function(data, callback){
	return send('post', contextPath + 'admin/msgInfo/delMsg', data, callback);
}
// 获取某个用户的消息列表
server.getUserMsgs = function(data, callback){
	return send('get', contextPath + 'admin/msgInfo/getUserMsgs', data, callback);
}
// 获取某个消息的接收用户列表
server.getMsgRevUsers = function(data, callback){
	return send('get', contextPath + 'admin/msgInfo/getMsgRevUsers', data, callback);
}
// 获取消息信息
server.getMsgInfo = function(data, callback){
	return send('get', contextPath + 'admin/msgInfo/getMsgInfo', data, callback);
}
// 修改消息
server.updateMsg = function(data, callback){
	return send('post', contextPath + 'admin/msgInfo/updateMsg', data, callback);
}
// 创建消息
server.addMsg = function(data, callback){
	return send('post', contextPath + 'admin/msgInfo/addMsg', data, callback);
}


// == 版本
// 获取app版本列表
server.getApps = function(data, callback){
	return send('get', contextPath + 'admin/appInfo/getApps', data, callback);
}
// 获取app版本列表（回收站）
server.getDelApps = function(data, callback){
	return send('get', contextPath + 'admin/appInfo/getDelApps', data, callback);
}
// 删除app版本
server.delApp = function(data, callback){
	return send('post', contextPath + 'admin/appInfo/delApp', data, callback);
}
// 提交新app版本
server.addApp = function(data, callback){
	return send('post', contextPath + 'admin/appInfo/addApp', data, callback);
}
// 修改app版本
server.updateApp = function(data, callback){
	return send('post', contextPath + 'admin/appInfo/updateApp', data, callback);
}

// push类型
// 创建push类型
server.addPushType = function(data, callback){
	return send('post', contextPath + 'admin/msgInfo/addPushType', data, callback);
}
// 修改push类型
server.updatePushType = function(data, callback){
	return send('post', contextPath + 'admin/msgInfo/updatePushType', data, callback);
}
// 删除push类型
server.delPushType = function(data, callback){
	return send('post', contextPath + 'admin/msgInfo/delPushType', data, callback);
}
// 获取push类型
server.getPushType = function(data, callback){
	return send('get', contextPath + 'admin/msgInfo/getPushType', data, callback);
}
// 获取push类型列表
server.getPushTypes = function(callback){
	return send('get', contextPath + 'admin/msgInfo/getPushTypes', {}, callback);
}

// push
// 添加push
server.addPush = function(data, callback){
	return send('post', contextPath + 'admin/msgInfo/addPush', data, callback);
}
// 获取push列表
server.getPushs = function(data, callback){
	return send('get', contextPath + 'admin/msgInfo/getPushs', data, callback);
}

// 推荐信息
// 获取推荐信息列表
server.getRecommends = function(data, callback){
	return send('get', contextPath + 'admin/actInfo/getRecommends', data, callback);
}
// 获取推荐信息列表（回收站）
server.getDelRecommends = function(data, callback){
	return send('get', contextPath + 'admin/actInfo/getDelRecommends', data, callback);
}
// 获取某个推荐信息
server.getRecommend = function(data, callback){
	return send('get', contextPath + 'admin/actInfo/getRecommend', data, callback);
}
// 删除某个推荐信息
server.delRecommend = function(data, callback){
	return send('post', contextPath	+ 'admin/actInfo/delRecommend', data, callback);
}

// 获取管理员操作日志
server.getLogs = function(data, callback){
	return send('get', contextPath	+ 'admin/adminInfo/getLogs', data, callback);
}
// 获取商家操作日志
server.getBusiLogs = function(data, callback){
	return send('get', contextPath	+ 'admin/adminInfo/getBusiLogs', data, callback);
}


// 抽奖
// 添加活动抽奖方案
server.addPrize = function(data, callback){
	return send('post', contextPath	+ 'admin/prizeInfo/addPrize', data, callback);
}
// 获取活动的抽奖方案列表
server.getPrizes = function(data, callback){
	return send('get', contextPath	+ 'admin/prizeInfo/getPrizes', data, callback);
}
// 添加活动抽奖方案的奖项
server.addAward = function(data, callback){
	return send('post', contextPath	+ 'admin/prizeInfo/addAward', data, callback);
}
// 获取活动抽奖方案的奖项列表
server.getAwards = function(data, callback){
	return send('get', contextPath	+ 'admin/prizeInfo/getAwards', data, callback);
}
// 产生一个备选中奖者
server.makeAwardUser = function(data, callback){
	return send('get', contextPath	+ 'admin/prizeInfo/makeAwardUser', data, callback);
}
// 保存一个中奖者
server.saveAwardUser = function(data, callback){
	return send('post', contextPath	+ 'admin/prizeInfo/saveAwardUser', data, callback);
}
// 活动奖项的中奖者列表
server.getAwardUsers = function(data, callback){
	return send('get', contextPath	+ 'admin/prizeInfo/getAwardUsers', data, callback);
}


return server;

});