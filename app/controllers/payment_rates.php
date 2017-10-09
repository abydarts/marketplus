<?php
/*  
* Company name  : BseTec Pvt Ltd.
* Product  name  : Themeforest clone for ecommerce  
* Framework name : Codeigniter
* Dev Team : Gokulnath,Balaji,Suchitra.
* File name : quiz.php (controller)
*/
class Payment_rates extends Front_Controller {
	
	function __construct()
	{
		parent::__construct();
		
		$this->config->set_item('language',$this->auth->language());		
		$this->load->model(array('location_model'));
		$this->load->model('Percentage_model');
		$this->load->library('form_validation');
		$this->lang->load('product');
		$this->lang->load('percentage');
		$this->customer = $this->bse_tec->customer();
	}
	
	function index()
	{
	$this->load->library('breadcrumb');
	// add breadcrumbs
	 $this->breadcrumb->append_crumb('Home', base_url());
	 $this->breadcrumb->append_crumb('Payment Rates', base_url().'payment_rates');
	// put this line in view to output
	$breadcrumb = $this->breadcrumb->output();
	$data['breadcrumb']	= $breadcrumb;
	$data['body_content']			= 'payment_rates';
		
		$data['plan']			= $this->Percentage_model->get_plan();
		$data['payment_rates']			= $this->Percentage_model->get_percentage($data['plan']->id);
		$this->load->view('template', $data);
	}
}