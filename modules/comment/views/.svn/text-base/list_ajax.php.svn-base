<style type="text/css">
	.comment_list{font-size:12px;}
	.comment_list dt,.comment_list dd{ float: left; margin: 0 0 5px;}
	.comment_list dt{width:18%; padding: 0 1%;}
	.comment_list dt img{ width:100%;}
	.comment_list dd{width:78%;padding: 0 1%;}
	.comment_list ul{ margin: 0; padding: 0;}
	.comment_list li{ list-style: none;float: left;width:50%; overflow: hidden;white-space:nowrap;}
	.comment_list li.w10{ width:25%;}
	.comment_list li.right{text-align: right;}
	.comment_list li.center{text-align: center;}
	.comment_list hr{ width: 100%; clear: both; border: 1px solid #ccc; border-bottom: 0;}
</style>
<dl class="comment_list">
<?php foreach($members->result_array() as $row):?>
	 <dt>
	 	<img src="http://placehold.it/30x30" />
	 </dt>
	 <dd>
	 	<ul>
	 		<li class="w10">piexl</li>
	 		<li class="center"><?php print date("Y-m-d H:i",$row['addtime'])?></li>
	 		<li class="w10 right">+<?php print $row['applaud']?>个赞</li>
	 	</ul>
	 	<span><?php print $row['subject']?></span>
	 </dd>
	 <hr>
<?php endforeach;?>
</dl>