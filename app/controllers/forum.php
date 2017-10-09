<?php
/* Controller Name : Forum */

class Forum extends Front_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model(array('Forum_model','Category_model'));
		$this->customer = $this->bse_tec->customer();
		$this->lang->load('forum');
		$this->load->helper('date');
		$this->load->library('breadcrumb');
	} 
	
	function index()	{
		$this->breadcrumb->append_crumb('Home', base_url());
		$this->breadcrumb->append_crumb('Forum', base_url());
		$breadcrumb = $this->breadcrumb->output();
		$data['search_box'] = 'forum';
		$data['breadcrumb'] = $breadcrumb;
		$data['forum']				= $this->Forum_model->get_forums();		 
		$data['categories']		= $this->Forum_model->get_forum_cats(); // forum categories listing  
		$data['recent']			= $this->Forum_model->get_homepage_forum(); // recent blogs   
		$data['page_title']	= lang('forum');
		$data['body_content']			= 'forum/forum';
		$this->load->view('template', $data);
	}
	  // forum	
	 
	// forum Details 
	function forum_details($id)	{ 
		$data['base_url']			= $this->uri->segment_array();	//breadcrumb
		$data['search_box'] = 'forum';
		$data['forum']		= $this->Forum_model->get_forum($id);  
		$data['recent']				= $this->Forum_model->get_homepage_forum(); // recent blogs
		$data['categories']		= $this->Forum_model->get_forum_cats(); // forum categories listing  
		$data['body_content']			= 'forum/forum_details';
		$this->load->view('template', $data);		
	}	
	
	// forum Category lsiting Page 
	function forum_cat($id=false){
		$this->breadcrumb->append_crumb('Home', base_url());
		$this->breadcrumb->append_crumb('Forum', site_url('forum'));
		$this->breadcrumb->append_crumb($id, base_url());
		$breadcrumb = $this->breadcrumb->output();
		$data['breadcrumb'] = $breadcrumb;
		$data['search_box'] = 'forum';
	  	$data['forum']				= $this->Forum_model->get_catforums($id);   
		$data['cname']				=$this->Forum_model->get_forum_cat($id); 
		$data['recent']				= $this->Forum_model->get_homepage_forum(); // recent blogs
		$data['categories']		= $this->Forum_model->get_forum_cats(); // forum categories listing
		//print_r($data);
		$data['page_title']	= lang('forum_category');
		$data['body_content']			= 'forum/forum_cat';
		$this->load->view('template', $data);	
	}	
	
	// forum Comment  
	function forum_ajax(){
		$item_id = $_POST['prod_id'];            
		$data['query'] = $this->Forum_model->get_comments($item_id);
		$data['body_content']			= 'forum/forum_comments';
		$this->load->view('template/default/forum/forum_comments', $data);	 	
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
        $data['forum_id'] = $_POST['product_ids'];
        $data['comment'] = $_POST['comment'];
        $data['time'] = time();
        
     
        $result = $this->Forum_model->inserttodb($data);
         $item_id = $_POST['product_ids'];   
         $show['query'] = $this->Forum_model->get_comments($item_id);
	echo $this->load->view('template/default/forum/forum_comments',$show);
	}
	}
	
	//forum user form
	function form($id = false)	{ 
		$this->load->helper('form');
		$this->load->library('form_validation');			
		//set the default values
		$data	= array(	 'id'=>$id
							,'category_id'=>''
							,'created_time'=>''  
							,'forum_topic'=>'' 
							,'forum_content'=>'' 
							,'created_by'=>'' 
							,'status'=>''
						);
		if($id)	{
			$forum				= (array) $this->Forum_model->get_forum($id);		
			$data['page_title']	= lang('forum_form');	
			$data['category_id']		= $forum['category_id']; 	
			$data['forum_topic']		= $forum['forum_topic'];  
			$data['forum_content']		= $forum['forum_content'];  
			$data['created_by']	= $forum['created_by'];  
			$data['status']	= $forum['status']; 
			$data['recent']				= $this->Forum_model->get_homepage_forum(); // recent blogs
			$data['categories']		= $this->Forum_model->get_forum_cats();
		}
		
		$this->form_validation->set_rules('created_date', 'lang:created_date', 'trim');  
		$this->form_validation->set_rules('forum_topic', 'lang:forum_topic', 'trim|required'); 
		$this->form_validation->set_rules('forum_content', 'lang:forum_content', 'trim|required'); 
		
		if ($this->form_validation->run() == false)	{
			$data['error'] = validation_errors();
			$data['categories']		= $this->Forum_model->get_forum_cats();
			$data['recent']				= $this->Forum_model->get_homepage_forum(); // recent blogs
			$this->load->library('breadcrumb');
	$this->breadcrumb->append_crumb('Home', base_url());
	$this->breadcrumb->append_crumb('Forum', base_url());
	$breadcrumb = $this->breadcrumb->output();
	$data['search_box'] = 'forum';
	$data['breadcrumb'] = $breadcrumb;
			$data['body_content']			= 'forum/forum_topic_form';
			$this->load->view('template', $data);

		} else {	 
			$save['forum_topic']			= $this->input->post('forum_topic'); 
			$save['forum_content']			= $this->input->post('forum_content');
			$save['category_id']			= $this->input->post('category_id');
			$save['created_by']			= $this->input->post('created_by');
			$save['status']				= $this->input->post('status');
			$save['created_time']			= time(); 
		 	if ($id)	{
		 		$save['id']	= $id; 
			 }  
			$this->Forum_model->save($save);			
			$this->session->set_flashdata('message', lang('message_box_saved'));			
			redirect('forum');
		}	
	}
	
	function delete($id)	{
		$this->Forum_model->delete($id);
		$this->session->set_flashdata('message', lang('message_delete_box'));
		redirect('forum');
	}
	
	function forum_search()	{ 
		$keyword	=$this->input->post('term');
		$data['base_url']			= $this->uri->segment_array();	//breadcrumb
		$data['recent']				= $this->Forum_model->get_homepage_forum(); // recent blogs
		$data['categories']		= $this->Forum_model->get_forum_cats(); // forum categories listing 
		$data['forum']				= $this->Forum_model->get_forum_search($keyword);		   
		$data['page_title']	= lang('search_forum');
		$data['search_box'] = 'forum';

		$data['body_content']			= 'forum/forum_search';
		$this->load->view('template', $data);
	}
}