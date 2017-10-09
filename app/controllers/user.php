<?php
/*  
* Company name  : BseTec Pvt Ltd.
* Product  name  : Themeforest clone for ecommerce  
* Framework name : Codeigniter
* Dev Team : Gokulnath,Balaji,Suchitra.
* File name : user.php (controller)
*/

class User extends Front_Controller {
	
	var $customer;
	
	function __construct()
	{
		parent::__construct();
		
		$this->config->set_item('language',$this->auth->language());		
		$this->load->model(array('location_model'));
		$this->load->model('digital_product_model');
		$this->lang->load('digital_product');
		$this->load->model('order_model');
		$this->load->model('Badges_model');
		$this->load->library('Paypal_Lib');
		$this->load->helper('date');
		$this->load->language('product');
		$this->load->library('recaptcha');
		$this->customer = $this->bse_tec->customer();
	}
	
	function index()
	{
		show_404();
	}
	
	function login($ajax = false)
	{
		
	$this->load->helper('cookie');
	$this->load->library('breadcrumb');
	$this->breadcrumb->append_crumb('Home', base_url());
	$this->breadcrumb->append_crumb('Login', base_url());
	$breadcrumb = $this->breadcrumb->output();
	$data['breadcrumb'] = $breadcrumb;
		//find out if they're already logged in, if they are redirect them to the my account page
		$redirect	= $this->Customer_model->is_logged_in(false, false);
		 
 		if ($redirect)
		{	
			redirect('dashboard');
		}	
		
		$data['page_title']	= 'Login';
		$data['gift_cards_enabled'] = $this->gift_cards_enabled;
		$this->load->helper('form');
		$data['redirect']	= $this->session->flashdata('redirect');
		$data['address']	= '';
		$data['countries_menu']	= $this->location_model->get_countries_menu();
		$data['fincaptcha'] = $this->recaptcha->recaptcha_get_html();
    		
		$submitted 		= $this->input->post('submitted');
		if ($submitted)
		{	

			$email		= $this->input->post('email');
			$password	= $this->input->post('password');
			$remember   = $this->input->post('remember');
			$redirect	= $this->input->post('redirect');

			$login		= $this->Customer_model->login($email, $password, $remember);

			if ($login)
			{
				if ($redirect == '')
				{
						//if there is not a redirect link, send them to the my account page	
						$redirect = 'dashboard';
				
				}
				//to login via ajax
				if($ajax)
				{
					die(json_encode(array('result'=>true)));
				}
				else
				{
					redirect($redirect);
				}
				
			}
			else
			{
				//this adds the redirect back to flash data if they provide an incorrect credentials
	
				//to login via ajax
				if($ajax)
				{
					die(json_encode(array('result'=>false)));
				}
				else
				{
					$this->session->set_flashdata('redirect', $redirect);
					$this->session->set_flashdata('error', lang('login_failed'));
					
					redirect('register');
				}
			}
		}
		
		$this->load->helper('directory');
		$data['categories']	= $this->Category_model->get_categories_tierd(0);          
    	$data['body_content']			= 'user/register';		
		$this->load->view('template', $data);
		
	}

	function login_popup()
	{
		$data['page_title']	= 'Login';
		$this->load->helper('form');

		$submitted 		= $this->input->post('submitted');
		if ($submitted)
		{
			$email		= $this->input->post('email');
			$password	= $this->input->post('password');
			$remember   = $this->input->post('remember');
			$redirect	= $this->input->post('redirect');
			$login		= $this->Customer_model->login($email, $password, $remember);
			if ($login)
			{
				echo 'true';	
				exit();			
			}
			else
			{
					echo $this->session->set_flashdata('error', lang('login_failed'));
					exit();
			}
		}         
     	
		$this->load->view('template/'.$this->config->item('theme').'/user/login_popup', $data);
	}	
	
	function logout()
	{
		$this->Customer_model->logout();
		redirect('');
	}
	
	function register()
	{
		$this->load->library('breadcrumb');
		$this->breadcrumb->append_crumb('Home', base_url());
		$this->breadcrumb->append_crumb('Register', base_url());
		$breadcrumb = $this->breadcrumb->output();
		$data['breadcrumb'] = $breadcrumb;

		$redirect	= $this->Customer_model->is_logged_in(false, false);
		//if they are logged in, we send them back to the my_account by default
		if ($redirect)
		{
			redirect('dashboard');
		}
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<div>', '</div>');

		$data['redirect']	= $this->session->flashdata('redirect');
		
		$data['page_title']	= lang('account_registration');
		//$data['gift_cards_enabled'] = $this->gift_cards_enabled;
		
		//default values are empty if the customer is new

		$data['username']	= '';
		$data['firstname']	= '';
		$data['lastname']	= '';
		$data['email']		= '';
		$data['phone']		= '';
		$data['address1']	= '';
		$data['address2']	= '';
		$data['city']		= '';
		$data['state']		= '';
		$data['zip']		= '';
		$data['address'] = ''; //country id
		$data['countries_menu']	= $this->location_model->get_countries_menu();
		$data['fincaptcha'] = $this->recaptcha->recaptcha_get_html();

		$this->form_validation->set_rules('username', 'Username', 'trim|max_length[20]|callback_check_username');
		$this->form_validation->set_rules('firstname', 'First Name', 'trim|required|max_length[32]');
		$this->form_validation->set_rules('lastname', 'Last Name', 'trim|required|max_length[32]');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[128]|callback_check_email');
		$this->form_validation->set_rules('phone', 'Phone', 'trim|required|max_length[32]');
		$this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|sha1');
		$this->form_validation->set_rules('confirm', 'Confirm Password', 'required|matches[password]');
		$this->form_validation->set_rules('email_subscribe', 'Subscribe', 'trim|numeric|max_length[1]');
    		

		if ($this->form_validation->run() == FALSE)
		{
			$this->load->helper('directory');
			$data['error'] = validation_errors();	
			$data['body_content']			= 'user/register';
			$this->load->view('template', $data);
		}
		else
		{
		$this->recaptcha->recaptcha_check_answer($_SERVER['REMOTE_ADDR'],$this->input->post('recaptcha_challenge_field'),$this->input->post('recaptcha_response_field'));
			if(!$this->recaptcha->getIsValid()) {
			$this->load->helper('directory');
         $data['error'] = 'incorrect captcha';
			$data['body_content']			= 'user/register';
			$this->load->view('template', $data);
			}
			else{
			$save['user_id']		= false;	
			$save['user_firstname']			= $this->input->post('firstname');
			$save['user_lastname']			= $this->input->post('lastname');
			$save['user_email']			= $this->input->post('email');
			$save['phone']				= $this->input->post('phone');
			$save['username']			= $this->input->post('username');
			$save['status']				= $this->config->item('new_customer_status');
			$save['password']			= $this->input->post('password');
			$country = $this->location_model->get_country($this->input->post('address'));
			$save['address']			= $country->name;
			
			$redirect					= $this->input->post('redirect');
			
			// New User register using referral:
			if($this->session->userdata('referral')) 
			{
			$ref['user_id'] = $this->session->userdata('referral');
			
			$credit = $this->Settings_model->get_settings('credits'); 
			$ref_comm = $credit['new_user_credits'];
			$ref['currency_code'] = 'USD';
			$ref_user_current_bal = $this->Customer_model->get_current_balance($ref['user_id']);
			$ref['balance'] =  $ref_user_current_bal->balance + $ref_comm; 
			$ref['text'] = 'User Referral commission($'.$ref_comm.')';
		   $this->Customer_model->purchase_from_deposit($ref);	   
		   } 
		   		  		   
		   //End of New User register using referral
			
			//if we don't have a value for redirect
			if ($redirect == '')
			{
				$redirect = 'dashboard';
			}
			
			// save the customer info and get their new id
			$id = $this->Customer_model->save($save);

			/* send an email */
			// get the email template
			$res = $this->db->where('id', '6')->get('canned_messages');
			$row = $res->row_array();
			
			// set replacement values for subject & body
			
			// {customer_name}
			$row['subject'] = str_replace('{customer_name}', $this->input->post('firstname').' '. $this->input->post('lastname'), $row['subject']);
			$row['content'] = str_replace('{customer_name}', $this->input->post('firstname').' '. $this->input->post('lastname'), $row['content']);		
			$row['content'] = str_replace('{email}', $this->input->post('email'), $row['content']);
			$row['content'] = str_replace('{password}', $this->input->post('confirm'), $row['content']);
			
			// {url}
			$row['subject'] = str_replace('{url}', $this->config->item('base_url'), $row['subject']);
			$row['content'] = str_replace('{url}', $this->config->item('base_url'), $row['content']);
			
			// {site_name}
			$row['subject'] = str_replace('{site_name}', $this->config->item('company_name'), $row['subject']);
			$row['content'] = str_replace('{site_name}', $this->config->item('company_name'), $row['content']);
			
			$this->load->library('email');
			
			$config['mailtype'] = 'html';
			
			$this->email->initialize($config);
	
			$this->email->from($this->config->item('email'), $this->config->item('company_name'));
			$this->email->to($save['user_email']);
			$this->email->bcc($this->config->item('email'));
			$this->email->subject($row['subject']);
			$this->email->message(html_entity_decode($row['content']));
			
			$this->email->send();
			
			$this->session->set_flashdata('message', sprintf( lang('registration_thanks'), $this->input->post('firstname') ) );
			
			//lets automatically log them in
			$this->Customer_model->login($save['user_email'], $this->input->post('confirm'));
			
			//we're just going to make this secure regardless, because we don't know if they are
			//wanting to redirect to an insecure location, if it needs to be secured then we can use the secure redirect in the controller
			//to redirect them, if there is no redirect, the it should redirect to the homepage.
			redirect($redirect);
			}
		}
	}
	
