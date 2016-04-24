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
// 管理员列表
server.managers = function(data, callback){
	return send('get', contextPath + 'manager/managerUser/managers', data, callback);
}
// 城市管理员列表
server.cityManagers = function(data, callback){
	return send('get', contextPath + 'manager/managerUser/cityManagers', data, callback);
}
// 城市操作员列表
server.cityOperators = function(data, callback){
	return send('get', contextPath + 'manager/managerUser/cityOperators', data, callback);
}
// 管理员登录
server.login = function(data, callback){
	return send('post', contextPath + 'manager/managerUser/login', data, callback);
}
// 城市管理员登录
server.cityLogin = function(data, callback){
	return send('post', contextPath + 'manager/managerUser/cityLogin', data, callback);
}
// 添加管理员
server.addM = function(data, callback){
	return send('post', contextPath + 'manager/managerUser/addM', data, callback);
}
// 修改指定管理员
server.updateM = function(data, callback){
	return send('post', contextPath + 'manager/managerUser/updateM', data, callback);
}
// 管理员修改自己的信息
server.updateMSelf = function(data, callback){
	return send('post', contextPath + 'manager/managerUser/updateMSelf', data, callback);
}
// 添加城市管理员
server.addCM = function(data, callback){
	return send('post', contextPath + 'manager/managerUser/addCM', data, callback);
}
// 修改城市管理员
server.updateCM = function(data, callback){
	return send('post', contextPath + 'manager/managerUser/updateCM', data, callback);
}
// 城市管理员修改自己的信息
server.updateCMSelf = function(data, callback){
	return send('post', contextPath + 'manager/managerUser/updateCMSelf', data, callback);
}
// 获取城市列表
server.citys = function(callback){
	return send('get', contextPath + 'manager/managerUser/cities', {}, callback);
}
// 退出登录
server.logout = function(callback){
	return send('post', contextPath + 'manager/managerUser/logout', {}, callback);
}
// 管理员备注列表
server.managerRemarks = function(data, callback){
	return send('get', contextPath + 'manager/managerUser/managerRemarks', data, callback);
}
// 城市管理员备注列表
server.cityManagerRemarks = function(data, callback){
	return send('get', contextPath + 'manager/managerUser/cityManagerRemarks', data, callback);
}
// 管理员添加备注
server.addRemarkM = function(data, callback){
	return send('post', contextPath + 'manager/managerUser/addRemarkM', data, callback);
}
// 管理员删除备注
server.delRemarkM = function(data, callback){
	return send('post', contextPath + 'manager/managerUser/delRemarkM', data, callback);
}
// 城市管理员添加备注
server.addRemarkCM = function(data, callback){
	return send('post', contextPath + 'manager/managerUser/addRemarkCM', data, callback);
}
// 城市管理员删除备注
server.delRemarkCM = function(data, callback){
	return send('post', contextPath + 'manager/managerUser/delRemarkCM', data, callback);
}

// 活动模块.
// 标签分类列表
server.tags = function(data, callback){
	return send('get', contextPath + 'manager/act/tags', data, callback);
}
// 活动搜索
server.acts = function(data, callback){
	return send('get', contextPath + 'manager/act/acts', data, callback);
}
// 活动详情
server.act = function(data, callback){
	return send('get', contextPath + 'manager/act/act', data, callback);
}
// 推荐的活动列表
server.recommendActs = function(data, callback){
	return send('get', contextPath + 'manager/act/recommendActs', data, callback);
}
// 活动签到列表
server.checkinsAct = function(data, callback){
	return send('get', contextPath + 'manager/act/checkins', data, callback);
}
// 活动报名列表
server.enrollsAct = function(data, callback){
	return send('get', contextPath + 'manager/act/enrolls', data, callback);
}
// 活动收藏者列表
server.lovsAct = function(data, callback){
	return send('get', contextPath + 'manager/act/lovs', data, callback);
}
// 活动分享者列表
server.sharesAct = function(data, callback){
	return send('get', contextPath + 'manager/act/shares', data, callback);
}
// 活动评论列表
server.commentsAct = function(data, callback){
	return send('get', contextPath + 'manager/act/comments', data, callback);
}
// 活动相关资讯
server.newsAct = function(data, callback){
	return send('get', contextPath + 'manager/act/news', data, callback);
}
// 活动相关达人
server.vipsAct = function(data, callback){
	return send('get', contextPath + 'manager/act/vips', data, callback);
}
// 添加标签分类
server.addTag = function(data, callback){
	return send('post', contextPath + 'manager/act/addTag', data, callback);
}
// 修改标签分类
server.updateTag = function(data, callback){
	return send('post', contextPath + 'manager/act/updateTag', data, callback);
}
// 添加活动
server.addAct = function(data, callback){
	return send('post', contextPath + 'manager/act/add', data, callback);
}
// 修改活动资料
server.updateAct = function(data, callback){
	return send('post', contextPath + 'manager/act/update', data, callback);
}
// 修改活动状态
server.updateStatusAct = function(data, callback){
	return send('post', contextPath + 'manager/act/updateStatus', data, callback);
}
// 修改推荐活动
server.updateRecommendsAct = function(data, callback){
	return send('post', contextPath + 'manager/act/updateRecommends', data, callback);
}
// 更新活动的资讯关联
server.dealNewAct = function(data, callback){
	return send('post', contextPath + 'manager/act/dealNews', data, callback);
}
// 更新活动的达人关联
server.dealVipAct = function(data, callback){
	return send('post', contextPath + 'manager/act/dealVip', data, callback);
}

