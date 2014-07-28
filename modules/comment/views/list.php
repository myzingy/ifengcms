<h2><?php print $header?></h2>

<div class="buttons">                
    <a href="#">
    	分类：
    	<select id="serSelectedClassify">
    	<option value="">全部</option>
    	<?php foreach($classify as $i=>$val):?>
    		<option value="<?php print $i;?>" <?php print ($params['ae-C.type']==$i)?'selected':'';?> ><?php print $val;?></option>
    	<?php endforeach;?>
    </select>
    评论内容：
    <input id="serSubject" value="<?php print $params['al-C.subject']?>" />
    新闻标题：
    <input id="serTitle" value="<?php print $params['title']?>" />
    
    <?php print  $this->bep_assets->icon('arrow_refresh');?>
    	<span id="serButton">搜 索</span>
    </a>
</div><br/><br/>

<?php print form_open('special/admin/special/delete')?>
<table class="data_grid" cellspacing="0">
    <thead>
        <tr>
            <th width=5%><?php print $this->lang->line('general_id')?></th>
            <th width=60%>评论内容</th>
            <th>评论时间</th>
            <th>状态</th>
            <th width=10%><?php print form_checkbox('all','select',FALSE)?><?php print $this->lang->line('general_delete')?></th>        
        </tr>
    </thead>
    <tfoot>
        <tr>
            <td colspan=4><?php print $pagination;?></td>
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
            <td><?php print $row['subject']?></td>
            
            <td><?php print date("Y-m-d H:i:s",$row['addtime'])?></td>
            <td><a href="#" onclick="switchStatus('<?php print $row['id']?>','<?php print $row['status']?>')"><?php print $this->bep_assets->icon($active);?></a></td>
            <td><?php print $delete?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php print form_close()?>
<script type="text/javascript">
	pageinit=function(){
		$('#serButton').click(function(){
			var classify=$('#serSelectedClassify').val();
			var title=$.trim($('#serTitle').val());
			var subject=$.trim($('#serSubject').val());
			var url='<?php print site_url('comment/admin/comment/index')?>';
			if(classify){
				url+='/ae-C.type/'+classify;
			}
			if(title){
				url+='/title/'+title;
			}
			if(subject){
				url+='/al-C.subject/'+subject;
			}
			location.href=url;
		});
	};
	function switchStatus(id,status){
		$.get('<?php print site_url('comment/admin/comment/switchStatus');?>/'+id+'/'+Math.abs(status-1),function(){
			location.reload();
		});
	}
</script>