<div class="entry-content">
	<div class="big">
		<h1><?php print $title;?></h1>		
		<img width="100%" src="<?php print $data->thumb?$data->thumb:(base_url() . '/images/head.jpg');?>" onerror="this.src='<?php print base_url() . 'assets/images/head.jpg';?>';">
		<div>
			<p class="rowmag"><i class="iconfont love">&#xe60b;<b><?php print $data->count;?></b></i></p>
			<p class="rowname"><?php print $data->name;?><span class="fr num">NO:&nbsp;<font class="nub"><?php print $data->sn;?></font></span></p>
		</div>
	</div>
</div><!-- .entry-content -->
<div class="entry-meta"></div>
<span class="more"><a href="<?php print site_url( 'vote_zh/display/list/'.$data->vid );?>">查看更多</a></span>
<div id="ifengwechat">
	<div class="left">
		<img src="<?php print base_url()?>assets/images/ifeng-zhonghan-wechat.jpg">
	</div>
	<div class="right">
		关注凤凰网中韩交流频道，回复 “<font color="#ea286e"><?php print $TP_STR?>选手编号</font>” 为选手投票。

	</div>
	<div class="ways">
		关注方法
		<ul>
			<li>
				搜索 凤凰网中韩交流频道 或 krifeng 加关注
			</li>
			<li>
				长按上方二维码图片，保存到手机，使用微信扫图片
			</li>
			<li>
				<a href="http://mp.weixin.qq.com/s?__biz=MzIyMzM2NjAwOA==&mid=100000007&idx=1&sn=84a1eb20ecc461dd6fcab5b837951414#rd">点击此处，在新开页面点击蓝色的“凤凰网中韩交流频道”</a>
			</li>
		</ul>
		关注凤凰网中韩交流频道，回复 “<font color="#ea286e"><?php print $TP_STR?>选手编号</font>” 为选手投票。
	</div>

</div>