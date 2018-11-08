<div class="entry-content">
	<h1><?php print $title;?></h1>	
	<?php if($data):foreach($data as $r):?>
	<article_pic id="post-704" class="entry ifengone publish author-admin post-704" itemscope="itemscope" itemtype="http://schema.org/CreativeWork">
		<div class="entry-wrap">
			<div class="entry-content">
				<a href="<?php print $r->url;?>">
				<div class="userpic" style="height:100%;">
					<img width="100%" src="<?php print $r->thumb?$r->thumb:(base_url() . '/images/head.jpg');?>" onerror="this.src='<?php print base_url() . 'assets/images/head.jpg';?>';">
				</div>
				<div>
					<p class="rowmag">
						<i class="iconfont love"><b><?php print $r->count;?></b></i>
					</p>
					<p class="rowname">
						<?php print $r->name;?>
					</p>
					<p class="rowname">
						<span class="num">NO:&nbsp;<font class="nub"><?php print $r->sn;?></font></span>
					</p>
				</div> </a>
			</div><!-- .entry-content -->

		</div><!-- .entry-wrap -->

	</article_pic>
	<?php endforeach;endif;?>
	<br style="clear:both;"/>
	<nav role="navigation" id="nav-below" class="navigation  paging-navigation">
		<?php print $pagination;?>
	</nav>
</div><!-- .entry-content -->
<div class="entry-meta"></div>

<div id="ifengwechat">
	<div class="left">
		<img src="<?php print base_url()?>assets/images/ifeng-wechat.jpg">
	</div>
	<div class="right">
		关注凤凰陕西，回复 “<font color="#ea286e"><?php print $TP_STR?>选手编号</font>” 为选手投票。

	</div>
	<div class="ways">
		关注方法
		<ul>
			<li>
				搜索凤凰陕西或sxifeng加关注
			</li>
			<li>
				长按上方二维码图片，保存到手机，使用微信扫图片
			</li>
			<li>
				<a href="http://mp.weixin.qq.com/mp/appmsg/show?__biz=MjM5NDY2NTA2NA==&amp;appmsgid=10000016&amp;itemidx=1&amp;sign=9e364b8cc1014bc352a6d6fb2f4c5f3f&amp;scene=4&amp;key=0d6a3ec0f59f9ba11981146eed3b728237959d25735d4c9be24ba30ad357793fcaed40d90edb57381bc4ce709f2a2a50&amp;ascene=3&amp;uin=OTAxMDgxMTU%3D&amp;pass_ticket=hgzWr%2BsWj94mXELsa2rrUPRo%2FQ1dBDo6DVO2SI83isI%3D">点击此处，在新开页面点击蓝色的“凤凰陕西”</a>
			</li>
		</ul>
		关注凤凰陕西，回复 “<font color="#ea286e"><?php print $TP_STR?>选手编号</font>” 为选手投票。
	</div>

</div>