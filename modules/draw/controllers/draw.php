<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class draw extends Public_Controller
{
	/**
	 * Constructor
	 */
	function draw(){
		parent::Public_Controller();
		// Load the Auth_form_processing class
		$this->load->library('draw_lib');
	}
	function index(){
		
	}
	function form(){
		$this->draw_lib->form($this->_container);
	}
}