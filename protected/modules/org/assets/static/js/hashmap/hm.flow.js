/**
 * 瀑布流插件
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



	//开始
	;(function(w,d,undefined){ 
		w.HM || (w.HM = {});
		w.HM.flow = {
			element: null  //判断对象
			,callback: function(){}  //回调函数
			,inited: false   //初始化状态
			,prevscroll: {left:0,top:0}  //上一次滚动状态
			,nowscroll: {left:0,top:0}   //当前滚动状态
			,diffscroll: {left:0,top:0}  //当前次和上一次差异
			,flow:function(ele,callback){
				this.setElement(ele);
				this.setCallback(callback);
				this.init();
				//程序初始化完成以后默认调用一次滚动.

				this.call(); 
				return this;
			}
			,setEleMini:function(){
				this.element.css('min-height',$(w).height()+'px');
			}
			,setElement:function(ele){
				this.element = ele;
				return this;	
			}
			,setCallback:function(callback){
				this.callback = callback;
				return this;
			}
			,call:function(a1,a2,a3,a4,a5){
				if(typeof this.callback =='function'){
					this.callback.call(this,a1,a2,a3,a4,a5);
				}
			}
			,init:function(){
				var that = this;
				if(!this.inited){
					this.inited = true;
					var mousewheel = d.all?"mousewheel":"DOMMouseScroll";
					var cache_timer = 0;	
					$(window).scroll(function(e){ 
						clearTimeout(cache_timer);
						cache_timer = setTimeout((function(e_o) {
							var e = e_o;
							return function(){
								 if(that.getScrollDir(e) =='down'){
								 	//向下滚动
								 	//判断距离
								 	//console.log( that.getWinMM() );
								 	//console.log( that.getEleBottom() );
								 	//判断元素底部时候在可视区域内
								 	var winMm = that.getWinMM();
								 	var eb = that.getEleBottom();
								 	if(winMm.top<eb  && winMm.bottom>=eb){
								 		//console.log('范围以内');
								 		//调用回调函数
								 		that.call();
								 	}	
								 }
							}
						})(e), 150);
					});			 
				}	
				return this;
			}
			,scrollInfo:function(e){
				if(e.type =='scroll'){
					this.nowscroll.left = $(w).scrollLeft();
					this.nowscroll.top = $(w).scrollTop();

					//记录差异
					this.diffScrollSet();

					//保存当前次
					this.prevscroll.left = $(w).scrollLeft();
					this.prevscroll.top = $(w).scrollTop();

				}
			}
			,diffScrollSet:function(){
				this.diffscroll.left = this.nowscroll.left - this.prevscroll.left;
				this.diffscroll.top = this.nowscroll.top - this.prevscroll.top;
			}
			//获取到滚动的方向
			,getScrollDir:function(e){
				if(e) this.scrollInfo(e);
				if(this.diffscroll.left == 0){//上下滚动
					if(this.diffscroll.top>0){
						return 'down';//向下
					}else{
						return 'up';//向上
					}
				}else if(this.diffscroll.top ==0){//左右滚动
					if(this.diffscroll.left>0){
						return 'left';//向下
					}else{
						return 'right';//向上
					}
				}
			}
			//window 的可视区域
			,getWinMM:function(){
				return {
					top: $(w).scrollTop(),
					bottom: $(w).scrollTop() + $(w).height(),
					left: $(w).width(),
					right: $(w).scrollLeft()+$(w).width()
				}
			}
			//	
			,getEleBottom:function(){
				return (this.element.offset()).top	+ this.element.height();
			}
		};

	})(window,document,undefined);
	module.exports = window.HM.flow;
});