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
	function __construct()
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
		$data['article']['url']=$this->CI->input->post('url');
		$subject=$this->CI->input->post('subject');
		$data['article']['subject']=$subject?$subject:preg_replace("/<[^>]+>/", "", $data['content']['content']);
		if ($_FILES['src']['name']){ //需要图片的资讯
			
			//game thumb
			$day=date('Ymd',TIME);
			$path='uploads/'.$day;
			$dir=FCPATH.$path;
			$filename=$day.rand(10000,99999);
			@mkdir($dir,0777);
			$config['upload_path'] = $dir;
			$config['allowed_types'] = 'jpg|jpeg|png|bmp';
			$config['max_size'] = 1024*8;
			$config['max_width']  = 8000;
			$config['max_height']  = 8000;
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
					,'source'=>$data['article']['source']
					//,'type'=>$data['article']['type']
					,'modtime'=>TIME
				);
			if($data['article']['src']){
				$update['src']=$data['article']['src'];
			}
			if($data['article']['url']){
				$update['url']=$data['article']['url'];
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
			$data['article']['modtime']=TIME;
			$data['article']['uid']=$this->CI->session->userdata('id');
			$this->CI->article_model->insert('A',$data['article']);
			$data['article']['id']=$this->CI->article_model->db->insert_id();
		}
		//global content
		$data['content']['aid']=$data['article']['id'];
		
		//img width 100%
		$data['content']['content']=preg_replace("/width[ :=]+[^ ]+/", "", $data['content']['content']);
		$data['content']['content']=preg_replace_callback("/<img[^>]+>/", create_function('$img'
			,'preg_match("/src=([^ ]+)/",$img[0],$arr);'
			.'return "<img src=$arr[1] width=\"100%\">";'
			), $data['content']['content']);
		//die('<textarea style="width:100%; height:500px;">'.$data['content']['content'].'</textarea>');
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
				$fields['url']='外链地址';
				$fields['subject']='新闻简介';
				$fields['content']='新闻详情';
				$fields['classify[]']='新闻分类';
				$fields['src']='资讯 图片';
				
				$rules['url']='trim'; //|valid_url
				$rules['subject']='trim|max_length[255]';
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
				$data['classify'] =$this->CI->article_model->classifyData(1);
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
		$data['type']=$this->CI->input->post('type');
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
	//点赞||收藏
	function article_push($table='AA',$uid){
		$data['aid']=$this->CI->input->post('aid');
		$data['uid']=$uid;
		$res=$this->CI->article_model->fetch($table,'*',null,$data);
		if($res->num_rows()>0){
			return false;
		}
		$data['ip']=$this->CI->input->ip_address();
		$data['addtime']=TIME;
		$this->CI->article_model->insert($table,$data);
		return true;
	}
	//早晚报
	function xwzwb_thumbHeight($oldWidth,$oldHeight,$newWidth){
		return ceil(($newWidth*$oldHeight)/$oldWidth);
	}
	function xwzwb_getImgRes($imgpath,$type){
		$type = image_type_to_extension($type);
		$fun='imagecreatefrom'.substr($type,1);
		eval("\$img=$fun(\$imgpath);");
		return $img;
	}
	function xwzwb_margeImg($img1,$img2,$old_title){
		$new_width=480;
		//标题文字处理
		$font_file = FCPATH.'/assets/fonts/SIMHEI.TTF';
		$font_size=20;
		$font_space=16;
		$tbox=imagettfbbox ($font_size,0,$font_file,"凤凰陕西");
		$title=mb_substr($old_title, 0,(int)($new_width/abs($tbox[6]+$tbox[7])-2),'UTF-8');
		$tbox=imagettfbbox ($font_size,0,$font_file,$title);
		$font_height=abs($tbox[6]+$tbox[7])+$font_space;
		
		list($width1, $height1, $type1, $attr) = getimagesize($img1);
		list($width2, $height2, $type2, $attr) = getimagesize($img2);
		$new_height1=$this->xwzwb_thumbHeight($width1, $height1,$new_width);
		$new_height2=$this->xwzwb_thumbHeight($width2, $height2,$new_width);
		$new_height=$new_height1+$new_height2;
		
		$new_img=imagecreatetruecolor($new_width,$new_height);
		$bgColor = imagecolorallocate($new_img, 255,255,255);
		$c_font_back = imagecolorallocatealpha($new_img, 0, 0, 0,80);
		imagefill($new_img , 0,0 , $bgColor);
		
		$old_img1=$this->xwzwb_getImgRes($img1,$type1);
		imagecopyresized($new_img,$old_img1,0,0,0,0,$new_width,$new_height1,$width1,$height1);
		imagedestroy($old_img1);
		
		//添加文字背景色
		imagefilledrectangle($new_img,0,$new_height1-$font_height,$new_width,$new_height1,$c_font_back);
		//添加文字
		imagettftext($new_img, $font_size, 0, $new_width-$tbox[2]-$font_space, $new_height1-$font_space/2, $bgColor, $font_file, $title);
		
		$old_img2=$this->xwzwb_getImgRes($img2,$type2);
		imagecopyresized($new_img,$old_img2,0,$new_height1,0,0,$new_width,$new_height2,$width2,$height2);
		imagedestroy($old_img2);
		
		@mkdir('uploads/xwzwb/');
		$ymd=date('ymd',TIME);
		@mkdir('uploads/xwzwb/'.$ymd);
		$filename=date('ymdh',TIME).'.jpeg';
		$img_file='uploads/xwzwb/'.$ymd.'/'.$filename;
		imagejpeg($new_img,$img_file);
		imagedestroy($new_img);
		return $img_file;
	}
	function xwzwb(){
		//读取幻灯 limit 1
		$limit=array('offset'=>0,'limit'=>1);
		$where=array('A.type'=>1,'A.status'=>0);
		$classify=1;
		$res=$this->CI->article_model->getArticleList($where,$limit,false,$classify);
		if($res->num_rows()>0){
			$data['img']=$res->row();
		}
		//读取首页新闻
		$limit=array('offset'=>0,'limit'=>10);
		$where=array('A.type'=>1,'A.status'=>0);
		$classify=2;
		$res=$this->CI->article_model->getArticleList($where,$limit,false,$classify);
		if($res->num_rows()>0){
			$data['news']=$res->result();
		}
		//读取首页广告
		$limit=array('offset'=>0,'limit'=>5);
		$where=array('A.type'=>1,'A.status'=>0);
		$classify=3;
		$res=$this->CI->article_model->getArticleList($where,$limit,false,$classify);
		if($res->num_rows()>0){
			$data['ad']=$res->result();
			$data['first_ad']=array_shift($data['ad']);
		}
		//合并幻灯图片与广告图片
		if(file_exists($data['img']->src) && file_exists($data['first_ad']->src)){
			$data['first_ad']->src=$this->xwzwb_margeImg($data['img']->src,$data['first_ad']->src,$data['img']->title);
		}
		return $data;
	}
	//用户发布
	function release($uid){
		$fields['title'] = '新闻标题';
		$fields['content'] = '新闻内容';
		// Set Rules
		$rules['title'] = 'trim|required|max_length[32]';
		$rules['content'] = 'trim|required|max_length[5000]';
		
		$this->CI->validation->set_fields($fields);
		$this->CI->validation->set_rules($rules);
		if ( $this->CI->validation->run() === FALSE )
		{
			return array('status'=>10000,'error'=>$this->CI->validation->_error_array[0]);
		}
		else
		{
			// Submit form
			return $this->_release($uid);
		}
	}
	function _release($uid){
		$data['article']['title']=$this->CI->input->post('title');
		$data['article']['addtime']=TIME;
		$data['article']['modtime']=TIME;
		$data['article']['type']=3;
		$data['article']['status']=$data['article']['type']==1?0:1;
		$data['content']['content']=preg_replace("/<[^>]+>/", "", $this->CI->input->post('content'));
		$data['article']['subject']=$data['content']['content'];
		$data['article']['uid']=$uid;
		
		$this->CI->article_model->insert('A',$data['article']);
		$data['content']['aid']=$this->CI->article_model->db->insert_id();
		if($data['content']['aid']){
			$hideimg=$this->CI->input->post('hideimg');
			if($hideimg){
				$imghtml='';
				foreach($hideimg as $src){
					$imghtml.='<img width="100%" src="'.$src.'">';
				}
				$data['content']['content'].=$imghtml;
			}
			$this->CI->article_model->insert('AC',$data['content']);
			$this->CI->article_model->insert('ACL',array(
				'aid'=>$data['content']['aid'],
				'cid'=>13
			));
		}
		return array('status'=>0);
	}
}
/* End of file Userlib.php */
/* Location: ./modules/auth/libraries/Userlib.php */