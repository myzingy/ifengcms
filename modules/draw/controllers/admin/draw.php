<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class draw extends Admin_Controller
{
	/**
	 * Constructor
	 */
	function __construct(){
		parent::__construct();
		// Load the Auth_form_processing class
		$this->load->library('draw_lib');
	}
	function index(){
		
	}
	function activity(){
		$limit=array('offset'=>$page,'limit'=>30);
		$info=$this->draw_model->getActivityList($where,$limit,true);
		$data['members'] = $info['data'];
		$data['pagination']=$info['pagination'];
		// Display Page
		$data['header'] = '抽奖活动管理';
		$data['page'] = $this->config->item('backendpro_template_dir') . "list_activity";
		$data['module'] = 'draw';
		$this->load->view($this->_container,$data);
	}
	function activity_clear($id=0){
		$this->draw_model->delete('DH',array('did'=>$id));
		$this->draw_model->update('D',array(
			'view_num'=>0,
			'ack_num'=>0,
			'win_num'=>0,
		),array('id'=>$id));
		redirect($_SERVER['HTTP_REFERER'],'location');
	}
	function history($id){
		$where=array('did'=>$id);
		$limit=array('offset'=>$page,'limit'=>30);
		$info=$this->draw_model->getHistoryList($where,$limit,true);
		$data['members'] = $info['data'];
		$data['pagination']=$info['pagination'];
		// Display Page
		$data['header'] = '活动参与列表';
		$data['page'] = $this->config->item('backendpro_template_dir') . "list_history";
		$data['module'] = 'draw';
		$data['id'] = $id;
		//echo $this->draw_model->db->last_query();
		$this->load->view($this->_container,$data);
	}
    function export($id)
    {
		$where=array('did'=>$id);
		$limit=array('offset'=>$page,'limit'=>30);
		$info=$this->draw_model->getHistoryList($where,$limit,true);
		$members = $info['data']->result_array();

        $filename = md5(time());
        header("Content-Type: text/csv");  
        header("Content-Disposition: attachment; filename={$filename}.csv");  
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');  
        header('Expires:0');  
        header('Pragma:public'); 
        
        echo "姓名,电话,奖品,OpenId,中奖时间\n";
        foreach($members as $member){
            $datetime = date('Y-m-d H:i:s', $member['addtime']);
            echo "{$member['name']},{$member['phone']},{$member['pname']},{$member['openid']},{$datetime}\n";
        }
    }
	function prize(){
		$limit=array('offset'=>$page,'limit'=>30);
		$data['params']=$this->uri->getParamsArr();
		//$where=array('A.type'=>1);
		$info=$this->draw_model->getPrizeList($where,$limit,true);
		//echo $this->article_model->db->last_query();
		$data['members'] = $info['data'];
		$data['pagination']=$info['pagination'];
		// Display Page
		$data['header'] = '奖品管理';
		$data['page'] = $this->config->item('backendpro_template_dir') . "list_prize";
		$data['module'] = 'draw';
		$this->load->view($this->_container,$data);
	}
	function form_prize($id=0){
		$this->draw_lib->form_prize($this->_container,$id);
	}
	function form_activity($id=0){
		$this->draw_lib->form_activity($this->_container,$id);
	}
	function delete_prize(){
		if(FALSE === ($selected = $this->input->post('select')))
		{
			redirect($_SERVER['HTTP_REFERER'],'location');
		}
		foreach($selected as $id)
		{
			$this->draw_model->delete('P',array('id'=>$id));
		}
		redirect($_SERVER['HTTP_REFERER'],'location');
	}
	function switchStatus($id,$status){
		$this->draw_model->update('DH',array('status'=>$status),array('id'=>$id));	
	}
}