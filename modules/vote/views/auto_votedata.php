<h2><?php print $header?></h2>
Tip：数据源中审核通过的数据才能实现导入！
<?php if($data):foreach($data as $r):?>
<div class="buttons">
	<a>
		<button type="button"><?php print $r->source_fieldname?></button>
	<?php if($r->source_name):?>
		<button style="background-color: #9f9" type="button">名称</button>
	<?php endif;?>
	<?php if($r->source_pic):?>
		<button style="background-color: #9f9" type="button">图片</button>
	<?php endif;?>
	</a>
	<button type="button" class="statrSource" source_id="<?php print $r->source_id?>" run_page="<?php print $r->run_page?>"><?php print  $this->bep_assets->icon('lightning');?>开始导入</button>
    
    
</div>
<a href="#" class="cleanSource" source_id="<?php print $r->source_id?>">解除绑定</a>
<br style="clear:both;">
<?php endforeach;endif;?>
<div style="clear:both;display: block;height: 10px;"></div>
<div id="totaldisplay">
	
</div>
<?php print form_open_multipart('vote/admin/vote/bindsource',array('class'=>'horizontal','id'=>'bindsource'))?>
<div class="buttons">
	<input type="hidden" id="vid" name="vid" value="<?php print $vid;?>" />
	<input type="hidden" id="source_id" name="source_id" />
	<a style="width:300px; border-bottom: 0;">表单名称:<input name="source_fieldname" id="source_fieldname" style="width:190px;"/><button style="float:right;" id="source_fieldname_but" type="button">OK</button></a> 
	<br style="clear:both;">               
	<a style="width:300px;border-bottom: 0;border-top: 0;">名称:<select name="source_name[]" id="source_name" style="width:225px;"><option value="">请选择</option></select>
		<button style="float:right;" id="add_name_but" type="button"><?php print  $this->bep_assets->icon('add');?></button>
	</a> 
	<br style="clear:both;">
	<div id="name_ext"></div>               
	<a style="width:300px;border-bottom: 0;border-top: 0;">图片:<select name="source_pic[]" id="source_pic" style="width:225px;"><option value="">请选择</option></select>
		<button style="float:right;" id="add_thumb_but" type="button"><?php print  $this->bep_assets->icon('add');?></button>
	</a>
	<br style="clear:both;">  
	<div id="thumb_ext"></div>
	 
	<!--
	<br style="clear:both;">               
	<a style="width:300px;border-top: 0;"><label><input name="source_code" id="source_code" type="checkbox" value="1" /> 使用号码段自动填充投票号码</label></a> 
	-->
	<div style="clear:both;display: block;height: 10px;"></div>            
	<a href="#" id="bindSource">
    <?php print  $this->bep_assets->icon('add');?>
    	绑定数据源
    </a>
</div>
<div style="clear:both;display: block;height: 100px;"></div>
<div class="buttons">
	<a href="<?php print  site_url('vote/admin/vote/update/'.$vid)?>">
    <?php print  $this->bep_assets->icon('cog');?>
    	修改投票配置
    </a>            
	<a href="<?php print  site_url('vote/admin/vote/votedata/'.$vid)?>">
    <?php print  $this->bep_assets->icon('arrow_left');?>
    	查看投票数据
    </a>
</div>
<?php print form_close()?>
<script type="text/javascript">
	pageinit=function(){
		$('#source_fieldname_but').click(function(){
			var source_fieldname=$.trim($('#source_fieldname').val());
			if(!source_fieldname){
				alert('请输入表单名称');
				return;
			}
			var opt='<option value="">请选择</option>';
			$('#source_name,#source_pic').html(opt);
			$('#source_id').val('');
			//$('#source_code').removeAttr('checked');
			var url='<?php print site_url('fields/getInfo/al-F.name/')?>/'+source_fieldname;
			if(source_fieldname>0){
				url='<?php print site_url('fields/getInfo/ae-F.id/')?>/'+source_fieldname;
			}
			$.getJSON(url,function(json){
				if(json.status!=0){
					alert('没有找到 '+source_fieldname+' 相关表单');
				}else{
					for(var i in json.data.fields_json){
						opt+='<option value="'+i+'">'+json.data.fields_json[i].label+'</option>';
					}
					$('select').html(opt);
					$('#source_id').val(json.data.id);
					$('#source_fieldname').val(json.data.name);
				}
			});
		});
		$('#bindSource').click(function(){
			if($('#source_id').val() && ($('#source_name').val() || $('#source_pic').val())){
				$('#bindsource').ajaxSubmit({
					dataType:'json',
					success:function(json){
						if(json.status==0){
							location.reload();
						}else{
							alert(json.error);
						}
					}
				});
			}else{
				alert('名称、图片必须选择一个');
			}
		});
		$('.cleanSource').click(function(){
			if(confirm('确定要解除此绑定吗？')){
				var source_id=$(this).attr('source_id');
				$.get('<?php print site_url('vote/admin/vote/unbindsource/'.$vid)?>/'+source_id,function(){
					location.reload();
				});
			}
		});
		var page=0;
		var getData=function(source_id){
			var url='<?php print site_url('vote/admin/vote/startSource/'.$vid)?>/'+source_id+'/page/'+page;
			$.ajax({
				url:url,
				dataType:'json',
				success:function(json){
					if(json.status!=0){
						$('button').removeAttr('disabled');
						alert(json.error);
						return;
					}
					$rows=$('#totaldisplay').find('#rows');
					$rows_html='共计'+json.rows+'条，已导入 '+(json.limit>json.rows?json.rows:json.limit)+'/'+json.rows;
					if($rows.length<1){
						$('#totaldisplay').html('<h3 id="rows">'+$rows_html+'</h3>');
					}else{
						$rows.html($rows_html);
					}
					if(!json.isnext){
						$('#totaldisplay').css('background','#33ee33')
						$('button').removeAttr('disabled');
					}else{
						page=parseInt(json.limit);
						setTimeout(function(){getData(source_id);},0);
					}
				}
			});
		};
		$('.statrSource').click(function(){
			var source_id=$(this).attr('source_id');
			//page=parseInt($(this).attr('run_page'));
			$('button').attr('disabled','disabled');
			$('#totaldisplay').css('background','#eeee33').html('开始导入。。。');
			getData(source_id);
			
		});
		$('#add_thumb_but').click(function(){
			var html='<a style="width:272px;border-bottom: 0;border-top: 0;padding-left: 35px;"><select name="source_pic[]" style="width:225px;">{option}</select></a><br style="clear:both;">';
			html=html.replace('{option}',$('#source_pic').html());
			$(html).appendTo('#thumb_ext');
		});
		$('#add_name_but').click(function(){
			var html='<a style="width:272px;border-bottom: 0;border-top: 0;padding-left: 35px;"><select name="source_name[]" style="width:225px;">{option}</select></a><br style="clear:both;">';
			html=html.replace('{option}',$('#source_pic').html());
			$(html).appendTo('#name_ext');
		});
	};
</script>