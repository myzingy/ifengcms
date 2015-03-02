$(document).ready(function(){
  var tzRemoveNum=parseInt($(window).width()*0.6549);
  var base_url=location.href.replace(/fields\/.*/,'');
  var url=base_url+'/index.php/fields/admin/fields/act/';
  var formdata={};
  var formdata_tmp={};
  var id=location.href.match(/id=([0-9]+)/);
	if(id){
		id=id[1];
		$.ajax({
			dataType:'json',
			url:url+'getFieldsData/'+id,
			async:false,
			success:function(json){
				if(json.status==0){
					var data=json.data;
					var fieldsForm='fieldsForm_'+parseInt(Math.random()*1000);
					$('pre').html($('pre').html().replace(/\{ifname\}/g,fieldsForm));
					$('pre').html($('pre').html().replace(/\{iframesrc\}/g,base_url+'index.php/fields/display/'+data.id));
					$('pre').html($('pre').html().replace(/\{iframeheightsrc\}/g,base_url+'index.php/fields/setIframeHeight/'+fieldsForm));
					$('#fieldsForm').attr('src',base_url+'index.php/fields/display/'+data.id);
					$('input[name="id"]','#setp1save_form').val(data.id);
					$('input[name="name"]','#setp1save_form').val(data.name);
					$('input[name="stime"]','#setp1save_form').val(data.stime);
					$('input[name="etime"]','#setp1save_form').val(data.etime);
					if(data.fields_html){
						$("#build").html(data.fields_html.replace(/(&lt;)|(&gt;)/g,function($1,$2){
							switch($1){
								case '&lt;':return '<';
								case '&gt;':return '>';
							}
						}));
					}
					if(data.fields_style){
						$("#style_script").html(data.fields_style);
						$("#source").html(data.fields_style.replace(/[<>]/g,function($1,$2){
							switch($1){
								case '<':return '&lt;';
								case '>':return '&gt;';
							}
							
						}));
					}
					if(data.fields_style_mobile){
						$("#source_mobile").html(data.fields_style_mobile.replace(/[<>]/g,function($1,$2){
							switch($1){
								case '<':return '&lt;';
								case '>':return '&gt;';
							}
							
						}));
					}
					if(data.fields_json){
						formdata=JSON.parse(data.fields_json);
					}
					$('#step2_name').html(data.name);
				}else{
					alert(json.error);
				}
			}
		});
	}
  $("form").delegate(".component", "mousedown", function(md){
    $(".popover").remove();

    md.preventDefault();
    var tops = [];
    var mouseX = md.pageX;
    var mouseY = md.pageY;
    var $temp;
    var timeout;
    var $this = $(this);
    var delays = {
      main: 0,
      form: 120
    }
    var type;

    if($this.parent().parent().parent().parent().attr("id") === "components"){
      type = "main";
    } else {
      type = "form";
    }

    var delayed = setTimeout(function(){
      if(type === "main"){
        $temp = $("<form class='form-horizontal span6' id='temp'></form>").append($this.clone());
      } else {
        if($this.attr("id") !== "legend"){
          $temp = $("<form class='form-horizontal span6' id='temp'></form>").append($this);
        }
      }

      $("body").append($temp);

      $temp.css({"position" : "absolute",
                 "top"      : mouseY - ($temp.height()/2) + "px",
                 "left"     : mouseX - ($temp.width()/2) + "px",
                 "opacity"  : "0.9"}).show()

      var half_box_height = ($temp.height()/2);
      var half_box_width = ($temp.width()/2);
      var $target = $("#target");
      var tar_pos = $target.position();
      var $target_component = $("#target .component");

      $(document).delegate("body", "mousemove", function(mm){

        var mm_mouseX = mm.pageX;
        var mm_mouseY = mm.pageY;

        $temp.css({"top"      : mm_mouseY - half_box_height + "px",
          "left"      : mm_mouseX - half_box_width  + "px"});

        if ( mm_mouseX > tar_pos.left &&
          mm_mouseX < tar_pos.left + $target.width() + $temp.width()/2 &&
          mm_mouseY > tar_pos.top &&
          mm_mouseY < tar_pos.top + $target.height() + $temp.height()/2
          ){
            $("#target").css("background-color", "#fafdff");
            $target_component.css({"border-top" : "1px solid white", "border-bottom" : "none"});
            tops = $.grep($target_component, function(e){
              return ($(e).position().top -  mm_mouseY + half_box_height > 0 && $(e).attr("id") !== "legend");
            });
            if (tops.length > 0){
              $(tops[0]).css("border-top", "1px solid #22aaff");
            } else{
              if($target_component.length > 0){
                $($target_component[$target_component.length - 1]).css("border-bottom", "1px solid #22aaff");
              }
            }
          } else{
            $("#target").css("background-color", "#fff");
            $target_component.css({"border-top" : "1px solid white", "border-bottom" : "none"});
            $target.css("background-color", "#fff");
          }
      });

      $("body").delegate("#temp", "mouseup", function(mu){
        mu.preventDefault();

        var mu_mouseX = mu.pageX;
        var mu_mouseY = mu.pageY;
        var tar_pos = $target.position();

        $("#target .component").css({"border-top" : "1px solid white", "border-bottom" : "none"});

        // acting only if mouse is in right place
        if (mu_mouseX + half_box_width > tar_pos.left &&
          mu_mouseX - half_box_width < tar_pos.left + $target.width() &&
          mu_mouseY + half_box_height > tar_pos.top &&
          mu_mouseY - half_box_height < tar_pos.top + $target.height()
          ){
            $temp.attr("style", null);
            // where to add
            if(tops.length > 0){
              $($temp.html()).insertBefore(tops[0]);
            } else {
              $("#target fieldset").append($temp.append("\n\n\ \ \ \ ").html());
            }
          } else {
            // no add
            $("#target .component").css({"border-top" : "1px solid white", "border-bottom" : "none"});
            tops = [];
          }

        //clean up & add popover
        $target.css("background-color", "#fff");
        $(document).undelegate("body", "mousemove");
        $("body").undelegate("#temp","mouseup");
        $("#target .component").popover({trigger: "manual"});
        $temp.remove();
        if(mu.clientX>tzRemoveNum){
        	delFormdata($temp);
        }
        //genSource();
      });
    }, delays[type]);

    $(document).mouseup(function () {
      clearInterval(delayed);
      return false;
    });
    $(this).mouseout(function () {
      clearInterval(delayed);
      return false;
    });
  });

  var genSource = function(){
  	var $temptxt = $("<div>").html($("#build").html());
    //scrubbbbbbb
    $($temptxt).find(".component").attr({"title": null,
      "data-original-title":null,
      "data-type": null,
      "data-content": null,
      "rel": null,
      "trigger":null,
      "style": null});
    $($temptxt).find(".valtype").attr("data-valtype", null).removeClass("valtype");
    $($temptxt).find(".component").removeClass("component");
    $($temptxt).find("form").attr({
    	"id":"target", 
    	"style":null,
    	"action":base_url+'/index.php/fields/form/'+$('#field_id').val(),
    	"method":"post",
    	"enctype":"multipart/form-data"
    });
    //$("#source").val($temptxt.html().replace(/\n\ \ \ \ \ \ \ \ \ \ \ \ /g,"\n"));
    return $temptxt.html().replace(/\n\ \ \ \ \ \ \ \ \ \ \ \ /g,"\n");
  };
  var genStyleScript = function(){
    var $temptxt = $("#style_script").html();

    $("#source").val($temptxt.replace(/\n\ \ \ \ \ \ \ \ \ \ \ \ /g,"\n"));
  };
  $("#source").change(function(){
  	$("#style_script").html($(this).val());
  });
  //activate legend popover
  $("#target .component").popover({trigger: "manual"});
  //popover on click event
  $("#target").delegate(".component", "click", function(e){
    e.preventDefault();
    //显示form时
    $(".popover").hide();
    var $active_component = $(this);
    $active_component.popover("show");
    var valtypes = $active_component.find(".valtype");
    
    var active_component_id=$active_component.attr('id');
    var dbkey='';
    if(active_component_id){
    	dbkey=active_component_id.replace('field_line_group_','');
    	console.log("showForm.dbkey=>",dbkey);
    	if(dbkey){
    		if(typeof formdata[dbkey]!='undefined'){
      			console.log("showForm.dbkey.isnull=>",formdata[dbkey].isnull);
      			if(formdata[dbkey].isnull){
      				$('.popover form #isnull').attr('checked','checked');
      			}
      			if(formdata[dbkey].rules){
      				$('.popover form #rules').val(formdata[dbkey].rules);
      			}
      			if(formdata[dbkey].filetype){
      				$('.popover form #filetype').val(formdata[dbkey].filetype);
      			}
      			if(formdata[dbkey].filesize){
      				$('.popover form #filesize').val(formdata[dbkey].filesize);
      			}
      		}
      	}
    }
    $.each(valtypes, function(i,e){
      var valID ="#" + $(e).attr("data-valtype");
      var val;
      if(valID ==="#placeholder"){
        val = $(e).attr("placeholder");
        $(".popover " + valID).val(val);
      } else if(valID==="#field_name"){
        val = $(e).val();
        $(".popover " + valID).val(val);
      } else if(valID==="#checkbox"){
        val = $(e).attr("checked");
        $(".popover " + valID).attr("checked",val);
      } else if(valID==="#option"){
        val = $.map($(e).find("option"), function(e,i){return $(e).text()});
        val = val.join("\n")
      $(".popover "+valID).text(val);
      } else if(valID==="#checkboxes"){
        val = $.map($(e).find("label"), function(e,i){return $(e).text().trim()});
        val = val.join("\n")
      $(".popover "+valID).text(val);
      } else if(valID==="#radios"){
        val = $.map($(e).find("label"), function(e,i){return $(e).text().trim()});
        val = val.join("\n");
        $(".popover "+valID).text(val);
        $(".popover #name").val($(e).find("input").attr("name"));
      } else if(valID==="#inline-checkboxes"){
        val = $.map($(e).find("label"), function(e,i){return $(e).text().trim()});
        val = val.join("\n")
          $(".popover "+valID).text(val);
      } else if(valID==="#inline-radios"){
        val = $.map($(e).find("label"), function(e,i){return $(e).text().trim()});
        val = val.join("\n")
          $(".popover "+valID).text(val);
        $(".popover #name").val($(e).find("input").attr("name"));
      } else if(valID==="#button") {
        val = $(e).text();
        var type = $(e).find("button").attr("class").split(" ").filter(function(e){return e.match(/btn-.*/)});
        $(".popover #color option").attr("selected", null);
        if(type.length === 0){
          $(".popover #color #default").attr("selected", "selected");
        } else {
          $(".popover #color #"+type[0]).attr("selected", "selected");
        }
        val = $(e).find(".btn").text();
        $(".popover #button").val(val);
      } else {
        val = $(e).text();
        $(".popover " + valID).val(val);
      }
    });
    //显示完表单后删除老数据
	delFormdata($active_component,true);
	
	//取消保存动作
    $(".popover").delegate(".btn-danger", "click", function(e){
      e.preventDefault();
      $active_component.popover("hide");
      //恢复临时数据
      recoveryFormdata();
    });

    $(".popover").delegate(".btn-info", "click", function(e){
      e.preventDefault();
      var inputs = $(".popover input");
      inputs.push($(".popover textarea")[0]);
      inputs.push($(".popover select")[0]);
      //提交form时
      var data={};
      $.each(inputs, function(i,e){
	      
	      var vartype = $(e).attr("id");
	      var value = $active_component.find('[data-valtype="'+vartype+'"]');
	      console.log("vartype",vartype,$(e).val());
	      
	      data[vartype]=$(e).val();
	      if(vartype==="label" || vartype==="label_imgfile"){
	      	if(vartype==="label_imgfile"){
	      		data['dbkey']='v'+$.md5('label_imgfile').substr(0,12);
	      	}else{
	      		data['dbkey']='v'+$.md5(data[vartype]).substr(0,12);	
	      	}
	      	data['multiple']=false;
	        setFieldName($(value).next(),data['dbkey']);
	        $(value).text($(e).val());
	      } else if(vartype==="isnull"){
	      	console.log("isnull==>",$(e).is(':checked'));
	        data['isnull']=$(e).is(':checked');
	      } else if(vartype==="filetype"){
	      	console.log("filetype==>",$(e).val());
	        data['filetype']=$(e).val();
	      } else if(vartype==="filesize"){
	      	console.log("filesize==>",$(e).val());
	        data['filesize']=$(e).val();
	      } else if(vartype==="rules"){
	      	console.log("rules==>",$(e).val());
	        data['rules']=$(e).val();
	      } else if(vartype==="placeholder"){
	        $(value).attr("placeholder", $(e).val());
	      } else if (vartype==="checkbox"){
	        if($(e).is(":checked")){
	          $(value).attr("checked", true);
	        }
	        else{
	          $(value).attr("checked", false);
	        }
	      } else if (vartype==="option"){
	      	var multiple=$(value).attr('multiple');
	      	if(multiple){
	      		data['multiple']=true;
	      		$(value).attr('name',data['dbkey']+'[]');
	      	}
	      	var options = $(e).val().split("\n");
	        $(value).html("");
	        $.each(options, function(i,e){
	          $(value).append("\n      ");
	          $(value).append($('<option value="'+e+'">').text(e));
	        });
	      } else if (vartype==="checkboxes"){
	      	data['multiple']=true;
	      	var checkboxes = $(e).val().split("\n");
	        $(value).html("\n      <!-- Multiple Checkboxes -->");
	        $.each(checkboxes, function(i,e){
	          if(e.length > 0){
	            $(value).append('\n      <label class="checkbox">\n        <input type="checkbox" value="'+e+'" name="'+data['dbkey']+'[]">\n        '+e+'\n      </label>');
	          }
	        });
	        $(value).append("\n  ")
	      } else if (vartype==="radios"){
	        var radios = $(e).val().split("\n");
	        $(value).html("\n      <!-- Multiple Radios -->");
	        $.each(radios, function(i,e){
	          if(e.length > 0){
	            $(value).append('\n      <label class="radio">\n        <input type="radio" value="'+e+'" name="'+data['dbkey']+'">\n        '+e+'\n      </label>');
	          }
	        });
	        $(value).append("\n  ")
	          $($(value).find("input")[0]).attr("checked", true)
	      } else if (vartype==="inline-checkboxes"){
	      	data['multiple']=true;
	      	var checkboxes = $(e).val().split("\n");
	        $(value).html("\n      <!-- Inline Checkboxes -->");
	        $.each(checkboxes, function(i,e){
	          if(e.length > 0){
	            $(value).append('\n      <label class="checkbox inline">\n        <input type="checkbox" value="'+e+'" name="'+data['dbkey']+'[]">\n        '+e+'\n      </label>');
	          }
	        });
	        $(value).append("\n  ")
	      } else if (vartype==="inline-radios"){
	        var radios = $(e).val().split("\n");
	        $(value).html("\n      <!-- Inline Radios -->");
	        $.each(radios, function(i,e){
	          if(e.length > 0){
	            $(value).append('\n      <label class="radio inline">\n        <input type="radio" value="'+e+'" name="'+data['dbkey']+'">\n        '+e+'\n      </label>');
	          }
	        });
	        $(value).append("\n  ")
	          $($(value).find("input")[0]).attr("checked", true)
	      } else if (vartype === "button"){
	        var type =  $(".popover #color option:selected").attr("id");
	        $(value).find("button").text($(e).val()).attr("class", "btn "+type);
	      } else {
	      	$(value).text($(e).val());
	      }
	    $active_component.popover("hide");
	    genSource();
    });
    setFormdata(data);
    });
  });
  $("#navtab").delegate("#sourcetab", "click", function(e){
    //genStyleScript();
  });
  ///////////////////////
  	//post data
  	$.ajaxSetup({
  		type:'POST',
		dataType:'json',
		beforeSend:function(){
			
		},
		complete:function(){

		},
		error:function(){
			alert('请重新登录');
			//location.href='../index.php/admin';
		}
  	});
  	
  	
	var setFormdata=function(data){
		if(typeof data['dbkey']=='undefined'){
			console.log("setFormdata==>",formdata);
			return;
		}
		if(typeof data.label_imgfile!='undefined'){
			data.label='图片';
		}
		if('d41d8cd98f00'!=data.dbkey){
			formdata[data.dbkey]=data;
		}
		console.log("setFormdata==>",formdata);
		//清空临时数据
		formdata_tmp={};
	};
	var delFormdata=function($dom,flag){
		flag=typeof flag=='undefined'?false:flag;
		var name=$dom.find('input').attr('name');
		if(!name){
			name=$dom.find('select').attr('name');
		}
		if(!name){
			name=$dom.find('textarea').attr('name');
		}
		var newformdata={};
		if(name){
			name=name.replace('[]','');
			for(var i in formdata){
				if(i==name) {
					//保存临时数据
					if(flag){formdata_tmp=formdata[i];}
					continue;
				}
				newformdata[i]=formdata[i];
			}
			formdata=newformdata;
		}
		console.log("delFormdata==>",name,formdata);
	};
	//恢复临时数据
	var recoveryFormdata=function(){
		if(formdata_tmp.dbkey){
			formdata[formdata_tmp.dbkey]=formdata_tmp;
		}
		formdata_tmp={};
		console.log("recoveryFormdata==>",formdata);
	};
	//设置字段name
	var setFieldName=function($div,dbkey){
		if(!dbkey || typeof dbkey=='undefined') return;
		var $t=$div.find("input,select,textarea,button");
	    if($t.length>0){
	    	console.log("setFieldName==>",dbkey);
	    	$t.attr('name',dbkey);
	    }
	    dbkey=dbkey.replace('[]','');
	    $div.parent().attr('id','field_line_group_'+dbkey);
	    $div.prev().attr('id','field_line_lbael_'+dbkey);
	    $div.attr('id','field_line_controls_'+dbkey);
	};
	var saveFields=function(goglag){
		goglag=typeof goglag=='undefined'?false:goglag;
		var fields={
			fields_html:$("#build").html(),
			fields_htmls:genSource(),
			fields_style:$('#source').val(),
			fields_style_mobile:$('#source_mobile').val(),
			fields_json:JSON.stringify(formdata),
			id:$('#field_id').val()
		};
		$.ajax({
			type:'POST',
			dataType:'json',
			url:url+'saveStep2',
			data:fields,
			success:function(json){
				if(json.status!=10000){
					if(json.status!=0){
						alert(json.error);
					}
					if(goglag){
						$('#step2').hide();
						$('#step3').show();
						location.hash='#step3';
					}
					return false;
				}
				alert(json.error);
			}
		});
	};
	$('#setp2save_but').click(function(){
		saveFields();
	});
	$('#setp2save_but_go').click(function(){
		saveFields(true);
	});
	$('#setp1save_but').click(function(){
		$('#setp1save_form').ajaxSubmit({
			type:'POST',
			dataType:'json',
			url:url+'saveStep1',
			success:function(json){
				if(json.status==0){
					if(location.href.indexOf('id=')>-1){
						$('#step1').hide();
						$('#step2').show();
						location.hash='#step2';
						$('#field_id').val(json.id);
						$('#step2_name').html(json.name);
					}else{
						location.href=location.href+'?id='+json.id+'#step2';
					}
					return false;
				}
				alert(json.error);
			}
		});
	});
	$('#setp3save_but').click(function(){
		$('#setp3save_form').ajaxSubmit({
			type:'POST',
			dataType:'json',
			url:url+'saveStep3',
			success:function(json){
				if(json.status==0){
					return false;
				}
				alert(json.error);
			}
		});
	});
	if(location.hash){
		$('#step1,#step2,#step3').hide();
		$(location.hash).show();
	}
	window.onhashchange=function(){
		$('#step1,#step2,#step3').hide();
		if(location.hash){
			$(location.hash).show();
		}else{
			$('#step1').show();
		}
	};
	//切换样式
	$('input[name="source_type"]').click(function(){
		var val=$('input[name="source_type"]:checked').val();
		if(val=='mobile'){
			$('#source').hide();
			$('#source_mobile').show();
			$('#style_script').html($('#source_mobile').val());
		}else{
			$('#source').show();
			$('#source_mobile').hide();
			$('#style_script').html($('#source').val());
		}
	});
	$('input[name="source_type"]:checked').trigger('click');
	//编辑源码
	$('#review_code_but').click(function(){
		var code=genSource();
		$('#textarea_code').val(code);
		$('#myModal').modal('show');
	});
	$('#save_textcode').click(function(){
		$("#build").html($('#textarea_code').val());
		$('#myModal').modal('hide');
	});
});