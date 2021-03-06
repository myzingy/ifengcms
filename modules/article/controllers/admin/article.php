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
 * auth.php
 *
 * Authentication Controller
 *
 * @package			BackendPro
 * @subpackage		Controllers
 */
class article extends Admin_Controller
{
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();

		// Load the Auth_form_processing class
		$this->load->library('articlelib');
		//check('ArticleGame');
		$this->load->helper('article');
		log_message('debug','BackendPro : Auth class loaded');
	}
	function articleList(){
		$limit=array('offset'=>$page,'limit'=>15);
		$data['params']=$this->uri->getParamsArr();
		//$where=array('A.type'=>1);
		$info=$this->article_model->getArticleList($where,$limit,true,$data['params']['classify']);
		//echo $this->article_model->db->last_query();
		$data['members'] = $info['data'];
		$data['pagination']=$info['pagination'];
		// Display Page
		$data['header'] = '新闻管理';
		$data['page'] = $this->config->item('backendpro_template_dir') . "list_article";
		$data['module'] = 'article';
		$data['TYPE']=$this->article_model->_TYPE;
		$data['classify'] =$this->article_model->classifyData();
		$this->load->view($this->_container,$data);
	}
	function update($aid=''){
		$res=$this->article_model->fetch('A','*',null,array('id'=>$aid));
		if($res->num_rows()==1){
			$type=$res->row()->type;
			switch ($type) {
				case '2':
					redirect('special/admin/special/update/'.$aid,'location');
					break;
				case '4':
					redirect('ads/admin/ads/update/'.$aid,'location');
					break;
			}
		}
		$this->articlelib->article_form($this->_container,'admin',$aid);
	}
	function order($id,$order){
		$this->articlelib->order($id,$order);
	}
	function delete()
	{
		if(FALSE === ($selected = $this->input->post('select')))
		{
			redirect($_SERVER['HTTP_REFERER'],'location');
		}
		$claen=$this->input->post('clean');
		if($claen){
			$clean_classify=$this->input->post('clean_classify');
			foreach($selected as $aid)
			{
				$this->article_model->delete('ACL',array('aid'=>$aid,'cid'=>$clean_classify));
			}
		}else{
			foreach($selected as $aid)
			{
				$this->article_model->delete('A',array('id'=>$aid));
				$this->article_model->delete('AC',array('aid'=>$aid));
				$this->article_model->delete('ACL',array('aid'=>$aid));
			}
		}
		
		redirect($_SERVER['HTTP_REFERER'],'location');
	}
	function switchStatus($id,$status){
		$this->article_model->update('A',array('status'=>$status),array('id'=>$id));	
	}
	function classify(){
		$this->articlelib->classify();
	}
	function classifyList(){
		// Display Page
		$data['header'] = '推荐位置管理';
		$data['page'] = $this->config->item('backendpro_template_dir') . "list_classify";
		$data['module'] = 'article';
		$data['classify'] =$this->article_model->classifyData();
		$this->load->view($this->_container,$data);
	}
	function deleteClassify($classifyID=0){
		if($classifyID+0<1){
			die('{"status":10000,"error":"classify is null!"}');
		}
		$limit=array('offset'=>0,'limit'=>1);
		$where=array('A.type'=>1);
		$res=$this->article_model->getArticleList($where,$limit,false,$classifyID);
		if($res->num_rows()>0){
			die(json_encode(array('status'=>10000,'error'=>'此分类下存在新闻，不能直接删除')));
		}
		$this->article_model->delete('C',array('id'=>$classifyID));
		$this->article_model->classifyData('W');
		die('{"status":0}');
	}
	function xwzwb(){
		parent::Ajax_Controller();
		$data['header'] = '新闻早晚报';
		$data['page'] = $this->config->item('backendpro_template_dir') . "xwzwb";
		$data['module'] = 'article';
		$data['data'] =$this->articlelib->xwzwb();
		$this->load->view($this->_container,$data);
	}
}
/* End of file auth.php */
/* Location: ./modules/auth/controllers/auth.php */