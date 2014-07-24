<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * BackendPro
 *
 * A website backend system for developers for PHP 4.3.2 or newer
 *
 * @package         BackendPro
 * @author          Adam Price
 * @copyright       Copyright (c) 2008
 * @license         http://www.gnu.org/licenses/lgpl.html
 * @link            http://www.kaydoo.co.uk/projects/backendpro
 * @filesource
 */

// ---------------------------------------------------------------------------

/**
 * Userlib
 *
 * User authentication library used by BackendPro. Permits
 * protecting controllers/methods from un-authorized access.
 *
 * @package			BackendPro
 * @subpackage		Libraries
 */
class Articlelib
{
	var $pagearr=array(
		'admin'=>array('type'=>1,'header'=>'编辑新闻','img'=>'trim'),//required
		'user'=>array('type'=>2,'header'=>'用户新闻','img'=>'trim'),
	);	
	function Articlelib()
	{
		// Get CI Instance
		$this->CI = &get_instance();

		// Load any files directly related to the authentication module
		$this->CI->load->model('article_model');
		$this->CI->load->helper('form');
		$this->CI->load->library('validation');
		$this->CI->bep_assets->load_asset_group('FORMS');
		
		log_message('debug','BackendPro : Articelib class loaded');
	}
	function _atticle_form($pagename)
	{
		$pageinfo=$this->pagearr[$pagename];	
		//global article
		$data['article']['id']=$this->CI->input->post('id');
		$data['article']['title']=$this->CI->input->post('title');
		if($subject=$this->CI->input->post('subject')){
			$data['article']['subject']=$subject;
		}
		$data['article']['addtime']=TIME;
		$data['article']['type']=$this->CI->input->post('type');
		$data['article']['status']=$data['article']['type']==1?0:1;
		$data['article']['source']=$this->CI->input->post('source');//来源
		$data['content']['content']=$this->CI->input->post('content');
		$data['article']['subject']=preg_replace("/<[^>]+>/", "", $data['content']['content']);
		if ($_FILES['src']['name']){ //需要图片的资讯
			
			//game thumb
			$day=date('Ymd',TIME);
			$path='uploads/'.$day;
			$dir=FCPATH.$path;
			$filename=$day.rand(10000,99999);
			@mkdir($dir,0777);
			$config['upload_path'] = $dir;
			$config['allowed_types'] = 'jpg|jpeg|png|bmp';
			$config['max_size'] = '2048';
			$config['max_width']  = '2000';
			$config['max_height']  = '1000';
			//$config['file_name'] = $filename;
			$config['encrypt_name'] = true;
			$this->CI->load->library('upload', $config);
			
			$imgflag=false;
			
			if (!$this->CI->upload->do_upload('src'))
			{
				$upload = $this->CI->upload->display_errors();
			} 
			else
			{
				$upload = $this->CI->upload->data();
			}
			if(is_array($upload)){
				$imgflag=true;
				$data['article']['src']=$path.'/'.$upload['file_name'];
			}
			if(!$imgflag) flashMsg('info','你本次操作没有上传任何图片。');
		}
		if($this->CI->input->post('id')){//update
			$update=array(
					'title'=>$data['article']['title']
					,'type'=>$data['article']['type']
					,'modtime'=>TIME
				);
			if($data['article']['src']){
				$update['src']=$data['article']['src'];
			}
			if($data['article']['subject']){
				$update['subject']=$data['article']['subject'];
			}
			$this->CI->article_model->update('A'
				,$update
				,array(
					'id'=>$data['article']['id']
			));
		}else{
			$this->CI->article_model->insert('A',$data['article']);
			$data['article']['id']=$this->CI->article_model->db->insert_id();
		}
		//global content
		$data['content']['aid']=$data['article']['id'];
		
		//img width 100%
		$data['content']['content']=preg_replace("/width[ :=]+[^ ]+/", "", $data['content']['content']);
		$data['content']['content']=preg_replace("/<img.*src=([^ ]+)[^>]+>/", "<img src=\$1 width=\"100%\"/>", $data['content']['content']);
		
		if($this->CI->input->post('id')){//update
			$this->CI->article_model->update('AC'
				,$data['content']
				,array(
					'aid'=>$data['article']['id']
				));
		}else{
			$this->CI->article_model->insert('AC',$data['content']);
		}
		//classify
		$classify=$this->CI->input->post('classify');
		if($classify){
			//clear
			$this->CI->article_model->delete('ACL',array('aid'=>$data['article']['id']));
			foreach($classify as $key){
				$this->CI->article_model->insert('ACL',array('aid'=>$data['article']['id'],'cid'=>$key));
			}
		}
		
		flashMsg('success','操作成功');
		redirect('article/admin/article/articleList/','location');
	}
	
