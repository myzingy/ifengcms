$(function(){
	var base_url=location.href.replace(/mnew\/.*/,'');
	var base_cgi=base_url+'index.php/';
	var page=[];
	var limit=10;
	var classify=1;
	var collectData=function(isShowTip){
		isShowTip=typeof isShowTip=='undefined'?false:isShowTip;
		if(typeof page[classify]=='undefined'){page[classify]=1;}
		$.getJSON(base_cgi+'api/act/getMyNews/'+limit+'/'+(limit*(page[classify]-1)),function(json){
			if(json.status==0){
				page[classify]++;
				display(json);
			}else{
				if(isShowTip)setTimeout(function(){tip('没有数据了');},0);
			}
		});
	};
	var display=function(json){
		var tpl=$('#collect_display_tpl').html();
		var html='';
		//article
		for(var i in json.data){
			var _html=tpl;
			for(var j in json.data[i]){
				var reg= new RegExp('{'+j+'(\|[^}]+)?}','g');
				_html=_html.replace(reg,function($1,$2){
					if(typeof $2!='undefined'){
						if('|display'==$2){
							return json.data[i][j]==0?'<span>审核通过</span>':(json.data[i][j]==1?'待审核':'已拒绝');
						}else{
							//substr
							return json.data[i][j].substr(0,30);
						}
					}
					return json.data[i][j];
				});
			}
			html+=_html;
		}
		$(html).appendTo('#thelist');
	};
	collectData();
	$('.morebtn').click(function(){
		collectData(true);
	});
	$('#thelist').on('click','.deleteCollect',function(){
		var aid=$(this).attr('aid');
		$.getJSON(base_cgi+'api/act/deleteRelease/'+aid,function(json){
			if(json.status==0){
				$('.newlist[aid="'+aid+'"]').remove();
			}else{
				setTimeout(function(){tip(json.error);},0);
			}
		});
	});
	initMMenu();
});
