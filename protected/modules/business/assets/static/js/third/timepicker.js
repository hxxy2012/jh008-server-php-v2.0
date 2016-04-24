define(function(require, exports, module){



/**
 * timepicker
 * @params {Object} 
 		-defaultTime 
 */
$.fn.timePicker = function(options) {
	return new TimePicker(this, options);
}

var TimePicker = function(target, options) {
	this.initialize(target, options);
}

var ATTRS = {
	time: '00:00:00'
}

TimePicker.prototype.initialize = function(target, options) {
	this.target = target;
	var options = $.extend(true, {}, ATTRS, options);
	this.createUi().setContent(options.time);
	this.setLocation(target);
}

TimePicker.prototype.createUi = function() {
	var el = $('<div></div>'), self = this;
	el.css({
		padding: '6px',
		backgroundColor: '#3399cc',
		fontSize: '12px',
		border: '1px solid #c1c1c1',
		position: 'absolute',
		zIndex: 99,
		display: 'none'
	});
	self.El = el;

	function createTimeItem(text, topNum, id) {
		var select = $('<select id="'+ id +'"></select>');
		for (var i=0; i<= topNum; i++) {
			var h = i < 10 ? '0' + i : i;
			select.append('<option value=\"' + h + '\">' + h + '</option>');
		}
		el.append($('<span>' + text + '</span>'));
		el.append(select);
	}
	function createButton() {
		var button = $('<a class="ui-button ui-button-sdarkred ml10">确定</a>');
		button.click(function() {
			var hour = el.find('#hour').val(),
				minute = el.find('#minute').val(),
				second = '00';
				self.target.val(hour + ':' + minute + ':' + second + '');
				el.hide();
		});
 		el.append(button);
	}
	createTimeItem('\u65f6', 23, 'hour');
	createTimeItem('\u5206', 59, 'minute');
	//createTimeItem('\u79d2', 59, 'second');
	createButton();
	
	this.target.focus(function(){
		el.show();
	});

	/*this.target.blur(function(){
		el.hide();
	});*/	
	
	$('body').append(el);
	return self;	
}

// 12:34:33
TimePicker.prototype.setContent = function(time) {
	var hour, minute, socond, time, timeArr, El;
	El = this.El;
	time = time || '00:00:00';
	timeArr = time.split(':');
	hour = timeArr[0],
	minute = timeArr[1],
	//second = timeArr[2];
	El.find('#hour').val(Number(hour));
	El.find('#minute').val(Number(minute));
	//El.find('#second').val(Number(second));
}

TimePicker.prototype.setLocation = function(target) {
	var loc = target.offset();
	this.El.css({left: loc.left, top: loc.top + target.outerHeight()});
}

return TimePicker;
})