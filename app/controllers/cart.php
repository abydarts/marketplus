<?php
/*  
* Company name  : BseTec Pvt Ltd.
* Product  name  : Themeforest clone for ecommerce  
* Framework name : Codeigniter
* Dev Team : Gokulnath,Balaji,Suchitra.
* File name : cart.php (controller)
*/

class Cart extends Front_Controller {

	function __construct()
	{
		parent::__construct();
		
		//make sure we're not always behind ssl
		$this->config->set_item('language',$this->auth->language());
		$this->load->helper('date');
		$this->load->language('product');
		$this->load->language('category');
		$this->load->model(array('Banner_model','Bsepanel_model','Customer_model'));
	}

	function index()
	{
		redirect('Search/all');
	}

	function page($id = false)
	{
		//if there is no page id provided redirect to the homepage.
		$data['page']	= $this->Page_model->get_page($id);
		$data['contents']	= $this->Bsepanel_model->get_content($id)->result(); // page management
		if(!$data['page']){
			show_404();
		}
		$this->load->model('Page_model');
		$data['base_url']			= $this->uri->segment_array();	
		$data['fb_like']			= true;	
		$data['page_title']			= $data['page']->title;	
		$data['meta']				= $data['page']->meta;
		$data['seo_title']			= (!empty($data['page']->seo_title))?$data['page']->seo_title:$data['page']->title;	
		$data['body_content']			= 'page';
		$this->load->view('template', $data);
	}
	
