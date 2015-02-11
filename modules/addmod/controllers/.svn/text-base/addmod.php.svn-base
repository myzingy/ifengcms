<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class addmod extends Public_Controller
{
	/**
	 * Constructor
	 */
	function addmod()
	{
		parent::Public_Controller();
	}
	function index($mod)
	{
		$mod_base=FCPATH."modules/".$mod;
		if(file_exists($mod_base)) return;	
		mkdir($mod_base);
$controllers=<<<END
class $mod extends Public_Controller
{
	/**
	 * Constructor
	 */
	function $mod(){
		parent::Public_Controller();
		// Load the Auth_form_processing class
		\$this->load->library('{$mod}_lib');
	}
	function index(){
		
	}
	function form(){
		\$this->{$mod}_lib->form(\$this->_container);
	}
}
END;
$controllers_admin=<<<END
class {$mod} extends Admin_Controller
{
	/**
	 * Constructor
	 */
	function {$mod}(){
		parent::Admin_Controller();
		// Load the Auth_form_processing class
		\$this->load->library('{$mod}_lib');
	}
	function index(){
		
	}
	function form(){
		\$this->{$mod}_lib->form(\$this->_container);
	}
}
END;
$libraries=<<<END
class {$mod}_lib
{
	function {$mod}_lib(){
		// Get CI Instance
		\$this->CI = &get_instance();
		\$this->CI->load->helper('form');
		\$this->CI->load->library('validation');
		\$this->CI->bep_assets->load_asset_group('FORMS');
		\$this->CI->load->model('{$mod}_model');
	}
	function form(\$container){
		\$fields['fields'] = "fields";
		
		\$rules['fieldsfields'] = 'trim|required|min_length[8]|max_length[32]';
		
		\$this->CI->validation->set_fields(\$fields);
		\$this->CI->validation->set_rules(\$rules);
		if ( \$this->CI->validation->run() === FALSE )
		{
			return array('status'=>1,'error'=>\$this->CI->validation->_error_array[0]);
		}else{
			// Submit form
			return \$this->_from();
		}
	}
	function _from(){
		\$data['fields']=\$this->CI->input->post('fields');
		
		return array('status'=>0);
	}
}
END;
$models=<<<END
class {$mod}_model extends Base_model
{
	function {$mod}_model(){
		parent::Base_model();

		\$this->_prefix = \$this->config->item('weixin_table_prefix');
		\$this->_TABLES = array(
			'U' => \$this->_prefix . 'users'
            ,'UP' => \$this->_prefix . 'user_profiles'
        );
	}
}
END;
		$mod_dir=array(
			'controllers'=>$controllers
			,'controllers_admin'=>$controllers_admin
			,'libraries'=>$libraries
			,'models'=>$models
			,'views'=>null
			,'helpers'=>null
		);
		$mod_name=array(
			'controllers'=>$mod.'.php'
			,'controllers_admin'=>$mod.'.php'
			,'libraries'=>$mod.'_lib.php'
			,'models'=>$mod.'_model.php'
			,'views'=>'list.php'
			,'helpers'=>$mod.'_helper.php'
		);
		foreach($mod_dir as $dir=>$tpl){
			$newdir=$mod_base."/".str_replace("_", "/", $dir);
			mkdir($newdir);
			if($tpl){
				$tpl="<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');\n".$tpl;
				$filename=$newdir.'/'.$mod_name[$dir];
				file_put_contents($filename, $tpl);
			}
		}
	}
}