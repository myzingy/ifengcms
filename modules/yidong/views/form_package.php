<h2><?php print $header?></h2>
<?php print form_open_multipart('yidong/admin/yidong/package_form/'.$editinfo['id'],array('class'=>'horizontal','id'=>'articleForm'))?>
    <input type="hidden" name="id" value="<?php print $editinfo['id'];?>" />
    <fieldset>
        <ol>
            <li>
            	<label for="farmtype">适用政策<font color="red">*</font></label>
            	<select name="type">
            	<?php
                foreach($classify as $pos=>$v):
				?>
				<option value="<?php echo $pos;?>" <?php if($pos==$editinfo['type']):echo 'selected="selected"';endif;?>><?php echo $v;?></option>
                <?php
                endforeach;
                ?>
                </select>
                
            </li>
            <li>
                <label for="chanpin">产品<font color="red">*</font></label>
                <input type="text" name="chanpin" id="chanpin" class="text" value="<?php print $editinfo['chanpin'];?>"/>
            </li>
            <li>
                <label for="chanpinneirong">产品内容<font color="red">*</font></label>
                <input type="text" name="chanpinneirong" id="chanpinneirong" class="text" value="<?php print $editinfo['chanpinneirong'];?>"/>
            </li>
            <li>
                <label for="yuefanhuafei">月返还话费<font color="red">*</font></label>
                <input type="text" name="yuefanhuafei" id="yuefanhuafei" class="text" value="<?php print $editinfo['yuefanhuafei'];?>"/>
            </li>
            <li>
                <label for="zuidixiaofei">最低消费<font color="red">*</font></label>
                <input type="text" name="zuidixiaofei" id="zuidixiaofei" class="text" value="<?php print $editinfo['zuidixiaofei'];?>"/>
            </li>
            <li>
                <label for="heyueqi">合约期<font color="red">*</font></label>
                <input type="text" name="heyueqi" id="heyueqi" class="text" value="<?php print $editinfo['heyueqi'];?>"/>
            </li>
            
            <li>
                <label for="yuebujiaofei">每月补交费用<font color="red">*</font></label>
                <input type="text" name="yuebujiaofei" id="yuebujiaofei" class="text" value="<?php print $editinfo['yuebujiaofei'];?>"/>
            </li>
            <li class="submit">
            	<div class="buttons">
            		<button type="submit" class="positive" name="submit" value="submit">
            			<?php print $this->bep_assets->icon('key') ?>
            			提交
            		</button>
            		<a href="<?php print strpos($_SERVER['HTTP_REFERER'],$_SERVER['PHP_SELF'])!==false?site_url('yidong/admin/yidong/package'):$_SERVER['HTTP_REFERER'];?>" class="negative">
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