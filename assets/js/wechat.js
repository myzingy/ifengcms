/*
 * author vking.wang
 */
(function($){
	$('<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>').appendTo('head');
	
	$.extend({
		
  		wechat:function(config){
  			
  			var shareConfig = $.extend({
  				debug:false,
				title: '迷你嘟嘟', // 分享标题
				desc: '迷你嘟嘟 社区电商', // 分享描述
			    link: location.href, // 分享链接
			    imgUrl: 'http://www.minidudu.com/pc/themes/res/images/logo.png', // 分享图标
			    success: function (res) { 
			        // 用户确认分享后执行的回调函数
			        console.log(res);
			    },
			    cancel: function (res) { 
			        // 用户取消分享后执行的回调函数
			        console.log(res);
			    }
			}, config);
  			$.ajax({
				url:'http://cms.wisheli.com/index.php/api/act/getWechatJS',
				dataType:'jsonp',
				success:function(wechat_conf){
					wx.config({
					    debug: shareConfig.debug, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
					    appId: wechat_conf.jsapi_sign.appid,
					    timestamp: wechat_conf.jsapi_sign.timestamp,
					    nonceStr: wechat_conf.jsapi_sign.noncestr,
					    signature: wechat_conf.jsapi_sign.signature,
					    jsApiList: [
					    	'onMenuShareTimeline','onMenuShareAppMessage'
					    	,'onMenuShareQQ','onMenuShareWeibo'
					    	,'scanQRCode'
					    ] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
					});
					wx.ready(function () {
						//过滤openid，添加fkid
						if(!wechat_conf.openid){
							match=location.href.match(/openid=([^&#]+)/);
							if(match){
								wechat_conf.openid=match[1];
							}
						}
						shareConfig.link=shareConfig.link.replace(/openid=([^&#]+)/g,'');
						shareConfig.link=shareConfig.link.replace(/fkid=([^&#]+)/g,'');
						if(wechat_conf.openid){
							shareConfig.link+=(shareConfig.link.indexOf('?')>-1?'&':'?')+'fkid='+wechat_conf.openid;
						}
						wx.onMenuShareTimeline(shareConfig);
						wx.onMenuShareAppMessage(shareConfig);
						wx.onMenuShareQQ(shareConfig);
						wx.onMenuShareWeibo(shareConfig);
					});
				}
			});
		}
  	});
})(jQuery);