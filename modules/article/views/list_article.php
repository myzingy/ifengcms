<h2><?php print $header?></h2>

<div class="buttons">                
	<a href="<?php print  site_url('article/admin/article/update')?>">
    <?php print  $this->bep_assets->icon('add');?>
    	添加新闻
    </a>
    <a target="_blank" href="<?php print  site_url('article/admin/article/xwzwb')?>">
    <?php print  $this->bep_assets->icon('page');?>
    	导出彩信
    </a>
    <a href="#" style="float: right; display: inline-block;">
    	分类：
    	<select id="serSelectedClassify">
    	<option value="">全部</option>
    	<?php foreach($classify as $i=>$val):?>
    		<option value="<?php print $i;?>" <?php print ($params['classify']==$i)?'selected':'';?> ><?php print $val;?></option>
    	<?php endforeach;?>
    </select>
    标题：
    <input id="serTitle" value="<?php print $params['al-A.title']?>" />
    
    <?php print  $this->bep_assets->icon('arrow_refresh');?>
    	<span id="serButton">搜 索</span>
    </a>
</div><br/><br/>

<?php print form_open('article/admin/article/delete')?>
<input name="clean_classify" type="hidden" value="<?php print $params['classify'];?>" />
<table class="data_grid" cellspacing="0">
    <thead>
        <tr>
            <th width=5%><?php print $this->lang->line('general_id')?></th>
            <th width=55%>新闻标题</th>
            <th>排序</th>
            <th>发布时间</th>
            <th>状态</th>
            <th>修改</th>
            <th width=10%><?php print form_checkbox('all','select',FALSE)?>选择</th>        
        </tr>
    </thead>
    <tfoot>
        <tr>
            <td colspan=3><?php print $pagination;?></td>
            <td colspan=3 style="text-align: right;"><?php print $params['classify']?form_submit('clean','清理此分类','onClick="return true;"'):'';?></td>
            <td><?php print form_submit('delete','删除资讯','onClick="return confirm(\'确认要删除吗？\');"')?></td>
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
            <td><?php print anchor('xwzwb/show/'.$row['id'],$row['title'],'target="_blank"');?></td>
            
            <td><input rowid="<?php print $row['id']?>" class="order" size="4" value="<?php print $row['order']?>" /></td>
            <td><?php print date("Y-m-d H:i:s",$row['addtime'])?></td>
            <td><?php print $this->bep_assets->icon($active);?></td>
            <td class="middle"><a href="<?php print site_url('article/admin/article/update/'.$row['id'])?>"><?php print $this->bep_assets->icon('pencil');?></a></td>
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
			$.get("<?php print site_url('article/admin/article/order/')?>/"+id+"/"+order);
		});
		$('#serButton').click(function(){
			var classify=$('#serSelectedClassify').val();
			var title=$.trim($('#serTitle').val());
			var url='<?php print site_url('article/admin/article/articleList')?>';
			if(title){
				url+='/al-A.title/'+title;
			}
			if(classify){
				url+='/classify/'+classify;
			}
			location.href=url;
		});
		$('#serSelectedClassify').change(function(){
			$('#serButton').trigger('click');
		});
	};
</script>