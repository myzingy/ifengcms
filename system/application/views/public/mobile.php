<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="format-detection" content="telephone=no" />
<meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0, user-scalable=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="white" />
<?php print $this->bep_site->get_metatags(); ?>
<title><?php print $title.' | '.$this->preference->item('site_name')?></title>
<?php print $this->bep_site->get_variables()?>
<?php print $this->bep_assets->get_header_assets();?>
<?php print $this->bep_site->get_js_blocks()?>
<body>
<section class="pages">
	<div class="page" id="home">
		<section class="content">
			<?php print displayStatus();?>
			<?php
			if( isset($page)){
				if( isset($module)){
			        $this->load->module_view($module,$page);
			    } else {
			        $this->load->view($page);
			    }
			}
			?>
		</section>
	</div>
</section>
<footer class="footer_bar clearfix" id="footer_bar">
	<nav>
		<ul>
			<li class="left">
				<a href="javascript:history.go(-1);"> <img src="<?php echo base_url()?>assets/zhangwen/images/weixin_goback.jpg" alt=""></a>
			</li>
			<li>
				<a href="<?php echo site_url('mobile/api/index/'.($site->id?$site->id:1));?>"><?php print $site->name?$site->name:$this->preference->item('site_name');?></a>
			</li>
			<li class="right">
				<a href="javascript:location.replace(location.href);"> <img src="<?php echo base_url()?>assets/zhangwen/images/weixin_refresh.jpg" alt=""></a>
			</li>
		</ul>
	</nav>
</footer>
<script type="text/javascript">
function viewProfile(id){
    if (typeof WeixinJSBridge != "undefined" && WeixinJSBridge.invoke){
        WeixinJSBridge.invoke('profile',{
            'username':id,
            'scene':'57'
        });
    }
}
var dataForWeixin={
   appId:"",
   MsgImg:"消息图片路径",
   TLImg:"时间线图路径",
   url:"分享url路径",
   title:"<?php print $title;?>",
   desc:"<?php print $title;?>",
   fakeid:"",
   callback:function(){}
};
WeixinJS = typeof WeixinJS!='undefined' || {};
WeixinJS.hideOptionMenu = function() {
	document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
		if (typeof WeixinJSBridge!='undefined')	WeixinJSBridge.call('hideOptionMenu');
	});
};
WeixinJS.hideToolbar = function() {
	document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
		if (typeof WeixinJSBridge!='undefined') WeixinJSBridge.call('hideToolbar');
	});
};

(function(){
   var onBridgeReady=function(){
   WeixinJSBridge.on('menu:share:appmessage', function(argv){
      WeixinJSBridge.invoke('sendAppMessage',{
         "appid":dataForWeixin.appId,
         "img_url":dataForWeixin.MsgImg,
         "img_width":"120",
         "img_height":"120",
         "link":dataForWeixin.url,
         "desc":dataForWeixin.desc,
         "title":dataForWeixin.title
      }, function(res){(dataForWeixin.callback)();});
   });
   WeixinJSBridge.on('menu:share:timeline', function(argv){
      (dataForWeixin.callback)();
      WeixinJSBridge.invoke('shareTimeline',{
         "img_url":dataForWeixin.TLImg,
         "img_width":"120",
         "img_height":"120",
         "link":dataForWeixin.url,
         "desc":dataForWeixin.desc,
         "title":dataForWeixin.title
      }, function(res){});
   });
   WeixinJSBridge.on('menu:share:weibo', function(argv){
      WeixinJSBridge.invoke('shareWeibo',{
         "content":dataForWeixin.title,
         "url":dataForWeixin.url
      }, function(res){(dataForWeixin.callback)();});
   });
   WeixinJSBridge.on('menu:share:facebook', function(argv){
      (dataForWeixin.callback)();
      WeixinJSBridge.invoke('shareFB',{
         "img_url":dataForWeixin.TLImg,
         "img_width":"120",
         "img_height":"120",
         "link":dataForWeixin.url,
         "desc":dataForWeixin.desc,
         "title":dataForWeixin.title
      }, function(res){(dataForWeixin.callback)();});
   });
};
if(document.addEventListener){
   document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
}else if(document.attachEvent){
   document.attachEvent('WeixinJSBridgeReady'   , onBridgeReady);
   document.attachEvent('onWeixinJSBridgeReady' , onBridgeReady);
}
})();
WeixinJS.hideToolbar();
</script>
</body>
</html>