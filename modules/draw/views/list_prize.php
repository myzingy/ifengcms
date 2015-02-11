<h2><?php print $header?></h2>
<?php if(check('draw','update',false)):?>
<div class="buttons">                
	<a href="<?php print site_url('draw/admin/draw/form_prize');?>">
    <?php print  $this->bep_assets->icon('add');?>
    	添加奖品
    </a>
</div><br/><br/>
<?php endif;?>
<?php print form_open('draw/admin/draw/delete_prize')?>
<table class="data_grid" cellspacing="0">
    <thead>
        <tr>
            <th width=5%><?php print $this->lang->line('general_id')?></th>
            <th width=30%>名称</th>
            <th>发行量</th>
            <th>剩余库存</th>
            <th>中奖概率</th>
            <th>操作</th>
            <th><?php print form_checkbox('all','select',FALSE)?>全选</th>       
        </tr>
    </thead>
    <tfoot>
        <tr>
            <td colspan=6><?php print $pagination;?></td>
            <td><?php print form_submit('delete','删除奖品','onClick="return confirm(\'确认要删除吗？\');"')?></td>
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
            <td><?php print $row['total']?></td>
            <td><?php print $row['stock']?></td>
            <td><?php print $row['gailv']?>/万(<?php print (round($row['gailv']/100,2));?>%)</td>
            <td>
            	<?php if(check('draw','update',false)):?>
            	<a href="<?php print site_url('draw/admin/draw/form_prize/'.$row['id']);?>" title="编辑"><?php print  $this->bep_assets->icon('pencil');?></a>
            	<?php endif;?>
            </td>
            <td>
            	<?php if(check('draw','update',false)):?>
            	<?php print $delete?>
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
	function deleteField(id){
		if(window.confirm('确认要删除吗？')){
			$.getJSON('<?php print site_url('draw/admin/draw/deletePrize');?>/'+id+'/'+Math.abs(status-1),function(json){
				if(json.status!=0){
					alert(json.error);
					return;
				}
				//location.reload();
			});
		}
	}
</script>