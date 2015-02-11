$(function(){
	var base_url=location.href.replace(/mnew\/.*/,'');
	var base_cgi=base_url+'index.php/';
	$('#login_form').submit(function(e){
		e.preventDefault();
		var data=$(this).find('input').fieldValue(true);
		if(!$.trim(data[0]) || !$.trim(data[1])){
			tip('请输入手机号和密码');
			return false;
		}
		$.post(base_cgi+'api/act/login',{username:data[0],password:data[1]},function(json){
			if(json.status==0){
				location.href=json.referer;
			}else{
				setTimeout(function(){tip(json.error,'red');},0);
				return false;
			}
		});
		return false;
	});
});