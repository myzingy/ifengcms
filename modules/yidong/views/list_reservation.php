<h2><?php print $header?></h2>

<div class="buttons">                
	<a href="javascript:void(0);" id="searchPhone"><input size="11" id="phone" value="<?php print $params['al-DR.'.$tab_fields['phone']];?>" /><?php print  $this->bep_assets->icon('arrow_refresh');?>电话查找</a>
    &nbsp;&nbsp;&nbsp;&nbsp;  
</div><br>
<h2 style="color:red;">Tip:绿色背景表示用户预约在有效时间内</h2>
<?php print form_open('yidong/admin/yidong/delete')?>
<table class="data_grid" cellspacing="0">
    <thead>
        <tr>
            <th width=5%><?php print $this->lang->line('general_id')?></th>
            <th>DC[ID]</th>
            <th>手机号</th>
            <th>预约详细</th>
            <th>预约时间</th>
            <th>状态</th>      
        </tr>
    </thead>
    <tfoot>
        <tr>
            <td colspan=10><?php print $pagination;?></td>
        </tr>
    </tfoot>
    <tbody>
        <?php foreach($members->result_array() as $row):
            // Check if this user account belongs to the person logged in
            // if so don't allow them to delete it, also if it belongs to the main
            // admin user don't allow them to delete it
            $delete  = form_checkbox('select[]',$row['id'],FALSE);  
			
			$active =  ($row['status']==0?'tick':'cross');
			$bgcolor=  ($row['addtime']>TIME-$timeout)?'style="background:#339933;color:#fff;"':'';
        ?>
        <tr <?php print $bgcolor;?>>
            <td><?php print $row['id']?></td>
            <td><?php print $row[$tab_fields['did']];?>-<?php print $row[$tab_fields['cid']];?></td>
            <td><?php print $row[$tab_fields['phone']];?></td>
            <td><?php print preg_replace("/[\n]/", '<br>', $row[$tab_fields['info']]);?></td>
            <td><?php print date("Y-m-d H:i",$row['addtime']);?></td>
            <?php if($row['status']==1):?>
            	<td><a href="#" onclick="switchStatus('<?php print $row['id']?>','<?php print $row['status']?>')"><?php print $this->bep_assets->icon($active);?></a></td>
            <?php else:?>
            	<td><?php print $this->bep_assets->icon($active);?>已领取</td>
            <?php endif;?>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php print form_close()?>
<script type="text/javascript">
	pageinit=function(){
		$('#phone').click(function(e){
			e.stopPropagation();
			e.preventDefault();
			return false;
		});
		$('#searchPhone').click(function(){
			var phone=$('#phone').val();
			var url='<?php print site_url('yidong/admin/yidong/reservation/')?>';
			if(phone){
				url+='/al-DR.<?php print $tab_fields['phone'];?>/'+phone;
			}
			location.href=url;
		});
	};
	function switchStatus(id,status){
		$.get('<?php print site_url('yidong/admin/yidong/switchReserStatus');?>/'+id+'/'+Math.abs(status-1),function(){
			location.reload();
		});
	}
</script>