define(function(require, exports, module){

	var $ = require('$'),
		server = require('server'),
		static = require('static'),
		Uploader = require('upload'),
		dialogUi = require('dialogUi'),
		K = require('K'),
		main = require('main');

	var tip = main.tip,
		wait = main.wait;

	var page = {
		init: function() {
			page.cityId = main.getCityId();
			var activeEdit = require('activeEdit')();
			K.Observe.make(activeEdit);
			activeEdit.init(function(el){ 
				$('#mainPanel').html(el);
			})
			/*activeEdit.on('save', function() {
				saveCallback && saveCallback();
			})*/
			activeEdit.setCityId(page.cityId);
			activeEdit.on('add', function() {
				// $('#searchBtn').trigger('click');
			})
			activeEdit.load('create');
		}
	}

	page.init();

})