var disobj=function(obj){
	if(typeof obj=='undefined') return 'undefined';
	if(typeof obj!='object') return obj;
	var str='';
	//console.log(typeof obj,obj);
	for(var i in obj){
		
		/*
		if(typeof obj[i]!='object'){
			str+="<p>"+i+':'+(obj[i].toString())+"</p>";
		}else{
			str+=disobj(obj[i]);
		}
		*/
		if(typeof obj[i]!='object'){
			str+="<p>"+i+':'+(obj[i])+"</p>";
		}else{
			str+="<p>"+i+':'+"</p>";
			for(var j in obj[i]){
				str+="<p>====>"+j+'::'+(obj[i][j])+"</p>";
			}
		}

	}
	return str;
};
var debug=function(label,obj){
	var html='<fieldset style="max-height: 300px;overflow-y: auto;"><legend>'+label+'</legend>'+disobj(obj)+'<fieldset>';
	$(html).appendTo('#debug');
};
$(function(){
	var alertx=function(msg){
		$('.modal-body','#alertModal').html('<p>'+msg+'</p>');
		$('#alertModalLink').trigger('click');
	};
	var loading=function(flag,msg){
		if(flag){
			msg=typeof msg=='undefined'?'Loading':msg;
			$('.modal-body','#loadModal').html('<p><img src="'+base_url+'assets/images/loading.gif">&nbsp;&nbsp;'+msg+'</p>');
			$('#loadModalLink').trigger('click');
		}else{
			console.log($('.modal-footer button','#loadModal'));
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
					return;
				}else{
					alertx('数据已经提交，感谢你的参与！');
					$('form').resetForm();
				}
			}
		});
		return false;
	});
	
	var $exp = $('.kiss_upload_photo .photo .exp');
    var $uploadBtn = $('.kiss_upload_photo .photo .upload_btn');
    var $loading = $('.kiss_upload_photo .photo .kiss_loading');
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
    var uploader = WebUploader.create({
        server: Kiss.sApi, // 文件接收服务端
        pick: '.kiss_upload_photo .photo .upload_btn',
        fileVal: 'pic1',
        auto: true,
        resize: false,
        fileNumLimit: 1,
        sendAsBinary: true,
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
    uploader.on('uploadBeforeSend', function(object, data,headers) {
    	debug('uploadBeforeSend object',object);
    	debug('uploadBeforeSend data',data);
    	debug('uploadBeforeSend headers',headers);
        $exp.hide();
        $uploadBtn.hide();
        $loading.show();
    });
    uploader.on('startUpload', function(file, ret) {
    	debug('开始上传',this);
        $exp.hide();
        $uploadBtn.hide();
        $loading.show();
    });
    uploader.on('uploadComplete', function(file, ret) {
    	debug('uploadComplete file',file);
    	debug('uploadComplete ret',ret);
        $loading.hide();
        $exp.show();
        $uploadBtn.show();
    });
    uploader.on('uploadSuccess', function(file, ret) {
    	debug('uploadSuccess file',file);
    	debug('uploadSuccess ret',ret);
        if (ret.errno == '0') {
            processUploadSuccess(ret.imgurl);
        } else {
            alertx('上传失败' + (ret.errmsg ? '：' + ret.errmsg : ''));
        }
    });
    uploader.on('uploadError', function(file, ret) {
    	alertx('上传失败，请换一张照片');
    });
    
    var $preview = $('.kiss_upload_success .preview');
    var $previewImg = $('.kiss_upload_success .preview img');
    function processUploadSuccess(icon) {
        var img = new Image();
        img.src = icon;
        img.addEventListener('load', function() {
            var cs = Kiss.calculatePhoto($preview.width(), img.width, img.height);
            $preview.css({
                'height': cs.ch + 'px',
                'max-height': cs.ch + 'px'
            });
            $previewImg.css({ 'margin-top': cs.it + 'px' }).attr('src', icon);
        }, false);
        img.addEventListener('error', function() {
            $previewImg.attr('src', icon);
        }, false);

        showSuccess(icon);
    }

    function showUpload() {
        $('.kiss_upload_success').hide();
        $('.kiss_upload_photo').show();
    }
    function showSuccess(url) {
        $('.kiss_upload_photo .kiss_btn_back').hide();
        $('.kiss_upload_photo').hide();
        $('.kiss_upload_success').show();
        window.location.hash = '1';
        $('input[type="file"]').attr('type','text').val(url);
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
});