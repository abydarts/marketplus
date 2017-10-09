<?php
/*  
* Company name  : BseTec Pvt Ltd.
* Product  name  : Themeforest clone for ecommerce  
* Framework name : Codeigniter
* Dev Team : Gokulnath,Balaji,Suchitra.
* File name : user_transaction.php (controller)
* For user deposit, withdraw, paypal success payment and cancel.
*/

class User_transaction extends Front_Controller {
	var $customer;
	function __construct()
	{
		parent::__construct();
		
		$this->config->set_item('language',$this->auth->language());		
		$this->load->model(array('location_model'));
		$this->load->model('digital_product_model');
		$this->lang->load('digital_product');
		$this->load->library('Paypal_Lib');
		$this->customer = $this->bse_tec->customer();
	}
	
	function index()
	{
		show_404();
	}
	
	function paypal_deposit() 
  {
  	   $this->Customer_model->is_logged_in();
  	 	$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<div>', '</div>');		
		$this->form_validation->set_rules('amount', 'amount', 'trim|numeric|required');  	 
		if ($this->form_validation->run() == FALSE)
		{		
			$data['error'] = validation_errors();
			$data['body_content']			= 'user/user_deposit';
		
			$this->load->view('template', $data);
		}
		else
		{
		$primary = "bsetec_footer.png";
  	  	 $trans['trans_id'] = "";   	    
	   	 $trans['trans_amount'] = $this->input->post('amount'); 
  	  	  $trans['user_id'] = $this->customer['user_id'];
      		 $trans['trans_type'] = "Deposit";   
		 	  
		 $trans['status'] = "0";   	         
      		 $trans['trans_method'] = "paypal_standard";   	   
  	  	  //$trans['currency_code'] = "USD";
  	  	  $trans['currency_code'] = $this->currency;
  	    	$trans_number = $this->Customer_model->save_transaction($trans);
  	    	$settings	= $this->Settings_model->get_settings('paypal_standard');
		$paypal_id 	= $settings['paypal_id'];
		
		 $this->paypal_lib->add_field('business', $paypal_id);
		 $this->paypal_lib->add_field('cmd', '_xclick');
	    $this->paypal_lib->add_field('return', site_url('user_transaction/paypal_success'));
	    $this->paypal_lib->add_field('cancel_return', site_url('user_transaction/paypal_cancel'));
	    $this->paypal_lib->add_field('notify_url', site_url('user_transaction/paypal_ipn')); // <-- IPN url
 		 $this->paypal_lib->add_field('currency_code', $trans['currency_code']); 
	    $this->paypal_lib->add_field('custom', $trans['user_id'].'/'.$trans['trans_amount'].'/'. $trans['trans_type'].'/'. $trans_number.'/');
		 // Verify return
		 $this->paypal_lib->add_field('item_name', $this->bse_tec->get_site_title().' Transaction');
	    $this->paypal_lib->add_field('item_number', $trans_number );
	    $this->paypal_lib->add_field('amount', $trans['trans_amount']);
	    $this->paypal_lib->add_field('cpp_header_image', base_url('uploads/images/'.$primary));
	    $this->paypal_lib->add_field('image_url',base_url('uploads/images/'.$primary));	
		
		// if you want an image button use this:
		$this->paypal_lib->image('button_03.gif');
		
		// otherwise, don't write anything or (if you want to 
		// change the default button text), write this:
		
	 	$data['paypal_form'] = $this->paypal_lib->paypal_form();
		$this->paypal_lib->paypal_auto_form();
  		}
  	}
	// after paypal success payment
	
	function paypal_success()
	{
		$this->Customer_model->is_logged_in();
		$data['page_title']	= 'paypal success';				
		$data['body_content']			= 'paypal/paypal_success';
		$this->load->view('template', $data);
	}
	
	// after paypal payment if payment is cancelled
	function paypal_cancel()
	{
		$data['page_title']	= 'paypal cancel';		
		$data['body_content']			= 'paypal/paypal_cancel';
		$this->load->view('template', $data);
		}	

// payment details inserted here
	
	function paypal_ipn()
    	{
   	 		$trans_data = $_POST['custom'];
   	 		$data['customer']			= $this->bse_tec->customer();
				$transaction = explode("/",$trans_data);
				$user_id     = $transaction[0]; 	 		
				$trans_amt     = $transaction[1]; 	 		
				$trans_id     = $transaction[3]; 	 		
   	 		
   	 		if($_POST['mc_gross'] == $trans_amt) 
   	 		{				
				$balance["user_id"]  = $user_id;
				//$balance["currency_code"]  = "USD";
				$balance['currency_code'] = $this->currency;
				$balance["balance"]  = $trans_amt; 
				$balance['text'] = "Deposit amount($".$trans_amt.")"; 
				$trans["trans_id"] = $trans_id ;
				$trans["status"]   = "1";
				$trans["paypal_payer_email"] = $_POST['payer_email'];
				$trans["paypal_receiver_email"] = $_POST['receiver_email'];
				
				$trans['trans_text'] = $_POST['txn_id'].",".$_POST['payment_status'];
				
				$this->load->model('messages_model');
				$row = $this->messages_model->get_message(10);
					 $trans_number = $this->Customer_model->save_transaction($trans); 
					 $save_balance = $this->Customer_model->save_balance($balance); 		
					if($trans_number) 
					{
							// send mail if details updated successfully on user_transaction table	
							$row['subject'] = str_replace('{site name}', $this->config->item('company_name'), $row['subject']);
	   	 				$row['content'] = str_replace('{user name}', $data['customer']['user_firstname'].' '.$data['customer']['user_lastname'], $row['content']);
							$row['content'] = str_replace('{payer email}', $_POST['payer_email'], $row['content']);
							$row['content'] = str_replace('{amount}', $trans_amt, $row['content']);
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
   	 	
   	 		} else {
	    	
    						$row['subject'] = str_replace('{site name}', $this->config->item('company_name'), $row['subject']);
	   	 				$row['content'] = str_replace('{user name}', $data['customer']['user_firstname'].' '.$data['customer']['user_lastname'], $row['content']);
							$row['content'] = str_replace('{payer email}', $_POST['payer_email'], $row['content']);
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
							
								}	
		
							}
				}
	}