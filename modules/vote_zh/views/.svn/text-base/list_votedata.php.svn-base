<h2><a href="<?php print site_url('vote/admin/vote/index/');?>">
    	投票管理
    </a>&nbsp;&nbsp;&gt;&gt;&nbsp;&nbsp;<?php print $header?></h2>

<div class="buttons">                
	<a href="<?php print  site_url('vote/admin/vote/updatevotedata/'.$vid)?>">
    <?php print  $this->bep_assets->icon('add');?>
    	手动添加数据
    </a>
    <a href="<?php print  site_url('vote/admin/vote/autovotedata/'.$vid)?>">
    <?php print  $this->bep_assets->icon('lightning');?>
    	自动导入数据
    </a>
    <a target="_blank" href="<?php print  site_url('vote/ifengvotedata/json/'.$vid.'?limit=10&callback=jQuery1234567890')?>">
    <?php print  $this->bep_assets->icon('page');?>
    	外部数据接口
    </a>
</div><br/><br/>

<?php print form_open('vote/admin/vote/delete')?>
<table class="data_grid" cellspacing="0">
    <thead>
        <tr>
            <th width="80">(TP)号码</th>
            <th>名称</th>
            <th>票数</th>
            <th>图片</th>
            <th width="100">操作</th>
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
            <td><?php print $row['code']?$row['code']:$row['id'];?></td>
            <?php if($vote->displayurl):
            $displayurl=str_replace('_remoteid_', $row['remoteid'], $vote->displayurl);
            ?>
            <td><a target="_blank" href="<?php print $displayurl;?>"><?php print $row['name']?></a></td>
            <?php else:?>
            <td><a target="_blank" href="<?php print site_url('vote/display/show/'.$row['vid'].'/'.$row['id']);?>"><?php print $row['name']?></a></td>
            <?php endif;?>
            <td><?php print $row['count']?></td>
            <td><?php print $row['thumb']?></td>
            <td><a href="<?php print site_url('vote/admin/vote/updatevotedata/'.$vid.'/'.$row['id']);?>" title="编辑"><?php print  $this->bep_assets->icon('pencil');?></a>
            	<a href="#" onclick="deleteField('<?php print $row['id']?>');" title="删除"><?php print  $this->bep_assets->icon('cross');?></a></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php print form_close()?>
<script type="text/javascript">
	pageinit=function(){
		
	};
	function deleteField(vmid){
		if(confirm("确认要删除吗？")){
			$('#content').html('<h2>数据处理中。。。</h2>');
			$.get('<?php print site_url('vote/admin/vote/deleteVoteData');?>/'+vmid,function(){
				location.reload();
			});
		}
	}
</script>