<?php
/*  
* Company name  : BseTec Pvt Ltd.
* Product  name  : Themeforest clone for ecommerce  
* Framework name : Codeigniter
* Dev Team : Gokulnath,Balaji,Suchitra.
* File name : quiz.php (controller)
*/

class Quiz extends Front_Controller {
	
	function __construct()
	{
		parent::__construct();
		
		$this->config->set_item('language',$this->auth->language());		
		$this->load->model(array('location_model'));
		$this->load->model('Quiz_model');
		$this->load->library('form_validation');
		$this->lang->load('product');
		$this->lang->load('quiz');
		$this->customer = $this->bse_tec->customer();
	}
	
	function index()
	{
		$data['body_content']			= 'quiz';
		$data['quiz']			= $this->Quiz_model->get_quizs();
		$this->load->view('template', $data);
	}
	
	function check(){
		$quiz_check			= $this->Quiz_model->get_quizs();	
		$i=1;
		foreach($quiz_check as $quiz){
		$this->form_validation->set_rules('answer_'.$i.'','Answer '.$i.'', 'required');
		$i++;
		}

		if ($this->form_validation->run() == FALSE)
		{
		$data['error'] = validation_errors();
		$data['body_content']			= 'quiz';
		$data['quiz']						= $this->Quiz_model->get_quizs();
		$this->load->view('template', $data);
		}
		else
		{
		$count=1;
		$user_score=0;
		foreach($quiz_check as $quiz){
		$check['id']	= $this->input->post('question_'.$count);
		$check['correct_answer']	= $this->input->post('answer_'.$count);
		$quiz_check			= $this->Quiz_model->check($check);
		if(@$quiz_check->status == 1)
		$user_score = $user_score+1 ;
		$count++;
		}

		if($user_score >= 8){
		
			$cart_val = $this->session->userdata('cart_contents');
			foreach($cart_val as $user)
			{
				if(isset($user['user_id'])) {
		   	$user_id = $user['user_id'];
			$email = $user['user_email'];	
			$name = $user['user_firstname'].' '.$user['user_lastname'];
				}	
			}
			$author['user_id'] = $user_id ;
			$author['user_type'] = '1';
			$author['score'] = $user_score;
			
			$this->Quiz_model->author($author);

//Send author notification

			$res = $this->db->where('id', '12')->get('canned_messages');
			$row = $res->row_array();

			$row['subject'] = str_replace('{site name}', $this->config->item('company_name'), $row['subject']);
			$row['content'] = str_replace('{user name}', $name, $row['content']);
			$row['content'] = str_replace('{site_name}', '<a href="'.site_url().'">'.$this->config->item('company_name').'</a>', $row['content']);
			
			$this->load->library('email');
			
			$config['mailtype'] = 'html';
			
			$this->email->initialize($config);
	
			$this->email->from($this->config->item('email'), $this->config->item('company_name'));
			$this->email->to($email);
			$this->email->bcc($this->config->item('email'));
			$this->email->subject($row['subject']);
			$this->email->message(html_entity_decode($row['content']));
			
			$this->email->send();


//send author notification

			$this->session->set_flashdata('message', sprintf( lang('success_author'), ' ') );
			redirect('products/form');
			}
		else{
			$this->session->set_flashdata('message', sprintf('Sorry your score is <b>'.$user_score.'</b> You must attend the exam again', ''));
			redirect('quiz');
			}
		}	
	}
}