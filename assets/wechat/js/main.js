
var score = 0;
var flag = false;
var add_score = 1;//每次增加的分数
var reduce_score = 4;//每次减少的分数
var drop_height = 15;//第一关每次的掉落的高度



$(function(){


var username;
var userphone;
// var useropenid;

//从地址栏中拿到用户的openid
// function GetRequest_search(){
//   var url_search = location.search; //获取url中"?"符后的字串
//   var theRequest = new Object();
//   if (url_search.indexOf("?") != -1) {
//     var str = url_search.substr(1);
//     strs = str.split("&");
//     for(var i = 0; i < strs.length; i ++) {
//        theRequest[strs[i].split("=")[0]]=(strs[i].split("=")[1]);
//     }
//     return theRequest;
//   }
//   return theRequest;
// }
// var Request_search = new Object();
// Request_search = GetRequest_search();
// useropenid = Request_search['useropenid'];
// console.log(useropenid);

var screnn_h = $(window).height();

$('.enterbtn').click(function(){
    $('.page1').hide();
    $('.game').show();
    $("#car_audio")[0].play();
    jinbiinit();
});


$(".rule_btn").click(function(){
    dialogshow();
    $(".modal-dialog .rule_box").show();
});
$(".closebtn").click(function(){
    dialoghide();
});


$(".sharebtn").click(function(){
    $(".share").show();
});
$(".share").click(function(){
    $(this).hide();
});

//弹出框的弹出
function dialogshow(){
    $(".modal-dialog .rule_box").hide();
    $(".modal-dialog .sinup_box").hide();
    $(".modal-dialog .mag_box").hide();
    $(".modal-dialog .sinup_box .has_prize").hide();
    $(".modal-dialog .sinup_box .unhas_prize").hide();
    $(".modal-dialog").css('visibility','visible');
}
//弹出框的弹出
function dialoghide(){
    $(".modal-dialog .rule_box").hide();
    $(".modal-dialog .sinup_box").hide();
    $(".modal-dialog .mag_box").hide();
    $(".modal-dialog .sinup_box .has_prize").hide();
    $(".modal-dialog .sinup_box .unhas_prize").hide();
    $(".modal-dialog").css('visibility','hidden');
}
dialoghide();
//测试
// dialogshow();
// $('.has_prize .prizename').attr('src','images/prize_1.png');
// $(".modal-dialog .sinup_box").show();
// $(".modal-dialog .sinup_box .has_prize").show();

function game_voer(){
    console.log("游戏的结束");
    $("#car_audio")[0].pause();
    sharecfj = {
        title:'我在“天降福利抢得快”活动中,接到了'+score+'件家电，快来参与哦～', // 分享标题
        desc:'我去，还真中奖了，土豪BOSS散千金啊',  //分享描述
        link: 'http://piexl.net/signup/guomeicj', // 分享链接
        imgUrl: 'http://www.piexl.net/signup/guomeicj/images/share.jpg' // 分享图标
    }
    $wx.share(sharecfj);
    //填写表单成功
    $.ajax({
      url:'draw.php',
      data:'&useropenid='+useropenid,
      dataType:'jsonp',
      jsonp:'callback',
      success:function(json){
        console.log(json);
        if(json.status==1){
            //抽过奖了
            //alert(json.masg);
            dialogshow();
            $(".modal-dialog .sinup_box").show();
            $(".modal-dialog .sinup_box .unhas_prize").show();
        }else if(json.status==0){
            //中奖
            dialogshow();
            $(".modal-dialog .sinup_box").show();
            if(json.userprize=='港澳双人游'){
                $('.has_prize .prizename').attr('src','images/prize_1.png');
            }else if(json.userprize=='毛绒玩具'){
                $('.has_prize .prizename').attr('src','images/prize_2.png');
            }else if(json.userprize=='双人电影票'){
                $('.has_prize .prizename').attr('src','images/prize_3.png');
            }else if(json.userprize=='青花瓷套碗'){
                $('.has_prize .prizename').attr('src','images/prize_4.png');
            }else if(json.userprize=='桑叶茶'){
                $('.has_prize .prizename').attr('src','images/prize_5.png');
            }else if(json.userprize=='十花汤'){
                $('.has_prize .prizename').attr('src','images/prize_6.png');
            }else if(json.userprize=='电风扇'){
                $('.has_prize .prizename').attr('src','images/prize_7.png');
            }else if(json.userprize=='电热水壶'){
                $('.has_prize .prizename').attr('src','images/prize_8.png');
            }else if(json.userprize=='酸奶机'){
                $('.has_prize .prizename').attr('src','images/prize_9.png');
            }
            $(".modal-dialog .sinup_box .has_prize").show();
            sharecfj = {
                title:'我在“天降福利抢得快”活动中,获得了'+json.userprize+'，快来参与哦～', // 分享标题
                desc:'我去，还真中奖了，土豪BOSS散千金啊',  //分享描述
                link: 'http://piexl.net/signup/guomeicj', // 分享链接
                imgUrl: 'http://www.piexl.net/signup/guomeicj/images/share.jpg' // 分享图标
            }
            $wx.share(sharecfj);

        }else if(json.status==10001){
            //未中奖
            dialogshow();
            $(".modal-dialog .sinup_box").show();
            $(".modal-dialog .sinup_box .unhas_prize").show();
        }else{
            //发生了错误
            alert(json.err);
        }
        //打开弹窗
        //dialogshow();
      }
    });
}


//确定按钮的点击
$("#setUser").click(function(){
    username = $.trim($('#username').val());
    userphone = $.trim($('#userphone').val());
    if(!username || !/^1[0-9]{10}$/.test(userphone)){
        alert("请填写表单！");
        return;
    }else{
      //填写表单成功
      $.ajax({
          url:'post.php',
          data:'&username='+username+'&userphone='+userphone+'&useropenid='+useropenid,
          dataType:'jsonp',
          jsonp:'callback',
          success:function(json){
            console.log(json);
            if(json.status==0){
                dialogshow();
                $(".modal-dialog .mag_box").show();

            }else{
                alert(json.masg);
            }
          }
      });
    }
});



//人物拖动的绑定
touch.on('#peple', 'touchstart', function(ev){
    ev.preventDefault();
});
var target = $("#peple");
var screen_w = $(window).width();
var element_w = target.width();
var dx=0, dy ;
touch.on('#peple', 'drag', function(ev){
    var p_left = parseInt(target.position().left);
    p_left+=ev.x-dx;
    dx=ev.x;
    if(p_left>=0 && p_left <= screen_w-element_w){
        target.css({left:p_left});
    }
});
touch.on('#peple', 'dragend', function(ev){
    dx = 0;
});

//游戏函数
var time_int = false;
var time = 30;//倒数的时间
var times=0;
var guanka = 1; //关卡
var game = true;
function jinbiinit(){
    console.log("time",time);
    times = time*2;
    flag = true;
    if(!time_int)
    time_int = window.setInterval(function(){
        if(!flag)return;
        $(".game .endtime").html(parseInt(times/2));
        times --;
        var jb;
        $(".game .endscre").html(score);
        if( times <=0){
            flag=false;
            $(".game .time").removeClass("shake infinite");
            $(".game .endtime").html('0');
            game_voer();
        }else{
            guanka+=1;
            console.log(guanka);
            jb = new $.jiejinbi(guanka);
            if(times <= 3){
                $(".game .time").addClass("shake infinite");
            }
        }
    },500);
}


// 控制声音函数
function music(){
  //获取声音元件
  var btn_au = $(".fn-audio").find(".btn");
  var audio_switch_btn = true;
  //绑定点击事件
  btn_au.on('click',audio_switch);
  function audio_switch(){
    if($("#car_audio")==undefined){
      return;
    }
    if(audio_switch_btn){
      //关闭声音
      $("#car_audio")[0].pause();
      audio_switch_btn = false;
      $("#car_audio")[0].currentTime = 0;
      btn_au.find("span").eq(0).css("display","none");
      btn_au.find("span").eq(1).css("display","inline-block");
    }
    //开启声音
    else{
      $("#car_audio")[0].play();
      audio_switch_btn = true;
      btn_au.find("span").eq(1).css("display","none");
      btn_au.find("span").eq(0).css("display","inline-block");
    }
  }
}
music();
$("#car_audio")[0].pause();

});
