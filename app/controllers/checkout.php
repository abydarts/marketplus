<?php 
/*  
* Company name  : BseTec Pvt Ltd.
* Product  name  : Themeforest clone for ecommerce  
* Framework name : Codeigniter
* Dev Team : Gokulnath,Balaji,Suchitra.
* File name : checkout.php (controller)
*/

/* Single page checkout controller*/

class Checkout extends Front_Controller {

	function __construct()
	{
		parent::__construct();

		$this->config->set_item('language',$this->auth->language());
		/*make sure the cart isnt empty*/
		if($this->bse_tec->total_items()==0)
		{
			redirect('cart/view_cart');
		}

		/*is the user required to be logged in?*/
		if (config_item('require_login'))
		{
			$this->Customer_model->is_logged_in('checkout');
		}

		$this->load->library('form_validation');
	}

	function index()
	{
		/*show address first*/
		redirect('StepTwo');
	}
	
	function step_2()
	{
			$this->step_3();
	}

	function step_3()
	{
		$customer	= $this->bse_tec->customer();

		if($payment_methods = $this->_get_payment_methods())
		{
			$this->payment_form($payment_methods);
		}
		/* now where? continue to step 4 */
		else
		{
			redirect('Confirm');
		}
	}

	protected function payment_form($payment_methods)
	{
		$this->load->library('breadcrumb');
	$this->breadcrumb->append_crumb('Home', base_url());
	$this->breadcrumb->append_crumb('Checkout', base_url());
	$breadcrumb = $this->breadcrumb->output();
	$data['breadcrumb'] = $breadcrumb;
		/* find out if we need to display the shipping */
		$data['customer']			= $this->bse_tec->customer();
	
		$customer_id = $data['customer']['user_id'];
		$data['user_balance']	= $this->Customer_model->get_current_balance($customer_id);

		$data['payment_method']		= $this->bse_tec->payment_method();

		/* pass in the payment methods */
		$data['payment_methods']	= $payment_methods;
		
		/* require that a payment method is selected */
		$this->form_validation->set_rules('module', 'lang:payment_method', 'trim|required|xss_clean|callback_check_payment');

		$module = $this->input->post('module');
		if($module)
		{

			$this->load->add_package_path(APPPATH.'packages/payment/'.$module.'/');
			$this->load->library($module);
		}

		if($this->form_validation->run() == false)
		{			
			$data['body_content']			= 'checkout/payment_form';
			$this->load->view('template', $data);			
		}
		else
		{
			$this->bse_tec->set_payment( $module, $this->$module->description() );
			redirect('Confirm');
		}
	}
	/* callback that lets the payment method return an error if invalid */
	function check_payment($module)
	{
		$check	= $this->$module->checkout_check();

		if(!$check)
		{
			return true;
		}
		else
		{
			 $this->session->set_flashdata('error', 'Fill (*) Mandatory fields'); // version 1.3
			 redirect('checkout/step_3'); // version 1.3
		}
	}

	private function _get_payment_methods()
	{
		$payment_methods	= array();
		if($this->bse_tec->total() != 0)
		{
			foreach ($this->Settings_model->get_settings('payment_modules') as $payment_method=>$order)
			{
				$this->load->add_package_path(APPPATH.'packages/payment/'.$payment_method.'/');
				$this->load->library($payment_method);

				$payment_form = $this->$payment_method->checkout_form();

				if(!empty($payment_form))
				{
					$payment_methods[$payment_method] = $payment_form;
				}
			}
		}
		if(!empty($payment_methods))
		{
			return $payment_methods;
		}
		else
		{
			return false;
		}
	}

	function step_4()
	{
		$this->load->library('breadcrumb');
	$this->breadcrumb->append_crumb('Home', base_url());
	$this->breadcrumb->append_crumb('Checkout', base_url());
	$breadcrumb = $this->breadcrumb->output();
	$data['breadcrumb'] = $breadcrumb;
		/* get addresses */
		$data['customer']		= $this->bse_tec->customer();

		$data['payment_method']		= $this->bse_tec->payment_method();

		/* Confirm the sale */
		$data['body_content']			= 'checkout/confirm';
		
		$this->load->view('template', $data);
	}

	function login()
	{
		$this->Customer_model->is_logged_in('checkout');
	}