	function check_email($str)
	{
		if(!empty($this->customer['id']))
		{
			$email = $this->Customer_model->check_email($str, $this->customer['id']);
		}
		else
		{
			$email = $this->Customer_model->check_email($str);
		}
		
        if ($email)
       	{
			$this->form_validation->set_message('check_email', lang('error_email'));
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

function check_username($str)
	{
		if(!empty($this->customer['id']))
		{
			$email = $this->Customer_model->check_username($str, $this->customer['id']);
		}
		else
		{
			$email = $this->Customer_model->check_username($str);
		}
		
        if ($email)
       	{
			$this->form_validation->set_message('check_username', 'Username Already exist');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}		
	
	
	function forgot_password()
	{
		$this->load->library('breadcrumb');
$this->breadcrumb->append_crumb('Home', base_url());
$this->breadcrumb->append_crumb('Forgot Password', base_url());
$breadcrumb = $this->breadcrumb->output();
	$data['breadcrumb'] = $breadcrumb;
		$redirect	= $this->Customer_model->is_logged_in(false, false);
		//if they are logged in, we send them back to the my_account by default
		if ($redirect)
		{
			redirect('dashboard');
		}
		$data['page_title']	= lang('forgot_password');
		$data['gift_cards_enabled'] = $this->gift_cards_enabled;
		$submitted = $this->input->post('submitted');
		if ($submitted)
		{
			$this->load->helper('string');
			$email = $this->input->post('email');
			
			$reset = $this->Customer_model->reset_password($email);
			
			if ($reset)
			{						
				$this->session->set_flashdata('message', lang('message_new_password'));
			}
			else
			{
				$this->session->set_flashdata('error', lang('error_no_account_record'));
			}
			redirect('user/forgot_password');
		}
		
		// load other page content 
		$this->load->helper('directory');
	
		//if they want to limit to the top 5 banners and use the enable/disable on dates, add true to the get_banners function
		
		$data['categories']	= $this->Category_model->get_categories_tierd();
		$data['body_content']			= 'user/forgot_password';
		$this->load->view('template', $data);
			}
	
	function my_account($offset=0)
	{
		$this->load->library('breadcrumb');
		// add breadcrumbs
		 $this->breadcrumb->append_crumb('Home', base_url());
		 $this->breadcrumb->append_crumb('Dashboard', base_url().'dashboard');
		// put this line in view to output
		$breadcrumb = $this->breadcrumb->output();
		$data['breadcrumb']	= $breadcrumb;
		//make sure they're logged in
		$this->Customer_model->is_logged_in('dashboard');
		$data['active_page']	=  'my_account_page';
		$data['gift_cards_enabled']	= $this->gift_cards_enabled;
		
		$data['customer']			= (array)$this->Customer_model->get_customer($this->customer['user_id']);
		$data['user_sales']	= $this->Customer_model->get_customer_sales($this->customer['user_id'],$offset);
		
		$data['total_sales_percentage']	= $this->Customer_model->get_percentage($this->customer['user_id']);	  		
		$data['total_sales']	= $this->Customer_model->get_sales_total($this->customer['user_id']);
		$data['page_title']			= 'Welcome '.$data['customer']['user_firstname'].' '.$data['customer']['user_lastname'];
		
		//Marketplus version 1.5
		$quiz = $this->Settings_model->get_settings(quiz);
		$data['quiz_status'] = $quiz['enable'];
		//Marketplus version 1.5 close
				
		// load other page content 
		//$this->load->model('banner_model');
		$this->load->model('order_model');
		$this->load->helper('directory');
		$this->load->helper('date');
		
		//if they want to limit to the top 5 banners and use the enable/disable on dates, add true to the get_banners function
		$data['categories']	= $this->Category_model->get_categories_tierd(0);
				
		//PROFILE PICTURE
			
		// paginate the orders
		$this->load->library('pagination');
		$data['orders']		= $this->order_model->get_customer_orders($this->customer['user_id'], $offset);	
		//if they're logged in, then we have all their acct. info in the cookie.
	if(!empty($_FILES['userfile']['name'])) {
	
		$config['upload_path'] = './uploads/profile/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size']	= '100';
		$config['max_width']  = '1024';
		$config['max_height']  = '768';

		$this->load->library('upload', $config);

			if ( ! $this->upload->do_upload('userfile'))
			{
				$data = array('error' => $this->upload->display_errors());
	
			}
			else
			{
				$data = array('upload_data' => $this->upload->data());
			}		
		}
		/*
		This is for the customers to be able to edit their account information
		*/

		$this->load->library('form_validation');	
		$this->form_validation->set_rules('company', 'Company', 'trim|max_length[128]');
		$this->form_validation->set_rules('firstname', 'First Name', 'trim|required|max_length[32]');
		$this->form_validation->set_rules('lastname', 'Last Name', 'trim|required|max_length[32]');
		//$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[128]|callback_check_email');
		$this->form_validation->set_rules('phone', 'Phone', 'trim|required|max_length[32]');
		$this->form_validation->set_rules('email_subscribe', 'Subscribe', 'trim|numeric|max_length[1]');

		if($this->input->post('password') != '' || $this->input->post('confirm') != '')
		{
			$this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|sha1');
			$this->form_validation->set_rules('confirm', 'Confirm Password', 'required|matches[password]');
		}
		else
		{
			$this->form_validation->set_rules('password', 'Password');
			$this->form_validation->set_rules('confirm', 'Confirm Password');
		}

		if ($this->form_validation->run() == FALSE)
		{
		$data['body_content']			= 'user/my_account';
		$this->load->view('template', $data);
			
			}
else
		{
		
		$avatar =$this->input->post('imagename');
			$customer = array();
			$customer['user_id']						= $this->customer['user_id'];
			$customer['company']				= $this->input->post('company');
			$customer['user_firstname']				= $this->input->post('firstname');
			$customer['user_lastname']				= $this->input->post('lastname');
			$customer['user_email']					= $this->input->post('email');
			$customer['phone']					= $this->input->post('phone');
			$customer['aboutme']					= $this->input->post('aboutme');

			$customer['facebook']					= $this->input->post('facebook');
			$customer['twitter']					= $this->input->post('twitter');
			$customer['google']					= $this->input->post('google');
			$customer['rssfeed']					= $this->input->post('rssfeed');
			$customer['youtupe']					= $this->input->post('youtupe');

          if (!empty($avatar))		{   
		   $customer['avatar']					= $this->input->post('imagename'); }
		   $customer['email_subscribe']		= intval((bool)$this->input->post('email_subscribe'));
			if($this->input->post('password') != '')
			{
				$customer['password']			= $this->input->post('password');
			}
///////////////////
		//avatar
	$targ_w = $targ_h = 120;
	$jpeg_quality = 120;

	$src = 'uploads/profile/'.$this->input->post('imagename');

	//Get the new coordinates to crop the image.
	$x1 = $_POST["x"];
	$y1 = $_POST["y"];
	$w = $_POST["w"];
	$h = $_POST["h"];
	//Scale the image to the 100px by 100px
	@$scale = 120/$w;
	$cropped = $this->resizeThumbnailImage($src, $src,$w,$h,$x1,$y1,$scale);
 
	//avatar	
	//////////////////	
			$this->bse_tec->save_customer($this->customer);
			$this->Customer_model->save($customer);
			
			$this->session->set_flashdata('message', lang('message_account_updated'));
			
			redirect('dashboard','refresh');
		}
	
	}

  // user deposit
  function my_deposit()
  
  {
	  $this->load->library('breadcrumb');
$this->breadcrumb->append_crumb('Home', base_url());
$this->breadcrumb->append_crumb('Deposit', base_url());
$breadcrumb = $this->breadcrumb->output();
	$data['breadcrumb'] = $breadcrumb;
  	   $this->Customer_model->is_logged_in($this->uri->uri_string());
  		$data['body_content']			= 'user/user_deposit';
		$this->load->view('template', $data);
  	}
  	
   // user withdraw
  function my_withdraw() 
  {
	  $this->load->library('breadcrumb');
$this->breadcrumb->append_crumb('Home', base_url());
$this->breadcrumb->append_crumb('Withdrawals', base_url());
$breadcrumb = $this->breadcrumb->output();
	$data['breadcrumb'] = $breadcrumb;
  		$offset = 0;
  		$this->Customer_model->is_logged_in($this->uri->uri_string());
  		$data['customer']			= $this->bse_tec->customer();
  		$user_balance	= $this->Customer_model->get_current_balance($this->customer['user_id']);
		if($user_balance=='')
		$data['user_balance']= 0 ;
		else
		$data['user_balance'] = $user_balance;

		$data['user_sales']	= $this->Customer_model->get_customer_sales($this->customer['user_id'],$offset);	  	
		$data['total_sales_percentage']	= $this->Customer_model->get_percentage($this->customer['user_id']);	  		
		$data['total_sales']	= $this->Customer_model->get_sales_total($this->customer['user_id']);	 
		
		$this->db->select('setting');
	   $this->db->where('code', 'withdrawal');
	   $this->db->where('setting_key', 'withdrawal_count_limit');
	   $withdrw	= $this->db->get('settings')->row();
	   
	  	   
	   $this->db->select_sum('trans_amount');
		$this->db->where('user_id',$this->customer['user_id']);
		//$this->db->where('status', '0');
		$this->db->where('trans_type', 'Withdraw');
		$withdrwamt	= $this->db->get('user_transactions')->row();		
	   
	   $data['tot_withdrawamt'] = $withdrwamt->trans_amount; // Total Withdraw amount
	  
	   $data['min_withdraw'] = $withdrw->setting; //Minimum amount
	   
	   $this->db->select('setting');
	   $this->db->where('code', 'withdrawal');
	   $this->db->where('setting_key', 'withdrawal_balance');
	   $withdrw_bal	= $this->db->get('settings')->row();
	   
	   $data['min_withdraw_bal'] = $withdrw_bal->setting; //Minimum balance
	 
	 	$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<div>', '</div>');		
		$this->form_validation->set_rules('amount', 'Amount', 'trim|numeric|required');  
		$this->form_validation->set_rules('paymethod', 'Payment Method', 'trim|required');  
		$this->form_validation->set_rules('payemail_address', 'Paypal Email', 'trim|required|valid_email|max_length[128]');  
		$this->form_validation->set_rules('payemail_address_confirm', 'Paypal Confirm Email', 'trim|required|matches[payemail_address]');
		if ($this->form_validation->run() == FALSE)
		{
			$data['error'] = validation_errors();
			//$this->load->view('register', $data);
			$data['body_content']			= 'user/user_withdraw';
			$this->load->view('template', $data);
		}
		else
		{
			$withdraw['user_id'] = $this->customer['user_id'];
       	$withdraw['trans_type'] = "Withdraw";   	  
		 	$withdraw['status'] = "0";   	         
       	$withdraw['currency_code'] = "USD";	
			$withdraw['trans_amount'] = $this->input->post('amount');
			$withdraw['trans_method'] = $this->input->post('paymethod');
			$withdraw['paypal_receiver_email'] = $this->input->post('payemail_address');
			$trans_number = $this->Customer_model->withdraw_request($withdraw);  	
				
			if($trans_number) {
							$this->load->model('messages_model');
							$row = $this->messages_model->get_message(10);							
							$row['subject'] = str_replace('{site name}', $this->config->item('company_name'), $row['subject']);
	   	 				$row['content'] = str_replace('{user name}', $data['customer']['user_firstname'].' '.$data['customer']['user_lastname'], $row['content']);
							$row['content'] = str_replace('{paypal email}', $this->input->post('payemail_address'), $row['content']);
							$this->load->library('email');
					$config['mailtype'] = 'html';
					$this->email->initialize($config);
		
					$this->email->from($this->config->item('email'), $this->config->item('company_name'));
		
					$this->email->to($data['customer']['user_email']);
		
					//email the admin
					$this->email->bcc($this->config->item('email'));
					$this->email->subject($row['subject']);
					$this->email->message($row['content']);
					$this->email->send();
					
					$data['page_title']	= 'withdraw request success ';
				$this->session->set_flashdata('message', sprintf( lang('withdraw request success') ) );
				redirect('user/my_withdraw');
  		} else {
  	$data['page_title']	= 'withdraw request';
   $this->session->set_flashdata('message', sprintf( lang('withdraw request fail') ) );
  	redirect('withdraw');      
  	      }
  	 	}       
  	}

	 // user balance
  function my_balance($offset=0)
  {
	  $this->load->library('breadcrumb');
$this->breadcrumb->append_crumb('Home', base_url());
$this->breadcrumb->append_crumb('My Balance', base_url());
$breadcrumb = $this->breadcrumb->output();
	$data['breadcrumb'] = $breadcrumb;
  		$this->Customer_model->is_logged_in($this->uri->uri_string());
  		$this->load->library('pagination');
  		$config['base_url'] = site_url('MyBalance');
		$count = $this->Customer_model->get_customer_sales($this->customer['user_id'],$offset);
		$total_count = count($count); 		
		$config['total_rows'] = "$total_count";
		$config['per_page'] = '5'; 
	
		$config['first_link'] = 'First';
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		$config['last_link'] = 'Last';
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';

		$config['full_tag_open'] = '<div class="pagination"><ul>';
		$config['full_tag_close'] = '</ul></div>';
		$config['cur_tag_open'] = '<li class="active"><a href="#">';
		$config['cur_tag_close'] = '</a></li>';

		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';

		$config['prev_link'] = '&laquo;';
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';

		$config['next_link'] = '&raquo;';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		
		$this->pagination->initialize($config); 
		
		$data['sales_pagination'] = $this->pagination->create_links();

  		$data['user_balance']	= $this->Customer_model->get_current_balance($this->customer['user_id']);	  	
		$data['user_sales']	= $this->Customer_model->get_customer_sales($this->customer['user_id'],$offset);	  	
		$data['total_sales_percentage']	= $this->Customer_model->get_percentage($this->customer['user_id']);	  		
		$data['total_sales']	= $this->Customer_model->get_sales_total($this->customer['user_id']);
		
		$data['tot_withdrawl'] = $this->Customer_model->get_total_withdrawal($this->customer['user_id']);	  
		
		$data['tot_purchase'] = $this->Customer_model->get_totalpurchase($this->customer['user_id']);
		
		$data['tot_deposit'] = $this->Customer_model->get_total_deposit($this->customer['user_id']);	 
		
		$data['body_content']			= 'user/user_balance';
		$this->load->view('template', $data);
  	
  	}

   // order history
  function order_history($offset=0)
  {
	  $this->load->library('breadcrumb');
$this->breadcrumb->append_crumb('Home', base_url());
$this->breadcrumb->append_crumb('Order History', base_url());
$breadcrumb = $this->breadcrumb->output();
	$data['breadcrumb'] = $breadcrumb;
		$this->Customer_model->is_logged_in($this->uri->uri_string());
		$this->load->model('order_model');
		$this->load->helper('directory');
		$this->load->helper('date');
		
	//if they want to limit to the top 5 banners and use the enable/disable on dates, add true to the get_banners function
		$data['categories']	= $this->Category_model->get_categories_tierd(0);
		// paginate the orders
		$this->load->library('pagination');

		$config['base_url'] = site_url('user/order_history');
		$config['total_rows'] = $this->order_model->count_customer_orders($this->customer['user_id']);
		$config['per_page'] = '15'; 
	
		$config['first_link'] = 'First';
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		$config['last_link'] = 'Last';
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';

		$config['full_tag_open'] = '<div class="pagination"><ul>';
		$config['full_tag_close'] = '</ul></div>';
		$config['cur_tag_open'] = '<li class="active"><a href="#">';
		$config['cur_tag_close'] = '</a></li>';

		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';

		$config['prev_link'] = '&laquo;';
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';

		$config['next_link'] = '&raquo;';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		
		$this->pagination->initialize($config); 
		
		$data['orders_pagination'] = $this->pagination->create_links();
		$data['orders']		= $this->order_model->get_customer_orders($this->customer['user_id'], $offset);
  		$data['body_content']			= 'user/order_history';		
		$this->load->view('template', $data);
  	}

// Update account

	function update_account($offset=0)
	{
		$this->load->library('breadcrumb');
$this->breadcrumb->append_crumb('Home', base_url());
$this->breadcrumb->append_crumb('Update Account', base_url());
$breadcrumb = $this->breadcrumb->output();
	$data['breadcrumb'] = $breadcrumb;
		//make sure they're logged in
		$this->Customer_model->is_logged_in('user/update_account/');
		
		$data['customer']			= (array)$this->Customer_model->get_customer($this->customer['user_id']);
				
		$data['page_title']			= 'Welcome '.$data['customer']['user_firstname'].' '.$data['customer']['user_lastname'];
		
		$this->load->helper('date');

		$this->load->library('form_validation');	
		$this->form_validation->set_rules('company', 'Company', 'trim|max_length[128]');
		$this->form_validation->set_rules('firstname', 'First Name', 'trim|required|max_length[32]');
		$this->form_validation->set_rules('lastname', 'Last Name', 'trim|required|max_length[32]');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[128]|callback_check_email');
		$this->form_validation->set_rules('phone', 'Phone', 'trim|required|max_length[32]');
		$this->form_validation->set_rules('email_subscribe', 'Subscribe', 'trim|numeric|max_length[1]');

		if($this->input->post('password') != '' || $this->input->post('confirm') != '')
		{
			$this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|sha1');
			$this->form_validation->set_rules('confirm', 'Confirm Password', 'required|matches[password]');
		}
		else
		{
			$this->form_validation->set_rules('password', 'Password');
			$this->form_validation->set_rules('confirm', 'Confirm Password');
		}

		if ($this->form_validation->run() == FALSE)
		{
		$data['body_content']			= 'user/update_account';
		
		$this->load->view('template', $data);
			}
		else
		{
			$customer = array();
			$customer['id']						= $this->customer['id'];
			$customer['company']				= $this->input->post('company');
			$customer['firstname']				= $this->input->post('firstname');
			$customer['lastname']				= $this->input->post('lastname');
			$customer['email']					= $this->input->post('email');
			$customer['phone']					= $this->input->post('phone');
			$customer['email_subscribe']		= intval((bool)$this->input->post('email_subscribe'));
			if($this->input->post('password') != '')
			{
				$customer['password']			= $this->input->post('password');
			}
	///////////////////
//avatar
	if($this->input->post('imagename')){
	$customer['image']               = $this->input->post('imagename');
	$targ_w = $targ_h = 120;
	$jpeg_quality = 120;
	$src = 'uploads/profile/'.$this->input->post('imagename');
	//Get the new coordinates to crop the image.
	$x1 = $_POST["x"];
	$y1 = $_POST["y"];
	$w = $_POST["w"];
	$h = $_POST["h"];
	//Scale the image to the 100px by 100px
	@$scale = 120/$w;
	$cropped = $this->resizeThumbnailImage($src, $src,$w,$h,$x1,$y1,$scale); }
	//avatar
	//////////////////			
			$this->bse_tec->save_customer($this->customer);
			$this->Customer_model->save($customer);
			
			$this->session->set_flashdata('message', lang('message_account_updated'));
			
			redirect('user/update_account');
		}
	}
	function resizeThumbnailImage($thumb_image_name, $image, $width, $height, $start_width, $start_height, $scale){
	list($imagewidth, $imageheight, $imageType) = @getimagesize($image);
	$imageType = @image_type_to_mime_type($imageType);
	
	$newImageWidth = ceil($width * $scale);
	$newImageHeight = ceil($height * $scale);
	$newImage = @imagecreatetruecolor($newImageWidth,$newImageHeight);
	switch($imageType) {
		case "image/gif":
			$source=@imagecreatefromgif($image); 
			break;
	    case "image/pjpeg":
		case "image/jpeg":
		case "image/jpg":
			$source=@imagecreatefromjpeg($image); 
			break;
	    case "image/png":
		case "image/x-png":
			$source=@imagecreatefrompng($image); 
			break;
  	}
	@imagecopyresampled($newImage,$source,0,0,$start_width,$start_height,$newImageWidth,$newImageHeight,$width,$height);
	switch($imageType) {
		case "image/gif":
	  		@imagegif($newImage,$thumb_image_name); 
			break;
      	case "image/pjpeg":
		case "image/jpeg":
		case "image/jpg":
	  		@imagejpeg($newImage,$thumb_image_name,90); 
			break;
		case "image/png":
		case "image/x-png":
			@imagepng($newImage,$thumb_image_name);  
			break;
    }
	chmod($thumb_image_name, 0777);
	return $thumb_image_name;
}
//update account

	function my_downloads($code=false)
	{	//error_reporting(1);
		$this->load->library('breadcrumb');
		$this->breadcrumb->append_crumb('Home', base_url());
		$this->breadcrumb->append_crumb('My Downloads', base_url());
		$breadcrumb = $this->breadcrumb->output();
		$this->Customer_model->is_logged_in($this->uri->uri_string());
			
		$customer = $this->bse_tec->customer();
		$data['breadcrumb'] = $breadcrumb;
		if($code!=false)
		{
			$data['downloads'] = $this->Digital_Product_model->get_downloads_by_code($code,$customer['user_id']);
		} else {
			$this->Customer_model->is_logged_in();
			
			$customer = $this->bse_tec->customer();
			
			$data['downloads'] = $this->Digital_Product_model->get_user_downloads($customer['user_id']);
			}
		
		$data['gift_cards_enabled']	= $this->gift_cards_enabled;
		$data['page_title'] = lang('my_downloads');
		$data['body_content'] = "user/my_downloads";	

		$this->load->view('template', $data);
	}	
	
	function download($link)
	{	$customer = $this->bse_tec->customer();
		$filedata = $this->Digital_Product_model->get_file_info_by_link($link,$customer['user_id']);
		
		// missing file (bad link)
		if(!$filedata)
		{
			show_404();
		}
		// validate download counter
		if(intval($filedata->downloads) >= intval($filedata->max_downloads))
		{
			show_404();
		}
		// increment downloads counter
		$this->Digital_Product_model->touch_download($link);
		// Deliver file
		$this->load->helper('download');
		force_download('uploads/digital_uploads/', $filedata->filename);
	}
	
	function set_default_address()
	{
		$id = $this->input->post('id');
		$type = $this->input->post('type');
	
		$customer = $this->bse_tec->customer();
		$save['id'] = $customer['id'];
		
		if($type=='bill')
		{
			$save['default_billing_address'] = $id;

			$customer['bill_address'] = $this->Customer_model->get_address($id);
			$customer['default_billing_address'] = $id;
		} else {

			$save['default_shipping_address'] = $id;

			$customer['ship_address'] = $this->Customer_model->get_address($id);
			$customer['default_shipping_address'] = $id;
		} 
		
		//update customer db record
		$this->Customer_model->save($save);
		
		//update customer session info
		$this->bse_tec->save_customer($customer);
		
		echo "1";
	}
	
	// this is a form partial for the checkout page
	function checkout_address_manager()
	{
		$customer = $this->bse_tec->customer();
		$data['customer_addresses'] = $this->Customer_model->get_address_list($customer['id']);
		$this->load->view('address_manager', $data);
	}
	
	// this is a partial partial, to refresh the address manager
	function address_manager_list_contents()
	{
		$customer = $this->bse_tec->customer();
		$data['customer_addresses'] = $this->Customer_model->get_address_list($customer['id']);
		$this->load->view('address_manager_list_content', $data);
	}
	
	function address_form($id = 0)
	{
		
		$customer = $this->bse_tec->customer();
		//grab the address if it's available
		$data['id']			= false;
		$data['company']	= $customer['company'];
		$data['firstname'] = $customer['user_firstname'];
		$data['lastname']	= $customer['user_lastname'];
		$data['email']		= $customer['user_email'];
		$data['phone']		= $customer['phone'];
		$data['address1']	= '';
		$data['address2']	= '';
		$data['city']		= '';
		$data['country_id'] = '';
		$data['zone_id']	= '';
		$data['zip']		= '';
		
		if($id != 0)
		{
			$a	= $this->Customer_model->get_address($id);
			if($a['user_id'] == $this->customer['id'])
			{
				//notice that this is replacing all of the data array
				//if anything beyond this form data needs to be added to
				//the data array, do so after this portion of code
				$data		= $a['field_data'];
				$data['id']	= $id;
			} else {
				redirect('/'); // don't allow cross-customer editing
			}
			
			$data['zones_menu']	= $this->location_model->get_zones_menu($data['country_id']);
		}
		//get the countries list for the dropdown
		$data['countries_menu']	= $this->location_model->get_countries_menu();
		
		if($id==0)
		{
			//if there is no set ID, the get the zones of the first country in the countries menu
			$data['zones_menu']	= $this->location_model->get_zones_menu(array_shift(array_keys($data['countries_menu'])));
		} else {
			$data['zones_menu']	= $this->location_model->get_zones_menu($data['country_id']);
		}
		$this->load->library('form_validation');	
		$this->form_validation->set_rules('company', 'Company', 'trim|max_length[128]');
		$this->form_validation->set_rules('firstname', 'First Name', 'trim|required|max_length[32]');
		$this->form_validation->set_rules('lastname', 'Last Name', 'trim|required|max_length[32]');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[128]');
		$this->form_validation->set_rules('phone', 'Phone', 'trim|required|max_length[32]');
		$this->form_validation->set_rules('address1', 'Address', 'trim|required|max_length[128]');
		$this->form_validation->set_rules('address2', 'Address', 'trim|max_length[128]');
		$this->form_validation->set_rules('city', 'City', 'trim|required|max_length[32]');
		$this->form_validation->set_rules('country_id', 'Country', 'trim|required|numeric');
		$this->form_validation->set_rules('zone_id', 'State', 'trim|required|numeric');
		$this->form_validation->set_rules('zip', 'Zip', 'trim|required|max_length[32]');
		
		if ($this->form_validation->run() == FALSE)
		{
			if(validation_errors() != '')
			{
				echo validation_errors();
			}
			else
			{
		$data['body_content']			= 'address_form';
		
		$this->load->view('template', $data);
			
			}
		}
		else
		{
			$a = array();
			$a['id']						= ($id==0) ? '' : $id;
			$a['user_id']				= $this->customer['user_id'];
			$a['field_data']['company']		= $this->input->post('company');
			$a['field_data']['firstname']	= $this->input->post('firstname');
			$a['field_data']['lastname']	= $this->input->post('lastname');
			$a['field_data']['email']		= $this->input->post('email');
			$a['field_data']['phone']		= $this->input->post('phone');
			$a['field_data']['address1']	= $this->input->post('address1');
			$a['field_data']['address2']	= $this->input->post('address2');
			$a['field_data']['city']		= $this->input->post('city');
			$a['field_data']['zip']			= $this->input->post('zip');
			
			// get zone / country data using the zone id submitted as state
			$country = $this->location_model->get_country(set_value('country_id'));	
			$zone    = $this->location_model->get_zone(set_value('zone_id'));		
			if(!empty($country))
			{
				$a['field_data']['zone']		= $zone->code;  // save the state for output formatted addresses
				$a['field_data']['country']		= $country->name; // some shipping libraries require country name
				$a['field_data']['country_code']   = $country->iso_code_2; // some shipping libraries require the code 
				$a['field_data']['country_id']  = $this->input->post('country_id');
				$a['field_data']['zone_id']		= $this->input->post('zone_id');  
			}
			
			$this->Customer_model->save_address($a);
			$this->session->set_flashdata('message', lang('message_address_saved'));
			echo 1;
		}
	}
	
	function delete_address()
	{
		$id = $this->input->post('id');
		// use the customer id with the addr id to prevent a random number from being sent in and deleting an address
		$customer = $this->bse_tec->customer();
		$this->Customer_model->delete_address($id, $customer['user_id']);
		echo $id;
	}
	
	// ajax avartar upload
    //profile image upload
	function profile_image_upload() {
			$path = "uploads/profile/";			
			$valid_formats = array("jpg", "png", "gif", "bmp","jpeg");
			if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST") 	{
			$name = $_FILES['photoimg']['name'];
			$size = $_FILES['photoimg']['size'];
			if(strlen($name)) {
			@list($txt, $ext) = explode(".", $name);
			if(in_array($ext,$valid_formats)) {
			if($size<(1024*1024)) { // Image size max 1 MB
			$user_id = $this->customer['user_id'];
			$actual_image_name = time().$user_id.".".$ext;
			$tmp = $_FILES['photoimg']['tmp_name'];
			if(move_uploaded_file($tmp, $path.$actual_image_name)) 	{
			list($width, $height) = getimagesize($path.$actual_image_name);
			if($width > 450){
			$image_resize = $this->resizeImg($path.$actual_image_name);
			}
			echo '<img src="'.base_url('uploads/profile/'.$actual_image_name).'" class="imagename" id="target" class="preview">';
			echo '<input type="hidden" class="imagename" name="imagename" value="'.$actual_image_name.'"/>';
			}
			else
			echo "failed";
			}
			else
			echo "Image file size max 1 MB";
			}
			else
			echo "Invalid file format..";
			}
			else
			echo "Please select image..!";
			exit;
		}	
	} 
    //profile image upload
    // resizeimg
	function resizeImg($path){
	$this->load->library('image_lib');
    $config['image_library'] = 'gd2';
    $config['source_image'] = $path;
    $config['new_image'] = $path;
    $config['maintain_ratio'] = FALSE;
    $config['x_axis'] = '10';
    $config['y_axis'] = '10';
    $config['width']     = '450';
    $config['height']     = '300';
    $this->image_lib->initialize($config);
	 $this->image_lib->resize();
		}
	// ajax upload 
    
	function my_uploads()
	{
				$this->Customer_model->is_logged_in('user/my_uploads/');				
				$data['page_title'] = lang('dgtl_pr_header');
				$userid = $this->customer['user_id'];
				$data['file_list']	= $this->digital_product_model->get_list_user($userid);
			  	$data['body_content']			= 'item/digital_products';
		   	$this->load->view('template', $data);		
	}
		
	function my_upload_form($id=false) 
	{
			
		$this->load->helper('form_helper');
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		
		$data	= array(	 'id'				=>''
							,'filename'			=>''
							,'max_downloads'	=>''
							,'title'			=>''
							,'size'				=>''
							);
		if($id)
		{
			$data	= array_merge($data, (array)$this->digital_product_model->get_file_info($id));
		}
		$data['page_title']		= lang('digital_products_form');
		
		$this->form_validation->set_rules('max_downloads', 'lang:max_downloads', 'numeric');
		$this->form_validation->set_rules('title', 'lang:title', 'trim|required');

		
		if ($this->form_validation->run() == FALSE)
		{
				$data['error'] = validation_errors();
				$data['body_content']			= 'item/digital_product_form';
		  	  $this->load->view('template', $data);		
		
			} else {
			if(!$id)
			{
				$data['file_name'] = false;
				$data['error']	= false;
				
				$config['allowed_types'] = 'zip';
				$config['upload_path'] = 'uploads/digital_uploads';//$this->config->item('digital_products_path');
				$config['remove_spaces'] = true;
		
				$this->load->library('upload', $config);
				
				if($this->upload->do_upload())
				{
					$upload_data	= $this->upload->data();
				} else 
				{
					$data['error']	= $this->upload->display_errors();
					//$this->load->view($this->config->item('admin_folder').'/digital_product_form', $data);
					$data['body_content']			= 'item/digital_product_form';
		
		  	  		$this->load->view('template', $data);
					return;
				}
				
				$save['filename']	= $upload_data['file_name'];
				$save['size']		= $upload_data['file_size'];
			} else {
				$save['id']			= $id;
			}
			$save['user_id'] = $this->customer['user_id'];
			$save['max_downloads']	= set_value('max_downloads');				
			$save['title']			= set_value('title');
			
			$this->digital_product_model->save($save);
			
			redirect('user/my_uploads');
		}
	}		
		
		//delete uploaded files by user		
		
	function upload_delete($id)
	{
		$this->digital_product_model->delete($id);
		
		$this->session->set_flashdata('message', lang('message_deleted_file'));
		redirect('user/my_uploads');
	}	
			
		//download user sales report as csv format
	function user_sales_report($id)
	{
		$offset = 0;		
		$subscribers	= $this->Customer_model->get_customer_sales($id,$offset);	  		
		$sales_lists = '';
		foreach($subscribers as $sales)
		{
			$sales_lists .= $sales->order_number.",".$sales->firstname.$subscriber->lastname.",".$sales->name.",".$sales->total.",".$sales->ordered_on.",\n";
		}
	
		$data['salelists']	= $sales_lists; 	
		$this->load->view('pre_load/user_sales_report', $data);
	}					
	function check_withdrawal()
	{
	 $amount = $_POST['amount'];
	 $id = $this->customer['user_id'];
	 
	 $this->db->select('setting');
	 $this->db->where('code', 'withdrawal');
	 $this->db->where('setting_key', 'withdrawal_count_limit');
	 $max	= $this->db->get('settings')->row();
	
 	 $this->db->select('setting');
	 $this->db->where('code', 'withdrawal');
	 $this->db->where('setting_key', 'withdrawal_amount_limit');
	 $maxi	= $this->db->get('settings')->row();
	
	 if($amount <= $max->setting){
	 $hai = $this->Customer_model->get_check_withdrwal($amount,$id);
	 if($hai->trans_amount <= $maxi->setting)
	 echo 'success';
	 else 
	 echo 'Sorry!! you have reached today transaction limit';
	 
	 }
	 else
	 {
 	 echo 'Sorry!! We allowed $'.$max->setting.' per withdrawal';
 	
 	 }
	}				


	function get_transaction_list($id='')
	{
		$subscribers = $this->Customer_model->get_transaction_details($id);
		
		$sub_list = '';
		foreach($subscribers as $subscriber)
		{
			$sub_list .= $subscriber['balance'].",".$subscriber['text'].",".$subscriber['last_updated'].",\n";
		}
		
		$data['sub_list']	= $sub_list;
		$this->load->view('pre_load/user_transaction_list', $data);
	}	
	function top_authors(){
		$this->load->library('breadcrumb');
	$this->breadcrumb->append_crumb('Home', base_url());
	$this->breadcrumb->append_crumb('Top authors', base_url());
	$breadcrumb = $this->breadcrumb->output();
	$data['breadcrumb'] = $breadcrumb;
		$data['page_title']	= 'Top authors';
		$data['new_authors']			= $this->Customer_model->new_authors();

		$data['best_customers']       = $this->Customer_model->get_customers(5,$offset=0, $order_by='recommend',$direction='DESC');
		$data['customers']	      = $this->Customer_model->get_customers(20,$offset=0, $order_by='recommend',$direction='DESC');
		$data['body_content'] = 'user/top_author';
		
		$this->load->view('template', $data);
		}
		
	function profile($id, $page=false) {
		$this->load->library('breadcrumb');
	$this->breadcrumb->append_crumb('Home', base_url());
	$this->breadcrumb->append_crumb('Portfolio', base_url());
	$breadcrumb = $this->breadcrumb->output();
		$data['customer']	= $this->Customer_model->get_customer($id);
		$customer_id = $data['customer']->user_id;
		
		/*Version 1.3*/
		if(!$data['customer'])
		{
			$data['body_content']			= '404_error_page';
		   $this->load->view('template', $data);

		}
	  else {
	  	$urls = parse_url($_SERVER['REQUEST_URI']);
		@$get_val = explode('=',$urls['query']);
		//print_r($get_val);
		if(@$urls['query']=='')
		{
		$sort_by	= array('by'=>'sequence', 'sort'=>'ASC');

		}	
		else
		{
			@$sort_bys	= explode('/',$get_val[1]);
			@$order_by = array('by'=>$sort_bys[0], 'sort'=>$sort_bys[1]);
			
			
		}
		/*End Version 1.3*/
		$data['breadcrumb'] = $breadcrumb;
		$data['page_title']	= 'Author Profile';
		$this->load->library('pagination');

		$config['base_url']		= base_url().'/profile/'.$id;
		$config['total_rows']		= count($this->Product_model->profile_user_products(array('user_id'=>$customer_id)));
		$config['per_page']		= 10;
		$config['uri_segment']		= 3;
		$config['first_link'] = 'First';
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		$config['last_link'] = 'Last';
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';

		$config['full_tag_open'] = '<div class="pagination"><ul>';
		$config['full_tag_close'] = '</ul></div>';
		$config['cur_tag_open'] = '<li class="active"><a href="#">';
		$config['cur_tag_close'] = '</a></li>';

		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';

		$config['prev_link'] = '&laquo;';
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';

		$config['next_link'] = '&raquo;';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		
		$this->pagination->initialize($config);

		$data['products']	= $this->Product_model->user_products(array('user_id'=>$customer_id,'page'=>$page,'rows'=>10,'by'=>$order_by['by'],'sort'=>$order_by['sort']));
		$data['grid_products']	= $this->Product_model->user_products(array('user_id'=>$customer_id,'page'=>$page,'rows'=>12, 'by'=>$order_by['by'],'sort'=>$order_by['sort'])); 

		$data['following']	= $this->Customer_model->get_following_list($id);
		$data['followers']	= $this->Customer_model->get_follower_list($id);

		$data['user_sales']	= $this->Customer_model->get_customer_sales($this->customer['user_id']);
		$data['orders']		= $this->order_model->count_customer_orders($id);
		$data['body_content'] = 'user/user_profile';
		$this->load->view('template', $data);	
		}
		}
	
	function wishlist() {
	$this->load->library('breadcrumb');
$this->breadcrumb->append_crumb('Home', base_url());
$this->breadcrumb->append_crumb('Wishlist', base_url());
$breadcrumb = $this->breadcrumb->output();
	$data['breadcrumb'] = $breadcrumb;
	$this->Customer_model->is_logged_in();
		$data['page_title']	= 'Wish List';
		$id = $this->customer['user_id'];
		
		$data['products']			= $this->Customer_model->get_wishlist($id);
		$data['body_content'] = 'user/user_wishlist';
		$this->load->view('template', $data);	
		}
		
	function user_mail() {
		 $from = $this->input->post('mailid');
		 $to = $this->input->post('to');
		 $id = $this->input->post('id');
		 $message = $this->input->post('message');
		 	
		 	$this->load->library('email');
			$config['mailtype'] = 'html';
			
			$this->email->initialize($config);
			$this->email->from($from, $this->config->item('company_name'));
			$this->email->to($to);
			$this->email->bcc($this->config->item('email'));
			$this->email->subject('Message sent via - '.$this->config->item('company_name'));
			$this->email->message($message);
			$this->email->send();
			
			$this->session->set_flashdata('message', 'Your message has been send');
			redirect('profile/'.$id);	
		}
	
	function user_follow(){
	$save['followers_id'] = $this->input->post('follower_id');
	$save['user_id'] = $this->input->post('user_id');
	$btn = $this->input->post('btn');
   $this->Customer_model->follow_list($save,$btn);
   $result = $this->Customer_model->check_follow($save['user_id'],$save['followers_id']);
   echo count($result);		
	}

	//Rating
	function rating()
    {   
    	$aResponse['error'] = false;
		$aResponse['message'] = '';
		
		// ONLY FOR THE DEMO, YOU CAN REMOVE THIS VAR
			$aResponse['server'] = ''; 
		// END ONLY FOR DEMO

		if(isset($_POST['action']))
		{
			if(htmlentities($_POST['action'], ENT_QUOTES, 'UTF-8') == 'rating')
			{
			/*
			* vars
			*/
			$id = intval($_POST['idBox']);
			$p_id = $_POST['p_id'];
			$package_id = $_POST['package_id'];
			$rate = floatval($_POST['rate']);
			$rating_status = floatval($_POST['rating_status']);  //rating_Status in download_package_files(rate by user for their downloads)
	
			$success = true;
		
			if($success)
				{
					$result = $this->Product_model->get_rating($p_id);
					$prod_id = $result;
					foreach($prod_id as $prod_id){
						$f_id = $prod_id['id'];
						}
				$this->Product_model->save_rating($f_id,$rate,$package_id,$rating_status);
				// ONLY FOR THE DEMO, YOU CAN REMOVE THE CODE UNDER
					$aResponse['server'] = 'Your rate has been recorded. Thanks for your rate :)'.$f_id;
				// END ONLY FOR DEMO
				echo json_encode($aResponse);
				}
			}
		}		 
     
   }
	//Rating


	function user_recommend(){
	$save['recommend_user_id'] = $this->input->post('follower_id');
	$save['user_id'] = $this->input->post('user_id');
	$btn = $this->input->post('btn');
   $this->Customer_model->recommend_list($save,$btn);
   $result = $this->Customer_model->check_recommend($save['user_id'],$save['recommend_user_id']);
   echo count($result);		
	}
	
	function all_authors(){
		$data['page_title']	= 'Authors';
		$data['customers']			= $this->Customer_model->new_authors();
		$data['body_content'] = 'user/all_author';
		$this->load->view('template', $data);	
		}

	function remove_wishlist($p_id,$u_id){
		$this->db->delete('user_wishlist', array('prod_id' => $p_id,'user_id'=>$u_id));
		$this->session->set_flashdata('message', 'Deleted successfully');
		redirect('wishlist'); 		
		}
    //forgot password
	function forgot_password_reset(){
	
	$redirect	= $this->Customer_model->is_logged_in(false, false);
	//if they are logged in, we send them back to the my_account by default
	if ($redirect)
	{
		redirect('dashboard');
	}
	$data['page_title']	= lang('forgot_password'); 
	$submitted = $this->input->post('submitted');	
	if ($submitted){
	$password = $this->input->post('new_password');
	$confirm = $this->input->post('confirm_password');
	$reset = $this->input->post('reset_password');
	$this->load->library('form_validation');
	$this->form_validation->set_rules('new_password', 'New Password', 'trim|required|max_length[32]');	
	$this->form_validation->set_rules('confirm_password', 'Confirm Password', 'trim|required|max_length[32]|matches[new_password]'); 
	$this->form_validation->set_rules('reset_password', 'Reset Password', 'trim|required|max_length[32]');	
	//Validation Checking
	if($this->form_validation->run() == FALSE){ 
	$data['error']				= validation_errors();
	$data['body_content'] = 'user/forgot_password_reset';
	$this->load->view('template', $data);
	} else {
	$conf_reset = $this->Customer_model->forget_password_reset($password,$reset);
	if ($conf_reset==1) {					
	$this->session->set_flashdata('message', 'Your password has been reseted.');
	redirect('login');
	} else {
	$this->session->set_flashdata('error', lang('error_no_account_record'));
	redirect('user/forgot_password');
	}
	}
	}
		$data['body_content'] = 'user/forgot_password_reset';
		$this->load->view('template', $data);	
	}
//Marketplus v1.3
function get_deposit_list($id='')
	{
		$lists = $this->Customer_model->get_deposit_details($id);
		$deposit_lists = '';
		foreach($lists as $deposit_list)
		{
			if($deposit_list['trans_text']!=''){
				$status = explode($deposit_list['trans_text'], ',');
			}
			$deposit_lists .= $deposit_list['trans_method'].",".$deposit_list['trans_amount'].",".$status[0].'-'.$status[1].",".$deposit_list['currency_code'].",".$deposit_list['trans_date'].",\n";
		}
		
		$data['deposit_lists']	= $deposit_lists;
		$this->load->view('pre_load/user_deposit_list', $data);
	}	
	
	function get_withdrawal_list($id='')
	{
		$lists = $this->Customer_model->get_withdrawal_details($id);
		$withdrawal_lists = '';
		foreach($lists as $withdrawal_list)
		{
			if($withdrawal_list['trans_text']!=''){
				$status = explode(',',$withdrawal_list['trans_text']);
			}
			$withdrawal_lists .= $withdrawal_list['trans_method'].",".$withdrawal_list['trans_amount'].",".$status[0].'-'.$status[1].",".$withdrawal_list['currency_code'].",".$withdrawal_list['trans_date'].",\n";
		}
		
		$data['withdrawal_lists']	= $withdrawal_lists;
		$this->load->view('pre_load/user_withdrawal_list', $data);
	}	
	function get_purchase_list($id='')
	{
		$lists = $this->Digital_Product_model->get_user_downloads($id);
		
		$purchase_lists = '';
		foreach($lists as $key=>$val) :
	   foreach($val as $purchase_list)
		{
			$product = $this->Product_model->get_product($purchase_list->product_id);
			$price = $this->Digital_Product_model->get_order_id($key)->total;
			$order = $this->Digital_Product_model->get_order_id($key)->ordered_on;
			$purchase_lists .= $key.",".$product->name.",".$price.",".$order.",\n";
		}
	   endforeach;
		$data['purchase_lists']	= $purchase_lists; 
		$this->load->view('pre_load/user_purchase_list', $data);
	}	
}