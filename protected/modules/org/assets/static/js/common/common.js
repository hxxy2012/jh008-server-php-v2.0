define(function(require, exports, module){
	var $ = require('$');
	var tip = require('main').tip,
		K = require('K');

	/**
	 * 工具函数
	 */
	var util = { 
		// 
		htmlDecode: function(text) {
			var temp = document.createElement("div");
			temp.innerHTML = text;
			var output = temp.innerText || temp.textContent;
			temp = null;
			return output;
		}
	}

	/** 
	 * @params{Object}
	 		-ThList
	 		-columnNameList
	 		-rowClass
	 		-source
	 		-perPageNums
	 */
	var PagTable = function(options, events, eventsHandle) {
		var pagTable = new K.PaginationTable({
			ThList: options.ThList,
			columnNameList: options.columnNameList,
			rowClass: function(index) {
				if (index%2 == 0) {
					return 'odd';
				} else {
					return 'even';
				}
			},
			source: options.source,
			perPageNums: options.perPageNums
		});

		pagTable.setEvents(events, eventsHandle);

		pagTable.on('errorSwitch', function(obj){
			//console.log(obj);
			if(obj.type == 'switch'){
				if(obj.page == 1){
					tip('已经是第一页')
				}else{
					tip('已经是最后一页')
				}
			}else if(obj.type == 'submit') {
				if(obj.page === ''){
					tip('不能为空')
				}else{
					tip('页码不正确');
				}
			}
		})

		pagTable.run();
		return pagTable;
	}

	/**
	 * 
	 */
	var pageManager = (function() {
		var list = [];

		function checkIsExit(name) {
			// default by name
			if (list.length) {
				for (var i=0, l=list.length; i<l; i++) {
					if (list[i].name == name) {
						return list[i];
					}
				}
			}
			return false;
		}

		/**
		 * @params {Object} 
		 		-el
		 		-parent
		 		-name
		 */
		function add(/* object */) {
			var argument = arguments[0];
			if (argument && $.type(argument) == 'object') {
				if (!checkIsExit(argument.name)){
					list.push(argument);
				}
			} else {
				throw Error('没有传入page add 参数');
			}
		}

		function show(name) {
			var result = checkIsExit(name);
			if (result) {
				hide();
				result.parent && result.parent.show();
				result.el && result.el.show();
			} else {
				throw new Error('name 不存在');
			}
		}

		function hide() {
			if (list.length) {
				for (var i=0, l=list.length; i<l; i++) {
					list[i].parent && list[i].parent.hide();
					list[i].el && list[i].el.hide();
				}
			}
		}

		function render(name, ele) {
			var result = checkIsExit(name);
			if (result) {
				show(name);
				result.el && result.el.html(ele);
			} else {
				throw new Error('name 不存在');
			}
		}

		return {
			add: add,
			show: show,
			hide: hide,
			render: render
		}
	})()


	var Validate = function() {
		var list = [];
		
		// @params {boolean | string | function}
		// @params {fn} if check false then execute
		function _check(term, fn) {
			if (term) {
				list.push({term: term, fn: fn});
			}
		}

		function start() {
			var length = list.length, index = 0; 
			function runItem(item) {
				var type, term, fn;	
				function goRun() {
					index ++;
					if (index >= length) {
						return true;
					}
					return runItem(list[index]);
				}
				if (item) {
					term = item.term;
					fn = item.fn;				
					type = $.type(term);
					if (type == 'function') {
						if (!term()) {
							fn && fn();
							return false;
						} else {
							return goRun();
						}
					} else {
						if (!term) {
							fn && fn();
							return false;
						} else {
							return goRun();
						}
					}	
				}
			}
			return runItem(list[0]);
		}



		function run(fn) {
			if (start()) {
				fn && fn();
			}
		}
 
		return {
			check: _check,
			run: run
		}
	}

	var regexp = {
		user: /^\S{6,16}$/, // 6-16位
		password: /^\S{6,16}$/, // 6-16位
		size: function(start, end) {
			if (!end) end = start;
			return new RegExp('^\\S{' + start + ',' + end + '}$');			
		},
		checkUser: function(user) {
			return function() {
				if (!regexp.user.test(user)) 
					return false;
				return true;				
			}
		},
		checkSize: function(val, start, end) {
			return function() {
				var reg = regexp.size(start, end);
				if (!reg.test(val))
					return false;
				return true;				
			}
		},
		checkEmpty: function(val) {
			return function() {
				if (!val.length)
					return false;
				return true;				
			}
		},
		checkSame: function(a, b) {
			return function() {
				if (a != b) 
					return false;
				return true;
			}
		}
	}

	var myValidate = function(t) {
		if(t){
			tip = t;
		}
		var validate = Validate();
		function _check(term, text) {
			validate.check(term, function(){
				tip(text);
			});
		}	
		function _run(fn) {
			validate.run(fn);
		}
		return {
			check: _check,
			run: _run
		}
	}
	myValidate.regexp = regexp;

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
	 *
	 */
	var outsiteClick = function() {
		var eventList = [];
		$(document).click(function(e) {
			var target = e.srcElement;
			$.each(eventList, function(i, item) {
				var flag = false;
				if (item.el[0] == target) {
					flag = true;
				} else {
					if (item.filter && $.type(item.filter) == 'array') {
						for (var i=0; i<item.filter.length; i++) {
							if (item.filter[i].className) {
								if ($(target).hasClass(item.filter[i].className)) {
									flag = true;
									break;
								}
							} else if(item.filter[i].parent) {
								if ($(target).parents('.'+item.filter[i].parent)[0]){
									flag = true;
									break;
								}
							}
						}
					}					
				}

				if (!flag) {
					item.fn.call(item.context, target);	
				}
			})
		});

		return function(el, callback, filter, /*isTrue,*/ context) {
			var parms = {
				el: el,
				fn: callback,
				filter: filter || null,
				context: context || null/*,
				isTrue: isTrue.toString() == 'false' ? isTrue : true*/
			}
			eventList.push(parms);
		}
	}


	exports.PagTable = PagTable;
	exports.pageManager = pageManager;
	exports.myValidate = myValidate;
	exports.checkItemValues = checkItemValues;
	exports.outsiteClick = outsiteClick;
	exports.util = util;
	
})