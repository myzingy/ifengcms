/*
 * author vking.wang
 */
(function($){
	$('<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>').appendTo('head');
	$.ajax({
		url:'http://cms.wisheli.com/api/act/getWechatJS',
		dataType:'jsonp',
		async:false,
		success:function(json){
			//初始化weixinjs
			wx.config({
			    debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
			    appId: json.jsapi_sign.appid,
			    timestamp: json.jsapi_sign.timestamp,
			    nonceStr: json.jsapi_sign.noncestr,
			    signature: json.jsapi_sign.signature,
			    jsApiList: ['onMenuShareTimeline','onMenuShareAppMessage'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
			});
		}
	});
	$.fn.wechat=function(shareConfig){
		shareConfig = $.extend({
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
		}, shareConfig);
		wx.onMenuShareTimeline(shareConfig);
		wx.onMenuShareAppMessage(shareConfig);
		wx.onMenuShareQQ(shareConfig);
		wx.onMenuShareWeibo(shareConfig);
	};
})(jQuery);