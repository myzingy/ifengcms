(function(){
	var getOpenID=function(){
		var cookie=document.cookie;
		
		var match=cookie.match(/WeChatWeiLan=([^;]+)/);
		if(match){
			var data=JSON.parse(decodeURIComponent(match[1]));
			return data.openid;
		}else{
			match=location.href.match(/openid=([^&#]+)/);
			if(match){
				return match[1];
			}else{
				return '';
			}
		}
	};
	PUBLIC__WECHAT_OPENID=getOpenID();
	if(!PUBLIC__WECHAT_OPENID){
		location.href='http://cms.wisheli.com/index.php/api/act/getWechatAuth?scope=snsapi_base&redirect_uri='
			+encodeURI(location.href);
	}
})();