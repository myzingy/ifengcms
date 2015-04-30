<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class special extends Admin_Controller
{
	/**
	 * Constructor
	 */
	function special(){
		parent::Admin_Controller();
		// Load the Auth_form_processing class
		$this->load->library('special_lib');
	}
	function index(){
		$limit=array('offset'=>$page,'limit'=>15);
		$where=array('S.type'=>2);
		$info=$this->special_model->getSpecialList($where,$limit,true);
		$data['members'] = $info['data'];
		$data['pagination']=$info['pagination'];

		// Display Page
		$data['header'] = '专题管理';
		$data['page'] = $this->config->item('backendpro_template_dir') . "list";
		$data['module'] = 'special';
		$this->load->view($this->_container,$data);
	}
	function update($id=0){
		$this->special_lib->update($this->_container,$id);
	}
	function delete()
	{
		if(FALSE === ($selected = $this->input->post('select')))
		{
			redirect('special/admin/special/index/','location');
		}
		$this->load->helper('file');
		foreach($selected as $aid)
		{
			$res=$this->special_model->fetch('S','*',null,array('id'=>$aid));
			if($res->num_rows()>0){
				$row=$res->row();
				if($row->src){
					@unlink(FCPATH.$row->src);
				}
				if($row->url){
					if(strpos($row->url,'uploads/special')!=false){
						delete_files(FCPATH.$row->url,true);
						@rmdir(FCPATH.$row->url);
						@unlink(FCPATH.$row->url.".zip");
					}
				}
				$this->special_model->delete('S',array('id'=>$aid));
			}
		}
		redirect('special/admin/special/index/','location');
	}
}