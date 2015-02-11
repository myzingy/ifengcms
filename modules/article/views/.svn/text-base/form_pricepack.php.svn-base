<h3><?php print $header?></h3>
<?php print form_open_multipart('article/admin/article/form/pricepack',array('class'=>'horizontal','id'=>'articleForm'))?>
    <input type="hidden" name="id" value="<?php print $editinfo['id'];?>" />
    <fieldset>
        <ol>
            <li>
                <label for="title">标 题</label>
                <input type="text" name="title" id="title" class="text" value="<?php print $editinfo['title'];?>"/>
            </li>
            <!--
            <li>
                <label for="price">价 格</label>
                <input type="text" name="price" id="price" class="text" value="<?php print $editinfo['price'];?>"/>
            </li>
            <li>
                <label for="expday">有效期（天）</label>
                <input type="text" name="extday" id="extday" class="text" value="<?php print $editinfo['extday'];?>"/>
            </li>
            -->
            <li>
                <label for="payment">付费方案</label>
                <?php
                $farmtypearr=@explode(",",$editinfo['payment']);
                foreach($payment as $pos=>$v):
				$checked=(in_array($pos, $farmtypearr))?'checked="checked"':'';	
                ?>
                <input type="checkbox" name="payment[]" <?php print $checked;?> value="<?php echo $pos;?>" price="<?php echo $v['price'];?>"/><?php echo $v['name'].'('.$v['price'].'元 '.$v['extday'].'天)'.$v['info'];?>
                <?php
                endforeach;
                ?>
            </li>
            <!--
            <li>
                <label for="thumb1">付费包图片(width*hight)
                <?php if($editinfo['src']):?>
                <img width="50" height="50" src="<?php print base_url().$editinfo['src'];?>" />
                <?php endif;?>	
                </label>
                <input type="file" name="src" id="src" class="text"/>
            </li>
            -->
            <li>
                <label for="content">付费包简介</label>
                <textarea id="subject" name="subject" ><?php print $editinfo['subject'];?></textarea>
            </li>
            <li>
                <label for="content">付费包详情</label>
                <textarea name="content" class="text"><?php print $editinfo['content'];?></textarea>
            </li>
            <li class="submit">
            	<div class="buttons">
            		<button type="submit" class="positive" name="submit" value="submit">
            			<?php print $this->bep_assets->icon('key') ?>
            			提交
            		</button>
            		<a href="<?php print site_url('article/admin/article/index/pricepack') ?>" class="negative">
            			<?php print  $this->bep_assets->icon('cross');?>
            			取消
            		</a>
            </li>
        </ol>
    </fieldset>
<?php print form_close()?>
<script type="text/javascript">
	pageinit=function(){
		jQuery.extend(jQuery.validator.messages, {
	        required: "至少勾选一个"
		});
		$("#articleForm").validate({
			<?php echo $jsform;?>,
			event: "blur",
			submitHandler: function() {
				var price=0;
				$('input[type="checkbox"]').each(function(i){
					if($(this).attr('checked')){
						price+=parseInt($(this).attr('price'));
					}
				});
				if(price>0){
					document.getElementById("articleForm").submit();
				}else{
					alert('请至少勾选一个收费方案');
				}
			}
		});
	};
</script>