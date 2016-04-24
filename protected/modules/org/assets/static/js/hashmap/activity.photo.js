/**
 * 活动分组页面
 * @author      吴华
 * @company     成都哈希曼普科技有限公司
 * @site        http://www.hashmap.cn
 * @email       wuhua@hashmap.cn
 * @date        2015-05-02
 */
define(function (require, exports, module){ 

	var $ = require('$');
    
    var dialogUi = require('dialogUi');
    var server = require('server'); 
    //require('webuploader');  
    var actid = 0;    
    var wait_dialog = dialogUi.wait();
    var uploader = null;

    var loader = require('loader').setConf({'base':basePath+'/'});        
    //var wait_dialog = dialogUi.wait();
    //loader.setConf({'base':assetsUrl+'/'});  

    var uploader_num = 0;
    var uploaded_num = 0;
    var act_info = {act_b_time: "2014.04.01 00:00",act_title: "成都哈希曼普科技有限公司"};
    /*
{
    "title": "", //相册标题
    "id": 123, //相册id
    "start": 0, //初始显示的图片序号，默认0
    "data": [   //相册包含的图片，数组格式
        {
            "alt": "图片名",
            "pid": 666, //图片id
            "src": "", //原图地址
            "thumb": "" //缩略图地址
        }
    ]
}
    */

    $(function(){
        var jQuery = $;
        window.jQuery = $;
        window.$ = $; 
        try{
            uploader = new WebUploader.Uploader({
                swf: url+'/static/js/hashmap/webuploader-0.1.5/dist/Uploader.swf'
                //其他配置项
                ,pick:{
                    id:$('#Photo_up_btn').get(0)
                    ,innerHTML:" &nbsp; &nbsp;  &nbsp;  &nbsp; "
                }
                ,accept:{
                    title: 'Images',
                    extensions: 'jpg',
                    mimeTypes: 'image/jpeg'
                }
                ,formData:{actId:actid}
                ,method:'POST'
                ,dnd:'body'
                ,thumb:{
                    width: 142,
                    height: 142,

                    // 图片质量，只有type为`image/jpeg`的时候才有效。
                    quality: 70,

                    // 是否允许放大，如果想要生成小图的时候不失真，此选项应该设置为false.
                    allowMagnify: true,

                    // 是否允许裁剪。
                    crop: true,

                    // 为空的话则保留原有图片格式。
                    // 否则强制转换成指定的类型。
                    type: 'image/jpeg'
                }
                ,fileNumLimit: 1000
                ,server:'/user/userInfo/imgUp'
                ,fileVal:'img'
            });   

            var upload_btn = $(".upload");
            uploader.on('filesQueued',function(files){
                if(files.length>0)upload_btn.show();
                console.log(files); 


                for (var i = files.length - 1; i >= 0; i--) { 
                    uploader_num ++;
                    (function(file){
                       uploader.makeThumb(file,function(error,ret){
                            if(error){
                            }else{
                                showPhotoForList({img_url:ret,id:'pvm',file:file});
                            }        
                        }); 
                    })(files[i]); 
                };
            }); 
            uploader.on('error',function(type){
                /*
                Q_EXCEED_NUM_LIMIT 在设置了fileNumLimit且尝试给uploader添加的文件数量超出这个值时派送。
                Q_EXCEED_SIZE_LIMIT 在设置了Q_EXCEED_SIZE_LIMIT且尝试给uploader添加的文件总大小超出这个值时派送。
                Q_TYPE_DENIED 当文件类型不满足时触发。。
                */
                dialogUi.text(type);
            }); 
            uploader.on('uploadError',function(file,reason){
                console.log('uploadError');
                console.log(file);
                console.log(reason);
            }); 
            uploader.on('uploadProgress',function(file,percentage){
                /*console.log(file);
                console.log(percentage);*/
                $("#"+file.id+" .prog").text(  percentage * 100 );
            });

            uploader.on('uploadSuccess',function(file,ret){
                //上传成功后添加到活动相册 
                addPhotoToActivity(ret.body,function(){
                    showPhotoForList(ret.body,$("#"+file.id));
                    uploaded_num++;
                    if(uploaded_num == uploader_num){
                        window.location.reload();
                    }
                });
            });




            upload_btn.click(function(){
                wait_dialog.show();
                $("#Photo_up_btn").hide();
                $(".prog").text(0);
                uploader.upload();
            });          
        }catch(e){ 
            //加载 webuploader-error
            //console.log('webuploader-error');
            window.location.reload();
        }


        $(".img .closer").live('click',function(e){
            //console.log(e);
            wait_dialog.show();
            var parent = $(this).parents('.img');
            var img_id = parent.data('id')
            if(img_id =='pvm'){
                //预览图片删除
                if(uploader){//使用uploader上传的删除
                   // console.log();
                   uploader.removeFile(parent.data('file'),true);
                   uploader_num -- ;
                   parent.remove();
                   counter();
                   wait_dialog.hide();
                }

            }else{
                server.delAlbumImg({'actId':actid,'imgId':img_id},function(ret){
                    wait_dialog.hide();
                    if(ret.code=='0'){
                        parent.remove();
                        counter();
                        window.location.reload();
                    }else{
                        dialogUi.text(ret.msg);
                    }
                });
            }
            //e.bubbles = false;
            //e.cancelable = true;
            e.stopPropagation?e.stopPropagation():event.stopPropagation();

        });

        $(".img").live('click',function(){
            var img = $(this).data('img');
            //console.log('click');
            if(img){  
                showAlbumView($(this).index());
            }
        });
        
    });

    function counter(){
        $(".c3_4_3 span").text($(".c3_4_4 .img").size() );
    }
    
    function addPhotoToActivity(photo,succes){
        server.addAlbumImg({'actId':actid,'imgId':photo.id},function(ret){
            this.ret = ret;
            if(ret.code =='0'){
                if($.isFunction(succes)){
                    succes.call(this);
                }
            }
        });
    }

    //程序初始化方法
    exports.init = function(id){
        loader.load('static/js/hashmap/webuploader-0.1.5/dist/webuploader.js',function(){
            _init(id);
        });
    } 

    function _init(id){
        if(!WebUploader || !WebUploader.Uploader){
            window.location.reload();
        }
        wait_dialog.show();
        actid = id;

        //alert(actid);

        //获取到相册列表
        server.orgAlbumImgs({actId:actid},function(ret){
            //wait_dialog.hide();
            showPhotoForList(null);//清空显示列表
            var imgs = ret.body.imgs;
            act_info.act_title = ret.body.act_title;
            act_info.act_b_time = (ret.body.act_b_time+"").slice(0,10);;
 
            for (var i = imgs.length - 1; i >= 0; i--) {
                showPhotoForList( imgs[i]);                
            };
            wait_dialog.hide();
            createAlbumView();
        });     
    }
 
    function showPhotoForList(photo,replace){
    	if(photo ==null){
    		$(".c3_4_4 .img").remove();
    	}else if(photo.id ){
            var img = '';
            if(photo.id=='pvm'){
                img = $('<div class="img pvm-thumb" id="'+photo.file.id+'" data-id="'+photo.id+'"><img src="'+photo.img_url+'" style="width: 100%;height: 100%;"><div class="prog">还没上传</div><span class="closer">×</span></div>');
                img.data('file', photo.file);
            }else{
                img = $('<div class="img" data-id="'+photo.id+'" data-img="'+photo.img_url+'"><img src="'+photo.img_url+'@124w_124h_1e_0c_50Q_1x.jpg" style="width: 100%;height: 100%;"><span class="closer">×</span></div>');
            }
    		if(!replace){
                $(".c3_4_4 .clear").before(img);
            }else{
                replace.replaceWith(img);
            }
    		
    	}
        counter();
    }


    //相册代码开始
    function createAlbumView(){
        var albumView = $("#album_view");
        if(albumView.size()<1){
            albumView = $('<div style="display: none;" id="album_view"><span class="closer">×</span><div class="album_prev">&nbsp;</div><div class="prev_box"><img ><div class="act-info-box"><span class="act_admin_logo" style="background-image: url(\''+userLogo+'\');"></span><span class="act_b_time">'+act_info.act_b_time+'</span><span class="act_title">'+act_info.act_title+'</span></div></div><div class="album_next">&nbsp;</div><div class="album_prev album_prev_small"></div><div class="prev_list"><div class="slider"></div></div><div class="album_next album_next_small"></div></div>');

            $("body").append(albumView);
            //albumView.hide();
            var prev_list =  $(".prev_list .slider",albumView);
            $("div.img").each(function(i){
                var clone = $(this).clone();
                //console.log(i);
                clone.removeClass('img');
                clone.addClass('prev_img');
                clone.addClass('prev_img_'+i);
                $(".closer",clone).remove();
                prev_list.append(clone);
                clone.data('i',i);
            });
            prev_list.width( 114*($(".prev_img").size() ) );
            $(".prev_box img",albumView).load(function(){ 
                //$(".prev_box img").fadeOut(0);
                $(this).fadeTo(150,0.1).fadeTo(500,1);
            }); 
            $(".prev_img",albumView).click(function(){
                /*
                $(".selected").removeClass('selected');
                $(this).addClass('selected');
                $(".prev_box img").attr('src',$(this).data('img'));
                */
                if(!showPrevPhoto.runing){
                    createAlbumView.imageIndex = $(this).index();
                    $(".selected").removeClass('selected');
                    $(this).addClass('selected');
                    showPrevPhoto($(this).data('img'));
                    //console.log(createAlbumView.imageIndex);
                }

            });
            $(".album_prev",albumView).click(function(){
                if(!showPrevPhoto.runing){
                    createAlbumView.imageIndex = createAlbumView.imageIndex<=0?$(".prev_img").size()-1:createAlbumView.imageIndex-1;
                    showAlbumPhoto();
                }
            });

            $(".album_next",albumView).click(function(){
                if(!showPrevPhoto.runing){
                    createAlbumView.imageIndex = createAlbumView.imageIndex>=$(".prev_img").size()-1?0:createAlbumView.imageIndex+1;
                    showAlbumPhoto();
                    
                }
            });

            $(".closer",albumView).click(closeAlbumView);

            //$(".prev_img:eq(0)",albumView).click();
            showAlbumPhoto();

            var ws = {w:0,h:0}; //window size    
            function _win_resize(e){
                ws.w = $(window).width();
                ws.h = $(window).height();
                albumView.width(ws.w-20);
                albumView.height(ws.h-20);

                $(".prev_box",albumView).width(ws.w-220);
                $(".prev_box",albumView).height(ws.h-134);
                $(".album_prev,.album_next",albumView).height(ws.h-134);
                $(".album_prev_small,.album_next_small",albumView).height(100);
                $(".prev_list",albumView).width(ws.w-120);
                //$(".prev_list",albumView).width(ws.h-220);
            }
            $(window).resize(_win_resize);
            _win_resize();
        }
        return albumView;

    }
    createAlbumView.imageIndex = 0;
    function showAlbumPhoto(){
        if(createAlbumView.imageIndex>=0 && createAlbumView.imageIndex<$(".prev_img").size()){
            $(".prev_img:eq("+createAlbumView.imageIndex+")").click();
        }
    }

    function showAlbumView(i){
        wait_dialog.show();
        createAlbumView.imageIndex = i;
        createAlbumView().fadeIn();
        showAlbumPhoto();

    } 

    function closeAlbumView(){
        wait_dialog.hide();
        createAlbumView().fadeOut();
    }

    function showPrevPhoto(src){
        if(!showPrevPhoto.runing){
            showPrevPhoto.runing = true
            var img = new Image();
            img.onload=function(){
                var bs = {w:0,h:0};//box_size
                bs.w = $(".prev_box").width();
                bs.h = $(".prev_box").height();
                var is = {w:0,h:0};//img size
                is.w = img.width;
                is.h = img.height;
                var w_bili,h_bili;
                if(is.w<bs.w  && is.h < bs.h){
                    w_bili =1;
                    h_bili =1;
                }else{
                    w_bili = bs.w/is.w;
                    h_bili = bs.h/is.h;
                }
                

                var bili =  w_bili>h_bili?h_bili:w_bili/*Math.min(w_bili,h_bili)*/;
                var ss = {width:bili*is.w,height:bili*is.h};// set size
                var io = {left:(bs.w-ss.width)/2,top:(bs.h-ss.height)/2};
                // console.log(w_bili);
                // console.log(h_bili); 
                // console.log(is,bs,ss,io);
                //获取到现在的尺寸
                $(".prev_box img").css(ss).css(io).attr('src',src);
                showPrevPhoto.runing = false;
                movePrevImg();
                setPrevTextPoi(io,ss);
            }
            img.onerror=function(){
                console.log('can not load: ' + src);
                showPrevPhoto(src);
            }
            img.src = src;
            // console.log(' load: ' + src);
                
        }
        
    }
    showPrevPhoto.runing = false;

    function movePrevImg(){
        var left = createAlbumView.imageIndex*104+104;
        var w = $(".prev_list").width();
        var w_2 = w/2 ;
        if(left<w_2){
            $(".prev_list .slider").animate({'margin-left': '0px'});
        }else if(left>w_2){
            var l = (left-w_2);
            console.log(l);
            if((l+w)>=$(".slider").width() ){
                $(".prev_list .slider").animate({'margin-left': '-'+($(".slider").width()-w)+'px'});
            }else{
                $(".prev_list .slider").animate({'margin-left': '-'+l+'px'});
            }
            
        }

    }

    function setPrevTextPoi(image_poi,image_size){
        image_poi.left = image_poi.left;
        image_poi.top = image_poi.top + image_size.height - $(".act-info-box").height();
        $(".act-info-box").width(image_size.width).css(image_poi);
    }
    //相册代码结束

});