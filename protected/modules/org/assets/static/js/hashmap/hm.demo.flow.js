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
    var flow = require('flow'); 
     

    console.log(flow); 
    flow.flow($(".aa"),function(){
        //console.log(this);
        //this.element.append("阿莱克斯打飞机那可是接电话那是肯定就好啦说的话");
    });
 
});