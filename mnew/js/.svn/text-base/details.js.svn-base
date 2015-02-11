$(function(){
	var base_url=location.href.replace(/mnew\/.*/,'');
	var base_cgi=base_url+'index.php/';
	var page=1,limit=10;
	var id=location.href.match(/id=([0-9]+)/);
	id=id?id[1]:0;
	var getComment=function(isShowTip){
		isShowTip=typeof isShowTip=='undefined'?false:isShowTip;
		var url=base_cgi+'comment/getCommnet/'+id+'/'+limit+'/page/'+(page-1)*limit;
		var tpl=$('#comment_list_tpl').html();
		$.getJSON(url,function(json){
			if(json.status==0){
				page++;
				var html='';
				for(var i in json.data){
					var _html=tpl;
					for(var j in json.data[i]){
						var reg= new RegExp('{'+j+'(\|[^}]+)?}','g');
						_html=_html.replace(reg,function($1,$2){
							if(typeof $2!='undefined'){
								if($2=='|avatar'){
									return base_cgi+'api/act/avatar/'+json.data[i][j];
								}else{
									//time
									var time=parseInt(json.data[i][j])*1000;
									time=new Date(time);
									return time.getFullYear()+'-'+time.getMonth()+'-'+time.getDay()+' '+time.getHours()+':'+time.getMinutes();
							
								}
							}
							return json.data[i][j];
						});
					}
					html+=_html;
				}
				$(html).appendTo('#comment_list');
			}else{
				//没有数据了
				if(isShowTip) setTimeout(function(){tip('没有数据了');},0);
			}
		});
	};	
	var init=function(){
		
		$.getJSON(base_cgi+'article/getContent/'+id,function(json){
			if(json.status==0){
				$('body').show();
				for(var i in json){
					if(json[i]!==""){
						if(i=='title'){$('title').html(json[i]);}
						if(i=='src'){
							if(json[i]==null){
								$('div.img').remove();
							}else{
								$('.c_'+i).attr('src',base_url+json[i]);
							}
						}else{
							$('.c_'+i).html(json[i]);
						}
					}
				}
				//相关新闻
				var html='';
				var data=json['news']['data'];
				for(var i in data){
					if(data[i].id==json.id) continue;
					html+='<li><a href="details.html?id='+data[i].id+'">'+data[i].title+'</a></li>';
				}
				$('#news_list').html(html);
				//内页广告
				if(json['ads']['status']==0){
					html=$('#banner_ads_tpl').html();
					data=json['ads']['data'];
					for(var i in data){
						for(var j in data[i]){
							var reg= new RegExp('{'+j+'(\|[^}]+)?}','g');
							html=html.replace(reg,function($1,$2){
								if(typeof $2!='undefined'){
									//format
									return data[i][j].substr(0,60);
								}
								return data[i][j];
							});
						}
						if(parseInt(Math.random()*10)%2==0) break;
					}
					$('#banner_ads').html(html);
				}
				totalcode();
			}else{
				$('body').html('').show();
				setTimeout(function(){tip('未找到内容','red',-1);},0);
				
			}
		});
		getComment();
		$('#morebtn_comment').click(function(){
			getComment(true);
		});
	};
	
	init();
	//收藏
	$('.favorites_but').click(function(){
		var num=$('.c_favorites');
		$.ajax({
			type: "POST",
			url: base_cgi+'api/act/favorites',
			data:'aid='+id,
			dataType:   "json",
			success:function(json){
				if(json.status==0){
					tip('收藏+1','blue');
					num.html(parseInt(num.html())+1);
				}else{
					setTimeout(function(){tip(json.error,'red');},0);
				}
			}
		});
	});
	//点赞
	$('.applaud_but').click(function(){
		var num=$('.c_applaud');
		$.ajax({
			type: "POST",
			url: base_cgi+'api/act/applaud',
			data:'aid='+id,
			dataType:   "json",
			success:function(json){
				if(json.status==0){
					tip('赞+1','blue');
					num.html(parseInt(num.html())+1);
				}else{
					setTimeout(function(){tip(json.error,'red');},0);
					
				}
			}
		});
	});
	//点评成功后显示
	var dis_comment=function(data){
		var tpl=$('#comment_list_tpl').html();
		var html=tpl;
		for(var i in data){
			var reg= new RegExp('{'+i+'(\|[^}]+)?}','g');
			html=html.replace(reg,function($1,$2){
				if(typeof $2!='undefined'){
					if($2=='|avatar'){
						return base_cgi+'api/act/avatar/'+data[i];
					}else{
						//time
						var time=new Date();
						return time.getFullYear()+'-'+(time.getMonth()+1)+'-'+time.getDay()+' '+time.getHours()+':'+time.getMinutes();
				
					}
				}
				return data[i];
			});
		}
		$(html).prependTo('#comment_list');
		location.hash='#comment';
	};
	//点评
	$('.selease').click(function(){
		var num=$('.c_comment');
		var msg=$.trim($('#comment_txt').val());
		if(!msg){
			tip('评论内容不能为空','red');
			return false;
		}
		$.ajax({
			type: "POST",
			url: base_cgi+'api/act/comment',
			data:'aid='+id+'&subject='+msg,
			dataType:   "json",
			success:function(json){
				if(json.status==0){
					$('#comment_txt').val('');
					num.html(parseInt(num.html())+1);
					dis_comment({subject:msg,addtime:0,uid:0,nickname:'你的点评'});
					setTimeout(function(){tip('评论+1','blue');},0);
				}else{
					$('.reply').find('input').eq(0).focus();
					setTimeout(function(){tip(json.error,'red');},0);
				}
			}
		});
	});
});
