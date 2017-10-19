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
		var REDIRECT_URI = 'http://www.duduxy.com/wechat/php/getWechatAuth.php'
		var APPID='wxc23b7df9375cc300';
		var BACK_URl = encodeURIComponent(location.href);
		var url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='+APPID+'&redirect_uri='+REDIRECT_URI+'&response_type=code&scope=snsapi_userinfo&state='+BACK_URl+'#wechat_redirect';
		location.href=url;
	}
})();