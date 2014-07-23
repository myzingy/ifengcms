<h2><?php print $header?></h2>

<div class="buttons">                
	<a href="<?php print  site_url('article/admin/article/form/pricepack')?>">
    <?php print  $this->bep_assets->icon('add');?>
    	添加订阅包
    </a>
</div><br/><br/>

<?php print form_open('article/admin/article/delete/pricepack')?>
<table class="data_grid" cellspacing="0">
    <thead>
        <tr>
            <th width=5%><?php print $this->lang->line('general_id')?></th>
            <th>订阅包名称</th>
            <th width=300>付费方案</th>
            <th width=80>修改</th>
            <th width=10%><?php print form_checkbox('all','select',FALSE)?><?php print $this->lang->line('general_delete')?></th>        
        </tr>
    </thead>
    <tfoot>
        <tr>
            <td colspan=4><?php print $pagination;?></td>
            <td><?php print form_submit('delete',$this->lang->line('general_delete'),'onClick="return confirm(\''.$this->lang->line('userlib_delete_user_confirm').'\');"')?></td>
        </tr>
    </tfoot>
    <tbody>
        <?php foreach($members->result_array() as $row):
            // Check if this user account belongs to the person logged in
            // if so don't allow them to delete it, also if it belongs to the main
            // admin user don't allow them to delete it
            $delete  = form_checkbox('select[]',$row['id'],FALSE);
        ?>
        <tr>
            <td><?php print $row['id']?></td>
            <td><?php print $row['title']?></td>
            <td>
            	<?php
                $farmtypearr=@explode(",",$row['payment']);
                foreach($payment as $pos=>$v):
					if(in_array($pos, $farmtypearr)):
						echo $v['name'].'('.$v['price'].'元 '.$v['extday'].'天)'.$v['info']." <br/> ";
					endif;
				endforeach;
                ?>
            </td>
            <td><a href="<?php print site_url('article/admin/article/form/pricepack/'.$row['id'])?>"><?php print $this->bep_assets->icon('pencil');?></a></td>
            <td><?php print $delete?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php print form_close()?>