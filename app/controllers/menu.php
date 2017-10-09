<?php
class Menu extends Admin_Controller {
	//this is used when editing or adding a customer
	var $customer_id	= false;	
	function __construct()
	{		
	parent::__construct();
	$this->load->model('Plugin_model');
	$this->config->set_item('language',$this->auth->language());
	$this->load->model(array('Menu_model'));
	}
	
	function index()
	{ 
	 $data['page_title']	= 'Menu Page';	
	 $data['menu']		= $this->Menu_model->get_menus();
	 // Plugin activation 
	 	$plugins = $this->Plugin_model->get_plugins();
		foreach($plugins as $plug):
		$this->auth->plugin_status($plug->table_name);
		endforeach;
	 // Plugin activation	 
	 $this->load->view($this->config->item('admin_folder').'/menu_page', $data); 
	}
	
	function addmenu()
	{ 
		$menu['page_title']	= 'Add Menu';	
		$menu['id']=$this->Menu_model->getmenuid();   // get all menu details   	
		$menu['menu_page']=$this->Menu_model->get_pages();   // get all menu details    	  
		$this->load->view($this->config->item('admin_folder').'/menu_setting',$menu);
	}
	
	function save()
	{
	  $this->load->model('Menu_model');        
		if($this->input->post('add'))
		{
		$this->Menu_model->process();                
		}
		$this->session->set_flashdata('message', lang('menu_sucessfully'));
		redirect($this->config->item('admin_folder').'/menu'); 
	} 
	/********************************************************************
	edit page
	********************************************************************/
	function form($id = false)
	{
		$this->load->helper('url');
		$this->load->helper('form');
		$this->load->library('form_validation'); 
		$data['page_title']	= 'Edit Menu';	
		if($id)
		{	 
		  	$menu			= $this->Menu_model->get_menu($id);	  
			//set values from db 
			$data['id']			= $menu->id;
			$data['title']		= $menu->title;  
			$data['url']	= $menu->url;  
			$data['dyn_group_id']	= $menu->dyn_group_id; 
			$data['position']	= $menu->position;  
			$data['parent_id']		= $menu->parent_id; 
			$data['show_menu']		= $menu->show_menu; 		 
			$data['menu']		= $this->Menu_model->get_menucs(); 
		} 
		$this->load->view($this->config->item('admin_folder').'/menu_edit_setting',$data);	
	}	 
	function update($id = false)
	{
	   $this->load->model('Menu_model');        
		if($this->input->post('update'))
		{
		$this->Menu_model->edit($id);                
		}
		$this->session->set_flashdata('message', lang('editmenu_sucessfully'));
		redirect($this->config->item('admin_folder').'/menu'); 
	} 
	/********************************************************************
	delete page
	********************************************************************/
	function delete($id)
	{
		$menu	= $this->Menu_model->get_menu($id);
		if($menu)
		{
			$this->load->model('Routes_model');
			
			$this->Routes_model->delete($menu->route_id);
			$this->Menu_model->delete_menu($id);
			$this->session->set_flashdata('message', lang('message_deleted_page'));
		}
		else
		{
			$this->session->set_flashdata('error', lang('error_page_not_found'));
		}
		redirect($this->config->item('admin_folder').'/menu');
	}
}