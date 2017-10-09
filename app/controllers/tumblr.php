<?php
 
 class Tumblr extends Front_Controller {

	function __construct(){
		//error_reporting(0);
		parent::__construct();
		$this->load->model(array('Customer_model')); 	
		$this->customer = $this->bse_tec->customer();	 
		$this->lang->load('common');
		$this->load->helper('date');
		$this->load->helper('url'); 
		$this->load->helper('directory'); 
		$this->load->library('tumblroauth');
		$this->load->config('tank_auth', TRUE);
		$this->load->library('tank_auth');
		$this->load->library('form_validation');		
	} 
	 

	/* show link to connect to Twiiter */
	public function index() {
		  
	}

	/* redirect to Twitter for authentication */
	public function connect() { 
	$CI =& get_instance(); 
$consumer_key = $CI->config->item('consumer_key');
$consumer_secret = $CI->config->item('consumer_secret');
$callback_url = site_url('tumblr/callback'); 
$tum_oauth = new TumblrOAuth($consumer_key, $consumer_secret); 
$request_token = $tum_oauth->getRequestToken($callback_url); 
$_SESSION['request_token'] = $token = $request_token['oauth_token'];
$_SESSION['request_token_secret'] = $request_token['oauth_token_secret']; 
$this->session->set_userdata('sess_request_token',$_SESSION['request_token']);	 
$this->session->set_userdata('sess_request_token_secret',$_SESSION['request_token_secret']);

switch ($tum_oauth->http_code) {
  case 200: 
    $url = $tum_oauth->getAuthorizeURL($token); 
    header('Location: ' . $url);  
    break;
default: 
    echo 'Could not connect to Tumblr. Refresh the page or try again later.';
}
exit(); 
	} 
	public function callback() {
		$CI =& get_instance();
		$consumer_key = $CI->config->item('consumer_key');
		$consumer_secret = $CI->config->item('consumer_secret');		
		$tum_oauth = new TumblrOAuth($consumer_key, $consumer_secret,$this->session->userdata('sess_request_token'),$this->session->userdata('sess_request_token_secret'));
		$access_token = $tum_oauth->getAccessToken($_GET['oauth_verifier']); 	
		if (200 == $tum_oauth->http_code) {
		  // good to go
		} else {
		  //die('Unable to authenticate');
         $this->session->set_flashdata('error','Unable to authenticate');
		  redirect('register');
		}		 
		$tum_oauth = new TumblrOAuth($consumer_key, $consumer_secret, $access_token['oauth_token'], $access_token['oauth_token_secret']);
		$userinfo = $tum_oauth->get('http://api.tumblr.com/v2/user/info'); 
		if (200 == $tum_oauth->http_code) {
		  // good to go
		} else {
		  //die('Unable to get info');
                  $this->session->set_flashdata('error','Unable to get info');
		  redirect('register');
		}		 
		// find primary blog.  Display its name. 
		$username= $userinfo->response->user->name;
		$this->session->set_userdata('screenname',$username);
		$exist_mail=$this->Customer_model->get_customer_by_username($username."tumblr"); 
				$user_count=count($exist_mail);  
				if($user_count=='0'){ 		
				 redirect('tumblr/fill_content');
				} else {
				$login	= $this->Customer_model->auto_login($exist_mail->user_email ,$exist_mail->password);

				redirect('dashboard', 'refresh');	
				}
		}    
	
	function fill_content(){
		$data['base_url']			= $this->uri->segment_array();	//breadcrumb
		$data['body_content'] = 'fill_user_tumblr';
		$this->load->view('template',$data);  
		}
	
	function confirm(){ 		 
				$email      =   $this->input->post('email');
				$firstname      = $this->input->post('firstname');
				$lastname      = $this->input->post('lastname'); 	
				$username   =   $this->input->post('username');
				$password = $this->generate_password(9,8); 
	
				$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean|callback_check_username');
				$this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email|callback_check_email');
				if ($this->form_validation->run() == FALSE){
				$data['error']	= validation_errors();
				$data['body_content'] = 'fill_user_tumblr';
				$this->load->view('template',$data);    
				} else {
				$exist=$this->Customer_model->get_customer_by_email($email); 
				$count=count($exist);  
				if($count==0){
				$datas = $this->tank_auth->create_user_open($id='',$username, $email, $password,$firstname,$lastname);
				if($datas){
				$this->session->set_flashdata('message', sprintf( lang('registration_thanks_open'), $this->input->post('firstname') ) ); // Flash Message 
				}
				$login	= $this->Customer_model->auto_login($email ,sha1($password));
				redirect('dashboard', 'refresh');		 
				} else {
					$login	= $this->Customer_model->auto_login($email ,sha1($password));
					//redirect('dashboard', 'refresh');				
					$this->session->set_flashdata('error',lang('error_email')); 		
					redirect('login', 'refresh');				
					}
				} //validation
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
		
        if ($email =='no_user')
       	{
			return TRUE;
		}
		else if ($email == 'exist')
		{
			$this->form_validation->set_message('check_email', lang('error_email'));
			return FALSE;
		}
		else if ($email == 'no_active')
		{
			$this->form_validation->set_message('check_email', 'You have registered already. We send Confirmation Email to you. please check your mail.<a href="'.base_url().'users/send_reconfirmation/'.'">Resend Confirmation</a>');
			//$this->session->set_flashdata('message', '');
			return FALSE;
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
		return $password;	}
}