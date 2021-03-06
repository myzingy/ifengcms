<!DOCTYPE html>
<html lang="zh-cn">
<head>
<!-- 为了确保适当的绘制和触屏缩放 -->
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- 可以禁用其缩放（zooming）功能 -->
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<!-- 对手机设备的一些设置 -->
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<?php print $this->bep_site->get_metatags(); ?>
<title><?php print $title.' | '.$this->preference->item('site_name')?></title>
<?php print $this->bep_site->get_variables()?>
<?php print $this->bep_assets->get_header_assets();?>
<?php print $this->bep_site->get_js_blocks()?>
<script>
document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
	WeixinJSBridge.call('hideToolbar');
});
</script>
</head>
<body>
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
<?php print $this->bep_assets->get_footer_assets();?>
</body>
</html>