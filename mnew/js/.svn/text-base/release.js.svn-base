$(function(){
	var base_url=location.href.replace(/mnew\/.*/,'');
	var base_cgi=base_url+'index.php/';
	var max_upload_num=9;
	$('#login_form').submit(function(e){
		e.preventDefault();
		var title=$.trim($('#title').val());
		var content=$.trim($('#content').val());
		if(!title){tip('请填写标题','red'); return false;}
		if(!content){tip('请填写内容','red'); return false;}
		var option={
			url:base_cgi+'api/act/release',
			success:function(json){
				if(json.status==0){
					location.href='news.html';
				}else{
					setTimeout(function(){tip(json.error,'red');},0);
					$('#content')[0].focus();
					return false;
				}
			}
		};
		$('#login_form').ajaxSubmit(option);
		return false;
	});
	//上传图片
	$('input[type="file"]','#release').change(function(){
		if($('input[name="hideimg\[\]"]').length>=max_upload_num){
			console.log($('input[name="hideimg\[\]"]','#release').length);
			tip('最多只能上传'+max_upload_num+'张图片','red');
			return false;
		}
		var data = new FormData();
		jQuery.each($('input[type="file"]','#release')[0].files, function(i, file) {
		    data.append('file', file);
		});
		$.ajax({
			type:'POST'
			,url:base_cgi+'api/act/uploadImage'
			,data:data
			,contentType: false
    		,processData: false
			,success:function(json){
				$('#content')[0].focus();
				if(json.status==0){
					var html=$('#upload_display_tpl').html();
					for(var i in json){
						var reg= new RegExp('{'+i+'(\|[^}]+)?}','g');
						html=html.replace(reg,function($1,$2){
							if(typeof $2!='undefined'){
								//substr
								return json[i].substr(0,200);
							}
							return json[i];
						});
						
					}
					$(html).appendTo('#upload_display');
				}else{
					setTimeout(function(){tip(json.error,'red');},0);
					return false;
				}
			}
		});
	});
	initMMenu();
});
