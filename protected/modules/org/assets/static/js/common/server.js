define(function(require, exports, module) {
var $ = require('$');
var contextPath = '/';	
//var Dialog = ika.dialogUi;	

var send = function (type, api, parameters, success,  async) {
    //alert(api);
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
		  
          //alert(jqXHR+errorThrown+textStatus);
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
                //alert(textStatus);
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


// 管理员登录
server.login = function(data, callback){
    //alert(data);
	return send('post', contextPath + 'user/userInfo/login', data, callback);
}

//登出
server.loginout = function(data, callback){
    //alert(data);
	return send('post', contextPath + 'org/user/logout', data, callback);
}

//个人信息
server.getinfo = function(data, callback){
	return send('get', contextPath + 'org/user/info', data, callback);
}


//修改密码
server.modifyPassword = function(data, callback){
    //alert(data);
	return send('post', contextPath + 'org/user/modifyPassword', data, callback);
}


//用户图片上传
server.imgUp = function(data, callback){
    //alert(data);
	return send('post', contextPath + 'user/userInfo/imgUp', data, callback);
}
//社团资料查看
server.orgInfo = function(data, callback){
    //alert(data);
	return send('get', contextPath + 'org/orgInfo/info', data, callback);
}

//社团资料修改
server.modifyInfo = function(data, callback){
    //alert(data);
	return send('Post', contextPath + 'org/orgInfo/modifyInfo', data, callback);
}


//首页当前活动
server.currentActs = function(data, callback){
    //alert(data);
	return send('get', contextPath + 'org/act/currentActs', data, callback);
}

//首页过往活动
server.pastActs = function(data, callback){
    //alert(data);
	return send('get', contextPath + 'org/act/pastActs', data, callback);
}

//创建活动 第一步
server.create = function(data, callback){
    //alert(data);
	return send('Post', contextPath + 'org/act/create', data, callback);
}

//修改活动 第2 和 3步 又哪些参数就传哪些
server.modify = function(data, callback){
    //alert(data);
	return send('Post', contextPath + 'org/act/modify', data, callback);
}

//活动详情
server.detail = function(data, callback){
    //alert(data);
	return send('Post', contextPath + 'org/act/detail', data, callback);
}

//活动报名列表
server.enrolls = function(data, callback){
    //alert(data);
	return send('get', contextPath + 'org/actMore/enrolls', data, callback);
}


//活动报名审核
server.verify = function(data, callback){
    //alert(data);
	return send('Post', contextPath + 'org/actMore/verify', data, callback);
}

//活动手动添加报名
server.manualEnroll = function(data, callback){
    //alert(data);
	return send('Post', contextPath + 'org/actMore/manualEnroll', data, callback);
}

//活动签到码列表
server.checkinCodes = function(data, callback){
    //alert(data);
	return send('Get', contextPath + 'org/actMore/checkinCodes', data, callback);
}

//活动添加签到码
server.addCode = function(data, callback){
    //alert(data);
	return send('Post', contextPath + 'org/actMore/addCode', data, callback);
}

//活动修改签到码
server.modifyCode = function(data, callback){
    //alert(data);
	return send('Post', contextPath + 'org/actMore/modifyCode', data, callback);
}

//活动删除签到码
server.delCode = function(data, callback){
    //alert(data);
	return send('Post', contextPath + 'org/actMore/delCode', data, callback);
}

//发送消息
server.sendMsg = function(data, callback){
    //alert(data);
	return send('Post', contextPath + 'org/actMore/sendMsg', data, callback);
}

//删除活动
server.del = function(data, callback){
    //alert(data);
	return send('Post', contextPath + 'org/act/del', data, callback);
}


//活动主办方相册的图片
server.orgAlbumImgs = function(data, callback){
    //alert(data);
	return send('Get', contextPath + 'org/actMore/orgAlbumImgs', data, callback);
}

//活动相册添加图片
server.addAlbumImg = function(data, callback){
    //alert(data);
	return send('Post', contextPath + 'org/actMore/addAlbumImg', data, callback);
}

//活动相册删除图片
server.delAlbumImg = function(data, callback){
    //alert(data);
	return send('Post', contextPath + 'org/actMore/delAlbumImg', data, callback);
}

//活动成员列表
server.members = function(data, callback){
    //alert(data);
	return send('Get', contextPath + 'org/actMore/members', data, callback);
}


//活动更新成员分组
server.modifyGroup = function(data, callback){
    //alert(data);
	return send('Post', contextPath + 'org/actMore/modifyGroup', data, callback);
}

//搜索用户
server.searchUser = function(data, callback){
    //alert(data);
	return send('Get', contextPath + 'org/user/searchUser', data, callback);
}

//添加社团管理员
server.addManager = function(data, callback){
    //alert(data);
	return send('Post', contextPath + 'org/orgInfo/addManager', data, callback);
}

//删除社团管理员
server.delManager = function(data, callback){
    //alert(data);
	return send('Post', contextPath + 'org/orgInfo/delManager', data, callback);
}

//社团的关注用户
server.lovs = function(data, callback){
    //alert(data);
	return send('Get', contextPath + 'org/orgInfo/lovs', data, callback);
}


//社团的管理员
server.orgmanagers = function(data, callback){
    //alert(data);
	return send('Get', contextPath + 'org/orgInfo/managers', data, callback);
}

/*-------------------------------------------------------------*/
//社团数据分析
server.OrgDA = function(data, callback){
    //alert(data);
	return send('Post', contextPath + 'org/orgInfo/OrgDA', data, callback);
}
//活动数据分析
server.ActDA = function(data, callback){
    //alert(data);
	return send('Post', contextPath + 'org/act/ActDA', data, callback);
}

/*-------------------------------------------------------------*/
//状态明细
server.bills = function(data, callback){
    //alert(data);
	return send('Get', contextPath + 'org/pay/bills', data, callback);
}
//申请提现情况
server.withdrawCashAllow = function(data, callback){
    //alert(data);
	return send('Get', contextPath + 'org/pay/withdrawCashAllow', data, callback);
}
//申请提现
server.withdrawCashApply = function(data, callback){
    //alert(data);
	return send('Post', contextPath + 'org/pay/withdrawCashApply', data, callback);
}


//充值支付
server.rechargePayUrl = function(data, callback){
    //alert(data);
	return send('Post', contextPath + 'org/pay/rechargePayUrl', data, callback);
}














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




//好友列表  act/friend/list
server.friend_list = function(data, callback){
    return send('get', contextPath + 'act/friend/list', data, callback);
}




return server;

});