$(function(){
	var base_url=location.href.replace(/mnew\/.*/,'');
	var base_cgi=base_url+'index.php/';
	$('#login_form').submit(function(e){
		e.preventDefault();
		var data=$(this).find('input').fieldValue(true);
		if(!/1[0-9]{10}/.test(data[0])){
			tip('请输入正确的手机号');
			return false;
		}
		if(!/[0-9]{6}/.test(data[1])){
			tip('请输入正确的验证码');
			return false;
		}
		data[2]=$.trim(data[2]);
		if(data[2].length<6){
			tip('密码长度不能少于六位');
			return false;
		}
		$.post(base_cgi+'api/act/register',{username:data[0],code:data[1],password:data[2]},function(json){
			if(json.status==0){
				location.href=base_cgi+'oauth/login';
			}else{
				setTimeout(function(){tip(json.error,'red');},0);
				return false;
			}
		});
		return false;
	});
	var codeInterval='',codeIntervalNum=60;
	$('.validate_btn').click(function(e){
		var that=$(this);
		var phone=$.trim($('#user_phone').val());
		if(!/1[0-9]{10}/.test(phone)){
			tip('请输入正确的手机号');
			return false;
		}
		$.post(base_cgi+'api/act/sendsms',{type:'register',username:phone},function(json){
			if(json.status!=0){
				setTimeout(function(){tip(json.error,'red');},0);
			}else{
				that.attr('disabled','disabled').addClass('disabled');
				if(!codeInterval){
					clearInterval(codeInterval);
				}
				codeInterval=setInterval(function(){
					that.attr('disabled',true).addClass('disabled');
					that.html('重新获取('+(codeIntervalNum--)+')');
					if(codeIntervalNum<1){
						that.html('重新获取').removeAttr('disabled').removeClass('disabled');
						codeIntervalNum=60;
						clearInterval(codeInterval);
						codeInterval='';
					}
				},1000);
			}
		});
		return false;
	});
});
