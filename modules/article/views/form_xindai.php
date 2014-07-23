<h3><?php print $header?></h3>
<?php print form_open_multipart('article/admin/article/form/xindai',array('class'=>'horizontal','id'=>'articleForm'))?>
    <input type="hidden" name="id" value="<?php print $editinfo['id'];?>" />
    <fieldset>
        <ol>
            <li>
                <label for="title">标 题</label>
                <input type="text" name="title" id="title" class="text" value="<?php print $editinfo['title'];?>"/>
            </li>
            <li>
                <label for="source">来 源</label>
                <input type="text" name="source" id="source" class="text" value="<?php print $editinfo['source'];?>"/>
            </li>
            <!--
            <li>
                <label for="farmtype">资讯归类</label>
                <?php
                $farmtypearr=@explode(",",$editinfo['farmtype']);
				foreach($farmtype as $pos=>$v):
				$checked=(in_array($pos, $farmtypearr))?'checked="checked"':'';	
                ?>
                <input type="checkbox" name="farmtype[]" id="farmtype" <?php print $checked;?> value="<?php echo $pos;?>"/><?php echo $v;?>
                <?php
                endforeach;
                ?>
            </li>
            -->
            <li>
                <label for="farmtype">信贷类别</label>
               	<select name="farmtype">
               		<?php foreach($farmtype as $pos=>$v):?>
               		<option value="<?php echo $pos?>" <?php echo $pos==$editinfo['farmtype']?'selected="selected"':'';?> ><?php echo $v;?></option>
               		<?php endforeach;?>
               	</select>
            </li>
            <li>
                <label for="thumb1">资讯 图片(160*120)<br>(jpg|jpeg|png|bmp)
                <?php if($editinfo['src']):?>
                <img style="margin-top: -20px; margin-left: 10px;" width="50" height="50" src="<?php print base_url().$editinfo['src'];?>" />
                <?php endif;?>	
                </label>
                <input type="file" name="src" id="src" class="text"/>
            </li>
            <li>
                <label for="content">资讯详情</label>
                <textarea name="content" class="text"><?php print $editinfo['content'];?></textarea>
            </li>
            <li class="submit">
            	<div class="buttons">
            		<button type="submit" class="positive" name="submit" value="submit">
            			<?php print $this->bep_assets->icon('key') ?>
            			提交
            		</button>
            		<a href="<?php print site_url('article/admin/article/index/xindai') ?>" class="negative">
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