<h2><?php print $header?></h2>
<!--
<div class="buttons">                
	<a href="<?php print  site_url('article/admin/article/form/gamegroup')?>">
    <?php print  $this->bep_assets->icon('add');?>
    	添加游戏种类
    </a>
</div><br/><br/>
-->
<?php print form_open('article/admin/article/delete/gamegroup')?>
<table class="data_grid" cellspacing="0">
    <!--
    <thead>
        <tr>
            <th width=5%><?php print $this->lang->line('general_id')?></th>
            <th width=55%>游戏种类名称</th>
            <th>游戏种类</th>
            <th>信息</th>
            <th>修改</th>
            <th width=10%><?php print form_checkbox('all','select',FALSE)?><?php print $this->lang->line('general_delete')?></th>        
        </tr>
    </thead>
    -->
    <!--
    <tfoot>
        <tr>
            <td colspan=10><?php print form_submit('delete',$this->lang->line('general_delete'),'onClick="return confirm(\''.$this->lang->line('userlib_delete_user_confirm').'\');"')?></td>
        </tr>
    </tfoot>
    -->
    <tbody>
        <tr>
        <?php foreach($members->result_array() as $row):
            // Check if this user account belongs to the person logged in
            // if so don't allow them to delete it, also if it belongs to the main
            // admin user don't allow them to delete it
            //$delete  = form_checkbox('select[]',$row['id'],FALSE);
        ?>
        
            <td class="middle" style="border: 1px solid #ccc;">
            	<a href="<?php print site_url('article/admin/article/index/'.$gametype[$row['type']]['page'])?>" title="点击查看详细" >
            	<img src="<?php print base_url().$row['src'];?>" width="150" height="150" style="border: 1px solid #ccc; padding: 10px;border-top:0;"/>
            	<br />
            	<span style="padding-right: 50px;"><?php print $row['title']?></span>
            	</a>
            	<a href="<?php print site_url('article/admin/article/form/'.$gametype[$row['type']]['page'].'group/'.$row['id'])?>"><?php print $this->bep_assets->icon('pencil');?></a>
            	<?php print $delete?>
            </td>
        
        <?php endforeach; ?>
        </tr>
    </tbody>
</table>
<?php print form_close()?>