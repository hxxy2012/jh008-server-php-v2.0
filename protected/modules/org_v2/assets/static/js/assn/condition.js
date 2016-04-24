(function(){
	var Class = K.Class,
		util = K.util,
		dialogUi = K.dialogUi;

	var conditionsManager = (function() {
		var conditions = [];
		// init

		var uiInforLinks = $('#conditions').find('.ui-infor-link');
		uiInforLinks.each(function(i) {
			conditions.push(uiInforLinks.eq(i).text());
		})

		function _add(value) {
			var flag = false;
			$.each(conditions, function(index) {
				if (conditions[index] == value) {
					flag = true;
				}
			})
			if (flag) return '';
			conditions.push(value);
			return '<div class="ui-infor">' +
                    '<a href="javascript:;" class="ui-infor-link">'+ value +'</a>' +
                    '<a href="javascript:;" class="badge del-btn"><i class="icon iconfont"></i></a>' +
                '</div>';
		}

		function _remove(value) {
			for (var i = conditions.length-1; i>=0; i--) {
				if (conditions[i] == value) {
					conditions.splice(i, 1);
				}
			}
		}

		return {
			add: _add,
			get: function() {
				return conditions;
			}
		}
	})()


	var infors = $('#conditions .ui-infor');
	infors.on('click', '.del-btn', function(e) {
		var delegateTarget = $(e.delegateTarget);
		delegateTarget.remove();
		var val = delegateTarget.siblings('ui-infor-link').text();
		conditionsManager.remove(val);
	})

	$('#addCondition').click(function(){
		var value = $.trim(conditionInput.val());
		if (!value) {
			dialogUi.tip('#conditionInput', '审核条件不能为空');
			//layer.tips('不能为空', '#conditionInput', {tips: 3});
		} else {
			var aresult = conditionsManager.add(value);
			if (!aresult) {
				dialogUi.tip('#conditionInput', '该审核条件已设置');
			} else {
				$('#conditions').append(aresult);
				conditionInput.val('');				
			}
		}
	})	

	var conditionInput = $('#conditionInput');

/*	var ConditionItem = Class.create(util.BaseView, {
		init: function() {
			console.log(sssss);
		},
		events: {
			'click: #'
		},
		render: function() {
			console.log(765756);
		}
	})

	new ConditionItem({aa:34, d:555});*/


	$('#preview').click(function(e){
		var result = '', conditions = conditionsManager.get();
		if (conditions.length) {
			$.each(conditions, function(i, condition) {
				var dom = '<span class="condition-d-tip">'+ condition +'</span>' + '<textarea class="condition-d-ta"></textarea>';
				result += dom;
			})
		}

		dialogUi.open({
			title: false,
			content: 	'<div class="phone-con">' +
							'<div class="phone-main"><div class="p15">' + 
								(result ? result : '') +
							'</div></div>' +
						'</div>',
			btn: false
		});
	})


	var page = {
		initialize: function(){

		}
	}

	page.initialize();

})()