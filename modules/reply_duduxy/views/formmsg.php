<style type="text/css">
#classify_dispaly label{ width:auto; border: 1px solid #ccc; padding: 3px; display: inline-block;}
#classify_dispaly label.error{vertical-align:top;width:auto; border: 0px;}
</style>
<h2><?php print $header?></h2>
<?php print form_open_multipart($wechat_name.'/admin/'.$wechat_name.'/msg/'.$editinfo['type'],array('class'=>'horizontal','id'=>'articleForm'))?>
    <input type="hidden" name="type" value="<?php print $editinfo['type'];?>" />
    <fieldset>
        <ol>
        	
            <li>
                <label for="subject">回复内容*</label>
                <textarea name="content" class="text" rows="10"><?php print $editinfo['content'];?></textarea>
                <label for="content" generated="true" class="error" style="display: inline-block;"></label>
            </li>
            <li class="submit">
            	<div class="buttons">
            		<button type="submit" class="positive" name="submit" value="submit">
            			<?php print $this->bep_assets->icon('key') ?>
            			提交
            		</button>
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