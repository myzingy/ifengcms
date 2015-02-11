<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class reply_admin_lib
{
	var $site;		//站点信息
	var $error;		//错误信息
	var $option;	//微信配置参数
	function reply_admin_lib(){
		// Get CI Instance
		$this->CI = &get_instance();
		$this->CI->load->library('validation');
		$this->CI->load->helper('form');
		$this->CI->load->model('reply_model');
		$this->CI->load->module_model('article','article_model');
		include FCPATH."/modules/reply/libraries/wechat.class.php";
		
	}
	function error($str='clear'){
		$error=$this->error;	
		$this->error=$str=='clear'?'':$str;
		return $error;
	}
	function init($site=array()){
		$this->site=$site?$site:array(
			'token'=>'wisheliqrcode',
			'appid'=>'wxc23b7df9375cc300',
			'appsecret'=>'d21884f6ad9f6408c0b69d6d0d970f76'
		);
		$this->site=(object)$this->site;
		$this->setWechatOption();
	}
	//设置微信启动信息
	function setWechatOption($option=array()){
		//$option=$option?$option:$this->site;
		if(!$option){
			$option=array(
				'token'=>$this->site->token,
				'appid'=>$this->site->appid,
				'appsecret'=>$this->site->appsecret,
			);
		}
		$this->option=$option;
		$this->weObj = new Wechat($this->option);
	}
	function keyword($container,$id){
		$data['wechat_name']=$this->CI->reply_model->wechat_name;
		$fields['title'] = "关键字";
		$fields['src'] = "回复图片";
		$fields['subject'] = "回复标题";
		$fields['content'] = "回复描述";
		$fields['url'] = "跳转地址";
		
		$rules['title'] = 'trim|required|min_length[1]|max_length[255]';
		$rules['subject'] = 'trim|required|min_length[1]|max_length[500]';
		$rules['content'] = 'trim|required';

		$this->CI->validation->set_fields($fields);
		$this->CI->validation->set_rules($rules);
		$data['jsform']=rules_ci2js($rules,$fields);
		if ( $this->CI->validation->run() === FALSE )
		{
			$this->CI->bep_assets->load_asset_group('FORMS');	
			// Output any errors
			$this->CI->validation->output_errors();

			// Display page
			$data['page'] = $this->CI->config->item('backendpro_template_dir') . 'form';
			$data['module'] = $this->CI->reply_model->wechat_name;
			$data['header'] = $this->CI->reply_model->wechat_name_cn.' >> 编辑关键字';
			//edit data
			$data['editinfo']=array(
				'classify'=>array()
			);
			if($id>0){
				$this->CI->load->module_model('article','article_model');
				$data['editinfo']=$this->CI->article_model->getArticleAllInfo($id);
			}
			$this->CI->load->view($container,$data);
		}else{
			// Submit form
			return $this->_keyword();
		}
	}
	function _keyword(){
		
		$data['id']=$this->CI->input->post('id');
		$data['title']=$this->CI->input->post('title');
		$data['subject']=$this->CI->input->post('subject');
		$data['url']=$this->CI->input->post('url');
		$this->CI->load->library('upload');
		if ($_FILES['src']['name']){ //需要图片的资讯
			
			//game thumb
			$day=date('Ymd',TIME);
			$path='uploads/'.$day;
			$dir=FCPATH.$path;
			@mkdir($dir,0777);
			$config['upload_path'] = $dir;
			$config['allowed_types'] = 'jpg|jpeg|png|bmp';
			$config['max_size'] = 1024*8;
			$config['max_width']  = 8000;
			$config['max_height']  = 8000;
			$config['encrypt_name'] = true;
			//$this->CI->load->library('upload', $config);
			$this->CI->upload->initialize($config);
			if (!$this->CI->upload->do_upload('src'))
			{
				$upload = $this->CI->upload->display_errors();
			} 
			else
			{
				$upload = $this->CI->upload->data();
			}
			if(is_array($upload)){
				$data['src']=$path.'/'.$upload['file_name'];
			}
		}
		
		$data['modtime']=TIME;
		$data['type']=5;
		if($this->CI->input->post('id')){//update
			$this->CI->article_model->update('A'
				,$data
				,array(
					'id'=>$data['id']
			));
			$this->CI->article_model->update('AC',array(
				'content'=>$this->CI->input->post('content')
			),array('aid'=>$data['id']));
		}else{
			$data['addtime']=TIME;
			$data['uid']=$this->CI->session->userdata('id');
			$this->CI->article_model->insert('A',$data);
			$data['id']=$this->CI->article_model->db->insert_id();
			$this->CI->article_model->insert('AC',array(
				'content'=>$this->CI->input->post('content'),
				'aid'=>$data['id']
			));
		}
		flashMsg('success','操作成功');
		redirect($this->CI->reply_model->wechat_name.'/admin/'.$this->CI->reply_model->wechat_name.'/keylist','location');
	}
	function menu($container){
		$data['wechat_name']=$this->CI->reply_model->wechat_name;
		if ( !$_POST )
		{
			// Output any errors
			$this->CI->validation->output_errors();
			
			//
			$this->init();
			
			$data['menu']=$this->weObj->getMenu();
			
			
			
			// Display page
			$data['header'] = $this->CI->reply_model->wechat_name_cn.' >> 自定义菜单';
			$data['page'] = $this->CI->config->item('backendpro_template_dir') . 'menu';
			$data['module'] = $this->CI->reply_model->wechat_name;
			$this->CI->load->view($container,$data);
		}
		else
		{
			// Submit form
			$this->_menu();
		}
	}
	function _menu(){
		
		$name=$this->CI->input->post('menu');
		$val=$this->CI->input->post('menu_val');
		for($i=0;$i<3;$i++){
			$menu_sub[$i]=array(
				'name'=>$this->CI->input->post('menu_sub'.$i),
				'val'=>$this->CI->input->post('menu_sub_val'.$i)
			);
			
			$data[$i]=$this->getMenuSubArr($name[$i],$val[$i]);
			
			for ($j=0; $j <5 ; $j++) { 
				$subMenu=$this->getMenuSubArr($menu_sub[$i]['name'][$j],$menu_sub[$i]['val'][$j]);
				if($subMenu){
					array_push($data[$i]['sub_button'],$subMenu);
				}
			}
			
		}
		//echo "<pre>";
		//var_dump($data);
		$menu=array('button'=>$data);
		$this->init();
		
		$res=$this->weObj->createMenu($menu);
		if($res){
			flashMsg('info','发布成功。');
			//将发布成功的菜单写入文件
			$this->wxmennuData('W',$menu);
		}else{
			flashMsg('info','发布成功，请检查自定义菜单是否设置正确。');
		}
		redirect($this->CI->reply_model->wechat_name.'/admin/'.$this->CI->reply_model->wechat_name.'/menu','location');
	}
	function getMenuSubArr($name,$val=''){
		if(!$name) return false;
		if(strstr($val,'http')!==false){
			//url
			$data=array(
				'name'=>$name,
				'type'=>'view',
				'url'=>$val,
				'sub_button'=>array()
			);
		}else{
			//keyword
			$data=array(
				'name'=>$name,
				'type'=>'click',
				'key'=>$val,
				'sub_button'=>array()
			);
		}
		return $data;
	}
	function wxmennuData($type='R',$newdata=array()){
		$file=BASEPATH."cache/wx_diymenu_".$this->CI->reply_model->wechat_name;
		if($type=='R'){
			$data=@include($file);
			$data=$data?$data:array();
			return $data;
		}
		if($type=='W'){
			$newdata['menu']=$newdata;
			$data="<?php\nreturn ".var_export($newdata,true).";\n";
			@file_put_contents($file,$data);
		}
	}
	//msg
	function msg($container,$type=''){
		$data['wechat_name']=$this->CI->reply_model->wechat_name;
		$fields['content'] = "回复内容";
		
		$rules['content'] = 'trim|required';

		$this->CI->validation->set_fields($fields);
		$this->CI->validation->set_rules($rules);
		$data['jsform']=rules_ci2js($rules,$fields);
		if ( $this->CI->validation->run() === FALSE )
		{
			$this->CI->bep_assets->load_asset_group('FORMS');	
			// Output any errors
			$this->CI->validation->output_errors();

			// Display page
			$data['page'] = $this->CI->config->item('backendpro_template_dir') . 'formmsg';
			$data['module'] = $this->CI->reply_model->wechat_name;
			$data['header'] = $this->CI->reply_model->wechat_name_cn.' >> 回复信息设置';
			//edit data
			$msg=$this->CI->reply_model->msgData();
			$data['editinfo']['content']=$msg[$type];
			$data['editinfo']['type']=$type;
			$this->CI->load->view($container,$data);
		}else{
			// Submit form
			return $this->_msg();
		}
	}
	function _msg(){
		$type=$this->CI->input->post('type');
		$content=$this->CI->input->post('content');
		$msg=$this->CI->reply_model->msgData();
		$msg[$type]=$content;
		$this->CI->reply_model->msgData('W',$msg);
		redirect($this->CI->reply_model->wechat_name.'/admin/'.$this->CI->reply_model->wechat_name.'/msg/'.$type,'location');
	}
}