define(function(require, exports, module){
	// 资讯类的添加、修改
	var $ = require('$'),
		common = require('common'),
		dialogUi = require('dialogUi'),
		main = require('main'),
		server = require('server'),
		//static = require('static'),
		K = require('K'),
		Uploader = require('upload');
	var tip = main.tip,
		wait = main.wait;
	/**
	 * type {String} 'edit' | 'create'
	 * @params {String} tempalte    tempalte id.
	 * @params {Number} typeId
	 		-- 'information' 1
	 		-- 'memory' 2
	 		-- 'ticket' 3
	 		-- 'interview' 4
	 		-- 'carousel' 5
	 * @parmas {Number} cityId    
	 */
	var ActiveEdit = function(options) {
		var authority = {
			tags: [1,2,3],
			price: [3],
			intro: [1,2,3,4],
			hImgId: [1,2,3,5]
		};

		function indexOf(array, value) {
			return ~$.inArray(value, array);
		}

		var type, el, html, um, 
			headImg={},  // 封面图片数据 {img_id: , img_url: }. 
			allTags,  // 所有标签，载入即保存. 
			tagList = [],
			sellerData,
			checkItemValues = common.checkItemValues;
		
		el = $('<div></div>');
		el.html($('#'+options.template).html());

		function setTitle(title) { // 标题
			el.find('#title').val(title);
		}

		function setIntro(intro) { // 简介
			el.find('#intro').val(intro);
		}

		function setDetail(detail) { // 详情
			el.find('#detail').val(detail);
		}

		function setPrice(price) { // 价格
			el.find('#price').val(price);
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

		function setDetailAll (detailAll) { //
			if (!um) {
				um = UE.getEditor('myEditor');		
			}
			um.ready(function(){
				um.setContent(detailAll);
			})
			
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
			for (var i = 0; i<allTags.length; i++) {
				if (allTags[i].name == tagName) {
					return allTags[i];
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
		}

		function init(type, datas) {
			clear();
			if (type == 'edit') sellerData = datas;
			setTitle(datas && datas.title || '');		
			setDetailAll(datas && datas.detail || '');
			if (indexOf(authority.hImgId, options.typeId)) {
				setHeadImgUrl(datas && datas.h_img_id);
			}
			if (indexOf(authority.tags, options.typeId)) {
				setActTags(datas && datas.tag_name || '');
			}
			if (indexOf(authority.intro, options.typeId)) {
				setIntro(datas && datas.intro || '');
			}
			if (indexOf(authority.price, options.typeId)) {
				setPrice(datas && datas.price || '');
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

		/**
		 * get 
		 * 
		 */
		function getValues(callback) { 
			var title, intro, detail, headImgId,  newsId, tagId;
			detail = UE.getEditor('myEditor').getContent();
			(title = checkItemValues($('#title'), '活动标题不能为空')) &&
			//(intro = checkItemValues($('#intro'), '活动简述不能为空')) &&
			function(){ // 封面照片验证.
				if (!indexOf(authority.hImgId, options.typeId)) return true;
				if (type == 'create' && !headImg.img_id) {
					tip ('请上传封面照片');
					return false;
				} else {
					return true;
				}
			}() &&
			function(){ // 标签验证规则
				if (!indexOf(authority.tags, options.typeId)) return true;
				if (!tagList.length) {
					tip ('必须添加标签');
					return false;
				} else {
					tagId = tagList[0].data.id;
					return true;			
				}
			}() && 
			function(){
				newsId = sellerData && sellerData.id || '';
				var parms = {
					newsId: newsId,
					title: title,
					detail: detail
				};
				if (indexOf(authority.hImgId, options.typeId)) {
					headImgId = headImg.img_id;
					parms.hImgId = headImgId;
				}
				if (indexOf(authority.tags, options.typeId)) {
					parms.tagId = tagId;
				}
				if (indexOf(authority.intro, options.typeId)) {
					parms.tagId = tagId;
					intro = $.trim($('#intro').val());
					parms.intro = intro;
				}
				if (indexOf(authority.price, options.typeId)) {
					var price = $.trim($('#price').val());
					parms.price = price;
				}
				callback(parms);
			}();
		}

		el.find('#hImgReset').click(function(e){
			headImgStatus.reset();
		})

		el.find('#tagsCon').on('click', '.ui-button-tag', function(e){
			var target = $(e.target),
				id = target.attr('data-id'),
				name = target.text();
			createTag({name: name, id: id});
		})

		el.find('#reset').click(function(){ // 重置
			init('create');
		})

		el.find('#save').click(function(){ // 保存
			getValues(function(o){
				var parms = o;
				var dialog = wait();
				if (type == 'create') {
					delete parms.newsId;
					parms.cityId = options.cityId;
					parms.typeId = options.typeId;
					server.addNews(parms, function(resp){
						if (resp.code == 0) {
							dialog.destroy();
							result.trigger('add', resp.body.news_id);
						} else {
							tip(resp.msg || '保存失败');
						}
					})
				} else if (type == 'edit') {
					!parms.headImgId && (delete parms.headImgId);
					parms.cityId = options.cityId;
					parms.typeId = options.typeId;
					server.updateNews(parms, function(resp){
						dialog.destroy();
						if (resp.code == 0) {
							result.trigger('save', sellerData.id);
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
				} else {
					init(type, datas);
					$('#reset').hide();
				}
			},
			init: function(fn) {
				fn && fn.call(null, el);
				if (indexOf(authority.hImgId, options.typeId)) {
					setHeadImgUpload();
				}
				// setFileUpload();
				if (indexOf(authority.tags, options.typeId)) {
					getAllTags(function(tags){
						var content = '';
						allTags = tags;
						for(var i=0; i<tags.length; i++) {
							content += '<a href="javascript:;" data-id='+ tags[i].id +' class="ui-button-tag ui-button ui-button-morange">'+ tags[i].name +'</a>';
						}
						$('#tagsCon').html(content);
					});					
				}
			}			
		}

		K.Observe.make(result);

		return result;
	}

	

	module.exports = ActiveEdit;
})