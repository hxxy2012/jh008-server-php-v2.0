/**
 * 对话框
 * @author      吴华
 * @company     成都哈希曼普科技有限公司
 * @site        http://www.hashmap.cn
 * @email       wuhua@hashmap.cn
 * @date        2015-05-14 
 */
define(function (require, exports, module){ 
	var $ = require('$'); 
	window.$ = window.jQuery = $;
    var dialogUi = require('dialogUi');
    var wait_dialog = dialogUi.wait();
    wait_dialog.hide();



	//开始
	;(function(w,d,undefined){
		w.HM || (w.HM ={});
		w.HM.dialoer={
			//遮罩层
			mask:wait_dialog
			,confirm_ele: null
			,confirm_callback: function(){} 
			,timeout: 250
			,showMask:function(){
				$(".hm-mask").fadeIn(this.timeout);
				return this;
			}
			,hideMask:function(){
				$(".hm-mask").fadeOut(this.timeout); 
				return this;
			}
			//确认框
			,confirm:function(text,callback ){
				var that = this;
				this.showMask();
				this.confirm_ele = $("#hm-dialog-confirm");
				if(this.confirm_ele.size() < 1 ){
					$('body').append('<div id="hm-dialog-confirm" class="hm-dialog-confirm"><div class="hm-dialog-warp"><span class="hm-closer"></span><div class="hm-content"></div><a class="hm-ok" href="javascript: void(0);">确定</a></div></div>'); 
					this.confirm_ele = $("#hm-dialog-confirm");	
					$(".hm-closer",this.confirm_ele).click(function(){
						that.hideMask();
						if(typeof that.confirm_callback =='function'){
							that.confirm_callback.call(that,'cancel');
						}
						that.confirm_ele.fadeOut(this.timeout);	
					});
					$(".hm-ok",this.confirm_ele).click(function(){
						that.hideMask();
						if(typeof that.confirm_callback =='function'){
							that.confirm_callback.call(that,'ok');
						}
						that.confirm_ele.fadeOut(this.timeout);	

					});
				}
				this.confirm_callback = callback; 
				$(".hm-content",this.confirm_ele).html(text);	
				this.confirm_ele.fadeIn(this.timeout);			
				return this;
			}

		};


		//编写css
		if($("#hm-dialog-style").size() < 1){
			$("head").append('<style id="hm-dialog-style"  type="text/css">.hm-mask{position:fixed;*position:absolute;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,.8);*opacity:.8;*background:#000;display:none}.hm-dialog-confirm{display:none;background:url("'+basePath+'/static/images/index/confirm-bg.png") no-repeat center;height:220px;width:380px;position:absolute;top:50%;left:50%;margin-left:-190px;margin-top:-110px;border-radius:3px;z-index:10000;overflow:hidden}.hm-dialog-confirm .hm-dialog-warp{width:100%;height:100%;position:relative}.hm-dialog-confirm .hm-closer{position:absolute;display:block;cursor:pointer;right:0;top:0;background:0;height:25px;width:25px;margin:16px}.hm-dialog-confirm .hm-content{position:absolute;top:105px;left:0;text-align:center;width:90%;margin:5%;color:#666;height:auto;font-size:14px;max-height:30px;line-height:15px;word-break:break-all;overflow:hidden}.hm-dialog-confirm .hm-ok{position:absolute;background:0;bottom:0;left:0;width:100%;height:47px;text-indent:-1000px;overflow:hidden}</style>');

			$("body").append('<div class="hm-mask" id="hm-mask"></div>');

		}
 
	})(window,document,undefined);
	module.exports = window.HM.dialoer;
});