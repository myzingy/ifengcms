<style>
	#totaldisplay { min-height: 300px; padding: 1px;}
	#totaldisplay li{ display: inline-block; border: 1px solid #9f9; padding: 5px; list-style: none; margin: 5px;}
	#totaldisplay div.block{ border-bottom: 1px solid #fff; margin: 10px;}
	#totaldisplay h3{margin: 8px; color: #808000; font-size:14px; font-weight: bold;}
	#totaldisplay div h3{ margin: 0 10px;color: #fff;}
	#totaldisplay div ul{ margin: 0 10px; color: #008000;}
</style>
<h2><?php print $header?></h2>

<?php print form_open('fields/admin/fields/totaldata')?>
<input type="hidden" name="fid" value="<?php print $fid;?>" />
<div class="buttons">           
	<a>开始时间<input name="stime" /> 00:00:00</a>
</div>
<div class="buttons">           
	<a>结束时间<input name="etime" value="<?php print date('Y-m-d',TIME)?>" /> 23:59:59</a>
</div>
<br style="clear: both;"/>
<?php foreach($fields as $v):if(in_array($v,array('id','ip','status'))!==false) continue;?>
<div class="buttons">
	<a><label><input type="checkbox" name="dbkey[]" value="<?php print $v;?>" /><?php print $dbkey[$v]['label']?$dbkey[$v]['label']:$v;?></label></a>
</div>
<?php endforeach;?>
<br style="clear: both;"/>
<div class="buttons" style=" margin: 10px 0; ">           
	<button id="totalstart" type="submit">
    <?php print  $this->bep_assets->icon('add');?>
    	开始分析
    </button>
</div>
<?php print form_close()?>
<br style="clear: both;"/>
<div id="totaldisplay">
	
</div>
<br style="clear: both;"/>
<div class="buttons">                
	<a href="<?php print site_url('fields/admin/fields/formdata/'.$fid);?>">
    <?php print  $this->bep_assets->icon('add');?>
    	返回数据列表
    </a>
