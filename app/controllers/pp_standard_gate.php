<?php
/*  
* Company name  : BseTec Pvt Ltd.
* Product  name  : Themeforest clone for ecommerce  
* Framework name : Codeigniter
* Dev Team : Gokulnath,Balaji,Suchitra.
* File name : pp_gate.php (controller)
* Description : It is used for paypal express checkout payment gateway integrations
*/

class pp_standard_gate extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$this->config->set_item('language',$this->auth->language());
		$this->load->add_package_path(APPPATH.'packages/payment/paypal_standard/');
		$this->load->library(array('Paypal_Lib',  'bse_tec'));
		$this->load->helper('form_helper');
	}
	
	function index()
	{
		redirect('');
	}
	/* 
	   Receive postback confirmation from paypal
	   to complete the customer's order.
	*/
	function pp_return()
	{
		if($_POST['payment_status'] == "Completed") {
			$this->bse_tec->set_payment_confirmed();
			redirect('checkout/place_order/');				
		} else {
			$this->session->set_flashdata('message', "<div>Paypal did not validate your order. Either it has been processed already, or something else went wrong. If you believe there has been a mistake, please contact us.</div>");
			redirect('checkout');
		}
	}
	/* 
		Customer cancelled paypal payment
		
	*/
	function pp_cancel()
	{
		//make sure they're logged in if the config file requires it
		if($this->config->item('require_login'))
		{
			$this->Customer_model->is_logged_in();
		}
	
		// User canceled using paypal, send them back to the payment page
		$cart  = $this->session->userdata('cart');	
		$this->session->set_flashdata('message', "<div>Paypal transaction canceled, select another payment method</div>");
		redirect('checkout');
	}
}