<h3><?php print $header?></h3>
<?php print form_open_multipart('article/admin/article/form/sscgame',array('class'=>'horizontal','id'=>'articleForm'))?>
    <input type="hidden" name="id" value="<?php print $editinfo['id'];?>" />
    <fieldset>
        <ol>
            <li>
                <label for="title">游戏名称</label>
                <input type="text" name="title" id="title" class="text" value="<?php print $editinfo['title'];?>"/>
            </li>
            <li>
                <label for="keycode">期货代码</label>
                <input type="text" name="keycode" id="keycode" class="text" value="<?php print $editinfo['keycode'];?>"/>
            </li>
            <li>
                <label for="price">单注价格</label>
                <input type="text" name="price" id="price" class="text" value="<?php print $editinfo['price'];?>"/>
            </li>
            <li>
            	<label for="src">游戏 图标(120*120)<br>(jpg|jpeg|png|bmp)
                <?php if($editinfo['src']):?>
                <img style="margin-top: -20px; margin-left: 10px;" width="50" height="50" src="<?php print base_url().$editinfo['src'];?>" />
                <?php endif;?>	
                </label>
                <input type="file" name="src" id="src" class="text"/>
            </li>
            <li class="submit">
            	<div class="buttons">
            		<button type="submit" class="positive" name="submit" value="submit">
            			<?php print $this->bep_assets->icon('key') ?>
            			提交
            		</button>
            		<a href="<?php print site_url('article/admin/article/index/sscgame') ?>" class="negative">
            			<?php print  $this->bep_assets->icon('cross');?>
            			取消
            		</a>
            </li>
        </ol>
    </fieldset>
<?php print form_close()?>
<script type="text/javascript">
	pageinit=function(){
		
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
				var res=null;
				var id=$('input[name="id"]').val();
				var code=$('#keycode').val();
				$.ajaxSetup({'async':false});
				$.getJSON("<?php print site_url('article/admin/article/checkcode/sscgame');?>/"+code+'/'+id,function(json){
					res=json;
				});
				if(res.status==0){
					document.getElementById("articleForm").submit();
				}else{
					alert(res.error);
				}
				
			}
		});
	};
</script>