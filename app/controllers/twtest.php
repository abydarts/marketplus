<?php
 class Twtest extends Front_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model(array('user_model','Customer_model')); 	
		$this->customer = $this->bse_tec->customer();	 
		$this->lang->load('common');
		$this->load->helper('date');
		$this->load->helper('url');
		$this->load->model(array('location_model'));
		$this->load->helper('directory');
		$this->load->config('tank_auth', TRUE); 
		$this->load->library('twconnect');	
	} 
	/* show link to connect to Twiiter */
	public function index() {
		$this->load->library('twconnect');  
	}
	/* redirect to Twitter for authentication */
	public function redirect() {
		$this->load->library('twconnect');
		$ok = $this->twconnect->twredirect('twtest/callback');
		if (!$ok) {
			echo 'Could not connect to Twitter. Refresh the page or try again later.';
			redirect('twtest/clearsession');
		}  
	}

	public function callback() {
		$ok = $this->twconnect->twprocess_callback();
	
			if($ok)
			redirect('twtest/success');
			else 
			redirect ('twtest/failure');
	}

	public function success() {
		
		echo 'Twitter connect succeded<br/>';
		echo '<p><a href="' . site_url() . 'twtest/clearsession">Do it again!</a></p>';
		$this->load->library('twconnect'); 
		$this->twconnect->twaccount_verify_credentials(); 
		$tw_user=$this->twconnect->tw_user_info;
		$twitter_id=$this->Customer_model->check_customer_twitterid($this->twconnect->tw_user_id);  
		if(count($twitter_id) == 1){
		$login = $this->Customer_model->twitter_login($twitter_id->user_email,$twitter_id->password);  
		if($login){
		redirect('dashboard');
		}
		else{
		redirect('login');}
		}
		$this->session->set_userdata('username',$this->twconnect->tw_user_name);
		$this->session->set_userdata('twitter_id',$this->twconnect->tw_user_id);		 
		redirect('twtest/fill_user_info');  
	}


	/* authentication un-successful */
	public function failure() {

		echo '<p>Twitter connect failed</p>';
		echo '<p><a href="' . base_url() . 'twtest/clearsession">Try again!</a></p>';
	}

	/* clear session */
	public function clearsession() {

		$this->session->sess_destroy();
		redirect('login');
	}
	
	function fill_user_info()
	{    	
	$username= $this->session->userdata('username');  
	$exist_mail=$this->Customer_model->get_customer_by_name($username);	 
	$user_count=count($exist_mail);   
	if($user_count=='0'){ 		
		 redirect('twtest/fill_content');
				}
				else {
				$exist=$this->Customer_model->get_customer_by_temail($exist_mail,$username);

				$count=count($exist);  
				echo $count;
				exit();
				if($count>0){ 		
				$login		= $this->Customer_model->auto_login($exist['email'] ,$exist['password']);//lets automatically log them in 					 
				redirect('dashboard', 'refresh');
					}	 
				} 
	}
	function fill_content(){
		$data['base_url']			= $this->uri->segment_array();	//breadcrumb
		$data['body_content']			= 'user/fill_user_info';
		$this->load->view('template', $data);
		}
	
	function confirm(){ 		 
				$email      =   $this->input->post('email');
				$firstname      = $this->input->post('firstname');
				$lastname      = $this->input->post('lastname'); 	
				$username   =   $this->input->post('username');
				$twitter_id   =   $this->input->post('twitter_id');
				$password = $this->generate_password(9,8); 
				$this->load->library('tank_auth');
				$exist=$this->Customer_model->get_customer_by_email($email); 
				$count=count($exist);  
				if($count==0){
				$data = $this->tank_auth->create_user_open($id=false,$username, $email, $password,$firstname,$lastname,$twitter_id);
				if($data){
				$this->session->set_flashdata('message', sprintf( lang('registration_thanks_open'), $this->input->post('firstname') ) ); // Flash Message 
				} 
				$login	= $this->Customer_model->auto_login($email ,sha1($password));//lets automatically log them in  
				redirect('dashboard', 'refresh');		 
				} else {
$this->session->set_flashdata('message', sprintf( 'This Email is already exist. Kindly login again') ); // Flash Message 
redirect('login');
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