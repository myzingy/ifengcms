<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2010, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Email Class
 *
 * Permits email to be sent using Mail, Sendmail, or SMTP.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/email.html
 */
class CI_Bredis {
	
	var $database		= "yUdUXXImAjlWzdvPrQKw";
	var $redis=null;
	/**
	 * Constructor - Sets Email Preferences
	 *
	 * The constructor can be passed an array of config values
	 */
	function CI_Bredis()
	{
		//$this->log=BaeLog::getInstance();
		$this->hostname		= getenv('HTTP_BAE_ENV_ADDR_REDIS_IP');
		$this->username		= getenv('HTTP_BAE_ENV_AK');
		$this->password		= getenv('HTTP_BAE_ENV_SK');
		$this->port			= getenv('HTTP_BAE_ENV_ADDR_REDIS_PORT');
		$this->conn();
	}
	function conn(){
		
		$this->redis=new Redis();
		return $this->redis;	
		$this->redis = new Redis();
	    $ret = $this->redis->connect($this->hostname, $this->port);
	    if ($ret === false) {
			//$this->log->logDebug('error1:'.$this->redis->getLastError());
	    }
		
	    $ret = $this->redis->auth($this->username . "-" . $this->password . "-" . $this->database);
	    if ($ret === false) {
			//$this->log->logDebug('error2:'.$this->redis->getLastError());
	    }
		return $this->redis;
	}
}
// END CI_Email class

/* End of file Email.php */
/* Location: ./system/libraries/Email.php */