/* Script to provide the navigation menu with functionality */
$(document).ready(function(){
    $('#menu').treeview({
        cookie_name: 'bep_navigation'
    });
    $('#site').click(function(){
    	var left=$('#navigation').css('left');
    	var width=$('#navigation').width();
    	if(left=='0px'){
    		$('#navigation').css('left',(0-width)+'px');
    		$('#content').css('padding-left','10px');
    		$.cookie('bep_left_menu', 'close',{path: '/'});
    	}else{
    		$('#navigation').css('left','0px');
    		$('#content').css('padding-left',(width+10)+'px');
    		$.cookie('bep_left_menu', 'open',{path: '/'});
    	}
    });
    if($.cookie('bep_left_menu')=='close'){
    	$('#site').trigger('click');
    }
});