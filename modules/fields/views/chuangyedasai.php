<form id="form1" class="form-horizontal" action="{base_url}index.php/fields/formChuangYe/{fieldid}" method="post" enctype="multipart/form-data">
		<div class="page2">
			<input type="hidden" name="id" id="id">
			<input type="hidden" name="codename" id="codename">
		</div>
		<div class="col-md-7">
            <div class="form-group page1">
                <label  class="col-md-2">姓名</label>
                <div class="col-md-10">
                  <input type="text" placeholder="姓名" class="input-xlarge" name="v60d0458ac6eb">
                </div>
                <br class="clear"/>
            </div>
            <div class="form-group page1">
                <label  class="col-md-2">身份证</label>
                <div class="col-md-10">
                  <input type="text" placeholder="身份证" class="input-xlarge" name="vf33656e11a47">
                </div>
                <br class="clear"/>
            </div>
            <div class="form-group page2">
                <label  class="col-md-2">电话</label>
                <div class="col-md-10">
                  <input type="text" placeholder="手机号" class="input-xlarge" name="v5a93d3f72d13">
                </div>
                <br class="clear"/>
            </div>
            <div class="form-group page2">
                <label class="col-md-2">学校</label>
                <div class="col-md-10">
                  <input type="text" placeholder="学校名称" class="input-xlarge" name="v413b738061f6">
                </div>
                <br class="clear"/>
            </div>
            <div class="form-group page2">
                <label  class="col-md-2">微信号</label>
                <div class="col-md-10">
                  <input type="text" placeholder="微信号" class="input-xlarge" name="vbcaaeefef600">
                </div>
                <br class="clear"/>
            </div>
        </div>
        <div class="col-md-5">
            <div class="form-group page2">
                <p>照片</p>
					<div class="kiss_upload_photo">
						<div class="photo">
							<div class="kiss_loading"></div>
							<div class="exp"></div>
							<div class="upload_btn webuploader-container">
								<div class="webuploader-pick"></div>
								<div id="rt_rt_18u5iu5do84v44rb941c1j164r1" style="position: absolute; top: 0px; left: 0px; width: 254px; height: 210px; overflow: hidden; bottom: auto; right: auto;">
									<input type="file" name="vaff06ea1d84a" class="webuploader-element-invisible" accept="image/*">
									<label style="opacity: 0; width: 100%; height: 100%; display: block; cursor: pointer; background-color: rgb(255, 255, 255); background-position: initial initial; background-repeat: initial initial;"></label>
								</div>
							</div>
						</div>
					</div>
					<div class="kiss_upload_success">
						<div class="photo">
							<div class="preview">
								<img src="./assets/blank.gif">
							</div>
						</div>
						<div class="msg"></div>
					</div>
				</div>
        </div>
        <br class="clear"/>
        <div class="form-group page2">
            <p>创业宣言</p>
            <textarea type="text" class="form-control col-md-12" rows="6" name="vcee35e67b1a3"></textarea>
            <br class="clear"/>
        </div>
        <div class="form-group btnbox">
            <button class="submitbtn" id="savePage" type="button">提交报名</button>
        </div>
        <br class="clear"/>
</form>
<script>
	$(function(){
		$('.page2').find('input,select,textarea').attr('disabled','disabled');
		var user_avatar='';
		var $mancode=$('.page1').find('input').last();
		//console.log($mancode);
		var checkManCode=function(){
			if(!($mancode.val().length==16 || $mancode.val().length==18)){
				console.log($mancode.val());
				return;
			}
			$.ajax({
				type:'post',
				url:'{base_url}index.php/fields/checkManCode',
				data:'fieldid={fieldid}&codename='+$mancode.attr('name')+'&'+$mancode.attr('name')+'='+$mancode.val(),
				dataType:'json',
				success:function(json){
					if(json.status!=0){
						alertx(json.error);
						return false;
					}
					$('.page2').find('input,select,textarea').removeAttr('disabled');
					//$('.page1').hide();
					//$('.page2').show();
					//$('#step').val(2);
					$('#codename').val($mancode.attr('name'));
					if(json.isexist){
						$('.page2,.page1').find('input,select,textarea').each(function(){
							//console.log(this.name,this.type,json.data[this.name]);
							if(json.data[this.name]){
								if(this.type=='file'){
									//console.log(this,json.data[this.name]);
									user_avatar=json.data[this.name];
									$('.kiss_upload_photo .photo .exp').css({
										'background-image':'url('+user_avatar+')',
										'-webkit-background-size':'100% 100%',
										'background-size':'100% 100%',
										'height':'95%',
										'width':'95%'
									});
									
								}else{
									this.value=json.data[this.name];
								}
							}
						});
						//计划书
						var bookname=$('.page3').find('input[type="file"]').attr('name');
						if(json.data[bookname]){
							$('#myPBook').html('<a href="'+json.data[bookname]+'" target="_blank">你已上传计划书，点此查看。若计划书没有更新不用再次上传。</a>');
						}
					}
				}
			});
		};
		$mancode.blur(function(){
			checkManCode();
		});
		$mancode.change(function(){
			checkManCode();
		});
		$('#savePage').click(function(){
			console.log(user_avatar);
			if(user_avatar){
				$('<input type="text" value="'+user_avatar+'" name="'+$imginput.attr('name')+'"/>').appendTo('.kiss_upload_photo');
	        	$('input[type="file"]','.kiss_upload_photo').remove();
			}
			$('form').trigger('submit');
		});
	});
	var pagecallback=function(json){
		alertx('报名成功，请尽快提交你的创业计划书！');
	};
	var pagecallbackerror=function(){
		$('input[type="text"]','.kiss_upload_photo').attr('type','file');
	};
</script>