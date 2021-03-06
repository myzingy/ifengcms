var alertx=function(msg,callback){
	$('.modal-body','#alertModal').html('<p>'+msg+'</p>');
	$('#alertModalLink').trigger('click');
	if(typeof callback == 'function'){
		$('#alertModal button').one('click',callback);
	}
};
var isIE=!!window.ActiveXObject,$imginput;
var isMobile=!!navigator.userAgent.match(/AppleWebKit.*Mobile.*/)||!!navigator.userAgent.match(/AppleWebKit/);
$(function(){
	
	$imginput=$('input[type="file"]','.kiss_upload_photo');
	var loading=function(flag,msg){
		if(flag){
			msg=typeof msg=='undefined'?'Loading':msg;
			$('.modal-body','#loadModal').html('<p><img src="'+base_url+'assets/images/loading.gif">&nbsp;&nbsp;'+msg+'</p>');
			$('#loadModalLink').trigger('click');
		}else{
			//console.log($('.modal-footer button','#loadModal'));
			$('.modal-footer button','#loadModal').trigger('click');
		}
	};
	$('form').submit(function(e){
		e.preventDefault();
		$('form').ajaxSubmit({
			dataType:'json',
			beforeSend:function(){
				loading(true,'数据提交中。。。');
				$('form button').attr('disabled','disabled');
			},
			complete:function(){
				loading(false);
			},
			success:function(json){
				if(json.status!=0){
					alertx(json.error);
					$('form button').removeAttr('disabled');
					if(typeof pagecallbackerror!='undefined'){
						pagecallbackerror(json);
						return;
					}
					return;
				}else{
					$('form').resetForm();
					$('form button').removeAttr('disabled');
					if(typeof pagecallback!='undefined'){
						pagecallback(json);
						return;
					}
					alertx('数据已经提交，感谢你的参与！');
				}
			}
		});
		return false;
	});
	//location.hash=$(document).height();
	var $exp = $('.kiss_upload_photo .photo .exp');
	if($exp.length>0){
		function getFlashVersion(){
            var version;
    
            try {
                version = navigator.plugins[ 'Shockwave Flash' ];
                version = version.description;
            } catch ( ex ) {
                try {
                    version = new ActiveXObject('ShockwaveFlash.ShockwaveFlash')
                            .GetVariable('$version');
                } catch ( ex2 ) {
                    version = '0.0';
                }
            }
            version = version.match( /\d+/g );
            return parseFloat( version[ 0 ] + '.' + version[ 1 ], 10 );
        }
		var $uploadBtn = $('.kiss_upload_photo .photo .upload_btn');
	    var $loading = $('.kiss_upload_photo .photo .kiss_loading');
	   	var $preview = $('.kiss_upload_success .preview');
	    var $previewImg = $('.kiss_upload_success .preview img');
		var Kiss={
			User:{
				get:function(){}
			}
			,sApi:base_url+'index.php/upload/fieldfile'
			,calculatePhoto:function(t,n,r){
				var e=$('.kiss_upload_success .preview');
				var i=e.innerWidth,s=e.innerHeight,o=720,u=1280;i>o&&(i=o,s=u),t>o&&(t=o);
				var a=t*s/i,f=a-t/i*160,l=300*i/s;f=f<l?l:f;
				var c=t/n,h=Math.ceil(n*c),p=Math.ceil(r*c),d={};
				return p<f?(d.cw=t,d.ch=p,d.iw=h,d.ih=p,d.it=0):(d.cw=t,d.ch=f,d.iw=h,d.ih=p,d.it=-(p-f)/2),d;
			}
		};
		var startUpload=function(file, ret) {
	        $exp.hide();
	        $uploadBtn.hide();
	        $loading.show();
	    };
	    var uploadComplete=function(file, ret) {
	        $loading.hide();
	        $exp.show();
	        $uploadBtn.show();
	    };
	    var uploadSuccess=function(file, ret) {
	    	//console.log(file, ret);
	    	if(typeof ret=='string'){
	    		eval('ret='+ret);
	    	}
	        if (ret.errno == '0') {
	            processUploadSuccess(ret.imgurl);
	        } else {
	            alertx('上传失败' + (ret.errmsg ? '：' + ret.errmsg : ''));
	        }
	    };
	    var uploadError=function(file, ret) {
	    	//console.log(file,ret);
	    	alertx('上传失败，请换一张照片');
	    };
	    var uploadProgress=function(){
	    	$('#buttonPlaceholderDiv').css({'z-index':-99,'height':'0px'});
	    	$loading.html('<img src="'+base_url+'assets/images/loading__.gif" width="100%"/>');
	    	$loading.css({
	    		'width':'50%',
	    		'height':'50%',
	    		'left':'30%',
	    		'top':'30%',
	    		'background': 'url()',
	    		'display':'block',
	    		'-webkit-animation': 'initial',
	    		'animation': 'initial'
	    	});
	    };
	    if(isMobile){
	    	//console.log('使用html5初始化==>');
	    	$input=$uploadBtn.find('input');
	    	$input.prependTo($('.kiss_upload_photo .photo'));
	    	$input.css({
	    		position: 'absolute',
				width: '100%',
				height: '100%',
				'z-index': 99,
				left: 0,
				top: 0,
				opacity: 0
	    	});
	    	$input.change(function(){
				var data = new FormData();
				jQuery.each($input[0].files, function(i, file) {
				    data.append('file', file);
				});
				startUpload();
				var  base_url=location.href.replace(/fields(.*)/,'');
				$.ajax({
					type:'POST'
					,url:base_url+'api/act/uploadImage'
					,data:data
					,contentType: false
		    		,processData: false
		    		,dataType:'json'
					,success:function(json){
						if(json.status==0){
							console.log(json);
							processUploadSuccess(json.src);
						}else{
							alertx('上传失败，请换一张照片');
							return false;
						}
					}
				});
			});
	    }else{
	    	// if(!isIE){
				//console.log('使用WebUploader初始化==>');
				var uploader = WebUploader.create({
			    	swf: base_url+'assets/js/webuploader0.1.5/Uploader.swf',
			        server: Kiss.sApi, // 文件接收服务端
			        pick: '.kiss_upload_photo .photo .upload_btn',
			        fileVal: 'pic1',
			        auto: true,
			        resize: false,
			        fileNumLimit: 1,
			        //sendAsBinary: true,
			        formData: {
			            apiname: 'uploadPhoto',
			            uid: Kiss.User.get('uid')
			        },
			        accept: {
			            title: 'Images',
			            extensions: 'gif,jpg,jpeg,bmp,png',
			            mimeTypes: 'image/*'
			        }
			    });
			    uploader.on('startUpload', function(file, ret) {
			        startUpload(file, ret);
			    });
			    uploader.on('uploadComplete', function(file, ret) {
			        uploadComplete(file, ret);
			    });
			    uploader.on('uploadSuccess', function(file, ret) {
			        uploadSuccess(file, ret);
			    });
			    uploader.on('uploadError', function(file, ret) {
			    	uploadError(file, ret);
			    });
			// }else{
			// 	console.log('使用swfupload初始化==>');
			// 	//$('.kiss_upload_photo').css({width:150,height:30});
			// 	var but_height=45;
			// 	var up_width=$('.kiss_upload_photo').width()-12;
			// 	var up_height=$('.kiss_upload_photo').height()-40;
			// 	$('<div id="buttonPlaceholderDiv" style="padding-top:'+((up_height-but_height)/2)+'px;"><div id="buttonPlaceholder"></div></div>').appendTo('.kiss_upload_photo .photo');
			// 	//使用swfupload初始化
			// 	var settings={
			// 		flash_url : base_url+"assets/js/swfupload/swfupload.swf",
			// 		file_post_name:"pic1",
					
			// 		upload_url: Kiss.sApi,
			// 		post_params: {"name" : "test"},
					
			// 		// File Upload Settings
			// 		file_size_limit : '10MB',  	// 1000MB
			// 		file_types : '*.jpg;*.jpeg;*.gif;*.png',    //*.*
			// 		file_types_description : "Web Image Files",//"Web Image Files",
			// 		file_upload_limit : 1,//20
			// 		file_queue_limit : 0,
					
			// 		//file_queue_error_handler : fileQueueError,
			// 		file_dialog_complete_handler : function(){
			// 			//选择好文件后提交
			// 			this.startUpload();
			// 		},
			// 		//file_queued_handler : fileQueued,
			// 		swfupload_load_failed_handler : swfUploadLoadFailed, //swf 加载失败
			// 		swfupload_loaded_handler : swfUploadLoaded,  //swf 加载完成
					
			// 		upload_start_handler: startUpload,
			// 		upload_progress_handler : uploadProgress,
			// 		upload_error_handler : uploadError,
			// 		upload_success_handler : uploadSuccess,
			// 		upload_complete_handler : uploadComplete,
		
			// 		// Button Settings
			// 		//button_image_url : base_url+"assets/js/swfupload/select-files-min.png",
			// 		//button_image_url : "http://p1.qhimg.com/t0166c44746ba2e85d1.png",
			// 		button_placeholder_id : "buttonPlaceholder",
			// 		//button_placeholder:$('.kiss_upload_photo .photo .upload_btn')[0],
			// 		button_width: up_width,
			// 		button_height: up_height-but_height,
			// 		button_text : '<span>&nbsp;</span><img width="100%"  src="'+base_url+'assets/js/swfupload/select-files-min.png"/>',
			// 		custom_settings : {
			// 			photoContainer_Id   : "photoContainer",  	//图片的容器id
			// 			btnUpload_ID		: "btnUpload",          //上传按钮
			// 			insertAll_Id		: "insertAll", 			//全部插入
			// 			clearAll_Id			: "clearAll", 			//全部清空
			// 		   	errorMsg_Id			: "errorMsg",  			//错误信息
			// 		  	errorMsg_fadeOutTime: 2000  				//错误信息谈出的时间
			// 		},
			// 		// Debug Settings
			// 		debug: false,   //是否显示调试窗口
			// 		auto_upload:true
			// 	};
			// 	setTimeout(function (){swfu = new SWFUpload(settings);},0);
			// 	//swf 加载失败
			// 	function swfUploadLoadFailed() {
			// 		//clearTimeout(this.customSettings.loadingTimeout);
			// 		var message='<div id="divAlternateContent" style="background-color: #FFFF66; text-align:center;">'
			// 				+'<a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" target="_blank"><font color="red">请安装或者升级您的Flash插件!</font></a></div>';
			// 	}
				
			// 	//swf 加载 完成
			// 	function swfUploadLoaded() {
			// 		//var self = this;
			// 		//clearTimeout(this.customSettings.loadingTimeout);
			// 		$("#"+this.customSettings.btnUpload_ID).click(function (){swfu.startUpload()});
			// 	}
			// }
	    }
	   	
	    

	    function processUploadSuccess(icon) {
	        var img = new Image();
	        img.src = icon;
	         if(img.addEventListener){//主流浏览器  
		         	img.addEventListener('load', function() {
			            var cs = Kiss.calculatePhoto($preview.width(), img.width, img.height);
			            $preview.css({
			                'height': cs.ch + 'px',
			                'max-height': cs.ch + 'px'
			            });
			            setTimeout(function(){
			            	$previewImg.css({ 'margin-top': cs.it + 'px' }).attr('src', icon);
			            },0);
			            showSuccess(icon);
			        }, false);
			        img.addEventListener('error', function() {
			        	setTimeout(function(){
			            	$previewImg.attr('src', icon);
			            },0);
			            showSuccess(icon);
			        }, false);  
		    }else{//IE
		        img.attachEvent('onload', function() {
		            var cs = Kiss.calculatePhoto($preview.width(), img.width, img.height);
		            $preview.css({
		                'height': cs.ch + 'px',
		                'max-height': cs.ch + 'px'
		            });
		            setTimeout(function(){
		            	$previewImg.css({ 'margin-top': cs.it + 'px' }).attr('src', icon);
		            },0);
		            showSuccess(icon);
		        }, false);
		        img.attachEvent('onerror', function() {
		        	setTimeout(function(){
		            	$previewImg.attr('src', icon);
		            },0);
		            showSuccess(icon);
		        }, false);       
		    }
	        
	    }
	
	    function showUpload() {
	        $('.kiss_upload_success').hide();
	        $('.kiss_upload_photo').show();
	    }
	    function showSuccess(url) {
	        $('.kiss_upload_photo .kiss_btn_back').hide();
	        $('.kiss_upload_photo').hide();
	        $('.kiss_upload_success').show();
	        
	        $('<input type="text" value="'+url+'" name="'+$imginput.attr('name')+'"/>').appendTo('.kiss_upload_photo');
	        $('input[type="file"]','.kiss_upload_photo').remove();
	    }
	
	    function resetUploader() {
	        uploader.reset();
	        showUpload();
	    }
	
	    $('.kiss_upload_success .kiss_btn_reupload').on('click', function(e) {
	        e.preventDefault();
	        resetUploader();
	    });
	
	    $('.kiss_upload_success .kiss_btn_share').on('click', function(e) {
	        e.preventDefault();
	        Kiss.showShare();
	    });
	}
    
});