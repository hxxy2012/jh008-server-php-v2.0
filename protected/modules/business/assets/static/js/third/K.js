define(function(require, exports, module) {
 var $ = require('$');
// define("jquery/jquery/1.7.2/jquery-debug", [], function () { return jQuery; } );
(function(undefined){
	var P;

	P = {};

	P.introduce = {
		__BUILD_TIME: '20130925145439',

		version: '0.01',

		author: 'shizi'
	};

	window.K = window.K = P;

})();


(function(){

// 订阅/发布 
K.Observe = (function(){

	var observe = {
		on: function(name, callback, context){
			if(!this['eventList']){
				this['eventList'] = {};
			}
			this.eventList[name] = {};
			this.eventList[name].context = context || '';
			this.eventList[name].callback = callback;
		//	this[name] = callback;
		},
		trigger: function(name){
			var argument = Array.prototype.slice.call(arguments, 1);
			if(this.eventList && this.eventList[name] && $.type(this.eventList[name].callback)==='function'){
				var context = this.eventList[name].context || this;
				this.eventList[name].callback.apply(context, argument);
			}
		},
		remove: function(name){
			delete this.eventList[name];
		},
		has: function(name){
			return this.eventList[name] ? true : false;
		},
		make: function(o){
			for(var i in this){
				if(i!='make'){
					o[i] = this[i];
				}
			}
		}
	};

	return observe;
})();

/**
 * 
 * {value: val, isEmpty: callback, validate: fn }
 *
 */
K.Validater = function() {

	var Validater = {};

	function run(data) {
		var isEmpty = data.isEmpty,
			value = data.value || '';
		if(!value && isEmpty) {
			isEmpty.call(null, value);
			return false;
		}
		if(data.validate && !data.validate.call(null, value)) {
			return false;
		}
		return true;
	}

	var createValidater = function() {
		var newValidater = {};
		newValidater.list = [];
		// @param {Array | Object}
		newValidater.push = function() { 
			var _this = this,
				data = arguments[0],
				datatype = $.type(data);
			if(datatype == 'array') {
				_this.list.concat(data);
			}else if(datatype == 'object') {
				_this.list.push(data);
			}
		};
		// success callback
		newValidater.run = function( done ) {
			var _this = this,
				list = this.list,
				flag = true;
			for(var i=0, l=list.length; i<l; i++) {
				if(!run(list[i])) {
					flag = false;
					break;
				}
			}

			flag && done();
		};
		return newValidater;
	}	

	var init = function() {
		return createValidater();
	}
	return init;
}

})();

(function(K){

	var Util = {};

	/**
	 * concat multiple array.
	 * @ params {Array | Arguments} 
	 * @ return {Array}
	 */
	function _concat() {

		var arr = [];

		$.each(arguments, function(index, argument){
			if ($.type(argument) == 'array') {
				arr = arr.concat(argument);
			} else if ($.type(argument) == 'object' && argument.length) {
				for (var i=0, l=argument.length; i < l; i++) {
					arr[arr.length] = argument[i];
				}
			}
		})

		return arr;
	}

	/**
	 * delegate events convinently.
	 * 
	 */
	Util.BindEventsHelper = (function() {

		var delegateEventSplitter = /^(\S+)\s*(.*)$/;

		/**
		 * set delegate events
		 * @params {Object} the object of delegate events. 
		 * 		such as {'click #edit': 'edit'} 
		 * @params {Object}	the object of event handlers.
		 *		such as { edit: function(e){}}
		 * @params {jQuery(el)} the dom that event delegate targetss.
		 *
		 * @params {Object} context of function excute.
		 *
		 * @params optional these params to be used as arguments which event handlers.
		 *
		 */
		function delegateEvents( events, eventsObj, el, context) {
			var properties = arguments,
				extraPros = Array.prototype.slice.call(properties, 4),
				context = context || null;

			var curry = function(method) {
				return function(){
					//concat $(e) & extraPros.
					var args = _concat(arguments, extraPros);
					method.apply(context, args);
				}
			};

			for(key in events){
				var meth = events[key];
					method = eventsObj[meth];
				//...
				var match = key.match(delegateEventSplitter),
					eventName = match[1], 
					selector = match[2],
					method = curry(method);
				if(selector == ''){
					// if selector is '' , the handler bind to outer el.
					el.bind(eventName, method);
				}else{
					// if selector is exist, selector's events delegate to outer el.
					el.delegate(selector, eventName, method);
				}
			}
		}

		/**
		 * undelegate target's events.
		 * 
		 */
		
		function undelegateEvents( jqueryEl ) {
				jqueryEl.undelegate();
		}

		function setEvents() {
			delegateEvents.apply(null, arguments);
		}

		return {
			setEvents: setEvents,
			undelegate: undelegateEvents
		};

	})();

	K.Util = Util;

})(K);

(function(K){
	/**
	 * TableRow 
	 * it is useful for Table.
	 * @params {Object} the Data for row
	 *
	 */

	var util = K.Util;

	var TableRow = function(options, parentTable) {
		this.parent = parentTable;
		this.initialize(options);
	}

	TableRow.prototype = {
		constructor: TableRow,
		initialize: function(options) {
			$.extend(this, options);
			this.El = $('<tr></tr>');
			this.render(this.data);
			this.hasSetEvents = false;
		},
		render: function(data) {
			var columnNameList = this.columnNameList,
				_this = this,
				data = data || _this.data,
				content = '',
				El;

			$.each(columnNameList, function(i, name) {
				if (name == 'index') {
					var page = (_this.parent.currentPage-1)*_this.parent.perPageNums + _this.index + 1;
					content += _this.createColItem(page);
				} else if ($.type(name) == 'string') {
					content += _this.createColItem(data[name]);
				} else if ($.type(name) == 'function') {
					content += _this.createColItem(name.call(null, data));
				} else if ($.type(name) == 'object') {
					content += _this.createColItem(data[name.name], name.class)
				}
				
			});
			this.El.html(content);
			this.setClass();
		},
		createColItem: function(value, className) {
			var classname = className ? 'class=' + className : ''; 
			return '<td ' + classname + '>' + value + '</td>';
		},
		setEvents: function(events, eventHandleObj) {
			if (events) {
				util.BindEventsHelper.setEvents(events, eventHandleObj, this.El, null, this);
				this.hasSetEvents = true;
			}
		},
		setClass: function() {
			var rowClass = this.rowClass,
				type = $.type(rowClass),
				index = this.index,
				className;

			if (type == 'string') {
				className = rowClass;
			} else if (type == 'function') {
				className = rowClass.call(this, index);
			}

			this.El.removeClass().addClass(className);
		},
		set: function(dataObj) {
			var _this = this;
			$.each(dataObj, function(name, val){
				_this.data[name] = val;
			})
		},
		setIndex: function(index) {
			this.index = index;
		},
		setData: function(data) {
			this.data = data;
		},
		destory: function() {
			this.El.remove();
			util.BindEventsHelper.undelegate(this.El);
			this.parent && this.parent.destoryRow(this);
		},
		refresh: function(data) {
			this.render(data);
		}
	}

	K.TableRow = TableRow;

})(K);

(function(K){
	/**
	 * Table
	 * @params {Object}
	 * depend on util jQuery
	 */	

	var TableRow = K.TableRow,
		observe = K.Observe;

	Table = function(options) {
		this.initialize(options);
	};

	Table.prototype = {
		constructor: Table,
		initialize: function(options) {
			var el,
				eltype,
				El,
				result, self;
			self = this;
			var attrs = {
				rowsList: [],
				El: ''
			}
			this.options = $.extend(attrs, options);
			if ((el = this.options.el)) {
				eltype = $.type(el);
				// sure El.
				if (el instanceof jQuery) {  
					// el is instance of jQuery	
					El = el;
				} else if (eltype == 'string') { 
					// default be id.
					if(!(El = $('#' + el)).length) return new ReferenceError('该dom对象不存在,请传入id');
				} else if(eltype == 'function') {
					// Dynamic afferent the el
					result = el.call(null);
					if (result && result instanceof jQuery) {
						El = result;
					} else if (result && $.type(result) == 'string') {
						El = $(result);
					} else {
						return new TypeError('方法el返回的格式不正确');
					}
				}
			} else if (this.options.ThList.length) {
				var El = $('<table class="ui-table">' +
							'<thead>' +
						        '<tr></tr>' +
						    '</thead>' +
							'<tbody></tbody>' +
						 '</table>'),
					container = El.find('tr');
				$.each(this.options.ThList, function(i, n){
					if ($.type(n) == 'string') {
						container.append($('<td>' + n + '</td>'));
					} else if($.type(n) == 'object') {
						if (n.isOrderBy == true) {
							var content = $('<td>' + n.text + '</td>');
							var triangle = $('<i class="triangle-up" title="按此排序"></i>');
							content.append(triangle);
							container.append(content);
							triangle.bind('click', (function(locX){
								return function(e) {
									self.trigger('orderBy', locX);
								}
							})(i))
						}
					}
				})
			}
			this.El = El;
		},
		run: function() {
			this.render();
			this._setEvents();
		},
		render: function() {
			var source = this.options.source,
				_this = this,
				type = $.type(source);
			if (type == 'array' && source.length) {
				this._render(source);
			} else if(type == 'function') {
				source.call(this, $.proxy(_this, '_render'));
			}
		},
		_render: function(datas) {
			var _this = this,
				rowsList = _this.options.rowsList,
				rowsListLength = rowsList.length,
				datasLength;

			if ($.type(datas) != 'array') throw new TypeError('数据类型必须为数组');

			datasLength = datas.length;

			if (rowsListLength > datasLength) {
				for (var i=datasLength, l=rowsListLength; i < l; i++) {
					rowsList[i].destory();
				}
				rowsList.splice(datasLength, rowsListLength - datasLength);
			}
			
			$.each(datas, function(i, rowData){
				if (rowsList[i] instanceof TableRow) {
					//rowsList[i].setData(rowData);
					rowsList[i].refresh(rowData);
				} else {
					var tableRow = _this.createRow(i, rowData);
					rowsList.push(tableRow);
					_this.El.find('tbody').append(tableRow.El);
				}
			})
		},
		createRow: function(index, rowdata) {
			var _this = this,
				columnNameList = _this.options.columnNameList,
				rowClass = _this.options.rowClass;

			return new TableRow({
					data: rowdata,
					columnNameList: columnNameList,
					rowClass: rowClass,
					index: index
				}, _this);
		},
		setEvents: function(events, eventHandleObj) {
			this.events = events;
			this.eventHandleObj = eventHandleObj;
		},
		_setEvents: function() {
			var self = this,
				rowsList = self.options.rowsList;
			
			if (rowsList) {
				$.each(rowsList, function(index, row){
					self.setRowEvents(row);
				})
			}
		},
		setRowEvents: function(row) {
			var events = this.events,
				eventHandleObj = this.eventHandleObj;

			events && eventHandleObj && row.setEvents(events, eventHandleObj);
		},
		setOptions: function(options) {
			$.extend(this.options, options);
		},
		destoryRow: function(row) {
			var rowsList = this.options.rowsList;
			rowsList.splice(row.index, 1);
			this.refresh();
		},
		destory: function() {
			this.El && this.El.remove();
			var rowsList = this.options.rowsList;
			$.each(rowsList, function(index, row){
				row.destory();
			});
			rowsList = [];	
		},
		refresh: function(datas) {
			var self = this,
				rowsList;

			if (datas) {
				self._render(datas);
				return true;
			}

			rowsList = this.options.rowsList;
			//console.log()
			$.each(rowsList, function(index, row){
				if (!row.hasSetEvents) {
					self.setRowEvents(row);
				}
				if (row.index != index) {
					row.setIndex(index);
					row.refresh();
				}
			})
		}
	};

	observe.make(Table.prototype);

	K.Table = Table;

})(K);

(function(K){
  /**
   * inheritance implementation.
   * depend on  prototype.js
   * 
   */
   
  var Class = (function() {
    var _toString = Object.prototype.toString,
        _hasOwnProperty = Object.prototype.hasOwnProperty;

    function keys(object) {
      if ($.type(object) !== 'object') { throw new TypeError(); }
      var results = [];
      for (var property in object) {
        if (_hasOwnProperty.call(object, property))
          results.push(property);
      }

      if (IS_DONTENUM_BUGGY) {
        for (var i = 0; property = DONT_ENUMS[i]; i++) {
          if (_hasOwnProperty.call(object, property))
            results.push(property);
        }
      }

      return results;
    }

    function argumentNames(value) {
      var names = value.toString().match(/^[\s\(]*function[^(]*\(([^)]*)\)/)[1]
        .replace(/\/\/.*?[\r\n]|\/\*(?:.|[\r\n])*?\*\//g, '')
        .replace(/\s+/g, '').split(',');
      return names.length == 1 && !names[0] ? [] : names;
    }

    function wrap(method, wrapper) {
      var __method = method;
      return function() {
        var _this = this;
        var a = update([bind(_this, __method)], arguments);
        return wrapper.apply(this, a);
      }
    }

    function bind(method, context) {
      var tmp;

      if ($.type(context) == 'function') {
        tmp = context;
        context = method;
        method = tmp;
      }

      var bound = function() { 
        var a = arguments;
        return method.apply(context, a);
      }

      return bound;
    }

    function $A(iterable) {
      if (!iterable) return [];
      if ('toArray' in Object(iterable)) return iterable.toArray();
      var length = iterable.length || 0, results = new Array(length);
      while (length--) results[length] = iterable[length];
      return results;
    }

    function update(array, args) {
      var arrayLength = array.length, length = args.length;
      while (length--) array[arrayLength + length] = args[length];
      return array;
    }


    var IS_DONTENUM_BUGGY = (function(){
      for (var p in { toString: 1 }) {
        if (p === 'toString') return false;
      }
      return true;
    })();

    function subclass() {};
    function create() {
      var parent = null, properties = $A(arguments);
      if ($.isFunction(properties[0]))
        parent = properties.shift();

      function klass() {
        this.initialize.apply(this, arguments);
      }

      $.extend(klass, Class.Methods);
      klass.superclass = parent;
      klass.subclasses = [];

      if (parent) {
        subclass.prototype = parent.prototype;
        klass.prototype = new subclass;
        parent.subclasses.push(klass);
      }

      for (var i = 0, length = properties.length; i < length; i++)
        klass.addMethods(properties[i]);

      if (!klass.prototype.initialize)
        klass.prototype.initialize = $.noop;

      klass.prototype.constructor = klass;
      return klass;
    }

    function addMethods(source) {
      var ancestor   = this.superclass && this.superclass.prototype,
          properties = keys(source);

      if (IS_DONTENUM_BUGGY) {
        if (source.toString != Object.prototype.toString)
          properties.push("toString");
        if (source.valueOf != Object.prototype.valueOf)
          properties.push("valueOf");
      }

      for (var i = 0, length = properties.length; i < length; i++) {
        var property = properties[i], value = source[property];
        if (ancestor && $.isFunction(value) &&
            argumentNames(value)[0] == "$super") {
          var method = value;
          value = wrap((function(m) {
            return function() { return ancestor[m].apply(this, arguments); };
          })(property), method);

          value.valueOf = (function(method) {
            return function() { return method.valueOf.call(method); };
          })(method);

          value.toString = (function(method) {
            return function() { return method.toString.call(method); };
          })(method);
          
        }
        this.prototype[property] = value;
      }

      return this;
    }

    return {
      create: create,
      Methods: {
        addMethods: addMethods
      }
    };

  })();

  K.Class = Class;

})(K);

(function(K){

	var observe = K.Observe,
		util = K.Util,
		bindEvents = util.BindEventsHelper;

	var ATTRS = {
		// 总页数
		totalPage: 10,
		// 默认选中的页数
		currentPage: 1,
		// 当前页的最大紧邻前置页数（不包括最前面的显示页数）
		preposePagesCount: 2,
		// 当前页的最大紧邻后置页数
		postposePagesCount: 1,
		// 第一个"..."前显示的页数
		firstPagesCount: 2,
		// 第二个"..."后显示的页数
		lastPagesCount: 0
	};

	var Pagination = function(options) {
		this.init(options);
	}

	Pagination.prototype = {
		constructor: Pagination,
		init: function(config){
			this.El = $('<div id="page_bar" class="page-ctrl">');
			this.curConfig = $.extend(true, ATTRS, config);
			this.render();
			this.setEvents();
		},

		// function:   add callback  to fnList 
		addfn: function(obj) {
			var self = this;
			this.fnList = $.extend(self.fnList, obj, true);
		},

		setEvents: function(events) {
			var self = this;
			bindEvents.setEvents({
				'click .page': 'turnPage',
				'click #page_button':'doSubmit'
				}, self, self.El, self, 345);
		},

		doSubmit: function(){
			var val = this.El.find('#page_input').val();
				num = Number(val);
			var totalPage = this.get('totalPage') > 0 ? this.get('totalPage') : 1;
			
			if (num <= totalPage && num >0){
				this._switchToPage(num);
			} else {
				this.trigger('errorSwitch', {type: 'submit', page: val});
			}
		},

		turnPage: function(e){
			var totalPage = this.get('totalPage') > 0 ? this.get('totalPage') : 1,
				currentPage = this.get('currentPage');
				target = $(e.target).closest('.page'),
				isbtn = $(e.target).closest('.page').hasClass('page-trigger') ? true: false;
				num = Number(target.attr('page'));

			if (isbtn && ( num <= 1 && currentPage == 1 ) || (num >= totalPage && currentPage == totalPage)) {
				this.trigger('errorSwitch', {type: 'switch', page: num});
			} else {
				this._switchToPage(num);
			}
		},

		render: function(config){
			var self = this;
			this.curConfig = $.extend(self.curConfig, config);
			this.renderUI();
		},

		renderUI: function(){
			this._resetPagination();
		},
		get: function(which){
			return this.curConfig[which];
		},
		_resetPagination: function(){
			var paginationInner = '',
				totalPage = this.get('totalPage') > 0 ? this.get('totalPage') : 1,
				currPage = (this.get('currentPage') <= totalPage && this.get('currentPage')) > 0 ? this.get('currentPage') : 1,
				preposePagesCount = this.get('preposePagesCount') >= 0 ? this.get('preposePagesCount') : 2,
				postposePagesCount = this.get('postposePagesCount') >= 0 ? this.get('postposePagesCount') : 1,
				firstPagesCount = this.get('firstPagesCount') >= 0 ? this.get('firstPagesCount') : 2,
				lastPagesCount = this.get('lastPagesCount') >= 0 ? this.get('lastPagesCount') : 0,
				offset;

			// currPage前的页码展示
			var prevpage = currPage > 1?currPage-1:1;
			paginationInner += '<a class="page page-trigger page-trigger-prev" page=' + prevpage  + '><i class="iconfont" title="左三角形">&#xF039;</i>上一页</a>';

			if (currPage <= firstPagesCount + preposePagesCount + 1) {
				for(var i=1; i<currPage; i++) {
					paginationInner += this._renderActivePage(i);
				}

			} else {
				for(var i=1; i<=firstPagesCount; i++) {
					paginationInner += this._renderActivePage(i);
				}
				paginationInner += '<span class="page-breaker">...</span>';
				for(var i=currPage-preposePagesCount; i<=currPage-1; i++) {
					paginationInner += this._renderActivePage(i);
				}
			}

			// currPage的页码展示
			paginationInner += '<a class="page page-item-ui page-active" page='+ currPage +'>' + currPage + '</a>';

			// currPage后的页码展示
			if (currPage >= totalPage - lastPagesCount - postposePagesCount) {
				offset = currPage + 1;
				for(var i=currPage+1; i<=totalPage; i++) {
					paginationInner += this._renderActivePage(i);
				}

			} else {
				for(var i=currPage+1; i<=currPage+postposePagesCount; i++) {
					paginationInner += this._renderActivePage(i);
				}
				paginationInner += '<a class="page-breaker">...</a>';
				for(var i=totalPage-lastPagesCount+1; i<=totalPage; i++) {
					paginationInner += this._renderActivePage(i);
				}
			}

			var houpage = currPage<totalPage ? currPage+1 : totalPage;
			paginationInner += '<a class="page page-trigger page-trigger-next" page='+ houpage +'>下一页 <i class="iconfont" title="右三角形">&#xF03A;</i></a>';

			// input submit
			paginationInner += '<span class="page-skip">共'+ totalPage +'页  </span><input id="page_input" type="text" class="text"> <a id="page_button" class="ui-paging-info ui-paging-goto" type="button">跳转</a>';

			this.El.html(paginationInner);
		},
		_renderActivePage: function(index) {
			return '<a class="page page-item-ui" page="' + index + '">' + index + '</a>';
		},
		destory: function(){
			var ele = this.El;
			this.El.remove();
			//this.undelegateEvents(ele);
			setEvents.undelegate(ele);
		},
		_switchToPage: function(page){
			//this.fnList.switchFn && this.fnList.switchFn();
			var self = this;
			if (self.has('switch')) {
				this.trigger('switch', {currentPage: page}, function(){
					self.curConfig.currentPage = page;
					self._resetPagination();
				});
			} else {
				self.curConfig.currentPage = page;
				self._resetPagination();
			}

		}		
	}

	observe.make(Pagination.prototype);

	K.Pagination = Pagination;

})(K);

(function(K){

	var Table = K.Table,
		Pagination = K.Pagination,
		observe = K.Observe;

	var ATTRS = {
		perPageNums: 50  // 每页的数量默认50条
	}

	var PaginationTable = function(options) {
		var options = $.extend(true, {}, ATTRS, options);
		this.initialize(options);
		this.currentPage = 1;
	}

	PaginationTable.prototype = {
		constructor: PaginationTable,
		initialize: function(options) {

			this.El = $('<div class="k-table-container"></div>');
			this.options = options;
			this.createTable(options);
			//this.render(options);
		},
		render: function() {
			var self = this,
				options = self.options,
				datas;

			function _createTable(data) {
				self.table.currentPage = 1;
				self.table.perPageNums = options.perPageNums || 0;
				self.table.setOptions({source: function(next) {
					next(data);
				}});
				self.table.run.call(self.table);
			}

			function _createPagination(options) {
				self.createPagination(options);
			}

			if ((datas = options.source) && $.type(datas) == 'array') {
				if (datas.length <= options.perPageNums) {
					_createTable(datas);
				} else {
					_createTable(datas.slice(0, options.perPageNums));
					_createPagination({totalPage: Math.ceil(datas.length / options.perPageNums)});
				}
			}

			if (datas  && $.type(datas) == 'function') {
				options.source.call(this, {currentPage: 1}, _createPagination, _createTable);
			}
		},
		orderByDesc: function(name) {
			var datas = this.options.source;
			if (datas.length) {
				for (var i=0, l=datas.length; i<l; i++) {
					var cp = datas[i], index = i;
					for (var j=1, n=datas.length; j<n; j++) {
						if (cp[name] < datas[j][name]) {
							datas[index] = datas[j];
							datas[j] = cp;
							index = j;
						}
					}
						
				}
			}
		},
		createTable: function(options) {
			var _this = this;
			var newOptions = {};
			$.each(options, function(key, val){
				if (key != 'source') {
					newOptions[key] = options[key];
				}
			})
			var table = new Table(newOptions);
			this.table = table;
			this.El.append(table.El);
			table.on('orderBy', function(locx){
				console.log(_this.options.source);
				_this.orderByDesc(_this.options.columnNameList[locx]);
				var currentPage = _this.pagination &&_this.pagination.curConfig.currentPage || 1,
					totalPage = _this.pagination &&_this.pagination.curConfig.totalPage || 1,
					options = _this.options;
				if (options.source && $.type(options.source) == 'function') {
					/*options.source(o, next, function(datas){
						self.table.refresh.call(self.table, datas);
					})*/
					throw new Error('has not write this function');
				}
				if (options.source && $.type(options.source) == 'array') {
					
					table.refresh.call(table, options.source.slice((currentPage-1)*options.perPageNums,
					currentPage*options.perPageNums > options.source.length ? options.source.length : currentPage*options.perPageNums));
				}
			})
		},
		createPagination: function(settings) {
			var pagination = new Pagination(settings),
				self = this,
				options,
				container = $('<div style="margin-top: 12px;" class="fn-clear"></div>');
			this.pagination = pagination;
			container.html(pagination.El);
			this.El.append(container);
			options = self.options;
			pagination.on('switch', function(o, next){
				/*self.trigger('switch', o, next, function(datas){
					self.table.refresh.call(self.table, datas);
				});*/
				//next();
				self.table.currentPage = o.currentPage;
				if (options.source && $.type(options.source) == 'function') {
					options.source(o, next, function(datas){
						self.table.refresh.call(self.table, datas);
					})
				}
				if (options.source && $.type(options.source) == 'array') {
					next();
					self.table.refresh.call(self.table, options.source.slice((o.currentPage-1)*options.perPageNums,
					o.currentPage*options.perPageNums > options.source.length ? options.source.length : o.currentPage*options.perPageNums));
				}
				
					
				//});
			});

			pagination.on('errorSwitch', function(o){
				self.trigger('errorSwitch', o);
			});
		},
		run: function() {
			this._setEvents();
			this.render();
		},
		setEvents: function() {
			this.events = Array.prototype.slice.call(arguments);
		},
		_setEvents: function() {
			this.table.setEvents.apply(this.table, this.events);

		}
	}

	observe.make(PaginationTable.prototype);

	K.PaginationTable = PaginationTable;

})(K);
	return K;
});