	/**
	 * Register form
	 *
	 * Display the register form to the user
	 *
	 * @access public
	 * @param string $container View file container
	 */
	function article_form($container,$pagename='admin',$aid=0)
	{
			
        $this->CI->bep_assets->load_asset_group('TEXTAREA3');   
		// Setup fields
		$pageinfo=$this->pagearr[$pagename];
		$fields['title'] = '新闻标题';
		// Set Rules
		$rules['title'] = 'trim|required|max_length[32]';
		$rules['content'] = 'trim|max_length[5000]';
		switch($pagename){
			case "admin":
				$fields['source']='新闻来源';
				$fields['content']='新闻详情';
				$fields['classify[]']='新闻分类';
				$fields['src']='资讯 图片';
				
				$rules['source']='trim|required|max_length[20]';
				$rules['classify[]']='required';
				$rules['content']='trim|required';
				break;
			case "user":
				$fields['content']='资讯详情';
				$fields['farmtype[]']='资讯归类';
				$fields['packprice']='归属订阅包';
				$fields['subject']='资讯简介';
				
				//$rules['farmtype[]']='required';
				$rules['packprice']='required';
				$rules['subject']='trim|required|max_length[250]';
				$rules['content']='trim|required';
				break;
			
		}
		if(!$this->CI->input->post('id') && !$aid){
			if (empty($_FILES['src']['name'])){
				$rules['src'] = $pageinfo['img'];// required || trim
			}
		}
		
		$this->CI->validation->set_fields($fields);
		$this->CI->validation->set_rules($rules);
		$data['jsform']=rules_ci2js($rules,$fields);
		if ( $this->CI->validation->run() === FALSE )
		{
			// Output any errors
			$this->CI->validation->output_errors();

			// Display page
			$data['header'] = $pageinfo['header'];
			$data['page'] = $this->CI->config->item('backendpro_template_dir') . 'form_'.$pagename;
			$data['module'] = 'article';
			if($pagename=='admin'){
				$data['classify'] =$this->CI->article_model->classifyData();
			}
			//edit data
			$data['editinfo']=array(
				'classify'=>array()
			);
			if($aid>0){
				$data['editinfo']=$this->CI->article_model->getArticleAllInfo($aid);
			}	
			$this->CI->load->view($container,$data);
		}
		else
		{
			// Submit form
			$this->_atticle_form($pagename);
		}
	}
	
	function _classify(){
		$data['id']=$this->CI->input->post('id');
		$data['name']=$this->CI->input->post('name');
		if($data['id']>0){
			$this->CI->article_model->update('C',$data,array('id'=>$data['id']));
		}else{
			$this->CI->article_model->insert('C',$data);
			$data['id']=$this->CI->article_model->db->insert_id();
		}
		
		$this->CI->article_model->classifyData('W');
		die('{"status":0,"id":'.$data['id'].'}');
	}
	function classify(){
		// Setup fields
		$fields['name'] = '分类名称';
		// Set Rules
		$rules['name'] = 'trim|required|max_length[32]';
		
		$this->CI->validation->set_fields($fields);
		$this->CI->validation->set_rules($rules);
		$data['jsform']=rules_ci2js($rules,$fields);
		
		if ( $this->CI->validation->run() === FALSE )
		{
			// Output any errors
			die('{"status":-1}');
		}
		else
		{
			// Submit form
			$this->_classify();
		}
	}
	function order($id,$order){
		$this->CI->article_model->update('A',array('order'=>$order+0),array('id'=>$id));
	}
}
/* End of file Userlib.php */
/* Location: ./modules/auth/libraries/Userlib.php */