<style type="text/css">
#classify_dispaly label{ width:auto; border: 1px solid #ccc; padding: 3px; display: inline-block;}
#classify_dispaly label.error{vertical-align:top;width:auto; border: 0px;}
</style>
<h3><?php print $header?></h3>
<?php print form_open_multipart('special/admin/special/update',array('class'=>'horizontal','id'=>'articleForm'))?>
    <input type="hidden" name="id" value="<?php print $editinfo['id'];?>" />
    <fieldset>
        <ol>
            <li>
                <label for="title">专题名称</label>
                <input type="text" name="title" id="title" class="text" value="<?php print $editinfo['title'];?>"/>
            </li>
            <li>
                <label for="thumb1">专题图片(320*240)<br>(jpg|jpeg|png|bmp)
                <?php if($editinfo['src']):?>
                <img style="margin-top: -20px; margin-left: 10px;" width="50" height="50" src="<?php print base_url().$editinfo['src'];?>" />
                <?php endif;?>	
                </label>
                <input type="file" name="src" id="src" class="text"/>
            </li>
            <li>
            	<label for="farmtype">专题位置<font color="red">*</font></label>
            	<span id="classify_dispaly">
                <?php
                $selectedArr=$editinfo['classify']?$editinfo['classify']:array();//文章选中的分类
				foreach($classify as $pos=>$v):
				$checked=(in_array($pos, $selectedArr))?'checked="checked"':'';	
                ?>
                <label><input type="checkbox" name="classify[]" <?php print $checked;?> value="<?php echo $pos;?>"/><?php echo $v;?></label>
                <?php
                endforeach;
                ?>
                <label for="classify[]" generated="true" class="error"></label>
                </span>
                
            </li>
            <li>
                <label for="subject">专题简介</label>
                <textarea name="subject" class="text" rows="10"><?php print $editinfo['subject'];?></textarea>
                <label for="subject" generated="true" class="error" style="display: inline-block;"></label>
            </li>
            <li>
                <label for="thumb1">专题文件包</label>
                <input type="file" name="special" id="special" class="text"/>
                <?php if($editinfo['url']):?>
                <a target="_blank" href="<?php print base_url().$editinfo['url'];?>"><?php print base_url().$editinfo['url'];?></a>
                <?php endif;?>
            </li>
            <li>
                <label for="thumb1">&nbsp;</label>
                压缩包必须是对文件夹压缩的zip包，文件夹名字不能含有中文/空格/符号等特殊字符，建议文件夹命名方式(a-Z,0-9,_)
            </li>
            <li class="submit">
            	<div class="buttons">
            		<button type="submit" class="positive" name="submit" value="submit">
            			<?php print $this->bep_assets->icon('key') ?>
            			提交
            		</button>
            		<a href="<?php print site_url('special/admin/special/index') ?>" class="negative">
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
		$("#articleForm").validate({
			<?php echo $jsform;?>,
			event: "blur",
			submitHandler: function() {
				document.getElementById("articleForm").submit();
			}
		});
	};
</script>