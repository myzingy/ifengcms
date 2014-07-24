<h2><?php print $header?></h2>

<div class="buttons">                
	<a href="<?php print  site_url('special/admin/special/update')?>">
    <?php print  $this->bep_assets->icon('add');?>
    	添加专题
    </a>
</div><br/><br/>

<?php print form_open('special/admin/special/delete')?>
<table class="data_grid" cellspacing="0">
    <thead>
        <tr>
            <th width=5%><?php print $this->lang->line('general_id')?></th>
            <th width=50%>专题名称</th>
            <th>点击查看</th>
            <th>发布时间</th>
            <th>状态</th>
            <th>修改</th>
            <th width=10%><?php print form_checkbox('all','select',FALSE)?><?php print $this->lang->line('general_delete')?></th>        
        </tr>
    </thead>
    <tfoot>
        <tr>
            <td colspan=6><?php print $pagination;?></td>
            <td><?php print form_submit('delete','删除','onClick="return confirm(\'确认要删除吗？\');"')?></td>
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
            
            <td><a target="_blank" href="<?php print base_url().$row['url']?>">右键复制链接地址</a></td>
            <td><?php print date("Y-m-d H:i:s",$row['addtime'])?></td>
            <td><?php print $this->bep_assets->icon($active);?></td>
            <td class="middle"><a href="<?php print site_url('special/admin/special/update/'.$row['id'])?>"><?php print $this->bep_assets->icon('pencil');?></a></td>
            <td><?php print $delete?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php print form_close()?>
<script type="text/javascript">
	pageinit=function(){
		
	};
</script>