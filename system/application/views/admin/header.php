<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php print $this->bep_site->get_metatags();?>
	<title><?php print $header.' | '.$this->preference->item('site_name');?></title>
	<?php print $this->bep_site->get_variables();?>
	<?php print $this->bep_assets->get_header_assets();?>
	<?php print $this->bep_site->get_js_blocks();?>
</head>
<body>

<div id="wrapper">
    <div id="header">
        <div id="site"><?php print $this->preference->item('site_name')?></div>
        <div id="info">
        	<?php if(false && check('System',NULL,FALSE)):?>
        	<?php print anchor('open/fighter','战斗机统计平台',array('class'=>'icon_shield','target'=>'_blank'))?>&nbsp;&nbsp;&nbsp;&nbsp;
        	<?php endif;?>
        	<?php print anchor('',$this->lang->line('backendpro_view_website'),array('class'=>'icon_world_go','target'=>'_blank'))?>&nbsp;&nbsp;&nbsp;&nbsp;
            <?php print anchor('http://cms.wisheli.com/mnew/changepassword.html','我的资料',array('class'=>'icon_user','target'=>'_blank'))?>&nbsp;&nbsp;&nbsp;&nbsp;
            <?php print anchor('auth/logout',$this->lang->line('userlib_logout'),array('class'=>'icon_key_go'))?>
        </div>
    </div>