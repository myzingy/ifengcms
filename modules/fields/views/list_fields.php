<h2><?php print $header?></h2>
<?php if(check('diyfields','update',false)):?>
<div class="buttons">                
	<a target="_blank" href="<?php print base_url().'fields/index.html';?>">
    <?php print  $this->bep_assets->icon('add');?>
    	添加表单
    </a>
</div><br/><br/>
<?php endif;?>
<table class="data_grid" cellspacing="0">
    <thead>
        <tr>
            <th width=5%><?php print $this->lang->line('general_id')?></th>
            <th width=55%>表单名称</th>
            <th>开始时间</th>
            <th>结束时间</th>
            <th>操作</th>       
        </tr>
    </thead>
    <tfoot>
        <tr>
            <td colspan=5><?php print $pagination;?></td>
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
            <td><?php print anchor('fields/admin/fields/formdata/'.$row['id'],$row['name'],'')?></td>
            <td><?php print date("Y-m-d H:i:s",$row['stime'])?></td>
            <td><?php print date("Y-m-d H:i:s",$row['etime'])?></td>
            <td>
            	<?php print anchor('fields/admin/fields/formdata/'.$row['id'],'数据')?>
            	&nbsp;|&nbsp;
            	<?php print anchor('fields/admin/fields/total/'.$row['id'],'统计')?>
            	<?php if(check('diyfields','update',false)):?>
            	&nbsp;|&nbsp;
            	<a target="_blank" href="<?php print base_url().'fields/index.html?id='.$row['id'];?>" title="编辑"><?php print  $this->bep_assets->icon('pencil');?></a>
            	<a target="_blank" href="<?php print base_url().'fields/index.html?id='.$row['id'];?>#step3" title="代码"><?php print  $this->bep_assets->icon('page');?></a>
            	&nbsp;|&nbsp;
            	<a href="#" onclick="deleteField('<?php print $row['id']?>');" title="删除"><?php print  $this->bep_assets->icon('cross');?></a>
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
</script>