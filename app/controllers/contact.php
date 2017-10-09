<?php

class Contact extends Front_Controller {

	function __construct(){
		parent::__construct();
		$this->customer = $this->bse_tec->customer();			 
		$this->lang->load('common'); 
		$this->load->helper('date');
	} 
	//contacts
	function index(){ 
	$data['base_url']			= $this->uri->segment_array();	//breadcrumb
	$data['page_title']	= lang('form_support');
	$data['id'] = $this->customer['user_id'];
	$data['customer'] = $this->customer['user_firstname'].' '.$this->customer['user_lastname'];
	$data['email'] = $this->customer['user_email'];			
	$this->load->library('form_validation');  
	if($this->input->post('contact')){
	$cid			= $this->input->post('id');
	$cname			= $this->input->post('cname');	
	$cmail			= $this->input->post('cmail');
	$subject		= $this->input->post('subject');
	$message		= $this->input->post('message');  
	if(($subject!='')&&($message!='')&&($cmail!='')&&($cname!='')){  
	//Mail
	$this->load->library('email');			
	$config['mailtype'] = 'html';		
	$this->email->initialize($config);  
	$username =$cname;
	$eid =$cmail;
	$admin_mail= $this->Customer_model->get_admin($id=1); 
	$this->email->from($data['email'], $this->config->item('company_name'));
	$this->email->to($cmail);
	$this->email->subject($subject);
	$this->email->message($message);		 
	$mail=$this->email->send();    
	$this->session->set_flashdata('message', lang('contact_send'));   
	redirect('contact');	
	}  else {
	$this->session->set_flashdata('error','Enter all values correctly'); 		 
	redirect('contact');	
	} 				  
	}// button 
	$data['body_content'] = 'contact';
	$this->load->view('template', $data);
	}  //contact     
}
?>