<style>
	.parent{width:200px; float: left; margin: 5px; padding: 5px; border: 1px solid #ccc; background-color: #efefef;}
	.parent:hover{ background-color: #fff;}
	.parent .title{white-space: nowrap;overflow: hidden;}
	.parent .number{text-align: right; font-size:12px;}
</style>
<div class="entry-content">
	<form method="get" action="<?php print site_url('vote_zh/ifengvotereldata')?>" >
		<input type="hidden" name="rand" value="<?php print rand();?>" />
		投票活动ID<input type="text" name="vid" value="<?php print $_GET['vid']?>" />
		开始时间<input type="text" placeholder="<?php print $stime;?>" name="stime" value="<?php print $_GET['stime']?>"  /> 00:00:00
		结束时间<input type="text" placeholder="<?php print $etime;?>" name="etime" value="<?php print $_GET['etime']?>" /> 23:59:59
		<button type="submit" id="form_but">统计</button>
	</form>
	<h1><?php print $title?></h1>
	<?php if($data):foreach($order as $key=>$val):
		$r=$data[$key];
		?>
		<div class="parent" title="<?php print $r->name?>">
			<div class="title">(<?php print $r->sn?>)<?php print $r->name?></div>
			<div class="number">(<span title="拉粉数" style="color:green;"><?php print $r->fencount?></span>)&nbsp;<span title="跳票数" style="color:red;"><?php print $r->tpcount?></span>/<span title="拉粉数总数" style="color:blue;"><?php print $r->count?></span></div>
		</div>
	<?php endforeach;endif;?>
	<br style="clear: both">
</div>