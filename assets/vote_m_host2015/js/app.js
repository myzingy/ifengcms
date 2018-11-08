function init(){
    // loading执行一次
    var loading_time = new Date().getTime();
    $(window).on('load',function(){
		var now = new Date().getTime();
		var loading_end = false;
		var time;
		var time_del = now - loading_time;
		if ( time_del >= 2200 ) {
			loading_end = true;
		}
		if ( loading_end ) {
			time = 0;
		} else {
			time = 2200 - time_del;
		}
		// loading完成后请求
		setTimeout(function(){
			$("#loading").remove();
			$("#main").show();
		},time);
    })
}
/*初始化对象函数*/
init();


$(".openbtn").click(function(){
	$(this).removeClass('fadeInLeft');
	$(this).addClass('fadeOutLeft');
	$('.cont').removeClass('fadeOutRight');
	$('.cont').addClass('fadeInRight');
	$('.cont').show();
});

$(".closebtn").click(function(){
	$('.openbtn').removeClass('fadeOutLeft');
	$('.openbtn').addClass('fadeInLeft');
	$('.cont').removeClass('fadeInRight');
	$('.cont').addClass('fadeOutRight');
});




