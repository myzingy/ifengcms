<style type="text/css">
#classify_dispaly label{ width:auto; border: 1px solid #ccc; padding: 3px; display: inline-block;}
#classify_dispaly label.error{vertical-align:top;width:auto; border: 0px;}
</style>
<h3><?php print $header?></h3>
<?php print form_open_multipart('vote/admin/vote/update',array('class'=>'horizontal','id'=>'articleForm'))?>
    <input type="hidden" name="id" value="<?php print $editinfo['id'];?>" />
    <fieldset>
        <ol>
            <li>
                <label for="title">活动名称</label>
                <input type="text" name="title" id="title" class="text" value="<?php print $editinfo['title'];?>"/>
            </li>
            <li>
                <label for="snum">开始号码</label>
                <input type="text" name="snum" id="snum" class="text" value="<?php print $editinfo['snum'];?>"/>
            </li>
            <li>
                <label for="enum">结束号码</label>
                <input type="text" name="enum" id="enum" class="text" value="<?php print $editinfo['enum'];?>"/>
            </li>
            <li>
                <label for="stime">开始时间</label>
                <input type="text" name="stime" id="stime" class="text" value="<?php print date('Y-m-d H:i:s',$editinfo['stime']?$editinfo['stime']:TIME);?>"/>
            </li>
            <li>
                <label for="etime">结束时间</label>
                <input type="text" name="etime" id="etime" class="text" value="<?php print date('Y-m-d H:i:s',$editinfo['etime']?$editinfo['etime']:TIME+86400*30);?>"/>
            </li>
            <li>
                <label for="rule">投票方式</label>
                <select name="rule">
                	<option value="0" <?php print $editinfo['rule']==0?'selected="selected"':'';?>>只投一票</option>
                	<option value="1" <?php print $editinfo['rule']==1?'selected="selected"':'';?>>每天一票</option>
                </select>
            </li>
            <li>
                <label for="enum">每人每天最多投多少票</label>
                <input type="text" name="vote_max" id="vote_max" class="text" value="<?php print $editinfo['vote_max'];?>"/>
            </li>
            <li>
            	<label for="testip">测试IP</label>
                <input type="text" name="testip" id="testip" class="text" value="<?php print $testip;?>"/>
                <?php if($editinfo['testip']):?>
                                                            当前IP：<?php print $editinfo['testip'];?>
                <?php endif;?>
            </li>
            <li>
                <label for="thumb">投票头图
                <?php if($editinfo['thumb']):?>
                <img style="margin-top: -20px; margin-left: 10px;" width="50" height="50" src="<?php print $editinfo['thumb'];?>" />
                <?php endif;?></label>
                <input type="file" name="thumb" id="thumb" class="text" value="<?php print $editinfo['thumb'];?>"/>
            </li>
            <li>
                <label for="thumb">详情头图
                <?php if($editinfo['thumb2']):?>
                <img style="margin-top: -20px; margin-left: 10px;" width="50" height="50" src="<?php print $editinfo['thumb2'];?>" />
                <?php endif;?></label>
                <input type="file" name="thumb2" id="thumb2" class="text" value="<?php print $editinfo['thumb2'];?>"/>
            </li>
            <li>
                <label for="enum">背景色</label>
                <input type="text" name="background" id="background" class="text" value="<?php print $editinfo['background'];?>"/>
            </li>
            <li>
                <label for="subject">活动简介</label>
                <textarea name="subject" class="text" rows="10"><?php print $editinfo['subject'];?></textarea>
                <label for="subject" generated="true" class="error" style="display: inline-block;"></label>
            </li>
            <li>
                <label for="remoteurl">外部通知地址</label>
                <input type="text" name="remoteurl" id="remoteurl" class="text" value="<?php print $editinfo['remoteurl'];?>"/>
                _remoteid_ , _votenum_ , _votecode_
            </li>
            <li>
                <label for="displayurl">外部展示地址</label>
                <input type="text" name="displayurl" id="displayurl" class="text" value="<?php print $editinfo['displayurl'];?>"/>
                _remoteid_
            </li>
            <li class="submit">
            	<div class="buttons">
            		<button type="submit" class="positive" value="submit">
            			<?php print $this->bep_assets->icon('key') ?>
            			提交
            		</button>
            		<a href="<?php print site_url('vote/admin/vote/index') ?>" class="negative">
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
							location.href='<?php print site_url('vote/admin/vote/index') ?>';
							return;
						}
						alert(json.error);
					}
				});
			}
		});
	};
</script>