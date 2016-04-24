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

// === seller ===
// 获取商家的活动
server.getActivity = function(data, callback){
	return send('get', contextPath + 'business/actInfo/businessActs', data, callback);
}

// 商家活动详情
server.getActivityDetail = function(data, callback){
	return send('get', contextPath + 'business/actInfo/businessAct', data, callback);
}

// 获取所有的标签
server.getAllTags = function(callback){
	return send('get', contextPath + 'act/tagInfo/getAllTags', {}, callback);
}

// 修改活动资料
server.updateActivity = function(data, callback){
	return send('post', contextPath + 'business/actInfo/update', data, callback);
}

// 添加活动
server.addActivity = function(data, callback){
	return send('post', contextPath + 'business/actInfo/add', data, callback);
}

// 提交活动
server.commitActivity = function(data, callback){
	return send('post', contextPath + 'business/actInfo/commit', data, callback);
}

// 发布活动
server.publishActivity = function(data, callback){
	return send('post', contextPath + 'business/actInfo/publish', data, callback);
}

// 下架活动
server.offpublishActivity = function(data, callback){
	return send('post', contextPath + 'business/actInfo/offPublish', data, callback);
}

// 删除活动
server.delActivity = function(data, callback){
	return send('post', contextPath + 'business/actInfo/del', data, callback);
}

// 查看商家个人资料
server.getSellerInfo = function(callback){
	return send('post', contextPath + 'business/businessInfo/getMyInfo', {}, callback); 
}

// 修改商家资料
server.updateSellerInfo = function(data, callback){
	return send('post', contextPath + 'business/businessInfo/upInfo', data, callback);  
}

// 获取签到的活动 
server.getActivityBySign = function(data, callback){
	return send('get', contextPath + 'business/actInfo/checkinActs', data, callback);
}

// 活动的签到信息以及用户信息
server.checkinActivityUsers = function(data, callback){
	return send('get', contextPath + 'business/actInfo/checkinUsers', data, callback);
}

// 修改标签备注
server.updateCheckinDescri = function(data, callback){
	return send('post', contextPath + 'business/actInfo/upCheckinDescri', data, callback);
}

// 标注用户签到
server.markUserCheckin = function(data, callback){
	return send('post', contextPath + 'business/actInfo/markCheckin', data, callback);
}

// 取消标注用户签到
server.unmarkUserCheckin = function(data, callback){
	return send('post', contextPath + 'business/actInfo/unMarkCheckin', data, callback);
}

// 商家登录
server.login = function(data, callback){
	return send('post', contextPath + 'business/businessInfo/login', data, callback);
}

// 商家注册
server.regist = function(data, callback){
	return send('post', contextPath + 'business/businessInfo/regist', data, callback);
}

// 退出登录
server.logout = function(callback){
	return send('get', contextPath + 'business/businessInfo/logout', {}, callback);
}

return server;

});