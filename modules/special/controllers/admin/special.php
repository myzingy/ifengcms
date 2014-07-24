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
		
	}
	function update(){
		$this->special_lib->update($this->_container);
	}
}