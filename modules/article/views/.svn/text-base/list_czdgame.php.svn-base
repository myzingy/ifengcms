<h2><?php print $header?></h2>

<div class="buttons">                
	<a href="<?php print  site_url('article/admin/article/form/czdgame')?>">
    <?php print  $this->bep_assets->icon('add');?>
    	添加游戏
    </a>
</div><br/><br/>

<?php print form_open('article/admin/article/delete/czdgame')?>
<table class="data_grid" cellspacing="0">
    <thead>
        <tr>
            <th width=5%><?php print $this->lang->line('general_id')?></th>
            <th width=45%>游戏名称</th>
            <th>价格</th>
            <th>赔率</th>
            <th>涨跌额度</th>
            <th>上架/下架</th>
            <th>修改</th>
            <th width=10%><?php print form_checkbox('all','select',FALSE)?><?php print $this->lang->line('general_delete')?></th>        
        </tr>
    </thead>
    <tfoot>
        <tr>
            <td colspan=7><?php print $pagination;?></td>
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
            <td><?php print $row['title']?>(<?php print $row['keycode']?>)</td>
            <td><?php print $row['price']?></td>
            <td><?php print $row['odds']?>%</td>
            <td><?php print $row['credit']?>%</td>
            <td><a href="#" onclick="switchStatus('<?php print $row['id']?>','<?php print $row['title']?>(<?php print $row['keycode']?>)','<?php print $row['status']?>')"><?php print $this->bep_assets->icon($active);?></a></td>
            <td class="middle"><a href="<?php print site_url('article/admin/article/form/czdgame/'.$row['id'])?>"><?php print $this->bep_assets->icon('pencil');?></a></td>
            <td><?php print $delete?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php print form_close()?>
<div class="buttons">                
	<a href="<?php print  site_url('article/admin/article/index/gamegroup')?>">
    <?php print  $this->bep_assets->icon('arrow_left');?>
    	返回游戏种类
    </a>
</div>
<script type="text/javascript">
	function switchStatus(id,title,status){
		if(confirm('确定要将'+title+(status==0?'下架':'上架')+'吗？')){
			$.get('<?php print site_url('article/admin/article/switchStatus/');?>/'+id+'/'+Math.abs(status-1),function(){
				location.href='<?php print site_url('article/admin/article/index/czdgame');?>';
			});
		}
	}
</script>