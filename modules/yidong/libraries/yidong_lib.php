<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class yidong_lib
{
	function yidong_lib(){
		// Get CI Instance
		$this->CI = &get_instance();
		$this->CI->load->helper('form');
		$this->CI->load->library('validation');
		$this->CI->bep_assets->load_asset_group('FORMS');
		$this->CI->load->model('yidong_model');
	}
	function devices_form($container,$aid=0){
		$fields['id'] = 'ID';
		$fields['name'] = '机型';
		$fields['classify[]']='适用政策';
		$fields['yonghujiaokuan'] = yidong_model::yonghujiaokuan;
		$fields['yuefanhuafei'] = yidong_model::yuefanhuafei;
		$fields['zuidixiaofei'] = yidong_model::zuidixiaofei;
		$fields['heyueqi'] = yidong_model::heyueqi;
		$fields['chanpin'] = yidong_model::chanpin;
		$fields['chanpinneirong'] = yidong_model::chanpinneirong;
		
		// Set Rules
		$rules['name'] = 'trim|required|max_length[32]';
		$rules['classify[]']='required';
		$rules['yonghujiaokuan'] = 'trim|required|integer';
		$rules['yuefanhuafei'] = 'trim|required|integer';
		$rules['zuidixiaofei'] = 'trim|required|integer';
		$rules['heyueqi'] = 'trim|required|integer';
		$rules['chanpin'] = 'trim|required|max_length[200]';
		$rules['chanpinneirong'] = 'trim|required|max_length[200]';
		
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
			$data['header'] = '编辑机型';
			$data['page'] = $this->CI->config->item('backendpro_template_dir') . 'form_devices';
			$data['module'] = 'yidong';
			$data['classify'] =$this->CI->yidong_model->ZHENGCE;
			if($aid>0){
				$data['editinfo']=$this->CI->yidong_model->getDevicesInfo($aid);
			}
			$this->CI->load->view($container,$data);
		}else{
			// Submit form
			return $this->_devices_form($container,$fields);
		}
	}
	function _devices_form($container,$fields){
		//如果有修改图片则进行上传处理
		$imgs=$this->uploadPhoneImgs();
		
		$devices_data=array();
		$classify=array();
		foreach ($fields as $key => $value) {
			if($key=='classify[]'){
				$classify=$this->CI->input->post('classify');
				continue;
			}
			$devices_data[$key]=$this->CI->input->post($key);
			
		}
		$isUpdate=false;
		if($imgs[0]){
			$devices_data['img']=$imgs[0];
		}
		if($devices_data['id']>0){
			$isUpdate=true;
			//修改
			$this->CI->yidong_model->delete('D2C',array('did'=>$devices_data['id']));
			$this->CI->yidong_model->update('D',$devices_data,array('id'=>$devices_data['id']));
		}else{
			$this->CI->yidong_model->insert('D',$devices_data);
			$devices_data['id']=$this->CI->yidong_model->db->insert_id();
		}
		//插入政策关系
		$classify_data=array();
		foreach ($classify as $cid) {
			$classify_data[]=array(
				'did'=>$devices_data['id'],
				'cid'=>$cid
			);
		}
		$this->CI->yidong_model->insert('D2C',$classify_data);
		
		//维护颜色数据
		$colorid=$this->CI->input->post('colorid');
		$color=$this->CI->input->post('color');
		$stock=$this->CI->input->post('stock');
		//清理废弃的颜色
		if($isUpdate){
			$this->CI->yidong_model->unlink_device2color($devices_data['id'],$colorid);
		}
		
		foreach ($colorid as $index=>$c_id) {
			if(!trim($color[$index])) continue;
			
			$dcdata=array(
				'color'=>$color[$index],
				'stock'=>$stock[$index],
				'did'=>$devices_data['id']
			);
			if($imgs[$index]){
				$dcdata['img']=$imgs[$index];
			}
			if($c_id>0){
				$this->CI->yidong_model->update('DC',$dcdata,array('id'=>$c_id));
			}else{
				$this->CI->yidong_model->insert('DC',$dcdata);
			}
		}
		
		flashMsg('success','操作成功');
		redirect('yidong/admin/yidong/devices','location');
		
	}
	function uploadPhoneImgs(){
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
		$data=array();
		//循环处理上传文件
		  foreach ($_FILES as $key => $value) {
		  	
		    if (!empty($value['name'])) {
		      if ($this->CI->upload->do_upload($key)) {
		        //上传成功
		        $upload = $this->CI->upload->data();
		        $data[]=$path.'/'.$upload['file_name'];
		      } else {
		        //上传失败
		        $data[]=false;
		      }
		    }else{
		    	$data[]=false;
		    }
		  }
		  return $data;
	}
}