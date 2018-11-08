<h2><?php print $header?></h2>

<div class="buttons">                
	<a href="<?php print  site_url('vote_zh/admin/vote/update')?>">
    <?php print  $this->bep_assets->icon('add');?>
    	添加投票
    </a>
</div><br/><br/>

<?php print form_open('vote_zh/admin/vote/delete')?>
<table class="data_grid" cellspacing="0">
    <thead>
        <tr>
            <th width=5%><?php print $this->lang->line('general_id')?></th>
            <th width=50%>活动名称</th>
            <th>号段</th>
            <th>时间</th>
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
            <td><a href="<?php print site_url('vote_zh/admin/vote/votedata/'.$row['id']);?>"><?php print $row['title']?></a></td>
            <td><?php print $row['snum']?>-<?php print $row['enum']?></td>
            <td><?php print date("Y-m-d H时",$row['stime'])?> 至 <?php print date("Y-m-d H时",$row['etime'])?></td>
            <td>
            	<a target="_blank" href="<?php print site_url('vote_zh/display/list/'.$row['id']);?>">查看</a>
            	&nbsp;|&nbsp;
            	<a target="_blank" href="<?php print site_url('vote_zh/ifengvotereldata?rand=&vid='.$row['id']);?>" title="统计">统计 </a>
            	&nbsp;|&nbsp;
            	<a href="<?php print site_url('vote_zh/admin/vote/update/'.$row['id']);?>" title="编辑"><?php print  $this->bep_assets->icon('pencil');?></a>
            	&nbsp;|&nbsp;
            	<a href="#" onclick="deleteField('<?php print $row['id']?>');" title="删除"><?php print  $this->bep_assets->icon('cross');?></a></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php print form_close()?>
<script type="text/javascript">
	pageinit=function(){
		
	};
</script>