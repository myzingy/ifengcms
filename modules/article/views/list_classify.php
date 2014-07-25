<h2><?php print $header?></h2>
<div class="buttons">
	<?php foreach($classify as $i=>$val):?>
	<a href="#" class="positive">
		<span><?php print $val;?></span>
		<?php print  $this->bep_assets->icon('pencil',$i.'" class="update');?>
		<?php print  $this->bep_assets->icon('cross',$i.'" class="delete');?>
	</a>
	<?php endforeach;?>
	<a href="#" id="new_classify" class="negative">
                	<?php print  $this->bep_assets->icon('add');?>
                	新建分类</a>
</div>
<div id="generatePasswordWindow">
	<table>
		<tr>
			<th width="50%">新闻分类</th>
			<th class="right"><a href="javascript:void(0);" id="gpCloseWindow"><?php print $this->bep_assets->icon('cross') ?></a></th>
		</tr>
		<tr>
		<tr>
			<td id="classify_form" colspan="2">
				<input type="hidden" name="id" id="classID" />
				分类名称 ：<input name="name" id="className" />
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
			</td>
		</tr>
	</table>
</div>           
<script type="text/javascript">
	pageinit=function(){
		$('#new_classify').click(function(){
			$('#generatePasswordWindow').find('input').val('');
			$('#generatePasswordWindow').attr('type','create').show();
		});
		$('#classSubmit').click(function(){
			var id=$('#classID').val();
			var name=$.trim($('#className').val());
			if(name){
				$.ajax({
					url:"<?php echo site_url('article/admin/article/classify')?>",
					data:"id="+id+"&name="+name,
					dataType:"json",
					type:"POST",
					success:function(json){
						var type=$('#generatePasswordWindow').attr('type');
						if(type=='update'){
							$('.update[title="'+json.id+'"]').prev().html(name);
						}else{
							var html='<a href="#" class="positive"><span>'+name+'</span>'+
							'<img src="<?php print base_url()?>/assets/icons/pencil.png" alt="pencil" title="'+json.id+'" class="update">'+
							'<img src="<?php print base_url()?>/assets/icons/cross.png" alt="cross" title="'+json.id+'" class="delete"></a>';
							$(html).insertBefore('#new_classify');
						}
						$('#generatePasswordWindow').hide();
					}
				});
				
			}
			
		});
		$('.delete').click(function(){
			var that=this;
			if(confirm('确定删除吗？')){
				var id=this.title;
				$.getJSON('<?php echo site_url('article/admin/article/deleteClassify')?>/'+id,function(json){
					if(json.status==0){
						$(that).parents('a').remove();
					}else{
						alert(json.error);
					}
				});
			}
			
		});
		$('.update').click(function(){
			var $name=$(this).parents('a').find('span');
			var id=this.title;
			var name=$name.html();
			$('#generatePasswordWindow').attr('type','update').show();
			$('#classID').val(id);
			$('#className').val(name);
		});
	};
	function deletePay(id){
		if(confirm('确定删除吗？')){
			$('input[name="select"]').val(id);
			document.getElementById("paymentdelete").submit();
		}
	}
</script>