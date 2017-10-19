$.extend({
    jiejinbi:function(step){
        var screnn_h = $(window).height();
        this.step = step;
        var that=$.extend(this,{
                create:function(){
                    var gailv = [1,2,3,4,0,5,6,7,8,0];
                    var randomimg = parseInt(Math.random()*10);
                    this.imgdata = gailv[randomimg];
                    this.randomid = parseInt(Math.random()*10000);
                    this.imgleft= parseInt(Math.random()*(4-1+1));
                    var left = (20*this.imgleft)+'%';
                    var html= '<img src="images/good'+this.imgdata+'.png" id='+this.randomid+' class="good" '+' style="left:'+left+';">';
                    $(".game").append(html);

                    this.element = $('#'+this.randomid);//掉落物
                    this.element_p = $('#peple');//碗
                },
                run:function(){
                    var e_top = this.element.position().top;//掉落物的上边
                    var e_left = this.element.position().left;//掉落物的上边
                    var e_bottom = e_top + this.element.height();//掉落物的下边距上方高度
                    var e_outleft= e_left + this.element.width();//掉落物的右边距左边的宽度
                    var e_x = e_left + this.element.width()/2;//掉落物的右边距左边的宽度
                    var e_y = e_top + this.element.height()/2;//掉落物的右边距左边的宽度

                    e_top = e_top + drop_height+(this.step/3)*1;//控制速度

                    if(e_top <= screnn_h){
                        this.element.css({top:e_top});

                        var p_x1 = this.element_p.position().left; //人物中心距左边的宽度
                        var p_y1 = screnn_h - this.element_p.height(); //人物的距上方高度

                        var p_x2 = p_x1+this.element_p.width(); //人物中心距左边的宽度
                        var p_y2 = screnn_h; //人物的距上方高度

                        if( (e_x>=p_x1 && e_x<=p_x2) && ( e_y>=p_y1 && e_y<=p_y2 ) ){
                            this.success();
                        }

                    }else{
                        this.die();
                    }

                },
                die:function(){
                    this.element.remove();
                    window.clearInterval(this.time_int);
                    this.time_int = null;
                },
                success:function(){
                    score+=add_score;
                    $("#peple .addscre").html('+'+add_score);
                    this.die();
                    $("#peple .add_c").show();
                    setTimeout(function(){
                        $("#peple .add_c").hide();
                    },300);
                },
                timer:function(){
                    this.time_int = window.setInterval(function(){
                        if(flag){
                            that.run();
                        }else{
                            if(this.time_int){
                                this.die();
                            }
                        }
                        
                    },50);
                }
            }
        );
        that.create();
        that.timer();
    }
});


