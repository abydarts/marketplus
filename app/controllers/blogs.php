<?php

class Blogs extends Front_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model(array('Blog_model'));
		$this->customer = $this->bse_tec->customer();
		$this->lang->load('blogs');
		$this->load->helper('date');
	} 
	function index()
	{
		$data['base_url']			= $this->uri->segment_array();	//breadcrumb
		$data['search_box'] = 'blog';
		$data['blog']				= $this->Blog_model->get_blogs(); 
		$data['categories']			= $this->Blog_model->get_blogs_cat(); // blog categories listing
		$data['recent']				= $this->Blog_model->get_homepage_blogs(); // recent blogs
		$data['tags']				= $this->Blog_model->get_tags(); // blog tags
		$data['page_title']	= lang('blog_updates');
		$data['body_content']			= 'blog';
		$this->load->view('template', $data);
	}
	  // Blogs	
	 
	// Blog Details 
	function blog_details($id=false)	{ 
	$this->load->library('breadcrumb');
	$this->breadcrumb->append_crumb('Home', base_url());
	$this->breadcrumb->append_crumb('Blog', site_url('blogs'));
	$this->breadcrumb->append_crumb($id, base_url());
	$breadcrumb = $this->breadcrumb->output();
	$data['breadcrumb'] = $breadcrumb;
	$data['search_box'] = 'blog';
		$data['blog']		= $this->Blog_model->get_blog($id);  
		$data['categories']		= $this->Blog_model->get_blogs_cat(); // blog categories listing
		$data['tags']				= $this->Blog_model->get_tags(); // blog tags
		$data['recent']				= $this->Blog_model->get_homepage_blogs(); // recent blogs
		$data['body_content']			= 'blog_details';
		
		$this->load->view('template', $data);
	}	
	// Blog Category lsiting Page 
	function blog_cat($id=false){
		$this->load->library('breadcrumb');
	$this->breadcrumb->append_crumb('Home', base_url());
	$this->breadcrumb->append_crumb('Blog', site_url('blogs'));
	$this->breadcrumb->append_crumb('Blog Categories', base_url());
	$breadcrumb = $this->breadcrumb->output();
	$data['breadcrumb'] = $breadcrumb;
	$data['search_box'] = 'blog';
		$data['blog']				= $this->Blog_model->get_catblogs($id);  
		$data['recent']				= $this->Blog_model->get_homepage_blogs(); // recent blogs
		$data['tags']				= $this->Blog_model->get_tags(); // blog tags
		$data['categories']		= $this->Blog_model->get_blogs_cat(); // blog categories listing
		$data['page_title']	= lang('blog_category');
		$data['body_content']			= 'blog_cat';
		$this->load->view('template', $data);
	}	
	// Blog Comment  
	function blog_ajax(){
		 $item_id = $_POST['prod_id'];            
       $data['query'] = $this->Blog_model->get_comments($item_id); 
		 $data['body_content'] = 'comments/item_comments';
		 $this->load->view('template/default/comments/blog_comments',$data);
	}
    //Ajax
	function ajax() 	{
 	$this->Customer_model->is_logged_in();
    	if(isset($_POST['page_id']) and isset($_POST['comment'])){
        	$cart_val = $this->session->userdata('cart_contents');
		foreach($cart_val as $user)
			{
			if(isset($user['user_id'])) {
		   	$data['user_id'] = $user['user_id'];	
			}	

		 }
        $data['blog_id'] = $_POST['product_ids'];
        $data['comment'] = $_POST['comment'];
        $data['time'] = time();
       
        $result = $this->Blog_model->inserttodb($data);
         $item_id = $_POST['product_ids'];   
         $show['query'] = $this->Blog_model->get_comments($item_id);
			echo $this->load->view('template/default/comments/blog_comments',$show);
	}
	}
//Marketplus version 1.3

		function blog_search()	{ 
		$keyword	=$this->input->post('term');	   
		$data['base_url']			= $this->uri->segment_array();	//breadcrumb
		$data['blog']				= $this->Blog_model->get_blog_search($keyword); 
		$data['categories']			= $this->Blog_model->get_blogs_cat(); // blog categories listing
		$data['recent']				= $this->Blog_model->get_homepage_blogs(); // recent blogs
		$data['tags']				= $this->Blog_model->get_tags(); // blog tags 
		$data['page_title']	= lang('blog_updates');
		$data['seo_title']			= lang('blog_updates');
		$data['body_content']			= 'blog';
		$data['search_box'] = 'blog';
		$this->load->view('template', $data);
		}
}