<?php

/**
 * The base controller which is used by the Front and the Admin controllers
 */
class Base_Controller extends CI_Controller
{
	
	public function __construct()
	{
		
		parent::__construct();
		
		force_ssl();
		
		// load the migrations class
		$this->load->library('migration');
		error_reporting(0);
		// Migrate to the latest migration file found
		if ( ! $this->migration->latest())
		{
			log_message('error', 'The migration failed');
		}
		
	}//end __construct()
	
}//end Base_Controller

class Front_Controller extends Base_Controller
{
	
	//we collect the categories automatically with each load rather than for each function
	//this just cuts the codebase down a bit
	var $categories	= '';
	
	//load all the pages into this variable so we can call it from all the methods
	var $pages = '';
	
	// determine whether to display gift card link on all cart pages
	//  This is Not the place to enable gift cards. It is a setting that is loaded during instantiation.
	var $gift_cards_enabled;
	
	function __construct(){
		
		parent::__construct();
		//load BseTec library
		$this->load->library('Bse_tec');
		error_reporting(1);
		$this->config->set_item('language',$this->auth->language());
		$this->lang->load('common');
		//load needed models
		$this->load->model(array('Page_model', 'Product_model', 'Digital_Product_model', 'Order_model', 'Settings_model'));
		
		//load helpers
		$this->load->helper(array('form_helper', 'formatting_helper'));

		//User Current balance calculation (Sum of deposit amount and sales amount)
		
		if($this->Customer_model->is_logged_in(false, false)):

		$name = $this->bse_tec->customer(); 
		
		$this->user_id  = $name['user_id'];
		$this->username = $name['username'];

		$user_balance	= $this->Customer_model->get_current_balance($name['user_id']);
		
		if(@$user_balance->balance == ''){
		$this->customer_balance = '0.00';
		}
		else
		{
		$val = $user_balance->balance;
		$this->customer_balance = round($val,2);
		}
		$cur['user_balance'] = $this->customer_balance;
		$cur['user_id'] = $name['user_id'];
		$this->Customer_model->user_currentbalance_update($cur);
		
		endif;
		
		//End of User Current balance calculation
		$cur = $this->Settings_model->get_settings('currency');
		$this->currency = $cur['currency'];
		//Currency
		
		 $this->db->select('setting');
	 $this->db->where('code', 'withdrawal');
	 $this->db->where('setting_key', 'withdrawal_count_limit');
	 $max	= $this->db->get('settings')->row();
		
		//End of Currency
		


		//fill in our variables
		$this->categories	= $this->Category_model->get_categories_tierd(0);
		$this->pages		= $this->Page_model->get_pages();
		$this->language   = array();
		//create a list of available payment modules
		if ($handle = opendir(APPPATH.'language/')) {
			while (false !== ($file = readdir($handle)))
			{
				//now we eliminate the periods from the list.
				if (!strstr($file, '.'))
				{
					//also, set whether or not they are installed according to our payment settings
					if(@array_key_exists($file, $enabled_modules))
					{
						$this->language[$file]	= true;
					}
					else
					{
						$this->language[$file]	= false;
					}
				}
			}
			closedir($handle);
		}	
		// check if giftcards are enabled
		$gc_setting = $this->Settings_model->get_settings('gift_cards');
		if(!empty($gc_setting['enabled']) && $gc_setting['enabled']==1)
		{
			$this->gift_cards_enabled = true;
		}			
		else
		{
			$this->gift_cards_enabled = false;
		}
		
		//load the theme package
		$this->load->add_package_path(APPPATH.'themes/'.$this->config->item('theme').'/');
	}
	
	/*
	This works exactly like the regular $this->load->view()
	The difference is it automatically pulls in a header and footer.
	*/
	function view($view, $vars = array(), $string=false)
	{
		if($string)
		{
			$result	 = $this->load->view('header', $vars, true);
			$result	.= $this->load->view($view, $vars, true);
			$result	.= $this->load->view('footer', $vars, true);
			
			return $result;
		}
		else
		{
			$this->load->view('header', $vars);
			$this->load->view($view, $vars);
			$this->load->view('footer', $vars);
		}
	}
	
	/*
	This function simple calls $this->load->view()
	*/
	function partial($view, $vars = array(), $string=false)
	{
		if($string)
		{
			return $this->load->view($view, $vars, true);
		}
		else
		{
			$this->load->view($view, $vars);
		}
	}
}

class Admin_Controller extends Base_Controller 
{
	function __construct()
	{
		
		parent::__construct();
		error_reporting(0);
		force_ssl();
		$this->load->library('auth');
		$this->auth->is_logged_in(uri_string());
		$this->config->set_item('language',$this->auth->language());
		//load the base language file
		$this->lang->load('admin_common');
		$this->lang->load('common');
		$this->lang->load('bsepanel');
		$this->load->helper(array('form_helper', 'formatting_helper'));
		
		
		$admin = $this->admin_session->userdata('admin');
		$id=$admin['id'];
		
		$this->admin =  $this->auth->get_admin($id);
		$res=$this->admin->menuname;
		$this->json=json_decode($res);
	}
}