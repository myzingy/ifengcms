<style type="text/css">
#classify_dispaly label{ width:auto; border: 1px solid #ccc; padding: 3px; display: inline-block;}
#classify_dispaly label.error{vertical-align:top;width:auto; border: 0px;}
</style>
<h3><?php print $header?></h3>
<?php print form_open_multipart('article/admin/article/update/'.$editinfo['id'],array('class'=>'horizontal','id'=>'articleForm'))?>
    <input type="hidden" name="id" value="<?php print $editinfo['id'];?>" />
    <input type="hidden" name="type" value="1" />
    <fieldset>
        <ol>
            <li>
                <label for="title">新闻标题<font color="red">*</font></label>
                <input type="text" name="title" id="title" class="text" value="<?php print $editinfo['title'];?>"/>
            </li>
            <li>
                <label for="source">新闻来源<font color="red">*</font></label>
                <input type="text" name="source" id="source" class="text" value="<?php print $editinfo['source'];?>"/>
            </li>
            <li>
            	<label for="farmtype">新闻分类<font color="red">*</font></label>
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
                <label for="thumb1">新闻图片(320*240)<br>(jpg|jpeg|png|bmp)
                <?php if($editinfo['src']):?>
                <img style="margin-top: -20px; margin-left: 10px;" width="50" height="50" src="<?php print base_url().$editinfo['src'];?>" />
                <?php endif;?>	
                </label>
                <input type="file" name="src" id="src" class="text"/>
            </li>
            <li>
                <label for="source">外链地址(优先展示)</label>
                <input type="text" name="url" id="url" class="text" value="<?php print $editinfo['url'];?>"/>
            </li>
            <li>
                <label for="source">新闻简介</label>
                <textarea type="text" name="subject" id="subject" style="width:600px; height: 60px;"><?php print $editinfo['subject'];?></textarea>
            </li>
            <li>
                <label for="content">新闻详情<font color="red">*</font></label>
                <textarea name="content" class="editor"><?php print $editinfo['content'];?></textarea>
                <label for="content" generated="true" class="error" style="display: inline-block;"></label>
            </li>
            <li class="submit">
            	<div class="buttons">
            		<button type="submit" class="positive" name="submit" value="submit">
            			<?php print $this->bep_assets->icon('key') ?>
            			提交
            		</button>
            		<a href="<?php print strpos($_SERVER['HTTP_REFERER'],$_SERVER['PHP_SELF'])!==false?site_url('article/admin/article/articleList'):$_SERVER['HTTP_REFERER'];?>" class="negative">
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
		editer=$('textarea.editor').xheditor({
			tools:'FontSize,Bold,FontColor,Removeformat,List,Link,Unlink,Img,Pastetext'
			,width:'300px'
			,height:'350px'
			,internalStyle:false
			,upImgUrl:'<?php print site_url('upload');?>'
		});
		$("#submit").click(function(){
			$('textarea[name="content"]').val(editer.getSource());
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