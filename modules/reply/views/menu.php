<style>
	#articleForm input.menu{width:100px;}
	#articleForm ul{ padding: 5px; margin-bottom: 0px;}
	#articleForm .background1{background-color: #eee; border: 1px solid #eeeeee;}
	#articleForm ul.background1{ border: 0;}
</style>
<h2>凤凰陕西公众号&nbsp;&nbsp;&gt;&gt;&nbsp;&nbsp;<?php print $header;?></h2>
<tip>
	<p>二级菜单有内容时，顶级菜单内容将失效；关键字只能填写一个词组；</p>
</tip>
<?php print form_open_multipart('reply/admin/reply/menu/',array('class'=>'horizontal','id'=>'articleForm'))?>
<fieldset>
        <ol>
            <?php for($i=0;$i<3;$i++):$fmenu=$menu['menu']['button'][$i];?>
            <li class="background<?php print $i%2;?>">
                <input type="text" name="menu[]" id="title" class="text menu" value="<?php print $fmenu['name'];?>"/>
                 &nbsp;=&gt;(链接/关键字)=&gt;&nbsp;<input type="text" name="menu_val[]" id="title" class="text" value="<?php print $fmenu['url']?$fmenu['url']:$fmenu['key'];?>"/>
                 <button type="button" class="negative clear_child">
        			<?php print $this->bep_assets->icon('cross') ?>
        			清空子菜单
        		</button>
                <ul class="background<?php print $i%2;?>">
                <?php $submenu=$fmenu['sub_button'];for($j=0;$j<5;$j++):$smenu=$submenu[$j];?>
                	<li>
		                <input type="text" name="menu_sub<?php print $i;?>[]" id="source" class="text menu" value="<?php print $smenu['name'];?>"/>
		                &nbsp;=&gt;(链接/关键字)=&gt;&nbsp;<input type="text" name="menu_sub_val<?php print $i;?>[]" id="title" class="text" value="<?php print $smenu['url']?$smenu['url']:$smenu['key'];?>"/>
		            </li>
                <?php endfor;?>
                </ul>
            </li>
            <?php endfor;?>
            <li class="submit">
            	<div class="buttons">
            		<button type="submit" class="positive" name="submit" value="submit">
            			<?php print $this->bep_assets->icon('key') ?>
            			提交后直接生效
            		</button>
            </li>
        </ol>
    </fieldset>
<?php print form_close()?>
<script type="text/javascript">
	var editer;
	pageinit=function(){
		$('.clear_child').click(function(){
			$input=$(this).next().find('input');
			$input.val('');
		});
	};
</script>