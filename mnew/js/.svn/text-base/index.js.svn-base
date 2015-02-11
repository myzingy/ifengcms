$(function(){
	var base_url=location.href.replace(/mnew\/.*/,'');
	var base_cgi=base_url+'index.php/';
	var classify=2;
	var page=[];
	var limit=10;
	var subject_length=30;
	var reply={};
	var lunbo=function(count){
		for(var i = 0 ; i < count ; i++){
	        $("#indicator").append("<li>" + "</li>");
	    }
	    $("#indicator li:first").addClass("active");
	    var myScroll_lunbo = new iScroll('wrapper', {
            snap: true,
            momentum: false,
            hScrollbar: false,
            onScrollEnd: function() {
            	document.querySelector('#indicator > li.active').className = '';
                document.querySelector('#indicator > li:nth-child(' + (this.currPageX + 1) + ')').className = 'active';
            }
        });
	    
	    window.onresize = function() {
	    	for (i = 0; i < count; i++) {
	            document.getElementById("thelist").getElementsByTagName("img").item(i).style.cssText = " width:" + document.body.clientWidth + "px";
	        }
	        document.getElementById("scroller").style.cssText = " width:" + document.body.clientWidth * count + "px";
	    	myScroll_lunbo.refresh();
	    };
	    setInterval(function() {
	    	if(myScroll_lunbo.currPageX+1==count){
	    		myScroll_lunbo.currPageX=-1;
	    	}else{
	    		myScroll_lunbo.scrollToPage('next', 0, 400, count);
	    	}
	    }, 3500);
	    $(window).trigger('resize');
	};
	$.getJSON(base_cgi+'article/newsList/1/5',function(json){
		if(json.status==0){
			var tpl=$('#lunbo_img_tpl').html();
			var html='';
			for(var i in json.data){
				var _html=tpl;
				for(var j in json.data[i]){
					var reg= new RegExp('{'+j+'(\|[^}]+)?}','g');
					_html=_html.replace(reg,function($1,$2){
						if(typeof $2!='undefined'){
							if($2=='|substr'){
								return json.data[i][j].substr(0,12);
							}else{
								//icon
								return json.data[i][j]==2?'&#xe621;':'';
							}
						}
						return json.data[i][j];
					});
				}
				html+=_html;
			}
			$('#thelist').html(html);
			lunbo(json.data.length);
		}
		
	});
	var newsListForClassify=function (isShowTip){
		isShowTip=typeof isShowTip=='undefined'?false:isShowTip;
		if(typeof page[classify]=='undefined'){page[classify]=1;}
		$.getJSON(base_cgi+'article/newsIndexData/'+classify+'/'+limit+'/'+(limit*(page[classify]-1)),function(json){
			if(json.status==0){
				page[classify]++;
				if(classify==13){//互动
					var tpl=$('#interaction_dis_tpl').html();
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
										//substr
										return json.data[i][j].substr(0,200);
									}
								}
								return json.data[i][j];
							});
						}
						html+=_html;
					}
				}else{
					var tpl=$('#news_list_tpl').html();
					var tpl2=$('#news_list_pics_tpl').html();
					var html='';
					for(var i in json.data){
						
						if(typeof json.data[i].thumb_1!='undefined'){
							//插入图集
							var _html=tpl2;
							for(var j in json.data[i]){
								var reg= new RegExp('{'+j+'(\|[^}]+)?}','g');
								_html=_html.replace(reg,function($1,$2){
									if(typeof $2!='undefined'){
										//substr
										return json.data[i][j].substr(0,subject_length);
									}
									return json.data[i][j];
								});
							}
						}else{
							var _html=tpl;
							for(var j in json.data[i]){
								var reg= new RegExp('{'+j+'(\|[^}]+)?}','g');
								if(reg.test(_html)){
									console.log(j,reg);
								}
								_html=_html.replace(reg,function($1,$2){
									if(typeof $2!='undefined'){
										//substr
										return json.data[i][j].substr(0,subject_length);
									}
									return json.data[i][j];
								});
							}
						}
						
						html+=_html;
						/*
						if(i%4==0 && i!=0){
							//插入图集
							_html='';
							var pi=parseInt(i/4)-1;
							if(typeof json.pics[pi]!='undefined'){
								_html=tpl2;
								for(var j in json.pics[pi]){
									var reg= new RegExp('{'+j+'(\|[^}]+)?}','g');
									_html=_html.replace(reg,function($1,$2){
										return json.pics[pi][j];
									});
								}
							}
							html+=_html;
						}
						*/
					}
				}
				//console.log(classify,html);
				$(html).appendTo('#news_list_'+classify);
			}else{
				if(isShowTip)setTimeout(function(){tip('没有数据了');},0);
			}
			
		});
	};
	newsListForClassify();
	$('.promptu-menu h3').click(function(){
		classify=this.className;
		newsListForClassify();
	});
	$('.morebtn').click(function(){
		newsListForClassify(true);
	});
	// 底部回帖框
	$('.selease').click(function(){
		var msg=$.trim($('#comment_txt').val());
		if(!msg){
			tip('评论内容不能为空','red');
			return false;
		}
		$.ajax({
			type: "POST",
			url: base_cgi+'api/act/comment',
			data:'aid='+reply.aid+'&subject='+msg,
			dataType:   "json",
			success:function(json){
				if(json.status==0){
					$('#comment_txt').val('');
					$(".reply").hide();
					setTimeout(function(){tip('评论+1','blue');},0);
					reply.num.html(parseInt(reply.num.html())+1);
					
				}else{
					$('.reply').find('input').eq(0).focus();
					setTimeout(function(){tip(json.error,'red');},0);
				}
			}
		});
	});
	$('.interaction').on('click','.buttons',function(){
		var that=$(this);
		var cn=that.attr('btype');
		$('.interaction').find('.buttons').removeClass('avtived');
		that.addClass("avtived");
		var aid=that.parents('.button-groups').attr('buttonsid');
		var num=that.find('num');
		$(".reply").hide();
		switch(cn){
			case 'replybtn':
				//点评
				$(".reply").show();
				reply={
					aid:aid,
					num:num
				};
			break;
			case 'collectbtn':
				//收藏
				$.ajax({
					type: "POST",
					url: base_cgi+'api/act/favorites',
					data:'aid='+aid,
					dataType:   "json",
					success:function(json){
						if(json.status==0){
							
							setTimeout(function(){tip('收藏+1','blue');},0);
							num.html(parseInt(num.html())+1);
						}else{
							setTimeout(function(){tip(json.error,'red');},0);
							
						}
					}
				});
			break;
			case 'praisebtn':
				//点赞
				$.ajax({
					type: "POST",
					url: base_cgi+'api/act/applaud',
					data:'aid='+aid,
					dataType:   "json",
					success:function(json){
						if(json.status==0){
							
							setTimeout(function(){tip('赞+1','blue');},0);
							num.html(parseInt(num.html())+1);
						}else{
							setTimeout(function(){tip(json.error,'red');},0);
						}
					}
				});
			break;	
		}
	});
	initMMenu();
});
