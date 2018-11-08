/*
 * author vking.wang
 */
(function($){
	$.extend({
		
  		youhuisheng:function(config){
  			var that=this;
  			var looperInterval='';
  			var payAccessFlag=false;
  			var setting = $.extend({
				showPayCode:function(){
					console.log('--showPayCode--');
				}
				,payAccess:function(){
					console.log('--payAccess--');
				}
			}, config);
			var sid='';
			// param:{url,size,name,price}
			that.createQRsrc=function(param){
				var cfg = $.extend({
					sid:sid,
					////
					size:150,
					proName:'',
					oldPrice:0,
					nowPrice:0,
					timeDay:0,
					payUrl:''
				}, param);
				if(cfg.payUrl){
					var url=cfg.payUrl;
				}else{
					var url='http://www.minidudu.com/wxpay2/index.php/app/youhuisheng';
					for(var i in cfg){
						if(i=='size' || cfg[i]==='') continue;
						url+='/'+i+'/'+cfg[i];
					}
				}
				console.log('--url--',url);
				var chl=encodeURIComponent(url);
				return 'http://api.kuaipai.cn/qr?chs='+cfg.size+'x'+cfg.size+'&chl='+chl;
			};
			(function(){
				//init cookie
				var cookie=document.cookie;		
				var match=cookie.match(/WeChatSID=([^;]+)/);
				if(match){
					sid=match[1];
					console.log('cookie',sid);
				}else{
					var expire = new Date((new Date()).getTime() + 365 * 3600000);
					sid='SID'+parseInt(expire.getTime()*(Math.random()*100));
					console.log('new-cookie',sid);
					expire = "; expires=" + expire.toGMTString();
					document.cookie = "WeChatSID=" + escape(sid) + expire;
				}
				
				//run paycode
				var oldjson={id:0};
				window.setInterval(function(){
					$.ajax({
						url:'http://www.minidudu.com/wxpay2/index.php/app/youhuisheng/act/getPaycode',
						//url:'http://192.168.23.1/minidudu/wxpay2/index.php/app/youhuisheng/act/getPaycode',
						data:{sid:sid},
						dataType:'jsonp',
						success:function(json){
							if(json.status==0){
								if(oldjson.id!=json.data.id){//开启新的支付
									if(json.data.status=="0" && json.data.code_url){
										payAccessFlag=true;
										oldjson=json.data;
										console.log('--json.data.code_url--',json.data.code_url);
										json.data.code_url=that.createQRsrc({payUrl:json.data.code_url,size:500});
										setting.showPayCode(json.data);
									}
								}else if(json.data.status=="1"){//支付成功
									if(payAccessFlag){
										setting.payAccess();
										payAccessFlag=false;
									}
								}
							}
						}
					});
				},1000);
			})();
  			return that;
		}
  	});
})(jQuery);