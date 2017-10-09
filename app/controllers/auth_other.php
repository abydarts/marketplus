<?php

class auth_other extends Front_Controller  
{    
    function __construct()
	{
		parent::__construct(); 
		$this->load->model(array('Category_model','user_model','Customer_model'));
		$this->load->model('tank_auth/users');
		$this->load->helper('directory');	
		$this->load->library('facebook');
		$this->load->library('tank_auth');
		$this->load->library('Lightopenid');
		// for google open id
        parse_str($_SERVER['QUERY_STRING'],$_GET);		
	}
	
	// handle when users log in using facebook account
	function fb_signin()
	{
		// load facebook library
		$fb_user = $this->facebook->get_any_user($_GET['uid']); 	
		if( isset($fb_user))
		{
			$this->session->set_userdata('facebook_id', $fb_user['id']);			
			$user = $this->user_model->get_user_by_sm(array('facebook' => $fb_user['id']), 'facebook_id'); 
			$this->session->set_userdata('namePerson/first',$fb_user['first_name']);
			$this->session->set_userdata('namePerson/last',$fb_user['last_name']);
			$this->session->set_userdata('username',$fb_user['username']);	
			$this->session->set_userdata('email',$_GET['email']);		
			if( sizeof($user) == 0) 
			{ 
				redirect('auth_other/fill_user_info', 'refresh'); 
			}
			else
			{
				// simulate what happens in the tank auth
				$this->session->set_userdata(array(	'user_id' => $user[0]->id, 'username' => $user[0]->username,
													'status' => ($user[0]->activated == 1) ? STATUS_ACTIVATED : STATUS_NOT_ACTIVATED));
				//$this->tank_auth->clear_login_attempts($user[0]->email); can't run this when doing FB
				$this->users->update_login_info( $user[0]->id, $this->config->item('login_record_ip', 'tank_auth'), 
												 $this->config->item('login_record_time', 'tank_auth'));
				redirect('login', 'refresh');
			}
		}
		else 
		{ 
			echo 'cannot find the Facebook user'; 
		}
	}
	
	// function for logging in via google open id
    function google_openid_signin()
    {
     
    	$required_attr = array('namePerson/friendly', 'contact/email', 
    						   'namePerson/first', 'namePerson/last', 
    						   'contact/country/home', 'contact/email', 'pref/language');
    	try 
    	{
			if(!isset($_GET['openid_mode'])) 
    		{
    			$lightopenid = new Lightopenid;
    			$lightopenid->identity = 'https://www.google.com/accounts/o8/id';
    			$lightopenid->required = $required_attr; 
    			redirect($lightopenid->authUrl(), 'refresh');
    		}
    		elseif($_GET['openid_mode'] == 'cancel')
    		{
    			echo 'User has cancelled authentication!';
    		}
    		else 
    		{
    			$lightopenid = new Lightopenid;
    			$lightopenid->required = $required_attr;
    			if($lightopenid->validate())
    			{
    				$google_open_id = $lightopenid->identity;
					$this->session->set_userdata('google_open_id', $google_open_id);	
					    				
    				// does this user exist?
					$user = $this->user_model->get_user_by_sm(array('google' => $google_open_id), 'google');   
					if( sizeof($user) == 0 ) 
					{    
						//users session call back values    				
	    				$this->session->set_userdata($lightopenid->getAttributes());	
	    				$google_user=$lightopenid->getAttributes(); 	    				
						$this->session->set_userdata('namePerson/first',$google_user['namePerson/first']);
						$this->session->set_userdata('namePerson/last',$google_user['namePerson/last']); 
						$this->session->set_userdata('email',$google_user['contact/email']);	 							
						redirect('auth_other/fill_user_info'); 
					}
					else
					{ 
						// simulate what happens in the tank auth						 
						$exist_user=$this->Customer_model->get_customer_by_email($this->session->userdata('email'));
						$this->Customer_model->auto_login($exist_user['email'] ,$exist_user['password']);//lets automatically log them in  
						redirect('dashboard', 'refresh');
					}
    			}
    			else 
    			{
    				echo 'User has not logged in.';
    			}
    		}
    	}
    	catch(ErrorException $e) 
    	{
    		echo $e->getMessage();
    	}
    }    

	// called when user logs in via facebook/twitter for the first time
	function fill_user_info()
	{
		// load validation library and rules 
		$this->load->config('tank_auth', TRUE);
		$this->load->library('form_validation');
		$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean|min_length['.$this->config->item('username_min_length', 'tank_auth').']|callback_username_check');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email|callback_email_check');
		 
			$user_mail=$this->session->userdata('email'); 
			//existing user checking
			$exist_user=$this->Customer_model->get_customer_by_email($user_mail);	
			$user_count=count($exist_user); 
			if($user_count=='0'){ 				
				$email      =   $this->session->userdata('email');
				$firstname      =  $this->session->userdata('namePerson/first');
				$lastname      = $this->session->userdata('namePerson/last'); 	
				$username   =   $firstname.$lastname;
				$password = $this->generate_password(9, 8);
				
				$data = $this->tank_auth->create_user_open($id=false,$username, $email, $password,$firstname,$lastname);
					if($data){
					$this->session->set_flashdata('message', sprintf( lang('registration_thanks_open'), $this->input->post('firstname') ) ); // Flash Message 
					} 
			$login	= $this->Customer_model->auto_login($email ,sha1($password));//lets automatically log them in  
			redirect('dashboard', 'refresh');
				}
				else {  
				$login		= $this->Customer_model->auto_login($exist_user['user_email'] ,$exist_user['password']);//lets automatically log them in 					 
				redirect('dashboard', 'refresh');
					}	 
	}

	// generates a random password for the user
	function generate_password($length=9, $strength=0) 
	{
		$vowels = 'aeuy';
		$consonants = 'bdghjmnpqrstvz';
		if ($strength & 1) { $consonants .= 'BDGHJLMNPQRSTVWXZ'; }
		if ($strength & 2) { $vowels .= "AEUY"; }
		if ($strength & 4) { $consonants .= '23456789'; }
		if ($strength & 8) { $consonants .= '@#$%'; }
	 
		$password = '';
		$alt = time() % 2;
		for ($i = 0; $i < $length; $i++) 
		{
			if ($alt == 1) 
			{
				$password .= $consonants[(rand() % strlen($consonants))];
				$alt = 0;
			} 
			else 
			{
				$password .= $vowels[(rand() % strlen($vowels))];
				$alt = 1;
			}
		}
		return $password;
	}
}