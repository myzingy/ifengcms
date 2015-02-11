<h3><?php print $header?></h3>
<?php print form_open_multipart('article/admin/article/form/top',array('class'=>'horizontal','id'=>'articleForm'))?>
    <input type="hidden" name="id" value="<?php print $editinfo['id'];?>" />
    <fieldset>
        <ol>
            <li>
                <label for="title">标 题</label>
                <input type="text" name="title" id="title" class="text" value="<?php print $editinfo['title'];?>"/>
            </li>
            <li>
                <label for="thumb1">宣传图片 (720*320)<br>(jpg|jpeg|png|bmp)
                <?php if($editinfo['src']):?>
                <img style="margin-top: -20px; margin-left: 10px;" width="50" height="50" src="<?php print base_url().$editinfo['src'];?>" />
                <?php endif;?>	
                </label>
                <input type="file" name="src" id="src" class="text"/>
            </li>
            <!--
            <li>
                <label for="thumb2">宣传图片(480*320)
                <?php if($editinfo['thumb'][2]):?>
                <img width="50" height="50" src="<?php print base_url().$editinfo['thumb'][2];?>" />
                <?php endif;?>	
                </label>
                <input type="file" name="thumb2" id="thumb2" class="text"/>
            </li>
            <li>
                <label for="thumb3">宣传图片(640*480)
                <?php if($editinfo['thumb'][3]):?>
                <img width="50" height="50" src="<?php print base_url().$editinfo['thumb'][3];?>" />
                <?php endif;?>	
                </label>
                <input type="file" name="thumb3" id="thumb3" class="text"/>
            </li>
            -->
            <li>
                <label for="position">推荐位置</label>
                <?php
                $arr=@explode(",",$editinfo['position']);
                foreach($position as $pos=>$v):
				$checked=(in_array($pos, $arr))?'checked="checked"':'';	
                ?>
                <input type="checkbox" name="position[]" id="position" <?php print $checked;?> value="<?php echo $pos;?>"/><?php echo $v;?>
                <?php
                endforeach;
                ?>
            </li>
            <li>
                <label for="content">内容详情</label>
                <textarea name="content" class="text"><?php print $editinfo['content'];?></textarea>
            </li>
            <li class="submit">
            	<div class="buttons">
            		<button type="submit" class="positive" name="submit" value="submit">
            			<?php print $this->bep_assets->icon('key') ?>
            			提交
            		</button>
            		<a href="<?php print site_url('article/admin/article/index/top') ?>" class="negative">
            			<?php print  $this->bep_assets->icon('cross');?>
            			取消
            		</a>
            </li>
        </ol>
    </fieldset>
<?php print form_close()?>
<script type="text/javascript">
	var editer;
	pageinit=function(){
		jQuery.extend(jQuery.validator.messages, {
	        required: "至少勾选一个"
		});
		editer=$('textarea').xheditor({
			tools:'FontSize,Bold,FontColor,Removeformat,List,Link,Unlink,Img,Pastetext'
			,width:'300px'
			,height:'350px'
			,internalStyle:false
			,upImgUrl:'<?php print site_url('upload');?>'
		});
		$("#submit").click(function(){
			$('textarea').val(editer.getSource());
		});
		$("#articleForm").validate({
			<?php echo $jsform;?>,
			event: "blur",
			submitHandler: function() {
				document.getElementById("articleForm").submit();
			}
		});
	};
</script>