// =======资讯模块
// 资讯搜索
server.news = function(data, callback){
	return send('get', contextPath + 'manager/news/news', data, callback);
}
// 资讯详情
server.newsInfo = function(data, callback){
	return send('get', contextPath + 'manager/news/newsInfo', data, callback);
}
// 资讯收藏者列表
server.lovsNews = function(data, callback){
	return send('get', contextPath + 'manager/news/lovs', data, callback);
}
// 资讯分享者列表
server.sharesNews = function(data, callback){
	return send('get', contextPath + 'manager/news/shares', data, callback);
}
// 资讯评论列表
server.commentsNews = function(data, callback){
	return send('get', contextPath + 'manager/news/comments', data, callback);
}
// 资讯相关活动
server.actsNews = function(data, callback){
	return send('get', contextPath + 'manager/news/acts', data, callback);
}
// 资讯相关达人 
server.vipsNews = function(data, callback){
	return send('get', contextPath + 'manager/news/vips', data, callback);
}
// 添加资讯 
server.addNews = function(data, callback){
	return send('post', contextPath + 'manager/news/add', data, callback);
}
// 修改资讯资料 
server.updateNews = function(data, callback){
	return send('post', contextPath + 'manager/news/update', data, callback);
}
// 修改资讯状态 
server.updateStatusNews = function(data, callback){
	return send('post', contextPath + 'manager/news/updateStatus', data, callback);
}
// 更新资讯的活动关联 
server.dealActNews = function(data, callback){
	return send('post', contextPath + 'manager/news/dealAct', data, callback);
}
// 更新资讯的达人关联 
server.dealVipNews = function(data, callback){
	return send('post', contextPath + 'manager/news/dealVip', data, callback);
}


// ========用户模块
// 用户搜索
server.users = function(data, callback){
	return send('get', contextPath + 'manager/user/users', data, callback);
}
// 用户信息
server.user = function(data, callback){
	return send('get', contextPath + 'manager/user/user', data, callback);
}
// 分享过的活动
server.shareActs = function(data, callback){
	return send('get', contextPath + 'manager/user/shareActs', data, callback);
}
// 收藏过的活动
server.lovActs = function(data, callback){
	return send('get', contextPath + 'manager/user/lovActs', data, callback);
}
// 报名过的活动
server.enrollActs = function(data, callback){
	return send('get', contextPath + 'manager/user/enrollActs', data, callback);
}
// 签到过的活动
server.checkinActs = function(data, callback){
	return send('get', contextPath + 'manager/user/checkinActs', data, callback);
}
// 发布过的动态
server.dynamics = function(data, callback){
	return send('get', contextPath + 'manager/user/dynamics', data, callback);
}
// 达人搜索
server.vips = function(data, callback){
	return send('get', contextPath + 'manager/user/vips', data, callback);
}
// 达人申请
server.vipApplys = function(data, callback){
	return send('get', contextPath + 'manager/user/vipApplys', data, callback);
}
// 设置达人
server.setVip = function(data, callback){
	return send('post', contextPath + 'manager/user/setVip', data, callback);
}
// 处理达人申请
server.dealVipApply = function(data, callback){
	return send('post', contextPath + 'manager/user/dealVipApply', data, callback);
}
// 设置达人专访
server.setVipInterview = function(data, callback){
	return send('post', contextPath + 'manager/user/setVipInterview', data, callback);
}
// 获取置顶的达人
server.topVips = function(data, callback){
	return send('get', contextPath + 'manager/user/topVips', data, callback);
}
// 设置达人置顶排序
server.setTopVips = function(data, callback){
	return send('post', contextPath + 'manager/user/setTopVips', data, callback);
}
// 达人标签列表
server.tagsUser = function(data, callback){
	return send('get', contextPath + 'manager/user/tags', data, callback);
}
// 添加用户标签
server.addTagUser = function(data, callback){
	return send('post', contextPath + 'manager/user/addTag', data, callback);
}
// 修改用户标签
server.updateTagUser = function(data, callback){
	return send('post', contextPath + 'manager/user/updateTag', data, callback);
}

// ==========系统消息及推送模块
// 获取系统消息列表
server.systemMsgs = function(data, callback){
	return send('get', contextPath + 'manager/push/systemMsgs', data, callback);
}
// 给用户发系统消息
server.sendSystemMsg = function(data, callback){
	return send('post', contextPath + 'manager/push/sendSystemMsg', data, callback);
}
// 给用户发推送消息
server.pushMsg = function(data, callback){
	return send('post', contextPath + 'manager/push/pushMsg', data, callback);
}
// 给资讯所在城市用户发资讯相关推送消息
server.pushMsgForNews = function(data, callback){
	return send('post', contextPath + 'manager/push/pushMsgForNews', data, callback);
}
// 首页轮播
server.homeAdverts = function(data, callback){
	return send('get', contextPath + 'manager/news/homeAdverts', data, callback);
}
// 修改首页轮播
server.updateHomeAdverts = function(data, callback){
	return send('post', contextPath + 'manager/news/updateHomeAdverts', data, callback);
}
// 爆料列表
server.brokenews = function(data, callback){
	return send('get', contextPath + 'manager/act/brokenews', data, callback);
}
// 意见反馈
server.feedbacks = function(data, callback){
	return send('get', contextPath + 'manager/user/feedbacks', data, callback);
}


return server;

});