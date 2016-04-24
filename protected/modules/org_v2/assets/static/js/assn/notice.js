(function(){
	var Class = K.Class,
		BaseView = K.util.BaseView,
		pageManager = K.util.pageManager,
		Observe = K.Observe;

	var User = Class.create(BaseView, {
		initialize: function(data) {
			this.data = data;
			this.render();
			this.setEvents();
		},
		events: {
			'click': 'select'
		},
		eventsHandler: {
			select: function(e) {
				this.trigger('_select', this.data);
				e.stopPropagation();
			}
		},
		render: function() {
			if (!this.data) return false;
			this.El = $('<li class="member-item"><a href="javascript:;" class="member-item-link">'+ this.data.name +'</a></li>');
		}
	})

	Observe.make(User.prototype);


	var Group = Class.create(BaseView, {
		initialize: function(data) {
			this.data = data;
			this.render();
			this.setEvents();
		},
		downIcon: '<i class="togicon icon iconfont"></i>',
		rightIcon: '<i class="togicon icon iconfont"></i>',
		events: {
			'click': 'toggle',
			'click .addicon': 'select'
		},
		eventsHandler: {
			toggle: function(e) {
				var _this = this,
					memberNavEl = _this.El.find('.member-nav');
				if (memberNavEl.css('display') == 'none') {
					_this.El.find('.togicon').replaceWith(_this.downIcon);
				} else {
					_this.El.find('.togicon').replaceWith(_this.rightIcon);
				}
				memberNavEl.toggle();
			},
			select: function(e) {
				this.trigger('_select', this.data);
				e.stopPropagation();
			}
		},
		render: function() {
			var name = this.data.name;
			var groupName = name == '' ? '未分组' : name;
			this.El = $(	'<li class="nav-item">' + 
								'<a class="item-link" href="javascript:;"><i class="togicon icon iconfont"></i>'+ groupName +'<i class="addicon icon iconfont"></i></a>' +
                            	'<ul class="member-nav"></ul>' +
                            '</li>');
		},
		append: function(user) {
			this.El.find('.member-nav').append(user.El);
		}
	})
	
	Observe.make(Group.prototype);
	

	var UserList = Class.create({
		initialize: function(users, groups) {
			this.groupList = {};
			this.groups = groups;
			this.groups.push('');
			this.userList = [];
			this.users = users;
			this.El = $('<ul class="group-nav"></ul>');
			this.render();
		},
		render: function() {
			var _this = this;
			_this.renderGroup();
			if (this.users.length) {
				$.each(this.users, function(index, userData) {
					_this.renderUser(userData);
				})
			}
		},
		renderGroup: function() {
			var _this = this;
			$.each(_this.groups, function(index, groupName) {
				if (!_this.groupList[groupName]) {
					var group = new Group({name: groupName});
					group.on('_select', function(groupData) {
						_this.trigger('_select', {type: 'group', data: groupData});
					})
					_this.El.append(group.El);
					_this.groupList[groupName] = group;
				}
			})
		},
		renderUser: function(userData) {
			var user = new User(userData),
				_this = this;
			user.on('_select', function(userData){
				_this.trigger('_select', {type: 'user', data: userData});
			})
			this.userList.push(user);
			if(this.groupList[user.data.group]) {
				this.groupList[user.data.group].append(user);
			}
		},
		selectAll: function() {
			this.trigger('_select', {type: 'all'});
		}
	})

	Observe.make(UserList.prototype);

	var SelUserManager = (function() {
		var userList,
			users,
			searchList = [],
			selectUserCache = {},
			selNumTextEl = $('#selNumText'),
			searchEl = $('#search'),
			delSearchEl = $('#delSearch'),
			searchNavEl = $('#searchNav'),
			delSearch = $('#delSearch'),
			hasSelNavEl = $('#hasSelNav');

		hasSelNavEl.on('click', '.delicon', function(e) {
			var target = $(e.target).parent(),
				id = target.attr('id');
			target.remove();
			removeUser(id);
		})

		searchEl.keyup(function(e){
			var target = $(e.target),
				value = target.val();
			if (value) {
				clearSearchUser();
				var reg = new RegExp('^' + value + '\\s*');
				pageManager.show('searchList');
				$.each(users, function(i, user) {
					if (reg.test(user.name)) {
						searchList.push(user);
						renderSearchUser(user);
					}
				})
			}
		})

		searchNavEl.on('click', 'a', function(e) {
			var target = $(e.target),
				id = target.attr('id');
			for (var i = 0; i < searchList.length; i++) {
				if (searchList[i].id == id) {
					selectUser(searchList[i]);
				}
			}
		})

		delSearch.click(function(){
			searchEl.val('');
			clearSearchUser();
			pageManager.show('groupList');
		})

		$('.checkbox-wrap').click(function(){
			var selAll = $('#selAll');
			if (!selAll[0].checked) {
				userList.selectAll();
			} else {
				removeAll();
			}
		})

		function clearSearchUser() {
			searchList = [];
			searchNavEl.html('');
		}

		function renderSearchUser(userData) {
			var el = '<li class="member-item">' +
						'<a href="javascript:;" class="member-item-link" id="'+ userData.id +'">'+ userData.name +'</a>' +
					 '</li>';
			searchNavEl.append(el);
		}

		function refreshSelectNumsTip() {
			var selNums = 0;
			for (var key in selectUserCache) {
				selNums ++;
			}
			selNumTextEl.text(selNums + '/' + users.length);
		}

		function removeUser(userId) {
			if (selectUserCache[userId]) {
				delete selectUserCache[userId];
			}
			refreshSelectNumsTip();
		}

		function renderUser(userData) {
			var el = '<li class="member-item">' +
						'<a href="javascript:;" class="member-item-link" id="'+ userData.id +'">'+ userData.name +'<i class="delicon icon iconfont"></i></a>' +
					 '</li>';
			hasSelNavEl.append(el);
			refreshSelectNumsTip();
		}

		function removeAll() {
			selectUserCache = {};
			hasSelNavEl.html('');
			refreshSelectNumsTip();
		}

		function filter(userData) {
			if (!selectUserCache[userData.id]) {
				selectUserCache[userData.id] = userData;
				return true;
			} else {
				return false;
			}
		}

		function selectUser(userData) {
			if (filter(userData)) {
				renderUser(userData);
			}
		}

		function selectGroup(groupData) {
			$.each(users, function(index, user) {
				if (user.group == groupData.name) {
					selectUser(user);
				}
			})
		}

		function selectAll() {
			$.each(users, function(index, user) {
				selectUser(user);
			})
		}

		function _getSelectIds() {
			var result = [];
			for (var key in selectUserCache) {
				result.push(selectUserCache[key].id);
			}
			return result;
		}

		function _init() {
			// ajax
			users = [
				{id: 1, name: '张三', group: 'A'}, 
				{id: 2, name: '张三1', group: 'B'}, 
				{id: 3, name: '张三2', group: 'C'}, 
				{id: 4, name: '张三3', group: 'D'}, 
				{id: 5, name: '张三4', group: 'A'}, 
				{id: 6, name: '张三5', group: 'A'}, 
				{id: 7, name: '张三6', group: 'A'}, 
				{id: 8, name: '张三7', group: 'B'}, 
				{id: 9, name: '张三8', group: 'B'}, 
				{id: 10, name: '张三9', group: ''}, 
				{id: 11, name: '张三10', group: ''}, 
				{id: 12, name: '张三11', group: ''}
			];
			userList = new UserList(users, ['A', 'B', 'C', 'D']);
			userList.on('_select', function(result) {
				if (!result) return;
				if (result.type == 'user') {
					selectUser(result.data);
				} else if (result.type == 'group') {
					selectGroup(result.data);
				} else if (result.type = 'all') {
					selectAll();
				}
			})
			pageManager.render('groupList', userList.El);
		}
		return {
			init: _init,
			getSelectIds: _getSelectIds
		}
	})();

	$('#sendBtn').click(function(){
		var ids = SelUserManager.getSelectIds();
		console.log(ids);
	})

	var page = {
		initialize: function() {
			var groupNavConEl = $('#groupNavCon');
			pageManager.add({name: 'groupList', el: $('#groupList'), parent: groupNavConEl});
			pageManager.add({name: 'searchList', el: $('#searchList'), parent: groupNavConEl});
			SelUserManager.init();
		}
	}

	page.initialize();

})()