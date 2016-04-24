$(function(){
	DialogUi = K.dialogUi;

  jQuery.validator.addMethod("isPhone", function(value, element, param) {
      var length = value.length;
      var myreg = /^(((13[0-9]{1})|(15[0-9]{1})|(18[0-9]{1}))+\d{8})$/; 
      if(!myreg.test(value)) { 
        return false; 
      } 
      return this.optional(element) || param;   
  }, $.validator.format("请输入有效的手机号码！"));

  $('#assnForm').validate({
      rules: {
         // name: "required",
         // city: "required",
         // address: "required",
          contact: "required",
          contactPhone: "isPhone",
          infor: "required"
      },
      messages: {
          //name: "团名不能为空",
          //city: "城市不能为空",
         // address: "地址不能为空",
          contact: "联系人不能为空",
          isPhone: '请输入正确的电话号码',
          infor: "团队简介不能为空"
      },
      keyup: false,
      submitHandler: function(form) {
         // var oldPass = $.trim($('#oldPass').val()),
          //    newPass = $.trim($('#newPass').val()),
          //    reNewPass = $.trim($('#reNewPass').val());
          // ajax 
      }
  });



	$('#saveProfile').click(function(e) {
		/*DialogUi.confirm({
			text: '确定要更新社团资料',
			cancelText: '返回修改',
			okText: '确认更新',
			okCallback: function() {

			},
			cancelCallback: function() {

			}
		})
		e.preventDefault();	*/
	})


function setCrop() {
    // Create variables (in this scope) to hold the API and image size
    var jcrop_api,
        boundx,
        boundy,

        // Grab some information about the preview pane
        $preview = $('.preview-box'),
        $pcnt = $('.preview-box-con'),
        $pimg = $('.preview-box-con img'),

        xsize = $pcnt.width(),
        ysize = $pcnt.height();
    
    $('#target').Jcrop({
      onChange: updatePreview,
      onSelect: updatePreview,
      aspectRatio: xsize / ysize
    },function(){
      // Use the API to get the real image size
      var bounds = this.getBounds();
      boundx = bounds[0];
      boundy = bounds[1];
      // Store the API in the jcrop_api variable
      jcrop_api = this;

      // Move the preview into the jcrop container for css positioning
      //$preview.appendTo(jcrop_api.ui.holder);
    });

    function updatePreview(c)
    {
      if (parseInt(c.w) > 0)
      {
        var rx = xsize / c.w;
        var ry = ysize / c.h;

        $pimg.css({
          width: Math.round(rx * boundx) + 'px',
          height: Math.round(ry * boundy) + 'px',
          marginLeft: '-' + Math.round(rx * c.x) + 'px',
          marginTop: '-' + Math.round(ry * c.y) + 'px'
        });
      }
    };
}



    var img = $('#target'),
    	logoCon = $('.logo-upload');
    img.load(function(){
    	var w = img.width(),
    		h = img.height(),
    		bl = w / h,
    		w1 = 470,
    		h1 = 340,
    		bl1 = w1/h1, w0, h0;
    	if (w > w1 || h > h1) {
    		if (bl >= bl1) {
    			w0 = 470;
    			h0 = Math.floor(w0 / bl);
    		} else {
    			h0 = 340;
    			w0 = bl * h0;
    		}
    	}
    	img.width(w0);
    	img.height(h0);
    	setCrop();
    })

    $('#selectLogo').click(function() {
    	img.attr('src', 'http://p6.qhimg.com/dmt/490_350_/t013fcbe776faa1d90c.jpg');
    	$(this).hide();
    })

})