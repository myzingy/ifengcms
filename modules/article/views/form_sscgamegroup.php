<h3><?php print $header?></h3>
<?php print form_open_multipart('article/admin/article/form/sscgamegroup',array('class'=>'horizontal','id'=>'articleForm'))?>
    <input type="hidden" name="id" value="<?php print $editinfo['id'];?>" />
    <fieldset>
        <ol>
            <li>
                <label for="title">游戏名称</label>
                <input type="text" name="title" id="title" class="text" value="<?php print $editinfo['title'];?>"/>
            </li>
            <li>
                <label for="award">奖池返奖率(%)</label>
                <input type="text" name="award" id="award" class="text" value="<?php print $editinfo['award'];?>"/>
            </li>
            <li>
                <label for="cycle">开奖周期（分钟）</label>
                <input type="text" name="cycle" id="cycle" class="text" value="<?php print $editinfo['cycle'];?>"/>
            </li>
            <li>
                <label for="opentime">开奖时长（分钟）</label>
                <input type="text" name="opentime" id="opentime" class="text" value="<?php print $editinfo['opentime'];?>"/>
            </li>
            <li>
                <label for="marquee">跑马灯</label>
                <input type="text" name="marquee" id="marquee" class="text" value="<?php print $editinfo['marquee'];?>"/>
            </li>
            <li>
                <label for="ratio">2-21数字返奖额</label>
                <table>
                	<tr>
                <?php foreach($ssc_radio as $i=>$radio):?>
                	<?php 
                		if($i%4==0 && $i!=21){
                			echo '</tr><tr>';
                		}
                	?>
                	<td style="width:150px; text-align: right;"><?php print $i;?>:<input type="text" style="width: 40px;" name="ratio_<?php print $i;?>" class="text" value="<?php print $radio;?>"/></td>
                		
                <?php endforeach;?>
                </tr>
                </table>
            </li>
            
            <li>
                <label for="thumb1">游戏 图标(120*120)<br>(jpg|jpeg|png|bmp)
                <?php if($editinfo['src']):?>
                <img style="margin-top: -20px; margin-left: 10px;" width="50"  height="50" src="<?php print base_url().$editinfo['src'];?>" />
                <?php endif;?>
                
                </label>
                <input type="file" name="src" id="src" class="text" title="(jpg|jpeg|png|bmp)"/>
                
            </li>
            <li>
                <label for="content">游戏说明</label>
                <textarea name="content" class="text"><?php print $editinfo['content'];?></textarea>
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
            <li class="submit">
            	<div class="buttons">
            		<button type="submit" class="positive" name="submit" value="submit">
            			<?php print $this->bep_assets->icon('key') ?>
            			提交
            		</button>
            		<a href="<?php print site_url('article/admin/article/index/gamegroup') ?>" class="negative">
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
	};
</script>