	function search($code=false, $page = 0)
	{
		$this->load->library('breadcrumb');
		$this->breadcrumb->append_crumb('Home', base_url());
		$this->breadcrumb->append_crumb('ALL Products', base_url());
		$breadcrumb = $this->breadcrumb->output();
		$data['breadcrumb']	= $breadcrumb;
		$this->load->model('Search_model');
		if(!$code)
		{
			//if the term is in post, save it to the db and give me a reference
			$term		= $this->input->post('term', true);
			$code		= $this->Search_model->record_term($term);
			
			// no code? redirect so we can have the code in place for the sorting.
			// I know this isn't the best way...
			redirect('Search/'.$code);
		}
		else if($code=='alls'){
			//if the term is in post, save it to the db and give me a reference
			$term		= ' ';
			$code		= $this->Search_model->record_terms($term);
			// no code? redirect so we can have the code in place for the sorting.
			// I know this isn't the best way...
			redirect('Search/'.$code);
			}
		else
		{
			//if we have the md5 string, get the term
			$term	= $this->Search_model->get_term($code);
		}
		
		if(empty($term))
		{
			//if there is still no search term throw an error
			$this->session->set_flashdata('error', lang('search_error'));
			redirect('cart');
		}
		$data['page_title']			= lang('search');
		//fix for the category view page.
		$data['base_url']			= array();
		
		$sort_array = array(
							'name/asc' => array('by' => 'name', 'sort'=>'ASC'),
							'name/desc' => array('by' => 'name', 'sort'=>'DESC'),
							'price/asc' => array('by' => 'price', 'sort'=>'ASC'),
							'price/desc' => array('by' => 'price', 'sort'=>'DESC'),
							);
		$urls = parse_url($_SERVER['REQUEST_URI']);
		@$get_val = explode('=',$urls['query']);			
		//echo $urls['query'];
		if(@$urls['query']=='')
		{
		$sort_by	= array('by'=>'id', 'sort'=>'ASC');
		//echo $sort_by['by'].' '.$sort_by['sort'];
		}	
		else
		{
			@$sort_bys	= explode('/',$get_val[1]);
			@$sort_by = array('by'=>$sort_bys[0], 'sort'=>$sort_bys[1]);
		}
		
		if(empty($term))
		{
			//if there is still no search term throw an error
			$this->load->view('search_error', $data);
		}
		else
		{
			$data['page_title']	= lang('search');
			
			//set up pagination
			$this->load->library('pagination');
			$config['base_url']		= base_url().'Search/'.$code.'/';
			$config['uri_segment']	= 4;
			$config['per_page']		= 16;
			
			$config['first_link'] = 'First';
			$config['first_tag_open'] = '<li>';
			$config['first_tag_close'] = '</li>';
			$config['last_link'] = 'Last';
			$config['last_tag_open'] = '<li>';
			$config['last_tag_close'] = '</li>';

			$config['full_tag_open'] = '<div class="pagination"><ul>';
			$config['full_tag_close'] = '</ul></div>';
			$config['cur_tag_open'] = '<li class="active"><a href="#">';
			$config['cur_tag_close'] = '</a></li>';

			$config['num_tag_open'] = '<li>';
			$config['num_tag_close'] = '</li>';

			$config['prev_link'] = '&laquo;';
			$config['prev_tag_open'] = '<li>';
			$config['prev_tag_close'] = '</li>';

			$config['next_link'] = '&raquo;';
			$config['next_tag_open'] = '<li>';
			$config['next_tag_close'] = '</li>';
			
			$result					= $this->Product_model->search_products($term, $config['per_page'], $page, $sort_by['by'], $sort_by['sort']);
			$config['total_rows']	= $result['count'];
			$this->pagination->initialize($config);
	
			$data['products']		= $result['products'];
			foreach ($data['products'] as &$p)
			{
				$p->images	= (array)json_decode($p->images);
			}
		  $data['body_content']			= 'category';
		
		$this->load->view('template', $data);
		}
	}

function keysearch($code=false, $page = 0, $search = false)
	{
		$this->load->model('Search_model');
		//check to see if we have a search term
		if($search){
				$userdata = array(
                   'key_name'  => $search,
               );

			$this->session->set_userdata($userdata);
			//if the term is in post, save it to the db and give me a reference
			$term		= $code;
			$code		= $this->Search_model->record_term($term);
			// no code? redirect so we can have the code in place for the sorting.
			// I know this isn't the best way...
			redirect('cart/keysearch/'.$code.'/'.$page);			
			}
		else
		{
			//if we have the md5 string, get the term
			$term	= $this->Search_model->get_term($code);
		}
		
		if(empty($term))
		{
			//if there is still no search term throw an error
		$this->session->set_flashdata('error', lang('search_error'));
		redirect('cart');
		}
		$data['page_title']			= $this->session->userdata('key_name');
		//fix for the category view page.
		$data['base_url']			= array();
		
		$sort_array = array(
							'name/asc' => array('by' => 'name', 'sort'=>'ASC'),
							'name/desc' => array('by' => 'name', 'sort'=>'DESC'),
							'price/asc' => array('by' => 'price', 'sort'=>'ASC'),
							'price/desc' => array('by' => 'price', 'sort'=>'DESC'),
							);
							
							
		$urls = parse_url($_SERVER['REQUEST_URI']);
		@$get_val = explode('=',$urls['query']);			
		//echo $urls['query'];
		if(@$urls['query']=='')
		{
		$sort_by	= array('by'=>'id', 'sort'=>'ASC');
		//echo $sort_by['by'].' '.$sort_by['sort'];
		}	
		else
		{
			@$sort_bys	= explode('/',$get_val[1]);
			@$sort_by = array('by'=>$sort_bys[0], 'sort'=>$sort_bys[1]);
		}		

		if(empty($term))
		{
			//if there is still no search term throw an error
			$this->load->view('search_error', $data);
		}
		else
		{
	
			$data['page_title']	= $this->session->userdata('key_name');
					
			//set up pagination
			$this->load->library('pagination');
			$config['base_url']		= base_url().'cart/keysearch/'.$code.'/';
			$config['uri_segment']	= 4;
			$config['per_page']		= 16;
			
			$config['first_link'] = 'First';
			$config['first_tag_open'] = '<li>';
			$config['first_tag_close'] = '</li>';
			$config['last_link'] = 'Last';
			$config['last_tag_open'] = '<li>';
			$config['last_tag_close'] = '</li>';

			$config['full_tag_open'] = '<div class="pagination"><ul>';
			$config['full_tag_close'] = '</ul></div>';
			$config['cur_tag_open'] = '<li class="active"><a href="#">';
			$config['cur_tag_close'] = '</a></li>';

			$config['num_tag_open'] = '<li>';
			$config['num_tag_close'] = '</li>';

			$config['prev_link'] = '&laquo;';
			$config['prev_tag_open'] = '<li>';
			$config['prev_tag_close'] = '</li>';

			$config['next_link'] = '&raquo;';
			$config['next_tag_open'] = '<li>';
			$config['next_tag_close'] = '</li>';
			
			$result					= $this->Product_model->search_products_key($term, $config['per_page'], $page, $sort_by['by'], $sort_by['sort']);
			$config['total_rows']	= $result['count'];
			$this->pagination->initialize($config);
	
			$data['products']		= $result['products'];
			foreach ($data['products'] as &$p)
			{
				$p->images	= (array)json_decode($p->images);
			}
		  $data['body_content']			= 'category';
		
		  $this->load->view('template', $data);
		}
	}
	
