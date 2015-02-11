<h2><?php print $header?></h2>
<textarea style="width: 100%; height: 80px; background-color: #252525;color: #0f0;">
&lt;!--//将此JS放入head部分，用户获取用户openid-->
&lt;script type="text/javascript">
var PUBLIC__WECHAT_OPENID = Math.random();
&lt;/script>
&lt;script src="http://cms.wisheli.com/assets/js/getWechatAuth.js"></script>

&lt;!--//参与抽奖请使用以下代码-->
&lt;script>
$.ajax({
	url:'<?php print base_url()?>index.php/api/act/dodraw/{$id}',
	data:'openid='+PUBLIC__WECHAT_OPENID+'&name=vking&phone=12111111113',
	dataType:'jsonp',
	jsonp:'callback',
	success:function(json){
		if(json.status==1){
			//中奖 json.msg
			
		}else if(json.status==0){
			//未中奖
			
		}else{
			//发生错误 json.error
		}
	}
});
&lt;/script>

&lt;!--//获奖者填写信息使用以下代码-->
&lt;script>
$.ajax({
	url:'<?php print base_url()?>index.php/api/act/updateDrawUser/{$id}',
	data:'openid='+PUBLIC__WECHAT_OPENID+'&name=vking&phone=12111111113',
	dataType:'jsonp',
	jsonp:'callback',
	success:function(json){
		if(json.status==0){
			//成功
		}else{
			//发生错误 json.error
		}
	}
});
&lt;/script>
</textarea>
<?php if(check('draw','update',false)):?>
<div class="buttons">                
	<a href="<?php print site_url('draw/admin/draw/form_activity');?>">
    <?php print  $this->bep_assets->icon('add');?>
    	添加抽奖活动
    </a>
</div><br/><br/>
<?php endif;?>
<table class="data_grid" cellspacing="0">
    <thead>
        <tr>
            <th width=5%><?php print $this->lang->line('general_id')?></th>
            <th width=30%>活动名称</th>
            <th>开始时间</th>
            <th>结束时间</th>
            <th>展示数</th>
            <th>参与数</th>
            <th>中奖数</th>
            <th>操作</th>       
        </tr>
    </thead>
    <tfoot>
        <tr>
            <td colspan=99><?php print $pagination;?></td>
        </tr>
    </tfoot>
    <tbody>
        <?php foreach($members->result_array() as $row):
            // Check if this user account belongs to the person logged in
            // if so don't allow them to delete it, also if it belongs to the main
            // admin user don't allow them to delete it
            $delete  = form_checkbox('select[]',$row['id'],FALSE);  
			
			$active =  ($row['status']==0?'tick':'cross');   
        ?>
        <tr>
            <td><?php print $row['id']?></td>
            <td><?php print $row['title']?></td>
            <td><?php print date("Y-m-d H:i:s",$row['stime'])?></td>
            <td><?php print date("Y-m-d H:i:s",$row['etime'])?></td>
            <td><?php print $row['view_num']?></td>
            <td><?php print $row['ack_num']?></td>
            <td><?php print $row['win_num']?></td>
            <td>
            	<?php print anchor('draw/admin/draw/history/'.$row['id'],'数据')?>
            	<?php if(check('draw','update',false)):?>
            	&nbsp;|&nbsp;
            	<a href="<?php print site_url('draw/admin/draw/form_activity/'.$row['id']);?>" title="编辑"><?php print  $this->bep_assets->icon('pencil');?></a>
            	<?php endif;?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php print form_close()?>
<script type="text/javascript">
	pageinit=function(){
		$('textarea').click(function(){
			$(this).height(600);
		});
		$('textarea').mouseout(function(){
			$('textarea').height(80);
		});
	};
</script>