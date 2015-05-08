<style type="text/css">
#classify_dispaly label{ width:auto; border: 1px solid #ccc; padding: 3px; display: inline-block;}
#classify_dispaly label.error{vertical-align:top;width:auto; border: 0px;}
</style>
<h3><?php print $header?></h3>
<?php print form_open_multipart('vote/admin/vote/updatevotedata/'.$vid,array('class'=>'horizontal','id'=>'articleForm'))?>
    <input type="hidden" name="id" value="<?php print $editinfo['id'];?>" />
    <input type="hidden" name="vid" value="<?php print $vid;?>" />
    <fieldset>
        <ol>
            <li>
                <label for="name">名称</label>
                <input type="text" name="name" id="name" class="text" value="<?php print $editinfo['name'];?>"/>
            </li>
            <li>
                <label for="code">号码</label>
                <input type="text" name="code" id="code" class="text" value="<?php print $editinfo['code'];?>"/>
            </li>
            <li>
                <label for="thumb">图片
                <?php if($editinfo['thumb']):?>
                <img style="margin-top: -20px; margin-left: 10px;" width="50" height="50" src="<?php print $editinfo['thumb'];?>" />
                <?php endif;?></label>
                <input type="file" name="thumb" id="thumb" class="text" value="<?php print $editinfo['enum'];?>"/>
            </li>
            <li>
                <label for="count">票数</label>
                <input type="text" name="count" id="count" class="text" value="<?php print $editinfo['count'];?>"/>
            </li>
            <li>
                <label for="info">简介</label>
                <textarea name="info" class="text" rows="10"><?php print $editinfo['info'];?></textarea>
                <label for="info" generated="true" class="error" style="display: inline-block;"></label>
            </li>
            <li class="submit">
            	<div class="buttons">
            		<button type="submit" class="positive" value="submit">
            			<?php print $this->bep_assets->icon('key') ?>
            			提交
            		</button>
            		<a href="<?php print site_url('vote/admin/vote/votedata/'.$vid) ?>" class="negative">
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
				$("#articleForm").ajaxSubmit({
					dataType:'json',
					success:function(json){
						console.log(json);
						if(json.status==0){
							location.href='<?php print site_url('vote/admin/vote/votedata/'.$vid) ?>';
							return;
						}
						alert(json.error);
					}
				});
			}
		});
	};
</script>