define(function(require, exports, module) {
	var $ = require('$');
	var server = require('server'),
		//common = require('common'),
        roleType=0,
		dialogUi = require('dialogUi');
      
	//var outsiteClick = common.outsiteClick;

	$(function(){
		// leftSide
		$('.ui-box-head').on('click', function(e){
			var target = $(e.target),
				toggleTarget = target.siblings('.ui-box-container'),
				triggleTarget = target.find('i');
			if (triggleTarget.hasClass('rotate')) {
				triggleTarget.removeClass('rotate slide-up');
				toggleTarget.slideUp(380);			
			} else {
				triggleTarget.addClass('rotate slide-up');
				toggleTarget.slideDown(380);
			}
		});

		// ui-select
		$('.ui-select-trigger').on('click', function(e) {
			var target = $(e.target).closest('.ui-select-trigger'),
				toggleTarget = target.siblings('.ui-select-content');
			toggleTarget.toggle();
		})

		$('.ui-select-content').on('click', '.ui-select-item', function(e) {
			var target = $(e.currentTarget).find('a'),
				parentTarget = target.closest('.ui-select');
			parentTarget.find('.ui-select-trigger span').text(target.text()).attr({'value': target.attr('data-val')});
			parentTarget.find('.ui-select-content').hide();
		})

		$(document).on('click', function(e) {
			var target = $(e.target);
			if (target.attr('id') == 'switchCity') 
				return false;
			if (!$(e.target).parents('.ui-select')[0]) {
				$('#cityWrap').hide();
			}
		})

		// logout

		$('#logout').click(function(){
			server.logout(function(resp){
				if (resp.code == 0) {
					if (~$.inArray(roleType, [1, 11, 12])) { //
						location.href = 'login';
					} else { // city manager
						location.href = 'cityLogin';
					}
				} else {
					dialogUi.tip(resp.msg || '退出登录失败');
				}
			})
		})

		$('#switchCity').click(function(){
			if (roleType == 1 || roleType == 11 || roleType == 12)
				$('#cityWrap').toggle();
		})

		$('#cityNav').on('click', 'a', function(e){
			var target = $(e.target);
			$('#switchCity').text(target.text());
		})

		$('#lsWrap').on('click', '.ui-list-item a', function(e){
			var href = $(e.target).attr('href');
			location.href = href + '?cityId=' + exports.getCityId();
			e.preventDefault();
		})

		function getCities() {
			var cityString = '';
			server.citys(function(resp){
				if (resp.code == 0) {
					$.each(resp.body.cities, function(i, city){
						cityString += '<li><a href="'+ location.origin + location.pathname +'?cityId='+ city.id
						 +'">'+ city.name +'</a></li>';
						if (city.id == exports.getCityId()) {
							$('#switchCity').text(city.name);
						}
					})
					if (roleType == 1 || roleType == 11 || roleType == 12) {
						$('#cityNav').html(cityString);
					}
				}
			})
		}

		// 初始化城市切换列表
		getCities();

	})
	
	exports.tip = function(text) {
		var dialog = dialogUi.text(text);
		setTimeout(function(){
			if (dialog.attrs.visible.value === true) dialog.hide();
		}, 5000);
		return dialog;
	}

	exports.wait = function() {
		return dialogUi.wait();
	}	

	exports.getParm = function(name) {
		var parmObj = {};
		var result = location.search.substring(1).split('&');
		$.each(result, function(index, parm){
			var arr = parm.split('=');
			parmObj[arr[0]] = arr[1];
		})
		if (name) return parmObj[name];
		return parmObj;
	}

	exports.getCityId = function() {
		return this.getParm('cityId') || 1;
	}

	// 初始化标签选择列表
	exports.initActTagsSelect = function(callback) {
		server.tags({page: 1, size: 50}, function(resp){
			var result = '';
			if (resp.code == 0) {
				if (resp.body.tags.length) {
					$.each(resp.body.tags, function(i, tag){
						result += '<li class="ui-select-item"><a href="#" data-val="'+resp.body.tags[i].id+'">'+resp.body.tags[i].name+'</a></li>';
					})
					$('#actTagsCon').append(result);
					callback && callback();
				}
			} else {
				dialogUi.tip('标签加载失败');
			}
		})
	}

	// 初始化用户标签选择列表
	exports.initUserTagsSelect = function(callback) {
		server.tagsUser({page: 1, size: 50}, function(resp){
			var result = '';
			if (resp.code == 0) {
				if (resp.body.tags.length) {
					$.each(resp.body.tags, function(i, tag){
						result += '<li class="ui-select-item"><a href="#" data-val="'+resp.body.tags[i].id+'">'+resp.body.tags[i].name+'</a></li>';
					})
					$('#mastersTagsCon').append(result);
					callback && callback();
				}
			} else {
				dialogUi.tip('标签加载失败');
			}
		})
	}

})