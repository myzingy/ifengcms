<?php
if(!function_exists('getArticleUrl')){
	function getArticleUrl($id,$type='',$url=''){
		$ci = &get_instance();
		$ci->load->model('article_model');
		return $ci->article_model->getUrl($id,$type,$url);
	}
}
if(!function_exists('getArticleTypeBlock')){
	function getArticleTypeBlock($type){
		$ci = &get_instance();
		$ci->load->model('article_model');
		$_arr=$ci->article_model->_TYPE;
		return $_arr[$type]['block'];
	}
}
