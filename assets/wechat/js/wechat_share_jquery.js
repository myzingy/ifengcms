/*
 * author piexl
 */
(function($){
	$.extend($.fn, {
			wechat:function(){
				var that=this;
				that=$.extend(this,{
					init:function(){
						$.ajax({
			            url:'http://cms.wisheli.com/assets/wechat/php/wechat.share.php',
			            dataType:'jsonp',
			            async:false,
			            data:"&shareurl="+encodeURIComponent(window.location.href),
			            success:function(wechat_conf){
			                // console.log("int",wechat_conf,window.location.href);
							wx.config({
								debug: false,
								appId: wechat_conf.appId,
								timestamp: wechat_conf.timestamp,
								nonceStr: wechat_conf.nonceStr,
								signature: wechat_conf.signature,
								jsApiList: [
								  'checkJsApi', 'onMenuShareTimeline', 'onMenuShareAppMessage', 'onMenuShareQQ', 'onMenuShareWeibo'
								]
							});
						}
					})
					},
					share:function(config){
		  			var shareConfig = $.extend({
						title: '分享标题', // 分享标题
						desc: '分享描述', // 分享描述
					    link: location.href, // 分享链接
					    imgUrl: 'http://cms.wisheli.com/uploads/special/wxb/images/share.jpg', // 分享图标
					    success: function (res) { 
					        // 用户确认分享后执行的回调函数
					        console.log(res);
					    },
					    cancel: function (res) { 
					        // 用户取消分享后执行的回调函数
					        console.log(res);
					    }
					}, config);
					wx.ready(function () {
						wx.onMenuShareTimeline(shareConfig);
						wx.onMenuShareAppMessage(shareConfig);
						wx.onMenuShareQQ(shareConfig);
						wx.onMenuShareWeibo(shareConfig);
					});
				}
				});
			that.init();
		}
	})
})(jQuery);