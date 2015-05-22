<h2><a href="<?php print site_url('draw/admin/draw/activity/');?>">
    	活动管理
    </a>&nbsp;&nbsp;&gt;&gt;&nbsp;&nbsp;<?php print $header?></h2>
<div class="buttons">
                 
	<a href="<?php print site_url('draw/admin/draw/history/'.$id.'/agt-DH.pid/0');?>">
    <?php print  $this->bep_assets->icon('add');?>
    	只看中奖者
    </a>
    &nbsp;&nbsp;&nbsp;&nbsp;
    <a href="<?php print site_url('draw/admin/draw/history/'.$id);?>">
    <?php print  $this->bep_assets->icon('add');?>
    	查看全部
    </a>
    
    <a href="javascript:void(0);" id="searchPhone"><input size="11" id="phone" /><?php print  $this->bep_assets->icon('arrow_refresh');?>电话查找</a>
    &nbsp;&nbsp;&nbsp;&nbsp; 
    <a id="activity_clear" class="negative">
		<?php print  $this->bep_assets->icon('error');?>
		清理抽奖数据
	</a>
</div><br/><br/>
<table class="data_grid" cellspacing="0">
    <thead>
        <tr>
            <th width=5%><?php print $this->lang->line('general_id')?></th>
            <th>姓名</th>
            <th>电话</th>
            <th>奖品</th>
            <th>openid</th>   
            <th>时间</th>
            <th>状态</th>  
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
            <td><?php print $row['name']?></td>
            <td><?php print $row['phone']?></td>
            <td><?php print $row['pid']?"[{$row['pid']}] {$row['pname']}":"";?></td>
            <td><?php print $row['openid']?></td>
            <td><?php print date("Y-m-d H:i:s",$row['addtime'])?></td>
            <td>
            	<?php if($row['pid']):?>
            		<?php if($row['status']==0):?>
            			<a href="#" onclick="switchStatus('<?php print $row['id']?>','<?php print $row['status']?>')"><?php print $this->bep_assets->icon($active);?><?php print $row['status']==1?'已领取':''?></a>
            		<?php else:?>
            			<a href="#" ><?php print $this->bep_assets->icon($active);?><?php print $row['status']==1?'已领取':''?></a>
            		<?php endif;?>
            	
            	<?php else:?>
            		&nbsp;
            	<?php endif;?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php print form_close()?>
<script type="text/javascript">
	pageinit=function(){
		$(".order").change(function(){
			var id=$(this).attr('rowid');
			var order=this.value;
			$.get("<?php print site_url('article/admin/article/order/')?>/"+id+"/"+order);
		});
		$('#serButton').click(function(){
			var classify=$('#serSelectedClassify').val();
			var title=$.trim($('#serTitle').val());
			var url='<?php print site_url('article/admin/article/articleList')?>';
			if(title){
				url+='/al-A.title/'+title;
			}
			if(classify){
				url+='/classify/'+classify;
			}
			location.href=url;
		});
		$('#serSelectedClassify').change(function(){
			$('#serButton').trigger('click');
		});
		$('#phone').click(function(e){
			e.stopPropagation();
			e.preventDefault();
			return false;
		});
		$('#searchPhone').click(function(){
			var phone=$('#phone').val();
			var url='<?php print site_url('draw/admin/draw/history/'.$id)?>';
			if(phone){
				url+='/al-DH.phone/'+phone;
			}
			location.href=url;
		});
		
		$('#activity_clear').click(function(){
			if(window.confirm('继续将清理抽奖记录，确定继续吗？')){
				location.href="<?php print site_url('draw/admin/draw/activity_clear/'.$id);?>";
			}
		});
	};
	function deleteField(id){
		if(window.confirm('确认要删除吗？')){
			$.getJSON('<?php print site_url('fields/admin/fields/deleteField');?>/'+id+'/'+Math.abs(status-1),function(json){
				if(json.status!=0){
					alert(json.error);
					return;
				}
				//location.reload();
			});
		}
	}
	function switchStatus(id,status){
		$.get('<?php print site_url('draw/admin/draw/switchStatus');?>/'+id+'/'+Math.abs(status-1),function(){
			location.reload();
		});
	}
</script>