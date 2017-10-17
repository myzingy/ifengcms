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
    <?php if(check('ifengcms',NULL,FALSE)):?>
    <li id="menu_bep_system_cms"><span class="icon_computer">CMS管理系统</span>
    	<ul>
	    	<li id="menu_bep_system_cms_news"><span class="icon_computer">新闻管理系统</span>
		        <ul>
		        	<li><?php print anchor('article/admin/article/articleList','新闻管理',array('class'=>'icon_pencil'))?></li>
					<li><?php print anchor('article/admin/article/update','添加新闻',array('class'=>'icon_add'))?></li>
					<?php if(check('classify',NULL,FALSE)):?><?php endif;?>
					<li><?php print anchor('article/admin/article/classifyList','推荐位置管理',array('class'=>'icon_application'))?></li>
		        </ul>
		    </li>
		    <li id="menu_bep_system_cms_spe"><span class="icon_computer">专题管理系统</span>
		        <ul>
		        	<li><?php print anchor('special/admin/special/index','专题管理',array('class'=>'icon_pencil'))?></li>
					<li><?php print anchor('special/admin/special/update','添加专题',array('class'=>'icon_add'))?></li>
				</ul>
		    </li>
		    <li id="menu_bep_system_cms_com"><span class="icon_computer">评论管理系统</span>
		        <ul>
		        	<li><?php print anchor('comment/admin/comment/index','评论管理',array('class'=>'icon_pencil'))?></li>
				</ul>
		    </li>
		    <li id="menu_bep_system_cms_ads"><span class="icon_computer">广告管理系统</span>
		        <ul>
		        	<li><?php print anchor('ads/admin/ads/index','广告管理',array('class'=>'icon_pencil'))?></li>
		        </ul>
		    </li>
		 </ul>
    </li>
    <?php endif;?>
    <?php if(check('wechat',NULL,FALSE)):?>
    <li id="menu_bep_system_wechat"><span class="icon_computer">公众号管理</span>
    	<ul>
    		<li id="menu_bep_system_wechat_ifeng"><span class="icon_computer">凤凰陕西公众号</span>
    			<ul>
		        	<li><?php print anchor('reply/admin/reply/menu','自定义菜单',array('class'=>'icon_pencil'))?></li>
		        	<li><?php print anchor('reply/admin/reply/keylist','关键字回复',array('class'=>'icon_pencil'))?></li>
		        	<li><?php print anchor('reply/admin/reply/msg/subscribe','被关注回复',array('class'=>'icon_pencil'))?></li>
		        	<li><?php print anchor('reply/admin/reply/msg/keyword','消息回复',array('class'=>'icon_pencil'))?></li>
		        </ul>
    		</li>
    		<li id="menu_bep_system_wechat_duduxy"><span class="icon_computer">嘟嘟校园公众号</span>
    			<ul>
		        	<li><?php print anchor('reply_duduxy/admin/reply_duduxy/menu','自定义菜单',array('class'=>'icon_pencil'))?></li>
		        	<li><?php print anchor('reply_duduxy/admin/reply_duduxy/keylist','关键字回复',array('class'=>'icon_pencil'))?></li>
		        	<li><?php print anchor('reply_duduxy/admin/reply_duduxy/msg/subscribe','被关注回复',array('class'=>'icon_pencil'))?></li>
		        	<li><?php print anchor('reply_duduxy/admin/reply_duduxy/msg/keyword','消息回复',array('class'=>'icon_pencil'))?></li>
		        </ul>
    		</li>
    		<li id="menu_bep_system_wechat_yidong"><span class="icon_computer">移动政企公众号</span>
    			<ul>
		        	<li><?php print anchor('reply_yidong/admin/reply_yidong/menu','自定义菜单',array('class'=>'icon_pencil'))?></li>
		        	<li><?php print anchor('reply_yidong/admin/reply_yidong/keylist','关键字回复',array('class'=>'icon_pencil'))?></li>
		        	<li><?php print anchor('reply_yidong/admin/reply_yidong/msg/subscribe','被关注回复',array('class'=>'icon_pencil'))?></li>
		        	<li><?php print anchor('reply_yidong/admin/reply_yidong/msg/keyword','消息回复',array('class'=>'icon_pencil'))?></li>
		        </ul>
    		</li>
    	</ul>
    </li>
    <?php endif;?>
    <?php if(check('diyfields',NULL,FALSE)):?>
    <li id="menu_bep_system_diymenu"><span class="icon_computer">自定义表单</span>
        <ul>
        	<li><?php print anchor('fields/admin/fields/index','表单管理',array('class'=>'icon_pencil'))?></li>
        </ul>
    </li>
    <?php endif;?>
    <?php if(check('vote',NULL,FALSE)):?>
    <li id="menu_bep_system_vote"><span class="icon_computer">投票管理</span>
        <ul>
        	<li><?php print anchor('vote/admin/vote/index','凤凰陕西投票管理',array('class'=>'icon_application'))?></li>
        	<li><?php print anchor('vote_zh/admin/vote/index','中韩交流投票管理',array('class'=>'icon_application'))?></li>
        </ul>
    </li>
    <?php endif;?>
    <?php if(check('draw',NULL,FALSE)):?>
    <li id="menu_bep_system_vote"><span class="icon_computer">抽奖管理</span>
        <ul>
        	<li><?php print anchor('draw/admin/draw/activity','抽奖活动',array('class'=>'icon_application'))?></li>
        	<li><?php print anchor('draw/admin/draw/prize','奖品管理',array('class'=>'icon_application'))?></li>
        </ul>
    </li>
    <?php endif;?>
    <li id="menu_bep_system_praise">
    	<?php print anchor('praise/admin/praise/index','点赞接口',array('class'=>'icon_application'))?>
    </li>
    <?php if(check('yidong',NULL,FALSE)):?>
    <li id="menu_bep_system_vote"><span class="icon_computer">移动政企</span>
        <ul>
        	<li><?php print anchor('yidong/admin/yidong/package','政策套餐',array('class'=>'icon_application'))?></li>
        	<li><?php print anchor('yidong/admin/yidong/devices','机型管理',array('class'=>'icon_application'))?></li>
        	<li><?php print anchor('yidong/admin/yidong/reservation','预约管理',array('class'=>'icon_application'))?></li>
        	<li><?php print anchor('http://cms.wisheli.com/index.php/fields/fieldsDataPage/html/23','微信绑定',array('class'=>'icon_application','target'=>'_blank'))?></li>
        </ul>
    </li>
    <?php endif;?>
</ul>