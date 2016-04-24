define(function(require, exports, module) {

	var $ = require('$'),
		Calendar = require('calendar'),
		server = require('server'),
		timePicker = require('timepicker'),
		main = require('main'),
		Uploader = require('upload');

	var tip = main.tip;

	var wait = main.wait;


	/**
	 * type {String} 'edit' | 'create'
	 */
	var activeEdit = function() {
		var type, el, html, um, 
			headImg={},  // 封面图片数据 {img_id: , img_url: }. 
			allTags,  // 所有标签，载入即保存. 
			sellerData, 
			tagList = [], 
			imgsList = [],
			point = {}, // 地图经纬度
			businessId;

		el = $('<div></div>');
		html = $('#activeCreateTemplate').html();
		el.html(html);

		function setTitle(title) { // 标题
			el.find('#title').val(title);
		}

		function setIntro(intro) { // 简述
			el.find('#intro').val(intro);
		}

		function setDetail(detail) { // 详情
			el.find('#detail').val(detail);
		}

		var bTimeCalendarFlag = false,
			bTimeTimepickerFlag = false;
		function setBTime(bTime) { // 开始时间
			var yearTime = '', 
				hourTime = '';
			if (bTime) {
				var timeSplit = bTime.split(' ');
				yearTime = timeSplit[0];
				hourTime = timeSplit[1];
			}
			el.find('#bTime').val(yearTime);
			el.find('#bhourTime').val(hourTime);
			if (!bTimeCalendarFlag) {
				new Calendar({
			        trigger: '#bTime'
			        //range: ['2012-12-06', '2012-12-20']
			        //focus: bTime
			    });
			    bTimeCalendarFlag = true;
			}

			if (!bTimeTimepickerFlag) {
				el.find('#bhourTime').timePicker({time: hourTime});
				bTimeTimepickerFlag = true;
			}
		}

		var eTimeCalendarFlag = false,
			eTimeTimepickerFlag = false; 
		function setETime(eTime) { // 结束时间
			var yearTime = '', 
				hourTime = '';
			if (eTime) {
				var timeSplit = eTime.split(' ');
				yearTime = timeSplit[0];
				hourTime = timeSplit[1];
			}
			el.find('#eTime').val(yearTime);
			el.find('#ehourTime').val(hourTime);
			if (!eTimeCalendarFlag) {
				var cal = new Calendar({
			        trigger: '#eTime'
			        //range: ['2012-12-06', '2012-12-20']
			        //focus: bTime
			    });
			    eTimeCalendarFlag = true;			
			}
			if (!eTimeTimepickerFlag) {
				el.find('#ehourTime').timePicker({time: hourTime});
				eTimeTimepickerFlag = true;
			}
		    
		}

		function setAddrCity(city) { // 设置城市 
			el.find('#city').val(city);
		}
		function setAddrArea(area) { // 设置区 
			el.find('#area').val(area); 
		}
		function setAddrRoad(road) { // 设置路 
			el.find('#road').val(road); 
		}
		function setAddrNum(num) { // 设置号 
			el.find('#number').val(num);
		}
		function setAddrName(name) { // 设置地址名称
			el.find('#addrName').val(name);
		}
		function setAddRoute(route) { // 设置路线 地址 
			el.find('#route').val(route);
		}

		var headImgStatus = {
			headImgUpload: el.find('#headImgUpload'),
			headImgWrap: el.find('#headImgWrap'),
			headImgUrl: el.find('#headImgUrl'),
			show: function(url) {
				this.headImgUpload.hide();
				this.headImgWrap.show();
				var img = '<img src='+ url +' alt="">';
				this.headImgUrl.html(img);
			},
			reset: function() {
				this.headImgUrl.html('');
				this.headImgUpload.show();
				this.headImgWrap.hide();
			}
		}
		function createHeadImg(o){ // {img_url: , img_id: }
			headImg = o;
			headImgStatus.show(o.img_url);
		}
		function setHeadImgUrl(head_img_url) { // 首图
			if (head_img_url) {
				createHeadImg({img_url: head_img_url});
			} else {
				headImgStatus.reset();
			}
		}

		var ActiveImg = function(options) {
			this.datas = options;
			this.init();
		}
		ActiveImg.prototype = {
			constructor: ActiveImg,
			init: function() {
				var datas = this.datas;
				this.el = $('<div class="img-item">' +
								'<img class="active-img" src="'+ datas.img_url +'" alt="" />' +
								'<span class="img-close">X</span>' +
							'</div>');
				this.events();
			},
			events: function() {
				var _this = this,
					imgClose = _this.el.find('.img-close');
				this.el.hover(function(){
					imgClose.show();
				}, function(){
					imgClose.hide();
				})
				this.el.on('click', '.img-close', function(){
					var imgId = _this.datas.id;
					_this.close();
					for(var i=imgsList.length-1; i >= 0; i--) {
						if (imgsList[i].datas.id == imgId) {
							imgsList.splice(i, 1);
							
						}
					}
				})
			},
			close: function() {
				this.el.remove();
			}
		}
		function createImg (datas) {
			var img = new ActiveImg(datas);
			imgsList.push(img);
			$('#imgListContainer').append(img.el);
		}
		function setImgs (imgs) { // 设置背景图片
			if (imgs.length) {
				for (var i=0; i < imgs.length; i++) {
					createImg(imgs[i]);
				}
			} else {
				$.each(imgsList, function(i, img){
					img.close();
				})
			}
		}
		function clearImgs() { // 清楚活动照片
			if (imgsList.length) {
				for(var i=0; i<imgsList.length; i++) {
					imgsList[i].close();
				}
			}
			imgsList = [];
		}

		function setDetailAll (detailAll) { //
			/*if (!um) {
				um = UM.getEditor('myEditor', {
					autoHeightEnabled: true
				})				
			}
			um.setContent(detailAll);*/
		}

		var Tag = function(tag) {
			this.data = tag;
			this.init();
		}
		Tag.prototype = {
			constructor: Tag,
			init: function() {
				this.el = $('<span class="tag">'+ this.data.name +'<a class="tag-del">X</a></span>');
				this.events();
				return this.el;
			},
			events: function() {
				var _this = this;
				this.el.on('click', '.tag-del', function(e){
					_this.close();
					removeTag(_this);
					e.stopPropagation();
				})
			},
			close: function() {
				this.el.remove();
			}
		}

		function createTag(tag) {
			var flag = true;
			if (tagList.length >=1) {
				tip('最多创建一个标签');
				flag = false;
			} else {
				for (var i=0; i<tagList.length; i++) {
					if (tagList[i].data.id == tag.id) {
						tip('已经创建此标签');
						flag = false;
					} 
				}				
			}

			if (flag) {
				var newTag = new Tag(tag);
				tagList.push(newTag);
				el.find('.ind-text').before(newTag.el);
			}
		}

		function removeTag(tag) {
			for (var i=tagList.length-1; i >= 0; i--) {
				if (tagList[i] == tag) tagList.splice(i, 1);
			}
		}

		function getTagData(tagName) {
			if (allTags) {
				for (var i = 0; i<allTags.length; i++) {
					if (allTags[i].name == tagName) {
						return allTags[i];
					}
				}				
			}
		}

		function setActTags(tags) { // 标签
			if (!tags) return false;
			if ($.type(tags) == 'array') {
				if (tags.length) {
					for (var i=0; i < tags.length; i++) {
						createTag(getTagData(tags[i]));
					}
	 			}				
			} else {
				createTag(getTagData(tags));
			}
		}

		function clearTags() {
			if (tagList.length) {
				for(var i=0; i<tagList.length; i++) {
					tagList[i].close();
				}
			}
			tagList = [];
		}

		function clear() {
			sellerData = '';
			headImg = {};
			clearTags();
			clearImgs();
			businessId = '';
			point = {};
		}

		var map;
		function createMap() {
			if (!map) {
				map = new BMap.Map("mapCreate");
				var geoc = new BMap.Geocoder();    

				map.addEventListener("click", function(e){        
					var pt = e.point;
					geoc.getLocation(pt, function(rs){
						var addComp = rs.addressComponents;
						point = rs.point;
						// alert(addComp.province + ", " + addComp.city + ", " + addComp.district + ", " + addComp.street + ", " + addComp.streetNumber);
						$('#city').val(addComp.city);
						$('#area').val(addComp.district);
						$('#road').val(addComp.street);
						$('#number').val(addComp.streetNumber);
					});        
				});
				map.centerAndZoom("成都",12);
				server.citys(function(resp){
					if (resp.code == 0) {
						$.each(resp.body.cities, function(i, city){
							if (result.cityId == city.id) {
								map.centerAndZoom(city.name, 12);
							}
						})
					}
				})
				map.enableScrollWheelZoom();   //启用滚轮放大缩小，默认禁用
				map.enableContinuousZoom();    //启用地图惯性拖拽，默认禁用
				var local = new BMap.LocalSearch(map, {
					renderOptions:{map: map}
				});

				$('#searchMapBtn').click(function(){
					var text = $('#searchMapInput').val();
					if(text) {
						local.search(text);
					}
				})
/*				$('#citySel').change(function(){
					var value = $(this).val();
					if (value != 'init') {
						map.centerAndZoom(value, 12);		
					}
				})*/
			}
			return map;
		}
		function setTstatusRule(value) {
			el.find('input[name="isLoop"][value='+ value +']').attr('checked', true);
			if (value == 1) {
				el.find('#weekWrap').show();
			} else {
				el.find('#weekWrap').hide();
			}
		}
		function setWeekRules(arrays) {
			if($.type(arrays) == 'array') {
				var wgs = el.find('input[name="weekgroup"]');
				$.each(wgs, function(i, wg){
					if(~$.inArray(Number(wgs.eq(i).val()), arrays)){
						wgs.eq(i).attr('checked', true);
					}else {
						wgs.eq(i).attr('checked', false);
					}
				})
			}
		}
		function setContactWay(value) {
			el.find('#contactWay').val(value);
		}
		function setCanEnroll(value) {
			el.find('input[name="isenroll"][value='+ value +']').attr('checked', true);
		}
		function setLoveBaseNum(value) {
			el.find('#loveBaseNum').val(value);
		}
		function setShareBaseNum(value) {
			el.find('#shareBaseNum').val(value);
		}
		function init(type, datas) {
			clear();

			if (type == 'edit') sellerData = datas;
			setTitle(datas && datas.title || '');
			setIntro(datas && datas.intro || '');
			setDetail(datas && datas.detail || '');
			setBTime(datas && datas.b_time || '');
			setETime(datas && datas.e_time || '');
			setTstatusRule(datas && datas.t_status_rule || 0 );
			setWeekRules(datas && datas.week_rules || []);
			setContactWay(datas && datas.contact_way || '');
			setCanEnroll(datas && datas.can_enroll || 0);
			if (datas) {
				point.lng = datas.lon;
				point.lat = datas.lat;
			}
			// setLoveBaseNum(datas && datas.lov_base_num || '');
			// setShareBaseNum(datas && datas.share_base_num || '');
			setAddrCity(datas && datas.addr_city || '');
			setAddrArea(datas && datas.addr_area || '');
			setAddrRoad(datas && datas.addr_road || '');
			setAddrNum(datas && datas.addr_num || '');
			setAddrName(datas && datas.addr_name || '');
			setAddRoute(datas && datas.addr_route || '');
			setActTags(datas && datas.tag_name || '');
			setHeadImgUrl(datas && datas.head_img_url);
			setImgs(datas && datas.act_imgs || '');
			setDetailAll(datas && datas.detail_all || '');
			// 启用地图.
			if (type == 'create') {
				//$('#mapCreate').append(map.Ha);
				createMap();
			} else {
				var map = createMap();
				var myGeo = new BMap.Geocoder();
				// 将地址解析结果显示在地图上,并调整地图视野
				var address = datas.addr_city + datas.addr_area + datas.addr_road + datas.addr_num;
				myGeo.getPoint(address, function(point){
					if (point) {
						map.centerAndZoom(point, 16);
						map.addOverlay(new BMap.Marker(point));
					}
				}, "北京市");
			}
		}

		function setHeadImgUpload() { // 上传背景图片
			var uploader = new Uploader({ 
			    trigger: '#uploaderActiveImg',
			    name: 'img',
			    action: '/manager/managerUser/imgUp',
			    accept: 'image/*',
			    data: {'isReturnUrl': 1}
			}).change(function(files){
				$('#uploaderActiveImg').text('上传中...');
				/*for (var i=0; i<files.length; i++) {
			        console.log('you are selecting ' + files[i].name + ' Size: ' + files[i].size);
			    }*/
			    uploader.submit();
			}).success(function(response) {
			    var response = $.parseJSON(response);
			    if (response.code == 0) {
			    	createHeadImg(response.body);
			    } else {
			    	tip(response.msg);
			    }
			    $('#uploaderActiveImg').text('选择图片');
			}).error(function(file){
				tip('上传封面图失败');
				$('#uploaderActiveImg').text('选择图片');
			})
		}

		function setActiveImgUpload() {
			var uploader = new Uploader({ // 上传活动图片
			    trigger: '#activeImgUpload',
			    name: 'img',
			    action: '/manager/managerUser/imgUp',
			    data: {'isReturnUrl': 1}
			}).change(function(files){
				console.log(files.length);
				if (imgsList.length >=3) {
					tip('活动照片最多只能上传3张');
				} else {
					$('#activeImgUpload').text('上传中...');
					uploader.submit();
				}
			}).success(function(response) {
			    var response = $.parseJSON(response);
			    if (response.code == 0) {
			    	createImg({id: response.body.img_id, img_url: response.body.img_url});
			    } else {
			    	tip(response.msg);
			    }
			    $('#activeImgUpload').text('选择图片');
			}).error(function(file) {
			    tip('上传活动图失败');
			    $('#activeImgUpload').text('选择图片');
			    //createImg({id: '004', img_url: '/ling/static/images/a.jpg'});
			})	
		}

		function filterTags(tags) {
			var result = [];
			$.each(tags, function(i, tag){
				if (tag.status == 0) {
					result.push(tag);
				}
			})
			return result;
		}

		function getAllTags(fn) { // 获取所有标签
			server.tags({page: 1, size: 50}, function(resp){
				if (resp.code == 0) {
					fn(filterTags(resp.body.tags));
				} else {
					tip(resp.msg || '获取标签失败');
				}
			})
		}

		var weekGroup = {
			get: function() {
				var result = [];
				$.each($('input[name="weekgroup"]:checked'), function(i, wg){
					result.push(wg.value);
				})
				return result;
			},
			set: function() {

			}
		}

		/**
		 * 
		 * @params{Jquery Selector} 
		 * @parmas{Array | Object} regexp list. [{reg: /\d{9,10}/, message: '只能是9位-10位的数字'}]
		 */
		function checkItemValues(){
			var a = arguments, el, list=[], item, text;
			if (!a.length) return false;
			el = a[0];
			text = $.trim(el.val());

			if (a.length > 1 && $.type(a[1]) == 'string') {
				item = {reg: /\S{1,}/, message: a[1]};
				list[0] = item;
			}

			if ($.type(a[1]) == 'array') {
				list = a[1];
			}

			for (var i=0; i < list.length; i++) {
				if (!list[i].reg.test(text)) {
					el.focus();
					tip(list[i].message);
					return false;
				}
			}
			return text;
		}

		/**
		 * get 
		 * 
		 */
		function getValues(callback) { 
			var title, intro, addrName, detail, bTime, eTime, bhourTime, ehourTime, addrCity, addrArea, addrRoad, addrNum,
				addrRoute, tagId, headImgId, imgIds=[], actId, lon, lat, contactWay,
				canEnroll, weekRules, tStatusRule, shareBaseNum, loveBaseNum;
			(title = checkItemValues($('#title'), '活动标题不能为空')) &&
			(intro = checkItemValues($('#intro'), '内部标题不能为空')) &&
			(addrCity = checkItemValues($('#city'), '城市不能为空')) &&
			(addrNum = checkItemValues($('#number'), '号不能为空')) &&
			(addrName = checkItemValues($('#addrName'), '地址名称不能为空')) &&
			//(detail = checkItemValues($('#detail'), '活动详情不能为空')) &&
			(bTime = checkItemValues($('#bTime'), '开始时间不能为空')) &&
			(bhourTime = checkItemValues($('#bhourTime'), '开始时间时分秒不能为空')) &&
			(eTime = checkItemValues($('#eTime'), '结束时间不能为空')) &&
			(ehourTime = checkItemValues($('#ehourTime'), '结束时间时分秒不能为空')) &&			

			function(){
				if (!point.lng) {
					tip('经度不能为空');
					return false;
				} else {
					lon = point.lng;
					return true;
				}
			}() &&
			function(){
				if (!point.lat) {
					tip('纬度不能为空');
					return false;
				} else {
					lat = point.lat;
					return true;
				}
			}() &&
			//(addrRoute = checkItemValues($('#route'), '交通信息不能为空')) && 
			function(){ // 标签验证规则
				if (!tagList.length) {
					tip ('必须添加标签');
					return false;
				} else {
					tagId = tagList[0].data.id;
					return true;			
				}
			}() && 
			function(){ // 封面照片验证.
				if (type == 'create' && !headImg.img_id) {
					tip ('请上传封面照片');
					return false;
				} else {
					return true;
				}
			}() &&
			function(){ // 活动照片验证规则
				if (!imgsList.length) {
					tip ('至少要上传一张活动照片');
					return false;
				} else {
					$.each(imgsList, function(index, img){
						imgIds.push(img.datas.id);
					});
					return true;			
				}
			}() &&
			function(){
				actId = sellerData && sellerData.id || '';
				headImgId = headImg.img_id;
				intro = $.trim($('#intro').val());
				detail = $('#detail').val(); // 详情需要特殊处理.
				addrArea = $.trim($('#area').val());
				addrRoad = $.trim($('#road').val());
				addrRoute = $.trim($('#route').val());
				contactWay = $.trim($('#contactWay').val());
				tStatusRule = $('input[name="isLoop"]:checked').val();
				shareBaseNum = $('#shareBaseNum').val();
				loveBaseNum = $('#loveBaseNum').val();
				canEnroll = $('input[name="isenroll"]:checked').val();
				if (canEnroll == 1 && !contactWay) {
					tip('如果你要报名，必须填写报名电话');
				} else {
					var parms = {
						actId: actId,
						title: title,
						intro: intro,
						lon: lon,
						lat: lat,
						addrCity: addrCity,
						addrArea: addrArea,
						addrRoad: addrRoad,
						addrNum: addrNum,
						addrName: addrName,
						addrRoute: addrRoute,
						contactWay: contactWay,
						bTime: bTime + ' ' + bhourTime,
						eTime: eTime + ' ' + ehourTime,
						tStatusRule: tStatusRule,
						detail: detail,
						//detailAll: detailAll,
						tagId: tagId,
						imgIds: imgIds,
						headImgId: headImgId,
						//shareBaseNum: shareBaseNum,
						//lovBaseNum: loveBaseNum,
						canEnroll: canEnroll
					};
					if (tStatusRule == 1) {
						parms.weekRules = weekGroup.get();
					}
					if ($('input[name="isFree"]:checked').val()==1) {
						parms.cost = $.trim($('#cost').val());
					}
					if (result.cityId) {
						parms.cityId = result.cityId;
					}
					/*if (type == 'create' && businessId) {
						parms.businessId = businessId;
					}
*/					//console.log(parms);
					callback(parms);			
				}
		
			}();
		}


		// 事件绑定.
		el.find('.ui-industry-con').on('click', function(e){
			var parent = $(e.target).closest('.ui-industry-con'),
				input = parent.find('#tagsInput');
			input.focus();
			input.val('');
		})

		el.find('#tagsInput').keydown(function(e){
			if (e.keyCode == 13) {
				$(this).trigger('blur');
			}
		})

		el.find("input[name='isenroll']").change(function(e){
			var contactWay = $.trim($('#contactWay').val());
			if (e.target.value == 1 && !contactWay){
				$('#isenrollTip').text('(' + '如果要报名， 必须填写报名电话' + ')');
			} else {
				$('#isenrollTip').text('');
			}
		})

		el.find('#tagsCon').on('click', '.ui-button-tag', function(e){
			var target = $(e.target),
				id = target.attr('data-id'),
				name = target.text();
			createTag({name: name, id: id});
		})

		el.find('#hImgReset').click(function(e){
			headImgStatus.reset();
		})
		// start | close the loop.
		el.find('[name="isLoop"]').change(function(e){
			var target = $(e.target), flag, weekWrap = el.find('#weekWrap');
			flag = target.attr('id') == 'startLoop' ? 'start' : 'close';
			if (flag == 'start') {
				weekWrap.show();
			} else {
				weekWrap.hide();
			}
		})

		el.find('[name="isFree"]').change(function(e){
			var target = $(e.target), flag, cost = el.find('#cost');
			flag = target.attr('id') == 'free' ? 'free' : 'unfree';
			if (flag == 'free') {
				cost.hide();
			} else if (flag == 'unfree') {
				cost.show();
			}
		})		

		el.find('#reset').click(function(){ // 重置
			init('create');
		})

		el.find('#save').click(function(){ // 保存
			getValues(function(o){
				var parms = o;
				var dialog = wait();
				if (type == 'create') {
					delete parms.actId;
					server.addAct(parms, function(resp){
						dialog.hide();
						if (resp.code == 0) {
							var d = tip('活动添加成功');
							//$('#return')[0].click();
							//d.hide();
							result.trigger('add');
							init('create');
						} else {
							tip(resp.msg || '活动添加失败');
						}
					})
				} else if (type == 'edit') {
					!parms.headImgId && (delete parms.headImgId);
					server.updateAct(parms, function(resp){
						dialog.hide();
						if (resp.code == 0) {
							tip('保存成功');
							$('#return')[0].click();
							result.trigger('save');
						} else {
							tip(resp.msg || '保存失败');
						}
					})						
				}
			})
		})

		var result = {
			load: function(typeway, datas, fn) {
				type = (typeway == 'edit' && typeway) || 'create';
				var fn = ($.type(datas) == 'function') && datas;
				if (type == 'create') {
					init(type);
					$('#reset').show();
					//if (datas && datas.businessId) {
						//businessId = datas.businessId;
					//}
				} else {
					init(type, datas);
					$('#reset').hide();
				}
			},
			init: function(fn) {
				fn && fn.call(null, el);
				setHeadImgUpload();
				setActiveImgUpload();
				getAllTags(function(tags){
					var content = '';
					allTags = tags;
					for(var i=0; i<tags.length; i++) {
						content += '<a href="javascript:;" data-id='+ tags[i].id +' class="ui-button-tag ui-button ui-button-morange">'+ tags[i].name +'</a>';
					}
					$('#tagsCon').html(content);
				});
			},
			setCityId: function(cityId) {
				this.cityId = cityId;
			}			
		}

		return result;
	}

	return activeEdit;

})