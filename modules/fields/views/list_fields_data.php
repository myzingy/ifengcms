<style>
	.data_grid img.pic{width:120px; max-height: 80px;}
</style>
<h2><a href="<?php print site_url('fields/admin/fields/index/');?>">
    	表单管理
    </a>&nbsp;&nbsp;&gt;&gt;&nbsp;&nbsp;<?php print $header?></h2>

<div class="buttons">                
	<a href="<?php print site_url('fields/admin/fields/total/'.$fid);?>">
    <?php print  $this->bep_assets->icon('add');?>
    	统计分析
    </a>
    
    <a target="_blank" href="<?php print site_url('fields/fieldsDataPage/html/'.$fid);?>">
    <?php print  $this->bep_assets->icon('add');?>
    	对外页面地址
    </a>
    <?php if ($fid==10):?>
    <a onclick="getTermData('<?php print site_url('fields/admin/fields/getTermData');?>');">
    <?php print  $this->bep_assets->icon('add');?>
    	更新团队数据
    </a>
    <script>
    	function getTermData(url){
    		$('#content').html('<h2>正在更新数据，请稍等片刻。。。</h2>');
    		$.get(url,function(){
    			location.href=location.href;
    		});
    	}
    </script>
    <?php endif;?>
    <?php if ($fid==13):?>
    <a onclick="getTermData('<?php print site_url('fields/admin/fields/getDuduxyData');?>');">
    <?php print  $this->bep_assets->icon('add');?>
    	更新用户数据
    </a>
    <script>
    	function getTermData(url){
    		$('#content').html('<h2>正在更新数据，请稍等片刻。。。</h2>');
    		$.get(url,function(){
    			location.href=location.href;
    		});
    	}
    </script>
    <?php endif;?>
    <a id="pushMsgButton" >
    <?php print  $this->bep_assets->icon('application_cascade');?>
    	短信群发
    </a>
</div><br/><br/>

