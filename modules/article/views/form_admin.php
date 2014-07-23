<style type="text/css">
#classify_dispaly label{ width:80px; border: 1px solid #ccc; padding: 3px; display: inline-block;}
#classify_dispaly label.error{vertical-align:top;width:auto; border: 0px;}
</style>
<h3><?php print $header?></h3>
<?php print form_open_multipart('article/admin/article/update',array('class'=>'horizontal','id'=>'articleForm'))?>
    <input type="hidden" name="id" value="<?php print $editinfo['id'];?>" />
    <input type="hidden" name="type" value="1" />
    <fieldset>
        <ol>
            <li>
                <label for="title">新闻标题</label>
                <input type="text" name="title" id="title" class="text" value="<?php print $editinfo['title'];?>"/>
            </li>
            <li>
                <label for="source">新闻来源</label>
                <input type="text" name="source" id="source" class="text" value="<?php print $editinfo['source'];?>"/>
            </li>
            <li>
            	<label for="farmtype">新闻分类</label>
            	<span id="classify_dispaly">
                <?php
                $selectedArr=$editinfo['classify'];//文章选中的分类
				foreach($classify as $pos=>$v):
				$checked=(in_array($pos, $selectedArr))?'checked="checked"':'';	
                ?>
                <label><input type="checkbox" name="classify[]" <?php print $checked;?> value="<?php echo $pos;?>"/><?php echo $v;?></label>
                <?php
                endforeach;
                ?>
                <label for="classify[]" generated="true" class="error"></label>
                </span>
                <div class="buttons" style="display: inline-block;"><a href="#" id="new_classify" class="positive">
                	<?php print  $this->bep_assets->icon('add');?>
                	新建分类</a></div>
                
            </li>
            <li>
                <label for="thumb1">新闻图片(320*240)<br>(jpg|jpeg|png|bmp)
                <?php if($editinfo['src']):?>
                <img style="margin-top: -20px; margin-left: 10px;" width="50" height="50" src="<?php print base_url().$editinfo['src'];?>" />
                <?php endif;?>	
                </label>
                <input type="file" name="src" id="src" class="text"/>
            </li>
            <li>
                <label for="content">新闻详情</label>
                <textarea name="content" class="text"><?php print $editinfo['content'];?></textarea>
                <label for="content" generated="true" class="error" style="display: inline-block;"></label>
            </li>
            <li class="submit">
            	<div class="buttons">
            		<button type="submit" class="positive" name="submit" value="submit">
            			<?php print $this->bep_assets->icon('key') ?>
            			提交
            		</button>
            		<a href="<?php print site_url('article/admin/article/articleList') ?>" class="negative">
            			<?php print  $this->bep_assets->icon('cross');?>
            			取消
            		</a>
            </li>
        </ol>
    </fieldset>
<?php print form_close()?>
<div id="generatePasswordWindow">
	<table>
		<tr>
			<th width="50%">新闻分类</th>
			<th class="right"><a href="javascript:void(0);" id="gpCloseWindow"><?php print $this->bep_assets->icon('cross') ?></a></th>
		</tr>
		<tr>
		<tr>
			<td id="classify_form" colspan="2">
				<input type="hidden" name="id" id="classID" />
				分类名称 ：<input name="name" id="className" />
			</td>
		</tr>
		<tr>
			<td colspan="2" >
				<div class="buttons" style="margin-top: 10px; margin-left: 100px;">
				<button type="button" class="positive" id="classSubmit" value="submit">
	    			<?php print $this->bep_assets->icon('key') ?>
	    			提交
	    		</button>
	    		</div>
			</td>
		</tr>
	</table>
</div>
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
		$('#new_classify').click(function(){
			$('#generatePasswordWindow').show();
		});
		$('#classSubmit').click(function(){
			var id=$('#classID').val();
			var name=$.trim($('#className').val());
			if(name){
				$.ajax({
					url:"<?php echo site_url('article/admin/article/classify')?>",
					data:"id="+id+"&name="+name,
					dataType:"json",
					type:"POST",
					success:function(json){
						$('<label><input type="checkbox" name="classify[]" value="'+json.id+'"/>'+name+'</label>').appendTo('#classify_dispaly');
						$('#generatePasswordWindow').hide();
					}
				});
				
			}
			
		});
	};
</script>