	function category($id=false,$new=false)
	{
		$data['category']			= $this->Category_model->get_category($id);
		//set up pagination
		$segments	= $this->uri->total_segments();
		$base_url	= $this->uri->segment_array();
		
		if (!$data['category'])
		{
			$ids = 151092;
			$data['page_title']	= 'ALL';
			$page	= false;
		}
		else {
				$ids = $data['category']->id;
				if($data['category']->slug == $base_url[count($base_url)])
				{
					$page	= 0;
					$segments++;
				}
				else
				{
					$page	= array_splice($base_url, -1, 1);
					$page	= $page[0];
				}
		$data['subcategories']		= $this->Category_model->get_categories($ids);
		$data['meta']		= $data['category']->meta;
		$data['seo_title']	= (!empty($data['category']->seo_title))?$data['category']->seo_title:$data['category']->name;
		$data['page_title']	= $data['category']->name;
		$data['subcategories']		= $this->Category_model->get_categories($data['category']->id);
		}
		
		$data['base_url']	= $base_url;
		$base_url			= implode('/', $base_url);
		$data['product_columns']	= $this->config->item('product_columns');
		$sort_array = array(
							'name/asc' => array('by' => 'products.name', 'sort'=>'ASC'),
							'name/desc' => array('by' => 'products.name', 'sort'=>'DESC'),
							'price/asc' => array('by' => 'products.price', 'sort'=>'ASC'),
							'price/desc' => array('by' => 'products.price', 'sort'=>'DESC'),
							);
							

		$urls = parse_url($_SERVER['REQUEST_URI']);
		@$get_val = explode('=',$urls['query']);			
		if(@$urls['query']=='')
		{
		$sort_by	= array('by'=>'sequence', 'sort'=>'ASC');
		}	
		else
		{
			@$sort_bys	= explode('/',$get_val[1]);
			@$sort_by = array('by'=>$sort_bys[0], 'sort'=>$sort_bys[1]);
		}
		
		//set up pagination
		$this->load->library('pagination');
		$config['base_url']		= site_url($base_url);
		$config['uri_segment']	= $segments;
		
		if($new){
		$config['per_page']		= 15;
		}
		else{
		$config['per_page']		= 16;}
		if(!$new){
		$config['total_rows']	= $this->Product_model->count_products(@$data['category']->id);
		}
		
		$config['first_link'] = 'First';
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		$config['last_link'] = 'Last';
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';

		$config['full_tag_open'] = '<div class="pagination"><ul>';
		$config['full_tag_close'] = '</ul></div>';
		$config['cur_tag_open'] = '<li class="active"><a href="#">';
		$config['cur_tag_close'] = '</a></li>';
		
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		
		$config['prev_link'] = '&laquo;';
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';

		$config['next_link'] = '&raquo;';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		
		$this->pagination->initialize($config);
		
		//grab the products using the pagination lib

		$data['products']	= $this->Product_model->get_products($ids, $config['per_page'], $page, $sort_by['by'], $sort_by['sort'],$new);
		foreach ($data['products'] as &$p)
		{
			$p->images	= (array)json_decode($p->images);
		}
		
		$data['body_content']			= 'category';
		
		$this->load->view('template', $data);
	}
	
	function product($id,$v_id=false)
	{
		//get the product
		if($this->uri->segment(2) == 1){
		$data['product']	= $this->Product_model->get_product($id);}		
		else {
		$data['product']	= $this->Product_model->get_product($id);
		if(!$data['product'] || $data['product']->enabled==0)
		{
			show_404();
		}
		}
	
	
		if(isset($_GET['ref'])){
		$this->session->unset_userdata('referral');
		$referral_username			= $_GET['ref'];
		$referral_user	= $this->Customer_model->get_id_by_username($referral_username);
		$referral			= $referral_user->user_id;
		$this->session->set_userdata('referral', $referral);
		}
		
		$data['brws_cmpt']  	= $this->Product_model->get_brws();
		$data['file_include']  	= $this->Product_model->get_file_type();
				
		$data['base_url']			= $this->uri->segment_array();
		
		// load the digital language stuff
		$this->lang->load('digital_product');
		
		$related		= $data['product']->related_products;
		$data['related']	= array();
					
		$data['page_title']			= $data['product']->name;
		$data['meta']				= $data['product']->meta;
		$data['seo_title']			= (!empty($data['product']->seo_title))?$data['product']->seo_title:$data['product']->name;
			
		if($data['product']->images == 'false')
		{
			$data['product']->images = array();
		}
		else
		{
			$data['product']->images	= array_values((array)json_decode($data['product']->images));
		}

		$data['orders'] = $this->Order_model->get_customer_orders($this->user_id);
		
		$data['category'] = $this->Product_model->get_product_categories($id);
		
		$userloged	= $this->Customer_model->is_logged_in(false, false);
		$data['user_loged']  = 		$userloged;  
		$data['body_content']	= 'product'; 
		
		$this->load->view('template', $data);		
	}
	
	
	function add_to_cart()
	{
		$product_id		= $this->input->post('id');
		$quantity 		= $this->input->post('quantity');
		$post_options 	= $this->input->post('option');
		$standard		= $this->input->post('ids');
		
		// Get a cart-ready product array
		$product = $this->Product_model->get_cart_ready_product($product_id, $quantity,$standard);
		
	$this->bse_tec->insert($product);
		
	redirect('Cart');
	}
	
