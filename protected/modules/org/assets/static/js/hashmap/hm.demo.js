/**
 * 测试js
 * @author      吴华
 * @company     成都哈希曼普科技有限公司
 * @site        http://www.hashmap.cn
 * @email       wuhua@hashmap.cn
 * @date        2015-05-14 
 */
define(function (require, exports, module){ 
	var $ = require('$'); 
	window.$ = window.jQuery = $;
    var dialoger = require('dialoger'); 
    //wait_dialog.show();
    console.log(dialoger);


    $(".show1").click(function(){
    	dialoger.confirm('你是动物吗?',function(status){
    		//status 返回的是一个状态...(ok,cancel)
    		//ok表示用户点击的是确定按钮
    		//cancel 表示用户点击的是关闭按钮
	    	//成功以后回调方法
	    	alert(status);
	    });
    });


    $(".show2").click(function(){
    	dialoger.confirm('你不是动物吗?',function(status){
	    	//成功以后回调方法
	    	alert(status);
	    });
    });

 
});