<h3><?php print $header?></h3>
<?php print form_open_multipart('article/admin/article/form/czdgame',array('class'=>'horizontal','id'=>'articleForm'))?>
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
                <label for="credit">涨跌额度（%）</label>
                <input type="text" name="credit" id="credit" class="text" value="<?php print $editinfo['credit']?$editinfo['credit']:2;?>"/>
            </li>
            <li>
                <label for="price">价 格</label>
                <input type="text" name="price" id="price" class="text" value="<?php print (int)($editinfo['price']?$editinfo['price']:10);?>"/>
            </li>
            <li>
                <label for="odds">赔 率（%）</label>
                <input type="text" name="odds" id="odds" class="text" value="<?php print $editinfo['odds']?$editinfo['odds']:100;?>"/>
            </li>
            <li>
                <label for="limit">下注限额（元）</label>
                <input type="text" name="limit" id="limit" class="text" value="<?php print $editinfo['limit']?$editinfo['limit']:10000;?>"/>
            </li>
            <li>
                <label for="limitrate">限额比率（%）</label>
                <input type="text" name="limitrate" id="limitrate" class="text" value="<?php print $editinfo['limitrate']?$editinfo['limitrate']:80;?>"/>
            </li>
            <!--
            <li>
                <label for="bettime">押注时间</label>
                <select name="bettime" id="bettime">
                	<option value="0">下午3:30-下个收盘日零点</option>
                </select>
            </li>
            -->
            <li>
            	<label for="src">游戏 图标(120*120)<br>(jpg|jpeg|png|bmp)
                <?php if($editinfo['src']):?>
                <img style="margin-top: -20px; margin-left: 10px;" width="50" height="50" src="<?php print base_url().$editinfo['src'];?>" />
                <?php endif;?>	
                </label>
                <input type="file" name="src" id="src" class="text"/>
            </li>
            <!--
            <li>
                <label for="thumb2">游戏 图标(48pix)
                <?php if($editinfo['thumb'][2]):?>
                <img width="50" height="50" src="<?php print base_url().$editinfo['thumb'][2];?>" />
                <?php endif;?>	
                </label>
                <input type="file" name="thumb2" id="thumb2" class="text"/>
            </li>
            <li>
                <label for="thumb3">游戏 图标(72pix)
                <?php if($editinfo['thumb'][3]):?>
                <img width="50" height="50" src="<?php print base_url().$editinfo['thumb'][3];?>" />
                <?php endif;?>	
                </label>
                <input type="file" name="thumb3" id="thumb3" class="text"/>
            </li>
            -->
            <li>
                <label for="subject">专业行情分析-标题</label>
                <input type="text" name="subject" id="subject" class="text" value="<?php print $editinfo['subject'];?>"/>
            </li>
            <li>
                <label for="content">专业行情分析-内容</label>
                <textarea name="content" class="text"><?php print $editinfo['content'];?></textarea>
                <label for="content" generated="true" class="error"></label>
            </li>
            <li class="submit">
            	<div class="buttons">
            		<button type="submit" class="positive" name="submit" value="submit">
            			<?php print $this->bep_assets->icon('key') ?>
            			提交
            		</button>
            		<a href="<?php print site_url('article/admin/article/index/czdgame') ?>" class="negative">
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
				$.getJSON("<?php print site_url('article/admin/article/checkcode/czdgame');?>/"+code+'/'+id,function(json){
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