	function view_cart()
	{
		$this->load->library('breadcrumb');
// add breadcrumbs
 $this->breadcrumb->append_crumb('Home', base_url());
 $this->breadcrumb->append_crumb('Shopping Cart', base_url().'cart/view_cart');
// put this line in view to output
$breadcrumb = $this->breadcrumb->output();
$data['breadcrumb']	= $breadcrumb;

		$data['page_title']	= 'View Cart';
				
		$data['body_content']	= 'view_cart';
		
		$this->load->view('template', $data);		
		}
	
	function remove_item($key)
	{
		//drop quantity to 0
		$this->bse_tec->update_cart(array($key=>0));
		redirect('cart/view_cart');
	}
	
	function update_cart($redirect = false)
	{
		//if redirect isn't provided in the URL check for it in a form field
		if(!$redirect)
		{
			$redirect = $this->input->post('redirect');
		}
		
		// see if we have an update for the cart
		$item_keys		= $this->input->post('cartkey');
		$coupon_code	= $this->input->post('coupon_code');
		$gc_code		= $this->input->post('gc_code');
			
			
		//get the items in the cart and test their quantities
		$items			= $this->bse_tec->contents();
		$new_key_list	= array();
		//first find out if we're deleting any products
		foreach($item_keys as $key=>$quantity)
		{
			if(intval($quantity) === 0)
			{
				//this item is being removed we can remove it before processing quantities.
				//this will ensure that any items out of order will not throw errors based on the incorrect values of another item in the cart
				$this->bse_tec->update_cart(array($key=>$quantity));
			}
			else
			{
				//create a new list of relevant items
				$new_key_list[$key]	= $quantity;
			}
		}
		$response	= array();
		foreach($new_key_list as $key=>$quantity)
		{
			$product	= $this->bse_tec->item($key);
			//if out of stock purchase is disabled, check to make sure there is inventory to support the cart.
			if(!$this->config->item('allow_os_purchase') && (bool)$product['track_stock'])
			{
				$stock	= $this->Product_model->get_product($product['id']);
			
				//loop through the new quantities and tabluate any products with the same product id
				$qty_count	= $quantity;
				foreach($new_key_list as $item_key=>$item_quantity)
				{
					if($key != $item_key)
					{
						$item	= $this->bse_tec->item($item_key);
						//look for other instances of the same product (this can occur if they have different options) and tabulate the total quantity
						if($item['id'] == $stock->id)
						{
							$qty_count = $qty_count + $item_quantity;
						}
					}
				}
				if($stock->quantity < $qty_count)
				{
					if(isset($response['error']))
					{
						$response['error'] .= '<p>'.sprintf(lang('not_enough_stock'), $stock->name, $stock->quantity).'</p>';
					}
					else
					{
						$response['error'] = '<p>'.sprintf(lang('not_enough_stock'), $stock->name, $stock->quantity).'</p>';
					}
				}
				else
				{
					//this one works, we can update it!
					//don't update the coupons yet
					$this->bse_tec->update_cart(array($key=>$quantity));
				}
			}
			else
			{
				$this->bse_tec->update_cart(array($key=>$quantity));
			}
		}
		
		//if we don't have a quantity error, run the update
		if(!isset($response['error']))
		{
			//update the coupons and gift card code
			$response = $this->bse_tec->update_cart(false, $coupon_code, $gc_code);
			// set any messages that need to be displayed
		}
		else
		{
			$response['error'] = '<p>'.lang('error_updating_cart').'</p>'.$response['error'];
		}
		
		//check for errors again, there could have been a new error from the update cart function
		if(isset($response['error']))
		{
			$this->session->set_flashdata('error', $response['error']);
		}
		if(isset($response['message']))
		{
			$this->session->set_flashdata('message', $response['message']);
		}
		
		if($redirect)
		{
			redirect($redirect);
		}
		else
		{
			redirect('cart/view_cart');
		}
	}
}