</div>
<link rel="stylesheet" type="text/css" href="<?php print base_url()?>assets/js/jplot/jquery.jqplot.css" />
<script type="text/javascript">
	pageinit=function(){
		var getData=function(){
			jQuery.isPlainObject = function(obj){
				if(typeof obj !='object') return false;
				return obj.constructor === Object;
			};
			$('form').ajaxSubmit({
				dataType:'json',
				success:function(json){
					//console.log(json);
					if(json.status!=0){
						alert('此字段数据可能为空，无法分析，请选择其它字段！');
						$('#totaldisplay').css('background','#fff');
						$('form').find('button').html('<?php print  $this->bep_assets->icon('add');?>开始分析').removeAttr('disabled');
						return;
					}
					$rows=$('#totaldisplay').find('#rows');
					$rows_html='<?php print $header?>，共计'+json.rows+'条，已汇总 '+(json.limit>json.rows?json.rows:json.limit)+'/'+json.rows;
					if($rows.length<1){
						$('<h3 id="rows">'+$rows_html+'</h3>').appendTo('#totaldisplay');
					}else{
						$rows.html($rows_html);
					}
					for(var i in json.data){
						$dbkey=$('#totaldisplay').find('#'+i);
						//console.log($dbkey);
						if($dbkey.length<1){
							$('<div id="'+i+'" class="block"><h3>'+json.data[i].label+'</h3><ul></ul></div>').appendTo('#totaldisplay');
						}
						$dbkey=$('#totaldisplay').find('#'+i);
						//console.log($dbkey);
						for(var j in json.data[i].options){
							if(/[0-9]+\.[0-9]{2}/.test(j)){
								$name=$dbkey.find('li');
								if($name.length<1){
									$('<li>'+json.data[i].label+'(<span>'+j+'</span>)</li>').appendTo($dbkey.find('ul'));
								}else{
									$num=$name.find('span');
									$num.html(parseFloat($num.html())+parseFloat(j));
								}
								
							}else{
								var name_id=j.replace(/[_\-:"'\~\\\!@#$%^&\*\(\)\|\/,\.;=\+]/g,'');
								var num=json.data[i].options[j];
								$name=$dbkey.find('#'+name_id);
								//console.log("options",$name);
								if($name.length<1){
									$('<li id="'+name_id+'">'+j+'(<span>'+num+'</span>)</li>').appendTo($dbkey.find('ul'));
								}else{
									$num=$name.find('span');
									$num.html(parseInt($num.html())+num);
								}
							}
						}
					}
					if(!json.isnext){
						$('#totaldisplay').css('background','#99ee99');
						$('form').find('button').html('<?php print  $this->bep_assets->icon('add');?>开始分析').removeAttr('disabled');
						$('<button type="button" id="viewChartButton">图形展示</button>').appendTo('#rows');
						$('#viewChartButton').click(function(){
							viewChart();
						});
					}else{
						$('form').attr('action','<?php print site_url('fields/admin/fields/totaldata/page')?>/'+json.limit);
						setTimeout(function(){getData();},0);
					}
				}
			});
		};
		$('form').submit(function(e){
			e.preventDefault();
			if($('input[name="dbkey\[\]"]:checked').length<1){
				alert('请至少选择一个要统计分析的字段');
				return false;
			}
			$('#totaldisplay').css('background','#eeee33').html('');
			$('form').attr('action','<?php print site_url('fields/admin/fields/totaldata')?>');
			$('form').find('button').html('正在分析中，请勿进行其它操作。。。').attr('disabled','disabled');
			getData();
		});
	};
	var oneLiData=[];
	var oneLiDataID=[];
	var oneLiDataDisplay=function(){
		if(oneLiData.length>0){
			var oneLiDataTitle=[];
			for(var i in oneLiDataID){
				$('#'+oneLiDataID[i]).hide();
				oneLiDataTitle.push($('#'+oneLiDataID[i]).find('h3').html());
			}
			console.log("oneLiData==>",oneLiData);
			$('#oneLiChat').jqplot([oneLiData], {
		        title:'<h3>'+oneLiDataTitle.join('/')+'</h3>',
		        seriesDefaults: {
		        	shadow: true, 
		        	renderer: jQuery.jqplot.PieRenderer, 
		        	rendererOptions: { 
		        		showDataLabels: true 
		        	} 
		       	}, 
  				legend: { 
  					show:true 
  				}
		    });
		}
	};
	function viewChart(){
		$('#oneLiChat').remove();
		$('<div id="oneLiChat"></div>').appendTo('#totaldisplay');
		var $block=$('#totaldisplay').find('.block');
		var cl=$('#viewChartButton').attr('class');
		if(cl.indexOf('chart_but')>-1){
			if(cl.indexOf('show')>-1){
				$block.show();
				$block.find('ul').show();
				$block.find('.chart').hide();
				$('#viewChartButton').attr('class','chart_but_hide').html('图形展示');
			}else{
				$block.find('ul').hide();
				$block.find('.chart').show();
				$('#viewChartButton').attr('class','chart_but_show').html('数据展示');
				oneLiDataDisplay();
			}
			
			return;
		}else{
			$block.find('ul').hide();
			$('#viewChartButton').attr('class','chart_but_show').html('数据展示');
		}
		oneLiData=[];
		oneLiDataID=[];
		$block.each(function(){
			var chart_id="chart_"+this.id;
			$('<div id="'+chart_id+'" class="chart"></div>').appendTo(this);
			$li=$(this).find('li');
			var data=[];
			$li.each(function(){
				data.push([this.innerHTML.replace(/<[^>]+>/ig,''),parseFloat($(this).children().text())]);
			});
			console.log("data==>",data);
			jQuery.jqplot.config.enablePlugins = true;
			if($li.length==1){
				$(this).hide();
				oneLiData.push(data[0]);
				oneLiDataID.push(this.id);
			}else if($li.length<10){
				//饼图
				$('#'+chart_id).jqplot([data], {
			        title:'',
			        seriesDefaults: {
			        	shadow: true, 
			        	renderer: jQuery.jqplot.PieRenderer, 
			        	rendererOptions: { 
			        		showDataLabels: true 
			        	} 
			       	}, 
      				legend: { 
      					show:true 
      				}
			    });
			}else if($li.length<20){
				//柱状图
				
				$('#'+chart_id).jqplot([data], {
			        title:'',
			        seriesDefaults:{
			            renderer:jQuery.jqplot.BarRenderer,
			            rendererOptions: {
			                varyBarColor: true
			            }
			        },
			        axes:{
			            xaxis:{
			                renderer: jQuery.jqplot.CategoryAxisRenderer
			            }
			        }
			    });
			}else{
				//点线图
				$('#'+chart_id).jqplot([data], {
			        title:'',
			        axes: {
				        xaxis: {
				          renderer: $.jqplot.CategoryAxisRenderer,
				          label: '',
				          labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
				          tickRenderer: $.jqplot.CanvasAxisTickRenderer,
				          tickOptions: {
				              // labelPosition: 'middle',
				              angle: 90
				          }
				           
				        },
				        yaxis: {
				          label: '',
				          labelRenderer: $.jqplot.CanvasAxisLabelRenderer
				        }
				    }
			    });
			}
		});
		oneLiDataDisplay();
	}
</script>