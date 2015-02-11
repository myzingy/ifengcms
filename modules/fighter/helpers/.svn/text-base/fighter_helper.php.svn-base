<?php
if( ! function_exists('fighterNum'))
{
	function fighterNum($popcode="",$field="",$number=0)
	{
		if($popcode){
			$CI = & get_instance();
			$CI->load->model('fighter_model');
			return $CI->fighter_model->setFighterData($popcode,$field,$number);
		}
		
	}
}