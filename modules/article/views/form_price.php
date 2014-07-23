<h3><?php print $header?></h3>
<?php print form_open_multipart('article/admin/article/form/price',array('class'=>'horizontal','id'=>'articleForm'))?>
    <input type="hidden" name="id" value="<?php print $editinfo['id'];?>" />
    <fieldset>
        <ol>
            <li>
                <label for="title">标 题</label>
                <input type="text" name="title" id="title" class="text" value="<?php print $editinfo['title'];?>"/>
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
                <label for="packprice">归属订阅包</label>
                <select name="packprice" id="packprice">
                	<option value=""/>请选择</option>
                <?php
                foreach($packprice as $pos=>$v):
				$checked=($editinfo['packprice']==$pos)?'selected="selected"':'';	
                ?>
                <option <?php print $checked;?> value="<?php echo $pos;?>"/><?php echo $v;?></option>
                <?php
                endforeach;
                ?>
                </select>
            </li>
            <li>
                <label for="content">资讯简介</label>
                <textarea id="subject" name="subject" ><?php print $editinfo['subject'];?></textarea>
            </li>
            <li>
                <label for="content">资讯详情</label>
                <textarea id="content" name="content" ><?php print $editinfo['content'];?></textarea>
            </li>
            <li>
                <label>&nbsp;</label>
            	<label for="content" generated="true" class="error" style="display: none;">资讯详情 是必须要填写的</label>
            </li>
            <li class="submit">
            	<div class="buttons">
            		<button type="submit" class="positive" id="submit" name="submit" value="submit">
            			<?php print $this->bep_assets->icon('key') ?>
            			提交
            		</button>
            		<a href="<?php print site_url('article/admin/article/index/price') ?>" class="negative">
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
		editer=$('textarea[name="content"]').xheditor({
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