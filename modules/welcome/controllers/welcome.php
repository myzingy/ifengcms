<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * BackendPro
 *
 * An open source development control panel written in PHP
 *
 * @package		BackendPro
 * @author		Adam Price
 * @copyright	Copyright (c) 2008, Adam Price
 * @license		http://www.gnu.org/licenses/lgpl.html
 * @link		http://www.kaydoo.co.uk/projects/backendpro
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Welcome
 *
 * The default welcome controller
 *
 * @package  	BackendPro
 * @subpackage  Controllers
 */
class Welcome extends Public_Controller
{
	function Welcome()
	{
		parent::Public_Controller();
		
	}

	function index()
	{
		// Display Page
		/*
		if($this->isPhone()){
			header("Location:".base_url()."shop/index.html");
		}else{
			header("Location:".base_url()."bootstrap/main.html");	
		}
		 */
		$data['header'] = "Welcome";
		$data['page'] = $this->config->item('backendpro_template_public') . 'welcome';
		$data['module'] = 'welcome';
		$this->load->view($this->_container,$data);
	}
	function upload(){
		$this->load->module_helper('baebcs','baebcs');
		$baidu_bcs=baidu_bcs();
		$res=bsc_upload($baidu_bcs,BASEPATH."codeigniter/CodeIgniter.php",'CodeIgniter2.php');
		var_dump("<pre>",$res);
	}
	public function isPhone(){
		$this->load->library('user_agent');
   		//$this->agent->agent_string()
		//$this->agent->mobile();
		return $this->agent->is_mobile();
   }
}


/* End of file welcome.php */
/* Location: ./modules/welcome/controllers/welcome.php */