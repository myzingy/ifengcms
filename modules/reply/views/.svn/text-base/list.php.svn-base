<h2>凤凰陕西公众号&nbsp;&nbsp;&gt;&gt;&nbsp;&nbsp;<?php print $header?></h2>

<div class="buttons">                
	<a href="<?php print  site_url('reply/admin/reply/keyword')?>">
    <?php print  $this->bep_assets->icon('add');?>
    	添加微信关键字
    </a>
</div><br/><br/>

<?php print form_open('reply/admin/reply/delete')?>
<table class="data_grid" cellspacing="0">
    <thead>
        <tr>
            <th width=5%><?php print $this->lang->line('general_id')?></th>
            <th>关键字</th>
            <th>回复标题</th>
            <th>回复描述</th>
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
            <td><?php print $row['subject']?></td>
            <td width="50%"><?php print $row['content']?></td>
            <td><?php print $this->bep_assets->icon($active);?></td>
            <td class="middle"><a href="<?php print site_url('reply/admin/reply/keyword/'.$row['id'])?>"><?php print $this->bep_assets->icon('pencil');?></a></td>
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