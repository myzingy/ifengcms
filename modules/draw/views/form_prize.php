<style type="text/css">
#classify_dispaly label{ width:auto; border: 1px solid #ccc; padding: 3px; display: inline-block;}
#classify_dispaly label.error{vertical-align:top;width:auto; border: 0px;}
</style>
<h3><?php print $header?></h3>
<?php print form_open_multipart('draw/admin/draw/form_prize/'.$editinfo['id'],array('class'=>'horizontal','id'=>'articleForm'))?>
    <input type="hidden" name="id" value="<?php print $editinfo['id'];?>" />
    <fieldset>
        <ol>
            <li>
                <label for="title">奖品名称<font color="red">*</font></label>
                <input type="text" name="name" id="name" class="text" value="<?php print $editinfo['name'];?>"/>
            </li>
            <li>
                <label for="source">发行量<font color="red">*</font></label>
                <input type="text" name="total" id="total" class="text" value="<?php print $editinfo['total'];?>"/>
            </li>
            <li>
                <label for="source">库存量<font color="red">*</font></label>
                <input type="text" name="stock" id="stock" class="text" value="<?php print $editinfo['stock'];?>"/>
            </li>
            <li>
                <label for="source">中奖概率<font color="red">*</font></label>
                <input type="text" name="gailv" id="gailv" class="text" value="<?php print $editinfo['gailv'];?>"/>/万
            </li>
            <li>
                <label for="source">领奖信息</label>
                <textarea type="text" name="info" style="width:500px; height: 120px;"><?php print $editinfo['info'];?></textarea>
            </li>
            <li class="submit">
            	<div class="buttons">
            		<button type="submit" class="positive" name="submit" value="submit">
            			<?php print $this->bep_assets->icon('key') ?>
            			提交
            		</button>
            		<a href="<?php print strpos($_SERVER['HTTP_REFERER'],$_SERVER['PHP_SELF'])!==false?site_url('draw/admin/draw/prize'):$_SERVER['HTTP_REFERER'];?>" class="negative">
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