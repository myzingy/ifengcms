<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class special extends Public_Controller
{
	/**
	 * Constructor
	 */
	function special(){
		parent::Public_Controller();
		// Load the Auth_form_processing class
		$this->load->library('special_lib');
	}
	function index(){
		
	}
	function form(){
		$this->special_lib->form($this->_container);
	}
}