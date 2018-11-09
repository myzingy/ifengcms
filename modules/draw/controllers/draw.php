<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class draw extends Public_Controller
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
	function form(){
		$this->draw_lib->form($this->_container);
	}
}