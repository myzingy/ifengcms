<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<style type="text/css">
			*{width:100%; margin: 0;padding: 0; font-size:14px;}
			.space{ width:480px; margin: 0 auto 20px;}
		</style>
	</head>
	<body>
		<div class="space">
			<img src="<?php print base_url().$data['first_ad']->src;?>"/>
			<p><?php print mb_substr($data['first_ad']->title, 0,26,'UTF-8');?></p>
			<p><?php print $data['first_ad']->url?$data['first_ad']->url:site_url('xwzwb/show/'.$data['first_ad']->id);?></p>
		</div>
		<div class="space">
			<p>【本期导读】</p>
			<?php for($i=0;$i<9;$i++):?>
				<p>&gt;&nbsp;<?php print mb_substr($data['news'][$i]->title, 0,26,'UTF-8');?></p>
			<?php endfor;?>
			<p>&gt;&nbsp;天气资讯</p>
			<p>网页版请点击 <?php print base_url();?></p>
			<p>&gt;&gt;&gt;&nbsp;后面还有更多内容&nbsp;&lt;&lt;&lt;</p>
		</div>
		<div class="space">
			<?php for($i=0;$i<9;$i++):?>
				<p>【<?php print $data['news'][$i]->title;?>】</p>
				<p><?php print $data['news'][$i]->subject;?></p>
				<p>详细请看&nbsp;<?php print $data['news'][$i]->url?$data['news'][$i]->url:site_url('xwzwb/show/'.$data['news'][$i]->id);?></p>
				<p>&nbsp;</p>
				<?php if($i%3==0):?>
					<p>-----------------</p>
					<p>&gt;&gt;&gt;&nbsp;后面还有更多内容&nbsp;&lt;&lt;&lt;</p>
				<?php endif;?>
				
				
			<?php endfor;?>
			<p>【天气资讯】</p>
			<?php
			//$cont=file_get_contents('http://i.tianqi.com/index.php?c=code&id=8&num=3');
			//$cont=preg_replace('/[\n\t\r]/', '', $cont);
			//preg_match_all('/<div class="wtleft">(.*)<div class="wtpic">/', $cont,$arr);
			//$cont=iconv('GBK', 'UTF-8', $arr[1][0]);
			//echo substr(str_replace(':none', '', $cont), 0,-6)
			$cont=@file_get_contents('http://i.tianqi.com/index.php?c=code&id=9');
			if(!$cont){
				echo "<p style=\"color:red;\">获取天气失败，请刷新重试或自行前往<a href=\"http://xian.tianqi.com/\" target=\"_blank\"> 天气网 </a>查看</p>";
			}else{
				/*
				$cont=preg_replace('/[\n\t\r]/', '', $cont);
				$cont=iconv('GBK', 'UTF-8', $cont);
				preg_match_all('/<i class="tqdate">([^<]+)<\/i>/', $cont,$darr);
				//var_dump($darr);
				preg_match_all('/<div class="wtline">([^<]+)<\/div>/', $cont,$arr);
				
				for($i=0;$i<6;$i+=2){
					echo "<p>".$darr[1][(int)($i/2)]." : ".$arr[1][$i]."/".$arr[1][$i+1]."</p>";
				}
				*/
				$cont=preg_replace('/[\n\t\r ]/', '', $cont);
				$cont=preg_replace('/<[^>]+>/', '#', $cont);
				$cont=iconv('GBK', 'UTF-8', $cont);
				preg_match_all('/#?([^#\[\]]+)#/', $cont,$arr);
				$data=$arr[1];
				$xi=array_search('西安',$data);
				if($xi===false){
					echo "<p style=\"color:red;\">定位失败，请刷新页面</p>";
				}
				//var_dump($data);
				$xi+=1;
				for($i=0;$i<3;$i++){
					echo "<p>".$data[++$xi].' ('.$data[++$xi].') '.$data[++$xi].' / '.$data[++$xi]."</p>";
				}
			}
			
			?>

			网页版请点击 <?php print base_url();?>
		</div>
		<div class="space">
			<pre>
【声明】
联系电话：029-88248121
订阅方法：陕西电信用户发短信SJB到10659781
产品资费：免费 
公众微信：sn-xwzwb 
公众易信：sxxwzwb 
最新内容：http://i.269.net 
版权所有：陕西公众信息产业有限公司
陕 ICP备：陕B2-20070053-5
信息网络传播视听节目许可证：2709501号
增值电信业务经营许可证A2.B1.B2-20090001
（本期内容完）
</pre></div>
	</body>
</html>