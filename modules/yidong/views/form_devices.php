<h2><?php print $header?></h2>
<?php print form_open_multipart('yidong/admin/yidong/devices_form/'.$editinfo['id'],array('class'=>'horizontal','id'=>'articleForm'))?>
    <input type="hidden" name="id" value="<?php print $editinfo['id'];?>" />
    <fieldset>
        <ol>
            <li>
                <label for="name">机型<font color="red">*</font></label>
                <input type="text" name="name" id="name" class="text" value="<?php print $editinfo['name'];?>"/>
            </li>
            <li>
            	<label for="farmtype">适用政策<font color="red">*</font></label>
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
                <label for="yonghujiaokuan">用户缴款<font color="red">*</font></label>
                <input type="text" name="yonghujiaokuan" id="yonghujiaokuan" class="text" value="<?php print $editinfo['yonghujiaokuan'];?>"/>
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
                <label for="chanpin">产品<font color="red">*</font></label>
                <input type="text" name="chanpin" id="chanpin" class="text" value="<?php print $editinfo['chanpin'];?>"/>
            </li>
            <li>
                <label for="chanpinneirong">产品内容<font color="red">*</font></label>
                <input type="text" name="chanpinneirong" id="chanpinneirong" class="text" value="<?php print $editinfo['chanpinneirong'];?>"/>
            </li>
            <hr>
            <li>
                <label for="chanpinneirong">颜色<font color="red">*</font>
                	<a id="add_color" href="#">新增颜色</a>
                </label>
                <table style="display: inline-block;width: 500px;">
                	<thead>
	                	<tr>
		                	<th width="100">颜色</th>
		                	<th width="100">库存</th>
		                	<th width="300">图片</td>
	                	</tr>
                	</thead>
                	<tbody id="color_body">
                		<?php if($editinfo['color']):foreach($editinfo['color'] as $color):?>
	                	<tr>
		                	<td><input type="hidden" name="colorid[]" value="<?php print $color['id'];?>"/><input type="text" style="width:50px;" name="color[]" id="color" class="text" value="<?php print $color['color'];?>"/></th>
		                	<td><input type="text" style="width:50px;" name="stock[]" id="stock" class="text" value="<?php print $color['stock'];?>"/></th>
		                	<td><input type="file" name="img_<?php print $color['id'];?>" id="img"/>
		                		
		                		<a href="#" onclick="del_color(this)"><?php print $this->bep_assets->icon('cross') ?></a>
		                	</td>
	                	</tr>
	                	<?php endforeach;else:?>
	                	<tr>
					    	<td><input type="hidden" name="colorid[]" value="0"/><input type="text" style="width:50px;" name="color[]" id="color" class="text" value=""/></th>
					    	<td><input type="text" style="width:50px;" name="stock[]" id="stock" class="text" value=""/></th>
					    	<td><input type="file" name="img_0" id="img" />
					    		<a href="#" onclick="del_color(this)"><?php print $this->bep_assets->icon('cross') ?></a>
					    	</td>
						</tr>	
	                	<?php endif;?>
                	</tbody>
                </table>
            </li>
            <li class="submit">
            	<div class="buttons">
            		<button type="submit" class="positive" name="submit" value="submit">
            			<?php print $this->bep_assets->icon('key') ?>
            			提交
            		</button>
            		<a href="<?php print strpos($_SERVER['HTTP_REFERER'],$_SERVER['PHP_SELF'])!==false?site_url('yidong/admin/yidong/devices'):$_SERVER['HTTP_REFERER'];?>" class="negative">
            			<?php print  $this->bep_assets->icon('cross');?>
            			取消
            		</a>
            </li>
        </ol>
    </fieldset>
<?php print form_close()?>
<script type="text/text" id="color_tpl">
	<tr>
    	<td><input type="hidden" name="colorid[]" value="0"/><input type="text" style="width:50px;" name="color[]" id="color" class="text" value=""/></th>
    	<td><input type="text" style="width:50px;" name="stock[]" id="stock" class="text" value=""/></th>
    	<td><input type="file" name="img_{id}" id="img" value=""/>
    		
    		<a href="#" onclick="del_color(this)"><?php print $this->bep_assets->icon('cross') ?></a>
    	</td>
	</tr>
</script>
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
		
		/////////
		$('#add_color').click(function(){
			var tpl=$('#color_tpl').html();
			tpl=tpl.replace('{id}',parseInt(Math.random()*10000));
			$(tpl).appendTo('#color_body');
		});
	};
	function del_color(that){
		var $tr=$('#color_body').find('tr');
		if($tr.length<2){
			alert('至少保留一条颜色');
			return;
		}
		$(that).parents('tr').remove();
	}
</script>