<?php print form_open('article/admin/article/switchStatus')?>
<input name="clean_classify" type="hidden" value="<?php print $params['classify'];?>" />
<table class="data_grid" id="data_grid" cellspacing="0">
    <thead>
        <tr>
            <?php foreach($fields as $v):if($v=='ip') continue;?>
            <th><?php print $dbkey[$v]['label']?$dbkey[$v]['label']:$v;?></th>
            <?php endforeach;?>
            <th width=10%><?php print form_checkbox('all','select',FALSE)?>选择</th>        
        </tr>
    </thead>
    <tfoot>
        <tr>
            <td colspan=<?php print count($fields)-1;?>><?php print $pagination;?></td>
            <td><?php print form_button('delete','通过','class="dobut" data="0"')?><?php print form_button('delete','失败','class="dobut" data="1"')?></td>
        </tr>
    </tfoot>
    <tbody>
        <?php foreach($members->result_array() as $row):?>
        <?php $delete  = form_checkbox('select[]',$row['id'],FALSE);$active =  ($row['status']==0?'tick':'cross');?>
        <tr>
	        <?php foreach($fields as $v):if($v=='ip') continue;?>
	            <td><?php
	            $html=$row[$v];
				$v=$dbkey[$v]['label']?$dbkey[$v]['label']:$v;
				if(preg_match('/http:\/\//',$html)){
					$html='<a href="'.$html.'" target="_blank">'.substr($html, -38).'</a>';
				}elseif(strlen($html)>20){
					$html='<span title="'.$html.'">'.mb_substr($html, 0,20,'UTF-8').'...</span>';
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
	            <!--
	            <td><a href="#" onclick="switchStatus('<?php print $row['id']?>','<?php print $row['status']?>')"><?php print $this->bep_assets->icon($active);?></a></td>
	            <td class="middle"><a href="<?php print site_url('article/admin/article/update/'.$row['id'])?>"><?php print $this->bep_assets->icon('pencil');?></a></td>
	            -->
	        <?php endforeach;?>
        <td><?php print $delete?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php print form_close()?>
<div id="generatePasswordWindow" style="height:auto;">
	<table>
		<tr>
			<th width="50%">短信群发</th>
			<th class="right"><a href="javascript:void(0);" id="gpCloseWindow"><?php print $this->bep_assets->icon('cross') ?></a></th>
		</tr>
		<tr>
			<td colspan="2">
				短信剩余条数：<span id="smslastnum" style="color: red;">0</span>
			</td>
		</tr>
		<tr>
			<td id="classify_form" colspan="2">
				<input type="hidden" name="id" id="classID" />
				发送地址 ：
				<select id="sendmsg_key">
					<option value="">手动输入号码</option>
				 <?php foreach($fields as $v):if(in_array($v, array('id','ip','status','addtime','fromaddr'))!==false) continue;?>
	            <option value="<?php print $v?>"><?php print $dbkey[$v]['label']?$dbkey[$v]['label']:$v;?></option>
	            <?php endforeach;?>
	            </select>
			</td>
		</tr>
		<tr id="sendmsg_phones_tr">
			<td colspan="2">
				请输入手机号，多个号码换行输入<br>
				<textarea id="sendmsg_phones" style="width:98%; height:50px;"></textarea>
			</td>
		</tr>
		<tr>
			<td id="classify_form" colspan="2">
				短信内容（建议64字符以内,超出将分多条发出）<br>
				<textarea id="sendmsg_content" style="width:98%; height:50px;"></textarea>
				<p id="content_count_num"></p>
			</td>
		</tr>
		<tr>
			<td colspan="2" >
				<div class="buttons" style="margin-top: 10px; margin-left: 100px;">
					<button type="button" class="positive" id="classSubmit" value="submit">
		    			<?php print $this->bep_assets->icon('key') ?>
		    			提交
		    		</button>
	    		</div>
	    		<div class="buttons" style="margin-top: 10px; margin-left: 50px; display: none;">
	    			<button type="button" class="positive" value="submit">
	    				信息发送中，请稍后。。。
	    			</button>
	    		</div>
			</td>
		</tr>
	</table>
</div>
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
		$('.dobut').click(function(){
			var status=$(this).attr('data');
			var tab='<?php echo $tab_index;?>';
			$('form').attr('action','<?php print site_url('/fields/admin/fields/switchStatus')?>/'+tab+'/'+status);
			$('form').trigger('submit');
		});
		$('#sendmsg_key').change(function(){
			if(this.value!=""){
				$('#sendmsg_phones_tr').hide();
			}else{
				$('#sendmsg_phones_tr').show();
			}
		});
		$('#pushMsgButton').click(function(){
			$('#generatePasswordWindow').find('input').val('');
			$('#generatePasswordWindow').attr('type','create').show();
		});
		var sendMsgAction=function(newdata){
			var url="<?php print site_url('fields/admin/fields/act/sendMsg')?>";
			var data = jQuery.extend({
		        'fid':'<?php print $fid;?>',
				'dbkey':$('#sendmsg_key').val(),
				'phones':$.trim($('#sendmsg_phones').val()),
				'content':$.trim($('#sendmsg_content').val()),
				'offset':0,
				'total':0     
		    }, newdata);  
			$.ajax({
				url:url,
				data:data,
				type:'post',
				dataType:'json',
				success:function(json){
					if(json.status==0){
						$('#generatePasswordWindow').find('.buttons:last button').html('马力全开，已发送'+json.data.offset+'/'+json.data.total+'条');
						if(json.data.isnext){
							return setTimeout(function(){sendMsgAction(json.data)},500);
						}
					}
					//$('#generatePasswordWindow').find('.buttons:first').show();
					//$('#generatePasswordWindow').find('.buttons:last').hide();
					alert(json.error);
					location.href=location.href;
				}
			});
		};
		$('#classSubmit').click(function(){
			var url="<?php print site_url('fields/admin/fields/act/sendMsg')?>";
			var data={
				'fid':'<?php print $fid;?>',
				'dbkey':$('#sendmsg_key').val(),
				'phones':$.trim($('#sendmsg_phones').val()),
				'content':$.trim($('#sendmsg_content').val())
			};
			if(data.dbkey || data.phones){
				if(!data.content){alert('请填写发送内容！');return;}
				$('#generatePasswordWindow').find('.buttons:first').hide();
				$('#generatePasswordWindow').find('.buttons:last').show();
				sendMsgAction(data);
			}else{
				alert('请填写电话号码！');
			}
		});
		var content_count_fun=function(){
			$('#sendmsg_content').val($('#sendmsg_content').val().replace(/[\r\n ]+/ig,''))
			var num=$('#sendmsg_content').val().length;
			$('#content_count_num').html('已经输入<font color="red">'+num+'</font>个字符。');
		};
		$('#sendmsg_content').keydown(content_count_fun)
			.keyup(content_count_fun)
			.change(content_count_fun)
			.blur(content_count_fun);
		$.getJSON('<?php print site_url('fields/admin/fields/act/checkSMS')?>',function(json){
			$('#smslastnum').html(json.status==0?json.data.last:json.error);
		});
		
	};
</script>