<style type="text/css">
#classify_dispaly label{ width:auto; border: 1px solid #ccc; padding: 3px; display: inline-block;}
#classify_dispaly label.error{vertical-align:top;width:auto; border: 0px;}
</style>
<h2><?php print $header?></h2>
<?php print form_open_multipart($wechat_name.'/admin/'.$wechat_name.'/keyword',array('class'=>'horizontal','id'=>'articleForm'))?>
    <input type="hidden" name="id" value="<?php print $editinfo['id'];?>" />
    <fieldset>
        <ol>
        	
            <li>
                <label for="title">关键字*</label>
                <input type="text" name="title" id="title" class="text" value="<?php print $editinfo['title'];?>"/>
                请用空格分隔
            </li>
            
            
            <li>
                <label for="subject">回复标题*</label>
                <input name="subject" class="text" value="<?php print $editinfo['subject'];?>">
            </li>
            <li>
                <label for="subject">回复描述*</label>
                <textarea name="content" class="text" rows="10"><?php print $editinfo['content'];?></textarea>
                <label for="content" generated="true" class="error" style="display: inline-block;"></label>
            </li>
            <li>
            	<label for="thumb1">&nbsp;</label>
            	纯文字回复时，可以不添加以下的图片和跳转链接
            </li>
            <li>
                <label for="thumb1">回复图片(320*240)<br>(jpg|jpeg|png|bmp)
                <?php if($editinfo['src']):?>
                <img style="margin-top: -20px; margin-left: 10px;" width="50" height="50" src="<?php print base_url().$editinfo['src'];?>" />
                <?php endif;?>	
                </label>
                <input type="file" name="src" id="src" class="text"/>
            </li>
            <li>
                <label for="subject">跳转链接</label>
                <input name="url" class="text" value="<?php print $editinfo['url'];?>">
            </li>
            <li class="submit">
            	<div class="buttons">
            		<button type="submit" class="positive" name="submit" value="submit">
            			<?php print $this->bep_assets->icon('key') ?>
            			提交
            		</button>
            		<a href="<?php print site_url($wechat_name.'/admin/'.$wechat_name.'/keylist') ?>" class="negative">
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
		$("#articleForm").validate({
			<?php echo $jsform;?>,
			event: "blur",
			submitHandler: function() {
				document.getElementById("articleForm").submit();
			}
		});
	};
</script>