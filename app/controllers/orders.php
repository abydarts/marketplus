<?php
class Orders extends Front_Controller {	

	function __construct()
	{		
		parent::__construct();

		$this->config->set_item('language',$this->auth->language());
		$this->load->model('Order_model');
		$this->load->model('Search_model');
		$this->load->model('location_model');
		$this->load->helper(array('formatting'));
		$this->lang->load('order');
	}
	
	function view($id)
	{
		$this->load->helper(array('form', 'date'));
		$this->load->library('form_validation');
					
		$this->form_validation->set_rules('notes', 'lang:notes');
		$this->form_validation->set_rules('status', 'lang:status', 'required');
	
		$message	= $this->session->flashdata('message');
			
		if ($this->form_validation->run() == TRUE)
		{
			$save			= array();
			$save['id']		= $id;
			$save['notes']	= $this->input->post('notes');
			$save['status']	= $this->input->post('status');
			$data['message']	= lang('message_order_updated');
			$this->Order_model->save_order($save);
		}
		//get the order information, this way if something was posted before the new one gets queried here
		$data['page_title']	= lang('view_order');
		$data['order']		= $this->Order_model->get_order($id);
		
		/*****************************
		* Order Notification details *
		******************************/
		// get the list of canned messages (order)
		$this->load->model('Messages_model');
    	$msg_templates = $this->Messages_model->get_list('order');
		
		// replace template variables

    		$data['body_content']			= 'user/order';
		   $this->load->view('template', $data);		
	}
	
	function bulk_delete()
    {
    	$orders	= $this->input->post('order');
    	if($orders)
		{
			foreach($orders as $order)
	   		{
	   			$this->Order_model->delete($order);
	   		}
			$this->session->set_flashdata('message', lang('message_orders_deleted'));
		}
		else
		{
			$this->session->set_flashdata('error', lang('error_no_orders_selected'));
		}
   		//redirect as to change the url
		redirect($this->config->item('admin_folder').'/orders');	
    }
}