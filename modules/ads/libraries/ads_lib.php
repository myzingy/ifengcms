<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class ads_lib
{
	function ads_lib(){
		// Get CI Instance
		$this->CI = &get_instance();
		$this->CI->load->helper('form');
		$this->CI->load->library('validation');
		$this->CI->bep_assets->load_asset_group('FORMS');
		$this->CI->load->module_model('article','article_model');
	}
	function update($container,$id=0){
		$this->CI->bep_assets->load_asset_group('TEXTAREA3');   
		$fields['title'] = "广告名称";
		$fields['src'] = "广告图片";
		$fields['content'] = "广告描述";
		
		$rules['title'] = 'trim|required|min_length[1]|max_length[255]';
		$rules['content'] = 'trim|max_length[5000]';
		if(!$this->CI->input->post('id') && !$id){
			if (empty($_FILES['src']['name'])){
				$rules['src'] = 'required';
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
			$data['page'] = $this->CI->config->item('backendpro_template_dir') . 'form';
			$data['module'] = 'ads';
			$data['header'] = '编辑广告';
			$data['classify'] =$this->CI->article_model->classifyData();
			//edit data
			$data['editinfo']=array(
				'classify'=>array()
			);
			if($id>0){
				$data['editinfo']=$this->CI->article_model->getArticleAllInfo($id);
				/*
				$res=$this->CI->article_model->fetch('S','*',null,array('id'=>$id));
				if($res->num_rows()>0){
					$data['editinfo']=$res->row_array();
				}
				*/
			}
			$this->CI->load->view($container,$data);
		}else{
			// Submit form
			return $this->_update();
		}
	}
	function _update(){
		$data['id']=$this->CI->input->post('id');
		$data['title']=$this->CI->input->post('title');
		$content=$this->CI->input->post('content');
		$data['subject']=preg_replace("/<[^>]+>/", "", $content);
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
		$data['type']=4;
		//content
		$content=preg_replace("/width[ :=]+[^ ]+/", "", $content);
		$content=preg_replace_callback("/<img[^>]+>/", create_function('$img'
			,'preg_match("/src=([^ ]+)/",$img[0],$arr);'
			.'return "<img src=$arr[1] width=\"100%\">";'
			), $content);
		if($this->CI->input->post('id')){//update
			$this->CI->article_model->update('A'
				,$data
				,array(
					'id'=>$data['id']
			));
			$this->CI->article_model->update('AC'
				,array('content'=>$content)
				,array('aid'=>$data['id'])
			);
		}else{
			$data['addtime']=TIME;
			$data['uid']=$this->CI->session->userdata('id');
			$this->CI->article_model->insert('A',$data);
			$data['id']=$this->CI->article_model->db->insert_id();
			$this->CI->article_model->insert('AC',array(
				'aid'=>$data['id'],
				'content'=>$content
			));
		}
		//classify
		$classify=$this->CI->input->post('classify');
		if($classify){
			//clear
			$this->CI->article_model->delete('ACL',array('aid'=>$data['id']));
			foreach($classify as $key){
				$this->CI->article_model->insert('ACL',array('aid'=>$data['id'],'cid'=>$key));
			}
		}
		flashMsg('success','操作成功');
		redirect('ads/admin/ads/index/','location');
	}
}