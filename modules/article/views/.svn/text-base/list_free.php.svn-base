<h2><?php print $header?></h2>

<div class="buttons">                
	<a href="<?php print  site_url('article/admin/article/form/free')?>">
    <?php print  $this->bep_assets->icon('add');?>
    	添加免费资讯
    </a>
</div><br/><br/>

<?php print form_open('article/admin/article/delete/free')?>
<table class="data_grid" cellspacing="0">
    <thead>
        <tr>
            <th width=5%><?php print $this->lang->line('general_id')?></th>
            <th width=55%>推荐名称</th>
            <!--
            <th>资讯类别</th>
            -->
            <th>发布时间</th>
            <th>状态</th>
            <th>修改</th>
            <th width=10%><?php print form_checkbox('all','select',FALSE)?><?php print $this->lang->line('general_delete')?></th>        
        </tr>
    </thead>
    <tfoot>
        <tr>
            <td colspan=5><?php print $pagination;?></td>
            <td><?php print form_submit('delete',$this->lang->line('general_delete'),'onClick="return confirm(\''.$this->lang->line('userlib_delete_user_confirm').'\');"')?></td>
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
            <?php 
            /*
            <td><?php 
            	$pos=explode(",",$row['farmtype']);
				foreach($pos as $key):
				?>
            	<?php print $farmtype[$key]?>&nbsp;
            	<?php endforeach;?></td>
			 * 
			 */
			?>
            <td><?php print date("Y-m-d H:i:s",$row['addtime'])?></td>
            <td><?php print $this->bep_assets->icon($active);?></td>
            <td class="middle"><a href="<?php print site_url('article/admin/article/form/free/'.$row['id'])?>"><?php print $this->bep_assets->icon('pencil');?></a></td>
            <td><?php print $delete?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php print form_close()?>