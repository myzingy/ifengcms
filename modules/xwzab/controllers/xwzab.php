<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class xwzab extends Public_Controller
{
	/**
	 * Constructor
	 */
	function xwzab(){
		parent::Public_Controller();
		// Load the Auth_form_processing class
		$this->load->library('xwzab_lib');
	}
	function index(){
		
	}
	function form(){
		$this->xwzab_lib->form($this->_container);
	}
}