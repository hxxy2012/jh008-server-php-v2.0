 
jQuery(document).ready(function(){
    
  loed();
})      
        function loed()
        {                          
             //报名结束时间 倒计时             
            if(diff_time!='')
            {
                //计时器
                var tick_time = 0;
                //定时器对象
                var tick_timer = 0;
             
                function TickTimer()
                {
                    if(diff_time <tick_time ){
                       // document.getElementById('sign_button').removeAttribute('disabled');
                        clearInterval(tick_timer);
                    }else{
                        //document.getElementById('sign_button').setAttribute('disabled','true');//按钮失效
                        var diff = diff_time - tick_time;
                        var h = Math.floor(diff/3600).toString();
                        var m = Math.floor( (diff-3600*h)/60 );
                        var s = diff-3600*h-60*m;
                        //int转成string
                         h = h.toString();
                         m = m.toString();
                         s = s.toString();
                        // alert(h.length);
                        if(h.length>=3)
                        {
                             document.getElementById('h1').innerHTML =h.substr(0, 1);
                        }
                        else
                        {
                            document.getElementById('h1').innerHTML =0;
                        }
                        if(h.length>=3)
                        {
                             document.getElementById('h2').innerHTML =h.substr(1, 1);
                        }
                        else if(h.length>=2)
                        {
                            document.getElementById('h2').innerHTML =h.substr(0, 1);
                        }
                        else
                        {
                            document.getElementById('h2').innerHTML =0;
                        }
                        if(h.length>=3)
                        {
                             document.getElementById('h3').innerHTML =h.substr(2, 1);
                        }
                        else if(h.length>=2)
                        {
                            document.getElementById('h3').innerHTML =h.substr(1, 1);
                        }
                        else
                        {
                            document.getElementById('h3').innerHTML =h;
                        }
                        //分钟
                        if(m.length>=2)
                        {
                            document.getElementById('m1').innerHTML =m.substr(0, 1);
                        }
                        else
                        {
                            document.getElementById('m1').innerHTML =0;
                        }
                        //分钟
                        if(m.length>=2)
                        {
                            document.getElementById('m2').innerHTML =m.substr(1, 1);
                        }
                        else
                        {
                            document.getElementById('m2').innerHTML =m;
                        }
                        //秒
                        if(s.length>=2)
                        {
                            document.getElementById('s1').innerHTML =s.substr(0, 1);
                        }
                        else
                        {
                            document.getElementById('s1').innerHTML =0;
                        }
                        //秒
                        if(s.length>=2)
                        {
                            document.getElementById('s2').innerHTML =s.substr(1, 1);
                        }
                        else
                        {
                            document.getElementById('s2').innerHTML =s;
                        }
                    }
                    tick_time++;
                } 
                    tick_timer = setInterval(TickTimer,1000);
             }
        }     
 //手机点击
 var down_url = 'http://app.jhla.com.cn/act/appInfo/lastVersionApk';//安卓包
	var open_url = {android:'gather://splash',apple:'gather://'};//安卓启动
    var weixin_url='http://fusion.qq.com/cgi-bin/qzapps/unified_jump?appid=11244952&from=mqq';//微信未安装
    
	var check_timeout = 600;
	(function(win,doc,undefined) 
    {
		win.HM || (win.HM={});
		if (!win.HM.browser) {
			win.HM.browser = {
				get:function(){
					var u = win.navigator?win.navigator.userAgent:'';
					if(u){
						return {//移动终端浏览器版本信息
			                trident: u.indexOf('Trident') > -1, //IE内核
			                presto: u.indexOf('Presto') > -1, //opera内核
			                webkit: u.indexOf('AppleWebKit') > -1, //苹果、谷歌内核
			                gecko: u.indexOf('Gecko') > -1 && u.indexOf('KHTML') == -1, 
			                mobile: !!u.match(/AppleWebKit.*Mobile.*/)||!!u.match(/AppleWebKit/), //是否为移动终端
			                ios: !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/), //ios终端
			                android: u.indexOf('Android') > -1 || u.indexOf('Linux') > -1, //android终端或者uc浏览器
			                iphone: u.indexOf('iPhone') > -1 || u.indexOf('Mac') > -1, //是否为iPhone或者QQHD浏览器
			                ipad: u.indexOf('iPad') > -1, //是否iPad
			                webapp: u.indexOf('Safari') == -1 , //是否web应该程序，没有头部与底部
			                weixin: (u.toLowerCase() ).match(/MicroMessenger/i) == 'micromessenger' //判断是否为微信
			            };
					}
					return false;
				}
				,is:function(type){
					type = (type+'').toLowerCase();
					return this.get()[type];
				}
				,os:function(){

				}
				,language:function(){
					return (navigator.browserLanguage || navigator.language).toLowerCase();
				}
			};
		};
        
		HM.$ = function(id){
			return document.getElementById(id);
		}
	})(window,document,undefined);
	

	window.onload = function(){
		var open_app = HM.$('open_app');
		open_app.onclick = function(){
			checkinstalled(function(status)
            {
				if(!status){
					//判断是否是微信
					if(HM.browser.is('weixin')){
						HM.$('wfob-mask').style.display = 'block';
						HM.$('wfob-mask').style.opacity = '1';
					}
                    else
                    {
						//跳转到下载连接
						window.location.href = down_url;
					}
				}
                else
                {
					window.location.href = weixin_url;
				}
			});
		}  
        
        
		var open_app = HM.$('open_app1');
		open_app.onclick = function(){
			checkinstalled(function(status)
            {
				if(!status){
					//判断是否是微信
					if(HM.browser.is('weixin')){
						HM.$('wfob-mask').style.display = 'block';
						HM.$('wfob-mask').style.opacity = '1';
					}
                    else
                    {
						//跳转到下载连接
						window.location.href = down_url;
					}
				}
                else
                {
					window.location.href = weixin_url;
				}
			});
		} 
        
        
        
      		var open_app = HM.$('open_app2');
		    open_app.onclick = function(){
			checkinstalled(function(status)
            {
				if(!status){
					//判断是否是微信
					if(HM.browser.is('weixin')){
						HM.$('wfob-mask').style.display = 'block';
						HM.$('wfob-mask').style.opacity = '1';
					}
                    else
                    {
						//跳转到下载连接
						window.location.href = down_url;
					}
				}
                else
                {
					window.location.href = weixin_url;
				}
			});
		} 
        
        
        
      		var open_app = HM.$('open_app3');
	     	open_app.onclick = function(){
			checkinstalled(function(status)
            {
				if(!status){
					//判断是否是微信
					if(HM.browser.is('weixin')){
						HM.$('wfob-mask').style.display = 'block';
						HM.$('wfob-mask').style.opacity = '1';
					}
                    else
                    {
						//跳转到下载连接
						window.location.href = down_url;
					}
				}
                else
                {
					window.location.href = weixin_url;
				}
			});
           }
            
 		    var open_app = HM.$('open_app4');
	     	open_app.onclick = function(){
			checkinstalled(function(status)
            {
				if(!status){
					//判断是否是微信
					if(HM.browser.is('weixin')){
						HM.$('wfob-mask').style.display = 'block';
						HM.$('wfob-mask').style.opacity = '1';
					}
                    else
                    {
						//跳转到下载连接
						window.location.href = down_url;
					}
				}
                else
                {
					window.location.href = weixin_url;
				}
			});
		} 
	};

	var t = 0;
	function checkinstalled(callback){

		window.onblur = function(){return false;};
		clearTimeout(t);
		var startTime = Date.now();
		var ua = window.navigator?window.navigator.userAgent:'';
		var startTime = Date.now();

		var ifr = document.createElement('iframe');


		ifr.src = ua.indexOf('os') > 0 ? open_url.apple : open_url.android ;
		ifr.style.display = 'none';
		document.body.appendChild(ifr);

		t = setTimeout(function() {
			var endTime = Date.now();
			if (!startTime || (endTime - startTime) < (check_timeout + 200) ) { 
				//window.location = config.download_url;
				if(typeof callback=='function'){
					callback.call(this,false); 
					window.onblur = function(){return false;};
					clearTimeout(t);
				}
			} else {

			}
		}, check_timeout);

		window.onblur = function() {
			clearTimeout(t);
			if(typeof callback=='function'){
				callback.call(this,true)
			}
		} 
	}    
        
        
        
      //$('.opn_app').click(function(){ alert('');} )
        
        