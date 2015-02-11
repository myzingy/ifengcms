<table class="data_grid" id="data_grid" cellspacing="0" width="100%">
    <thead>
        <tr>
            <?php foreach($fields as $v):if($v=='id' || $v=='ip' || $v=='status') continue;?>
            <th><?php print $dbkey[$v]['label']?$dbkey[$v]['label']:$v;?></th>
            <?php endforeach;?>       
        </tr>
    </thead>
    <tfoot>
        <tr>
            <td colspan=<?php print count($fields);?>><?php print $pagination;?></td>
        </tr>
    </tfoot>
    <tbody>
        <?php foreach($members->result_array() as $row):?>
        <?php $delete  = form_checkbox('select[]',$row['id'],FALSE);$active =  ($row['status']==0?'tick':'cross');?>
        <tr>
	        <?php foreach($fields as $v):if($v=='id' || $v=='ip'|| $v=='status') continue;?>
	            <td><?php
	            $html=$row[$v];
				$v=$dbkey[$v]['label']?$dbkey[$v]['label']:$v;
				if(preg_match('/http:\/\//',$html)){
					$html='<a href="'.$html.'" target="_blank">'.substr($html, -38).'</a>';
				}
				switch($v){
					case 'addtime':
						$html=date('Y-m-d H:i',$html);
					break;
					case 'status':
						$html=$this->bep_assets->icon($active);
					break;
				}
				print $html;
				?></td>
	        <?php endforeach;?>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>