<style>
#form2 {width:200px; margin: 50px auto;}
.form-group2{margin:18px 0;}
.form-group2 input{width:98%; font:14px/20px "微软雅黑"; padding:5px 1%; background:#fff;  color:#252525;}
.form-group2 input:focus{}
.form-group2 .control-label{font:14px/1.5 "微软雅黑"; padding:5px 0;}
.form-group2 p{font:14px/1.5 "微软雅黑"; padding-bottom:10px;}
.form-group2 .submitbtn{width:100%; height:30px; font:14px/30px "微软雅黑"; background-color:#eb4948; color:#fff;  border:0; cursor:pointer;}
.form-group2 .upload{background:#11b2ad;}
</style>
<form id="form2" class="form-horizontal" action="{base_url}index.php/fields/formChuangYePart2/{fieldid}" method="post" enctype="multipart/form-data">
		<input type="hidden" name="codename" id="codename" value="vf33656e11a47">
		<input type="hidden" name="filename" id="filename" value="v90d27532b863">
		<div class="form-group2">
            <input type="text" class="form-control input" placeholder="输入身份证号" name="vf33656e11a47">
        </div>
        <div class="form-group2">
            <input type="file" class="picker upload" name="v90d27532b863"/>
        </div>
        <div class="form-group2">
            <button class="submitbtn">确定提交</button>
        </div>
        <br class="clear"/>
</form>
<script>
	var pagecallback=function(json){
		if(json.rows>0){
			alertx('文案已经提交，感谢你的参与！');
		}else{
			alertx('你还没有报名，报名后才能提交计划书！');
		}
		
	};
	$(function(){
		$('body').css('background','#11b2ad');
	});
</script>