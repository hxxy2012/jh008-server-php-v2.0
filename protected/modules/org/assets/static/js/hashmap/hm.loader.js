/**
 * 资源加载器
 * @author      吴华
 * @company     成都哈希曼普科技有限公司
 * @site        http://www.hashmap.cn
 * @email       wuhua@hashmap.cn
 * @date        2015-04-30 
 */
define(function (require, exports, module){ 
	var $ = require('$'); 
	window.$ = window.jQuery = $;
	;(function(win,doc,undefined){
		win.HM ?'': (win.HM = {});
		var Finder = {
			id:function (id) {
				return doc.getElementById(id);
			}
			,tag:function(tag){
				return doc.getElementsByTagName(tag);
			}
			,inArray:function(find,array){
				if(array){
					for(var key in array){
						if(array[key]==find){
							return true;
						}
					}
				}
				return false;
			}
			,getAjax:function(){
				var xmlhttp = null;
				if (window.XMLHttpRequest)
				  {// code for IE7+, Firefox, Chrome, Opera, Safari
				  xmlhttp=new XMLHttpRequest();
				}else{// code for IE6, IE5
				  	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
				}
				return xmlhttp;
			}
		}; 
		function _Loader(){
			//获取添加元素的容器(默认使用body,head)
			if (doc.body) {
				this.container = doc.body;
			}else if(Finder.tag('head') && Finder.tag('head')[0]){
				this.container = Finder.tag('head')[0];
			}

		}
		_Loader.pt = _Loader.prototype;

		//程序的配置
		_Loader.pt.config = {
			base: '/'  //连接的基础路径
			,alias:{}  //别名引入
			,incId: 6595  //加载编号.
		}; 
		//配置设置
		_Loader.pt.setConf = function(config){
			for(var key in config){
				this.config[key] = config[key];
			}
			return this;
		}

		//添加元素
		_Loader.pt.append = function(ele,callback){
			if(this.container && this.container.appendChild){
				var that = this;
				ele.id = this.getIncId();
				ele.onload = function(){
					if(typeof callback =='function'){
						callback.call(ele,that,'success');
					}
				}
				ele.onerror = function(){
					//console.log(ele);
					if(typeof callback =='function'){
						callback.call(ele,that,'error');
					}
				}
				this.container.appendChild(ele);
			}
			return this;
		}
		//加载元素
		_Loader.pt.load = function (url,callback) {
			
			//console.log(this.getType(url));
			/*console.log();
			*/
			this[this.getType(url)](url,callback);
			return this;
		}
		//判断加载元素类型
		_Loader.pt.getType = function (url) {
			if((/\.css$/).test(url)){
				return 'css';
			}else if((/\.js$/).test(url)){
				return 'script';
			}else if((/\.(jpg|png|gif|bmp|jpeg)$/).test(url)){
				return 'img';
			}else{
				return 'ajax';
			}
			//return undefined;
		}
		//格式化地址
		_Loader.pt.formatUrl=function(url){
			//如果是以http开头.或者//开头.或者 /开头直接返回
			if((/^(http|\/\/|\/)/).test(url)){
				return url;
			}else if(Finder.inArray(url,this.config.alias)){
				return this.config.alias[url];
			}else{
				return this.config.base + url;
			}
		}

		//加载css
		_Loader.pt.css = function(url,callback){
			var ele = this.cretEle('link');
			ele.rel = 'stylesheet';
			ele.href = this.formatUrl( url );
			this.append(ele,callback);
			return this;
		};
		//加载 js
		_Loader.pt.script = function(url,callback){
			var ele = this.cretEle('script');
			ele.type = 'text/javascript';
			ele.src = this.formatUrl( url );
			this.append(ele,callback);
			return this;
		};
		//加载 img
		_Loader.pt.img = function(url,callback){
			var ele = this.cretEle('img'); 
			ele.src = this.formatUrl( url );
			this.append(ele,callback);
			return this;
		};

		//加载 ajax
		_Loader.pt.ajax = function(url,callback){
			var ajax = Finder.getAjax();
			if(ajax){
				var that = this;
				ajax.onreadystatechange=function(){
					if (xmlhttp.readyState==4 && xmlhttp.status==200){
				    	if(typeof callback =='function'){
							callback.call(ajax,that,'success');
						}
				    }else if(xmlhttp.readyState==4){
				    	if(typeof callback =='function'){
							callback.call(ajax,that,'error');
						}
				    }
				}
				ajax.open('GET',url,true);
				ajax.send();
			}
		};
		_Loader.pt.cretEle=function(name){ 
			return doc.createElement(name);
		};
		_Loader.pt.getIncId=function(){
			return 'hm_loader_'+(++this.config.incId);
		}



		win.HM.Loader = _Loader;
	})(window,document);
	//console.log(window.HM.Loader);
	module.exports = new window.HM.Loader();
});