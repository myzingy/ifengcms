<h2><?php print $header?></h2>

<div class="buttons">                
	<a href="<?php print  site_url('yidong/admin/yidong/package_form')?>">
    <?php print  $this->bep_assets->icon('add');?>
    	添加套餐政策
    </a>
</div><br/><br/>

<?php print form_open('yidong/admin/yidong/delete')?>
<input name="clean_classify" type="hidden" value="<?php print $params['classify'];?>" />
<table class="data_grid" cellspacing="0">
    <thead>
        <tr>
            <th>产品</th>
            <th>用户缴款</th>
            <th>月返还话费</th>
            <th>最低消费</th>
            <th>合约期</th>
            <th>每月补交费用</th>
            <th>产品内容</th>
            <th width=10%><?php print form_checkbox('all','select',FALSE)?>选择</th>        
        </tr>
    </thead>
    <tfoot>
        <tr>
            <td colspan=10><?php print $pagination;?></td>
            <td><?php //print form_submit('delete','删除机型','onClick="return confirm(\'确认要删除吗？\');"')?></td>
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
            <td>[<?php print $classify[$row['type']];?>]<?php print $row['chanpin'];?></td>
            <td><?php print $row['yonghujiaokuan'];?></td>
            <td><?php print $row['yuefanhuafei'];?></td>
            <td><?php print $row['zuidixiaofei'];?></td>
            <td><?php print $row['heyueqi'];?></td>
            <td><?php print $row['yuebujiaofei'];?></td>
            <td><?php print $row['chanpinneirong'];?></td>
            <td><?php print $delete?><a href="<?php print site_url('yidong/admin/yidong/package_form/'.$row['id'])?>"><?php print $this->bep_assets->icon('pencil');?></a>
            </td>
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
			$.get("<?php print site_url('yidong/admin/yidong/order/')?>/"+id+"/"+order);
		});
	};
	function switchStatus(id,status){
		$.get('<?php print site_url('yidong/admin/yidong/switchDeviceStatus');?>/'+id+'/'+Math.abs(status-1),function(){
			location.reload();
		});
	}
</script>