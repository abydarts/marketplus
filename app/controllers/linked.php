<?php

class Linked extends Front_Controller
{

    function __construct()
    {
//error_reporting(0);
        parent::__construct();

        $this->config->load('linkedin');

        $this->data['consumer_key'] = $this->config->item('api_key');
        $this->data['consumer_secret'] = $this->config->item('secret_key');
        $this->data['callback_url'] = site_url() . 'linked/linkedin_submit';
		$this->load->config('tank_auth', TRUE);
		$this->load->library('tank_auth');
		$this->load->library('form_validation');
    }

    function index()
    {
        
    }

    function linkedin()
    {
        $this->load->library('linkedin', $this->data);

        $token = $this->linkedin->get_request_token();

        $oauth_data = array(
            'oauth_request_token' => $token['oauth_token'],
            'oauth_request_token_secret' => $token['oauth_token_secret']
        );
		
        $this->session->set_userdata($oauth_data);

        $request_link = $this->linkedin->get_authorize_URL($token);
		   header("Location: " . $request_link);
    }

    /**
     * Get Access tokens
     */
    function linkedin_submit()
    {
        $this->data['oauth_token'] = $this->session->userdata('oauth_request_token');

        $this->data['oauth_token_secret'] = $this->session->userdata('oauth_request_token_secret');

        $this->load->library('linkedin', $this->data);

        $this->session->set_userdata('oauth_verifier', $this->input->get('oauth_verifier'));

        $tokens = $this->linkedin->get_access_token($this->input->get('oauth_verifier'));

        $access_data = array(
            'oauth_access_token' => $tokens['oauth_token'],
            'oauth_access_token_secret' => $tokens['oauth_token_secret']
        );

        $this->session->set_userdata($access_data);

        /*
         * Store Linkedin info in a session
         */
        $auth_data = array('linked_in' => serialize($this->linkedin->token), 'oauth_secret' => $this->input->get('oauth_verifier'));

        $this->session->set_userdata(array('auth' => $auth_data));
        redirect('linked/postdata');
    }

    /**
     * Post a Status update to linkedin
     */
    function postdata()
    {
        $auth_data = $this->session->userdata('auth'); 		
        $title = "Trying out a Codeignier Linkedin Library";
        $comment = "Trying out a Codeignier Linkedin Library created by Murrion Software. Get the code on Github.com";
        $target_url = "https://github.com/MurrionSoftware/codeigniter-linkedin-library";
        $image_url = ""; // optional  
        $this->load->library('linkedin', $this->data);

        $status_response = $this->linkedin->getData('http://api.linkedin.com/v1/people/~:(id,first-name,last-name,headline,picture-url,location,industry,summary,specialties,positions,public-profile-url)?format=json', unserialize($auth_data['linked_in']));
       $username= $this->session->userdata('username');
				$iid= $this->session->userdata('lid');
				$twitter_id=$this->Customer_model->check_customer_linkedid($iid); 
				if(count($twitter_id)=='0'){ 		
				$data['body_content'] = 'fill_user_linked';
				$this->load->view('template',$data); 
				} else {
				$login	= $this->Customer_model->auto_login($twitter_id->user_email ,$twitter_id->password);
				redirect('dashboard', 'refresh');	
				}
				
   
			//redirect('linked/fill_user_info');   
}
function fill_user_info()
	{    	
	$username= $this->session->userdata('username');
	@$exist_mail=$this->Customer_model->get_customer_by_name($username);
	$user_count=count($exist_mail);   
	if($user_count=='0'){ 		
		 redirect('linked/fill_content');
				}
				else { 
				$exist=$this->Customer_model->get_customer_by_temail($exist_mail,$username);   
				$count=count($exist);  
				if($count==1){ 		
				$login		= $this->Customer_model->auto_login($exist->email,$exist->password);//lets automatically log them in \
				if(isset($login)){	 
				redirect('dashboard', 'refresh');
				} else {
					redirect('home', 'refresh');
					}
					}	 
					else {
						redirect('home', 'refresh');
						}
				} 
	}
	function fill_content(){
		//$data['category_menu'] = $this->Category_model->get_all_categories(0);	  //header menu 
		$data['base_url']			= $this->uri->segment_array();	//breadcrumb
		$data['body_content'] = 'fill_user_linked';
		$this->load->view('template',$data); 
		}
	
	function confirm(){ 	
				$iid= $this->session->userdata('lid'); 
				$email      =   $this->input->post('email');	
				$firstname      = $this->input->post('firstname');
				$lastname      = $this->input->post('lastname'); 	
				$username   =   $this->input->post('username');
				$linkedin_id = $this->input->post('linked_id');
				$password = $this->generate_password(9,8); 
				
				$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean|callback_check_username');
				$this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email|callback_check_email');
				if ($this->form_validation->run() == FALSE){
				$data['error']	= validation_errors();
				$data['body_content'] = 'fill_user_linked';
				$this->load->view('template',$data);    
				} else {
				$exist=$this->Customer_model->get_customer_by_email($email); 
				$count=count($exist);  
				if($count==0){
				$datas = $this->tank_auth->create_user_open_linkedin($username, $email, $password,$firstname,$lastname,$linkedin_id);
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

/* End of file user.php */
/* Location: ./application/controllers/user.php */