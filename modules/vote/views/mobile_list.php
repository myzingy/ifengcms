
<!-- Link Swiper's CSS -->
<link rel="stylesheet" href="<?php print base_url()?>assets/vote_m_host2015/css/animate.css">
<link rel="stylesheet" href="<?php print base_url()?>assets/vote_m_host2015/css/app.css?43trfh">
<script type="text/javascript" src="<?php print base_url()?>assets/vote_m_host2015/js/jquery-1.9.1.min.js"></script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script type="text/javascript" src="<?php print base_url()?>assets/wechat/js/wechat_share_jquery.js"></script>
<script type="text/javascript">
    // 微信分享
    var sharecfj = {
        title:'<?php print $title;?>', // 分享标题
        desc:'<?php print $title;?>', // 分享描述
        link: location.href, // 分享链接
        imgUrl: 'http://cms.wisheli.com/uploads/special/host2015_m/images/share.jpg' //分享图标
    }
    $wx=new $.wechat();
    $wx.share(sharecfj);
</script>


<div id="loading">
    <div class="content">
      <div class="logo"><img src="<?php print base_url()?>assets/vote_m_host2015/images/logo.png"></div>
      <div class="logo_text"><img src="<?php print base_url()?>assets/vote_m_host2015/images/logo_text.png"></div>
    </div>
</div>

<div id="main">

<section class="page page_list"<?php if(!empty($background)): ?> style="background:<?php echo $background; ?>"<?php endif; ?>>
    <div class="tip_cont">
        <div class="cont_v">
            <div class="btn openbtn animated">
               <img src="<?php print base_url()?>assets/vote_m_host2015/images/tip_top.png" class="top_img animated shake infinite">
               <img src="<?php print base_url()?>assets/vote_m_host2015/images/tipng.png" class="bg">
            </div>
            <div class="cont animated">
                <div class="cont_img">
                    <button class="btn closebtn"><img src="<?php print base_url()?>assets/vote_m_host2015/images/close_btn.png"></button>
                    <img src="<?php print base_url()?>assets/vote_m_host2015/images/tip_cont_01.jpg" class="img_l fl">
                    <img src="<?php print base_url()?>assets/vote_m_host2015/images/tip_cont_01-02.jpg" class="img_r fl">
                    <div class="clear"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="banner"><img src="<?php if(!empty($thumb)): ?><?php echo $thumb; ?><?php else: ?><?php print base_url()?>assets/vote_m_host2015/images/banner.png<?php endif; ?>"></div>
    <ul class="players">
		<?php if($data):foreach($data as $r):?>
        <li class="player fl">
            <div class="img">
                <a href="<?php print $r->url;?>" class="img"><img src="<?php print $r->thumb?$r->thumb:(base_url() . '/images/head.jpg');?>" onerror="this.src='<?php print base_url() . 'assets/images/head.jpg';?>';"></a>
            </div>
            <div class="infor1">
                <?php print $r->get_status;?>
                <span class="fl"><?php print $r->name;?></span>
                <span class="fr"><?php print $r->info;?></span>
                <div class="clear"></div>

                <?php if( !empty($r->custom) ): ?>
                <?php $custom = explode('#line#', $r->custom); ?>
                <?php foreach($custom as $value): ?>
                <p><?php echo $value; ?></p>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="infor2">
                <div class="fl no">NO:<span class="num"><?php print $r->sn;?></span></div>
                <div class="votenum fr"><?php print $r->count;?></div>
                <div class="clear"></div>
            </div>
        </li>
		<?php endforeach;endif;?>
        <div class="clear"></div>
    </ul>

    <div class="foot">
        <p class="img">
        	<img src="<?php print base_url()?>assets/vote_m_host2015/images/foot_img_01.png" class='img_l fl'>
        	<img src="<?php print base_url()?>assets/vote_m_host2015/images/foot_img_01-02.png" class='img_r fl'>
        	<div class="clear"></div>
        </p>
        <div class="text">
            <h3>三种方式关注凤凰陕西</h3>
            <ul>
                <li> • 搜索凤凰陕西或sxifeng加关注 </li>
                <li> • 长按上方二维码图片，保存到手机，使用微信扫图片 </li>
                <li> • <a href="http://mp.weixin.qq.com/mp/appmsg/show?__biz=MjM5NDY2NTA2NA==&appmsgid=10000016&itemidx=1&sign=9e364b8cc1014bc352a6d6fb2f4c5f3f&scene=4&key=0d6a3ec0f59f9ba11981146eed3b728237959d25735d4c9be24ba30ad357793fcaed40d90edb57381bc4ce709f2a2a50&ascene=3&uin=OTAxMDgxMTU%3D&pass_ticket=hgzWr%2BsWj94mXELsa2rrUPRo%2FQ1dBDo6DVO2SI83isI%3D">点击此处，在新开页面点击蓝色的“凤凰陕西”</a></li>
                <li>关注凤凰陕西，回复 “<?php print $TP_STR?>选手编号” 为选手投票。</li>
            </ul>
        </div>
    </div>
</section>

</div>

<script type="text/javascript" src="<?php print base_url()?>assets/vote_m_host2015/js/app.js"></script>
<!-- 统计代码 -->
<div style="display:none;"><script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan id='cnzz_stat_icon_1255648736'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s95.cnzz.com/z_stat.php%3Fid%3D1255648736%26online%3D1%26show%3Dline' type='text/javascript'%3E%3C/script%3E"));</script></div>
