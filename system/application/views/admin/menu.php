<!--
When creating a new menu item on the top-most level
Please ensure that you assign the LI a unique ID

Examples can be seen below for menu_bep_system
-->
<ul id="menu">
    <li id="menu_bep_home"><?php print anchor('admin',$this->lang->line('backendpro_dashboard'),array('class'=>'icon_house'))?></li>
    <?php if(check('System',NULL,FALSE)):?>
    <li id="menu_bep_system"><span class="icon_computer"><?php print $this->lang->line('backendpro_system')?></span>
        <ul>
            <?php if(check('Members',NULL,FALSE)):?><li><?php print anchor('auth/admin/members',$this->lang->line('backendpro_members'),array('class'=>'icon_group'))?></li><?php endif;?>
            <?php if(check('Access Control',NULL,FALSE)):?><li><?php print anchor('auth/admin/access_control',$this->lang->line('backendpro_access_control'),array('class'=>'icon_shield'))?></li><?php endif;?>
            <?php if(check('Settings',NULL,FALSE)):?><li><?php print anchor('admin/settings',$this->lang->line('backendpro_settings'),array('class'=>'icon_cog'))?></li><?php endif;?>
        </ul>
    </li>
    <?php endif;?>
    <li id="menu_bep_system"><span class="icon_computer">新闻管理系统</span>
        <ul>
        	<li><?php print anchor('article/admin/article/update','添加新闻',array('class'=>'icon_add'))?></li>
			<li><?php print anchor('article/admin/article/articleList','新闻管理',array('class'=>'icon_pencil'))?></li>
			<li><?php print anchor('article/admin/article/classifyList','分类管理',array('class'=>'icon_application'))?></li>
        </ul>
    </li>
</ul>