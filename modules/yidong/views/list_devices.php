<h2><?php print $header?></h2>

<div class="buttons">                
	<a href="<?php print  site_url('yidong/admin/yidong/devices_form')?>">
    <?php print  $this->bep_assets->icon('add');?>
    	添加机型
    </a>
</div><br/><br/>

<?php print form_open('yidong/admin/yidong/delete')?>
<input name="clean_classify" type="hidden" value="<?php print $params['classify'];?>" />
<table class="data_grid" cellspacing="0">
    <thead>
        <tr>
            <th width=5%><?php print $this->lang->line('general_id')?></th>
            <th >机型名称</th>
            <th >用户缴款</th>
            <th width=10%>排序</th>
            <th width=10%>状态</th>
            <th width=10%>操作</th>
            <th width=10%><?php print form_checkbox('all','select',FALSE)?>选择</th>        
        </tr>
    </thead>
    <tfoot>
        <tr>
            <td colspan=10><?php print $pagination;?></td>
            <td><?php //print form_submit('delete','删除机型','onClick="return confirm(\'确认要删除吗？\');"')?></td>
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
            <td><?php print $row['name'];?></td>
            <td><?php print $row['yonghujiaokuan'];?></td>
            <td><input rowid="<?php print $row['id']?>" class="order" size="4" value="<?php print $row['order']?>" />
            </td>
            <td><a href="#" onclick="switchStatus('<?php print $row['id']?>','<?php print $row['status']?>')"><?php print $this->bep_assets->icon($active);?></a>
            </td>
            <td><a href="<?php print site_url('yidong/admin/yidong/devices_form/'.$row['id'])?>"><?php print $this->bep_assets->icon('pencil');?></a>
            </td>
            <td><?php print $delete?></td>
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
			$.get("<?php print site_url('yidong/admin/yidong/order/')?>/"+id+"/"+order);
		});
	};
	function switchStatus(id,status){
		$.get('<?php print site_url('yidong/admin/yidong/switchDeviceStatus');?>/'+id+'/'+Math.abs(status-1),function(){
			location.reload();
		});
	}
</script>