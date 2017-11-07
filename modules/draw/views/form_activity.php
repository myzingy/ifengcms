<style type="text/css">
#classify_dispaly label{ width:auto; border: 1px solid #ccc; padding: 3px; display: inline-block;}
#classify_dispaly label.error{vertical-align:top;width:auto; border: 0px;}
</style>
<h3><?php print $header?></h3>
<?php print form_open_multipart('draw/admin/draw/form_activity/'.$editinfo['draw']->id,array('class'=>'horizontal','id'=>'articleForm'))?>
    <input type="hidden" name="id" value="<?php print $editinfo['draw']->id;?>" />
    <fieldset>
        <ol>
            <li>
                <label for="title">活动名称<font color="red">*</font></label>
                <input type="text" name="title" id="title" class="text" value="<?php print $editinfo['draw']->title;?>"/>
            </li>
            <li>
                <label for="title">开始时间<font color="red">*</font></label>
                <input type="text" name="stime" id="stime" class="text" value="<?php print date('Y-m-d H:i:s',$editinfo['draw']->stime?$editinfo['draw']->stime:TIME);?>"/>
            </li>
            <li>
                <label for="title">结束时间<font color="red">*</font></label>
                <input type="text" name="etime" id="etime" class="text" value="<?php print date('Y-m-d H:i:s',$editinfo['draw']->etime?$editinfo['draw']->etime:(TIME+7*86400));?>"/>
            </li>
            <li>
                <label for="enum">每人每天最多抽奖次数<font color="red">*</font></label>
                <input type="text" name="draw_max" id="draw_max" class="text" value="<?php print $editinfo['draw']->draw_max;?>"/>
            </li>
            <li>
                <label for="title">奖品ID<font color="red">*</font></label>
                <?php
                $prize=$editinfo['prize'];
				$d2p=array();
				if($prize){
					foreach ($prize as $r) {
						array_push($d2p,$r->id);
					}
				}
                ?>
                <input type="text" name="d2p" id="d2p" class="text" value="<?php print implode(',', $d2p);?>"/>
                请用,分割多个奖品
            </li>
            <li class="submit">
            	<div class="buttons">
            		<button type="submit" class="positive" name="submit" value="submit">
            			<?php print $this->bep_assets->icon('key') ?>
            			提交
            		</button>
            		<a href="<?php print strpos($_SERVER['HTTP_REFERER'],$_SERVER['PHP_SELF'])!==false?site_url('draw/admin/draw/activity'):$_SERVER['HTTP_REFERER'];?>" class="negative">
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