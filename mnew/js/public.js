var base_url=location.href.replace(/mnew\/.*/,'');
var base_cgi=this.base_url+'index.php/';
function tip(msg,color,time){
	time=typeof time!='undefined'?time:3000;
	color=typeof color!='undefined'?color:'yellow';
	$tip=$('.tip___msg');
	if($tip[0]){
		$tip.remove();
		return false;
	}else{
		$tip=$('<div id="promptmag" class="tip___msg"><p class="mag">收藏成功</p></div>');
		$tip.appendTo('body');
	}
	$p=$tip.find('p');
	$p.html(msg);
	$p.attr('class','mag '+color);
	var screenwidth    = $(window).width();
    var screenheight   = $(window).height();
    var promptmagwidth = $("#promptmag").width();
    var promptmagheight = $("#promptmag").height();
    $("#promptmag").css("left",(screenwidth-promptmagwidth)/2);
    $("#promptmag").css("top",(screenheight-promptmagheight)/2);
    if(time<0) return;
    setTimeout(function(){
    	$tip.remove();
    },time);
}
function loadimg(flag,msg){
	msg=typeof msg!='undefined'?msg:'';
	$tip=$('.tip___msg');
	if(flag){
		if($tip[0]){
			return;
		}
		tip('<i class="iconfont">&#xe61c;'+msg+'</i>','loading',-1);
	}else{
		$tip.remove();
	}
}
function loadTpl(tplname,$dom,async){
	async=typeof async!='undefined'?async:false;
	$.ajax({
		url:base_url+'mnew/tpl/'+tplname,
		complete:function(){},
		dataType:'html',
		async:async,
		success:function(html){
			$dom.html(html);
		}
	});
};
$(function(){
	//ajax setting
	$.ajaxSetup({
		beforeSend:function(){
			loadimg(true);
		}
		,complete:function(XMLHttpRequest,status){
			loadimg(false);
			if('success'==status){
				try{
					var json=JSON.parse(XMLHttpRequest.responseText);
				}catch(e){
					var json={status:0};
				}
				if(json.status==-1){
					var base_url=location.href.replace(/mnew\/.*/,'');
					location.href=base_url+'index.php/oauth/login/referer';
				}
			}
		}
		,error:function(){
			tip('出错了，请重试！');
		}
		,dataType:'json'
	});
	//首页导航卡的选项卡
	function tab(){
	    $(".promptu li.clume").hide();
	    $(".promptu li.show").show();
	    $(".promptu-menu li").each(
	    function(i){
	        $(this).click(function(){
	        	location.hash='#classify='+$(this).find('h3').attr('class');
	            $(".promptu-menu .active").removeClass("active");
	            $(this).addClass("active");
	            $(".promptu li.show").removeClass("show");
	            $(".reply").hide();
	            $(".buttons").removeClass("avtived");
	            $(".promptu li.clume").hide();
	            $(".promptu li.clume").eq(i).addClass("show");
	            $(".promptu li.show").show();
	            });
	        }
	    );
	}
	//底部回帖输入框的focus状态改变
	function inputfocus_bottom(){
		$(".reply input").focusin(function(){
			$(".focus").removeClass("focus");
			$(this).parents(".reply").addClass("focus");
		});
		$(".reply input").focusout(function(){
			// $(this).parents(".reply").removeClass("focus");
			if($(".reply input").value().length>0){
				$(this).parents(".reply").removeClass("focus");
			}
		});
	}


	//输入框的focus状态改变
	function inputfocus(){
		$(".form-group input").focusin(function(){
			$(".focus").removeClass("focus");
			$(this).parents(".form-group").addClass("focus");
		});
		$(".form-group input").focusout(function(){
			$(".focus").removeClass("focus");
			$(this).parents(".form-group").removeClass("focus");
		});
	}
	//textarea输入框的focus状态改变
	function textareafocus(){
		$(".form-group textarea").focusin(function(){
			$(".focus").removeClass("focus");
			$(this).parents(".form-group").addClass("focus");
		});
		$(".form-group textarea").focusout(function(){
			$(".focus").removeClass("focus");
			$(this).parents(".form-group").removeClass("focus");
		});

		// 模拟上传的按钮点击
	    $("#imgntn").click(
	        function(){
	        $("#imgfile").click();
	    });
	}
	//我的更贴选项卡
	function tab2(){
	   	   	$('.switch li').each(function(i){
			$(this).click(function(){
				$(this).siblings().removeClass('active');
				$(this).addClass('active');
				$(this).parent(".switch").siblings(".columnbox").find(".open").removeClass("open");
				$(this).parent(".switch").siblings(".columnbox").find(".column").eq(i).addClass("open");
			});
		});
	}

	// 首页js开始
	if($("#page").length){(function($){
		tab();
		inputfocus();
		inputfocus_bottom();
	})(jQuery);}
	// 首页js结束	
	
	//发布新闻js
	if($("#release").length){(function($){ textareafocus(); })(jQuery);};
	//忘记密码js
	if($("#changepassword").length){(function($){ inputfocus(); })(jQuery);};
	//登陆的js
	if($("#login").length){(function($){ inputfocus(); })(jQuery);};
	//注册的js
	if($("#register").length){(function($){ inputfocus(); })(jQuery);};
	//新闻详情js
	if($("#details").length){(function($){ inputfocus_bottom(); })(jQuery);};
	//我的更贴的js
	if($("#reply").length){(function($){ tab2(); })(jQuery);};
	//我的更贴的js
	if($("#reply").length){(function($){ tab2(); })(jQuery);};	
	//图集页面的js
	if($("#phones").length){(function($){	
		$(function(){
			var num=$('#slider').find('li').size();
			$('.bcount').text(num);	
		})
		var tt=new TouchSlider({id:'slider','auto':'-1',fx:'ease-out',direction:'left',speed:600,timeout:5000,'before':function(index){
			var es=document.getElementById('slider').getElementsByTagName('li');
			var it=$(es[index]).index()+1;	
			$('.bi').text(it);	
			var tx=$(es[index]).find('p').text();	
			$('.title').text(tx);
		}});
	})(jQuery);};
});
var initMMenuUser=function(){
	//获取用户状态
	$.ajax({
		url:base_cgi+'api/act/getUserInfo',
		complete:function(){loadimg(false);},
		dataType:'json',
		success:function(json){
			if(json.status==-1){
				//登录超时
				$('#menu').find('a').each(function(){
					if($(this).attr('nologin')=='true'){
						$('#mmenu_username').attr('href',base_cgi+'oauth/login/referer');
						return;
					}
					var $html=$('<a style="cursor: pointer;">'+$(this).html()+'</a>');
					$(this).after($html);
					$(this).remove();
					$html.click(function(){tip('登陆后才能使用哦');});
				});
			}else{
				$('#mmenu_username').html(json.nickname);
				$('last_visit').html(json.last_visit);
				$("#imgntn").click(function(){
			        $("#imgfile_userhead").click();
			    });
			    if(json.headimgurl!=null){
			    	$("#imgntn").attr('src',json.headimgurl);
			    }   
			}
		}
	});
	//上传头像
	
	$('#imgfile_userhead').change(function(){
		var data = new FormData();
		jQuery.each($('#imgfile_userhead')[0].files, function(i, file) {
		    data.append('file', file);
		});
		$.ajax({
			type:'POST'
			,url:base_cgi+'api/act/uploadHeadImage'
			,data:data
			,contentType: false
    		,processData: false
			,success:function(json){
				if(json.status==0){
					$('#imgntn').attr('src',json.src);
				}else{
					setTimeout(function(){tip(json.error,'red');},0);
					return false;
				}
			}
		});
	});
};

//init mmenu;
var initMMenu=function(){
	loadTpl('mmenu.html',$('nav#menu'));
	initMMenuUser();
	$('nav#menu').mmenu();
	$('.reply').appendTo('body');
};
//totalcode
var totalcode=function(){
	var id=location.href.match(/id=([0-9]+)/);
	id=id?id[1]:0;
	if(id>0){
		$.ajax({
			url:base_cgi+'api/act/totalcode/'+id,
			dataType:'html',
			beforeSend:function(){},
			complete:function(XMLHttpRequest){
				$(XMLHttpRequest.responseText).appendTo('body');
			}
		});
	}
};