	function register()
	{
		$this->Customer_model->is_logged_in('checkout', 'secure/register');
	}

	function place_order()
	{	
	$this->load->library('breadcrumb');
	$this->breadcrumb->append_crumb('Home', base_url());
	$this->breadcrumb->append_crumb('Checkout', base_url());
	$breadcrumb = $this->breadcrumb->output();
	// retrieve the payment method
		$payment 			= $this->bse_tec->payment_method();
		$payment_methods	= $this->_get_payment_methods();
		
		//make sure they're logged in if the config file requires it
		if($this->config->item('require_login'))
		{
			$this->Customer_model->is_logged_in();
		}
		
		if($this->input->post('module')=='desposit'){
		$customer			= $this->bse_tec->customer();
		
		$data['text']  = 'Purchased item($'.$this->bse_tec->total().')';
		$data['user_id'] = $customer['user_id'];
		$user_balance	= $this->Customer_model->get_current_balance($data['user_id']);
		
		$userbalance = $user_balance->balance;
		
		$data['balance'] = $userbalance - $this->bse_tec->total();
				
		$data['currency_code'] = 'USD';
		$error_status = 'Your account balance is running low. You will need to deposit some more funds to afford this item. It costs $'.$this->bse_tec->total().' and you have $'.$user_balance->balance.' <a href="'.site_url('user/my_deposit').'">Deposit Cash?</a>';
		if($userbalance < $this->bse_tec->total()){
				$this->session->set_flashdata('error', $error_status);
					redirect('StepTwo');		
		}
		$this->Customer_model->purchase_from_deposit($data);
		}
				
		// are we processing an empty cart? 
		$contents = $this->bse_tec->contents();
		$contents = $this->bse_tec->contents();
		foreach($contents as $conts){
			if($conts['standard']==1){
			$standards = array(
               		'standard' => 1,
			'enabled' => 4
            		);
		$this->db->where('id', $conts['id']);
		$this->db->update('products', $standards); 
			}
		}

	
		if(empty($contents))
		{
			redirect('cart/view_cart');
		} 
		
		if(!empty($payment) && (bool)$payment_methods == true)
		{
			//load the payment module
			$this->load->add_package_path(APPPATH.'packages/payment/'.$payment['module'].'/');
			$this->load->library($payment['module']);
		
			// Is payment bypassed? (total is zero, or processed flag is set)
			if($this->bse_tec->total() > 0 && ! isset($payment['confirmed'])) {
				//run the payment
				$error_status	= $this->$payment['module']->process_payment();
				if($error_status !== false)
				{
					// send them back to the payment page with the error
					$this->session->set_flashdata('error', $error_status);
					redirect('checkout/step_3');
				}
			}
		}
			
		
		// save the order
		$order_id = $this->bse_tec->save_order();
		
		$data['order_id']			= $order_id;
		$data['payment']			= $this->bse_tec->payment_method();
		$data['customer']			= $this->bse_tec->customer();
				
		$order_downloads 			= $this->bse_tec->get_order_downloads();
		
		$data['hide_menu']			= true;
		
		// run the complete payment module method once order has been saved
	if(!empty($payment))
		{
			if(method_exists($this->$payment['module'], 'complete_payment'))
			{
				$this->$payment['module']->complete_payment($data);
			}
		}
	
		// Send the user a confirmation email
		
		// - get the email template
		$this->load->model('messages_model');
		$row = $this->messages_model->get_message(7);
		
		$download_section = '';
		if( ! empty($order_downloads))
		{
			// get the download link segment to insert into our confirmations
			$downlod_msg_record = $this->messages_model->get_message(8);
			
			if(!empty($data['customer']['id']))
			{
				// they can access their downloads by logging in
				$download_section = str_replace('{download_link}', anchor('user/my_downloads', lang('download_link')),$downlod_msg_record['content']);
			} else {
				// non regs will receive a code
				$download_section = str_replace('{download_link}', anchor('user/my_downloads/'.$order_downloads['code'], lang('download_link')), $downlod_msg_record['content']);
			}
		}
		
		$row['content'] = html_entity_decode($row['content']);
		
		// set replacement values for subject & body
		// {customer_name}
		$row['subject'] = str_replace('{customer_name}', $data['customer']['user_firstname'].' '.$data['customer']['user_lastname'], $row['subject']);
		$row['content'] = str_replace('{customer_name}', $data['customer']['user_firstname'].' '.$data['customer']['user_lastname'], $row['content']);
		
		// {url}
		$row['subject'] = str_replace('{url}', $this->config->item('base_url'), $row['subject']);
		$row['content'] = str_replace('{url}', $this->config->item('base_url'), $row['content']);
		
		// {site_name}
		$row['subject'] = str_replace('{site_name}', $this->config->item('company_name'), $row['subject']);
		$row['content'] = str_replace('{site_name}', $this->config->item('company_name'), $row['content']);
			
		// {order_summary}
	
		$row['content'] = str_replace('{order_summary}', $this->load->view('pre_load/order_email', $data,true), $row['content']);
		
		// {download_section}
		$row['content'] = str_replace('{download_section}', $download_section, $row['content']);
			
		$this->load->library('email');
		
		$config['mailtype'] = 'html';
		$this->email->initialize($config);

		$this->email->from($this->config->item('email'), $this->config->item('company_name'));
		
		if($this->Customer_model->is_logged_in(false, false))
		{
			$this->email->to($data['customer']['user_email']);
		}
		else
		{
			$this->email->to($data['customer']['ship_address']['email']);
		}
		
		$this->email->subject($row['subject']);
		$this->email->message($row['content']);
		
		$this->email->send();

		//Admin Mail
		$link = $this->Order_model->get_order_id($order_id);
		$this->load->library('email');
			
			$config['mailtype'] = 'html';
			
			$this->email->initialize($config);
	
			$this->email->from($this->config->item('email'), $this->config->item('company_name'));
			$this->email->to($this->config->item('email'));
			$this->email->subject('Sale Details');
			$this->email->message('Product Has purchased by '.$data['customer']['user_firstname'].' '.$data['customer']['user_lastname'].' <a href="'.site_url($this->config->item('admin_folder').'/orders/view/'.$link->id).'"> Click here </a> to view the details');
			
			$this->email->send();		
		
		//Amin Mail

		//Seller Mail
		$seller['content'] = str_replace('{order_summary}', $this->load->view('pre_load/order_email', $data,true), $row['content']);
		$this->load->library('email');
			
			$config['mailtype'] = 'html';
			
			$this->email->initialize($config);
	
			$this->email->from($this->config->item('email'), $this->config->item('company_name'));
			$this->email->to($this->Order_model->get_seller_email($link->id)->user_email);
			$this->email->subject('Sale Details');
			$this->email->message('Product Has purchased by '.$data['customer']['user_firstname'].' '.$data['customer']['user_lastname'].' <a href="'.site_url('user/my_balance').'"> Click here </a> to view the details');
			
			$this->email->send();		
		
		//Seller Mail

		$data['page_title'] = 'Thanks for shopping with '.$this->config->item('company_name');
		$data['download_section']	= $download_section;
		/*  get all cart information before destroying the cart session info */
		
		$data['bse_tec']['subtotal']            = $this->bse_tec->subtotal();
		
		$data['bse_tec']['order_tax']           = $this->bse_tec->order_tax();
		$data['bse_tec']['discounted_subtotal'] = $this->bse_tec->discounted_subtotal();
		
		$data['bse_tec']['total']               = $this->bse_tec->total();
		$data['bse_tec']['contents']            = $this->bse_tec->contents();
		//Affiliate Referral Commission calculation code: 
		
		if($this->session->userdata('referral')) 
		{
		$credit = $this->Settings_model->get_settings('credits'); 
		$ref['user_id'] = $this->session->userdata('referral');	
		$ref['currency_code'] = 'USD';
		$ref_comm = $this->bse_tec->total()* ($credit['product_referral_credits']/100);
		$ref_user_current_bal = $this->Customer_model->get_current_balance($ref['user_id']);
		$ref['balance'] =  $ref_user_current_bal->balance + $ref_comm; 
		$ref['text'] = 'Referral commission($'.$ref_comm.')';
		$this->Customer_model->purchase_from_deposit($ref);
		}
		/* remove the cart from the session */
	  	$this->session->unset_userdata('referral');
		$this->bse_tec->destroy();

		/*  show final confirmation page */

		$data['body_content']			= 'order_placed';
		$this->load->view('template', $data);		//$this->load->view('order_placed', $data);
	}
}