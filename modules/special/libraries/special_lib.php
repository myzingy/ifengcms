<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class special_lib
{
	function special_lib(){
		// Get CI Instance
		$this->CI = &get_instance();
		$this->CI->load->helper('form');
		$this->CI->load->library('validation');
		$this->CI->bep_assets->load_asset_group('FORMS');
		$this->CI->load->model('special_model');
		$this->CI->load->module_model('article','article_model');
	}
	function update($container,$id=0){
		$fields['title'] = "专题名称";
		$fields['src'] = "专题图片";
		$fields['subject'] = "专题简介";
		$fields['special'] = "专题压缩包";
		
		$rules['title'] = 'trim|required|min_length[1]|max_length[255]';
		$rules['subject'] = 'trim|required|min_length[1]|max_length[255]';
		if(!$this->CI->input->post('id') && !$id){
			if (empty($_FILES['src']['name'])){
				$rules['src'] = 'required';
			}
			if (empty($_FILES['special']['name'])){
				$rules['special'] = 'required';
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
			$data['module'] = 'special';
			$data['header'] = '编辑专题';
			$data['classify'] =$this->CI->article_model->classifyData(1);
			//edit data
			$data['editinfo']=array(
				'classify'=>array()
			);
			if($id>0){
				$data['editinfo']=$this->CI->article_model->getArticleAllInfo($id);
				/*
				$res=$this->CI->special_model->fetch('S','*',null,array('id'=>$id));
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
		$data['subject']=$this->CI->input->post('subject');
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
		if ($_FILES['special']['name']){ //需要图片的资讯
			//game thumb
			$path='uploads/special/';
			$dir=FCPATH.$path;
			@mkdir($dir,0777);
			$config['upload_path'] = $dir;
			$config['allowed_types'] = 'zip';
			$config['max_size'] = '20480';
			$config['encrypt_name'] = false;
			//$this->CI->load->library('upload',$config);
			$this->CI->upload->initialize($config);
			if (!$this->CI->upload->do_upload('special'))
			{
				$upload = $this->CI->upload->display_errors();
			} 
			else
			{
				$upload = $this->CI->upload->data();
			}
			if(is_array($upload)){
					
				$unzipfile=$dir.$upload['file_name'];
				$unziptarget=$dir;
				$serName=php_uname('s');
				if(strstr($serName, 'Windows')===false){
					//linux
					exec("unzip -o $unzipfile -d $unziptarget");
				}else{
					include(FCPATH."modules/special/libraries/phpZip.php");
					$archive   = new phpZip();
	        		$zipfile   = $unzipfile;
	        		$savepath  = $unziptarget;
	        		$array     = $archive->GetZipInnerFilesInfo($zipfile);
	        		$filecount = 0;
	        		$dircount  = 0;
	        		$failfiles = array();
	        		//set_time_limit(0);  // 修改为不限制超时时间(默认为30秒)
	        		for($i=0; $i<count($array); $i++) {
	        		    if($array[$i][folder] == 0){
	        		        if($archive->unZip($zipfile, $savepath, $i) > 0){
	        		            $filecount++;
	        		        }else{
	        		            $failfiles[] = $array[$i][filename];
	        		        }
	        		    }else{
	        		        $dircount++;
	        		    }
	        		}
				}
				$data['url']=$path.(preg_replace("/\.zip/","",$_FILES['special']['name']));
			}
		}
		$data['modtime']=TIME;
		$data['type']=2;
		if($this->CI->input->post('id')){//update
			$this->CI->special_model->update('S'
				,$data
				,array(
					'id'=>$data['id']
			));
		}else{
			$data['addtime']=TIME;
			$data['uid']=$this->CI->session->userdata('id');
			$this->CI->special_model->insert('S',$data);
			$data['id']=$this->CI->special_model->db->insert_id();
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
		redirect('special/admin/special/index/','location');
	}
}