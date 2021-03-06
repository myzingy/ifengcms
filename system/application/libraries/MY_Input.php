<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
if (!defined('UTF8_ENABLED')) define('UTF8_ENABLED',false);
class MY_Input extends CI_Input {
	function _clean_input_keys($str)
	{
	    /*if ( ! preg_match("/^[a-z0-9:_\/-]+$/i", $str))
	    {
	        exit('Disallowed Key Characters.');
	    }*/
	    $config = &get_config('config');  
	    if (!empty($config['permitted_uri_chars']))
	    {
	        if ( ! preg_match("/^[".$config['permitted_uri_chars']."]+$/i", rawurlencode($str)))   
	        {   
	            exit('Disallowed Key Characters.');   
	        }  
	    }
	    // Clean UTF-8 if supported
	    if (UTF8_ENABLED === TRUE)
	    {
	        $str = $this->uni->clean_string($str);
	    }
	    return $str;
	}
}