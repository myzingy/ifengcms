$(function(){
	var base_url=location.href.replace(/mnew\/.*/,'');
	var base_cgi=base_url+'index.php/';
	var page=[];
	var limit=10;
	var classify=1;
	var replyData=function(isShowTip){
		isShowTip=typeof isShowTip=='undefined'?false:isShowTip;
		if(typeof page[classify]=='undefined'){page[classify]=1;}
		$.getJSON(base_cgi+'api/act/replyData/'+classify+'/'+limit+'/'+(limit*(page[classify]-1)),function(json){
			if(json.status==0){
				page[classify]++;
				display(json);
			}else{
				if(isShowTip)setTimeout(function(){tip('没有数据了');},0);
			}
		});
	};
	var display=function(json){
		var tpl_1=$('#reply_article_tpl').html();
		var tpl_2=$('#reply_comment_tpl').html();
		var html='';
		//article
		for(var i in json.data){
			var _html=tpl_1;
			if($('#reply_display_'+classify+' .review[aid="'+json.data[i].aid+'"]').length>0) break;
			for(var j in json.data[i]){
				var reg= new RegExp('{'+j+'(\|[^}]+)?}','g');
				_html=_html.replace(reg,function($1,$2){
					return json.data[i][j];
				});
			}
			$(_html).appendTo('#reply_display_'+classify);
		}
		
		//replay
		for(var i in json.data){
			var _html=tpl_2;
			for(var j in json.data[i]){
				var reg= new RegExp('{'+j+'(\|[^}]+)?}','g');
				_html=_html.replace(reg,function($1,$2){
					if(typeof $2!='undefined'){
						//avatar
						return base_cgi+'api/act/avatar/'+json.data[i][j];
					}
					return json.data[i][j];
				});
			}
			$(_html).appendTo('#reply_display_'+classify+' .review[aid="'+json.data[i].aid+'"]');
		}
	};
	$('.switch li').click(function(){
		var type=$(this).find('a').attr('href');
		switch(type){
			case '#me-reply':
				classify=1;
				replyData();
			break;	
			case '#reply-me':
				classify=2;
				replyData();
			break;	
		}
	});
	replyData();
	$('.morebtn').click(function(){
		replyData(true);
	});
	initMMenu();
});