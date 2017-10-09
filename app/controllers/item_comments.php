<?php
class Item_comments extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->model('Itemcomment_model');
		$this->customer = $this->bse_tec->customer();
		$this->load->model('Product_model');
		$this->load->model('customer_model');
		$this->lang->load('product');
	}
	function index(){
		 $item_id = $_POST['prod_id'];                  
       $data['query'] = $this->Itemcomment_model->get_comments($item_id);
		$data['body_content'] = 'comments/item_comments';
		$this->load->view('template/default/comments/item_comments',$data);	
		}
	
	function ajax()
	{
 $this->Customer_model->is_logged_in();
    	if(isset($_POST['page_id']) and isset($_POST['comment'])){
        	$cart_val = $this->session->userdata('cart_contents');
		foreach($cart_val as $user)
			{
			if(isset($user['user_id'])) {
		   	$data['user_id'] = $user['user_id'];	
			}	
		 }
        $data['page_id'] = $_POST['page_id'];
        $data['item_id'] = $_POST['product_ids'];
        $data['comment'] = $_POST['comment'];
        $data['time'] = time();
        $result = $this->Itemcomment_model->inserttodb($data);
         $item_id = $_POST['product_ids'];   
         $show['query'] = $this->Itemcomment_model->get_comments($item_id);
			echo $this->load->view('template/default/comments/item_comments',$show);
	}
	}

	function counts(){
		$item_id = $_POST['prod_id'];    
       $top_products			= $this->Product_model->get_top_product($item_id);
   	foreach($top_products as $prod){
		if($prod->c == NULL)
	$counts = 0;
	else
		$counts = $prod->c;
		}	
       $query = $this->Itemcomment_model->get_comments($item_id);
		echo @$counts.','.count($query);
		}
}