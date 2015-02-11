$(function(){
	var base_url=location.href.replace(/mnew\/.*/,'');
	var base_cgi=base_url+'index.php/';
	$('#login_form').submit(function(e){
		e.preventDefault();
		var data=$(this).find('input').fieldValue(true);
		var nickname=$.trim(data[0]);
		var p1=$.trim(data[1]);
		var p2=$.trim(data[2]);
		if(!nickname){
			tip('昵称不能为空');
			return false;
		}
		if(p1 && (p1.length<6 || p2.length<6)){
			tip('密码长度不能少于六位');
			return false;
		}
		$.post(base_cgi+'api/act/modifyUser',{nickname:nickname,old_password:p1,password:p2},function(json){
			if(json.status==0){
				setTimeout(function(){tip('操作成功');},0);
				return false;
			}else{
				setTimeout(function(){tip(json.error,'red');},0);
				return false;
			}
		});
		return false;
	});
	//获取用户状态
	$.ajax({
		url:base_cgi+'api/act/getUserInfo',
		dataType:'json',
		async:false,
		success:function(json){
			$('input[name="nickname"]').val(json.nickname);
		}
	});
	initMMenu();
});