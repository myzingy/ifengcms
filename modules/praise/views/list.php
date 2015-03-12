<h2><?php print $header?></h2>
<textarea style="width: 100%; height: 80px; background-color: #252525;color: #0f0;">
&lt;!--//获取点赞数量使用以下代码-->
&lt;script>
$.ajax({
	url:'<?php print base_url()?>index.php/praise/index/{$pkid}',
	dataType:'jsonp',
	jsonp:'jscallback',
	success:function(json){
		if(json.status==0){
			//json.up,json.down
			
		}else{
			//发生错误 json.error
		}
	}
});
&lt;/script>

&lt;!--//点赞使用以下代码-->
&lt;script>
//$act=up|down|0-127;
$.ajax({
	url:'<?php print base_url()?>index.php/praise/index/{$pkid}/{$act}',
	dataType:'jsonp',
	jsonp:'jscallback',
	success:function(json){
		if(json.status==0){
			//成功  json.up,json.down
		}else{
			//发生错误 json.error
		}
	}
});
&lt;/script>
</textarea>
<table class="data_grid" cellspacing="0">
    <thead>
        <tr>
            <th width=30%>PKID</th>
            <th>合计数</th>      
        </tr>
    </thead>
    <tfoot>
        <tr>
            <td colspan=99><?php print $pagination;?></td>
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
            <td><?php print $row['pkid']?></td>
            <td><?php print $row['count']?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php print form_close()?>
<script type="text/javascript">
	pageinit=function(){
		$('textarea').click(function(){
			$(this).height(600);
		});
		$('textarea').mouseout(function(){
			$('textarea').height(80);
		});
	};
</script>