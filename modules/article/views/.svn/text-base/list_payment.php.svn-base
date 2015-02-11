<h2><?php print $header?></h2>
<?php print form_open_multipart('article/admin/article/payment',array('class'=>'horizontal','id'=>'payment'))?>
<table class="data_grid" cellspacing="0">
	<input name="id" type="hidden" value="<?php print $editinfo['id'];?>"/>
	<tr>
		<td>名称:<input name="name" value="<?php print $editinfo['name'];?>"/></td>
		<td>方案描述:<input name="info" value="<?php print $editinfo['info'];?>"/></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>价格:<input name="price" value="<?php print $editinfo['price'];?>"/></td>
		<td>有效期(天):<input name="extday" value="<?php print $editinfo['extday'];?>"/></td>
		<td><button type="submit" class="positive" name="submit" value="submit">
            			<?php print $this->bep_assets->icon('key') ?>
            			<?php print ($editinfo['id']?"修改":"添加");?>方案
            		</button></td>
	</tr>
</table>
<?php print form_close()?>
<?php print form_open('article/admin/article/payment_del',array('class'=>'horizontal','id'=>'paymentdelete'))?>
<input type="hidden" name="select" />
<table class="data_grid" cellspacing="0">
    <tfoot>
        <tr>
            <td colspan=3><?php print $pagination;?></td>
            <!--
            <td><?php print form_submit('delete',$this->lang->line('general_delete'),'onClick="return confirm(\'确定删除吗？\');"')?></td>
        	-->
        </tr>
    </tfoot>
    <tbody>
        <tr>
        <?php 
        $i=0;
        foreach($payment as $row):
        	//$delete  = form_checkbox('select[]',$row['id'],FALSE);  
			if($i%3==0 && $i!=0) echo '</tr><tr>';
			$i++;
        	?>
        
            <td class="middle" style="border: 1px solid #ccc;">
            	<?php /*print $delete*/?>
            	
            	<span style="padding-right: 50px;"><?php print $row['name']?> &nbsp;<?php print $row['price']?>元 &nbsp;<?php print $row['extday']?>天
            		<a href="<?php print site_url('article/admin/article/payment/'.$row['id'])?>"><?php print $this->bep_assets->icon('pencil');?></a>
            		<a href="#" onclick="deletePay('<?php print $row['id']?>')"><?php print $this->bep_assets->icon('cancel');?></a>
            		<br/>方案描述：<?php print $row['info']?></span>
            	
            	
            	
            </td>
        	
        <?php endforeach; ?>
        </tr>
    </tbody>
</table>
<?php print form_close()?>
<script type="text/javascript">
	pageinit=function(){
		jQuery.extend(jQuery.validator.messages, {
	        required: "至少勾选一个"
		});
		$("#payment").validate({
			<?php echo $jsform;?>,
			event: "blur",
			submitHandler: function() {
				document.getElementById("payment").submit();
			}
		});
	};
	function deletePay(id){
		if(confirm('确定删除吗？')){
			$('input[name="select"]').val(id);
			document.getElementById("paymentdelete").submit();
		}
	}
</script>