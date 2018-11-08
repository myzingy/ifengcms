<?php
$fieldName='pic';
///////////////////////////////////////////////////
$dir="uploads/";
if(!file_exists($dir)){
	mkdir($dir);
}
$data=array('status'=>10000,'msg'=>'请选择文件');
if(is_uploaded_file($_FILES[$fieldName]['tmp_name'])){
	$filename=md5(time());
	$ufname=$_FILES[$fieldName]['name'];
	$fg=preg_match("/\.[^\.]{3,4}$/", $ufname,$match);
	if($fg){
		$filename.=$match[0];
	}else{
		$filename.='.jpg';
	}
	if(move_uploaded_file($_FILES[$fieldName]['tmp_name'], $dir.$filename)){
		$data=array('status'=>0,'msg'=>$dir.$filename);
	}else{
		$data=array('status'=>10000,'msg'=>'上传失败');
	}
}
$json=json_encode($data);
die($json);