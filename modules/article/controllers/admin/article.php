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
	function article()
	{
		parent::Admin_Controller();

		// Load the Auth_form_processing class
		$this->load->library('Articlelib');
		//check('ArticleGame');
		log_message('debug','BackendPro : Auth class loaded');
	}
	function index($type='game')
	{
		
	}
	function articleList(){
		$limit=array('offset'=>$page,'limit'=>15);
		$info=$this->article_model->getArticleList(null,$limit,true);
		$data['members'] = $info['data'];
		$data['pagination']=$info['pagination'];

		// Display Page
		$data['header'] = '新闻管理';
		$data['page'] = $this->config->item('backendpro_template_dir') . "list_article";
		$data['module'] = 'article';
		$this->load->view($this->_container,$data);
	}
	function update($aid=''){
		$this->articlelib->article_form($this->_container,'admin',$aid);
	}
	function order($id,$order){
		$this->articlelib->order($id,$order);
	}
	function delete()
	{
		if(FALSE === ($selected = $this->input->post('select')))
		{
			redirect('article/admin/article/index/'.$pagename,'location');
		}
		foreach($selected as $aid)
		{
			$this->article_model->delete('A',array('id'=>$aid));
			$this->article_model->delete('AC',array('aid'=>$aid));
			$this->article_model->delete('ACL',array('aid'=>$aid));
		}
		redirect('article/admin/article/articleList/','location');
	}
	function switchStatus($id,$status){
		$this->article_model->update('FA',array('status'=>$status),array('id'=>$id));	
	}
	function classify(){
		$this->articlelib->classify();
	}
}
/* End of file auth.php */
/* Location: ./modules/auth/controllers/auth.php */